<?php

namespace App\Http\Controllers\Backend;

use App\Constants\NotificationConstants;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::firstOrCreate(['user_id' => user_id()]);
        return view('pages.backend.setting.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'show_customizer_button' => 'boolean',
            'notification_channel' => 'in:' . implode(',', NotificationConstants::getAllChannels()),
            'theme' => 'boolean',
        ]);

        $theme = $request->boolean('theme')
            ? NotificationConstants::THEME_DARK
            : NotificationConstants::THEME_LIGHT;

        Setting::updateOrCreate(
            ['user_id' => user_id()],
            [
                'show_customizer_button' => $request->boolean('show_customizer_button'),
                'theme' => $theme,
                'notification_channel' => $request->notification_channel
            ]
        );

        return redirect()->back()->with('success', __('messages.settings_updated_success'));
    }

}
