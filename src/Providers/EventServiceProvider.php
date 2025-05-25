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
}
