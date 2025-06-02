@extends('layouts.master')

@section('css')
    {{-- Font Awesome pour plus d'icônes --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    {{-- Vos autres liens CSS si nécessaires --}}
    <link href="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa; 
            font-family: 'Cairo', 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            color: #333; 
        }

        .breadcrumb-header {
            background-color: #ffffff !important;
            border-bottom: 1px solid #dee2e6; 
            padding-top: 15px;
            padding-bottom: 15px;
        }
        .main-content-title, .breadcrumb-header p {
            color: #212529 !important; 
        }
        .main-dashboard-header-right h5 {
            color: #0069d9 !important; 
            font-weight: 700; 
        }
        .main-dashboard-header-right label {
            color: #5a6268 !important; 
        }

        .card-rdv {
            border: 1px solid #e3e6f0; 
            border-radius: 0.75rem; 
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.07) !important; 
            transition: all 0.3s ease-in-out;
            margin-bottom: 30px;
        }
        .card-rdv:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
        }

        .card-header-custom {
            background-color: #28a745; /* Vert Bootstrap success */
            color: white;
            border-top-left-radius: calc(0.75rem - 1px); 
            border-top-right-radius: calc(0.75rem - 1px);
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.075); 
        }
        .card-header-custom .card-title { font-size: 1.5rem; font-weight: 600; }
        .card-header-custom .card-title i { margin-left: 10px; }

        .card-body-custom {
            padding: 1.5rem;
            background-color: #fff;
            border-bottom-left-radius: calc(0.75rem - 1px);
            border-bottom-right-radius: calc(0.75rem - 1px);
        }

        .table-rdv thead th {
            background-color: #ffffff; color: #28a745; font-weight: 700;       
            text-transform: uppercase; font-size: 0.9rem; letter-spacing: 0.05em;     
            padding: 1.1rem 1.25rem; text-align: right; border-top: none;           
            border-bottom: 3px solid #28a745; white-space: nowrap;        
        }

        .table-rdv tbody tr {
            transition: background-color 0.15s ease-in-out;
            border-bottom: 1px solid #ecf0f1;
        }
        .table-rdv tbody tr:last-child { border-bottom: none; }
        .table-rdv tbody tr:hover {
            background-color: #e6f7e9; /* Vert très clair au survol par défaut */
            cursor: pointer;
        }
        .table-rdv td {
            padding: 0.9rem 1.25rem; vertical-align: middle;
            color: #3e5569; font-size: 0.875rem;
        }
        .table-rdv .text-muted { font-size: 0.75rem; color: #8898aa !important; }
        .table-rdv .company-name { font-weight: 600; color: #1e7e34; }
        .table-rdv .company-name i { margin-left: 8px; color: #28a745; }

        .search-form .form-control {
            border-radius: 0.375rem 0 0 0.375rem !important;
            border: 1px solid #ced4da; border-left: none;
        }
        .search-form .btn { 
            border-radius: 0 0.375rem 0.375rem 0 !important;
            padding: 0.5rem 1rem;
        }
        .search-form .btn-success { background-color: #28a745; border-color: #28a745; }
        .search-form .btn i { margin-left: 5px; }

        .empty-state-rdv {
            background-color: #fff; padding: 3rem 1.5rem; border-radius: 0.75rem;
            text-align: center; color: #6c757d; border: 1px dashed #d1d9e2;
        }
        .empty-state-rdv i { font-size: 3.5rem; margin-bottom: 1rem; color: #28a745; }
        .empty-state-rdv p { font-size: 1.05rem; }

        .alert-custom {
            border-radius: 0.375rem; padding: 0.9rem 1.25rem; font-size: 0.9rem;
            display: flex; align-items: center;
        }
        .alert-custom i { margin-left: 10px; font-size: 1.2rem; }
        .alert-success-custom { background-color: #d4edda !important; border-color: #c3e6cb !important; color: #155724 !important; }
        .alert-danger-custom { background-color: #f8d7da !important; border-color: #f5c6cb !important; color: #721c24 !important; }
        .alert-warning-custom { background-color: #fff3cd !important; border-color: #ffeeba !important; color: #856404 !important; }

        .pagination .page-item.active .page-link { background-color: #28a745; border-color: #28a745; color: white; }
        .pagination .page-link { color: #28a745; border-radius: 0.25rem; margin: 0 3px; }
        .pagination .page-link:hover { color: #1e7e34; background-color: #e6f7e9; }
        .text-muted small, small.text-muted { color: #8898aa !important; }

        /* --- NOUVEAUX STYLES POUR LES ANIMATIONS RDV --- */
        .rdv-highlight-green {
            animation: pulse-green 1.5s infinite ease-in-out;
        }
        @keyframes pulse-green {
            0% { background-color: inherit; } /* Ou la couleur de fond normale de la ligne si différente de #fff */
            50% { background-color: #d4edda; } /* Vert pâle (Bootstrap success background) */
            100% { background-color: inherit; }
        }
        .table-rdv tbody tr.rdv-highlight-green:hover {
            animation: none; /* Stoppe l'animation de pulsation au survol */
            background-color: #c3e6cb; /* Un vert légèrement plus soutenu pour le survol */
        }

        .rdv-highlight-red {
            background-color: #f8d7da !important; /* Rouge pâle (Bootstrap danger background), important pour passer outre le hover de base */
        }
        /* Optionnel: si le texte devient difficile à lire sur fond rouge pâle */
        /* .rdv-highlight-red td, .rdv-highlight-red td small, .rdv-highlight-red .company-name {
            color: #58151c !important; 
        } */
        .table-rdv tbody tr.rdv-highlight-red:hover {
            background-color: #f5c6cb !important; /* Un rouge légèrement plus soutenu pour le survol */
        }
        /* --- FIN NOUVEAUX STYLES --- */
    </style>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <div>
                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1" dir="rtl">مواعيد اليوم</h2>
                <p class="mg-b-0" dir="rtl">عرض مواعيدك المجدولة لهذا اليوم، مرتبة حسب القرب الزمني.</p>
            </div>
        </div>
        <div class="main-dashboard-header-right">
            <div><label class="tx-13" dir="rtl">عدد الشركات التي أجابت</label><h5>{{ $nombreEntreprisesRepondues ?? '0' }}</h5></div>
            <div><label class="tx-13" dir="rtl">عدد الشركات المخصصة لك</label><h5>{{ $nombreEntreprisesAttribuees ?? '0' }}</h5></div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid" dir="rtl">
        @if (session('success'))
            <div class="alert alert-success-custom mg-b-20 text-right auto-hide alert-custom" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger-custom mg-b-20 text-right auto-hide alert-custom" role="alert">
                <i class="fas fa-times-circle"></i> {{ session('error') }}
            </div>
        @endif

        <div class="row row-sm">
            <div class="col-lg-12">
                <div class="card card-rdv">
                    <div class="card-header card-header-custom text-center">
                        <h4 class="card-title mg-b-0"><i class="fas fa-calendar-day"></i> مواعيد اليوم</h4>
                    </div>
                    <div class="card-body card-body-custom text-right">
                        <form method="GET" action="{{ route('rendezvous.aujourdhui') }}" class="mb-4 search-form">
                            <div class="input-group">
                                <input type="text" name="search_entreprise" class="form-control" placeholder="البحث عن شركة..." value="{{ request('search_entreprise') }}" aria-label="البحث عن شركة">
                                <div class="input-group-append">
                                    <button class="btn btn-success" type="submit">
                                        <i class="fas fa-search"></i> بحث
                                    </button>
                                </div>
                            </div>
                        </form>

                        @if(request('search_entreprise') && (!isset($rendezVous) || $rendezVous->isEmpty()))
                            <div class="alert alert-warning-custom text-right mt-3 alert-custom" role="alert">
                                <i class="fas fa-exclamation-triangle"></i>
                                لم يتم العثور على مواعيد لشركات تطابق بحثك عن: "{{ request('search_entreprise') }}" لهذا اليوم.
                                <a href="{{ route('rendezvous.aujourdhui') }}" class="alert-link" style="text-decoration: underline;">إظهار كافة مواعيد اليوم</a>.
                            </div>
                        @endif

                        @if(isset($rendezVous) && $rendezVous->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-hover mg-b-0 text-md-nowrap table-rdv">
                                    <thead>
                                        <tr>
                                            <th style="width: 30%;">الشركة</th>
                                            <th style="width: 25%;">وقت الموعد</th>
                                            <th style="width: 25%;">جهة الاتصال بالموعد</th>
                                            <th style="width: 20%;">ملاحظات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rendezVous as $rdv)
                                            @if($rdv->echantillonEnquete && $rdv->echantillonEnquete->entreprise)
                                                <tr title="عرض تفاصيل الشركة: {{ $rdv->echantillonEnquete->entreprise->nom_entreprise }}"
                                                    data-rdv-time="{{ $rdv->heure_rdv ? \Carbon\Carbon::parse($rdv->heure_rdv)->toIso8601String() : '' }}"
                                                    onclick="window.location='{{ route('echantillons.show', ['echantillon' => $rdv->echantillonEnquete->id]) }}'">
                                                    <td class="company-name">
                                                        <i class="fas fa-building"></i>
                                                        {{ $rdv->echantillonEnquete->entreprise->nom_entreprise }}
                                                    </td>
                                                    <td>
                                                        {{ $rdv->heure_rdv ? \Carbon\Carbon::parse($rdv->heure_rdv)->format('H:i') : 'غير محدد' }}
                                                        @if($rdv->heure_rdv)
                                                            <br><small class="text-muted">({{ (\Carbon\Carbon::parse($rdv->heure_rdv)->setTimezone(config('app.timezone')))->locale('ar')->diffForHumans() }})</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ $rdv->contact_rdv ?? 'غير متوفر' }}</td>
                                                    <td>{{ Str::limit($rdv->notes ?? 'غير متوفرة', 45) }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if ($rendezVous->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $rendezVous->links('pagination::bootstrap-4') }}
                                </div>
                            @endif
                        @elseif(!request('search_entreprise'))
                            <div class="empty-state-rdv">
                                <i class="fas fa-calendar-check"></i>
                                <p>لا توجد مواعيد مسجلة لهذا اليوم.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Script existant pour masquer les alertes
            setTimeout(function() {
                const autoHideAlerts = document.querySelectorAll('.auto-hide');
                autoHideAlerts.forEach(alert => {
                    if (alert) {
                        let opacity = 1;
                        const timer = setInterval(function () {
                            if (opacity <= 0.1) {
                                clearInterval(timer);
                                alert.style.display = 'none';
                                if(alert.parentNode) {
                                    alert.parentNode.removeChild(alert);
                                }
                            }
                            alert.style.opacity = opacity;
                            alert.style.filter = 'alpha(opacity=' + opacity * 100 + ")";
                            opacity -= opacity * 0.1;
                        }, 50);
                    }
                });
            }, 4000);

            // Fonction pour mettre à jour la mise en évidence des RDV
            function updateAppointmentHighlights() {
                const now = new Date();
                const fifteenMinutesInMilliseconds = 15 * 60 * 1000;

                document.querySelectorAll('.table-rdv tbody tr[data-rdv-time]').forEach(row => {
                    const rdvTimeString = row.dataset.rdvTime;
                    if (!rdvTimeString) {
                        return;
                    }

                    const rdvTime = new Date(rdvTimeString);
                    const timeDifference = rdvTime.getTime() - now.getTime();

                    // Réinitialiser les classes de mise en évidence avant d'appliquer la nouvelle
                    row.classList.remove('rdv-highlight-green', 'rdv-highlight-red');

                    if (timeDifference < 0) {
                        // Le RDV est dans le passé (pour aujourd'hui)
                        row.classList.add('rdv-highlight-red');
                    } else if (timeDifference <= fifteenMinutesInMilliseconds) {
                        // Le RDV est dans le futur ET dans les 15 prochaines minutes
                        row.classList.add('rdv-highlight-green');
                    }
                    // Sinon (RDV dans le futur mais dans plus de 15 minutes), aucune classe spéciale n'est ajoutée.
                });
            }

            // Exécuter la fonction au chargement de la page
            updateAppointmentHighlights();

            // Exécuter la fonction toutes les minutes pour mettre à jour le statut
            setInterval(updateAppointmentHighlights, 60000); // 60000 ms = 1 minute
        });
    </script>
@endsection