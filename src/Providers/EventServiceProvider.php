<?php

namespace FriendsOfBotble\Pwa\Providers;

use Botble\Base\Events\SystemUpdatePublished;
use Botble\PluginManagement\Events\ActivatedPluginEvent;
use FriendsOfBotble\Pwa\Listeners\PublishPwaAssets;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SystemUpdatePublished::class => [
            PublishPwaAssets::class,
        ],
        ActivatedPluginEvent::class => [
            PublishPwaAssets::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();

        // Listen for cache:cleared event to regenerate service worker with new cache version
        $this->app['events']->listen(['cache:cleared'], function (): void {
            if (! pwa_is_enabled()) {
                return;
            }

            // Regenerate service worker with new cache name based on CMS version
            if (pwa_clear_cache()) {
                logger()->info('PWA: Service worker regenerated after cache cleared with version: ' . pwa_get_cache_version());
            } else {
                logger()->error('PWA: Failed to regenerate service worker after cache cleared');
            }
        });
    }
}
