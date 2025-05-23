@extends('layouts.master')
@section('css')
<!-- Owl-carousel css -->
<link href="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
<!-- Maps css -->
<link href="{{URL::asset('assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
<!-- Icones typcn pour un style amélioré -->
<link href="{{URL::asset('assets/plugins/iconfonts/plugin.css')}}" rel="stylesheet" />
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between" style="background-color: #3498db;">
        <div class="left-content">
            <div>
                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1 text-white" dir="rtl">مرحباً، مرحباً بك مجدداً!</h2>
                <p class="mg-b-0 text-white" dir="rtl">قالب لوحة تحكم لمراقبة المبيعات.</p>
            </div>
        </div>
        <div class="main-dashboard-header-right">
            <div>
                <label class="tx-13 text-white" dir="rtl">تقييمات العملاء</label>
                <div class="main-star">
                    <i class="typcn typcn-star active"></i> <i class="typcn typcn-star active"></i> <i class="typcn typcn-star active"></i> <i class="typcn typcn-star active"></i> <i class="typcn typcn-star"></i> <span>(14,873)</span>
                </div>
            </div>
            <div>
                <label class="tx-13 text-white" dir="rtl">المبيعات عبر الإنترنت</label>
                <h5 class="text-white">563,275</h5>
            </div>
            <div>
                <label class="tx-13 text-white" dir="rtl">المبيعات غير المتصلة</label>
                <h5 class="text-white">783,675</h5>
            </div>
        </div>
    </div>
    <!-- /breadcrumb -->
