<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\Setting;
use App\Services\Setting\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        protected readonly SettingService $settings,
    ) {}

    public function edit(): View
    {
        return view('admin.settings', [
            'values' => $this->settings->getMany([
                'reminder_lead_minutes',
                'reminder_channel',
                'default_task_view',
                'daily_digest_enabled',
                'daily_digest_time',
            ]),
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $this->settings->setMany($request->validated(), Setting::GROUP_NOTIFICATION);

        return back()->with('toast', [
            'type' => 'success',
            'title' => 'Settings saved',
            'message' => 'System settings have been updated.',
        ]);
    }
}
