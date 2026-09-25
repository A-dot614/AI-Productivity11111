<x-app-layout>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">AI configuration</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Configure the provider and model used by the AI assistant.</p>
        </div>
        <form method="POST" action="{{ route('admin.ai-config.test') }}">
            @csrf
            <button class="btn-secondary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                Test connection
            </button>
        </form>
    </div>

    <form method="POST" action="{{ route('admin.ai-config.update') }}" class="max-w-3xl">
        @csrf
        @method('PUT')
        <x-card title="Provider">
            <div class="space-y-4">
                <div>
                    <label for="ai_provider" class="label">Provider</label>
                    <select id="ai_provider" name="ai_provider" class="input">
                        @foreach ($availableProviders as $provider)
                            <option value="{{ $provider }}" @selected(old('ai_provider', $values['ai_provider'] ?? '') === $provider)>{{ ucfirst($provider) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="ai_api_key" class="label">API key</label>
                    <input type="password" id="ai_api_key" name="ai_api_key" value="" placeholder="{{ filled($values['ai_api_key'] ?? null) ? '•••••••• (saved, leave blank to keep)' : 'sk-…' }}" class="input">
                    <p class="mt-1 text-xs text-slate-400">Stored encrypted. Leave blank to keep the current key.</p>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="ai_model" class="label">Model</label>
                        <input type="text" id="ai_model" name="ai_model" value="{{ old('ai_model', $values['ai_model'] ?? '') }}" placeholder="e.g. gpt-4o-mini" class="input">
                    </div>
                    <div>
                        <label for="ai_base_url" class="label">Base URL (optional)</label>
                        <input type="text" id="ai_base_url" name="ai_base_url" value="{{ old('ai_base_url', $values['ai_base_url'] ?? '') }}" placeholder="https://api.openai.com/v1" class="input">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="ai_max_tokens" class="label">Max tokens</label>
                        <input type="number" id="ai_max_tokens" name="ai_max_tokens" min="256" max="8000" value="{{ old('ai_max_tokens', $values['ai_max_tokens'] ?? '') }}" placeholder="2048" class="input">
                    </div>
                    <div>
                        <label for="ai_timeout" class="label">Timeout (seconds)</label>
                        <input type="number" id="ai_timeout" name="ai_timeout" min="5" max="300" value="{{ old('ai_timeout', $values['ai_timeout'] ?? '') }}" placeholder="60" class="input">
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="ai_cache_enabled" name="ai_cache_enabled" value="1"
                        @checked(old('ai_cache_enabled', (bool) ($values['ai_cache_enabled'] ?? false)))
                        class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800">
                    <label for="ai_cache_enabled" class="text-sm text-slate-600 dark:text-slate-300">Cache repeated AI requests</label>
                </div>
            </div>
        </x-card>

        <div class="mt-6">
            <button type="submit" class="btn-primary">Save configuration</button>
        </div>
    </form>
</x-app-layout>
