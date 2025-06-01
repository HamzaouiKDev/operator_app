@extends('layouts.master')

@section('css')
    {{-- Font Awesome pour plus d'icônes --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
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
            color: #17a2b8 !important; /* Couleur info/cyan pour les stats ici */
            font-weight: 700; 
        }
        .main-dashboard-header-right label {
            color: #5a6268 !important; 
        }

        .card-suivi { /* Nom de classe spécifique */
            border: 1px solid #e3e6f0; 
            border-radius: 0.75rem; 
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.07) !important; 
            transition: all 0.3s ease-in-out;
            margin-bottom: 30px;
        }
        .card-suivi:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
        }

        .card-header-custom-suivi { /* Nom de classe spécifique */
            background-color: #17a2b8; /* Couleur info/cyan pour le header de cette page */
            color: white;
            border-top-left-radius: calc(0.75rem - 1px); 
            border-top-right-radius: calc(0.75rem - 1px);
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.075); 
        }
        .card-header-custom-suivi .card-title {
            font-size: 1.5rem; 
            font-weight: 600;
        }
        .card-header-custom-suivi .card-title i {
            margin-left: 10px; 
        }

        .card-body-custom {
            padding: 1.5rem;
            background-color: #fff;
            border-bottom-left-radius: calc(0.75rem - 1px);
            border-bottom-right-radius: calc(0.75rem - 1px);
        }

        .table-suivi thead th { /* Nom de classe spécifique */
            background-color: #ffffff;
            color: #138496;           /* Texte en cyan foncé */
            font-weight: 700;         
            text-transform: uppercase; 
            font-size: 0.9rem;          
            letter-spacing: 0.05em;     
            padding: 1.1rem 1.25rem;    
            text-align: right;          
            border-top: none;           
            border-bottom: 3px solid #17a2b8; /* Bordure inférieure accentuée cyan */
            white-space: nowrap;        
        }

        .table-suivi tbody tr { /* Nom de classe spécifique */
            transition: background-color 0.15s ease-in-out;
            border-bottom: 1px solid #ecf0f1;
        }
        .table-suivi tbody tr:last-child {
            border-bottom: none;
        }
        .table-suivi tbody tr:hover {
            background-color: #e2f7fa; /* Cyan très clair au survol */
            cursor: pointer;
        }
        .table-suivi td { /* Nom de classe spécifique */
            padding: 0.9rem 1.25rem; 
            vertical-align: middle;
            color: #3e5569;
            font-size: 0.875rem;
        }
        .table-suivi .text-muted { /* Nom de classe spécifique */
            font-size: 0.75rem;
            color: #8898aa !important;
        }
        .table-suivi .company-name { /* Nom de classe spécifique */
            font-weight: 600;
            color: #117a8b; /* Cyan foncé pour nom entreprise */
        }
        .table-suivi .company-name i { /* Nom de classe spécifique */
            margin-left: 8px; 
            color: #17a2b8;
        }

        /* Styles pour search, empty-state, alerts, pagination (similaires à indexRDV, mais les couleurs peuvent être adaptées au thème cyan) */
        .search-form .form-control { /* ... */ }
        .search-form .btn { background-color: #17a2b8; border-color: #17a2b8; /* ... */ }
        .empty-state-suivi i { color: #17a2b8; /* ... */ }
        .pagination .page-item.active .page-link { background-color: #17a2b8; border-color: #17a2b8; }
        .pagination .page-link { color: #17a2b8; }
        .pagination .page-link:hover { color: #117a8b; background-color: #e2f7fa; }
        /* Reprendre les autres styles .search-form, .empty-state-rdv (renommer en .empty-state-suivi), .alert-custom, etc. de indexRDV.blade.php et ajuster les couleurs si besoin */
        .search-form .form-control { border-radius: 0.375rem 0 0 0.375rem !important; border: 1px solid #ced4da; border-left: none; }
        .search-form .btn i { margin-left: 5px; }
        .empty-state-suivi { background-color: #fff; padding: 3rem 1.5rem; border-radius: 0.75rem; text-align: center; color: #6c757d; border: 1px dashed #d1d9e2; }
        .empty-state-suivi p { font-size: 1.05rem; }
        .alert-custom { border-radius: 0.375rem; padding: 0.9rem 1.25rem; font-size: 0.9rem; display: flex; align-items: center; }
        .alert-custom i { margin-left: 10px; font-size: 1.2rem; }
        .alert-success-custom { background-color: #e0f2f1 !important; border-color: #b2dfdb !important; color: #004d40 !important; }
        .alert-danger-custom { background-color: #fce4e4 !important; border-color: #f8c1c1 !important; color: #5f0f0f !important; }
        .alert-warning-custom { background-color: #fff8e1 !important; border-color: #ffecb3 !important; color: #665200 !important; }
        .text-muted small, small.text-muted { color: #8898aa !important; }

    </style>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <div>
                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1" dir="rtl">قائمة المتابعات</h2>
                <p class="mg-b-0" dir="rtl">عرض جميع متابعاتك، مرتبة من الأحدث إلى الأقدم.</p>
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
                <div class="card card-suivi"> {{-- Classe de carte spécifique --}}
                    <div class="card-header card-header-custom-suivi text-center"> {{-- Classe de header spécifique --}}
                        <h4 class="card-title mg-b-0"><i class="fas fa-clipboard-list"></i> متابعاتي</h4>
                    </div>
                    <div class="card-body card-body-custom text-right">
                        {{-- Le formulaire de recherche pourrait filtrer par nom d'entreprise, résultat, ou commentaire --}}
                        <form method="GET" action="{{ route('suivis.index') }}" class="mb-4 search-form"> {{-- Nouvelle route pour la recherche de suivis --}}
                            <div class="input-group">
                                <input type="text" name="search_term" class="form-control" placeholder="البحث في المتابعات (شركة، نتيجة، تعليق)..." value="{{ request('search_term') }}" aria-label="البحث في المتابعات">
                                <div class="input-group-append">
                                    <button class="btn btn-info" type="submit"> {{-- Ou btn-success pour le thème cyan/vert --}}
                                        <i class="fas fa-search"></i> بحث
                                    </button>
                                </div>
                            </div>
                        </form>

                        @if(request('search_term') && (!isset($suivis) || $suivis->isEmpty()))
                            <div class="alert alert-warning-custom text-right mt-3 alert-custom" role="alert">
                                <i class="fas fa-exclamation-triangle"></i>
                                لم يتم العثور على متابعات تطابق بحثك عن: "{{ request('search_term') }}".
                                <a href="{{ route('suivis.index') }}" class="alert-link" style="text-decoration: underline;">إظهار كافة المتابعات</a>.
                            </div>
                        @endif

                        @if(isset($suivis) && $suivis->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-hover mg-b-0 text-md-nowrap table-suivi"> {{-- Classe de table spécifique --}}
                                    <thead>
                                        <tr>
                                            <th style="width: 25%;">الشركة</th>
                                            <th style="width: 20%;">تاريخ المتابعة</th>
                                            <th style="width: 15%;">النتيجة</th>
                                            <th style="width: 40%;">التعليق</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($suivis as $suivi)
                                            @if($suivi->echantillonEnquete && $suivi->echantillonEnquete->entreprise)
                                                <tr title="عرض تفاصيل الشركة: {{ $suivi->echantillonEnquete->entreprise->nom_entreprise }}"
                                                    onclick="window.location='{{ route('entreprise.show', ['entreprise' => $suivi->echantillonEnquete->entreprise_id]) }}'">
                                                    <td class="company-name">
                                                        <i class="fas fa-building"></i>
                                                        {{ $suivi->echantillonEnquete->entreprise->nom_entreprise }}
                                                    </td>
                                                    <td>
                                                        {{ $suivi->date_suivi ? \Carbon\Carbon::parse($suivi->date_suivi)->format('d/m/Y H:i') : 'غير محدد' }}
                                                        @if($suivi->date_suivi)
                                                            <br><small class="text-muted">({{ \Carbon\Carbon::parse($suivi->date_suivi)->locale('ar')->diffForHumans() }})</small>
                                                        @endif
                                                    </td>
                                                    <td><span class="badge badge-pill" style="background-color: #17a2b8; color:white;">{{ $suivi->resultat ?? 'غير متوفر' }}</span></td>
                                                    <td>{{ Str::limit($suivi->commentaire ?? 'لا يوجد', 70) }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if ($suivis->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $suivis->links('pagination::bootstrap-4') }}
                                </div>
                            @endif
                        @elseif(!request('search_term'))
                            <div class="empty-state-suivi"> {{-- Classe d'état vide spécifique --}}
                                <i class="fas fa-folder-open"></i>
                                <p>لا توجد متابعات مسجلة في الوقت الحالي.</p>
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