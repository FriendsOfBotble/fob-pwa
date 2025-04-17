<?php

namespace FriendsOfBotble\Pwa\Http\Controllers\Settings;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Setting\Facades\Setting;
use FriendsOfBotble\Pwa\Forms\PwaSettingForm;
use FriendsOfBotble\Pwa\Http\Requests\PwaSettingRequest;
use FriendsOfBotble\Pwa\Listeners\PublishPwaAssets;

class PwaSettingController extends BaseController
{
    public function edit()
    {
        $this->pageTitle(trans('plugins/fob-pwa::pwa.settings.title'));

        return PwaSettingForm::create()->renderForm();
    }

    public function update(PwaSettingRequest $request, BaseHttpResponse $response)
    {
        $data = $request->validated();

        foreach ($data as $key => $value) {
            Setting::set('pwa_' . $key, $value);
        }

        Setting::save();

        (new PublishPwaAssets())->generatePwaIcons();
        (new PublishPwaAssets())->publishPwaAssets();

        return $response
            ->setPreviousUrl(route('pwa.settings'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }
}
