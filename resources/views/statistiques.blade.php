@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('assets/plugins/iconfonts/plugin.css') }}" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet" />
    <style>
        /* ... (vos styles restent inchangés) ... */
        .stat-card {
            border: none;
            border-radius: 15px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            padding: 25px;
        }
        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.2);
        }
       .stat-icon {
            font-size: 48px;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        .stat-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        .stat-table th, .stat-table td {
            padding: 15px 30px;
            text-align: center;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        .stat-table th {
            background-color: #f8f9fa;
            color: #2c3e50;
            font-weight: 700;
            font-size: 16px;
        }
        /* ... etc ... */
    </style>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);">
        <div class="left-content">
            <div>
                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1 text-white" dir="rtl">إحصائيات المستخدم</h2>
                <p class="mg-b-0 text-white" dir="rtl">نظرة عامة على أدائك وإحصائيات المواعيد.</p>
            </div>
        </div>
        <div class="main-dashboard-header-right">
            <div>
                <label class="tx-13 text-white" dir="rtl">عدد الشركات التي أجابت</label>
                <h5>{{ $nombreEntreprisesRepondues ?? '0' }}</h5>
            </div>
            <div>
                <label class="tx-13 text-white" dir="rtl">عدد الشركات المخصصة</label>
                <h5>{{ $nombreEntreprisesAttribuees ?? '0' }}</h5>
            </div>
        </div>
    </div>
    @endsection

@section('content')
    <div class="container-fluid" dir="rtl">
        @if (!isset($totalRendezVous) || !isset($rendezVousAujourdHui) || !isset($nombreEntreprisesAttribuees) || !isset($nombreEntreprisesRepondues))
            <div class="debug-message">
                تحذير: بعض البيانات غير متوفرة. يرجى التحقق من مصدر البيانات.
            </div>
        @endif

        <div class="row row-sm mb-5 stat-cards-row">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3 animate__animated animate__fadeInUp animate__delay-1s">
                <div class="card text-center stat-card stat-card-dark">
                    <i class="typcn typcn-calendar stat-icon"></i>
                    <h6 class="tx-16 mg-b-5">إجمالي المواعيد</h6>
                    <h3 class="tx-30 mg-b-0">{{ $totalRendezVous ?? '0' }}</h3>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3 animate__animated animate__fadeInUp animate__delay-2s">
                <div class="card text-center stat-card" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);">
                    <i class="typcn typcn-time stat-icon"></i>
                    <h6 class="tx-16 mg-b-5">مواعيد اليوم</h6>
                    <h3 class="tx-30 mg-b-0">{{ $rendezVousAujourdHui ?? '0' }}</h3>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3 animate__animated animate__fadeInUp animate__delay-3s">
                <div class="card text-center stat-card" style="background: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);">
                    <i class="typcn typcn-briefcase stat-icon"></i>
                    <h6 class="tx-16 mg-b-5">الشركات المخصصة</h6>
                    <h3 class="tx-30 mg-b-0">{{ $nombreEntreprisesAttribuees ?? '0' }}</h3>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3 animate__animated animate__fadeInUp animate__delay-4s">
                <div class="card text-center stat-card" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);">
                    <i class="typcn typcn-tick stat-icon"></i>
                    <h6 class="tx-16 mg-b-5">الشركات التي أجابت</h6>
                    <h3 class="tx-30 mg-b-0">{{ $nombreEntreprisesRepondues ?? '0' }}</h3>
                </div>
            </div>
        </div>

        <div class="row row-sm justify-content-center">
           

            <div class="col-lg-6 mb-4 animate__animated animate__fadeInRight animate__delay-1s">
                <div class="card mg-b-20 shadow-sm" style="border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); overflow: hidden;">
                    <div class="card-header pb-0 text-center" style="background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%); color: white; padding: 20px 0;">
                        <h4 class="card-title mg-b-0 tx-22" style="font-weight: 700;">توزيع الشركات حسب الحالة</h4>
                    </div>
                    <div class="card-body" style="padding: 30px; background-color: white;">
                        <table class="stat-table">
                            <thead>
                                <tr>
                                    <th>الحالة</th>
                                    <th>عدد الشركات</th>
                                </tr>
                            </thead>
                            {{-- ✅ DEBUT DE LA SECTION MISE À JOUR --}}
                            <tbody>
                                @php
                                    $traductionsEchantillon = [
                                        'en attente' => 'في الانتظار',
                                        'en attente' => 'في الانتظار',
                                        'Complet' => 'مكتمل',
                                        'Partiel' => 'رد جزئي',
                                        'refus' => 'رفض',
                                        'impossible de contacter' => 'إستحالة الإتصال',
                                        'un rendez-vous' => 'موعد',
                                        'à appeler' => 'إعادة إتصال',
                                    ];
                                @endphp
                                @forelse ($entreprisesParStatut as $statut => $count)
                                    <tr>
                                        <td>{{ $traductionsEchantillon[$statut] ?? ucfirst($statut) }}</td>
                                        <td style="font-weight: bold;">{{ $count }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-muted">لا توجد بيانات للشركات.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            {{-- ✅ FIN DE LA SECTION MISE À JOUR --}}
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 mb-4 animate__animated animate__fadeInUp animate__delay-2s">
                <div class="card mg-b-20 shadow-sm" style="border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); overflow: hidden;">
                    <div class="card-header pb-0 text-center" style="background: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%); color: white; padding: 20px 0;">
                        <h4 class="card-title mg-b-0 tx-22" style="font-weight: 700;">تطور المواعيد (آخر 7 أيام)</h4>
                    </div>
                    <div class="card-body" style="padding: 30px; background-color: white;">
                        <table class="stat-table">
                            <thead>
                                <tr>
                                    <th>اليوم</th>
                                    <th>عدد المواعيد</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $labels = isset($evolutionRendezVous['labels']) ? json_decode('[' . $evolutionRendezVous['labels'] . ']', true) : [];
                                    $data = isset($evolutionRendezVous['data']) ? explode(',', $evolutionRendezVous['data']) : [];
                                @endphp
                                @forelse ($labels as $index => $label)
                                    <tr>
                                        <td>{{ $label }}</td>
                                        <td style="font-weight: bold;">{{ $data[$index] ?? '0' }}</td>
                                    </tr>
                                @empty
                                     <tr>
                                        <td colspan="2" class="text-muted">لا توجد بيانات لعرضها.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log('Page chargée, pas de dépendances JavaScript pour les graphiques.');
            const statCards = document.querySelectorAll('.stat-card');
            if (statCards) {
                statCards.forEach(card => {
                    card.addEventListener('mouseenter', function () {
                        this.style.transform = 'translateY(-8px)';
                        this.style.boxShadow = '0 12px 25px rgba(0, 0, 0, 0.2)';
                    });
                    card.addEventListener('mouseleave', function () {
                        this.style.transform = 'translateY(0)';
                        this.style.boxShadow = '0 8px 20px rgba(0, 0, 0, 0.15)';
                    });
                });
            }
        });
    </script>
@endsection