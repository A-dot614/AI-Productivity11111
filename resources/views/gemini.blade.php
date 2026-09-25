<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ask Gemini</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Tailwind Play CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script>
        tailwind.config = {
            darkMode: 'media',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Inter"', 'system-ui', '-apple-system', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#EEF4FF',
                            100: '#E0EAFF',
                            500: '#3B82F6',
                            600: '#2563EB',
                            700: '#1D4ED8',
                        }
                    }
                },
            },
        };
    </script>

    <style>
        :root {
            --bg-radial: radial-gradient(circle at 50% 0%, rgba(59, 130, 246, 0.08) 0%, transparent 65%);
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --bg-radial: radial-gradient(circle at 50% 0%, rgba(59, 130, 246, 0.12) 0%, transparent 70%);
            }
        }

        .bg-mesh {
            background-image: var(--bg-radial);
        }

        /* Subtle animated gradient on focus */
        .composer-ring {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .composer-ring:focus-within {
            box-shadow: 0 0 0 1px rgba(99, 102, 241, 0.2), 0 12px 30px -8px rgba(0, 0, 0, 0.08);
            border-color: rgba(99, 102, 241, 0.45);
        }

        /* Markdown styling overrides */
        .markdown-body pre {
            position: relative;
            background-color: #0d1117 !important;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 1.15rem;
        }
        .markdown-body code {
            font-size: 0.86rem;
            font-family: 'JetBrains Mono', monospace;
        }
    </style>
</head>
<body class="bg-neutral-50 text-neutral-900 dark:bg-[#0B0D13] dark:text-neutral-100 font-sans antialiased min-h-screen bg-mesh selection:bg-brand-500/20 selection:text-brand-600 dark:selection:text-brand-100 flex flex-col justify-between">

    <div class="max-w-3xl w-full mx-auto px-5 py-12 sm:py-16">

        <!-- Header -->
        <header class="mb-10 text-center sm:text-left flex flex-col sm:flex-row items-center sm:items-start justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-medium bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 border border-brand-200/60 dark:border-brand-500/20 mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-brand-500 animate-pulse"></span>
                    Gemini 3.8 Pro
                </div>
                <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-neutral-950 dark:text-white">Ask Gemini</h1>
                <p class="text-neutral-500 dark:text-neutral-400 text-sm mt-1.5 leading-relaxed">Ask anything, generate code, or explore ideas with real-time markdown rendering.</p>
            </div>
        </header>

        <!-- Error Alert -->
        @if($errors->any())
            <div role="alert" class="flex gap-3 items-center p-3.5 mb-6 rounded-xl text-sm border border-rose-200 bg-rose-50/70 text-rose-800 dark:bg-rose-950/20 dark:text-rose-300 dark:border-rose-900/50 backdrop-blur-sm shadow-sm">
                <svg class="w-4 h-4 shrink-0 text-rose-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-4a1 1 0 00-1 1v3a1 1 0 002 0V7a1 1 0 00-1-1zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Composer Panel -->
        <form id="prompt-form" action="{{ route('gemini.generate') }}" method="POST" class="relative group">
            @csrf
            <div class="composer-ring relative rounded-2xl bg-white dark:bg-[#12151D] border border-neutral-200 dark:border-neutral-800 shadow-sm transition-all duration-200 overflow-hidden">
                <label for="prompt" class="sr-only">Your prompt</label>
                <textarea
                    name="prompt"
                    id="prompt"
                    rows="3"
                    required
                    placeholder="Ask a question or explain what you want to build..."
                    class="w-full bg-transparent p-5 text-sm sm:text-base leading-relaxed placeholder:text-neutral-400 dark:placeholder:text-neutral-500 outline-none resize-none text-neutral-900 dark:text-neutral-100 min-h-[100px]"
                >{{ old('prompt', session('prompt')) }}</textarea>

                <div class="flex items-center justify-between gap-3 px-4 py-3 bg-neutral-50/50 dark:bg-neutral-900/40 border-t border-neutral-100 dark:border-neutral-800/80">
                    <div class="flex items-center gap-1.5 text-xs text-neutral-400 dark:text-neutral-500 select-none">
                        <kbd class="px-1.5 py-0.5 rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-[10px] font-mono shadow-xs">⌘</kbd>
                        <span>+</span>
                        <kbd class="px-1.5 py-0.5 rounded border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-[10px] font-mono shadow-xs">Enter</kbd>
                    </div>

                    <button
                        id="submit-btn"
                        type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-medium rounded-xl bg-neutral-950 text-white dark:bg-white dark:text-neutral-950 hover:bg-neutral-800 dark:hover:bg-neutral-200 transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed shadow-xs active:scale-[0.98]"
                    >
                        <svg id="submit-spinner" class="w-3.5 h-3.5 animate-spin hidden text-current" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="submit-label">Submit</span>
                        <svg class="w-3.5 h-3.5" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M3.75 2a.75.75 0 0 0-.75.75v10.5c0 .414.336.75.75.75h8.5a.75.75 0 0 0 .75-.75V6.5a.75.75 0 0 0-.22-.53l-4-4A.75.75 0 0 0 8.25 2H3.75Zm4.5 1.56L11.44 6.75H8.25V3.56Z" />
                        </svg>
                    </button>
                </div>
            </div>
        </form>

        <!-- Suggestion Chips -->
        @unless(session('response'))
            <div class="mt-6">
                <span class="text-xs font-medium text-neutral-400 dark:text-neutral-500 uppercase tracking-wider block mb-2.5">Suggested</span>
                <div class="flex flex-wrap gap-2" aria-label="Example prompts">
                    @foreach([
                        'Write a PHP function that checks for palindromes',
                        'Explain Laravel Service Container in simple terms',
                        'Solve Eloquent N+1 problem with eager loading'
                    ] as $example)
                        <button
                            type="button"
                            data-example="{{ $example }}"
                            class="px-3.5 py-1.5 rounded-lg border border-neutral-200/80 dark:border-neutral-800 bg-white/70 dark:bg-neutral-900/60 hover:border-neutral-300 dark:hover:border-neutral-700 text-neutral-600 dark:text-neutral-300 text-xs transition duration-150 active:scale-[0.98] shadow-2xs backdrop-blur-xs text-left"
                        >
                            {{ $example }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endunless

        <!-- Response Container -->
        @if(session('response'))
            <section id="response" class="mt-8 rounded-2xl bg-white dark:bg-[#12151D] border border-neutral-200/90 dark:border-neutral-800 shadow-xs overflow-hidden" aria-labelledby="response-title">
                <div class="flex items-center justify-between px-6 py-3.5 border-b border-neutral-100 dark:border-neutral-800/80 bg-neutral-50/50 dark:bg-neutral-900/40">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <h2 id="response-title" class="font-medium text-xs text-neutral-700 dark:text-neutral-300">Answer</h2>
                    </div>
                    <button
                        id="copy-btn"
                        type="button"
                        class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium rounded-lg text-neutral-600 dark:text-neutral-400 hover:bg-neutral-200/50 dark:hover:bg-neutral-800 transition-colors"
                    >
                        <svg class="w-3.5 h-3.5" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M0 6.75C0 5.784.784 5 1.75 5h1.5a.75.75 0 0 1 0 1.5h-1.5a.25.25 0 0 0-.25.25v7.5c0 .138.112.25.25.25h7.5a.25.25 0 0 0 .25-.25v-1.5a.75.75 0 0 1 1.5 0v1.5A1.75 1.75 0 0 1 9.25 16h-7.5A1.75 1.75 0 0 1 0 14.25Z" />
                            <path d="M5 1.75C5 .784 5.784 0 6.75 0h7.5C15.216 0 16 .784 16 1.75v7.5A1.75 1.75 0 0 1 14.25 11h-7.5A1.75 1.75 0 0 1 5 9.25Zm1.75-.25a.25.25 0 0 0-.25.25v7.5c0 .138.112.25.25.25h7.5a.25.25 0 0 0 .25-.25v-7.5a.25.25 0 0 0-.25-.25Z" />
                        </svg>
                        <span id="copy-text">Copy Full</span>
                    </button>
                </div>
                <div class="markdown-body prose prose-neutral dark:prose-invert prose-sm sm:prose-base max-w-none p-6 sm:p-7 leading-relaxed prose-pre:my-3">
                    {!! \Illuminate\Support\Str::markdown(session('response'), [
                        'html_input' => 'escape',
                        'allow_unsafe_links' => false,
                    ]) !!}
                </div>
            </section>
        @endif

    </div>

    <!-- Scripting -->
    <script>
        const form = document.getElementById('prompt-form');
        const prompt = document.getElementById('prompt');

        // Textarea auto-resize
        const grow = () => {
            prompt.style.height = 'auto';
            prompt.style.height = Math.max(100, prompt.scrollHeight) + 'px';
        };
        prompt.addEventListener('input', grow);
        grow();

        // Keyboard submission (Ctrl/Cmd + Enter)
        prompt.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                e.preventDefault();
                form.requestSubmit();
            }
        });

        // Submit state transition
        form.addEventListener('submit', () => {
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            document.getElementById('submit-spinner').classList.remove('hidden');
            document.getElementById('submit-label').textContent = 'Thinking...';
        });

        // Populate prompt suggestions
        document.querySelectorAll('[data-example]').forEach(btn => {
            btn.addEventListener('click', () => {
                prompt.value = btn.dataset.example;
                grow();
                prompt.focus();
            });
        });

        // Response actions & copy enhancement
        @if(session('response'))
            const rawResponse = @json(session('response'));
            const copyBtn = document.getElementById('copy-btn');
            const copyText = document.getElementById('copy-text');

            copyBtn.addEventListener('click', async () => {
                try {
                    await navigator.clipboard.writeText(rawResponse);
                    copyText.textContent = 'Copied!';
                } catch (e) {
                    copyText.textContent = 'Failed';
                }
                setTimeout(() => (copyText.textContent = 'Copy Full'), 1800);
            });

            // Smooth scroll into response
            document.getElementById('response').scrollIntoView({ behavior: 'smooth', block: 'start' });
        @endif
    </script>
</body>
</html>
