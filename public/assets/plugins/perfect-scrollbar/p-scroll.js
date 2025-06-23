// Fichier : p-scroll.js (VERSION FINALE CORRIGÉE)

(function($) {
    "use strict";

    // P-scrolling - VERSION CORRIGÉE ET ROBUSTE

    // Initialisation pour la liste de discussion (chat)
    const chatElement = document.querySelector('.chat-scroll');
    if (chatElement) {
        // SI l'élément .chat-scroll EXISTE, ALORS on initialise la scrollbar.
        const ps2 = new PerfectScrollbar(chatElement, {
            useBothWheelAxes: true,
            suppressScrollX: true,
        });
    }

    // Initialisation pour la liste de notifications (c'est ce bloc qui causait l'erreur)
    const notificationElement = document.querySelector('.Notification-scroll');
    if (notificationElement) {
        // SI l'élément .Notification-scroll EXISTE, ALORS on initialise la scrollbar.
        // Puisque nous avons supprimé cette classe de notre header, ce code sera simplement ignoré sur cette page, sans causer d'erreur.
        // Si une autre page de votre site utilise cette classe, elle continuera de fonctionner sans problème.
        const ps3 = new PerfectScrollbar(notificationElement, {
            useBothWheelAxes: true,
            suppressScrollX: true,
        });
    }

})(jQuery);