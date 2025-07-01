<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sujet }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap');

        body {
            margin: 0;
            padding: 0;
            background-color: #f2f4f6;
            font-family: 'Lato', Arial, sans-serif;
            color: #333333;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f2f4f6;
            padding: 40px 20px;
        }
        .email-container {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            border: 1px solid #e1e8ed;
        }
        .header-table {
            width: 100%;
            padding: 30px;
            border-bottom: 1px solid #e1e8ed;
        }
        .header-left {
            text-align: left;
            font-size: 14px;
            line-height: 1.5;
            color: #555;
        }
        .header-right {
            text-align: right;
        }
        .header-right img {
            max-width: 90px;
        }
        .email-body {
            padding: 30px 40px;
            text-align: left;
            font-family: 'Times New Roman', Times, serif;
            font-size: 16px;
            line-height: 1.7;
        }
        .date-section {
            text-align: right;
            padding-bottom: 30px;
            font-size: 15px;
        }
        .address-section {
            padding-bottom: 20px;
        }
        .subject-section {
            padding: 20px 0;
            font-weight: bold;
            font-size: 17px;
            border-bottom: 1px solid #f0f0f0;
            margin-bottom: 25px;
        }
        .body-content p {
            margin: 0 0 1.2em 0;
            text-align: justify;
        }
        .closing-section {
            padding-top: 30px;
            text-align: right;
            font-style: italic;
        }
        .footer-cell {
            padding: 30px;
            font-size: 12px;
            text-align: center;
            color: #8899a6;
            background-color: #f8f9fa;
        }
        .footer-cell a {
            color: #3498db;
            text-decoration: none;
        }

    </style>
</head>
<body>
    <table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center">
                <table class="email-container" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <!-- En-tête -->
                    <tr>
                        <td>
                            <table class="header-table">
                                <tr>
                                    <td class="header-left">
                                        <strong>Ministère de l’Économie et de la Planification</strong><br>
                                        Institut National de la Statistique
                                    </td>
                                    <td class="header-right">
                                        <img src="https://www.ins.tn/themes/custom/ins/logo.png" alt="Logo INS">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Corps du message -->
                    <tr>
                        <td class="email-body">
                            <div class="date-section">
                                Tunis, le {{ \Carbon\Carbon::now()->format('d F Y') }}
                            </div>

                            <div class="address-section">
                                Le Directeur Général de l’Institut National de la Statistique<br>
                                <br>
                                À l'attention de Monsieur le Président Directeur Général<br>
                                @if(isset($entreprise) && $entreprise)
                                    <strong>{{ $entreprise->nom_entreprise }}</strong>
                                @endif
                            </div>

                            <div class="subject-section">
                                <u>Objet: {{ $sujet }}</u><br>
                                <u>Pièces jointes : Questionnaire de l’enquête.</u>
                            </div>

                            <div class="body-content">
                                <p>{!! nl2br(e($corps)) !!}</p>
                            </div>

                            <div class="closing-section">
                                 Comptant sur votre précieuse collaboration, nous vous prions de croire, Monsieur le Président Directeur Général, à notre plus haute considération.
                            </div>
                        </td>
                    </tr>
                    <!-- Pied de page -->
                     <tr>
                        <td class="footer-cell">
                           70 Rue Echem - 1002 Tunis Belvédère - Tunisie <br>
                           Tél: (+216) 71 891 002 | Fax: (+216) 71 792 559 | E-mail: <a href="mailto:INS@ins.tn">INS@ins.tn</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
