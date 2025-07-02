{{-- resources/views/echantillons/en_attente.blade.php --}}

@extends('layouts.master')

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Styles généraux (similaires à indexSuivi) */
        body { font-family: 'Cairo', sans-serif; }
        .breadcrumb-header { background-color: #ffffff !important; border-bottom: 1px solid #dee2e6; }
        
        /* Styles pour la carte "en attente" */
        .card-echantillon {
            border: 1px solid #e3e6f0;
            border-radius: 0.75rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.07) !important;
            margin-bottom: 30px;
        }
        .card-header-custom {
            background-color: #ffc107; /* Thème Jaune/Warning pour "en attente" */
            color: #212529;
            padding: 1.25rem 1.5rem;
        }
        .card-header-custom .card-title { font-size: 1.5rem; font-weight: 600; }

        /* Styles pour le tableau */
        .table-echantillon thead th {
            color: #b38600; /* Jaune foncé */
            font-weight: 700;
            text-transform: uppercase;
            border-bottom: 3px solid #ffc107; /* Jaune */
            text-align: right;
            white-space: nowrap;
        }
        .table-echantillon tbody tr:hover { background-color: #fff8e1; cursor: pointer; } /* Survol jaune clair */
        .table-echantillon .company-name { font-weight: 600; color: #b38600; }
        
        .pagination .page-item.active .page-link { background-color: #ffc107; border-color: #ffc107; }
        .pagination .page-link { color: #ffc107; }
    </style>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <div>
                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1" dir="rtl">قائمة العينات في الانتظار</h2>
                <p class="mg-b-0" dir="rtl">عرض جميع العينات التي تنتظر المعالجة.</p>
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
        <div class="row row-sm">
            <div class="col-lg-12">
                <div class="card card-echantillon">
                    <div class="card-header card-header-custom text-center">
                        <h4 class="card-title mg-b-0"><i class="fas fa-hourglass-half"></i> عينات في الانتظار</h4>
                    </div>
                    <div class="card-body text-right">
                        <form method="GET" action="{{ route('echantillons.en_attente') }}" class="mb-4">
                            <div class="input-group">
                                <input type="text" name="search_term" class="form-control" placeholder="البحث عن طريق الشركة أو الاستطلاع..." value="{{ request('search_term') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-warning" type="submit"><i class="fas fa-search"></i> بحث</button>
                                </div>
                            </div>
                        </form>

                        @if($echantillons->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-hover mg-b-0 text-md-nowrap table-echantillon">
                                    <thead>
                                        <tr>
                                            <th style="width: 30%;">الشركة</th>
                                           
                                            <th style="width: 20%;">الحالة</th>
                                            <th style="width: 20%;">آخر تعديل</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($echantillons as $echantillon)
                                            @if($echantillon->entreprise)
                                                {{-- 
                                                    CORRECTION ICI : On utilise la route 'echantillons.show' comme dans votre page de suivis.
                                                    Ceci va appeler la méthode show(EchantillonEnquete $echantillon) de votre EchantillonController.
                                                --}}
                                                <tr onclick="window.location='{{ route('echantillons.show', ['echantillon' => $echantillon->id]) }}'" title="عرض تفاصيل العينة: {{ $echantillon->entreprise->nom_entreprise }}">
                                                    <td class="company-name">
                                                        <i class="fas fa-building"></i>
                                                        {{ $echantillon->entreprise->nom_entreprise }}
                                                    </td>
                                                    
                                                    <td><span class="badge badge-warning">{{ $echantillon->statut }}</span></td>
                                                    <td>
                                                        {{ $echantillon->updated_at->format('d/m/Y') }}<br>
                                                        <small class="text-muted">({{ $echantillon->updated_at->locale('ar')->diffForHumans() }})</small>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if ($echantillons->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $echantillons->appends(request()->query())->links('pagination::bootstrap-4') }}
                                </div>
                            @endif
                        @else
                            <div class="text-center p-5">
                                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                <p>لا توجد عينات في الانتظار حاليًا.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection