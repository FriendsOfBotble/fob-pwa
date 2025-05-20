<?php

namespace FriendsOfBotble\Pwa\Listeners;

use Botble\Media\Facades\RvMedia;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
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
        try {
            $logoPath = setting('pwa_icon', theme_option('logo'));

            if (! $logoPath) {
                Log::warning('PWA: No logo path found for generating PWA icons');

                return;
            }

            $logoRealPath = RvMedia::getRealPath($logoPath);

            if (! file_exists($logoRealPath)) {
                Log::warning('PWA: Logo file does not exist at path: ' . $logoRealPath);

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

                try {
                    $resizedImage = $image->resize($size, $size);
                    $resizedImage->save($iconPath);
                } catch (Exception $e) {
                    Log::error('PWA: Failed to generate icon of size ' . $size . 'x' . $size . ': ' . $e->getMessage());
                }
            }
        } catch (Exception $e) {
            Log::error('PWA: Error generating PWA icons: ' . $e->getMessage());
        }
    }

    public function publishPwaAssets(): void
    {
        try {
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
                Log::info('PWA: Service worker file copied successfully');
            } else {
                Log::warning('PWA: Service worker file not found at: ' . $source . '/service-worker.js');
            }

            // Copy the offline.html file
            if (File::exists($source . '/pwa/offline.html')) {
                if (! File::isDirectory($destination)) {
                    File::makeDirectory($destination, 0755, true);
                }
                File::copy($source . '/pwa/offline.html', $destination . '/offline.html');
                Log::info('PWA: Offline HTML file copied successfully');
            } else {
                Log::warning('PWA: Offline HTML file not found at: ' . $source . '/pwa/offline.html');
            }
        } catch (Exception $e) {
            Log::error('PWA: Error publishing PWA assets: ' . $e->getMessage());
        }
    }

    public function generateManifestJson(): void
    {
        try {
            $appName = setting('pwa_app_name', setting('site_title', 'Progressive Web App'));
            $shortName = setting('pwa_short_name', 'PWA');
            $themeColor = setting('pwa_theme_color', '#0989ff');
            $backgroundColor = setting('pwa_background_color', '#ffffff');
            $startUrl = setting('pwa_start_url', '/');
            $display = setting('pwa_display', 'standalone');
            $orientation = setting('pwa_orientation', 'portrait');

            // Ensure app name is never null
            if (empty($appName)) {
                $appName = 'Progressive Web App';
                Log::warning('PWA: App name was empty, using default name');
            }

            $manifest = [
                'name' => $appName,
                'short_name' => $shortName,
                'start_url' => $startUrl,
                'display' => $display,
                'background_color' => $backgroundColor,
                'theme_color' => $themeColor,
                'orientation' => $orientation,
                'scope' => '/',
                'icons' => [],
                'description' => setting('site_description', 'Progressive Web App'),
                'dir' => 'ltr',
                'lang' => app()->getLocale(),
                'prefer_related_applications' => false,
            ];

            $sizes = [72, 96, 128, 144, 152, 192, 384, 512];
            foreach ($sizes as $size) {
                // Regular icons
                $manifest['icons'][] = [
                    'src' => "/pwa/icon-{$size}x{$size}.png",
                    'sizes' => "{$size}x{$size}",
                    'type' => 'image/png',
                    'purpose' => 'any',
                ];

                // Add maskable icons for sizes 192 and above (for adaptive icons on Android)
                if ($size >= 192) {
                    $manifest['icons'][] = [
                        'src' => "/pwa/icon-{$size}x{$size}.png",
                        'sizes' => "{$size}x{$size}",
                        'type' => 'image/png',
                        'purpose' => 'maskable',
                    ];
                }
            }

            // Add a special 512x512 icon with both purposes
            $manifest['icons'][] = [
                'src' => '/pwa/icon-512x512.png',
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'any maskable',
            ];

            $manifestJson = json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            if ($manifestJson === false) {
                Log::error('PWA: Failed to encode manifest.json: ' . json_last_error_msg());

                return;
            }

            $manifestPath = public_path('pwa/manifest.json');
            if (File::put($manifestPath, $manifestJson) === false) {
                Log::error('PWA: Failed to write manifest.json to: ' . $manifestPath);
            } else {
                Log::info('PWA: manifest.json generated successfully');
            }
        } catch (Exception $e) {
            Log::error('PWA: Error generating manifest.json: ' . $e->getMessage());
        }
    }
}
