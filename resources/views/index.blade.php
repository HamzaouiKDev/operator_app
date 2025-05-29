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
                <label class="tx-13 text-white" dir="rtl">عدد الشركات التي أجابت</label>
                <h5 class="text-white">{{ $nombreEntreprisesRepondues ?? '0' }}</h5>
            </div>
            <div>
                <label class="tx-13 text-white" dir="rtl">عدد الشركات المخصصة</label>
                <h5 class="text-white">{{ $nombreEntreprisesAttribuees ?? '0' }}</h5>
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
                    <div class="card-header pb-0 text-center text-white" style="background-color: #3498db;">
                        <h4 class="card-title mg-b-0 tx-28">الشركة العينة</h4>
                    </div>
                    <div class="card-body text-right">
                        @if (session('success'))
                            <div class="alert alert-success mg-b-0 text-right" role="alert" style="background-color: #2ecc71; border-color: #2ecc71; color: white;">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger mg-b-0 text-right" role="alert" style="background-color: #e74c3c; border-color: #e74c3c; color: white;">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if(isset($echantillon) && $echantillon)
                            <h5>الشركة العينة</h5>
                            <ul class="list-group list-group-flush text-right">
                                <li class="list-group-item"><strong>اسم الشركة:</strong> {{ $echantillon->entreprise->nom_entreprise }}</li>
                                <li class="list-group-item"><strong>النشاط:</strong> {{ $echantillon->entreprise->libelle_activite }}</li>
                                <li class="list-group-item"><strong>العنوان:</strong> {{ $echantillon->entreprise->numero_rue }} {{ $echantillon->entreprise->nom_rue }}, {{ $echantillon->entreprise->ville }}, {{ $echantillon->entreprise->gouvernorat }}</li>
                                <li class="list-group-item">
                                    <strong>حالة العينة:</strong> 
                                    <span id="statutDisplay" style="cursor: pointer;" class="badge 
                                        @if($echantillon->statut == 'répondu') badge-success 
                                        @elseif($echantillon->statut == 'réponse partielle') badge-warning 
                                        @elseif($echantillon->statut == 'un rendez-vous') badge-info 
                                        @elseif($echantillon->statut == 'pas de réponse') badge-secondary 
                                        @elseif($echantillon->statut == 'refus') badge-danger 
                                        @elseif($echantillon->statut == 'introuvable') badge-dark 
                                        @endif">
                                        {{ $echantillon->statut == 'répondu' ? 'تم الرد' : 
                                          ($echantillon->statut == 'réponse partielle' ? 'رد جزئي' : 
                                          ($echantillon->statut == 'un rendez-vous' ? 'موعد' : 
                                          ($echantillon->statut == 'pas de réponse' ? 'لا رد' : 
                                          ($echantillon->statut == 'refus' ? 'رفض' : 'غير موجود')))) }}
                                    </span>
                                </li>
                                <li class="list-group-item"><strong>الأولوية:</strong> {{ $echantillon->priorite ?? 'غير محددة' }}</li>
                            </ul>

                            <!-- Boutons pour passer à l'échantillon suivant, ajouter un rendez-vous et lancer un appel -->
                            <div class="mt-3">
                                <form action="{{ route('echantillons.next') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary" style="background-color: #f39c12; border-color: #f39c12;">الانتقال إلى العينة التالية <i class="typcn typcn-arrow-right ml-1"></i></button>
                                </form>
                                <button id="btnRendezVousModal" class="btn btn-info mg-r-10" style="background-color: #1abc9c; border-color: #1abc9c;">إضافة موعد <i class="typcn typcn-calendar ml-1"></i></button>
                                <button id="btnLancerAppel" class="btn btn-success mg-r-10" style="background-color: #2ecc71; border-color: #2ecc71;" data-echantillon-id="{{ $echantillon->id }}" data-utilisateur-id="{{ auth()->id() }}">Lancer un appel <i class="typcn typcn-phone-outgoing ml-1"></i></button>
                                <button id="btnPrendreRdv" class="btn btn-info mg-r-10" style="background-color: #3498db; border-color: #3498db; display: none;">Prendre rendez-vous <i class="typcn typcn-calendar ml-1"></i></button>
                                <button id="btnRelance" class="btn btn-warning mg-r-10" style="background-color: #f1c40f; border-color: #f1c40f; display: none;" data-echantillon-id="{{ $echantillon->id }}" data-utilisateur-id="{{ auth()->id() }}">Relance <i class="typcn typcn-arrow-sync ml-1"></i></button>
                            </div>
                        @else
                            <p class="text-muted">لا توجد شركة متاحة في الوقت الحالي (جميع العينات معينة).</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Ajout de la liste des rendez-vous si elle existe -->
        @if(isset($rendezVous) && $rendezVous->isNotEmpty())
            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card mg-b-20 shadow-sm" style="border: none; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                        <div class="card-header pb-0 text-center text-white" style="background-color: #27ae60; padding: 15px 0; border-radius: 10px 10px 0 0;">
                            <h4 class="card-title mg-b-0 tx-28">مواعيد الشركة: {{ $entreprise->nom_entreprise ?? 'غير محدد' }}</h4>
                        </div>
                        <div class="card-body text-right" style="background-color: #f8f9fa; padding: 20px;">
                            @foreach($rendezVousGrouped as $echantillonId => $rdvs)
                                <h5 class="mt-4" style="color: #34495e; font-weight: 600; margin-bottom: 15px;">عينة رقم: {{ $echantillonId }}</h5>
                                <div class="table-responsive mb-4">
                                    <table class="table table-striped mg-b-0 text-md-nowrap" style="background-color: white; border-radius: 8px; overflow: hidden; border: none;">
                                        <thead style="background-color: #ecf0f1; color: #2c3e50;">
                                            <tr>
                                                <th class="tx-16 fw-bold" style="padding: 12px; border: none;">التاريخ والوقت</th>
                                                <th class="tx-16 fw-bold" style="padding: 12px; border: none;">المكان</th>
                                                <th class="tx-16 fw-bold" style="padding: 12px; border: none;">ملاحظات</th>
                                            </tr>
                                        </thead>
                                        <tbody style="color: #7f8c8d;">
                                            @foreach($rdvs as $rdv)
                                                <tr style="border: none; border-bottom: 1px solid #e0e0e0;">
                                                    <td style="padding: 12px; border: none;">{{ $rdv->heure_debut ?? 'غير محدد' }}</td>
                                                    <td style="padding: 12px; border: none;">{{ 'غير محدد' }}</td>
                                                    <td style="padding: 12px; border: none;">{{ $rdv->notes ?? 'غير متوفرة' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach

                            <!-- Ajout des liens de pagination avec style flat -->
                            <div class="pagination justify-content-center mt-4">
                                {{ $rendezVous->links('pagination::bootstrap-4') }}
                                <style>
                                    .pagination .page-item .page-link {
                                        background-color: #ecf0f1;
                                        color: #2c3e50;
                                        border: none;
                                        border-radius: 5px;
                                        margin: 0 3px;
                                        padding: 8px 12px;
                                        transition: background-color 0.3s ease;
                                    }
                                    .pagination .page-item.active .page-link {
                                        background-color: #27ae60;
                                        color: white;
                                    }
                                    .pagination .page-item .page-link:hover {
                                        background-color: #bdc3c7;
                                        color: #2c3e50;
                                    }
                                </style>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(isset($rendezVous) && $rendezVous->isEmpty())
            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card mg-b-20 shadow-sm" style="border: none; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
                        <div class="card-header pb-0 text-center text-white" style="background-color: #27ae60; padding: 15px 0; border-radius: 10px 10px 0 0;">
                            <h4 class="card-title mg-b-0 tx-28">مواعيد الشركة: {{ $entreprise->nom_entreprise ?? 'غير محدد' }}</h4>
                        </div>
                        <div class="card-body text-right" style="background-color: #f8f9fa; padding: 20px;">
                            <p class="text-muted" style="color: #95a5a6; font-size: 16px; margin-top: 20px;">لا توجد مواعيد مسجلة لهذه الشركة في الوقت الحالي.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Carte pour les numéros de téléphone -->
        @if(isset($echantillon) && $echantillon)
            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card mg-b-20 shadow-sm" style="border-color: #3498db;">
                        <div class="card-header pb-0 text-center text-white" style="background-color: #3498db;">
                            <h4 class="card-title mg-b-0 tx-28">أرقام الهاتف</h4>
                        </div>
                        <div class="card-body text-right">
                            @if($echantillon->entreprise->telephones->isEmpty())
                                <p class="text-muted">لا توجد أرقام هاتف مسجلة.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped mg-b-0 text-md-nowrap">
                                        <thead>
                                            <tr>
                                                <th class="tx-16 fw-bold">الرقم</th>
                                                <th class="tx-16 fw-bold">المصدر</th>
                                                <th class="tx-16 fw-bold">أساسي</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($echantillon->entreprise->telephones as $telephone)
                                                <tr>
                                                    <td>{{ $telephone->numero }}</td>
                                                    <td>{{ $telephone->source ?? 'غير محدد' }}</td>
                                                    <td>{{ $telephone->est_primaire ? 'نعم' : 'لا' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            <button id="btnTelephoneModal" class="btn btn-primary mg-r-10 tx-16 mt-2" style="background-color: #3498db; border-color: #3498db;">إضافة رقم هاتف <i class="typcn typcn-phone ml-1"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte pour les contacts -->
            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card mg-b-20 shadow-sm" style="border-color: #2ecc71;">
                        <div class="card-header pb-0 text-center text-white" style="background-color: #2ecc71;">
                            <h4 class="card-title mg-b-0 tx-28">جهات الاتصال</h4>
                        </div>
                        <div class="card-body text-right">
                            @if($echantillon->entreprise->contacts->isEmpty())
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
                                            @foreach($echantillon->entreprise->contacts as $contact)
                                                <tr>
                                                    <td>{{ $contact->civilite ?? 'غير محدد' }}</td>
                                                    <td>{{ $contact->prenom }}</td>
                                                    <td>{{ $contact->nom }}</td>
                                                    <td>{{ $contact->poste ?? 'غير محدد' }}</td>
                                                    <td>{{ $contact->email ?? 'غير محدد' }}</td>
                                                    <td>{{ $contact->telephone ?? 'غير محدد' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            <button id="btnContactModal" class="btn btn-success mg-r-10 tx-16 mt-2" style="background-color: #2ecc71; border-color: #2ecc71;">إضافة جهة اتصال <i class="typcn typcn-user-add ml-1"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Modal pour ajouter un rendez-vous -->
        @if(isset($echantillon) && $echantillon)
            <div class="modal fade" id="rendezVousModal" tabindex="-1" role="dialog" aria-labelledby="rendezVousModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #1abc9c; color: white;">
                            <h5 class="modal-title" id="rendezVousModalLabel">Ajouter un nouveau rendez-vous</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-right">
                            <form action="{{ route('rendezvous.store', $echantillon->id) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="dateRdv">Date et heure du rendez-vous <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="dateRdv" name="date_rdv" required>
                                </div>
                                <div class="form-group">
                                    <label for="lieuRdv">Lieu du rendez-vous <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="lieuRdv" name="lieu_rdv" placeholder="Entrez le lieu du rendez-vous" required>
                                </div>
                                <div class="form-group">
                                    <label for="contactId">Contact (optionnel)</label>
                                    @if($echantillon->entreprise->contacts->isNotEmpty())
                                        <select class="form-control" id="contactId" name="contact_id">
                                            <option value="">Sans contact spécifique</option>
                                            @foreach($echantillon->entreprise->contacts as $contact)
                                                <option value="{{ $contact->id }}">{{ $contact->prenom }} {{ $contact->nom }} {{ $contact->poste ? '(' . $contact->poste . ')' : '' }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" class="form-control" id="contactNom" name="contact_nom" placeholder="Entrez le nom du contact (optionnel)">
                                        <small class="form-text text-muted">Aucun contact enregistré, vous pouvez entrer un nom manuellement si nécessaire.</small>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="notes">Notes supplémentaires (optionnel)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Entrez des notes sur le rendez-vous"></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                                    <button type="submit" class="btn btn-info" style="background-color: #1abc9c; border-color: #1abc9c;">Enregistrer le rendez-vous</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Modal pour ajouter un numéro de téléphone -->
        @if(isset($echantillon) && $echantillon)
            <div class="modal fade" id="telephoneModal" tabindex="-1" role="dialog" aria-labelledby="telephoneModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #3498db; color: white;">
                            <h5 class="modal-title" id="telephoneModalLabel">إضافة رقم هاتف جديد</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-right">
                            <form action="{{ route('telephones.store', $echantillon->entreprise->id) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="numero">رقم الهاتف <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="numero" name="numero" placeholder="أدخل رقم الهاتف" required>
                                </div>
                                <div class="form-group">
                                    <label for="source">المصدر (اختياري)</label>
                                    <input type="text" class="form-control" id="source" name="source" placeholder="أدخل مصدر الرقم (مثل: إنترنت، مكتب)">
                                </div>
                                <div class="form-group">
                                    <label for="estPrimaire">رقم أساسي</label>
                                    <input type="checkbox" id="estPrimaire" name="est_primaire" value="1">
                                    <small class="form-text text-muted">حدد إذا كان هذا الرقم هو الرقم الأساسي للشركة.</small>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                                    <button type="submit" class="btn btn-primary" style="background-color: #3498db; border-color: #3498db;">حفظ رقم الهاتف</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Modal pour ajouter un contact -->
        @if(isset($echantillon) && $echantillon)
            <div class="modal fade" id="contactModal" tabindex="-1" role="dialog" aria-labelledby="contactModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #2ecc71; color: white;">
                            <h5 class="modal-title" id="contactModalLabel">إضافة جهة اتصال جديدة</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-right">
                            <form action="{{ route('contacts.store', $echantillon->entreprise->id) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="civilite">اللقب (اختياري)</label>
                                    <input type="text" class="form-control" id="civilite" name="civilite" placeholder="أدخل اللقب (مثل: السيد، السيدة)">
                                </div>
                                <div class="form-group">
                                    <label for="prenom">الاسم الأول <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="prenom" name="prenom" placeholder="أدخل الاسم الأول" required>
                                </div>
                                <div class="form-group">
                                    <label for="nom">الاسم الأخير <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nom" name="nom" placeholder="أدخل الاسم الأخير" required>
                                </div>
                                <div class="form-group">
                                    <label for="poste">المنصب (اختياري)</label>
                                    <input type="text" class="form-control" id="poste" name="poste" placeholder="أدخل المنصب (مثل: مدير، موظف)">
                                </div>
                                <div class="form-group">
                                    <label for="email">البريد الإلكتروني (اختياري)</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="أدخل البريد الإلكتروني">
                                </div>
                                <div class="form-group">
                                    <label for="telephone">رقم الهاتف (اختياري)</label>
                                    <input type="text" class="form-control" id="telephone" name="telephone" placeholder="أدخل رقم الهاتف">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                                    <button type="submit" class="btn btn-success" style="background-color: #2ecc71; border-color: #2ecc71;">حفظ جهة الاتصال</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Modal pour afficher le script lors du lancement d'un appel -->
        @if(isset($echantillon) && $echantillon)
            <div class="modal fade" id="appelScriptModal" tabindex="-1" role="dialog" aria-labelledby="appelScriptModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #2ecc71; color: white;">
                            <h5 class="modal-title" id="appelScriptModalLabel">Script d'Appel</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-right">
                            <div class="mb-3">
                                <button id="switchToArabic" class="btn btn-primary" style="background-color: #3498db; border-color: #3498db;">عربي</button>
                                <button id="switchToFrench" class="btn btn-secondary">Français</button>
                            </div>
                            <p>Voici le script à suivre pour l'appel :</p>
                            <div style="background-color: white; padding: 20px; border-radius: 5px; border: 1px solid #ddd; height: 400px; overflow-y: auto;">
                                <!-- Conteneur pour le script en arabe (visible par défaut) -->
                                <div id="scriptArabe" style="display: block;">
                                    <pre style="white-space: pre-wrap; direction: rtl; font-family: inherit; font-size: 16px; line-height: 1.6;">
<span style="color: #0066cc; font-weight: bold;">1. التقديم الأولي</span>
مرحبًا، معاك [الاسم الكامل]، نخدم في المعهد الوطني للإحصاء.
نتصل بيك في إطار البحث الوطني حول التشغيل والأجور، واللي يهدف باش نجمعو معلومات محينة على عدد العاملين، أنواع الخطط، والأجور المعتمدة في المؤسسات.

<span style="color: #0066cc; font-weight: bold;">2. التحقق من المؤسسة</span>
باش نبدأ، نحب نتأكد اللي أنا نحكي مع مؤسسة [اسم المؤسسة الوارد في العينة]؟
وإذا ماكنتش المؤسسة هاذي، تنجم تعطيني من فضلكم الاسم القانوني الكامل للمؤسسة؟
(إذا ما كانتش هي: نقفل المكالمة بطريقة لائقة. إذا نعم، نكملو.)

<span style="color: #0066cc; font-weight: bold;">3. طلب عنوان البريد الإلكتروني الخاص بالمؤسسة</span>
يعطيك الصحة. باش نجم نبعتلكم إيميل تعريفي رسمي، تنجم تعطيني البريد الإلكتروني المهني متاع المؤسسة، من فضلك؟

<span style="color: #0066cc; font-weight: bold;">4. البحث على الشخص المناسب</span>
نحب نحكي مع المسؤول على الموارد البشرية، ولا أي شخص عندو فكرة على عدد العاملين والأجور في المؤسسة.
تنجم تقولي شكون نجم نحكي معاه؟ ولا تحوّلني ليه، إذا ممكن؟

<span style="color: #0066cc; font-weight: bold;">5. تقديم جديد (إذا وصلنا للشخص المناسب)</span>
(إذا تم التحويل للشخص المناسب، نعاودو التقديم.)
مرحبًا، معاك [الاسم الكامل]، نخدم كـ مشغّل/مشغّلة هاتفية في المعهد الوطني للإحصاء.
نتصل بيك في إطار البحث حول التشغيل والأجور، ومؤسستكم تـمّ اختيارها باش تشارك في البحث هذا.
البحث إجباري، والنتائج متاعو تُستعمل فقط لأغراض إحصائية ووضع السياسات العامة.
وكل المعطيات اللي باش تمدّونا بيها، باش نتعاملو معاها بكل سرية.

<span style="color: #0066cc; font-weight: bold;">6. جمع المعطيات الشخصية</span>
باش نجم نبعثلكم تفاصيل الاستبيان، نحب نطلب منكم المعطيات التالية:
•   الاسم واللقب
•   الخطة/الوظيفة
•   رقم الهاتف المباشر
•   البريد الإلكتروني المهني

<span style="color: #0066cc; font-weight: bold;">7. إرسال الإيميل مع رابط الاستبيان</span>
يعطيك الصحة. توّا باش نبعتلكم إيميل فيه الرابط متاع الاستبيان الإلكتروني، مع كل التوضيحات اللازمة على كل سؤال.

<span style="color: #0066cc; font-weight: bold;">8. اقتراح تعبئة الاستبيان مباشرة أو تحديد موعد</span>
تحب نعمروا الاستبيان مع بعضنا توا عبر الهاتف؟ ياخو تقريبًا بين 15 و20 دقيقة.
وإلا، إذا الوقت ما يسمحش، نجموا نحددو موعد آخر يناسبكم، باش تطلعوا على الاستبيان وتحضّرو الإجابات من قبل.

<span style="color: #0066cc; font-weight: bold;">9. الخاتمة</span>
إذا تم تحديد موعد:
بهـي، باش نرجع نتصل بيكم نهار [اليوم] على [الساعة].
يعطيكم الصحة على تعاونكم وتفهمكم.
إذا تم إجراء المقابلة مباشرة:
يعطيك الصحة، نجموا نبدؤوا توا.
في حال الرفض أو وضع آخر:
شكرًا على وقتكم. وإذا تحتاجونا في أي وقت، ما تترددوش تتصلوا بينا. نهاركم زين!
                                    </pre>
                                </div>

                                <!-- Conteneur pour le script en français (caché par défaut, direction LTR) -->
                                <div id="scriptFrancais" style="display: none;">
                                    <pre style="white-space: pre-wrap; direction: ltr; font-family: inherit; font-size: 16px; line-height: 1.6;">
<span style="color: #0066cc; font-weight: bold;">1. Présentation initiale</span>
Bonjour, je suis [Nom complet], je travaille à l'Institut National de la Statistique.
Je vous contacte dans le cadre de l'enquête nationale sur l'emploi et les salaires, qui vise à collecter des informations actualisées sur le nombre d'employés, les types de postes et les salaires pratiqués dans les entreprises.

<span style="color: #0066cc; font-weight: bold;">2. Vérification de l'entreprise</span>
Pour commencer, puis-je m'assurer que je suis bien en contact avec l'entreprise [Nom de l'entreprise mentionné dans l'échantillon] ?
Si ce n'est pas le cas, pourriez-vous me donner le nom légal complet de l'entreprise, s'il vous plaît ?
(Si ce n'est pas l'entreprise recherchée : terminer l'appel poliment. Si c'est correct, continuer.)

<span style="color: #0066cc; font-weight: bold;">3. Demande de l'adresse e-mail de l'entreprise</span>
Merci beaucoup. Afin de pouvoir vous envoyer un e-mail de présentation officiel, pourriez-vous me fournir l'adresse e-mail professionnelle de l'entreprise, s'il vous plaît ?

<span style="color: #0066cc; font-weight: bold;">4. Recherche de la personne appropriée</span>
J'ai besoin de parler à la personne responsable ou au service des ressources humaines, ou à toute personne pouvant fournir des informations sur le nombre d'employés et les salaires dans l'entreprise.
Pourriez-vous me dire à qui je peux m'adresser ou me transférer à cette personne, s'il vous plaît ?

<span style="color: #0066cc; font-weight: bold;">5. Nouvelle présentation (à l'interlocuteur approprié)</span>
(Si l'appel est transféré à la personne appropriée, se présenter à nouveau.)
Bonjour, je suis [Nom complet], opérateur/opératrice téléphonique à l'Institut National de la Statistique.
Je vous contacte dans le cadre de l'enquête sur l'emploi et les salaires, pour laquelle votre entreprise a été sélectionnée.
Cette enquête est obligatoire et ses résultats sont utilisés uniquement à des fins statistiques et pour l'élaboration de politiques publiques.
Toutes les informations que vous fournirez seront traitées avec la plus grande confidentialité.

<span style="color: #0066cc; font-weight: bold;">6. Collecte des informations personnelles</span>
Afin de pouvoir vous envoyer les détails de l'enquête, pourriez-vous me fournir les informations suivantes :
- Nom et prénom
- Fonction
- Numéro de téléphone direct
- Adresse e-mail professionnelle

<span style="color: #0066cc; font-weight: bold;">7. Envoi de l'e-mail avec le lien du questionnaire</span>
Merci. Je vais immédiatement vous envoyer un e-mail contenant un lien vers le questionnaire en ligne, accompagné de toutes les explications nécessaires pour chaque question.

<span style="color: #0066cc; font-weight: bold;">8. Proposition de remplir le questionnaire immédiatement ou de fixer un rendez-vous</span>
Souhaitez-vous que nous le remplissions ensemble maintenant par téléphone ? Cela prend environ 15 à 20 minutes.
Sinon, si ce n'est pas possible maintenant, je peux proposer de fixer un rendez-vous ultérieur qui vous convient, ce qui vous permettra de consulter le questionnaire et de préparer vos réponses à l'avance.

<span style="color: #0066cc; font-weight: bold;">9. Conclusion</span>
Si un rendez-vous est fixé :
Parfait, je vous rappellerai le [jour] à [heure]. Merci pour votre coopération et votre compréhension.
Si l'entretien est réalisé immédiatement :
Merci, nous pouvons commencer maintenant.
En cas de refus ou toute autre situation :
Merci pour votre temps. N'hésitez pas à nous contacter en cas de besoin. Bonne journée !
                                    </pre>
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                <label for="notesAppel">Notes sur l'appel (optionnel)</label>
                                <textarea class="form-control" id="notesAppel" name="notesAppel" rows="3" placeholder="أدخل ملاحظات حول المكالمة"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log('Page chargée, initialisation des événements...');
            // Faire disparaître les messages de succès ou d'erreur après 5 secondes
            const alertSuccess = document.querySelector('.alert-success');
            const alertDanger = document.querySelector('.alert-danger');
            if (alertSuccess || alertDanger) {
                setTimeout(function () {
                    if (alertSuccess) alertSuccess.style.display = 'none';
                    if (alertDanger) alertDanger.style.display = 'none';
                }, 5000);
            }

            // Gestion des modals pour Ajouter téléphone
            const btnTelephoneModal = document.getElementById('btnTelephoneModal');
            if (btnTelephoneModal) {
                btnTelephoneModal.addEventListener('click', function (e) {
                    e.preventDefault();
                    console.log('Clic sur Ajouter un numéro de téléphone');
                    $('#telephoneModal').modal('show');
                });
            }

            // Gestion des modals pour Ajouter contact
            const btnContactModal = document.getElementById('btnContactModal');
            if (btnContactModal) {
                btnContactModal.addEventListener('click', function (e) {
                    e.preventDefault();
                    console.log('Clic sur Ajouter un contact');
                    $('#contactModal').modal('show');
                });
            }

            // Gestion du clic sur le statut pour afficher le modal
            const statutDisplay = document.getElementById('statutDisplay');
            if (statutDisplay) {
                statutDisplay.addEventListener('click', function (e) {
                    e.preventDefault();
                    console.log('Clic sur le statut pour ouvrir le modal');
                    $('#statutModal').modal('show');
                });
            }

            // Gestion du clic sur le bouton d'ajout de rendez-vous
            const btnRendezVousModal = document.getElementById('btnRendezVousModal');
            if (btnRendezVousModal) {
                btnRendezVousModal.addEventListener('click', function (e) {
                    e.preventDefault();
                    console.log('Clic sur Ajouter un rendez-vous');
                    $('#rendezVousModal').modal('show');
                });
            }

            // Gestion du bouton Lancer un appel / Fin d'appel
            const btnLancerAppel = document.getElementById('btnLancerAppel');
            if (btnLancerAppel) {
                const btnPrendreRdv = document.getElementById('btnPrendreRdv');
                const btnRelance = document.getElementById('btnRelance');
                let isCalling = false;
                let currentAppelId = null;

                btnLancerAppel.addEventListener('click', async function (e) {
                    e.preventDefault();
                    const echantillonId = btnLancerAppel.getAttribute('data-echantillon-id');
                    const utilisateurId = btnLancerAppel.getAttribute('data-utilisateur-id');

                    if (!echantillonId || !utilisateurId) {
                        alert('Erreur : Données manquantes pour lancer l\'appel.');
                        return;
                    }

                    if (!isCalling) {
                        // Création d'un nouvel appel via AJAX
                        try {
                            const response = await fetch('{{ route("appels.store") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ 
                                    echantillon_enquete_id: echantillonId,
                                    utilisateur_id: utilisateurId
                                })
                            });

                            const data = await response.json();
                            if (response.ok) {
                                currentAppelId = data.appel_id;
                                // Passage à l'état "Fin d'appel"
                                btnLancerAppel.innerHTML = "Fin d'appel <i class='typcn typcn-phone ml-1'></i>";
                                btnLancerAppel.style.backgroundColor = '#e74c3c';
                                btnLancerAppel.style.borderColor = '#e74c3c';
                                if (btnPrendreRdv) btnPrendreRdv.style.display = 'inline-block';
                                if (btnRelance) btnRelance.style.display = 'inline-block';
                                isCalling = true;
                                console.log('Appel lancé, ID:', currentAppelId);
                                // Afficher le modal avec le script
                                $('#appelScriptModal').modal('show');
                            } else {
                                alert('Erreur lors de la création de l\'appel : ' + data.error);
                            }
                        } catch (error) {
                            console.error('Erreur AJAX:', error);
                            alert('Une erreur s\'est produite lors du lancement de l\'appel.');
                        }
                    } else {
                        // Mise à jour de l'appel pour marquer la fin via AJAX
                        try {
                            const response = await fetch('{{ route("appels.end") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ appel_id: currentAppelId })
                            });

                            const data = await response.json();
                            if (response.ok) {
                                // Retour à l'état "Lancer un appel"
                                btnLancerAppel.innerHTML = "Lancer un appel <i class='typcn typcn-phone-outgoing ml-1'></i>";
                                btnLancerAppel.style.backgroundColor = '#2ecc71';
                                btnLancerAppel.style.borderColor = '#2ecc71';
                                if (btnPrendreRdv) btnPrendreRdv.style.display = 'none';
                                if (btnRelance) btnRelance.style.display = 'none';
                                isCalling = false;
                                currentAppelId = null;
                                console.log('Appel terminé');
                                // Fermer le modal du script
                                $('#appelScriptModal').modal('hide');
                            } else {
                                alert('Erreur lors de la fin de l\'appel : ' + data.error);
                            }
                        } catch (error) {
                            console.error('Erreur AJAX:', error);
                            alert('Une erreur s\'est produite lors de la fin de l\'appel.');
                        }
                    }
                });

                // Gestion du clic sur Prendre rendez-vous (optionnel, ouverture du modal)
                if (btnPrendreRdv) {
                    btnPrendreRdv.addEventListener('click', function (e) {
                        e.preventDefault();
                        console.log('Clic sur Prendre rendez-vous');
                        $('#rendezVousModal').modal('show');
                    });
                }

                // Gestion du clic sur Relance (peut être personnalisé selon vos besoins)
                if (btnRelance) {
                    btnRelance.addEventListener('click', async function (e) {
                        e.preventDefault();
                        const echantillonId = btnRelance.getAttribute('data-echantillon-id');
                        const utilisateurId = btnRelance.getAttribute('data-utilisateur-id');
                        console.log('Clic sur Relance pour l\'échantillon ID:', echantillonId);

                        if (!echantillonId || !utilisateurId) {
                            alert('Erreur : Données manquantes pour la relance.');
                            return;
                        }

                        try {
                            const response = await fetch('{{ route("relance.store") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ 
                                    echantillon_enquete_id: echantillonId,
                                    utilisateur_id: utilisateurId
                                })
                            });

                            const data = await response.json();
                            if (response.ok) {
                                alert('Relance enregistrée avec succès !');
                            } else {
                                alert('Erreur lors de l\'enregistrement de la relance : ' + data.error);
                            }
                        } catch (error) {
                            console.error('Erreur AJAX:', error);
                            alert('Une erreur s\'est produite lors de l\'enregistrement de la relance.');
                        }
                    });
                }
            }

            // Gestion du basculement entre arabe et français pour le script
            const switchToArabic = document.getElementById('switchToArabic');
            const switchToFrench = document.getElementById('switchToFrench');
            if (switchToArabic && switchToFrench) {
                switchToArabic.addEventListener('click', function (e) {
                    e.preventDefault();
                    const scriptArabe = document.getElementById('scriptArabe');
                    const scriptFrancais = document.getElementById('scriptFrancais');
                    if (scriptArabe && scriptFrancais) {
                        scriptArabe.style.display = 'block';
                        scriptFrancais.style.display = 'none';
                        this.classList.add('btn-primary');
                        this.classList.remove('btn-secondary');
                        switchToFrench.classList.add('btn-secondary');
                        switchToFrench.classList.remove('btn-primary');
                    }
                });

                switchToFrench.addEventListener('click', function (e) {
                    e.preventDefault();
                    const scriptArabe = document.getElementById('scriptArabe');
                    const scriptFrancais = document.getElementById('scriptFrancais');
                    if (scriptArabe && scriptFrancais) {
                        scriptArabe.style.display = 'none';
                        scriptFrancais.style.display = 'block';
                        this.classList.add('btn-primary');
                        this.classList.remove('btn-secondary');
                        switchToArabic.classList.add('btn-secondary');
                        switchToArabic.classList.remove('btn-primary');
                    }
                });
            }
        });
    </script>
@endsection
