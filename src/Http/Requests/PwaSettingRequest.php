<?php

namespace FriendsOfBotble\Pwa\Http\Requests;

use Botble\Support\Http\Requests\Request;

class PwaSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'enable' => 'nullable|boolean',
            'app_name' => 'nullable|string|max:120',
            'short_name' => 'nullable|string|max:30',
            'theme_color' => 'nullable|string|max:20',
            'background_color' => 'nullable|string|max:20',
            'icon' => 'nullable|string',
            'start_url' => 'nullable|string|max:255',
            'display' => 'nullable|string|in:fullscreen,standalone,minimal-ui,browser',
            'orientation' => 'nullable|string|in:any,natural,landscape,portrait',
        ];
    }
}
