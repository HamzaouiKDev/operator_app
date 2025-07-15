<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar sidebar-scroll">
    <div class="main-sidebar-header active">
        {{-- Liens du logo pointant vers la racine --}}
        <a class="desktop-logo logo-light active" href="{{ url('/') }}"><img src="{{URL::asset('assets/img/brand/ins.svg')}}" class="main-logo" alt="logo"></a>
        <a class="desktop-logo logo-dark active" href="{{ url('/') }}"><img src="{{URL::asset('assets/img/brand/ins.svg')}}" class="main-logo dark-theme" alt="logo"></a>
        <a class="logo-icon mobile-logo icon-light active" href="{{ url('/') }}"><img src="{{URL::asset('assets/img/brand/ins.svg')}}" class="logo-icon" alt="logo"></a>
        <a class="logo-icon mobile-logo icon-dark active" href="{{ url('/') }}"><img src="{{URL::asset('assets/img/brand/ins.svg')}}" class="logo-icon dark-theme" alt="logo"></a>
    </div>
    <div class="main-sidemenu">
        <div class="app-sidebar__user clearfix">
            <div class="dropdown user-pro-body">
                <div class="">
                    <img alt="user-img" class="avatar avatar-xl brround" src="{{URL::asset('assets/img/faces/user.png')}}"><span class="avatar-status profile-status bg-green"></span>
                </div>
                <div class="user-info">
                    <h4 class="font-weight-semibold mt-3 mb-0">{{ Auth::user()->name }}</h4>
                    {{-- CORRIGÉ : Affiche le premier rôle de manière plus propre --}}
                    <span class="mb-0 text-muted">{{ Auth::user()->roles->first()->name ?? 'Pas de rôle' }}</span>
                </div>
            </div>
        </div>

        <ul class="side-menu">
            
            {{-- ============================================= --}}
            {{-- == SECTION ADMINISTRATEUR == --}}
            {{-- ============================================= --}}
            @role('Admin')
                <li class="side-item side-item-category">الإدارة</li>
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('admin.dashboard') }}">
                        <i class="side-menu__icon fas fa-tachometer-alt"></i><span class="side-menu__label">لوحة التحكم</span>
                    </a>
                </li>
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('admin.users.index') }}">
                        <i class="side-menu__icon fas fa-users-cog"></i><span class="side-menu__label">إدارة المستخدمين</span>
                    </a>
                </li>
                <li class="slide">
                    {{-- CORRIGÉ : Traduction du label --}}
                    <a class="side-menu__item" href="{{ route('admin.entreprises.import.form') }}">
                        <i class="side-menu__icon fas fa-file-import"></i><span class="side-menu__label">استيراد الشركات</span>
                    </a>
                </li>
                {{-- CORRECTION : Mise en forme et traduction du lien --}}
            <li class="slide">
                <a class="side-menu__item" href="{{ route('admin.enquetes.index') }}">
                    <i class="side-menu__icon fas fa-poll"></i><span class="side-menu__label">إدارة المسوح</span>
                </a>
            </li>
             <li class="slide">
                <a class="side-menu__item" href="{{ route('admin.echantillons.import.form') }}">
                    <i class="side-menu__icon fas fa-users"></i><span class="side-menu__label">إدارة العينات</span>
                </a>
            </li>
            @endrole
            

            {{-- ============================================= --}}
            {{-- == SECTION SUPERVISEUR (Prêt pour le futur) == --}}
            {{-- ============================================= --}}
            @role('Superviseur')
                <li class="side-item side-item-category">الإشراف</li>
                {{-- Ajoutez ici les futurs liens pour le superviseur --}}
                <li class="slide">
                    <a class="side-menu__item" href="#"> {{-- Mettre la bonne route --}}
                        <i class="side-menu__icon fas fa-eye"></i><span class="side-menu__label">مراقبة الفريق</span>
                    </a>
                </li>
            @endrole


          {{-- ============================================= --}}
{{-- == SECTION TÉLÉOPÉRATEUR == --}}
{{-- ============================================= --}}
@role('Téléopérateur')
    <li class="side-item side-item-category">برنامج ادارة مركز نداء</li>
    <li class="slide">
        <a class="side-menu__item" href="{{ route('home') }}">
            <i class="side-menu__icon fas fa-home"></i><span class="side-menu__label">الرئيسية</span>
        </a>
    </li>

    {{-- Menu déroulant pour les rendez-vous --}}
    <li class="slide">
        <a class="side-menu__item" data-toggle="slide" href="#">
            <i class="side-menu__icon fas fa-calendar-alt"></i><span class="side-menu__label">المواعيد</span><i class="angle fe fe-chevron-down"></i>
        </a>
        <ul class="slide-menu">
            <li><a class="slide-item" href="{{ route('rendezvous.aujourdhui') }}">مواعيد اليوم</a></li>
            <li><a class="slide-item" href="{{ route('rendezvous.index') }}">قائمة كل المواعيد</a></li>
        </ul>
    </li>

    {{-- ✅ DÉBUT DE LA MODIFICATION : Liens directs pour les échantillons --}}
    <li class="slide">
        <a class="side-menu__item" href="{{ route('echantillons.partiels') }}">
            <i class="side-menu__icon fas fa-edit"></i><span class="side-menu__label">العينات الجزئية</span>
        </a>
    </li>
    <li class="slide">
        <a class="side-menu__item" href="{{ route('echantillons.en_attente') }}">
            <i class="side-menu__icon typcn typcn-time"></i><span class="side-menu__label">قائمة العينات في الانتظار</span>
        </a>
    </li>
    {{-- ✅ FIN DE LA MODIFICATION --}}

    <li class="slide">
        <a class="side-menu__item" href="{{ route('suivis.index') }}">
            <i class="side-menu__icon fas fa-history"></i><span class="side-menu__label">قائمة المتابعات</span>
        </a>
    </li>
    
    <li class="slide">
        <a class="side-menu__item" href="{{ route('statistiques.index') }}">
            <i class="side-menu__icon fas fa-chart-bar"></i><span class="side-menu__label">التقارير</span>
        </a>
    </li>
@endrole

        </ul>
    </div>
</aside>