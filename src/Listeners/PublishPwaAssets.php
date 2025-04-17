<?php

namespace FriendsOfBotble\Pwa\Listeners;

use Botble\Media\Facades\RvMedia;
use Illuminate\Support\Facades\File;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class PublishPwaAssets
{
    public function handle(): void
    {
        if (is_plugin_active('fob-pwa')) {
            $this->generatePwaIcons();
            $this->publishPwaAssets();
        }
    }

    public function generatePwaIcons(): void
    {
        $logoPath = setting('pwa_icon', theme_option('logo'));

        if (! $logoPath) {
            return;
        }

        $logoRealPath = RvMedia::getRealPath($logoPath);

        if (! file_exists($logoRealPath)) {
            return;
        }

        $pwaDir = public_path('pwa');
        if (! File::isDirectory($pwaDir)) {
            File::makeDirectory($pwaDir, 0755, true);
        }

        $sizes = [72, 96, 128, 144, 152, 192, 384, 512];

        $manager = new ImageManager(new Driver());
        $image = $manager->read($logoRealPath);

        foreach ($sizes as $size) {
            $iconPath = $pwaDir . "/icon-{$size}x{$size}.png";

            $resizedImage = $image->resize($size, $size);
            $resizedImage->save($iconPath);
        }
    }

    public function publishPwaAssets(): void
    {
        $source = plugin_path('fob-pwa/public');
        $destination = public_path('pwa');

        // Make sure the destination directory exists
        if (! File::isDirectory($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        // Generate and save the manifest.json file
        $this->generateManifestJson();

        // Copy the service-worker.js file
        if (File::exists($source . '/service-worker.js')) {
            File::copy($source . '/service-worker.js', public_path('service-worker.js'));
        }

        // Copy the offline.html file
        if (File::exists($source . '/pwa/offline.html')) {
            if (! File::isDirectory($destination)) {
                File::makeDirectory($destination, 0755, true);
            }
            File::copy($source . '/pwa/offline.html', $destination . '/offline.html');
        }
    }

    public function generateManifestJson(): void
    {
        $appName = setting('pwa_app_name', setting('site_title', 'Progressive Web App'));
        $shortName = setting('pwa_short_name', 'PWA');
        $themeColor = setting('pwa_theme_color', '#0989ff');
        $backgroundColor = setting('pwa_background_color', '#ffffff');
        $startUrl = setting('pwa_start_url', '/');
        $display = setting('pwa_display', 'standalone');
        $orientation = setting('pwa_orientation', 'portrait');

        $manifest = [
            'name' => $appName,
            'short_name' => $shortName,
            'start_url' => $startUrl,
            'display' => $display,
            'background_color' => $backgroundColor,
            'theme_color' => $themeColor,
            'orientation' => $orientation,
            'icons' => [],
        ];

        $sizes = [72, 96, 128, 144, 152, 192, 384, 512];
        foreach ($sizes as $size) {
            $manifest['icons'][] = [
                'src' => "/pwa/icon-{$size}x{$size}.png",
                'sizes' => "{$size}x{$size}",
                'type' => 'image/png',
            ];
        }

        $manifestJson = json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        File::put(public_path('pwa/manifest.json'), $manifestJson);
    }
}
