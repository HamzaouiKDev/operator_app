<?php

namespace App\Observers;

use App\Models\EchantillonEnquete;
use App\Models\EchantillonStatutHistory;
use Illuminate\Support\Facades\Auth;

class EchantillonEnqueteObserver
{
    /**
     * Gère l'événement "updating" (avant la sauvegarde).
     * C'est ici que toute la logique est maintenant centralisée.
     */
    public function updating(EchantillonEnquete $echantillonEnquete): bool
    {
        $originalStatus = $echantillonEnquete->getOriginal('statut');
        $newStatus = $echantillonEnquete->statut;

        // Règle 1 : Empêcher la modification si le statut est déjà final.
        if (in_array($originalStatus, ['complet', 'refus final'])) {
            // On empêche la sauvegarde en retournant false.
            return false;
        }

        // Règle 2 : Si le statut est sur le point de passer à 'refus', on vérifie le seuil.
        if ($newStatus === 'refus') {
            // On calcule le futur nombre de refus (ceux existants + celui-ci).
            $refusCount = EchantillonStatutHistory::where('echantillon_enquete_id', $echantillonEnquete->id)
                ->where('nouveau_statut', 'refus')
                ->count();

            // Si le nombre de refus atteint ou dépasse 4 (le 5ème refus est celui en cours),
            // on force le statut à 'refus final' directement.
            if ($refusCount >= 4) {
                $echantillonEnquete->statut = 'refus final';
            }
        }
        
        // Règle 3 : Créer l'historique si le statut a changé.
        // On vérifie à nouveau car la logique ci-dessus a pu le modifier.
        if ($echantillonEnquete->isDirty('statut')) {
            EchantillonStatutHistory::create([
                'echantillon_enquete_id' => $echantillonEnquete->id,
                'user_id'                => Auth::id(),
                'ancien_statut'          => $originalStatus,
                'nouveau_statut'         => $echantillonEnquete->statut, // On utilise le statut potentiellement modifié.
                'commentaire'            => $echantillonEnquete->commentaire,
            ]);
        }

        // On autorise la sauvegarde.
        return true;
    }

    /**
     * La méthode "updated" n'est plus nécessaire pour cette logique.
     * La supprimer évite la sauvegarde récursive et corrige l'erreur.
     */
    // public function updated(EchantillonEnquete $echantillonEnquete): void
    // {
    //     // Cette logique a été déplacée dans la méthode "updating".
    // }
}
