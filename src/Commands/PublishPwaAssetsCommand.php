<?php

namespace FriendsOfBotble\Pwa\Commands;

use FriendsOfBotble\Pwa\Listeners\PublishPwaAssets;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:pwa:publish', 'Publish PWA assets')]
class PublishPwaAssetsCommand extends Command
{
    public function handle(): int
    {
        $this->components->info('Publishing PWA assets...');

        try {
            $publisher = new PublishPwaAssets();
            $publisher->generatePwaIcons();
            $publisher->publishPwaAssets();

            $this->components->info('PWA assets published successfully!');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->components->error('Error publishing PWA assets: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
