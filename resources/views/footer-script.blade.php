<script>
    // Register Service Worker for PWA functionality
    if ("serviceWorker" in navigator) {
        window.addEventListener("load", function() {
            navigator.serviceWorker.register("/service-worker.js")
                .then(function(registration) {
                    console.log("PWA Service Worker registered with scope:", registration.scope);
                })
                .catch(function(error) {
                    console.log("PWA Service Worker registration failed:", error);
                });
        });
    }
</script>
