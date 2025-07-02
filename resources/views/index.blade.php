@extends('layouts.master')

@section('css')
    <link href="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/iconfonts/plugin.css')}}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        /* Styles existants */
        #appelActions { transition: all 0.3s ease-in-out; }
        #appelActions .btn { transition: all 0.2s ease; }
        #appelActions .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        #btnLancerAppel.btn-danger { animation: pulse 1.5s infinite; }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(231, 76, 60, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(231, 76, 60, 0); }
            100% { box-shadow: 0 0 0 0 rgba(231, 76, 60, 0); }
        }
        .list-group-item-action { cursor: pointer; }
        .list-group-item-action:hover, .list-group-item-action.active:hover { background-color: #f0f0f0; }
        .list-group-item-action.active { 
            color: #fff !important; 
            background-color: #007bff !important; 
            border-color: #007bff !important; 
            font-weight: bold;
        }
        .numero-badge-etat { 
            margin-left: 8px; 
            font-size: 0.8em;
            padding: 0.3em 0.6em;
            vertical-align: middle;
        }

        /* Nouveaux styles pour la modale Ajouter Suivi */
        .modal-header-custom-suivi {
            background-color: #3498db;
            color: white;
            border-bottom: 2px solid #2980b9;
        }
        .btn-submit-custom-suivi {
            background-color: #3498db;
            border-color: #2980b9;
            color: white;
            transition: all 0.2s ease;
        }
        .btn-submit-custom-suivi:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
         /* NOUVEAUX STYLES pour agrandir les informations de l'entreprise */
        .company-details-card .list-group-item {
            font-size: 1.1rem; /* Augmente la taille de la police. Essayez 1.05rem, 1.1rem, 16px, ou 17px selon votre préférence. */
            /* Optionnel: vous pouvez aussi augmenter légèrement le padding vertical si vous trouvez que c'est trop serré */
             padding-top: 0.85rem; */
             padding-bottom: 0.85rem; */
        }

        /* Optionnel: Rendre les étiquettes (en gras) un peu plus foncées pour un meilleur contraste avec la taille augmentée */
        .company-details-card .list-group-item strong {
            color: #343a40; /* Un gris foncé. Ajustez si nécessaire. */
        }
        /* ================================================================== */
    /* == NOUVEAUX STYLES POUR UN AFFICHAGE PROFESSIONNEL DES DÉTAILS == */
    /* ================================================================== */

    /* Style général pour les cartes de détails */
    .details-card {
        border-left: 4px solid #3498db;
        border-radius: 8px;
        overflow: hidden; /* Assure que les coins arrondis s'appliquent partout */
    }
    .details-card .card-header {
        border-bottom: 2px solid rgba(255, 255, 255, 0.2);
    }

    /* Styles pour la liste des détails principaux */
    .details-list .list-group-item {
    display: flex;
    /* La ligne justify-content: space-between; a été supprimée */
    align-items: center; /* On garde l'alignement vertical */
    padding: 0.9rem 1.25rem;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.2s ease-in-out;
}
    .details-list .list-group-item:last-child {
        border-bottom: none;
    }
    .details-list .list-group-item:hover {
        background-color: #f8f9fa; /* Un gris très léger au survol */
    }

    /* Style pour l'icône et le label (ex: "Nom de l'entreprise") */
    .item-label {
        display: flex;
        align-items: center;
        color: #555; /* Couleur du texte du label */
       min-width: 220px;
       gap: 15px;
    }
    .item-label .item-icon {
        font-size: 1.1rem;
        margin-right: 15px; /* Espace entre l'icône et le texte */
        color: #007bff; /* Couleur de l'icône */
        width: 20px; /* Largeur fixe pour un alignement parfait */
        text-align: center;
       
    }
    .item-label strong {
        font-weight: 800; /* Un peu plus gras */
    }

    /* Style pour la valeur (ex: "Nom de l'Entreprise ABC") */
    .item-value {
        font-size: 1rem;
        font-weight: 500;
        color: #333;
        text-align: left; /* Assure que le texte est aligné à gauche de son conteneur */
    }
    .item-value .badge {
        font-size: 0.9rem; /* Badge un peu plus grand */
        padding: 0.4em 0.8em;
    }

    /* Styles spécifiques pour les cartes Téléphone, Email, Contacts */
    .sub-details-list .list-group-item {
        flex-wrap: wrap; /* Permet aux éléments de passer à la ligne sur mobile */
        padding: 0.8rem 1rem;
    }
    .sub-details-content {
        flex-grow: 1;
        display: flex;
        align-items: center;
    }
    .sub-details-badges {
        margin-left: auto; /* Pousse les badges à droite */
        padding-left: 10px; /* Espace avec le contenu */
    }
    .sub-details-badges .badge {
        margin: 0 2px;
    }
    .sub-details-list .item-icon {
        color: #555;
    }
    .sub-details-list a {
        text-decoration: none;
        color: #007bff;
        font-weight: 600;
    }
    .sub-details-list a:hover {
        text-decoration: underline;
    }
    
    /* Couleurs des cartes secondaires */
    .details-card-phone { border-left-color: #3498db; }
    .details-card-email { border-left-color: #e74c3c; }
    .details-card-contact { border-left-color: #2ecc71; }


    </style>
@endsection

@section('page-header')
    {{-- Votre breadcrumb-header --}}
    <div class="breadcrumb-header justify-content-between" style="background-color: #3498db; padding: 15px 25px; align-items: center;">
        <div class="left-content">
            <div>
                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1 text-white" dir="rtl">مرحباً بك {{ Auth::user()->name }} ! </h2>
                <p class="mg-b-0 text-white" dir="rtl">لوحة تحكم لمتابعة سير العمل.</p>
            </div>
        </div>
        <div class="main-dashboard-header-right">
            <div><label class="tx-13 text-white" dir="rtl">عدد الشركات التي أجابت</label><h5 class="text-white">{{ $nombreEntreprisesRepondues ?? '0' }}</h5></div>
            <div><label class="tx-13 text-white" dir="rtl">عدد الشركات المخصصة لك</label><h5 class="text-white">{{ $nombreEntreprisesAttribuees ?? '0' }}</h5></div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid" dir="rtl">
        {{-- Affichage des messages de session et erreurs --}}
        @if (session('success')) <div class="alert alert-success mg-b-20 text-right auto-hide" role="alert" style="background-color: #2ecc71; border-color: #2ecc71; color: white;">✅ {{ session('success') }}</div> @endif
        @if (session('error')) <div class="alert alert-danger mg-b-20 text-right auto-hide" role="alert" style="background-color: #e74c3c; border-color: #e74c3c; color: white;">❌ {{ session('error') }}</div> @endif
        @if (isset($error) && $error && !session('error')) <div class="alert alert-warning mg-b-20 text-right auto-hide" role="alert" style="background-color: #f39c12; border-color: #f39c12; color: white;">⚠️ {{ $error }}</div> @endif

        <div class="row row-sm">
            <div class="col-lg-12">
                <div class="card mg-b-20 shadow-sm" style="border-color: #3498db;">
                    <div class="card-header pb-0 text-center text-white" style="background-color: #3498db;">
                        <h4 class="card-title mg-b-0 tx-28">الشركة العينة</h4>
                        <small class="badge badge-light mt-2" id="echantillonInfo">
                            @if(isset($echantillon) && $echantillon) الشركة الحالية: #{{ $echantillon->id }} @else لا توجد شركة مخصصة @endif
                        </small>
                    </div>
                    <div class="card-body text-right">
                        @if(isset($echantillon) && $echantillon && $echantillon->entreprise)
                        <div class="card border-0 mb-3 shadow-sm details-card company-details-card">
    <div class="card-body p-0">
        <div class="list-group list-group-flush details-list">

            <div class="list-group-item">
                <div class="item-label">
                    <i class="item-icon fas fa-building"></i>
                    <strong>اسم الشركة</strong>
                </div>
                <span class="item-value">{{ $echantillon->entreprise->nom_entreprise }}</span>
            </div>

            <div class="list-group-item">
                <div class="item-label">
                    <i class="item-icon fas fa-hashtag"></i>
                    <strong>الرمز الوطني</strong>
                </div>
                <span class="item-value">{{ $echantillon->entreprise->id ?? 'غير متوفر' }}</span>
            </div>

            <div class="list-group-item">
                <div class="item-label">
                    <i class="item-icon fas fa-briefcase"></i>
                    <strong>النشاط</strong>
                </div>
                <span class="item-value text-wrap">{{ $echantillon->entreprise->libelle_activite }}</span>
            </div>

            <div class="list-group-item">
                <div class="item-label">
                    <i class="item-icon fas fa-barcode"></i>
                    <strong>رمز النشاط</strong>
                </div>
                <span class="item-value">{{ $echantillon->entreprise->code_national }}</span>
            </div>

            <div class="list-group-item">
                <div class="item-label">
                    <i class="item-icon fas fa-map-marker-alt"></i>
                    <strong>العنوان</strong>
                </div>
                <span class="item-value text-wrap">
                    {{ $echantillon->entreprise->numero_rue }} {{ $echantillon->entreprise->nom_rue }},
                    {{ $echantillon->entreprise->ville }},
                    {{ $echantillon->entreprise->gouvernorat->nom ?? 'غير متوفر' }}
                </span>
            </div>

            
            <div class="list-group-item">
                <div class="item-label">
                    <i class="item-icon far fa-address-card"></i>
                    <strong>عنوان CNSS</strong>
                </div>
                <span class="item-value text-wrap">{{ $echantillon->entreprise->adresse_cnss ?? 'غير متوفر' }}</span>
            </div>

            <div class="list-group-item">
                <div class="item-label">
                    <i class="item-icon fas fa-globe-africa"></i>
                    <strong>منطقة CNSS</strong>
                </div>
                <span class="item-value">{{ $echantillon->entreprise->localite_cnss ?? 'غير متوفر' }}</span>
            </div>

            <div class="list-group-item">
                <div class="item-label">
                    <i class="item-icon fas fa-chart-line"></i>
                    <strong>حالة العينة</strong>
                </div>
                <span class="item-value">
                     @php
                                                $statut = $echantillon->statut;
                                                $badgeClass = '';
                                                $statutText = '';

                                                if ($statut == 'Complet' || $statut == 'termine') { // Gère 'Complet' et 'termine'
                                                    $badgeClass = 'badge-success'; 
                                                    $statutText = 'مكتمل';
                                                } 
                                                elseif ($statut == 'répondu') { 
                                                    $badgeClass = 'badge-success'; 
                                                    $statutText = 'تم الرد'; 
                                                } 
                                                elseif ($statut == 'réponse partielle') { 
                                                    $badgeClass = 'badge-warning'; 
                                                    $statutText = 'رد جزئي'; 
                                                } 
                                                elseif ($statut == 'un rendez-vous') { 
                                                    $badgeClass = 'badge-info'; 
                                                    $statutText = 'موعد'; 
                                                } 
                                                elseif ($statut == 'à appeler') { 
                                                    $badgeClass = 'badge-primary'; 
                                                    $statutText = 'إعادة إتصال'; 
                                                } 
                                                elseif ($statut == 'pas de réponse') { 
                                                    $badgeClass = 'badge-secondary'; 
                                                    $statutText = 'لا رد'; 
                                                } 
                                                elseif ($statut == 'refus' || $statut == 'refus final') { // Gère 'refus' et 'refus final'
                                                    $badgeClass = 'badge-danger';
                                                    $statutText = ($statut == 'refus final') ? 'رفض كلي' : 'رفض';
                                                } 
                                                elseif ($statut == 'introuvable') { 
                                                    $badgeClass = 'badge-dark'; 
                                                    $statutText = 'غير موجود'; 
                                                } 
                                                else { // 'en attente' ou autre statut par défaut
                                                    $badgeClass = 'badge-light'; 
                                                    $statutText = 'في الانتظار'; 
                                                }
                                            @endphp
                    <span class="badge {{ $badgeClass }}">{{ $statutText }}</span>
                </span>
            </div>
        </div>
    </div>
</div>
                            <div class="mt-4 text-center">
                                <div class="btn-group-vertical" role="group" style="width: 100%;">
                                    <form id="formEchantillonSuivant" action="{{ route('echantillons.next') }}" method="POST" style="display: block; width:100%;">
                                        @csrf
                                        <button id="btnEchantillonSuivant" type="submit" class="btn btn-primary btn-lg mb-2" style="background-color: #f39c12; border-color: #f39c12; width: 100%;"><i class="typcn typcn-arrow-right ml-2"></i> الانتقال إلى العينة التالية</button>
                                    </form>
                                    @if($peutLancerAppel ?? false)
                                        <button id="btnLancerAppel" class="btn btn-success btn-lg mb-2" style="width: 100%;" data-echantillon-id="{{ $echantillon->id }}"><i class="typcn typcn-phone-outgoing ml-1"></i> بدء المكالمة</button>
                                       {{-- ... à l'intérieur de @if($peutLancerAppel ?? false) ... --}}
<div id="appelActions" style="display: none; width: 100%;">
    <div class="btn-group mb-2" role="group" style="width: 100%;">
        <button id="btnAjouterRendezVous" class="btn btn-info" style="background-color: #1abc9c; border-color: #1abc9c; flex-grow: 1;"><i class="typcn typcn-calendar ml-1"></i> إضافة موعد</button>
        <button id="btnVoirScript" class="btn btn-warning" style="background-color: #f39c12; border-color: #f39c12; flex-grow: 1;"><i class="typcn typcn-document-text ml-1"></i> عرض نص المكالمة</button>
        
        @if(isset($echantillon) && $echantillon->id)
            <button id="btnOuvrirModalAjoutSuivi" type="button" class="btn btn-secondary" style="background-color: #95a5a6; border-color: #95a5a6; color:white; flex-grow: 1;" data-echantillon-id="{{ $echantillon->id }}">
                <i class="fas fa-history ml-1"></i> إعادة اتصال
            </button>
        @endif

        <button id="btnRefusAppel" class="btn btn-danger" style="display: none; flex-grow: 1;"><i class="typcn typcn-user-delete ml-1"></i> رفض</button> 
    </div>

    {{-- VÉRIFIEZ QUE CE BOUTON EST BIEN DANS LA BOUCLE @if(isset($echantillon) ... ) --}}
@if(isset($echantillon) && $echantillon->entreprise)
    <button id="btnVoirQuestionnaire" 
            class="btn btn-outline-primary btn-block"
            {{-- Ici nous ajoutons les données dynamiques --}}
            data-id-echantillon="{{ $echantillon->id }}"
            data-code-national="{{ $echantillon->entreprise->code_national }}"
            data-raison-sociale="{{ $echantillon->entreprise->nom_entreprise }}"
            data-id-utilisateur="{{ Auth::user()->id }}">
        <i class="typcn typcn-document-add ml-1"></i> الاستبيان
    </button>
@endif
</div>
{{-- ... --}}
                                {{-- ... --}}
                                    @else
                                        <p class="text-muted mt-2">لا يمكن بدء المكالمة لهذه العينة.</p>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-4 text-center">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">📊 إحصائيات سريعة</h6>
                                        <div class="row text-center">
                                            <div class="col-4"><span class="text-muted">متاح</span><br><span id="disponiblesCount" class="badge badge-info">...</span></div>
                                            <div class="col-4"><span class="text-muted">مخصص لك</span><br><span class="badge badge-primary">{{ $nombreEntreprisesAttribuees ?? '0' }}</span></div>
                                            <div class="col-4"><span class="text-muted">مكتمل</span><br><span class="badge badge-success">{{ $nombreEntreprisesRepondues ?? '0' }}</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-4"><i class="typcn typcn-coffee" style="font-size: 4rem; color: #95a5a6;"></i></div>
                                <h4 class="text-muted">لا توجد شركة متاحة في الوقت الحالي</h4>
                                <p class="text-muted">جميع العينات مخصصة أو مكتملة. يرجى المحاولة لاحقاً أو تحديث الصفحة.</p>
                                <button type="button" class="btn btn-outline-primary mt-3" onclick="window.location.reload()"><i class="typcn typcn-arrow-sync ml-1"></i> تحديث الصفحة</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if(isset($echantillon) && $echantillon && $echantillon->entreprise)
            <div class="row row-sm">
                {{-- Section Téléphones de l'entreprise (affichage direct sur la page) --}}
                <div class="col-lg-4">
                    <div class="card mg-b-20 shadow-sm" style="border-color: #3498db;">
                        <div class="card-header pb-0 text-center text-white" style="background-color: #3498db;"><h5 class="card-title mg-b-0">📞 أرقام هواتف الشركة</h5></div>
                        <div class="card-body text-right">
                            @if($echantillon->entreprise->telephones->isEmpty())
                                <p class="text-muted">لا توجد أرقام هاتف مسجلة لهذه الشركة.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped mg-b-0 text-md-nowrap">
                                        <thead><tr><th class="tx-14 fw-bold">الرقم</th><th class="tx-14 fw-bold">المصدر</th><th class="tx-14 fw-bold">أساسي</th><th class="tx-14 fw-bold">الحالة</th></tr></thead>
                                        <tbody>
                                            @foreach($echantillon->entreprise->telephones as $telephone)
                                                <tr>
                                                    <td><strong>{{ $telephone->numero }}</strong></td>
                                                    <td>{{ $telephone->source ?? 'غير محدد' }}</td>
                                                    <td>@if($telephone->est_primaire)<span class="badge badge-success">نعم</span>@else<span class="badge badge-secondary">لا</span>@endif</td>
                                                    <td>
                                                        @php
                                                            $etatVerif = $telephone->etat_verification ?? 'non_verifie';
                                                            $etatText = $etatVerif;
                                                            $etatBadgeClass = 'badge-light';
                                                            if($etatVerif === 'valide') { $etatBadgeClass = 'badge-success'; $etatText = 'صالح'; }
                                                            else if($etatVerif === 'faux_numero') { $etatBadgeClass = 'badge-danger'; $etatText = 'رقم خاطئ'; }
                                                            else if($etatVerif === 'pas_programme') { $etatBadgeClass = 'badge-warning'; $etatText = 'لا يرد'; }
                                                            else if($etatVerif === 'non_verifie') { $etatBadgeClass = 'badge-secondary'; $etatText = 'لم يتم التحقق منه'; }
                                                        @endphp
                                                        <span class="badge {{ $etatBadgeClass }} numero-badge-etat">{{ $etatText }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            <button id="btnTelephoneModal" class="btn btn-outline-primary btn-sm mg-t-10"><i class="typcn typcn-phone ml-1"></i> إضافة رقم هاتف للشركة</button>
                        </div>
                    </div>
                </div>
                {{-- Section Emails de l'entreprise --}}
                <div class="col-lg-4">
                    <div class="card mg-b-20 shadow-sm" style="border-color: #e74c3c;">
                        <div class="card-header pb-0 text-center text-white" style="background-color: #e74c3c;"><h5 class="card-title mg-b-0">📧 عناوين البريد الإلكتروني</h5></div>
                        <div class="card-body text-right">
                            @if(!isset($echantillon->entreprise->emails) || $echantillon->entreprise->emails->isEmpty())
                                <p class="text-muted">لا توجد عناوين بريد إلكتروني مسجلة.</p>
                            @else
                                <div class="table-responsive">
                                        <table class="table table-striped mg-b-0 text-md-nowrap"><thead><tr><th class="tx-14 fw-bold">البريد</th><th class="tx-14 fw-bold">المصدر</th><th class="tx-14 fw-bold">أساسي</th></tr></thead><tbody>
                                        @foreach($echantillon->entreprise->emails as $email)
                                            <tr><td>
                            {{-- Le lien contient maintenant toutes les données nécessaires --}}
                           <a href="#" class="clickable-email text-primary font-weight-bold"
                                data-email="{{ $email->email }}"
                                data-sujet-fr="{{ $echantillon->enquete->titre_mail_fr ?? '' }}"
                                data-corps-fr="{{ $echantillon->enquete->corps_mail_fr ?? '' }}"
                                data-sujet-ar="{{ $echantillon->enquete->titre_mail_ar ?? '' }}"
                                data-corps-ar="{{ $echantillon->enquete->corps_mail_ar ?? '' }}"
                                data-piecejointe="{{ $echantillon->enquete->piece_jointe_path ?? '' }}"
                                style="text-decoration: none; word-break: break-all; font-size: 14px;">
                                    <i class="fas fa-paper-plane" style="margin-left: 8px;"></i>{{ $email->email }}
                                </a>
                        </td>
<td>{{ $email->source ?? 'غير محدد' }}</td><td>@if($email->est_primaire)<span class="badge badge-success">نعم</span>@else<span class="badge badge-secondary">لا</span>@endif</td></tr>
                                        @endforeach
                                        </tbody></table>
                                </div>
                            @endif
                            <button id="btnEmailModal" class="btn btn-outline-danger btn-sm mg-t-10"><i class="typcn typcn-mail ml-1"></i> إضافة بريد إلكتروني</button>
                        </div>
                    </div>
                </div>
                {{-- Section Contacts de l'entreprise --}}
                <div class="col-lg-4">
                    <div class="card mg-b-20 shadow-sm" style="border-color: #2ecc71;">
                        <div class="card-header pb-0 text-center text-white" style="background-color: #2ecc71;"><h5 class="card-title mg-b-0">👥 جهات الاتصال</h5></div>
                        <div class="card-body text-right">
                            @if($echantillon->entreprise->contacts->isEmpty())
                                <p class="text-muted">لا توجد جهات اتصال مسجلة.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped mg-b-0 text-md-nowrap">
                                            <thead>
                                                <tr>
                                                    <th class="tx-14 fw-bold">الاسم</th>
                                                    <th class="tx-14 fw-bold">المنصب</th>
                                                    <th class="tx-14 fw-bold">الهاتف</th>
                                                    <th class="tx-14 fw-bold">البريد الإلكتروني</th>
                                                </tr>
                                            </thead>                                        <tbody>
                                        @foreach($echantillon->entreprise->contacts as $contact)
                                               {{-- NOUVEAU CODE --}}
<tr>
    <td><strong>{{ $contact->prenom }} {{ $contact->nom }}</strong></td>
    <td>{{ $contact->poste ?? 'غير محدد' }}</td>
    <td>{{ $contact->telephone ?? 'غير محدد' }}</td>
    <td>
        @if($contact->email)
            {{-- Ce lien est la clé. Il a la classe 'clickable-email' et les données de l'enquête. --}}
            <a href="#" class="clickable-email text-primary font-weight-bold"
               data-email="{{ $contact->email }}"
               data-sujet-fr="{{ $echantillon->enquete->titre_mail_fr ?? '' }}"
               data-corps-fr="{{ $echantillon->enquete->corps_mail_fr ?? '' }}"
               data-sujet-ar="{{ $echantillon->enquete->titre_mail_ar ?? '' }}"
               data-corps-ar="{{ $echantillon->enquete->corps_mail_ar ?? '' }}">
                <i class="fas fa-paper-plane" style="margin-left: 8px;"></i>{{ $contact->email }}
            </a>
        @else
            <span class="text-muted">غير متوفر</span>
        @endif
    </td>
</tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            <button id="btnContactModal" class="btn btn-outline-success btn-sm mg-t-10"><i class="typcn typcn-user-add ml-1"></i> إضافة جهة اتصال</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="modal fade" id="statutModal" tabindex="-1" role="dialog" aria-labelledby="statutModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header" style="background-color: #f39c12; color: white;"><h5 class="modal-title" id="statutModalLabel">تغيير حالة العينة</h5><button type="button" class="close" data-dismiss="modal" aria-label="إغلاق"><span aria-hidden="true">&times;</span></button></div><div class="modal-body text-right">@csrf<div class="form-group"><label for="statutSelect">الحالة الجديدة <span class="text-danger">*</span></label><select class="form-control" id="statutSelect" name="statut" required><option value="">اختر الحالة</option><option value="en attente">في الانتظار</option><option value="répondu">تم الرد</option><option value="réponse partielle">رد جزئي</option><option value="un rendez-vous">موعد</option><option value="pas de réponse">لا رد</option><option value="refus">رفض</option><option value="introuvable">غير موجود</option><option value="termine">مكتمل</option></select></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button><button type="button" class="btn btn-primary" onclick="changerStatut()">حفظ الحالة</button></div></div></div></div>
        
        {{-- Modale de Sélection de Numéro --}}
        <div class="modal fade" id="selectNumeroModal" tabindex="-1" role="dialog" aria-labelledby="selectNumeroModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #3498db; color: white;">
                        <h5 class="modal-title" id="selectNumeroModalLabel" dir="rtl">📞 اختيار رقم الهاتف وحالته</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق" style="color:white;"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body" dir="rtl">
                        <p>يرجى تحديد رقم للاتصال به وتحديد حالته من القائمة أدناه:</p>
                        <div id="listeNumerosContainer" class="list-group mb-3" style="max-height: 300px; overflow-y: auto;">
                            <p class="text-center text-muted" id="loadingNumeros">جاري تحميل الأرقام...</p>
                        </div>
                        <div class="form-group">
                            <label for="statutNumeroAppel">حالة الرقم المحدد:</label>
                            <select id="statutNumeroAppel" class="form-control">
                                <option value="valide" selected>صالح (لبدء المكالمة)</option>
                                <option value="faux_numero">رقم خاطئ</option>
                                <option value="pas_programme">غير مبرمج</option>
                            </select>
                        </div>
                        <p id="selectedPhoneNumberInfo" class="mt-2 font-weight-bold" style="display:none;">الرقم المختار: <span id="numeroChoisiText" style="color: #007bff;"></span></p>
                    </div>
                    <div class="modal-footer" dir="rtl">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                        <button type="button" id="btnEnregistrerStatutNumero" class="btn btn-info" disabled><i class="typcn typcn-bookmark"></i> حفظ حالة الرقم فقط</button>
                        <button type="button" id="btnConfirmerNumeroEtAppeler" class="btn btn-success" disabled><i class="typcn typcn-phone-outgoing"></i> اتصال بهذا الرقم</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Autres Modales (RendezVous, Telephone, Email, Contact, Script Appel) --}}
@if(isset($echantillon)) {{-- La modale est contextuelle à un échantillon --}}
<div class="modal fade" id="ajouterSuiviModal" tabindex="-1" role="dialog" aria-labelledby="ajouterSuiviModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-custom-suivi">
                <h5 class="modal-title" id="ajouterSuiviModalLabel"><i class="fas fa-history"></i> إضافة متابعة للعينة #{{ $echantillon->id }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-right">
                <form id="formAjouterNouveauSuivi" action="{{ route('suivis.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="suivi_echantillon_id_modal_input_js" name="echantillon_enquete_id" value="{{ $echantillon->id }}">
                    <div class="form-group">
                        <label for="cause_suivi_modal_input">سبب المتابعة <span class="text-danger">*</span></label>
                        <select class="form-control" id="cause_suivi_modal_input" name="cause_suivi" required>
                            <option value="">اختر سبب المتابعة</option>
                            <option value="Réponse absente">ليس هناك رد</option>
                            <option value="Personne non adéquate">لم أجد الشخص المناسب للإجابة</option>
                            <option value="Rappel demandé par client">إعادة الاتصال بطلب من المجيب  </option>
                            <option value="Information manquante">معلومات ناقصة</option>
                            <option value="Autre">أسباب أخرى</option>
                        </select>
                        <div class="invalid-feedback" id="cause_suivi_modal_error_msg">يرجى اختيار سبب المتابعة.</div>
                    </div>
                    <div class="form-group">
                        <label for="note_suivi_modal_input">ملاحظات (اختياري)</label>
                        <textarea class="form-control" id="note_suivi_modal_input" name="note" rows="4" placeholder="أدخل ملاحظاتك هنا..."></textarea>
                        <div class="invalid-feedback" id="note_suivi_modal_error_msg"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                <button type="submit" id="btnSubmitNouvelleSuivi" class="btn btn-submit-custom-suivi"><i class="fas fa-save" style="margin-left: 8px;"></i> حفظ المتابعة</button>
            </div>
        </div>
    </div>
</div>
@endif


        @if(isset($echantillon) && $echantillon && $echantillon->entreprise)
            {{-- ****************************************************** --}}
            {{-- ***** DEBUT DE LA SECTION MODIFIÉE POUR RENDEZVOUSMODAL ***** --}}
            {{-- ****************************************************** --}}
            <div class="modal fade" id="rendezVousModal" tabindex="-1" role="dialog" aria-labelledby="rendezVousModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #1abc9c; color: white;">
                            <h5 class="modal-title" id="rendezVousModalLabel">إضافة موعد جديد</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body text-right">
                            {{-- Le formulaire pour ajouter un rendez-vous --}}
                            {{-- Assurez-vous que $echantillon est disponible et a un ID --}}
                            <form id="formAjouterRendezVous" action="{{ route('rendezvous.store', ['id' => $echantillon->id]) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="heure_rdv_modal">تاريخ ووقت الموعد <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control @error('heure_rdv') is-invalid @enderror" id="heure_rdv_modal" name="heure_rdv" value="{{ old('heure_rdv') }}" required>
                                    @error('heure_rdv')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                               <div class="form-group">
    <label for="contact_personne_associee_nom_modal">جهة الاتصال بالشركة (اختياري)</label>
    @if(isset($echantillon->entreprise) && $echantillon->entreprise->contacts->isNotEmpty())
        <select class="form-control" id="contact_personne_associee_nom_modal" name="contact_rdv">
            {{-- MODIFIÉ: name="contact_personne_associee_nom" --}}
            <option value="">بدون جهة اتصال محددة</option>
            @foreach($echantillon->entreprise->contacts as $contact)
                {{-- MODIFIÉ: value contient maintenant le nom et le poste --}}
                <option value="{{ $contact->prenom }} {{ $contact->nom }} {{ $contact->poste ? '(' . $contact->poste . ')' : '' }}">
                    {{ $contact->prenom }} {{ $contact->nom }} {{ $contact->poste ? '(' . $contact->poste . ')' : '' }}
                </option>
            @endforeach
        </select>
    @else
        {{-- Ce champ texte est déjà correct pour une saisie manuelle du nom --}}
        <input type="text" class="form-control" id="contact_personne_associee_nom_fallback_modal" name="contact_rdv" placeholder="أدخل اسم جهة الاتصال (اختياري)">
        <small class="form-text text-muted">لا توجد جهات اتصال مسجلة.</small>
    @endif
</div>
                                <div class="form-group">
                                    <label for="notes_modal">ملاحظات إضافية للموعد (اختياري)</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes_modal" name="notes" rows="3" placeholder="أدخل ملاحظات حول الموعد">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                {{-- Champ caché pour identifier la soumission de ce formulaire modal spécifique (utile pour réouvrir en cas d'erreur) --}}
                                <input type="hidden" name="form_modal_submitted" value="rendezVousModal">

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                    <button type="submit" id="btnSubmitRendezVous" class="btn btn-info" style="background-color: #1abc9c; border-color: #1abc9c;">حفظ الموعد</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            {{-- **************************************************** --}}
            {{-- ***** FIN DE LA SECTION MODIFIÉE POUR RENDEZVOUSMODAL ***** --}}
            {{-- **************************************************** --}}
            
            {{-- Modale Telephone (pour ajouter un numéro à l'entreprise) --}}
            <div class="modal fade" id="telephoneModal" tabindex="-1" role="dialog" aria-labelledby="telephoneModalLabel" aria-hidden="true"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header" style="background-color: #3498db; color: white;"><h5 class="modal-title" id="telephoneModalLabel">إضافة رقم هاتف جديد</h5><button type="button" class="close" data-dismiss="modal" aria-label="إغلاق"><span aria-hidden="true">&times;</span></button></div><div class="modal-body text-right"><form action="{{ route('telephones.store', ['entreprise_id' => $echantillon->entreprise->id]) }}" method="POST">@csrf<div class="form-group"><label for="numeroTel">رقم الهاتف <span class="text-danger">*</span></label><input type="text" class="form-control" id="numeroTel" name="numero" placeholder="أدخل رقم الهاتف" required></div><div class="form-group"><label for="sourceTel">المصدر (اختياري)</label><input type="text" class="form-control" id="sourceTel" name="source" placeholder="أدخل مصدر الرقم"></div><div class="form-check"><input type="checkbox" class="form-check-input" id="estPrimaireTel" name="est_primaire" value="1"><label class="form-check-label" for="estPrimaireTel">رقم أساسي</label><small class="form-text text-muted">حدد إذا كان هذا الرقم هو الرقم الأساسي.</small></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button><button type="submit" class="btn btn-primary" style="background-color: #3498db; border-color: #3498db;">حفظ رقم الهاتف</button></div></form></div></div></div></div>
            
            {{-- Modale Email --}}
            <div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel" aria-hidden="true"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header" style="background-color: #e74c3c; color: white;"><h5 class="modal-title" id="emailModalLabel">إضافة بريد إلكتروني جديد</h5><button type="button" class="close" data-dismiss="modal" aria-label="إغلاق"><span aria-hidden="true">&times;</span></button></div><div class="modal-body text-right"><form action="{{ route('emails.store', ['entreprise_id' =>$echantillon->entreprise->id]) }}" method="POST">@csrf<div class="form-group"><label for="emailAddr">عنوان البريد الإلكتروني <span class="text-danger">*</span></label><input type="email" class="form-control" id="emailAddr" name="email" placeholder="أدخل عنوان البريد الإلكتروني" required><small class="form-text text-muted">مثال: info@company.com</small></div><div class="form-group"><label for="sourceEmailModal">المصدر (اختياري)</label><select class="form-control" id="sourceEmailModal" name="source"><option value="">اختر المصدر</option><option value="موقع_الشركة">موقع الشركة</option><option value="دليل_الأعمال">دليل الأعمال</option><option value="أخرى">أخرى</option></select></div><div class="form-check"><input type="checkbox" class="form-check-input" id="estPrimaireEmailModal" name="est_primaire" value="1"><label class="form-check-label" for="estPrimaireEmailModal">بريد إلكتروني أساسي</label><small class="form-text text-muted">حدد إذا كان هذا هو البريد الرئيسي.</small></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button><button type="submit" class="btn btn-danger" style="background-color: #e74c3c; border-color: #e74c3c;">حفظ البريد</button></div></form></div></div></div></div>
            
            {{-- Modale Contact (pour ajouter un contact à l'entreprise) --}}
{{-- Modale Contact (MODIFIÉE) --}}
<div class="modal fade" id="contactModal" tabindex="-1" role="dialog" aria-labelledby="contactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #2ecc71; color: white;">
                <h5 class="modal-title" id="contactModalLabel">إضافة جهة اتصال جديدة</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body text-right">
                <form action="{{ route('contacts.store', ['entreprise_id' => $echantillon->entreprise->id]) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="civiliteContact">اللقب (اختياري)</label>
                        {{-- MODIFICATION : Champ texte changé en liste de choix --}}
                        <select class="form-control" id="civiliteContact" name="civilite">
                            <option value="">اختر...</option>
                            <option value="Monsieur">السيد</option>
                            <option value="Madame">السيدة</option>
                        </select>
                    </div>
                    <div class="form-group">
                        {{-- MODIFICATION : Label traduit en arabe --}}
                        <label for="prenomContact">الإسم <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="prenomContact" name="prenom" required placeholder="أدخل الإسم">
                    </div>
                    <div class="form-group">
                        {{-- MODIFICATION : Label traduit en arabe --}}
                        <label for="nomContact">اللقب <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nomContact" name="nom" required placeholder="أدخل اللقب">
                    </div>
                    <div class="form-group">
                        <label for="posteContact">المنصب (اختياري)</label>
                        <input type="text" class="form-control" id="posteContact" name="poste" placeholder="مثل: مدير، موظف">
                    </div>
                    <div class="form-group">
                        <label for="emailContactModal">البريد الإلكتروني (اختياري)</label>
                        <input type="email" class="form-control" id="emailContactModal" name="email" placeholder="أدخل البريد الإلكتروني">
                    </div>
                    <div class="form-group">
                        <label for="telephoneContact">رقم الهاتف (اختياري)</label>
                        <input type="text" class="form-control" id="telephoneContact" name="telephone" placeholder="أدخل رقم الهاتف">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success" style="background-color: #2ecc71; border-color: #2ecc71;">حفظ جهة الاتصال</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>            
             
            {{-- ========================================================== --}}
            {{-- == DEBUT : MODALE DU SCRIPT D'APPEL (MIS A JOUR) == --}}
            {{-- ========================================================== --}}
            <div class="modal fade" id="appelScriptModal" tabindex="-1" role="dialog" aria-labelledby="appelScriptModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #2ecc71; color: white;">
                            <h5 class="modal-title" id="appelScriptModalLabel">نص المكالمة الهاتفية</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body text-right">
                            <div class="mb-3">
                                <button id="switchToArabic" class="btn btn-primary" style="background-color: #3498db; border-color: #3498db;">عربي</button>
                                <button id="switchToFrench" class="btn btn-secondary">Français</button>
                            </div>
                            <div style="background-color: white; padding: 20px; border-radius: 5px; border: 1px solid #ddd; height: 400px; overflow-y: auto;">
                                <div id="scriptArabe" style="display: block;">
<pre style="white-space: pre-wrap; direction: rtl; font-family: inherit; font-size: 16px; line-height: 1.6;">
<span style="color: #0066cc; font-weight: bold;">1. التقديم الأولي</span>
مرحبًا، معاك [الاسم الكامل]، نخدم في المعهد الوطني للإحصاء.
نتصل بيك في إطار البحث الوطني حول التشغيل والأجور، واللي يهدف باش نجمعو معلومات محينة على عدد العاملين، أنواع الخطط، والأجور المعتمدة في المؤسسات.

<span style="color: #0066cc; font-weight: bold;">2. التحقق من المؤسسة</span>
باش نبدأ، نحب نتأكد اللي أنا نحكي مع مؤسسة [اسم المؤسسة: {{ $echantillon->entreprise->nom_entreprise }}]؟
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
الاسم واللقب
الخطة/الوظيفة
رقم الهاتف المباشر
البريد الإلكتروني المهني

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
                                <div id="scriptFrancais" style="display: none;">
<pre style="white-space: pre-wrap; direction: ltr; font-family: inherit; font-size: 16px; line-height: 1.6;">
<span style="color: #0066cc; font-weight: bold;">1. Présentation initiale</span>
Bonjour, je suis [Prénom Nom], enquêteur à l’Institut National de la Statistique. Je vous appelle dans le cadre de l’Enquête Nationale sur l’Emploi et les Salaires, qui vise à collecter des informations actualisées sur les effectifs, les types d’emplois et les rémunérations pratiquées dans les entreprises.

<span style="color: #0066cc; font-weight: bold;">2. Vérification de l’entreprise</span>
Pour commencer, puis-je confirmer que je suis bien en ligne avec [Nom de l'entreprise: {{ $echantillon->entreprise->nom_entreprise }}] ?
Sinon, pourriez-vous s’il vous plaît me communiquer la raison sociale exacte de votre société ?
(Si ce n’est pas l’entreprise attendue : mettre fin à l’appel poliment. Si oui, poursuivre.)

<span style="color: #0066cc; font-weight: bold;">3. Demande d’adresse email de la société</span>
Merci beaucoup. Afin de vous transmettre un courriel introductif officiel, pourriez-vous me communiquer une adresse mail professionnelle de la société, s’il vous plaît ?

<span style="color: #0066cc; font-weight: bold;">4. Recherche du bon interlocuteur</span>
J’aurais besoin de parler à la personne responsable des ressources humaines ou à toute autre personne pouvant fournir des informations sur les effectifs et les salaires.
Pourriez-vous m’indiquer son nom ou me transférer l’appel, s’il vous plaît ?

<span style="color: #0066cc; font-weight: bold;">5. Nouvelle présentation (au bon interlocuteur)</span>
(Si transféré à la bonne personne, recommencer la présentation adaptée.)
Bonjour, je suis [Prénom Nom], téléopérateur/trice auprès de l’Institut National de la Statistique.
Je vous contacte dans le cadre de l’Enquête sur l’Emploi et les Salaires, à laquelle votre entreprise a été sélectionnée pour participer.
Cette enquête est obligatoire et ses résultats sont utilisés exclusivement à des fins statistiques et de politique publique. Toutes les informations que vous nous fournirez seront traitées de manière strictement confidentielle.

<span style="color: #0066cc; font-weight: bold;">6. Collecte des coordonnées</span>
Afin de vous envoyer les détails de l’enquête, pourriez-vous me communiquer vos coordonnées complètes :
– Nom et prénom
– Fonction
– Numéro de téléphone direct
– Adresse email professionnelle

<span style="color: #0066cc; font-weight: bold;">7. Envoi du mail avec le lien vers le questionnaire</span>
Merci. Je vais immédiatement vous faire parvenir un email contenant un lien vers le questionnaire en ligne, accompagné de toutes les explications nécessaires pour chaque question.

<span style="color: #0066cc; font-weight: bold;">8. Proposition de réponse immédiate ou prise de rendez-vous</span>
Souhaitez-vous que nous le remplissions ensemble dès maintenant par téléphone ? Cela prend en moyenne 15 à 20 minutes.
Si ce n’est pas possible tout de suite, je peux vous proposer de convenir d’un rendez-vous à un moment plus propice. Cela vous permettra également de jeter un œil au questionnaire et de préparer les réponses en amont.

<span style="color: #0066cc; font-weight: bold;">9. Clôture</span>
(Si rendez-vous fixé :)
Parfait, je vous recontacterai donc le [jour] à [heure]. Je vous remercie pour votre disponibilité et votre collaboration.

(Si l’entretien est mené immédiatement :)
Merci, nous allons pouvoir commencer.

(Si refus ou autre cas :)
Très bien, je vous remercie pour votre temps. N’hésitez pas à nous recontacter si besoin. Bonne journée !
</pre>
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                <label for="notesAppel">ملاحظات المكالمة (اختياري)</label>
                                <textarea class="form-control" id="notesAppel" name="notesAppel" rows="3" placeholder="أدخل ملاحظات حول المكالمة"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- ======================================================== --}}
            {{-- == FIN : MODALE DU SCRIPT D'APPEL (MIS A JOUR) == --}}
            {{-- ======================================================== --}}
            
        @endif {{-- ✅ Fin du @if qui englobe les modales conditionnelles --}}
        


    </div> {{-- Fin de .container-fluid --}}

  {{-- ========================================================== --}}
{{-- == MODALE D'ENVOI D'EMAIL (VERSION FINALE BILINGUE) == --}}
{{-- ========================================================== --}}
{{-- ========================================================== --}}
{{-- == MODALE D'ENVOI D'EMAIL AVEC PRÉVISUALISATION == --}}
{{-- ========================================================== --}}
<div class="modal fade" id="sendEmailModal" tabindex="-1" role="dialog" aria-labelledby="sendEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #007bff; color: white;">
                <h5 class="modal-title" id="sendEmailModalLabel">Aperçu et Envoi d'E-mail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق" style="color: white; margin-left: 0; padding-left:0;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="sendEmailForm">
                <div class="modal-body text-right" dir="rtl">
                    @csrf
                    @if(isset($echantillon))
                        <input type="hidden" name="echantillon_id" value="{{ $echantillon->id }}">
                        <input type="hidden" name="entreprise_id" value="{{ $echantillon->entreprise->id }}">
                    @endif

                    <div class="form-group text-center bg-light p-2 rounded mb-3">
                        <label class="d-block mb-2"><strong>Langue du message</strong></label>
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-primary active">
                                <input type="radio" name="langue_mail" value="ar" autocomplete="off" checked> العربية
                            </label>
                            <label class="btn btn-outline-primary">
                                <input type="radio" name="langue_mail" value="fr" autocomplete="off"> Français
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="destinataire_email"><strong>Destinataire :</strong></label>
                        <input type="email" class="form-control" id="destinataire_email" name="destinataire" readonly style="background-color: #e9ecef; direction: ltr;">
                    </div>

                    {{-- NOUVEAU: Champs visibles mais non modifiables --}}
                    <div class="form-group">
                        <label for="email_sujet"><strong>Sujet :</strong></label>
                        <input type="text" class="form-control" id="email_sujet" name="sujet" readonly style="background-color: #e9ecef;">
                    </div>

                    <div class="form-group">
                        <label for="email_corps"><strong>Message :</strong></label>
                        <textarea class="form-control" id="email_corps" name="corps" rows="8" readonly style="background-color: #e9ecef;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" id="sendEmailSubmitBtn" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Envoyer</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    // Fonction showFeedback (inchangée)
    function showFeedback(message, type = 'success') {
        console.log(`💬 Feedback (${type}): ${message}`);
        const feedbackDiv = document.createElement('div');
        const icon = type === 'success' ? '✅' : (type === 'danger' ? '❌' : '⚠️');
        feedbackDiv.className = `alert alert-${type} auto-hide-feedback`;
        feedbackDiv.innerHTML = `${icon} ${message}`;
        feedbackDiv.style.cssText = `position: fixed; top: 70px; right: 20px; z-index: 10001; background-color: ${type === 'success' ? '#2ecc71' : (type === 'danger' ? '#e74c3c' : '#f39c12')}; color: white; padding: 15px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); font-size: 1rem;`;
        document.body.appendChild(feedbackDiv);
        setTimeout(() => { if (feedbackDiv) { feedbackDiv.style.transition = 'opacity 0.5s ease, transform 0.5s ease'; feedbackDiv.style.opacity = '0'; feedbackDiv.style.transform = 'translateY(-20px)'; setTimeout(() => feedbackDiv.remove(), 500);}}, 3500);
    }

    // Fonction changerStatut (pour l'échantillon global)
    async function changerStatut() { 
        console.log("🔶 changerStatut (échantillon) - Fonction appelée.");
        const statutSelect = document.getElementById('statutSelect');
        const statut = statutSelect ? statutSelect.value : null;
        const csrfTokenFromMeta = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
        
        @if(isset($echantillon) && $echantillon)
            const echantillonId = {{ $echantillon->id }};
            if (!statut) { showFeedback('الرجاء اختيار حالة للعينة', 'warning'); return; }

            let urlUpdateStatut = `{{ url('/echantillons') }}/${echantillonId}/statut`;
            try {
                const response = await fetch(urlUpdateStatut, {
                    method: 'POST', 
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfTokenFromMeta },
                    body: JSON.stringify({ statut: statut })
                });
                let data;
                try { data = await response.json(); } 
                catch (e) {
                    const textResponse = await response.text(); console.error("Impossible de parser la réponse JSON pour changerStatut:", e, "Réponse texte:", textResponse);
                    showFeedback(response.status === 419 ? 'خطأ في جلسة المستخدم (CSRF). يرجى تحديث الصفحة.' : 'حدث خطأ.', 'danger');
                    if (typeof $ !== 'undefined' && $('#statutModal').modal) $('#statutModal').modal('hide'); return;
                }

                if (typeof $ !== 'undefined' && $('#statutModal').modal) $('#statutModal').modal('hide');

                if (response.ok && data.success) {
    const statutDisplayElement = document.getElementById('statutDisplay');
    if (statutDisplayElement) {
    let statutText = statut;
    let badgeClass = 'badge-light'; // Un défaut sûr

    if (statut === 'Complet' || statut === 'termine') {
        statutText = 'مكتمل';
        badgeClass = 'badge-success';
    } else if (statut === 'répondu') {
        statutText = 'تم الرد';
        badgeClass = 'badge-success';
    } else if (statut === 'réponse partielle') {
        statutText = 'رد جزئي';
        badgeClass = 'badge-warning';
    } else if (statut === 'un rendez-vous') {
        statutText = 'موعد';
        badgeClass = 'badge-info';
    } else if (statut === 'à appeler') {
        statutText = 'إعادة إتصال';
        badgeClass = 'badge-primary';
    } else if (statut === 'pas de réponse') {
        statutText = 'لا رد';
        badgeClass = 'badge-secondary';
    } else if (statut === 'refus' || statut === 'refus final') {
        statutText = (statut === 'refus final') ? 'رفض كلي' : 'رفض';
        badgeClass = 'badge-danger';
    } else if (statut === 'introuvable') {
        statutText = 'غير موجود';
        badgeClass = 'badge-dark';
    } else { // 'en attente' ou autre
        statutText = 'في الانتظار';
        badgeClass = 'badge-primary';
    }

    statutDisplayElement.textContent = statutText;
    statutDisplayElement.className = 'badge ' + badgeClass;
        // On ré-attache le style et l'attribut pour la modale
        statutDisplayElement.style.cursor = 'pointer'; 
    }
    showFeedback(data.message || 'تم تحديث حالة العينة بنجاح!');
                } else { 
                    showFeedback(data.message || 'حدث خطأ أثناء تحديث حالة العينة.', 'danger'); 
                }
            } catch (error) { 
                console.error("Erreur AJAX (changerStatut):", error);
                showFeedback('خطأ في الاتصال بالخادم لتحديث حالة العينة.', 'danger'); 
            }
        @else
            showFeedback('لا يوجد عينة محددة لتغيير حالتها.', 'warning');
        @endif
    }

    document.addEventListener('DOMContentLoaded', function () {
        
        
        console.log('🚀 PAGE INDEX CHARGÉE - JS MODIFIÉ EN COURS 🚀');
        const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
        
       // =============================================================
        // == DÉBUT : NOUVELLE LOGIQUE POUR L'ENVOI D'EMAIL BILINGUE ==
        // =============================================================
        
        var emailData = {}; // Variable pour stocker les données du mail cliqué

        // Fonction pour mettre à jour les champs de prévisualisation 'sujet' et 'corps'
        function updatePreviewFields(lang) {
            if (!emailData) return; // Sécurité

            if (lang === 'fr') {
                $('#email_sujet').val(emailData.sujetFr || '').css('direction', 'ltr');
                $('#email_corps').val(emailData.corpsFr || '').css('direction', 'ltr');
            } else { // 'ar' par défaut
                $('#email_sujet').val(emailData.sujetAr || '').css('direction', 'rtl');
                $('#email_corps').val(emailData.corpsAr || '').css('direction', 'rtl');
            }
        }

        // Étape 1 : Quand un lien email est cliqué, on stocke les données et on prépare la modale
        $(document).on('click', '.clickable-email', function(e) {
            e.preventDefault();
            var link = $(this);

            emailData = {
                email:     link.data('email'),
                sujetFr:   link.data('sujet-fr'),
                corpsFr:   link.data('corps-fr'),
                sujetAr:   link.data('sujet-ar'),
                corpsAr:   link.data('corps-ar')
            };

            $('#destinataire_email').val(emailData.email);
            
            // Réinitialiser la langue sur "Arabe"
            $('input[name="langue_mail"][value="ar"]').prop('checked', true).parent().addClass('active').siblings().removeClass('active');
            
            // Mettre à jour les champs de prévisualisation avec le contenu arabe par défaut
            updatePreviewFields('ar');
            
            $('#sendEmailModal').modal('show');
        });

        // Étape 2: Mettre à jour les champs si l'utilisateur change de langue
        $('input[name="langue_mail"]').on('change', function() {
            updatePreviewFields($(this).val());
        });

        // Étape 3 : Quand le formulaire est soumis, il envoie directement tous les champs
        $('#sendEmailForm').on('submit', async function(e) {
            e.preventDefault();
            const submitBtn = $('#sendEmailSubmitBtn');
            
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> ...جاري الإرسال');
            
            // Le FormData va maintenant inclure automatiquement les champs sujet et corps car ils ont un attribut "name"
            let formData = new FormData(this);
            
            try {
                const response = await fetch('{{ route("emails.send") }}', { 
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    showFeedback(result.message || 'تم إرسال البريد الإلكتروني بنجاح!', 'success');
                    $('#sendEmailModal').modal('hide');
                } else {
                    let errorMsg = result.message || 'حدث خطأ أثناء الإرسال.';
                    if(result.errors) {
                       errorMsg = Object.values(result.errors)[0][0];
                    }
                    showFeedback(errorMsg, 'danger');
                }
            } catch (error) {
                console.error("Erreur AJAX d'envoi d'email:", error);
                showFeedback('خطأ في الشبكة. لا يمكن إرسال البريد الإلكتروني.', 'danger');
            } finally {
                submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> إرسال');
            }
        });
        
        // =============================================================
        // == FIN : NOUVELLE LOGIQUE POUR L'ENVOI D'EMAIL BILINGUE   ==
        // =============================================================
        



        let echantillon_entreprise_id_js = {!! $echantillonEntrepriseIdJson ?? 'null' !!};
        let echantillonDataForModal = null; 
         let emailPreselectionne = null; 
        @if(isset($echantillon) && $echantillon) // Initialiser seulement si $echantillon est défini
            echantillonDataForModal = {
                entreprise: {
                    id: echantillon_entreprise_id_js,
                    telephones: {!! $echantillonEntrepriseTelephonesJson ?? '[]' !!},
                    contacts: {!! $echantillonContactsJson ?? '[]' !!}
                },
                echantillon_id: {{ $echantillon->id }} 
            };
            console.log("Données pour la modale (echantillonDataForModal) initialisées:", echantillonDataForModal);
        @else
            console.log("Aucun échantillon valide pour initialiser echantillonDataForModal.");
            echantillonDataForModal = { entreprise: { id: null, telephones: [], contacts: [] }, echantillon_id: null };
        @endif
        
        setTimeout(function() { const alerts = document.querySelectorAll('.auto-hide'); alerts.forEach(alert => { if (alert) { alert.style.transition = 'opacity 0.5s ease'; alert.style.opacity = '0'; setTimeout(() => alert.remove(), 500); }}); }, 5000);
        
        const disponiblesCountElement = document.getElementById('disponiblesCount');
        // NOUVEAU : Gérer le clic sur un e-mail pour le présélectionner
                document.body.addEventListener('click', function(e) {
                    const link = e.target.closest('.clickable-email');
                    if (link) {
                        e.preventDefault();
                        emailPreselectionne = link.dataset.email;
                    }
                });
        
        
        
        function updateDisponiblesCount() { 
            if (!disponiblesCountElement) return; 
            fetch('{{ route("api.echantillons.disponibles") }}', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } })
            .then(response => response.ok ? response.json() : Promise.reject(response))
            .then(data => { 
                if (data.success) disponiblesCountElement.textContent = data.disponibles; 
                else { disponiblesCountElement.textContent = 'N/A'; console.warn('Compteur API échec:', data.message); }
            }).catch(error => { console.error('⚠️ Erreur réseau compteur:', error); disponiblesCountElement.textContent = 'N/A'; });
        }
        if (disponiblesCountElement) { updateDisponiblesCount(); setInterval(updateDisponiblesCount, 30000); }
        
        const btnLancerAppel = document.getElementById('btnLancerAppel');
        const appelActions = document.getElementById('appelActions');
        // const notesAppelTextarea = document.getElementById('notesAppel'); // Déjà défini dans la modale scriptAppelModal
        let isCalling = false;
        let currentAppelId = null;

        function setupModalButton(buttonId, modalId) {
            const button = document.getElementById(buttonId);
            if (button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (typeof $ !== 'undefined' && $(modalId).modal) $(modalId).modal('show');
                    else console.error(`jQuery ou Bootstrap modal non disponible pour ${modalId}`);
                });
            } else {
                console.warn(`Bouton avec ID '${buttonId}' non trouvé pour setupModalButton.`);
            }
        }
        setupModalButton('btnTelephoneModal', '#telephoneModal');
        setupModalButton('btnEmailModal', '#emailModal');
        setupModalButton('btnContactModal', '#contactModal');
        
        
       function updateCallUI(calling, appelData = null) {
    console.log(`🔄 updateCallUI - Appel en cours demandé: ${calling}, Données d'appel reçues:`, appelData);
    isCalling = calling;
    currentAppelId = appelData ? appelData.id : null;
    console.log(`   Nouveau statut UI: isCalling = ${isCalling}, currentAppelId = ${currentAppelId}`);

    const btnLancerAppelElem = document.getElementById('btnLancerAppel');
    const appelActionsElem = document.getElementById('appelActions');
    const notesAppelTextareaElem = document.getElementById('notesAppel');
    const btnRefusAppelElem = document.getElementById('btnRefusAppel'); // Récupérer l'élément du bouton Refus

    if (!btnLancerAppelElem) { console.warn("Bouton '#btnLancerAppel' non trouvé dans updateCallUI."); return; }

    if (calling) {
        btnLancerAppelElem.innerHTML = "<i class='typcn typcn-phone ml-1'></i> إنهاء المكالمة";
        btnLancerAppelElem.classList.remove('btn-success'); btnLancerAppelElem.classList.add('btn-danger');
        if (appelActionsElem) { appelActionsElem.style.display = 'block'; /* ... animation ... */ }
        if (btnRefusAppelElem) { btnRefusAppelElem.style.display = 'inline-block'; } // Afficher le bouton Refus
    } else {
        btnLancerAppelElem.innerHTML = "<i class='typcn typcn-phone-outgoing ml-1'></i> بدء المكالمة";
        btnLancerAppelElem.classList.remove('btn-danger'); btnLancerAppelElem.classList.add('btn-success');
        if (appelActionsElem) { appelActionsElem.style.display = 'none'; /* ... animation ... */ }
        if (notesAppelTextareaElem) notesAppelTextareaElem.value = '';
        if (btnRefusAppelElem) { btnRefusAppelElem.style.display = 'none'; } // Cacher le bouton Refus
        if (typeof $ !== 'undefined' && $('#appelScriptModal').modal) $('#appelScriptModal').modal('hide');
    }
    console.log("🔄 updateCallUI - UI mise à jour visuellement.");
}
        async function checkInitialCallState() {
            console.log('🔍 checkInitialCallState - DÉBUT de la vérification.');
            const btnLancerAppelElem = document.getElementById('btnLancerAppel');
            if (!btnLancerAppelElem) { console.warn("Bouton '#btnLancerAppel' non trouvé pour checkInitialCallState."); updateCallUI(false); return; }
            
            const echantillonActuelIdSurPage = btnLancerAppelElem.getAttribute('data-echantillon-id');
            console.log(`   ID échantillon sur la page (checkInitialCallState): ${echantillonActuelIdSurPage}`);

            if (!echantillonActuelIdSurPage) { console.warn("Pas d'ID échantillon sur le bouton #btnLancerAppel, appel non en cours par défaut."); updateCallUI(false); return; }
            
            try {
                const response = await fetch('{{ route("echantillons.appelEnCours") }}', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }});
                if (!response.ok) throw new Error(`Erreur réseau: ${response.status} ${response.statusText}`);
                const data = await response.json();
                console.log('   Données reçues de echantillons.appelEnCours:', data);
                if (data.success && data.appel && data.appel.echantillon_enquete_id == echantillonActuelIdSurPage) { 
                    console.log('   ✅ Appel en cours détecté pour cet échantillon.'); updateCallUI(true, data.appel); 
                    const notesAppelTextareaElem = document.getElementById('notesAppel');
                    if (notesAppelTextareaElem && data.appel.notes) notesAppelTextareaElem.value = data.appel.notes; 
                } else { 
                    console.log('   Aucun appel en cours pour cet échantillon ou données/ID invalides.'); updateCallUI(false); 
                }
            } catch (error) { console.error('   ❌ Erreur dans checkInitialCallState:', error); /* showFeedback Potentiel ici */ updateCallUI(false); }
            console.log('🔍 checkInitialCallState - FIN de la vérification.');
        }
        const btnRefusAppel = document.getElementById('btnRefusAppel');
if (btnRefusAppel) {
    btnRefusAppel.addEventListener('click', async function (e) {
        e.preventDefault();
        const echantillonId = document.getElementById('btnLancerAppel').getAttribute('data-echantillon-id');
        const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

        if (!echantillonId) {
            showFeedback('Erreur : L\'ID de l\'échantillon est manquant pour le refus.', 'danger');
            return;
        }

        if (confirm('هل تريد فعلا تعديل حالة العينة إلى رفض ؟')) { // Confirmation avant de refuser
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refus...';

            try {
                const response = await fetch(`{{ url('/echantillons/${echantillonId}/refus') }}`, { // Définissez cette route dans votre web.php
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showFeedback(data.message || '👍 L\'échantillon a été refusé avec succès !');
                    // Mettez à jour l'interface utilisateur pour refléter le statut 'refus' (par exemple, recharger la page ou mettre à jour le badge)
                    window.location.reload(); // Moyen le plus simple de mettre à jour l'UI
                } else {
                    showFeedback(data.message || '❌ Échec du refus de l\'échantillon.', 'danger');
                }
            } catch (error) {
                console.error('Erreur AJAX (refus) :', error);
                showFeedback('⚠️ Une erreur s\'est produite lors de la tentative de refus de l\'échantillon.', 'danger');
            } finally {
                this.disabled = false;
                this.innerHTML = '<i class="typcn typcn-user-delete ml-1"></i> Refus';
            }
        }
    });
}


        // --- NOUVELLE LOGIQUE POUR MODAL DE SÉLECTION DE NUMÉRO ---
        const selectNumeroModal = document.getElementById('selectNumeroModal');
        const listeNumerosContainer = document.getElementById('listeNumerosContainer');
        const btnConfirmerNumeroEtAppeler = document.getElementById('btnConfirmerNumeroEtAppeler');
        const btnEnregistrerStatutNumero = document.getElementById('btnEnregistrerStatutNumero');
        const statutNumeroAppelSelect = document.getElementById('statutNumeroAppel');
        const selectedPhoneNumberInfo = document.getElementById('selectedPhoneNumberInfo');
        const numeroChoisiText = document.getElementById('numeroChoisiText');

        function populateNumeroModal(data) {
            if (!listeNumerosContainer) { console.error("Conteneur #listeNumerosContainer non trouvé!"); return; }
            listeNumerosContainer.innerHTML = '<p class="text-center text-muted" id="loadingNumeros">جاري تحميل الأرقام...</p>';
            
            if(btnConfirmerNumeroEtAppeler) btnConfirmerNumeroEtAppeler.disabled = true;
            if(btnEnregistrerStatutNumero) btnEnregistrerStatutNumero.disabled = true;
            if(selectedPhoneNumberInfo) selectedPhoneNumberInfo.style.display = 'none';
            if(statutNumeroAppelSelect) statutNumeroAppelSelect.value = 'valide';

            let listContent = '';
            let hasNumbers = false;

            if (data && data.entreprise && data.entreprise.telephones && data.entreprise.telephones.length > 0) {
                hasNumbers = true;
                data.entreprise.telephones.forEach(tel => {
                    let displayText = `<strong>${tel.numero}</strong> <span class="badge badge-pill badge-info numero-badge-etat">Entreprise</span>`;
                    if (tel.est_primaire) displayText += ` <span class="badge badge-success numero-badge-etat">أساسي</span>`;
                    let etatVerification = tel.etat_verification || 'non_verifie';
                    let etatBadgeClass = 'badge-light'; let etatText = etatVerification;
                    if(etatVerification === 'valide') { etatBadgeClass = 'badge-success'; etatText = 'صالح'; }
                    else if(etatVerification === 'faux_numero') { etatBadgeClass = 'badge-danger'; etatText = 'رقم خاطئ'; }
                    else if(etatVerification === 'pas_programme') { etatBadgeClass = 'badge-warning'; etatText = 'لا يرد'; }
                    else if(etatVerification === 'non_verifie') { etatBadgeClass = 'badge-secondary'; etatText = 'لم يتم التحقق منه'; }
                    else { etatText = etatVerification; }
                    displayText += ` <span class="badge ${etatBadgeClass} numero-badge-etat" data-current-status="${etatVerification}">${etatText}</span>`;
                    listContent += `<a href="#" class="list-group-item list-group-item-action text-right" data-numero="${tel.numero}" data-phone-id="${tel.id || ''}" data-phone-type="entreprise" data-contact-id="${tel.contact_id || ''}">${displayText}</a>`;
                });
            }

            if (data && data.entreprise && data.entreprise.contacts && data.entreprise.contacts.length > 0) {
                data.entreprise.contacts.forEach(contact => {
                    if (contact.telephone_principal_contact && contact.telephone_principal_contact.trim() !== '') {
                        hasNumbers = true;
                        let contactDisplayName = `${contact.prenom || ''} ${contact.nom || ''}`.trim() || 'N/A';
                        let displayText = `<strong>${contact.telephone_principal_contact}</strong> <span class="badge badge-pill badge-secondary numero-badge-etat">Contact: ${contactDisplayName}</span>`;
                        let etatVerificationContact = contact.etat_verification || 'non_verifie';
                        let etatBadgeClass = 'badge-light'; let etatText = etatVerificationContact;
                        if(etatVerificationContact === 'valide') { etatBadgeClass = 'badge-success'; etatText = 'صالح'; }
                        else if(etatVerificationContact === 'faux_numero') { etatBadgeClass = 'badge-danger'; etatText = 'رقم خاطئ'; }
                        else if(etatVerificationContact === 'pas_programme') { etatBadgeClass = 'badge-warning'; etatText = 'لا يرد'; }
                        else if(etatVerificationContact === 'non_verifie') { etatBadgeClass = 'badge-secondary'; etatText = 'لم يتم التحقق منه'; }
                        else { etatText = etatVerificationContact; }
                        displayText += ` <span class="badge ${etatBadgeClass} numero-badge-etat" data-current-status="${etatVerificationContact}">${etatText}</span>`;
                        listContent += `<a href="#" class="list-group-item list-group-item-action text-right" data-numero="${contact.telephone_principal_contact}" data-phone-id="${contact.telephone_entreprise_id || ''}" data-contact-id="${contact.id}" data-phone-type="contact">${displayText}</a>`;
                    }
                });
            }

            if (!hasNumbers) {
                listeNumerosContainer.innerHTML = '<p class="text-center text-muted py-3">لا توجد أرقام هاتف للعرض.</p>';
            } else {
                listeNumerosContainer.innerHTML = listContent;
                document.querySelectorAll('#listeNumerosContainer .list-group-item-action').forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        document.querySelectorAll('#listeNumerosContainer .list-group-item-action').forEach(i => i.classList.remove('active'));
                        this.classList.add('active');
                        const currentStatus = this.querySelector('.numero-badge-etat[data-current-status]')?.getAttribute('data-current-status');
                        if(numeroChoisiText) numeroChoisiText.textContent = this.getAttribute('data-numero');
                        if(selectedPhoneNumberInfo) selectedPhoneNumberInfo.style.display = 'block';
                        if(btnConfirmerNumeroEtAppeler) btnConfirmerNumeroEtAppeler.disabled = false;
                        if(btnEnregistrerStatutNumero) btnEnregistrerStatutNumero.disabled = false;
                        // Pré-sélectionner le statut actuel dans le dropdown si possible
                        if (currentStatus && statutNumeroAppelSelect) {
                                if (Array.from(statutNumeroAppelSelect.options).some(opt => opt.value === currentStatus)) {
                                    statutNumeroAppelSelect.value = currentStatus;
                                } else {
                                    // Si le statut actuel n'est pas une option valide (ex: un ancien statut), remettre à 'valide' ou 'non_verifie'
                                    statutNumeroAppelSelect.value = 'non_verifie'; 
                                }
                        } else if (statutNumeroAppelSelect) {
                            statutNumeroAppelSelect.value = 'valide'; // Défaut si pas de statut actuel connu
                        }
                    });
                });
            }
        }

        const btnLancerAppelGlobal = document.getElementById('btnLancerAppel'); // Variable globale pour ce bouton
        if (btnLancerAppelGlobal) {
            if (echantillonDataForModal && echantillonDataForModal.echantillon_id) {
                console.log("✅ Bouton '#btnLancerAppel' initialisé pour échantillon ID:", echantillonDataForModal.echantillon_id);
                btnLancerAppelGlobal.setAttribute('data-echantillon-id', echantillonDataForModal.echantillon_id);
                checkInitialCallState(); // Vérifie si un appel est déjà en cours au chargement
            } else {
                   console.warn("Impossible d'initialiser #btnLancerAppel, pas d'échantillon actif.");
            }
            
            btnLancerAppelGlobal.addEventListener('click', async function (e) {
                e.preventDefault();
                // ... (Logique de btnLancerAppel comme dans ma réponse précédente détaillée - Turn 10)
                // S'assurer d'appeler populateNumeroModal(echantillonDataForModal);
                   console.log(`🔥 CLIC sur #btnLancerAppel! isCalling: ${isCalling}, currentAppelId: ${currentAppelId}`);
                if (!isCalling) { 
                    const echantillonIdPourAppel = this.getAttribute('data-echantillon-id');
                    if (!echantillonIdPourAppel) { showFeedback('معرف العينة مفقود. يرجى تحديث الصفحة.', 'danger'); return; }
                    
                    if (!echantillonDataForModal || echantillonDataForModal.echantillon_id != echantillonIdPourAppel) {
                        showFeedback('عدم تطابق في بيانات العينة. يرجى تحديث الصفحة.', 'warning');
                        // Pourrait nécessiter un rechargement des données ou de la page.
                        // Utiliser les données actuelles si echantillonDataForModal existe, sinon une structure vide.
                        populateNumeroModal(echantillonDataForModal || { entreprise: { id: null, telephones: [], contacts: [] } });
                    } else {
                        populateNumeroModal(echantillonDataForModal);
                    }
                    
                    if (typeof $ !== 'undefined' && $(selectNumeroModal).modal) { $(selectNumeroModal).modal('show'); } 
                    else { console.error("Modal #selectNumeroModal non trouvé.");}
                } else { // Terminer l'appel
                    console.log('⏹️ Tentative de fin d\'appel ID:', currentAppelId);
                    if (!currentAppelId) { showFeedback('معرف المكالمة الحالية مفقود لإنهاء.', 'danger'); return; }
                    this.disabled = true;
                    const notesTextarea = document.getElementById('notesAppel');
                    const notes = notesTextarea ? notesTextarea.value : '';
                    try {
                        const response = await fetch('{{ route("echantillons.terminerAppel") }}', { 
                            method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }, 
                            body: JSON.stringify({ appel_id: currentAppelId, notes: notes }) 
                        });
                        const data = await response.json();
                        if (response.ok && data.success) { 
                            showFeedback(data.message || 'انتهت المكالمة بنجاح!'); updateCallUI(false); 
                        } else { showFeedback(data.message || 'تعذر إنهاء المكالمة.', 'danger'); }
                    } catch (error) { 
                        console.error("Erreur AJAX (terminerAppel):", error);
                        showFeedback('خطأ في الاتصال بالخادم (إنهاء المكالمة).', 'danger');
                    } finally { this.disabled = false; }
                }
            });
        } else {
            console.info("Bouton '#btnLancerAppel' non trouvé ou non applicable sur cette page.");
        }

        if (btnEnregistrerStatutNumero) {
    btnEnregistrerStatutNumero.addEventListener('click', async function() {
        console.log('💾 [SaveStatus] Clic sur Enregistrer Statut Numéro');
        const activeListItem = document.querySelector('#listeNumerosContainer .list-group-item-action.active');
        if (!activeListItem) { showFeedback('الرجاء تحديد رقم أولاً.', 'warning'); return; }

        let phoneIdToUpdate = activeListItem.getAttribute('data-phone-id'); 
        const contactIdForCreation = activeListItem.getAttribute('data-contact-id');
        const phoneType = activeListItem.getAttribute('data-phone-type');
        const numeroAAenregistrer = activeListItem.getAttribute('data-numero');
        
        if (!echantillonDataForModal || !echantillonDataForModal.entreprise || !echantillonDataForModal.entreprise.id) {
            showFeedback('خطأ: معرف المؤسسة للعينة الحالية مفقود.', 'danger'); return;
        }
        const entrepriseIdForCall = echantillonDataForModal.entreprise.id;

        const statutChoisi = statutNumeroAppelSelect ? statutNumeroAppelSelect.value : null;
        if (!statutChoisi) { showFeedback('الرجاء تحديد حالة للرقم.', 'warning'); return; }

        this.disabled = true; this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> حفظ...';

        if (phoneType === 'contact' && (!phoneIdToUpdate || phoneIdToUpdate === 'null' || phoneIdToUpdate === '') && contactIdForCreation && entrepriseIdForCall) {
            console.log(`📞 [SaveStatus] Tentative de get-or-create pour contact #${contactIdForCreation}, numéro ${numeroAAenregistrer}, entreprise #${entrepriseIdForCall}`);
            try {
                const gocResponse = await fetch('{{ route("telephones.getOrCreateForContact") }}', { 
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ entreprise_id: entrepriseIdForCall, contact_id: contactIdForCreation, numero: numeroAAenregistrer })
                });
                const gocData = await gocResponse.json();
                if (gocResponse.ok && gocData.success && gocData.telephone_entreprise_id) {
                    phoneIdToUpdate = gocData.telephone_entreprise_id;
                    activeListItem.setAttribute('data-phone-id', phoneIdToUpdate);
                    const etatVerificationLu = gocData.etat_verification || 'non_verifie';
                    let badge = activeListItem.querySelector('.numero-badge-etat:not(.badge-info):not(.badge-secondary)');
                    if (!badge && activeListItem.querySelector('.badge-secondary')) {
                        let typeBadge = activeListItem.querySelector('.badge-secondary');
                        badge = document.createElement('span');
                        typeBadge.parentNode.insertBefore(badge, typeBadge.nextSibling);
                        if (typeBadge.nextSibling) typeBadge.parentNode.insertBefore(document.createTextNode(" "), typeBadge.nextSibling);
                    }
                    if (badge) {
                        let etatBadgeClass = 'badge-light'; let etatText = etatVerificationLu;
                        if (etatVerificationLu === 'valide') { etatBadgeClass = 'badge-success'; etatText = 'صالح'; }
                        else if (etatVerificationLu === 'faux_numero') { etatBadgeClass = 'badge-danger'; etatText = 'رقم خاطئ'; }
                        else if (etatVerificationLu === 'pas_programme') { etatBadgeClass = 'badge-warning'; etatText = 'لا يرد'; }
                        else if (etatVerificationLu === 'non_verifie') { etatBadgeClass = 'badge-secondary'; etatText = 'لم يتم التحقق منه'; }
                        else { etatText = etatVerificationLu; }
                        badge.className = `badge ${etatBadgeClass} numero-badge-etat`;
                        badge.setAttribute('data-current-status', etatVerificationLu);
                        badge.textContent = etatText;
                    }
                } else { 
                    showFeedback(gocData.message || 'Erreur lors de la création du numéro.', 'danger'); 
                    this.disabled = false; 
                    this.innerHTML = '<i class="typcn typcn-bookmark"></i> حفظ حالة الرقم فقط'; 
                    return; 
                }
            } catch (error) { 
                console.error('Erreur AJAX (getOrCreate):', error); 
                showFeedback('Erreur de connexion lors de la création du numéro.', 'danger'); 
                this.disabled = false; 
                this.innerHTML = '<i class="typcn typcn-bookmark"></i> حفظ حالة الرقم فقط'; 
                return; 
            }
        }

        if (!phoneIdToUpdate || phoneIdToUpdate === 'null' || phoneIdToUpdate === '') { 
            showFeedback('Erreur : ID du téléphone manquant.', 'danger'); 
            this.disabled = false; 
            this.innerHTML = '<i class="typcn typcn-bookmark"></i> حفظ حالة الرقم فقط'; 
            return; 
        }

        try {
            const updateStatusUrl = `{{ url('/telephones') }}/${phoneIdToUpdate}/update-status`;
            const response = await fetch(updateStatusUrl, { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ statut_numero: statutChoisi })
            });
            const data = await response.json();
            if (response.ok && data.success) {
                showFeedback(data.message || 'تم حفظ حالة الرقم بنجاح!');
                let etatBadgeClass = 'badge-light'; let etatText = statutChoisi;
                if (statutChoisi === 'valide') { etatBadgeClass = 'badge-success'; etatText = 'صالح'; }
                else if (statutChoisi === 'faux_numero') { etatBadgeClass = 'badge-danger'; etatText = 'رقم خاطئ'; }
                else if (statutChoisi === 'pas_programme') { etatBadgeClass = 'badge-warning'; etatText = 'لا يرد'; }
                else if (statutChoisi === 'non_verifie') { etatBadgeClass = 'badge-secondary'; etatText = 'لم يتم التحقق منه'; }
                else { etatText = statutChoisi; }

                let statusBadge = activeListItem.querySelector('.numero-badge-etat:not(.badge-info):not(.badge-secondary)');
                if (statusBadge) {
                    statusBadge.className = `badge ${etatBadgeClass} numero-badge-etat`;
                    statusBadge.setAttribute('data-current-status', statutChoisi);
                    statusBadge.textContent = etatText;
                }
                if (typeof $ !== 'undefined' && $(selectNumeroModal).modal) $(selectNumeroModal).modal('hide');
                // Rafraîchir la page après la mise à jour réussie
                window.location.reload();
            } else { 
                showFeedback(data.message || 'لم يتم حفظ حالة الرقم.', 'danger'); 
            }
        } catch (error) { 
            console.error('Erreur AJAX (updateStatus):', error); 
            showFeedback('Erreur de connexion lors de la mise à jour du statut.', 'danger'); 
        } finally { 
            this.disabled = false; 
            this.innerHTML = '<i class="typcn typcn-bookmark"></i> حفظ حالة الرقم فقط'; 
        }
    });
}
        if (btnConfirmerNumeroEtAppeler) {
            btnConfirmerNumeroEtAppeler.addEventListener('click', async function() {
                // ... (Logique complète de btnConfirmerNumeroEtAppeler comme dans ma réponse précédente détaillée - Turn 10)
                // S'assurer qu'il utilise echantillonDataForModal.entreprise.id et echantillonDataForModal.echantillon_id
                const activeListItem = document.querySelector('#listeNumerosContainer .list-group-item-action.active');
                if (!activeListItem) { showFeedback('الرجاء تحديد رقم للاتصال به.', 'warning'); return; }

                let numeroAAppeler = activeListItem.getAttribute('data-numero');
                let telephoneIdPourAppel = activeListItem.getAttribute('data-phone-id');
                const contactIdPourAppel = activeListItem.getAttribute('data-contact-id');
                const phoneTypePourAppel = activeListItem.getAttribute('data-phone-type');

                if (!echantillonDataForModal || !echantillonDataForModal.entreprise || !echantillonDataForModal.entreprise.id || !echantillonDataForModal.echantillon_id) {
                    showFeedback('خطأ: بيانات العينة أو المؤسسة مفقودة.', 'danger'); return;
                }
                const entrepriseIdForCall = echantillonDataForModal.entreprise.id;
                const echantillonIdForCall = echantillonDataForModal.echantillon_id;

                const statutNumeroSelectionne = statutNumeroAppelSelect ? statutNumeroAppelSelect.value : 'valide';

                if (statutNumeroSelectionne === 'valide') {
                    if (phoneTypePourAppel === 'contact' && (!telephoneIdPourAppel || telephoneIdPourAppel === 'null' || telephoneIdPourAppel === '') && contactIdPourAppel && entrepriseIdForCall) {
                        console.log(`📞 [CallNum] Préparation (getOrCreate) contact #${contactIdPourAppel}, num ${numeroAAppeler}`);
                        try {
                            const gocResponse = await fetch('{{ route("telephones.getOrCreateForContact") }}', { 
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                                body: JSON.stringify({ entreprise_id: entrepriseIdForCall, contact_id: contactIdPourAppel, numero: numeroAAppeler })
                            });
                            const gocData = await gocResponse.json();
                            if (gocResponse.ok && gocData.success && gocData.telephone_entreprise_id) {
                                telephoneIdPourAppel = gocData.telephone_entreprise_id;
                                activeListItem.setAttribute('data-phone-id', telephoneIdPourAppel);
                            } else { showFeedback(gocData.message || 'لم نتمكن من إعداد رقم الاتصال للمكالمة.', 'danger'); return; }
                        } catch (error) { showFeedback('خطأ اتصال (إعداد رقم الاتصال للمكالمة).', 'danger'); return; }
                    }
                    
                    if (typeof $ !== 'undefined' && $(selectNumeroModal).modal) $(selectNumeroModal).modal('hide');
                    
                    const btnLancerAppelElem = document.getElementById('btnLancerAppel');
                    if(!btnLancerAppelElem) { console.error("Bouton Lancer Appel principal non trouvé."); return; }
                    btnLancerAppelElem.disabled = true;

                    try {
                        const response = await fetch('{{ route("echantillons.demarrerAppel") }}', { 
                            method: 'POST', 
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }, 
                            body: JSON.stringify({ 
                                echantillon_id: echantillonIdForCall, 
                                telephone_id: (telephoneIdPourAppel && telephoneIdPourAppel !== 'null' && telephoneIdPourAppel !== '') ? telephoneIdPourAppel : null, 
                                numero_appele: numeroAAppeler, 
                                statut_numero: statutNumeroSelectionne 
                            }) 
                        });
                        const data = await response.json();
                        if (response.ok && data.success) {
                            updateCallUI(true, data.appel);
                            if (typeof $ !== 'undefined' && $('#appelScriptModal').modal) $('#appelScriptModal').modal('show');
                            showFeedback(data.message || 'بدأت المكالمة بنجاح!');
                        } else { showFeedback(data.message || 'تعذر بدء المكالمة.', 'danger'); updateCallUI(false); }
                    } catch (error) { console.error("Erreur AJAX (demarrerAppel):", error); showFeedback('خطأ في الاتصال بالخادم (بدء المكالمة).', 'danger'); updateCallUI(false); 
                    } finally { btnLancerAppelElem.disabled = false; }
                } else {
                    showFeedback(`لا يمكن بدء المكالمة. حالة الرقم المختارة هي: '${statutNumeroAppelSelect.options[statutNumeroAppelSelect.selectedIndex].text}'. يرجى حفظ هذه الحالة أو اختيار 'صالح'.`, 'warning');
                }
            });
        }
        
        // Gestion du bouton "Ajouter Suivi" pour ouvrir la modale
const btnOuvrirModalAjoutSuivi = document.getElementById('btnOuvrirModalAjoutSuivi');
if (btnOuvrirModalAjoutSuivi) {
    btnOuvrirModalAjoutSuivi.addEventListener('click', function(e) {
        e.preventDefault();
        console.log('🔵 Clic sur #btnOuvrirModalAjoutSuivi - Ouverture de la modale Ajouter Suivi');
        const formAjouterNouveauSuivi = document.getElementById('formAjouterNouveauSuivi');
        if (formAjouterNouveauSuivi) {
            formAjouterNouveauSuivi.reset(); // Réinitialiser le formulaire
            const causeSuiviInput = document.getElementById('cause_suivi_modal_input');
            if (causeSuiviInput) causeSuiviInput.classList.remove('is-invalid'); // Réinitialiser l'état d'erreur
            const noteSuiviInput = document.getElementById('note_suivi_modal_input');
            if (noteSuiviInput) noteSuiviInput.classList.remove('is-invalid');
        }
        if (typeof $ !== 'undefined' && $('#ajouterSuiviModal').modal) {
            $('#ajouterSuiviModal').modal('show');
        } else {
            console.error('jQuery ou Bootstrap modal non disponible pour #ajouterSuiviModal');
        }
    });
}

// Gestion de la soumission du formulaire de suivi
const btnSubmitNouvelleSuivi = document.getElementById('btnSubmitNouvelleSuivi');
if (btnSubmitNouvelleSuivi) {
    btnSubmitNouvelleSuivi.addEventListener('click', async function(e) {
        e.preventDefault();
        console.log('💾 Clic sur #btnSubmitNouvelleSuivi - Soumission du suivi');

        const form = document.getElementById('formAjouterNouveauSuivi');
        const causeSuiviInput = document.getElementById('cause_suivi_modal_input');
        const noteSuiviInput = document.getElementById('note_suivi_modal_input');
        const causeErrorMsg = document.getElementById('cause_suivi_modal_error_msg');
        const echantillonId = document.getElementById('suivi_echantillon_id_modal_input_js')?.value;

        if (!echantillonId) {
            showFeedback('معرف العينة مفقود. يرجى تحديث الصفحة.', 'danger');
            return;
        }

        // Validation côté client
        if (!causeSuiviInput.value) {
            causeSuiviInput.classList.add('is-invalid');
            causeErrorMsg.textContent = 'يرجى اختيار سبب المتابعة.';
            showFeedback('يرجى اختيار سبب المتابعة.', 'warning');
            return;
        } else {
            causeSuiviInput.classList.remove('is-invalid');
            causeErrorMsg.textContent = '';
        }

        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';

        try {
            const response = await fetch('{{ route("suivis.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    echantillon_enquete_id: echantillonId,
                    cause_suivi: causeSuiviInput.value,
                    note: noteSuiviInput.value
                })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                showFeedback(data.message || 'تم حفظ المتابعة بنجاح!');
                if (typeof $ !== 'undefined' && $('#ajouterSuiviModal').modal) {
                    $('#ajouterSuiviModal').modal('hide');
                }
                form.reset();
                // Optionnel : Recharger la page pour refléter les changements
                window.location.reload();
            } else {
                showFeedback(data.message || 'فشل في حفظ المتابعة.', 'danger');
            }
        } catch (error) {
            console.error('Erreur AJAX (suivi):', error);
            showFeedback('خطأ في الاتصال بالخادم أثناء حفظ المتابعة.', 'danger');
        } finally {
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-save" style="margin-left: 8px;"></i> حفظ المتابعة';
        }
    });
}
        // Vos autres boutons et logiques (btnAjouterRendezVous, btnVoirScript, etc.)
        const btnAjouterRendezVous = document.getElementById('btnAjouterRendezVous');
        // Correction: s'assurer que le formulaire dans la modale est réinitialisé et l'action est correctement définie
        if (btnAjouterRendezVous) {
            btnAjouterRendezVous.addEventListener('click', function (e) {
                e.preventDefault();
                const formRdv = document.getElementById('formAjouterRendezVous'); // C'est l'ID du formulaire DANS la modale
                if (formRdv) {
                    formRdv.reset(); // Réinitialise les champs du formulaire
                    @if(isset($echantillon) && $echantillon && $echantillon->id)
                        // Assure que l'action du formulaire est correcte pour l'échantillon actuel
                        formRdv.action = `{{ route('rendezvous.store', ['id' => $echantillon->id]) }}`;
                    @endif
                }
                // Ouvre la modale
                if (typeof $ !== 'undefined' && $('#rendezVousModal').modal) {
                    $('#rendezVousModal').modal('show');
                }
            });
        }
        
        
        // const btnSubmitRendezVous = document.getElementById('btnSubmitRendezVous'); // Ce bouton est DANS la modale
        // Sa logique de soumission est gérée par le type="submit" du formulaire.
        // Si vous avez besoin d'une soumission AJAX, le code irait ici, attaché à l'événement de soumission du formulaire.

        const btnVoirScript = document.getElementById('btnVoirScript');
        if (btnVoirScript) { btnVoirScript.addEventListener('click', function (e) { e.preventDefault(); if (typeof $ !== 'undefined' && $('#appelScriptModal').modal) $('#appelScriptModal').modal('show'); }); }
         // ===================================================================
        // == DÉBUT : BLOC À AJOUTER POUR LE BOUTON QUESTIONNAIRE ==
        // ===================================================================

        // On cible le bouton du questionnaire par son ID
        const btnVoirQuestionnaire = document.getElementById('btnVoirQuestionnaire');

        // CETTE VÉRIFICATION EST CRUCIALE !
        // Elle s'assure que le code ne s'exécute que si le bouton existe dans la page
        // (c'est-à-dire quand un échantillon est chargé).
        // Cela empêche toute erreur JavaScript de bloquer les autres scripts.
        if (btnVoirQuestionnaire) {
            
            btnVoirQuestionnaire.addEventListener('click', function(e) {
                e.preventDefault(); // On empêche le comportement par défaut

                // On récupère les informations stockées dans les attributs data-* du bouton
                const idEchantillon = this.dataset.idEchantillon;
                const codeNational = this.dataset.codeNational;
                const idUtilisateur = this.dataset.idUtilisateur;
                const raisonSociale = this.dataset.raisonSociale;

                // On vérifie que les données essentielles sont bien là
                if (!idEchantillon || !idUtilisateur) {
                    showFeedback('Données manquantes pour ouvrir le questionnaire. Veuillez actualiser.', 'danger');
                    console.error('Données manquantes pour le questionnaire:', this.dataset);
                    return; // On arrête l'exécution si les données manquent
                }

                // On construit l'URL de destination
                const baseUrl = 'http://172.31.5.128/saisie_enquete/emploi_entreprise/mon-api/api.php';
                
                // On utilise URLSearchParams pour construire les paramètres de manière sécurisée
                // (cela gère automatiquement les espaces ou caractères spéciaux dans la raison sociale, par exemple)
                const params = new URLSearchParams({
                    id_echantillon: idEchantillon,
                    code_nationale: codeNational || '', // On met une chaîne vide si c'est null
                    id: idUtilisateur,
                    rs: raisonSociale || '' // On met une chaîne vide si c'est null
                });

                // On assemble l'URL finale
                const finalUrl = `${baseUrl}?${params.toString()}`;

                console.log("URL du questionnaire générée :", finalUrl); // Pour le débogage

                // On ouvre le lien dans un nouvel onglet
                window.open(finalUrl, '_blank', 'noopener,noreferrer');
            });
        }
        // ===================================================================
        // == FIN : BLOC POUR LE BOUTON QUESTIONNAIRE ==
        // ===================================================================

        
        const btnRelance = document.getElementById('btnRelance');
        if (btnRelance) {
    btnRelance.addEventListener('click', async function (e) {
        e.preventDefault();
        const echantillonId = this.getAttribute('data-echantillon-id');
        const notesAppelTextarea = document.getElementById('notesAppel');
        const commentaire = notesAppelTextarea ? notesAppelTextarea.value : '';
        const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
        const causeSuiviModal = document.getElementById('causeSuiviModal');

        if (!echantillonId) {
            showFeedback('Erreur: L\'ID de l\'échantillon est manquant pour la relance.', 'danger');
            return;
        }

        if (typeof $ !== 'undefined' && $(causeSuiviModal).modal) {
            $(causeSuiviModal).modal('show'); // Show the modal instead of the prompt
        } else {
            showFeedback('Erreur: La modal de cause du suivi n\'a pas pu être affichée.', 'warning');
            return;
        }

        const btnConfirmerCauseSuivi = document.getElementById('btnConfirmerCauseSuivi');
        if (btnConfirmerCauseSuivi) {
            btnConfirmerCauseSuivi.onclick = async () => { // Utiliser une fonction fléchée pour conserver le 'this' de btnRelance si besoin, ou le gérer autrement.
                const causeSuiviSelect = document.getElementById('causeSuiviSelect');
                const causeSuivi = causeSuiviSelect ? causeSuiviSelect.value : '';

                if (causeSuivi.trim() === '') {
                    showFeedback('Veuillez sélectionner une cause du suivi.', 'warning');
                    return;
                }

                if (typeof $ !== 'undefined' && $(causeSuiviModal).modal) {
                    $(causeSuiviModal).modal('hide');
                }

                // Gérer l'état du bouton de confirmation de la cause
                btnConfirmerCauseSuivi.disabled = true; // Ou le 'this' de btnRelance si c'est l'intention.
                // Mettre à jour le texte du bouton de confirmation de la cause
                // btnConfirmerCauseSuivi.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';

                try {
                    const response = await fetch('{{ route('suivis.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            echantillon_enquete_id: echantillonId,
                            commentaire: commentaire,
                            cause_suivi: causeSuivi
                        })
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        showFeedback(data.message || '👍 Le suivi avec la cause a été enregistré avec succès !');
                        if (notesAppelTextarea) {
                            notesAppelTextarea.value = '';
                        }
                        if (typeof $ !== 'undefined' && $('#appelScriptModal').modal) {
                            $('#appelScriptModal').modal('hide');
                        }
                    } else {
                        showFeedback(data.message || '❌ Échec de l\'enregistrement du suivi.', 'danger');
                    }
                } catch (error) {
                    console.error('Erreur AJAX (relance) :', error);
                    showFeedback('⚠️ Une erreur s\'est produite lors de la tentative d\'enregistrement du suivi.', 'danger');
                } finally {
                     btnConfirmerCauseSuivi.disabled = false; // Réactiver le bouton
                     // Réinitialiser le texte du bouton
                     // btnConfirmerCauseSuivi.innerHTML = 'Confirmer'; // ou le texte original
                }
            };
        }
        
    });
    
}
        // Logique de beforeunload et navigationElements (si nécessaire)
        // window.addEventListener('beforeunload', function (event) { /* ... Votre code ... */ });
        // const navigationElements = document.querySelectorAll('...'); 
        // navigationElements.forEach(element => { /* ... Votre code ... */ });

        const switchToArabic = document.getElementById('switchToArabic'); 
        const switchToFrench = document.getElementById('switchToFrench');
        const scriptArabe = document.getElementById('scriptArabe');
        const scriptFrancais = document.getElementById('scriptFrancais');

        if (switchToArabic && switchToFrench && scriptArabe && scriptFrancais) {
            switchToArabic.addEventListener('click', function() {
                scriptArabe.style.display = 'block';
                scriptFrancais.style.display = 'none';
                this.classList.add('btn-primary'); this.classList.remove('btn-secondary');
                switchToFrench.classList.add('btn-secondary'); switchToFrench.classList.remove('btn-primary');
            });
            switchToFrench.addEventListener('click', function() {
                scriptArabe.style.display = 'none';
                scriptFrancais.style.display = 'block';
                this.classList.add('btn-primary'); this.classList.remove('btn-secondary');
                switchToArabic.classList.add('btn-secondary'); switchToArabic.classList.remove('btn-primary');
            });
        }
        
        

        // Pour la réouverture de la modale RendezVous en cas d'erreur de validation Laravel
        @if($errors->any() && old('form_modal_submitted') == 'rendezVousModal')
            if (typeof $ !== 'undefined' && $('#rendezVousModal').modal) {
                $('#rendezVousModal').modal('show');
            }
        @endif
        




        

    }); // Fin de DOMContentLoaded
    
</script>
@endsection