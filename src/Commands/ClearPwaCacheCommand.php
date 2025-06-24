<?php

namespace FriendsOfBotble\Pwa\Commands;

use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:pwa:clear-cache', 'Clear PWA cache and regenerate service worker')]
class ClearPwaCacheCommand extends Command
{
    public function handle(): int
    {
        $this->components->info('Clearing PWA cache...');

        if (! pwa_is_enabled()) {
            $this->components->warn('PWA is not enabled. Skipping cache clear.');

            return self::SUCCESS;
        }

        try {
            $oldVersion = pwa_get_cache_version();
            $this->components->info("Current cache version: {$oldVersion}");

            if (pwa_clear_cache()) {
                $newVersion = pwa_get_cache_version();
                $this->components->info('PWA cache cleared successfully!');
                $this->components->info("New cache version: {$newVersion}");

                // Also trigger the cache:cleared event to test the event listener
                $this->components->info('Triggering cache:cleared event...');
                event('cache:cleared');

                return self::SUCCESS;
            } else {
                $this->components->error('Failed to clear PWA cache.');

                return self::FAILURE;
            }
        } catch (Exception $e) {
            $this->components->error('Error clearing PWA cache: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
