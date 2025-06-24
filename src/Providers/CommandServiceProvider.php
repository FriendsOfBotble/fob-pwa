<?php

namespace FriendsOfBotble\Pwa\Providers;

use FriendsOfBotble\Pwa\Commands\ClearPwaCacheCommand;
use FriendsOfBotble\Pwa\Commands\PublishPwaAssetsCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PublishPwaAssetsCommand::class,
                ClearPwaCacheCommand::class,
            ]);
        }
    }
}
