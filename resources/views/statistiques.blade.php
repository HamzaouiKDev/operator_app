```blade
@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('assets/plugins/iconfonts/plugin.css') }}" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet" />
    <style>
        /* ... (vos autres styles .stat-table, .breadcrumb-header etc. restent inchangés) ... */

        .stat-card {
            border: none;
            border-radius: 15px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            /* La couleur par défaut du texte dans stat-card n'est plus nécessaire ici si nous spécifions pour h6, h3, icon */
            padding: 25px;
        }
        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.2);
        }

       .stat-icon {
            font-size: 48px; /* MODIFIÉ : Augmenté de 40px à 48px. Vous pouvez ajuster cette valeur. */
            margin-bottom: 10px; /* Conserve la marge inférieure */
            opacity: 0.9;        /* Conserve l'opacité */
            /* La couleur de l'icône est gérée par des règles plus spécifiques comme : */
            /* .stat-card.stat-card-dark .stat-icon et .stat-card:not(.stat-card-dark) .stat-icon */
        }
        /* Styles pour les AUTRES cartes de statistiques (celles qui ne sont PAS .stat-card-dark) */
        .stat-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        /* MODIFICATION ICI pour les cellules des tableaux de statistiques */
        .stat-table th, .stat-table td {
            padding: 15px 30px; /* Augmenté : 15px haut/bas, 30px gauche/droite pour plus d'espace */
            text-align: center;  /* MODIFIÉ : Texte centré */
            border-bottom: 1px solid #eee;
            vertical-align: middle; /* Assure un bon alignement vertical si le contenu a des hauteurs différentes */
        }

        .stat-table th {
            background-color: #f8f9fa;
            color: #2c3e50;
            font-weight: 700;
            font-size: 16px;
        }
        .stat-table td {
            color: #2c3e50;
            font-size: 14px;
        }
        .stat-table tr:last-child td {
            border-bottom: none;
        }
        .stat-table tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }

        /* Titres, icônes et chiffres tous en noir/gris foncé */

        .stat-card:not(.stat-card-dark) h6 {       /* Titres des cartes générales */
            color: #212529 !important;             /* Noir/gris foncé (était blanc) */
            text-shadow: none;                     /* Enlever l'ombre si elle ne convient pas au texte noir */
        }

        .stat-card:not(.stat-card-dark) .stat-icon { /* Icônes des cartes générales */
            color: #212529 !important;             /* Noir/gris foncé (était blanc) */
            opacity: 1;                            /* S'assurer qu'elles sont bien visibles */
        }

        .stat-card:not(.stat-card-dark) h3 {       /* Chiffres des cartes générales */
            color: #212529 !important;             /* Noir/gris foncé (était #2c3e50, harmonisé) */
        }

        /* Styles existants pour .main-dashboard-header-right h5 et .debug-message restent inchangés */
        .main-dashboard-header-right h5 {
            color: #2c3e50 !important; /* Dark color for header numbers */
            font-weight: 700;
        }
        .debug-message {
            color: #e74c3c;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
            font-size: 16px;
            background-color: #ffe6e6;
            padding: 10px;
            border-radius: 5px;
        }
        .stat-cards-row > [class*="col-"] { /* Cible toutes les classes de colonnes directes de .stat-cards-row */
            padding-left: 25px;  /* Augmentez cette valeur pour plus d'espace à gauche de la carte */
            padding-right: 25px; /* Augmentez cette valeur pour plus d'espace à droite de la carte */
        }
    </style>
@endsection

@section('page-header')
    <!-- breadcrumb -->
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
    <!-- /breadcrumb -->
@endsection

@section('content')
    <div class="container-fluid" dir="rtl">
        <!-- Debug message if data is missing -->
        @if (!isset($totalRendezVous) || !isset($rendezVousAujourdHui) || !isset($nombreEntreprisesAttribuees) || !isset($nombreEntreprisesRepondues))
            <div class="debug-message">
                تحذير: بعض البيانات غير متوفرة. يرجى التحقق من مصدر البيانات.
            </div>
        @endif

        <!-- Statistiques rapides sous forme de cartes -->
        <div class="row row-sm mb-5 stat-cards-row">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3 animate__animated animate__fadeInUp animate__delay-1s">
                        <div class="card text-center stat-card stat-card-dark"> {{-- IMPORTANT: Pas de style="background: ..." ici et stat-card-dark est présent --}}
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

        <!-- Statistiques sous forme de tableaux -->
        <div class="row row-sm">
            <!-- Tableau : Répartition des rendez-vous par statut -->
            <div class="col-lg-6 mb-4 animate__animated animate__fadeInLeft animate__delay-1s">
                <div class="card mg-b-20 shadow-sm chart-card" style="border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); overflow: hidden;">
                    <div class="card-header pb-0 text-center" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white; padding: 20px 0; border-radius: 15px 15px 0 0;">
                        <h4 class="card-title mg-b-0 tx-22" style="font-weight: 700;">توزيع المواعيد حسب الحالة</h4>
                    </div>
                    <div class="card-body" style="padding: 30px; background-color: white;">
                        <table class="stat-table">
                            <thead>
                                <tr>
                                    <th>الحالة</th>
                                    <th>عدد المواعيد</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="background-color: rgba(46, 204, 113, 0.1);">Planifié</td>
                                    <td style="font-weight: bold;">{{ $rendezVousParStatut['planifie'] ?? '5' }}</td>
                                </tr>
                                <tr>
                                    <td style="background-color: rgba(52, 152, 219, 0.1);">Confirmé</td>
                                    <td style="font-weight: bold;">{{ $rendezVousParStatut['confirme'] ?? '3' }}</td>
                                </tr>
                                <tr>
                                    <td style="background-color: rgba(231, 76, 60, 0.1);">Annulé</td>
                                    <td style="font-weight: bold;">{{ $rendezVousParStatut['annule'] ?? '2' }}</td>
                                </tr>
                                <tr>
                                    <td style="background-color: rgba(241, 196, 15, 0.1);">Terminé</td>
                                    <td style="font-weight: bold;">{{ $rendezVousParStatut['termine'] ?? '4' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tableau : Répartition des entreprises par statut -->
            <div class="col-lg-6 mb-4 animate__animated animate__fadeInRight animate__delay-1s">
                <div class="card mg-b-20 shadow-sm chart-card" style="border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); overflow: hidden;">
                    <div class="card-header pb-0 text-center" style="background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%); color: white; padding: 20px 0; border-radius: 15px 15px 0 0;">
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
                                <tr>
                                    <td style="background-color: rgba(46, 204, 113, 0.1);">Répondu</td>
                                    <td style="font-weight: bold;">{{ $entreprisesParStatut['repondu'] ?? '4' }}</td>
                                </tr>
                                <tr>
                                    <td style="background-color: rgba(241, 196, 15, 0.1);">Réponse Partielle</td>
                                    <td style="font-weight: bold;">{{ $entreprisesParStatut['partiel'] ?? '2' }}</td>
                                </tr>
                                <tr>
                                    <td style="background-color: rgba(52, 152, 219, 0.1);">Pas de Réponse</td>
                                    <td style="font-weight: bold;">{{ $entreprisesParStatut['pas_reponse'] ?? '3' }}</td>
                                </tr>
                                <tr>
                                    <td style="background-color: rgba(231, 76, 60, 0.1);">Refus</td>
                                    <td style="font-weight: bold;">{{ $entreprisesParStatut['refus'] ?? '1' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tableau : Évolution des rendez-vous sur les 7 derniers jours -->
            <div class="col-lg-12 mb-4 animate__animated animate__fadeInUp animate__delay-2s">
                <div class="card mg-b-20 shadow-sm chart-card" style="border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); overflow: hidden;">
                    <div class="card-header pb-0 text-center" style="background: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%); color: white; padding: 20px 0; border-radius: 15px 15px 0 0;">
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
                                    $labels = isset($evolutionRendezVous['labels']) ? json_decode('[' . $evolutionRendezVous['labels'] . ']', true) : ['Jour 1', 'Jour 2', 'Jour 3', 'Jour 4', 'Jour 5', 'Jour 6', 'Jour 7'];
                                    $data = isset($evolutionRendezVous['data']) ? explode(',', $evolutionRendezVous['data']) : [5, 3, 7, 2, 8, 4, 6];
                                @endphp
                                @foreach ($labels as $index => $label)
                                    <tr>
                                        <td style="background-color: rgba(46, 204, 113, 0.1);">{{ $label }}</td>
                                        <td style="font-weight: bold;">{{ $data[$index] ?? '0' }}</td>
                                    </tr>
                                @endforeach
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

            // Effet de survol sur les cartes de statistiques
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
```