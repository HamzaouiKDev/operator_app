@extends('layouts.master')

@section('css')
    {{-- Vos CSS existants. Font Awesome est utilisé pour les icônes. --}}
    <link href="{{URL::asset('assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/iconfonts/plugin.css')}}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Styles pour les cartes KPI */
        .kpi-card {
            /* Changé la bordure de gauche à droite pour RTL */
            border-right: 5px solid #3498db;
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
        /* Assurer que le texte dans les tableaux est bien à droite */
        .table th, .table td {
            text-align: right !important;
        }
    </style>
@endsection

@section('page-header')
    {{-- Le header est maintenant en RTL --}}
    <div class="breadcrumb-header justify-content-between" dir="rtl">
        <div class="left-content">
            <div>
                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">لوحة تحكم المسؤول</h2>
                <p class="mg-b-0">نظرة عامة على نشاط مركز الاتصال.</p>
            </div>
        </div>
        <div class="main-dashboard-header-right">
            <div>
                <label class="tx-13">التاريخ الحالي</label>
                {{-- Pour afficher les mois en arabe, il faudrait configurer la locale de Carbon dans AppServiceProvider --}}
                <h5>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</h5>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid" dir="rtl">
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
                                <p class="mb-0 tx-12 text-white op-7">الحالات: تم الرد، مكتمل، مرفوض</p>
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
        <div class="row row-sm">
            <div class="col-md-12 col-lg-6 col-xl-7">
                <div class="card">
                    <div class="card-header bg-transparent pd-b-0 pd-t-20 bd-b-0">
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title mb-0">أداء المشغلين</h4>
                            <i class="mdi mdi-dots-horizontal text-gray"></i>
                        </div>
                        <p class="tx-12 text-muted mb-0">نظرة عامة على النشاط حسب المستخدم.</p>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped mg-b-0 text-md-nowrap">
                                <thead>
                                    <tr>
                                        <th>المعرف</th>
                                        <th>الاسم</th>
                                        <th>العينات المعالجة</th>
                                        <th>المواعيد المأخوذة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($teleoperateurs as $teleoperateur)
                                        <tr>
                                            <th scope="row">{{ $teleoperateur->id }}</th>
                                            <td>{{ $teleoperateur->name }}</td>
                                            <td><span class="badge badge-success">{{ $teleoperateur->echantillons_traites }}</span></td>
                                            <td><span class="badge badge-info">{{ $teleoperateur->rdv_pris }}</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">لم يتم العثور على أي مشغل هاتفي.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-xl-5">
                <div class="card">
                    <div class="card-header bg-transparent pd-b-0 pd-t-20 bd-b-0">
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title mb-0">توزيع الحالات</h4>
                            <i class="mdi mdi-dots-horizontal text-gray"></i>
                        </div>
                        <p class="tx-12 text-muted mb-0">حالة جميع العينات.</p>
                    </div>
                    <div class="card-body">
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
                            label: 'عدد العينات', // Traduit en arabe
                            data: @json($chartData),
                            backgroundColor: [
                                'rgba(40, 167, 69, 0.8)',
                                'rgba(23, 162, 184, 0.8)',
                                'rgba(220, 53, 69, 0.8)',
                                'rgba(255, 193, 7, 0.8)',
                                'rgba(108, 117, 125, 0.8)',
                                'rgba(52, 58, 64, 0.8)',
                                'rgba(0, 123, 255, 0.8)'
                            ],
                            borderColor: ['rgba(255, 255, 255, 1)'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: { // Dans Chart.js v3+, legend est dans plugins
                            legend: {
                                position: 'bottom',
                                labels: {
                                    // Optionnel: pour s'assurer que la police est correcte
                                    font: {
                                        family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection