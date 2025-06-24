<?php

use Illuminate\Support\Facades\File;

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

if (! function_exists('pwa_get_cache_version')) {
    /**
     * Get the current PWA cache version based on CMS version
     */
    function pwa_get_cache_version(): string
    {
        return 'pwa-cache-v' . get_cms_version();
    }
}

if (! function_exists('pwa_clear_cache')) {
    /**
     * Clear PWA cache by regenerating service worker with new cache version
     */
    function pwa_clear_cache(): bool
    {
        if (! pwa_is_enabled()) {
            return false;
        }

        try {
            $sourceFile = plugin_path('fob-pwa/public/service-worker.js');
            $destinationFile = public_path('service-worker.js');

            if (! File::exists($sourceFile)) {
                return false;
            }

            $content = File::get($sourceFile);

            // Replace the cache name with a dynamic one based on CMS version
            $newCacheName = pwa_get_cache_version();

            // Use regex to replace any existing cache name pattern
            $content = preg_replace(
                "/const CACHE_NAME = '[^']*';/",
                "const CACHE_NAME = '{$newCacheName}';",
                $content
            );

            File::put($destinationFile, $content);

            return true;
        } catch (Exception) {
            return false;
        }
    }
}
