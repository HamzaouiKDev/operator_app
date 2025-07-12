@extends('layouts.master')

@section('css')
    {{-- Liaisons CSS pour les plugins, Font Awesome et la police Google 'Cairo' --}}
    <link href="{{ URL::asset('assets/plugins/iconfonts/plugin.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #4A90E2; /* Bleu doux */
            --success-color: #50E3C2; /* Vert menthe */
            --warning-color: #F5A623; /* Orange */
            --danger-color: #D0021B;  /* Rouge */
            --info-color: #4A4A4A;    /* Gris foncé */
            --teal-color: #008080;   /* Teal */
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
            --text-color: #495057;
            --border-color: #dee2e6;
        }

        body, h1, h2, h3, h4, h5, h6, .main-content-title, p, span, div {
            font-family: 'Cairo', sans-serif !important;
            color: var(--text-color);
        }

        /* ----- Style Général des Cartes ----- */
        .card {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease-in-out;
            background-color: #fff;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
        .card-title {
            font-weight: 700;
            color: var(--dark-gray);
        }

        /* ----- Style des Cartes KPI (Indicateurs Clés) ----- */
        .kpi-card {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            border-left: 6px solid;
            background-color: var(--light-gray);
        }
        .kpi-card .kpi-icon {
            font-size: 2.5rem;
            margin-right: 1.5rem;
            opacity: 0.8;
        }
        .kpi-card .kpi-content h6 {
            font-size: 0.9rem;
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 0.25rem;
        }
        .kpi-card .kpi-content h3 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-gray);
            margin-bottom: 0;
        }
        .kpi-card.primary { border-color: var(--primary-color); }
        .kpi-card.primary .kpi-icon { color: var(--primary-color); }

        .kpi-card.success { border-color: var(--success-color); }
        .kpi-card.success .kpi-icon { color: var(--success-color); }

        .kpi-card.warning { border-color: var(--warning-color); }
        .kpi-card.warning .kpi-icon { color: var(--warning-color); }

        .kpi-card.info { border-color: var(--info-color); }
        .kpi-card.info .kpi-icon { color: var(--info-color); }
        
        .kpi-card.teal { border-color: var(--teal-color); }
        .kpi-card.teal .kpi-icon { color: var(--teal-color); }

        /* ----- Style des Barres de Progression ----- */
        .progress-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        .progress {
            height: 10px !important;
            border-radius: 25px;
            background-color: #e9ecef;
        }
        .progress-bar {
            border-radius: 25px;
            transition: width 0.6s ease;
        }
        
        /* Formulaire */
        .form-control {
            border-radius: 8px;
            border: 1px solid var(--border-color);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.2);
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
            @if(isset($avancementParStatut) && !empty($avancementParStatut))
                @foreach($avancementParStatut as $statut)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <h6 class="progress-label mb-0">{{ $statut['nom'] }}</h6>
                            <span class="tx-14 font-weight-bold" style="color: {{ $statut['couleur'] }};">{{ $statut['count'] }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" 
                                 role="progressbar" 
                                 style="width: {{ $statut['pourcentage'] }}%; background-color: {{ $statut['couleur'] }};" 
                                 aria-valuenow="{{ $statut['pourcentage'] }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-muted text-center">لا توجد بيانات لعرضها.</p>
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
    <h3 class="mt-4 mb-4 text-center">إحصائيات لـ : <span style="color: var(--primary-color);">{{ $selectedTeleoperateur->name }}</span></h3>

    {{-- Cartes KPI (Indicateurs Clés) --}}
    <div class="row row-sm">
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card kpi-card primary">
                <i class="fas fa-tasks kpi-icon"></i>
                <div class="kpi-content">
                    <h6>العينات المعالجة</h6>
                    <h3>{{ $statsOperateur->echantillons_traites ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card kpi-card success">
                <i class="fas fa-check-double kpi-icon"></i>
                <div class="kpi-content">
                    <h6>العينات المكتملة</h6>
                    <h3>{{ $statsOperateur->echantillons_complets ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card kpi-card warning">
                <i class="fas fa-puzzle-piece kpi-icon"></i>
                <div class="kpi-content">
                    <h6>الجزئيات</h6>
                    <h3>{{ $statsOperateur->echantillons_partiels ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card kpi-card teal">
                <i class="fas fa-calendar-check kpi-icon"></i>
                <div class="kpi-content">
                    <h6>موعد (مع جزئي)</h6>
                    <h3>{{ $statsOperateur->rdv_avec_partiel_count ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card kpi-card info">
                <i class="fas fa-calendar-plus kpi-icon"></i>
                <div class="kpi-content">
                    <h6>موعد (بدون جزئي)</h6>
                    <h3>{{ $statsOperateur->rdv_sans_partiel_count ?? 0 }}</h3>
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
        const evolutionChartData = @json($evolutionChartData);

        // --- DEBUGGING: Log data to the browser console to check its content ---
        console.log("Données du graphique d'évolution :", evolutionChartData);

        const ctx = document.getElementById('evolutionChart');
        if (ctx) {
            // Check if there is actually data to display (more than just zeros)
            const hasData = evolutionChartData.completedData.some(item => item > 0) || evolutionChartData.partialData.some(item => item > 0);

            if (hasData) {
                new Chart(ctx, {
                    type: 'bar', // ✅ CHANGEMENT: Type de graphique changé en 'bar' (histogramme)
                    data: {
                        labels: evolutionChartData.labels,
                        datasets: [
                        {
                            label: 'عينات مكتملة',
                            data: evolutionChartData.completedData,
                            backgroundColor: 'rgba(80, 227, 194, 0.8)', // Couleur de fond solide
                            borderColor: 'var(--success-color)',
                            borderWidth: 2,
                            borderRadius: 5
                        },
                        {
                            label: 'عينات مكتملة جزئيا',
                            data: evolutionChartData.partialData,
                            backgroundColor: 'rgba(245, 166, 35, 0.8)', // Couleur de fond solide
                            borderColor: 'var(--warning-color)',
                            borderWidth: 2,
                            borderRadius: 5
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
                                ticks: { 
                                    stepSize: 1, 
                                    font: { family: 'Cairo' },
                                    color: 'var(--text-color)'
                                },
                                grid: { 
                                    drawBorder: false,
                                    color: 'var(--border-color)'
                                }
                            },
                            x: {
                                ticks: { 
                                    font: { family: 'Cairo' },
                                    color: 'var(--text-color)'
                                },
                                grid: { display: false }
                            }
                        }
                    }
                });
            } else {
                // S'il n'y a pas de données, afficher un message sur le canvas
                const context = ctx.getContext('2d');
                ctx.height = 100; // Réduire la hauteur pour le message
                context.textAlign = 'center';
                context.textBaseline = 'middle';
                context.font = "16px 'Cairo', sans-serif";
                context.fillStyle = '#6c757d';
                context.fillText('لا توجد بيانات لرسم المخطط لهذه الفترة', ctx.width / 2, 50);
            }
        }
    });
    @endif
</script>
@endsection
