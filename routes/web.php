<?php

use Botble\Base\Facades\AdminHelper;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'FriendsOfBotble\Pwa\Http\Controllers'], function (): void {
    AdminHelper::registerRoutes(function (): void {
        Route::group(['prefix' => 'settings'], function (): void {
            Route::group(['prefix' => 'pwa', 'as' => 'pwa.'], function (): void {
                Route::get('', [
                    'as' => 'settings',
                    'uses' => 'Settings\PwaSettingController@edit',
                    'permission' => 'pwa.settings',
                ]);

                Route::put('', [
                    'as' => 'settings.update',
                    'uses' => 'Settings\PwaSettingController@update',
                    'permission' => 'pwa.settings',
                ]);
            });
        });
    });
});
