<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rapport de Performance</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #374151;
            background-color: #f9fafb;
        }
        @page {
            margin: 20mm;
        }
        .main-content {
            position: relative;
            z-index: 10;
        }
        h2 {
            font-size: 18px;
            font-weight: 700;
            color: #1e3a8a;
            background-color: #eff6ff;
            padding: 12px 18px;
            border-radius: 6px;
            border-left: 4px solid #3b82f6;
            margin-top: 40px;
            margin-bottom: 20px;
        }
        .report-header {
            padding-bottom: 20px;
            margin-bottom: 20px;
            overflow: auto;
            border-bottom: 3px solid #3b82f6;
        }
        .header-left {
            float: left;
            width: 70%;
        }
        .header-right {
            float: right;
            width: 30%;
            text-align: right;
        }
        .logo {
            max-width: 140px;
        }
        .report-title h1 {
            font-size: 22px;
            color: #1e3a8a;
            margin: 0;
            font-weight: 700;
            line-height: 1.2;
        }
        .report-title p {
            font-size: 14px;
            color: #6b7280;
            margin-top: 8px;
        }
        .report-notice {
            text-align: center;
            font-style: italic;
            font-size: 12px;
            color: #4b5563;
            background-color: #f3f4f6;
            padding: 15px 20px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            margin-bottom: 40px;
            max-width: 80%;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.8;
            clear: both; /* Ajouté pour éviter toute superposition */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            border: none;
            border-bottom: 1px solid #e5e7eb;
            padding: 14px 16px;
            text-align: left;
            vertical-align: middle;
        }
        th {
            background-color: #f9fafb;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            color: #374151;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .total-row td {
            font-weight: 700;
            background-color: #e0e7ff;
            color: #1e3a8a;
        }
        .text-right { text-align: right !important; }
        .footer {
            position: fixed;
            bottom: -20mm;
            left: 0px;
            right: 0px;
            height: 50px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
            font-size: 10px;
            font-weight: normal;
            color: #9ca3b0;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: -1;
            opacity: 0.05;
        }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('assets/img/brand/ins.png');
        $logoSrc = '';
        if (File::exists($logoPath)) {
            $logoData = base64_encode(File::get($logoPath));
            $logoSrc = 'data:image/png;base64,' . $logoData;
        }

        $labels = [
            'complets' => 'Complets',
            'partiels' => 'Partiels',
            'refus' => 'Refus',
            'suivis' => 'À rappeler',
            'impossible' => 'Contact impossible',
            'rdv_avec_partiel' => 'RDV (avec partiel)',
            'rdv_sans_partiel' => 'RDV (sans partiel)',
        ];
    @endphp

    <div class="watermark">
        @if($logoSrc)
            <img src="{{ $logoSrc }}" width="500" />
        @endif
    </div>

    <div class="main-content">
        <div class="report-header">
            <div class="header-left report-title">
                <h1>Rapport de Performance</h1>
                <p>Date de création : {{ $date }}</p>
            </div>
            <div class="header-right">
                @if($logoSrc)
                    <img src="{{ $logoSrc }}" alt="Logo" class="logo">
                @endif
            </div>
        </div>

        <div class="report-notice">
            <p>Ce rapport est généré<br>automatiquement par l'application<br>de gestion du centre d'appel.</p>
        </div>

        <div class="footer">
            <!-- Le pied de page peut rester vide ou contenir un numéro de page si besoin -->
        </div>

        <h2>Rapport Général de l'enquete pilote emploi et salaire</h2>
        <table>
            <thead>
                <tr>
                    <th>Statut</th>
                    <th class="text-right">Nombre</th>
                    <th class="text-right" style="width: 25%;">Pourcentage</th>
                </tr>
            </thead>
            <tbody>
                @php $totalGlobal = $statsGlobales['total'] ?: 1; @endphp
                <tr class="total-row">
                    <td>Total des Échantillons</td>
                    <td class="text-right">{{ $statsGlobales['total'] }}</td>
                    <td class="text-right">100%</td>
                </tr>
                @foreach($labels as $key => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td class="text-right">{{ $statsGlobales[$key] ?? 0 }}</td>
                    <td class="text-right">{{ number_format((($statsGlobales[$key] ?? 0) / $totalGlobal) * 100, 2) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @foreach($statsOperateurs as $stats)
            <h2>Rapport de l'Opérateur : {{ $stats['nom'] }}</h2>
            <table>
                <thead>
                    <tr>
                        <th>Statut</th>
                        <th class="text-right">Nombre</th>
                        <th class="text-right" style="width: 25%;">Pourcentage</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalOperateur = $stats['total'] ?: 1; @endphp
                    <tr class="total-row">
                        <td>Échantillons Assignés</td>
                        <td class="text-right">{{ $stats['total'] }}</td>
                        <td class="text-right">100%</td>
                    </tr>
                    @foreach($labels as $key => $label)
                    <tr>
                        <td>{{ $label }}</td>
                        <td class="text-right">{{ $stats[$key] ?? 0 }}</td>
                        <td class="text-right">{{ number_format((($stats[$key] ?? 0) / $totalOperateur) * 100, 2) }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    </div>
</body>
</html>