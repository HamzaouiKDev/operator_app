<?php

namespace App\Observers;

use App\Models\EchantillonEnquete;
use App\Models\EchantillonStatutHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EchantillonEnqueteObserver
{
    /**
     * Gère l'événement "updating" (avant la sauvegarde).
     *
     * @param  \App\Models\EchantillonEnquete  $echantillonEnquete
     * @return bool|void
     */
    public function updating(EchantillonEnquete $echantillonEnquete): bool
    {
        $originalStatus = $echantillonEnquete->getOriginal('statut');
        $newStatus = $echantillonEnquete->statut;

        // ✅ MISE À JOUR ICI : La liste des statuts finaux a été changée.
        $finalStatuses = ['Complet', 'refus', 'impossible de contacter'];

        // Si le statut original est DÉJÀ dans la liste des statuts finaux,
        // on bloque la nouvelle sauvegarde pour éviter toute modification.
        if (in_array($originalStatus, $finalStatuses)) {
            Log::warning("Observer: Tentative de modification du statut final '{$originalStatus}' pour l'échantillon #{$echantillonEnquete->id} bloquée.");
            // On empêche la sauvegarde en retournant false.
            return false;
        }

        // Créer l'historique si le statut a réellement changé.
        if ($echantillonEnquete->isDirty('statut')) {
            EchantillonStatutHistory::create([
                'echantillon_enquete_id' => $echantillonEnquete->id,
                'user_id'                => Auth::id(),
                'ancien_statut'          => $originalStatus,
                'nouveau_statut'         => $newStatus,
                'commentaire'            => $echantillonEnquete->commentaire,
            ]);
        }

        // On autorise la sauvegarde.
        return true;
    }
}