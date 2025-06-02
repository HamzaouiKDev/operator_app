@extends('layouts.master')

@section('css')
    {{-- Font Awesome pour plus d'icônes --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    {{-- Vos autres liens CSS si nécessaires --}}
    <link href="{{ URL::asset('assets/plugins/owl-carousel/owl.carousel.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/jqvmap/jqvmap.min.css') }}" rel="stylesheet">

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

        .card-suivi { /* Changé de card-rdv pour potentiellement styliser différemment si besoin */
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

        .card-header-custom {
            background-color: #17a2b8; /* Couleur Info, pour différencier des RDV */
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

        .table-suivi thead th { /* Changé de table-rdv */
            background-color: #ffffff;
            color: #17a2b8; /* Couleur Info */
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.05em;
            padding: 1.1rem 1.25rem;
            text-align: right;
            border-top: none;
            border-bottom: 3px solid #17a2b8; /* Couleur Info */
            white-space: nowrap;
        }

        .table-suivi tbody tr {
            transition: background-color 0.15s ease-in-out;
            border-bottom: 1px solid #ecf0f1;
        }
        .table-suivi tbody tr:last-child {
            border-bottom: none;
        }
        .table-suivi tbody tr:hover {
            background-color: #e0f7fa; /* Lighter Info Hover */
            cursor: pointer;
        }
        .table-suivi td {
            padding: 0.9rem 1.25rem;
            vertical-align: middle;
            color: #3e5569;
            font-size: 0.875rem;
        }
        .table-suivi .text-muted {
            font-size: 0.75rem;
            color: #8898aa !important;
        }
        .table-suivi .company-name {
            font-weight: 600;
            color: #138496; /* Darker Info */
        }
        .table-suivi .company-name i {
            margin-left: 8px;
            color: #17a2b8;
        }

        .search-form .form-control {
            border-radius: 0.375rem 0 0 0.375rem !important;
            border: 1px solid #ced4da;
            border-left: none; /* RTL support: this should be border-right: none if input is first */
        }
        .search-form .btn-primary { /* Using primary for search button for consistency or btn-info */
            border-radius: 0 0.375rem 0.375rem 0 !important; /* RTL support: adjust if button is first */
            background-color: #007bff;
            border-color: #007bff;
            padding: 0.5rem 1rem;
        }
        .search-form .btn-primary i {
            margin-left: 5px;
        }

        .empty-state-suivi { /* Changé de empty-state-rdv */
            background-color: #fff;
            padding: 3rem 1.5rem;
            border-radius: 0.75rem;
            text-align: center;
            color: #6c757d;
            border: 1px dashed #d1d9e2;
        }
        .empty-state-suivi i {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            color: #adb5bd;
        }
        .empty-state-suivi p {
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
            margin-left: 10px; /* RTL: margin-right */
        }
        .alert-success-custom { background-color: #e0f2f1 !important; border-color: #b2dfdb !important; color: #004d40 !important; }
        .alert-danger-custom { background-color: #fce4e4 !important; border-color: #f8c1c1 !important; color: #5f0f0f !important; }
        .alert-warning-custom { background-color: #fff8e1 !important; border-color: #ffecb3 !important; color: #665200 !important; }

        .pagination .page-item.active .page-link {
            background-color: #17a2b8; /* Couleur Info pour pagination active */
            border-color: #17a2b8;
            color: white;
        }
        .pagination .page-link {
            color: #17a2b8; /* Couleur Info */
            border-radius: 0.25rem;
            margin: 0 3px;
        }
        .pagination .page-link:hover {
            color: #117a8b; /* Darker Info */
            background-color: #e9ecef;
        }
        .text-muted small, small.text-muted {
            color: #8898aa !important;
        }
    </style>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <div>
                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1" dir="rtl">قائمة متابعاتك</h2>
                <p class="mg-b-0" dir="rtl">عرض جميع إجراءات المتابعة المخطط لها أو المنجزة.</p>
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
                <div class="card card-suivi">
                    <div class="card-header card-header-custom text-center">
                        <h4 class="card-title mg-b-0"><i class="fas fa-clipboard-list"></i> متابعاتي</h4>
                    </div>
                    <div class="card-body card-body-custom text-right">
                        <form method="GET" action="{{ route('suivis.index') }}" class="mb-4 search-form">
                            <div class="input-group">
                                <input type="text" name="search_term" class="form-control" placeholder="البحث عن طريق الشركة، الملاحظة، السبب..." value="{{ request('search_term') }}" aria-label="البحث في المتابعات">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit"> 
                                        <i class="fas fa-search"></i> بحث
                                    </button>
                                </div>
                            </div>
                        </form>

                        @if(request('search_term') && $suivis->isEmpty())
                            <div class="alert alert-warning-custom text-right mt-3 alert-custom" role="alert">
                                <i class="fas fa-exclamation-triangle"></i>
                                لم يتم العثور على متابعات تطابق بحثك عن: "{{ request('search_term') }}".
                                <a href="{{ route('suivis.index') }}" class="alert-link" style="text-decoration: underline;">إظهار كافة المتابعات</a>.
                            </div>
                        @endif

                        @if($suivis->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-hover mg-b-0 text-md-nowrap table-suivi">
                                    <thead>
                                        <tr>
                                            <th style="width: 25%;">الشركة</th>
                                            <th style="width: 20%;">تاريخ المتابعة</th>
                                            <th style="width: 25%;">سبب المتابعة</th>
                                            <th style="width: 30%;">ملاحظة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($suivis as $suivi)
                                            @if($suivi->echantillonEnquete && $suivi->echantillonEnquete->entreprise)
                                                <tr title="عرض تفاصيل العينة: {{ $suivi->echantillonEnquete->entreprise->nom_entreprise }}"
                                                    onclick="window.location='{{ route('echantillons.show', ['echantillon' => $suivi->echantillon_enquete_id]) }}'">
                                                    <td class="company-name">
                                                        <i class="fas fa-building"></i>
                                                        {{ $suivi->echantillonEnquete->entreprise->nom_entreprise }}
                                                    </td>
                                                    <td>
                                                        {{ $suivi->created_at ? \Carbon\Carbon::parse($suivi->created_at)->format('d/m/Y H:i') : 'غير محدد' }}
                                                        @if($suivi->created_at)
                                                            <br><small class="text-muted">({{ \Carbon\Carbon::parse($suivi->created_at)->locale('ar')->diffForHumans() }})</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ $suivi->cause_suivi ?? 'غير متوفر' }}</td>
                                                    <td>{{ Str::limit($suivi->note ?? 'لا توجد ملاحظات', 45) }}</td>
                                                </tr>
                                            @else
                                                {{-- Optionnel: Gérer le cas où un suivi n'a pas d'échantillon ou d'entreprise lié --}}
                                                {{-- Cela ne devrait pas arriver si vos contraintes de BDD et logique sont correctes --}}
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">
                                                        <i>بيانات متابعة غير مكتملة (العينة أو الشركة مفقودة لهذا الإدخال). معرف المتابعة: {{ $suivi->id }}</i>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if ($suivis->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $suivis->appends(request()->query())->links('pagination::bootstrap-4') }}
                                </div>
                            @endif
                        @elseif(!request('search_term'))
                            <div class="empty-state-suivi">
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
    {{-- Le JavaScript reste identique à la version précédente pour les alertes --}}
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