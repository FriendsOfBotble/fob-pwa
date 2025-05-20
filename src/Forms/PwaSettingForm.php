<?php

namespace FriendsOfBotble\Pwa\Forms;

use Botble\Base\Forms\FieldOptions\ColorFieldOption;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\ColorField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Setting\Forms\SettingForm;
use FriendsOfBotble\Pwa\Http\Requests\PwaSettingRequest;

class PwaSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setSectionTitle(trans('plugins/fob-pwa::pwa.settings.title'))
            ->setSectionDescription(trans('plugins/fob-pwa::pwa.settings.description'))
            ->setValidatorClass(PwaSettingRequest::class)
            ->add(
                'enable',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-pwa::pwa.settings.enable'))
                    ->value(setting('pwa_enable', false))
            )
            ->addOpenCollapsible('enable', '1', setting('pwa_enable', false) == '1')
            ->add(
                'app_name',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/fob-pwa::pwa.settings.app_name'))
                    ->value(setting('pwa_app_name', setting('site_title')))
                    ->placeholder(trans('plugins/fob-pwa::pwa.settings.app_name_placeholder'))
            )
            ->add(
                'short_name',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/fob-pwa::pwa.settings.short_name'))
                    ->value(setting('pwa_short_name', 'PWA'))
                    ->placeholder(trans('plugins/fob-pwa::pwa.settings.short_name_placeholder'))
            )
            ->add(
                'theme_color',
                ColorField::class,
                ColorFieldOption::make()
                    ->label(trans('plugins/fob-pwa::pwa.settings.theme_color'))
                    ->value(setting('pwa_theme_color', '#0989ff'))
            )
            ->add(
                'background_color',
                ColorField::class,
                ColorFieldOption::make()
                    ->label(trans('plugins/fob-pwa::pwa.settings.background_color'))
                    ->value(setting('pwa_background_color', '#ffffff'))
            )
            ->add(
                'icon',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(trans('plugins/fob-pwa::pwa.settings.icon'))
                    ->value(setting('pwa_icon', theme_option('logo')))
                    ->helperText(trans('plugins/fob-pwa::pwa.settings.icon_description'))
            )
            ->add(
                'start_url',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/fob-pwa::pwa.settings.start_url'))
                    ->value(setting('pwa_start_url', '/'))
            )
            ->add(
                'display',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/fob-pwa::pwa.settings.display'))
                    ->selected(setting('pwa_display', 'standalone'))
                    ->choices([
                        'fullscreen' => trans('plugins/fob-pwa::pwa.settings.display_options.fullscreen'),
                        'standalone' => trans('plugins/fob-pwa::pwa.settings.display_options.standalone'),
                        'minimal-ui' => trans('plugins/fob-pwa::pwa.settings.display_options.minimal_ui'),
                        'browser' => trans('plugins/fob-pwa::pwa.settings.display_options.browser'),
                    ])
            )
            ->add(
                'orientation',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/fob-pwa::pwa.settings.orientation'))
                    ->selected(setting('pwa_orientation', 'portrait'))
                    ->choices([
                        'any' => trans('plugins/fob-pwa::pwa.settings.orientation_options.any'),
                        'natural' => trans('plugins/fob-pwa::pwa.settings.orientation_options.natural'),
                        'landscape' => trans('plugins/fob-pwa::pwa.settings.orientation_options.landscape'),
                        'portrait' => trans('plugins/fob-pwa::pwa.settings.orientation_options.portrait'),
                    ])
            )
            ->add(
                'enable_notifications',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-pwa::pwa.settings.enable_notifications'))
                    ->value(setting('pwa_enable_notifications', false))
                    ->helperText(trans('plugins/fob-pwa::pwa.settings.enable_notifications_description'))
            )
            ->add(
                'show_install_prompt',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-pwa::pwa.settings.show_install_prompt'))
                    ->value(setting('pwa_show_install_prompt', true))
                    ->helperText(trans('plugins/fob-pwa::pwa.settings.show_install_prompt_description'))
            )
            ->addCloseCollapsible('enable', '1');
    }
}
