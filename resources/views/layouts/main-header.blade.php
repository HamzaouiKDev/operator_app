<div class="main-header sticky side-header nav nav-item">
    <div class="container-fluid">
        <div class="main-header-left ">
            <div class="responsive-logo">
                <a href="{{ url('/' . ($page = 'index')) }}"><img src="{{ URL::asset('assets/img/brand/logo.png') }}" class="logo-1" alt="logo"></a>
                <a href="{{ url('/' . ($page = 'index')) }}"><img src="{{ URL::asset('assets/img/brand/logo-white.png') }}" class="dark-logo-1" alt="logo"></a>
                <a href="{{ url('/' . ($page = 'index')) }}"><img src="{{ URL::asset('assets/img/brand/favicon.png') }}" class="logo-2" alt="logo"></a>
                <a href="{{ url('/' . ($page = 'index')) }}"><img src="{{ URL::asset('assets/img/brand/favicon.png') }}" class="dark-logo-2" alt="logo"></a>
            </div>
            <div class="app-sidebar__toggle" data-toggle="sidebar">
                <a class="open-toggle" href="#"><i class="header-icon fe fe-align-left"></i></a>
                <a class="close-toggle" href="#"><i class="header-icons fe fe-x"></i></a>
            </div>
            <div class="main-header-center mr-3 d-sm-none d-md-none d-lg-block">
                <input class="form-control" placeholder="Search for anything..." type="search"> <button class="btn"><i class="fas fa-search d-none d-md-block"></i></button>
            </div>
        </div>
        <div class="main-header-right">
            <div class="nav nav-item navbar-nav-right ml-auto">
                <div class="dropdown nav-item main-header-notification">
                    <a class="new nav-link" href="#" data-toggle="dropdown">
                        <svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                        <span class="pulse" id="notification-pulse" style="display: none;"></span>
                    </a>
                    <div class="dropdown-menu">
                        <div class="menu-header-content bg-primary text-right">
                            <div class="d-flex">
                                <h6 class="dropdown-title mb-1 tx-15 text-white font-weight-semibold">Notifications</h6>
                                <span class="badge badge-pill badge-warning mr-auto my-auto float-left" id="notification-count">0 notifications</span>
                            </div>
                            <p class="dropdown-title-text subtext mb-0 text-white op-6 pb-0 tx-12" id="notification-summary">Vous n'avez pas de nouvelles notifications.</p>
                        </div>
                        
                        <div class="main-notification-list" id="notification-list-container">
                            <div class="text-center p-3">
                                <p class="text-muted">Aucun rendez-vous imminent.</p>
                            </div>
                        </div>

                        <div class="dropdown-footer">
                            <a href="{{ route('rendezvous.index') }}">VOIR TOUS LES RENDEZ-VOUS</a>
                        </div>
                    </div>
                </div>
                <div class="nav-item full-screen fullscreen-button">
                    <a class="new nav-link full-screen-link" href="#"><svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-maximize"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path></svg></a>
                </div>
                <div class="dropdown main-profile-menu nav nav-item nav-link">
                    <a class="profile-user d-flex" href=""><img alt="" src="{{ URL::asset('assets/img/faces/6.jpg') }}"></a>
                    <div class="dropdown-menu">
                        <div class="main-header-profile bg-primary p-3">
                            <div class="d-flex wd-100p">
                                <div class="main-img-user"><img alt="" src="{{ URL::asset('assets/img/faces/6.jpg') }}" class=""></div>
                                <div class="mr-3 my-auto">
                                    <h6>{{ Auth::user()->name }}</h6><span>{{ Auth::user()->email }}</span>
                                </div>
                            </div>
                        </div>
                        <a class="dropdown-item" href=""><i class="bx bx-user-circle"></i>Profile</a>
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="bx bx-log-out"></i>Déconnexion</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{--@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- 1. SÉLECTION DES ÉLÉMENTS DU DOM ---
    const notificationListContainer = document.getElementById('notification-list-container');
    const notificationCountElement = document.getElementById('notification-count');
    const notificationSummaryElement = document.getElementById('notification-summary');
    const notificationPulse = document.getElementById('notification-pulse');

    if (!notificationListContainer || !notificationCountElement || !notificationSummaryElement || !notificationPulse) {
        console.error("Erreur critique : Un ou plusieurs éléments de l'interface des notifications sont introuvables.");
        return;
    }

    // --- 2. FONCTION DE MISE À JOUR DE L'INTERFACE ---
   function updateNotificationsUI(notifications, count) {
    // ===================================================================
    // LIGNE DE DÉBOGAGE LA PLUS IMPORTANTE
    // Ce bloc va nous dire la vérité sur les données reçues par la fonction.
    // ===================================================================
    console.log("%c--- DÉBUT DE updateNotificationsUI ---", "color: blue; font-weight: bold; font-size: 14px;");
    console.log("Argument 'count' reçu -> Valeur:", count, "| Type:", typeof count);
    console.log("Argument 'notifications' reçu -> Est-ce un tableau?", Array.isArray(notifications));
    if (Array.isArray(notifications)) {
        console.log("Nombre d'éléments dans 'notifications':", notifications.length);
        console.log("Contenu de 'notifications':", JSON.parse(JSON.stringify(notifications))); // Affiche une copie propre de l'objet
    } else {
        console.log("Contenu de 'notifications':", notifications);
    }
    console.log("Résultat du test (count > 0):", count > 0);
    console.log("%c------------------------------------", "color: blue; font-weight: bold; font-size: 14px;");
    // ===================================================================

    notificationCountElement.textContent = `${count} notifications`;
    if (count > 0) {
        notificationSummaryElement.textContent = `Vous avez ${count} notifications non lues.`;
        notificationPulse.style.display = 'inline-block';
    } else {
        notificationSummaryElement.textContent = "Vous n'avez pas de notifications de rendez-vous imminents.";
        notificationPulse.style.display = 'none';
    }

    notificationListContainer.innerHTML = '';

    if (count > 0 && notifications && notifications.length > 0) {
        notifications.forEach(notification => {
            const notificationHtml = `
                <a class="d-flex p-3 border-bottom" href="${notification.link}">
                    <div class="notifyimg ${notification.bg_class || 'bg-info-light'}">
                        <i class="${notification.icon_class || 'las la-bell'}"></i>
                    </div>
                    <div class="mr-3">
                        <h5 class="notification-label mb-1">${notification.title}</h5>
                        <div class="notification-subtext">${notification.time_left}</div>
                    </div>
                    <div class="mr-auto">
                        <i class="las la-angle-left text-left text-muted"></i>
                    </div>
                </a>
            `;
            notificationListContainer.insertAdjacentHTML('beforeend', notificationHtml);
        });
    } else {
        const noNotificationHtml = `
            <div class="text-center p-3">
                <p class="text-muted">Aucun rendez-vous imminent.</p>
            </div>
        `;
        notificationListContainer.innerHTML = noNotificationHtml;
    }
    
    if (typeof PerfectScrollbar !== 'undefined') {
         new PerfectScrollbar(notificationListContainer, {
            suppressScrollX: true
         });
    }
}
    
    // --- 3. CONNEXION AUX SERVER-SENT EVENTS (SSE) ---
    if (typeof(EventSource) !== "undefined") {
        const eventSource = new EventSource('{{ route("notifications.stream") }}');

        eventSource.onopen = function() {
            console.log("SSE: Connexion au flux de notifications établie.");
        };

        eventSource.addEventListener('new_rendezvous_notification', function(event) {
            console.log("SSE: Nouvelles données reçues:", event.data);
            try {
                const data = JSON.parse(event.data);
                updateNotificationsUI(data.notifications, data.count);
            } catch (e) {
                console.error("SSE: Erreur lors du parsing des données JSON.", e, event.data);
            }
        });

        eventSource.onerror = function(err) {
            console.error("SSE: Erreur de connexion au flux.", err);
        };

    } else {
        console.warn("Votre navigateur ne supporte pas les Server-Sent Events.");
    }
});
</script>
@endpush--}}