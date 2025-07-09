@extends('layouts.master')

@section('css')
    {{-- Liaisons CSS pour les plugins, Font Awesome et la police Google 'Cairo' --}}
    <link href="{{ URL::asset('assets/plugins/iconfonts/plugin.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* La police 'Cairo' est appliquée à tous les éléments pour une cohérence parfaite */
        body, h1, h2, h3, h4, h5, h6, .main-content-title, p, span, div {
            font-family: 'Cairo', sans-serif !important;
        }

        /* ----- Style Général des Cartes ----- */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.07);
            transition: all 0.3s ease-in-out;
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #f0f0f0;
            padding: 1.25rem 1.5rem;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }
        .card-title {
            font-weight: 700;
            color: #1a2130;
        }

        /* ----- Style des Cartes KPI (Indicateurs Clés) ----- */
        .kpi-card {
    border-radius: 16px;
    color: #212529; /* Texte noir foncé */
    position: relative;
    overflow: hidden;
    border: none;
}
        .kpi-card:hover {
            transform: translateY(-6px) scale(1.03);
            box-shadow: 0 12px 30px rgba(0,0,0,0.12);
        }
        .kpi-card .kpi-icon {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%) rotate(-15deg);
            font-size: 4.5rem;
            opacity: 0.15;
            transition: all 0.4s ease;
        }
        .kpi-card:hover .kpi-icon {
            transform: translateY(-50%) rotate(0deg) scale(1.1);
            opacity: 0.25;
        }
        /* Palette de couleurs avec dégradés pour un look moderne */
        .bg-c-primary { background: linear-gradient(45deg, #3b82f6, #60a5fa); } /* Bleu */
        .bg-c-success { background: linear-gradient(45deg, #22c55e, #4ade80); } /* Vert */
        .bg-c-danger  { background: linear-gradient(45deg, #ef4444, #f87171); } /* Rouge */
        .bg-c-warning { background: linear-gradient(45deg, #f97316, #fb923c); } /* Orange */

        /* ----- Style des Barres de Progression ----- */
        .progress-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        .progress {
            height: 12px !important;
            border-radius: 25px;
            background-color: #f3f4f6;
            overflow: visible; /* Permet à l'infobulle de déborder */
        }
        .progress-bar {
            border-radius: 25px;
            position: relative;
            transition: width 0.6s ease;
        }
        /* Style pour le texte à l'intérieur de la barre */
        .visible-progress-text {
            font-weight: 700;
            font-size: 0.7rem;
            line-height: 1;
        }
        .progress-bar-dark-text {
            color: #1f2937 !important;
        }

        /* ----- Style des Tableaux ----- */
        .table-custom {
            border-collapse: separate;
            border-spacing: 0 10px; /* Espace vertical entre les lignes */
        }
        .table-custom tr {
            background-color: #fff;
            transition: all 0.2s ease;
        }
        .table-custom td, .table-custom th {
            border: none;
            padding: 1rem;
            vertical-align: middle;
        }
        .table-custom thead th {
            font-weight: 700;
            color: #888;
        }
        .table-custom tbody tr:hover {
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
            transform: scale(1.01);
        }
        .badge-custom {
            padding: 0.5em 0.9em;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.8rem;
            color: white; /* Assurer que le texte du badge est blanc par défaut */
        }
    </style>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between" dir="rtl">
        <div class="left-content">
            <div>
                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">لوحة تحكم المشرف</h2>
                <p class="mg-b-0 text-muted">نظرة عامة على الأداء العام وحسب المشغل.</p>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid" dir="rtl">

    {{-- SECTION : Avancement Général --}}
    <div class="card mg-b-20">
        <div class="card-header">
            <h4 class="card-title mb-0">التقدم العام للاستبيان حسب الحالة</h4>
        </div>
        <div class="card-body">
            @php
                // Palette de couleurs unifiée pour tout le dashboard
                $colorMap = [
                    'مكتمل'           => '#10b981', // Vert Émeraude
                    'مكتمل جزئيا'      => '#f59e0b', // Orange Ambré
                    'موعد'            => '#0ea5e9', // Bleu Ciel
                    'رفض'             => '#ef4444', // Rouge Vif
                    'إعادة إتصال'      => '#8b5cf6', // Violet Intense
                    'إستحالة الإتصال' => '#64748b', // Gris Ardoise
                    'في الانتظار'     => '#e5e7eb', // Gris Très Clair
                ];
            @endphp
            @if(isset($avancementParStatut))
                @foreach($avancementParStatut as $statut)
                    @php
                        $couleur = $colorMap[$statut['nom']] ?? '#6b7280';
                        $percentage = $statut['pourcentage'];
                        $isLightBg = $statut['nom'] === 'في الانتظار';
                    @endphp
                    <div class="mb-4">
                        <div class="d-flex justify-content-between">
                            <h6 class="progress-label">{{ $statut['nom'] }}</h6>
                            <span class="tx-14 font-weight-bold" style="color: {{ $couleur }};">{{ $statut['count'] }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar d-flex align-items-center justify-content-center" 
                                 role="progressbar" 
                                 style="width: {{ $percentage }}%; background-color: {{ $couleur }};" 
                                 aria-valuenow="{{ $percentage }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                @if($percentage > 5) 
                                    <span class="visible-progress-text {{ $isLightBg ? 'progress-bar-dark-text' : '' }}">
                                        {{ round($percentage, 1) }}%
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Formulaire de sélection par opérateur --}}
    <div class="card mg-b-20">
         <div class="card-header">
            <h4 class="card-title mb-0">عرض إحصائيات حسب المشغل</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('supervisor.dashboard') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-5">
                        <label for="teleoperateur_id" class="form-label font-weight-bold">اختر مشغلًا:</label>
                        {{-- AMÉLIORATION UX : Le formulaire se soumet automatiquement au changement --}}
                        <select name="teleoperateur_id" id="teleoperateur_id" class="form-control" onchange="this.form.submit()">
                            <option value="">-- عرض الإحصائيات لجميع المشغلين --</option>
                            @foreach($teleoperateurs as $teleoperateur)
                                <option value="{{ $teleoperateur->id }}"
                                    @if(isset($selectedTeleoperateur) && $selectedTeleoperateur->id == $teleoperateur->id) selected @endif>
                                    {{ $teleoperateur->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Cette section s'affiche uniquement si un opérateur est sélectionné --}}
    @if(isset($selectedTeleoperateur))
    <hr class="my-4">
    <h3 class="mt-4 mb-4 text-center">إحصائيات لـ : <span class="text-primary">{{ $selectedTeleoperateur->name }}</span></h3>

    {{-- Cartes KPI (Indicateurs Clés) --}}
    <div class="row row-sm">
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="card kpi-card bg-c-primary">
                <div class="card-body text-center">
                    <i class="fas fa-building kpi-icon"></i>
                    <h6 class="mb-3 tx-14">إجمالي العينات المخصصة</h6>
                    <h3 class="font-weight-bold mb-1">{{ $nombreEntreprisesAttribuees ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="card kpi-card bg-c-success">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle kpi-icon"></i>
                    <h6 class="mb-3 tx-14">العينات المعالجة</h6>
                    <h3 class="font-weight-bold mb-1">{{ $echantillonsTraites ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="card kpi-card bg-c-danger">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-check kpi-icon"></i>
                    <h6 class="mb-3 tx-14">إجمالي المواعيد</h6>
                    <h3 class="font-weight-bold mb-1">{{ $totalRendezVous ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 mb-4">
            <div class="card kpi-card bg-c-warning">
                <div class="card-body text-center">
                    <i class="fas fa-bullseye kpi-icon"></i>
                    <h6 class="mb-3 tx-14">معدل الفعالية</h6>
                    <h3 class="font-weight-bold mb-1">{{ $tauxDefficacite ?? 0 }}%</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableaux des statuts --}}
    @php
        // Tableau de traduction. Assurez-vous que les clés correspondent aux statuts de la base de données.
        $statutTranslations = [
            'Complet' => 'مكتمل', 'Partiel' => 'مكتمل جزئيا',
            'Rendez-vous' => 'موعد', 'À rappeler' => 'إعادة إتصال', 'Refus' => 'رفض',
            'Refus final' => 'رفض كلي', 'Impossible de contacter' => 'إستحالة الإتصال',
            'En attente' => 'في الانتظار', 'Confirmé' => 'مؤكد', 'Annulé' => 'ملغى', 'Reporté' => 'مؤجل',
        ];
    @endphp

    <div class="row row-sm">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">توزيع حالات العينات (الإجمالي)</h4></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-custom">
                            <tbody>
                                @forelse($entreprisesParStatut as $statut => $count)
                                    @php
                                        // Logique pour obtenir la traduction et la couleur
                                        $translatedStatus = $statutTranslations[$statut] ?? $statut;
                                        $badgeColor = $colorMap[$translatedStatus] ?? '#6c757d'; // Couleur par défaut
                                        $isLightColor = $translatedStatus === 'في الانتظار';
                                    @endphp
                                    <tr>
                                        <td>{{ $translatedStatus }}</td>
                                        <td class="text-left">
                                            {{-- AMÉLIORATION : La couleur du badge est maintenant dynamique et cohérente --}}
                                            <span class="badge badge-custom" style="background-color: {{ $badgeColor }}; color: {{ $isLightColor ? '#333' : 'white' }};">
                                                {{ $count }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td class="text-center" colspan="2">لا توجد بيانات.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">توزيع حالات العينات (اليوم)</h4></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-custom">
                            <tbody>
                                @forelse($statutsAujourdhui as $statut => $count)
                                     @php
                                        $translatedStatus = $statutTranslations[$statut] ?? $statut;
                                        $badgeColor = $colorMap[$translatedStatus] ?? '#6c757d';
                                        $isLightColor = $translatedStatus === 'في الانتظار';
                                    @endphp
                                    <tr>
                                        <td>{{ $translatedStatus }}</td>
                                        <td class="text-left">
                                             <span class="badge badge-custom" style="background-color: {{ $badgeColor }}; color: {{ $isLightColor ? '#333' : 'white' }};">
                                                {{ $count }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td class="text-center" colspan="2">لا توجد بيانات لهذا اليوم.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphique d'évolution --}}
    <div class="row row-sm mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">تطور العينات المكتملة و الجزئية (آخر 7 أيام)</h4></div>
                <div class="card-body" style="height: 400px;"><canvas id="evolutionChart"></canvas></div>
            </div>
        </div>
    </div>
    @endif {{-- Fin de la condition @if(isset($selectedTeleoperateur)) --}}
</div>
@endsection

@section('js')
{{-- Chargement de la librairie Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Le script ne s'exécute que si les données du graphique existent pour éviter les erreurs
    @if(isset($selectedTeleoperateur) && !empty($evolutionChartData))
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('evolutionChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line', // Type de graphique
                data: {
                    labels: @json($evolutionChartData['labels'] ?? []),
                    datasets: [
                    {
                        label: 'عينات مكتملة',
                        data: @json($evolutionChartData['completedData'] ?? []),
                        borderColor: '#22c55e',
                        backgroundColor: 'rgba(34, 197, 94, 0.2)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#22c55e',
                        pointHoverRadius: 7,
                        pointRadius: 5
                    },
                    {
                        label: 'عينات مكتملة جزئيا',
                        data: @json($evolutionChartData['partialData'] ?? []),
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249, 115, 22, 0.2)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#f97316',
                        pointHoverRadius: 7,
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { labels: { font: { family: 'Cairo', size: 14 } } },
                        tooltip: {
                            titleFont: { family: 'Cairo', size: 14 },
                            bodyFont: { family: 'Cairo', size: 12 },
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            ticks: { stepSize: 1, font: { family: 'Cairo' } },
                            grid: { drawBorder: false }
                        },
                        x: {
                            ticks: { font: { family: 'Cairo' } },
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    });
    @endif
</script>
@endsection