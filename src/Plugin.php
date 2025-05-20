<?php

namespace FriendsOfBotble\Pwa;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Facades\Setting;
use Illuminate\Support\Facades\File;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        $keys = [
            'pwa_enable',
            'pwa_app_name',
            'pwa_short_name',
            'pwa_theme_color',
            'pwa_background_color',
            'pwa_icon',
            'pwa_start_url',
            'pwa_display',
            'pwa_orientation',
            'pwa_enable_notifications',
            'pwa_show_install_prompt',
        ];

        Setting::delete($keys);

        $files = [
            public_path('service-worker.js'),
            public_path('pwa/manifest.json'),
            public_path('pwa/offline.html'),
        ];

        foreach ($files as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }

        // Delete PWA icons
        $sizes = [72, 96, 128, 144, 152, 192, 384, 512];
        foreach ($sizes as $size) {
            $iconPath = public_path("pwa/icon-{$size}x{$size}.png");
            if (File::exists($iconPath)) {
                File::delete($iconPath);
            }
        }
    }
}
