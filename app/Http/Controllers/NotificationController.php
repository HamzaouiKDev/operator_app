<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RendezVous;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function __construct()
    {
        // ...
    }

    public function streamUpcomingRendezVous()
    {
        $response = new StreamedResponse(function () {
            while (true) {
                if (!Auth::check() || !Auth::user()->hasRole('Téléopérateur')) {
                    Log::warning('SSE Stream: User is no longer authenticated or does not have the required role. Closing connection.');
                    echo "event: error\n";
                    echo "data: " . json_encode(['message' => 'Unauthorized access or session expired', 'code' => 401]) . "\n\n";
                    break;
                }

                $user = Auth::user();
                $now = Carbon::now(); // Utilise le fuseau horaire de l'application (normalement CET pour la Tunisie)
                $limitTime = $now->copy()->addMinutes(15);

                // --- MODIFICATION CLÉ ICI : Utiliser format('Y-m-d H:i:s.v') pour SQL Server
                // Ce format est le style 121 de CONVERT et ne contient pas de décalage horaire.
                $startDateTime = $now->format('Y-m-d H:i:s.v'); 
                $endDateTime = $limitTime->format('Y-m-d H:i:s.v'); 

                $upcomingRendezVous = RendezVous::where('utilisateur_id', $user->id)
                                               // Utilisation de CONVERT avec le style 121 pour forcer l'interprétation par SQL Server
                                               ->whereRaw('[heure_rdv] BETWEEN CONVERT(DATETIME2(3), ?, 121) AND CONVERT(DATETIME2(3), ?, 121)', [$startDateTime, $endDateTime])
                                               ->with('echantillonEnquete.entreprise')
                                               ->orderBy('heure_rdv', 'asc')
                                               ->get();

                $notificationsData = $upcomingRendezVous->map(function ($rdv) use ($now) {
                    $companyName = optional($rdv->echantillonEnquete->entreprise)->nom_entreprise ?? 'Entreprise inconnue';
                    $echantillonId = optional($rdv->echantillonEnquete)->id ?? null; 

                    $link = '#';
                    if ($echantillonId) {
                        // Utiliser la route 'echantillons.show' qui prend l'ID de l'échantillon
                        $link = route('echantillons.show', ['echantillon' => $echantillonId]); 
                    }

                    return [
                        'id' => $rdv->id,
                        'title' => 'Rendez-vous imminent avec ' . $companyName,
                        'time_left' => Carbon::parse($rdv->heure_rdv)->diffForHumans($now, [
                            'parts' => 2,
                            'short' => true,
                            'join' => true,
                            'syntax' => Carbon::DIFF_ABSOLUTE
                        ]),
                        'link' => $link,
                        'icon_class' => 'las la-calendar-check text-white',
                        'bg_class' => 'bg-info',
                    ];
                });

                echo "event: new_rendezvous_notification\n";
                echo "data: " . json_encode([
                    'count' => $notificationsData->count(),
                    'notifications' => $notificationsData->toArray(),
                ]) . "\n\n";

                ob_flush();
                flush();
                sleep(15);
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no');

        return $response;
    }
}