<!DOCTYPE html>
<html lang="fr" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $sujet }}</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f4f7f6; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
        .email-wrapper { width: 100%; background-color: #f4f7f6; }
        .email-container { width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e0e0e0; }
        .email-header { background-color: #007bff; color: #ffffff; padding: 25px; text-align: center; }
        .email-header h1 { margin: 0; font-size: 24px; font-weight: 600; }
        .email-body { padding: 30px; text-align: right; line-height: 1.7; color: #333333; font-size: 16px; }
        .email-body p { margin: 0 0 15px 0; }
        .email-footer { text-align: center; padding: 20px; font-size: 12px; color: #888888; }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7f6; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
    <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <!-- Conteneur principal -->
                <table class="email-container" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <!-- En-tête -->
                    <tr>
                        <td class="email-header">
                            <h1>{{ $sujet }}</h1>
                        </td>
                    </tr>
                    <!-- Corps du message -->
                    <tr>
                        <td class="email-body">
                            {{-- Salutation personnalisée si l'objet entreprise existe --}}
                            @if(isset($entreprise) && $entreprise)
                                <p>Bonjour, <b>{{ $entreprise->nom_entreprise }}</b>,</p>
                            @endif
                            
                            {{-- Corps du message avec sauts de ligne --}}
                            <p>{!! nl2br(e($corps)) !!}</p>
                        </td>
                    </tr>
                </table>
                <!-- Pied de page -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                     <tr>
                        <td class="email-footer">
                           <p>Ceci est un message automatique. Merci de ne pas répondre directement.</p>
                           <p>&copy; {{ date('Y') }} Votre Application. Tous droits réservés.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
