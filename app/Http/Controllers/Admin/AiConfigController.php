<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAiConfigRequest;
use App\Models\Setting;
use App\Services\AI\AIService;
use App\Services\Setting\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AiConfigController extends Controller
{
    public function __construct(
        protected readonly SettingService $settings,
        protected readonly AIService $ai,
    ) {}

    public function edit(): View
    {
        $values = $this->settings->getMany([
            'ai_provider',
            'ai_api_key',
            'ai_model',
            'ai_base_url',
            'ai_max_tokens',
            'ai_timeout',
            'ai_cache_enabled',
        ]);

        return view('admin.ai-config', [
            'values' => $values,
            'availableProviders' => ['openai', 'gemini'],
        ]);
    }

    public function update(UpdateAiConfigRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (filled($request->input('ai_api_key'))) {
            $data['ai_api_key'] = $request->input('ai_api_key');
        } else {
            unset($data['ai_api_key']);
        }

        $this->settings->setMany($data, Setting::GROUP_AI);

        return back()->with('toast', [
            'type' => 'success',
            'title' => 'AI configuration saved',
            'message' => 'The AI provider settings have been updated.',
        ]);
    }

    public function test(): RedirectResponse
    {
        try {
            $provider = $this->ai->provider();
            $response = $this->ai->generate(
                feature: 'system_test',
                template: 'natural_language_task',
                params: ['request' => 'Test connection'],
            );

            return back()->with('toast', [
                'type' => 'success',
                'title' => 'AI connection works',
                'message' => 'Provider "'.$response->provider.'" responded successfully.',
            ]);
        } catch (\Throwable $e) {
            return back()->with('toast', [
                'type' => 'error',
                'title' => 'AI connection failed',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
