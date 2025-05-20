<?php

namespace FriendsOfBotble\Pwa\Providers;

use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\PanelSections\PanelSectionItem;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Setting\PanelSections\SettingOthersPanelSection;

class PwaServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->register(HookServiceProvider::class);
        $this->app->register(CommandServiceProvider::class);
    }

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/fob-pwa')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['general', 'permissions'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->publishAssets();

        PanelSectionManager::default()->beforeRendering(function (): void {
            PanelSectionManager::registerItem(
                SettingOthersPanelSection::class,
                fn () => PanelSectionItem::make('pwa')
                    ->setTitle(trans('plugins/fob-pwa::pwa.settings.title'))
                    ->withIcon('ti ti-device-mobile')
                    ->withDescription(trans('plugins/fob-pwa::pwa.settings.description'))
                    ->withPriority(170)
                    ->withRoute('pwa.settings')
            );
        });

        $this->app->booted(function () {
            $this->app->register(EventServiceProvider::class);
        });
    }
}
