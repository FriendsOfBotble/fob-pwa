<?php

if (! function_exists('pwa_get_manifest_url')) {
    /**
     * Get the URL to the PWA manifest file
     */
    function pwa_get_manifest_url(): string
    {
        return asset('pwa/manifest.json');
    }
}

if (! function_exists('pwa_get_service_worker_url')) {
    /**
     * Get the URL to the PWA service worker file
     */
    function pwa_get_service_worker_url(): string
    {
        return asset('service-worker.js');
    }
}

if (! function_exists('pwa_is_enabled')) {
    /**
     * Check if PWA is enabled
     */
    function pwa_is_enabled(): bool
    {
        return is_plugin_active('fob-pwa') && setting('pwa_enable', false);
    }
}
