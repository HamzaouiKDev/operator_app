<?php

namespace App\Observers;

use App\Models\EchantillonEnquete;
use App\Models\EchantillonStatutHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EchantillonEnqueteObserver
{
    /**
     * Gère l'événement "created" (après la création d'un nouvel échantillon).
     * C'est la fonction qui manquait pour compter les "nouveaux partiels".
     */
    public function created(EchantillonEnquete $echantillonEnquete): void
    {
        // On enregistre le premier statut dans l'historique.
        $this->logStatus($echantillonEnquete, null, $echantillonEnquete->statut);
    }

    /**
     * Gère l'événement "updating" (avant la mise à jour d'un échantillon existant).
     */
    public function updating(EchantillonEnquete $echantillonEnquete): bool
    {
        // Si le statut n'a pas changé, on ne fait rien.
        if (!$echantillonEnquete->isDirty('statut')) {
            return true;
        }

        $originalStatus = $echantillonEnquete->getOriginal('statut');
        $newStatus = $echantillonEnquete->statut;
        $finalStatuses = ['Complet', 'refus', 'impossible de contacter'];

        // On bloque la modification si le statut est déjà final.
        if (in_array($originalStatus, $finalStatuses)) {
            Log::warning("Observer: Tentative de modification du statut final '{$originalStatus}' pour l'échantillon #{$echantillonEnquete->id} bloquée.");
            return false; // Bloque la sauvegarde
        }

        // On enregistre le changement de statut dans l'historique.
        $this->logStatus($echantillonEnquete, $originalStatus, $newStatus);

        return true; // Autorise la sauvegarde
    }

    /**
     * Méthode privée et propre pour enregistrer dans l'historique.
     */
    private function logStatus(EchantillonEnquete $echantillon, ?string $ancien, string $nouveau): void
    {
        if (Auth::check()) {
            EchantillonStatutHistory::create([
                'echantillon_enquete_id' => $echantillon->id,
                'user_id'                => Auth::id(),
                'ancien_statut'          => $ancien, // Sera null lors de la création
                'nouveau_statut'         => $nouveau,
                'commentaire'            => $echantillon->commentaire,
            ]);
        }
    }
}