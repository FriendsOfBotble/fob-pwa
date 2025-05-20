<script>
    window.pwaConfig = {
        isCacheIgnored: false
    };

    if ("serviceWorker" in navigator) {
        window.addEventListener("load", function() {
            navigator.serviceWorker.register("/service-worker.js")
                .then(function(registration) {
                    if (registration.active) {
                        registration.active.postMessage({
                            type: 'CACHE_STATUS',
                            isCacheIgnored: window.pwaConfig.isCacheIgnored
                        });
                    }
                });
        });
    }

    let deferredPrompt;
    const installButton = document.getElementById('pwa-install-button');
    const installPrompt = document.getElementById('pwa-install-prompt');
    const showInstallPrompt = {{ setting('pwa_show_install_prompt', true) ? 'true' : 'false' }};

    if (showInstallPrompt) {
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;

            if (installButton) {
                installButton.style.display = 'block';

                installButton.addEventListener('click', () => {
                    if (installPrompt) {
                        installPrompt.style.display = 'none';
                    }
                    deferredPrompt.prompt();
                    deferredPrompt.userChoice.then(() => {
                        deferredPrompt = null;
                    });
                });
            }

            if (installPrompt) {
                installPrompt.style.display = 'block';
            }
        });

        window.addEventListener('appinstalled', () => {
            if (installButton) {
                installButton.style.display = 'none';
            }
            if (installPrompt) {
                installPrompt.style.display = 'none';
            }
            deferredPrompt = null;
        });
    }

    const enableNotifications = {{ setting('pwa_enable_notifications', false) ? 'true' : 'false' }};

    if (enableNotifications && "Notification" in window) {
        if (Notification.permission === "granted") {

        } else if (Notification.permission !== "denied") {
            Notification.requestPermission();
        }
    }
</script>

<div id="pwa-install-prompt" style="display: none; position: fixed; bottom: 20px; left: 20px; background: #fff; padding: 15px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.2); z-index: 9999; max-width: 300px;">
    <div style="display: flex; align-items: center; margin-bottom: 10px;">
        <img src="{{ RvMedia::getImageUrl(setting('pwa_icon', theme_option('logo')), 'thumb', false, RvMedia::getDefaultImage()) }}" alt="App Icon" style="width: 40px; height: 40px; margin-right: 10px; border-radius: 8px;">
        <div>
            <div style="font-weight: bold;">{{ setting('pwa_app_name', setting('site_title', 'Progressive Web App')) }}</div>
            <div style="font-size: 12px; color: #666;">Install this app on your device</div>
        </div>
    </div>
    <button id="pwa-install-button" style="background: #0989ff; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; width: 100%;">Install App</button>
    <button onclick="this.parentNode.style.display='none';" style="background: none; border: none; color: #666; margin-top: 8px; cursor: pointer; width: 100%; text-align: center; font-size: 12px;">Not now</button>
</div>
