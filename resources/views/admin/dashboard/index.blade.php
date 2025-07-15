@extends('layouts.master')

@section('css')
    {{-- Vos CSS existants --}}
    <link href="{{URL::asset('assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/iconfonts/plugin.css')}}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    {{-- Ajout de Google Fonts pour une typographie professionnelle --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body, h1, h2, h3, h4, h5, h6, .main-content-title, p, span, div, .tx-13, .tx-12 {
            font-family: 'Cairo', sans-serif !important;
        }

        /* ----- Style Général ----- */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.07);
            transition: all 0.3s ease-in-out;
            background-color: #fff;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid #f0f0f0;
            padding: 1.25rem 1.5rem;
        }
        .card-title {
            font-weight: 700;
            color: #1a2130;
        }

        /* ----- Styles pour les cartes KPI originales ----- */
        .kpi-card {
             border-right: 5px solid #3498db; /* Bordure par défaut */
             border-left: 0;
             transition: all 0.3s ease-in-out;
       }
        .kpi-card:hover {
             transform: translateY(-5px);
             box-shadow: 0 10px 20px rgba(0,0,0,0.1);
       }
        .kpi-icon {
             font-size: 3rem;
             opacity: 0.3;
       }

        /* ----- Tableaux Modernes ----- */
        .table-custom {
            border-collapse: separate;
            border-spacing: 0 8px; /* Espace entre les lignes */
        }
        .table-custom tr {
            background-color: #fff;
            border-radius: 10px;
            transition: all 0.2s ease;
        }
        .table-custom td, .table-custom th {
            border-top: 1px solid #f1f1f1;
            border-bottom: 1px solid #f1f1f1;
            padding: 1rem 1.25rem;
            vertical-align: middle;
        }
        .table-custom thead th {
            border: none;
            font-weight: 600;
            color: #888da8;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }
        .table-custom tbody tr:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transform: scale(1.02);
            z-index: 10;
            position: relative;
        }
        .table-custom td:first-child {
            border-right: 1px solid #f1f1f1; /* Adapté pour RTL */
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }
        .table-custom td:last-child {
            border-left: 1px solid #f1f1f1; /* Adapté pour RTL */
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }
        .badge-custom {
            padding: 0.5em 1em;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.8rem;
        }
        .table-custom .stat-number {
            font-size: 1rem;
            font-weight: 700; /* Assure que le texte est bien gras */
            color: #000000 !important; /* ✅ Force la couleur du texte en noir */
            min-width: 40px;
            display: inline-block;
        }
    </style>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between" dir="rtl">
        <div class="left-content">
            <div>
                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">لوحة تحكم المسؤول</h2>
                <p class="mg-b-0 text-muted">نظرة عامة على نشاط مركز الاتصال.</p>
            </div>
        </div>
        <div class="main-dashboard-header-right text-right">
            <div>
                <label class="tx-13 text-muted">التاريخ الحالي</label>
                <h5 class="font-weight-bold">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</h5>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid" dir="rtl">
        {{-- Cartes KPI (Ancien Design Restauré) --}}
        <div class="row row-sm">
            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <div class="card overflow-hidden sales-card bg-primary-gradient kpi-card">
                    <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                        <div class="d-flex justify-content-between">
                            <div class="mr-auto">
                                <h6 class="mb-3 tx-12 text-white">المشغلون النشطون</h6>
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $totalTeleoperateurs }}</h4>
                                <p class="mb-0 tx-12 text-white op-7">مستخدمون بدور 'مشغل هاتفي'</p>
                            </div>
                            <i class="fas fa-headset kpi-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <div class="card overflow-hidden sales-card bg-danger-gradient kpi-card" style="border-right-color: #e74c3c;">
                    <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                        <div class="d-flex justify-content-between">
                            <div class="mr-auto">
                                <h6 class="mb-3 tx-12 text-white">إجمالي العينات</h6>
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $totalEchantillons }}</h4>
                                <p class="mb-0 tx-12 text-white op-7">العدد الإجمالي للشركات للتواصل معها</p>
                            </div>
                            <i class="fas fa-building kpi-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <div class="card overflow-hidden sales-card bg-success-gradient kpi-card" style="border-right-color: #2ecc71;">
                    <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                        <div class="d-flex justify-content-between">
                            <div class="mr-auto">
                                <h6 class="mb-3 tx-12 text-white">العينات المكتملة</h6>
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $echantillonsTermines }}</h4>
                                <p class="mb-0 tx-12 text-white op-7">جميع الحالات المكتملة</p>
                            </div>
                            <i class="fas fa-check-circle kpi-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <div class="card overflow-hidden sales-card bg-warning-gradient kpi-card" style="border-right-color: #f39c12;">
                    <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                        <div class="d-flex justify-content-between">
                             <div class="mr-auto">
                                <h6 class="mb-3 tx-12 text-white">مواعيد اليوم</h6>
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $rendezVousAujourdhui }}</h4>
                                <p class="mb-0 tx-12 text-white op-7">المواعيد المخطط لها اليوم</p>
                            </div>
                            <i class="fas fa-calendar-day kpi-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- NOUVELLE RANGÉE DE CARTES KPI --}}
        <div class="row row-sm mt-4">
            <div class="col-xl-4 col-lg-6 col-md-6 col-xm-12">
                <div class="card overflow-hidden sales-card bg-info-gradient kpi-card" style="border-right-color: #a78bfa;">
                    <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                        <div class="d-flex justify-content-between">
                            <div class="mr-auto">
                                <h6 class="mb-3 tx-12 text-white">إجمالي الجزئيات</h6>
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $totalPartiels }}</h4>
                                <p class="mb-0 tx-12 text-white op-7">العينات التي لم تكتمل بعد</p>
                            </div>
                            <i class="fas fa-puzzle-piece kpi-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-6 col-xm-12">
                <div class="card overflow-hidden sales-card bg-teal-gradient kpi-card" style="border-right-color: #1abc9c;">
                    <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                        <div class="d-flex justify-content-between">
                            <div class="mr-auto">
                                <h6 class="mb-3 tx-12 text-white">مواعيد (مع رد جزئي)</h6>
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $rdvAvecPartiel }}</h4>
                                <p class="mb-0 tx-12 text-white op-7">مواعيد لعينات مع رد جزئي</p>
                            </div>
                            <i class="fas fa-calendar-check kpi-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-6 col-xm-12">
                <div class="card overflow-hidden sales-card bg-secondary-gradient kpi-card" style="border-right-color: #95a5a6;">
                    <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                        <div class="d-flex justify-content-between">
                            <div class="mr-auto">
                                <h6 class="mb-3 tx-12 text-white">مواعيد (بدون رد جزئي)</h6>
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $rdvSansPartiel }}</h4>
                                <p class="mb-0 tx-12 text-white op-7">مواعيد لعينات جديدة</p>
                            </div>
                            <i class="fas fa-calendar-plus kpi-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- Contenu principal : Tableaux et Graphique (Nouveau Design Conservé) --}}
        <div class="row row-sm mt-4">
            {{-- Tableau des performances --}}
            <div class="col-lg-12 col-xl-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">أداء المشغلين</h4>
                        <p class="tx-12 text-muted mb-0">نظرة عامة على النشاط حسب المستخدم.</p>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-custom">
                                <thead>
                                    <tr>
                                        <th>الاسم</th>
                                        <th>معالج</th>
                                        <th>مكتمل</th>
                                        <th>جزئي</th>
                                        <th>موعد (مع رد جزئي)</th>
                                        <th>موعد (بدون رد جزئي)</th>
                                    </tr>
                                </thead>
                               {{-- Dans la vue admin.dashboard.index --}}

