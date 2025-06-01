@extends('layouts.master')

@section('css')
    {{-- Font Awesome pour plus d'icônes --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    {{-- Vos autres liens CSS si nécessaires --}}
    <link href="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
    {{-- Suggestion: Importez la police Cairo dans votre layouts.master.blade.php --}}
    {{-- <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet"> --}}

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
            background-color: #007bff; 
            color: white;
            border-top-left-radius: calc(0.75rem - 1px); 
            border-top-right-radius: calc(0.75rem - 1px);
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.075); 
        }
        .card-header-custom .card-title {
            font-size: 1.5rem; 
            font-weight: 600;
        }
        .card-header-custom .card-title i {
            margin-left: 10px; 
        }


        .card-body-custom {
            padding: 1.5rem;
            background-color: #fff;
            border-bottom-left-radius: calc(0.75rem - 1px);
            border-bottom-right-radius: calc(0.75rem - 1px);
        }

        /* --- AMÉLIORATION DE L'EN-TÊTE DU TABLEAU (TITRES PLUS GRANDS ET JOLIE COULEUR) --- */
        .table-rdv thead th {
            background-color: #ffffff;    /* Fond blanc, se fond avec le corps de la carte */
            color: #007bff;             /* ✅ JOLIE COULEUR: Texte en bleu primaire */
            font-weight: 700;            /* Gras pour l'importance */
            text-transform: uppercase;   /* Texte en majuscules pour un style d'en-tête distinct */
            font-size: 0.9rem;           /* ✅ PLUS GRAND: Taille de police augmentée */
            letter-spacing: 0.05em;      /* Espacement des lettres pour la lisibilité */
            padding: 1.1rem 1.25rem;     /* Padding ajusté pour la nouvelle taille */
            text-align: right;           /* Maintenir l'alignement RTL */
            border-top: none;            /* Pas de bordure en haut */
            border-bottom: 3px solid #007bff; /* Bordure inférieure accentuée avec la couleur primaire */
            white-space: nowrap;         /* Empêcher le texte de passer à la ligne si possible */
        }
        /* --- FIN AMÉLIORATION --- */

        .table-rdv tbody tr {
            transition: background-color 0.15s ease-in-out;
            border-bottom: 1px solid #ecf0f1;
        }
        .table-rdv tbody tr:last-child {
            border-bottom: none;
        }
        .table-rdv tbody tr:hover {
            background-color: #e9f5ff;
            cursor: pointer;
        }
        .table-rdv td {
            padding: 0.9rem 1.25rem; 
            vertical-align: middle;
            color: #3e5569;
            font-size: 0.875rem;
        }
        .table-rdv .text-muted {
            font-size: 0.75rem;
            color: #8898aa !important;
        }
        .table-rdv .company-name {
            font-weight: 600;
            color: #0062cc;
        }
        .table-rdv .company-name i {
            margin-left: 8px; 
            color: #007bff;
        }

        .search-form .form-control {
            border-radius: 0.375rem 0 0 0.375rem !important;
            border: 1px solid #ced4da;
            border-left: none;
        }
        .search-form .btn-info {
            border-radius: 0 0.375rem 0.375rem 0 !important;
            background-color: #17a2b8;
            border-color: #17a2b8;
            padding: 0.5rem 1rem;
        }
        .search-form .btn-info i {
            margin-left: 5px;
        }

        .empty-state-rdv {
            background-color: #fff;
            padding: 3rem 1.5rem;
            border-radius: 0.75rem;
            text-align: center;
            color: #6c757d;
            border: 1px dashed #d1d9e2;
        }
        .empty-state-rdv i {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            color: #adb5bd;
        }
        .empty-state-rdv p {
            font-size: 1.05rem;
        }
        .alert-custom {
            border-radius: 0.375rem;
            padding: 0.9rem 1.25rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }
        .alert-custom i {
            margin-left: 10px;
            font-size: 1.2rem;
        }
        .alert-success-custom { background-color: #e0f2f1 !important; border-color: #b2dfdb !important; color: #004d40 !important; }
        .alert-danger-custom { background-color: #fce4e4 !important; border-color: #f8c1c1 !important; color: #5f0f0f !important; }
        .alert-warning-custom { background-color: #fff8e1 !important; border-color: #ffecb3 !important; color: #665200 !important; }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
        }
        .pagination .page-link {
            color: #007bff;
            border-radius: 0.25rem;
            margin: 0 3px;
        }
        .pagination .page-link:hover {
            color: #0056b3;
            background-color: #e9ecef;
        }
         .text-muted small, small.text-muted {
            color: #8898aa !important;
        }
    </style>
@endsection

@section('page-header')
    {{-- L'en-tête de page reste identique à la version précédente --}}
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <div>
                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1" dir="rtl">قائمة مواعيدك</h2>
                <p class="mg-b-0" dir="rtl">عرض جميع مواعيدك المرتبة حسب القرب الزمني.</p>
            </div>
        </div>
        <div class="main-dashboard-header-right">
            <div><label class="tx-13" dir="rtl">عدد الشركات التي أجابت</label><h5>{{ $nombreEntreprisesRepondues ?? '0' }}</h5></div>
            <div><label class="tx-13" dir="rtl">عدد الشركات المخصصة لك</label><h5>{{ $nombreEntreprisesAttribuees ?? '0' }}</h5></div>
        </div>
    </div>
@endsection

@section('content')
    {{-- La section content reste identique à la version précédente --}}
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
                        <h4 class="card-title mg-b-0"><i class="fas fa-calendar-alt"></i> مواعيدي</h4>
                    </div>
                    <div class="card-body card-body-custom text-right">
                        <form method="GET" action="{{ route('rendezvous.index') }}" class="mb-4 search-form">
                            <div class="input-group">
                                <input type="text" name="search_entreprise" class="form-control" placeholder="البحث عن شركة..." value="{{ request('search_entreprise') }}" aria-label="البحث عن شركة">
                                <div class="input-group-append">
                                    <button class="btn btn-info" type="submit">
                                        <i class="fas fa-search"></i> بحث
                                    </button>
                                </div>
                            </div>
                        </form>

                        @if(request('search_entreprise') && (!isset($rendezVous) || $rendezVous->isEmpty()))
                            <div class="alert alert-warning-custom text-right mt-3 alert-custom" role="alert">
                                <i class="fas fa-exclamation-triangle"></i>
                                لم يتم العثور على مواعيد لشركات تطابق بحثك عن: "{{ request('search_entreprise') }}".
                                <a href="{{ route('rendezvous.index') }}" class="alert-link" style="text-decoration: underline;">إظهار كافة المواعيد</a>.
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
                                                    onclick="window.location='{{ route('entreprise.show', ['entreprise' => $rdv->echantillonEnquete->entreprise_id]) }}'">
                                                    <td class="company-name">
                                                        <i class="fas fa-building"></i>
                                                        {{ $rdv->echantillonEnquete->entreprise->nom_entreprise }}
                                                    </td>
                                                    <td>
                                                        {{ $rdv->heure_rdv ? \Carbon\Carbon::parse($rdv->heure_rdv)->format('d/m/Y H:i') : 'غير محدد' }}
                                                        @if($rdv->heure_rdv)
                                                            <br><small class="text-muted">({{ \Carbon\Carbon::parse($rdv->heure_rdv)->locale('ar')->diffForHumans() }})</small>
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
                                <i class="fas fa-calendar-times"></i>
                                <p>لا توجد مواعيد مسجلة في الوقت الحالي.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    {{-- Le JavaScript reste identique à la version précédente --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.auto-hide');
                alerts.forEach(alert => {
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
        });
    </script>
@endsection