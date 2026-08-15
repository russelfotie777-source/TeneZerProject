(function () {
    var DISMISS_KEY = 'tenezer_pwa_dismissed_at';
    var DISMISS_DAYS = 7;
    var deferredPrompt = null;

    function isStandalone() {
        return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    }

    function isIos() {
        return /iphone|ipad|ipod/i.test(window.navigator.userAgent);
    }

    function wasDismissedRecently() {
        var raw = localStorage.getItem(DISMISS_KEY);
        if (!raw) return false;
        var elapsedDays = (Date.now() - parseInt(raw, 10)) / (1000 * 60 * 60 * 24);
        return elapsedDays < DISMISS_DAYS;
    }

    function dismiss() {
        localStorage.setItem(DISMISS_KEY, String(Date.now()));
        var banner = document.getElementById('pwa-install-banner');
        if (banner) banner.remove();
    }

    function buildBanner(onInstallClick) {
        var wrap = document.createElement('div');
        wrap.className = 'pwa-banner-install';
        wrap.id = 'pwa-install-banner';
        wrap.innerHTML =
            '<div class="pwa-banner-card">' +
                '<button class="pwa-banner-close" aria-label="Fermer">&times;</button>' +
                '<div class="pwa-banner-icon-wrapper">' +
                    '<div class="pwa-banner-icon-pulse"></div>' +
                    '<img class="pwa-banner-icon" src="includes/icon-96.png" alt="Tenezer">' +
                '</div>' +
                '<div class="pwa-banner-title">Installez <span>Tenezer</span></div>' +
                '<div class="pwa-banner-description">Accédez plus vite aux biens, terrains et services Tenezer, même hors connexion.</div>' +
                '<div class="pwa-banner-buttons">' +
                    '<button class="pwa-banner-btn pwa-banner-btn-secondary" id="pwa-later-btn">Plus tard</button>' +
                    '<button class="pwa-banner-btn pwa-banner-btn-primary" id="pwa-install-btn">Installer</button>' +
                '</div>' +
            '</div>';

        document.body.appendChild(wrap);
        wrap.querySelector('.pwa-banner-close').addEventListener('click', dismiss);
        document.getElementById('pwa-later-btn').addEventListener('click', dismiss);
        document.getElementById('pwa-install-btn').addEventListener('click', onInstallClick);
    }

    function buildIosModal() {
        var modal = document.createElement('div');
        modal.className = 'pwa-install-modal';
        modal.id = 'pwa-ios-modal';
        modal.innerHTML =
            '<div class="pwa-install-modal-overlay"></div>' +
            '<div class="pwa-install-modal-content">' +
                '<div class="pwa-install-modal-header">' +
                    '<button class="pwa-install-modal-close" aria-label="Fermer">&times;</button>' +
                    '<div class="pwa-install-modal-icon"><img src="includes/icon-96.png" alt="Tenezer"></div>' +
                    '<h3>Installer Tenezer</h3>' +
                '</div>' +
                '<div class="pwa-install-modal-body">' +
                    '<p>Sur iPhone / iPad, l\'installation se fait depuis Safari en 2 étapes :</p>' +
                    '<div class="pwa-modal-step">' +
                        '<div class="pwa-modal-step-icon">1</div>' +
                        '<div class="pwa-modal-step-content"><strong>Appuyez sur Partager</strong><span>L\'icône carrée avec une flèche vers le haut, en bas de l\'écran.</span></div>' +
                    '</div>' +
                    '<div class="pwa-modal-step">' +
                        '<div class="pwa-modal-step-icon">2</div>' +
                        '<div class="pwa-modal-step-content"><strong>Sur l\'écran d\'accueil</strong><span>Faites défiler puis appuyez sur "Sur l\'écran d\'accueil".</span></div>' +
                    '</div>' +
                    '<div class="pwa-install-modal-tip">Astuce : l\'application apparaîtra ensuite comme une app normale sur votre écran d\'accueil.</div>' +
                '</div>' +
                '<div class="pwa-install-modal-footer">' +
                    '<button class="pwa-modal-btn-primary" id="pwa-ios-ok-btn">J\'ai compris</button>' +
                '</div>' +
            '</div>';

        document.body.appendChild(modal);
        var close = function () { modal.classList.remove('visible'); setTimeout(function () { modal.remove(); }, 300); };
        modal.querySelector('.pwa-install-modal-close').addEventListener('click', close);
        modal.querySelector('.pwa-install-modal-overlay').addEventListener('click', close);
        document.getElementById('pwa-ios-ok-btn').addEventListener('click', close);
        requestAnimationFrame(function () { modal.classList.add('visible'); });
    }

    function initInstallBanner() {
        if (isStandalone() || wasDismissedRecently()) return;

        if (isIos()) {
            buildBanner(function () { dismiss(); buildIosModal(); });
            return;
        }

        window.addEventListener('beforeinstallprompt', function (e) {
            e.preventDefault();
            deferredPrompt = e;
            buildBanner(function () {
                dismiss();
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    deferredPrompt = null;
                }
            });
        });
    }

    function registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('sw.js').catch(function () {
                    // Échec silencieux : la navigation normale continue de fonctionner sans SW
                });
            });
        }
    }

    document.addEventListener('DOMContentLoaded', initInstallBanner);
    registerServiceWorker();
})();