<tbody>
    @forelse($teleoperateurs as $teleoperateur)
        <tr>
            <td>{{ $teleoperateur->name }}</td>
            
            {{-- ✅ CORRECTION : Utilisation des noms avec _count générés par withCount --}}
            <td><span class="badge bg-secondary-transparent badge-custom stat-number">{{ $teleoperateur->echantillons_traites_count }}</span></td>
            <td><span class="badge bg-success-transparent badge-custom stat-number">{{ $teleoperateur->echantillons_complets_count }}</span></td>
            <td><span class="badge bg-warning-transparent badge-custom stat-number">{{ $teleoperateur->echantillons_partiels_count }}</span></td>
            <td><span class="badge bg-teal-transparent badge-custom stat-number">{{ $teleoperateur->rdv_avec_partiel_count }}</span></td>
            <td><span class="badge bg-info-transparent badge-custom stat-number">{{ $teleoperateur->rdv_sans_partiel_count }}</span></td>
            
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center text-muted">لم يتم العثور على أي مشغل هاتفي.</td>
        </tr>
    @endforelse
</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Graphique de distribution --}}
            <div class="col-lg-12 col-xl-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">توزيع الحالات</h4>
                        <p class="tx-12 text-muted mb-0">حالة جميع العينات.</p>
                    </div>
                    <div class="card-body" style="height: 350px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('statusChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [{
                            label: 'عدد العينات',
                            data: @json($chartData),
                            // ✅ MISE À JOUR: Nouvelles couleurs pour le graphique
                            backgroundColor: [
                                '#f59e0b', // Orange Ambré (Non traité)
                                '#e5e7eb', // Gris Très Clair (En attente)
                                '#10b981', // Vert Émeraude (Complet)
                                '#a78bfa', // Violet Lavande (Partiel)
                                '#ef4444', // Rouge Vif (Refus)
                                '#64748b', // Gris Ardoise (Impossible de contacter)
                                '#1abc9c', // Teal (RDV avec partiel)
                                '#95a5a6', // Gris Secondaire (RDV sans partiel)
                                '#f97316'  // Orange Vif (À appeler)
                            ],
                            borderColor: '#fff',
                            borderWidth: 3,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    font: {
                                        family: "'Cairo', sans-serif",
                                        size: 14
                                    },
                                    padding: 20
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
