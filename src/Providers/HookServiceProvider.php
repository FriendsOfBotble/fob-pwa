<?php

namespace FriendsOfBotble\Pwa\Providers;

use Botble\Base\Facades\AdminHelper;
use Botble\Base\Supports\ServiceProvider;
use Illuminate\Support\Facades\View;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! is_plugin_active('fob-pwa')) {
            return;
        }

        $enabled = setting('pwa_enable', false);
        if (! $enabled) {
            return;
        }

        // Don't load PWA in admin panel
        if (AdminHelper::isInAdmin()) {
            return;
        }

        add_filter(THEME_FRONT_HEADER, function ($html) {
            $themeColor = setting('pwa_theme_color', '#0989ff');
            $appName = setting('pwa_app_name', setting('site_title', 'Progressive Web App'));

            $metaTags = View::make('plugins/fob-pwa::header-meta', compact('themeColor', 'appName'))->render();

            return $html . $metaTags;
        }, 15);

        add_filter(THEME_FRONT_FOOTER, function ($html) {
            $script = View::make('plugins/fob-pwa::footer-script')->render();

            return $html . $script;
        }, 15);
    }
}
