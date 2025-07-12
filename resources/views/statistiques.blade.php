@extends('layouts.master')

@section('css')
    <link href="{{ URL::asset('assets/plugins/iconfonts/plugin.css') }}" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body, h1, h2, h3, h4, h5, h6, .main-content-title, p, span, div, .tx-13, .tx-12 {
            font-family: 'Cairo', sans-serif !important;
        }
        .stat-card {
            border: 1px solid #e9ecef;
            border-radius: 15px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            padding: 25px;
            background-color: #f8f9fa; /* Fond clair pour la lisibilité */
        }
        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
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
        
        /* ✅ CORRECTION: Texte des cartes en noir foncé */
        .stat-card h6 {
            color: #6c757d !important; /* Gris pour le sous-titre */
        }
        .stat-card h3 {
            color: #2c3e50 !important; /* Noir foncé pour le chiffre */
        }
        
        /* Couleurs pour les icônes */
        .icon-dark { color: #34495e; }
        .icon-primary { color: #3498db; }
        .icon-warning { color: #f39c12; }
        .icon-danger { color: #e74c3c; }

        .breadcrumb-header h5 {
             color: white !important;
        }
    </style>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); padding: 20px; border-radius: 15px;">
        <div class="left-content">
            <div>
                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1 text-white" dir="rtl">إحصائيات المستخدم</h2>
                <p class="mg-b-0 text-white" dir="rtl">نظرة عامة على أدائك وإحصائيات المواعيد.</p>
            </div>
        </div>
        <div class="main-dashboard-header-right text-right">
            <div>
                <label class="tx-13 text-white" dir="rtl">عدد الشركات التي أجابت</label>
                <h5 class="font-weight-bold">{{ $nombreEntreprisesRepondues ?? '0' }}</h5>
            </div>
            <div class="mr-4">
                <label class="tx-13 text-white" dir="rtl">عدد الشركات المخصصة</label>
                <h5 class="font-weight-bold">{{ $nombreEntreprisesAttribuees ?? '0' }}</h5>
            </div>
        </div>
    </div>
    @endsection

@section('content')
    <div class="container-fluid" dir="rtl">
        <div class="row row-sm mb-5 stat-cards-row">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3 animate__animated animate__fadeInUp animate__delay-1s">
                <div class="card text-center stat-card">
                    <i class="typcn typcn-calendar stat-icon icon-dark"></i>
                    <h6 class="tx-16 mg-b-5">إجمالي المواعيد</h6>
                    <h3 class="tx-30 mg-b-0">{{ $totalRendezVous ?? '0' }}</h3>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3 animate__animated animate__fadeInUp animate__delay-2s">
                <div class="card text-center stat-card">
                    <i class="typcn typcn-time stat-icon icon-primary"></i>
                    <h6 class="tx-16 mg-b-5">مواعيد اليوم</h6>
                    <h3 class="tx-30 mg-b-0">{{ $rendezVousAujourdHui ?? '0' }}</h3>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3 animate__animated animate__fadeInUp animate__delay-3s">
                <div class="card text-center stat-card">
                    <i class="typcn typcn-briefcase stat-icon icon-warning"></i>
                    <h6 class="tx-16 mg-b-5">الشركات المخصصة</h6>
                    <h3 class="tx-30 mg-b-0">{{ $nombreEntreprisesAttribuees ?? '0' }}</h3>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3 animate__animated animate__fadeInUp animate__delay-4s">
                <div class="card text-center stat-card">
                    <i class="typcn typcn-tick stat-icon icon-danger"></i>
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
                            <tbody>
                                @php
                                    $traductionsEchantillon = [
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
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4 animate__animated animate__fadeInUp animate__delay-2s">
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
                                @forelse ($evolutionRendezVous['labels'] as $index => $label)
                                    <tr>
                                        <td>{{ $label }}</td>
                                        <td style="font-weight: bold;">{{ $evolutionRendezVous['data'][$index] ?? '0' }}</td>
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
        });
    </script>
@endsection