@endsection
@section('content')
    <div class="container-fluid" dir="rtl">
        <div class="row row-sm">
            <div class="col-lg-12">
                <div class="card mg-b-20 shadow-sm" style="border-color: #3498db;">
                    <div class="card-header pb-0 text-right text-white" style="background-color: #3498db;">
                        <h4 class="card-title mg-b-0 tx-22">تفاصيل الشركة <i class="typcn typcn-building ml-2"></i></h4>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success mg-b-0 text-right" role="alert" style="background-color: #2ecc71; border-color: #2ecc71; color: white;">
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- Enterprise Details -->
                        <div class="card mg-b-20 shadow-sm" style="border-color: #1abc9c;">
                            <div class="card-header pb-0 text-right text-white" style="background-color: #1abc9c;">
                                <h5 class="card-title mg-b-0 tx-18">معلومات الشركة <i class="typcn typcn-info-large-outline ml-2"></i></h5>
                            </div>
                            <div class="card-body text-right">
                                <ul class="list-group list-group-flush text-right">
                                    <li class="list-group-item"><strong>الرمز الوطني:</strong> {{ $entreprise->code_national }}</li>
                                    <li class="list-group-item"><strong>الاسم:</strong> {{ $entreprise->nom_entreprise }}</li>
                                    <li class="list-group-item"><strong>النشاط:</strong> {{ $entreprise->libelle_activite }}</li>
                                    <li class="list-group-item"><strong>العنوان:</strong> {{ $entreprise->numero_rue }} {{ $entreprise->nom_rue }}، {{ $entreprise->ville }}، {{ $entreprise->gouvernorat }}</li>
                                    <li class="list-group-item"><strong>الحالة:</strong> {{ $entreprise->statut }}</li>
                                    @if ($entreprise->adresse_cnss)
                                        <li class="list-group-item"><strong>عنوان CNSS:</strong> {{ $entreprise->adresse_cnss }}</li>
                                    @endif
                                    @if ($entreprise->localite_cnss)
                                        <li class="list-group-item"><strong>موقع CNSS:</strong> {{ $entreprise->localite_cnss }}</li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <!-- Phone Numbers -->
                        <div class="card mg-b-20 shadow-sm" style="border-color: #3498db;">
                            <div class="card-header pb-0 text-right text-white" style="background-color: #3498db;">
                                <h5 class="card-title mg-b-0 tx-18">أرقام الهاتف <i class="typcn typcn-phone ml-2"></i></h5>
                            </div>
                            <div class="card-body text-right">
                                @if ($telephones->isEmpty())
                                    <p class="text-muted">لا توجد أرقام هاتف مسجلة.</p>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-striped mg-b-0 text-md-nowrap">
                                            <thead>
                                                <tr>
                                                    <th class="tx-16 fw-bold">رقم الهاتف</th>
                                                    <th class="tx-16 fw-bold">أساسي</th>
                                                    <th class="tx-16 fw-bold">المصدر</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($telephones as $telephone)
                                                    <tr>
                                                        <td>{{ $telephone->numero }}</td>
                                                        <td>{{ $telephone->est_primaire ? 'نعم' : 'لا' }}</td>
                                                        <td>{{ $telephone->source ?? 'غير متوفر' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Contacts -->
                        <div class="card mg-b-20 shadow-sm" style="border-color: #2ecc71;">
                            <div class="card-header pb-0 text-right text-white" style="background-color: #2ecc71;">
                                <h5 class="card-title mg-b-0 tx-18">جهات الاتصال <i class="typcn typcn-user ml-2"></i></h5>
                            </div>
                            <div class="card-body text-right">
                                @if ($contacts->isEmpty())
                                    <p class="text-muted">لا توجد جهات اتصال مسجلة.</p>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-striped mg-b-0 text-md-nowrap">
                                            <thead>
                                                <tr>
                                                    <th class="tx-16 fw-bold">اللقب</th>
                                                    <th class="tx-16 fw-bold">الاسم الأول</th>
                                                    <th class="tx-16 fw-bold">الاسم الأخير</th>
                                                    <th class="tx-16 fw-bold">المنصب</th>
                                                    <th class="tx-16 fw-bold">البريد الإلكتروني</th>
                                                    <th class="tx-16 fw-bold">الهاتف</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($contacts as $contact)
                                                    <tr>
                                                        <td>{{ $contact->civilite ?? 'غير متوفر' }}</td>
                                                        <td>{{ $contact->prenom }}</td>
                                                        <td>{{ $contact->nom }}</td>
                                                        <td>{{ $contact->poste ?? 'غير متوفر' }}</td>
                                                        <td>{{ $contact->email ?? 'غير متوفر' }}</td>
                                                        <td>{{ $contact->telephone ?? 'غير متوفر' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-center">
                            <button id="btnTelephoneModal" class="btn btn-primary mg-r-10 tx-16" style="background-color: #3498db; border-color: #3498db;">إضافة رقم هاتف <i class="typcn typcn-phone ml-1"></i></button>
                            <button id="btnContactModal" class="btn btn-success mg-r-10 tx-16" style="background-color: #2ecc71; border-color: #2ecc71;">إضافة جهة اتصال <i class="typcn typcn-user-add ml-1"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal pour ajouter un numéro de téléphone -->
        <div id="telephoneModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="telephoneModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header text-right text-white" style="background-color: #3498db;">
                        <h5 class="modal-title tx-18" id="telephoneModalLabel">إضافة رقم هاتف</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeTelephoneModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-right">
                        <form action="{{ route('entreprise.telephone.store', $entreprise->id) }}" method="POST" class="parsley-style-1" data-parsley-validate novalidate>
                            @csrf
                            <div class="form-group">
                                <label for="numero" class="tx-bold">رقم الهاتف <span class="tx-danger">*</span></label>
                                <input type="text" name="numero" id="numero" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="source" class="tx-bold">المصدر</label>
                                <input type="text" name="source" id="source" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="ckbox">
                                    <input type="checkbox" name="est_primaire" id="est_primaire" value="1">
                                    <span>رقم أساسي</span>
                                </label>
                            </div>
                            <div class="form-group text-right mg-b-0">
                                <button type="button" class="btn btn-secondary mg-r-5" data-dismiss="modal">إلغاء</button>
                                <button type="submit" class="btn btn-primary" style="background-color: #3498db; border-color: #3498db;">إضافة</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal pour ajouter une personne de contact -->
        <div id="contactModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="contactModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-right text-white" style="background-color: #2ecc71;">
                        <h5 class="modal-title tx-18" id="contactModalLabel">إضافة جهة اتصال</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeContactModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-right">
                        <form action="{{ route('entreprise.contact.store', $entreprise->id) }}" method="POST" class="parsley-style-1" data-parsley-validate novalidate>
                            @csrf
                            <div class="form-group">
                                <label for="civilite" class="tx-bold">اللقب</label>
                                <input type="text" name="civilite" id="civilite" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="prenom" class="tx-bold">الاسم الأول <span class="tx-danger">*</span></label>
                                <input type="text" name="prenom" id="prenom" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="nom" class="tx-bold">الاسم الأخير <span class="tx-danger">*</span></label>
                                <input type="text" name="nom" id="nom" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="email" class="tx-bold">البريد الإلكتروني</label>
                                <input type="email" name="email" id="email" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="telephone" class="tx-bold">الهاتف</label>
                                <input type="text" name="telephone" id="telephone" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="poste" class="tx-bold">المنصب</label>
                                <input type="text" name="poste" id="poste" class="form-control">
                            </div>
                            <div class="form-group text-right mg-b-0">
                                <button type="button" class="btn btn-secondary mg-r-5" data-dismiss="modal">إلغاء</button>
                                <button type="submit" class="btn btn-primary" style="background-color: #2ecc71; border-color: #2ecc71;">إضافة</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
<!-- Internal Chart.bundle js -->
<script src="{{URL::asset('assets/plugins/chart.js/Chart.bundle.min.js')}}"></script>
<!-- Moment js -->
<script src="{{URL::asset('assets/plugins/raphael/raphael.min.js')}}"></script>
<!-- Internal Flot js -->
<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.pie.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.resize.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.categories.js')}}"></script>
<script src="{{URL::asset('assets/js/dashboard.sampledata.js')}}"></script>
<script src="{{URL::asset('assets/js/chart.flot.sampledata.js')}}"></script>
<!-- Internal Apexchart js -->
<script src="{{URL::asset('assets/js/apexcharts.js')}}"></script>
<!-- Internal Map -->
<script src="{{URL::asset('assets/plugins/jqvmap/jquery.vmap.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jqvmap/maps/jquery.vmap.usa.js')}}"></script>
<script src="{{URL::asset('assets/js/modal-popup.js')}}"></script>
<!-- Internal index js -->
<script src="{{URL::asset('assets/js/index.js')}}"></script>
<script src="{{URL::asset('assets/js/jquery.vmap.sampledata.js')}}"></script>
<!-- Script pour gérer les modals -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Boutons pour ouvrir les modals
        document.getElementById('btnTelephoneModal').addEventListener('click', function (e) {
            e.preventDefault();
            $('#telephoneModal').modal('show');
        });

        document.getElementById('btnContactModal').addEventListener('click', function (e) {
            e.preventDefault();
            $('#contactModal').modal('show');
        });
    });
</script>
@endsection
