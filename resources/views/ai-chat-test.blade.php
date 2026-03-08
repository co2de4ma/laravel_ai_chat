<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>AI Chat Test</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-50">
        <main class="mx-auto flex min-h-screen w-full max-w-4xl items-center px-6 py-10">
            <div class="w-full rounded-3xl border border-slate-800 bg-slate-900/90 p-6 shadow-2xl shadow-slate-950/40 md:p-8">
                <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div class="space-y-3">
                        <p class="text-sm font-medium uppercase tracking-[0.3em] text-cyan-300">ENV-backed AI smoke test</p>
                        <h1 class="text-3xl font-semibold text-white md:text-4xl">AI Chat Test Page</h1>
                        <p class="max-w-2xl text-sm leading-6 text-slate-300 md:text-base">
                            Send a short prompt to the AI provider configured in your environment and verify the live response end-to-end.
                        </p>
                    </div>
                    <div class="grid gap-3 text-sm text-slate-200">
                        <div class="rounded-2xl border border-slate-800 bg-slate-950/60 px-4 py-3">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Provider</p>
                            <p class="mt-1 font-semibold">{{ $configuredProvider ? strtoupper($configuredProvider) : 'NOT CONFIGURED' }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-800 bg-slate-950/60 px-4 py-3">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Model</p>
                            <p class="mt-1 break-all font-semibold">{{ $configuredModel ?? 'Add AI_MODEL or provider model env' }}</p>
                        </div>
                    </div>
                </div>

                @if (! $configuredProvider)
                    <div class="mb-6 rounded-2xl border border-amber-500/40 bg-amber-500/10 px-4 py-3 text-sm text-amber-100">
                        No provider could be inferred. Set <code class="font-mono">AI_PROVIDER</code> or add a supported API key such as
                        <code class="font-mono">OPENAI_API_KEY</code>.
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-rose-500/40 bg-rose-500/10 px-4 py-3 text-sm text-rose-100">
                        {{ $errors->first('prompt') }}
                    </div>
                @endif

                @if ($error)
                    <div class="mb-6 rounded-2xl border border-rose-500/40 bg-rose-500/10 px-4 py-3 text-sm text-rose-100">
                        {{ $error }}
                    </div>
                @endif

                <form id="chat-form" method="POST" action="{{ route('ai-chat-test.store') }}" class="space-y-5">
                    @csrf
                    <div class="space-y-2">
                        <label for="prompt" class="text-sm font-medium text-slate-200">Prompt</label>
                        <textarea
                            id="prompt"
                            name="prompt"
                            rows="6"
                            class="w-full rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-slate-100 outline-none transition focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/30"
                            placeholder="Ask the configured provider to confirm the connection."
                        >{{ $prompt }}</textarea>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <button
                            id="submit-button"
                            type="submit"
                            class="inline-flex items-center justify-center rounded-2xl bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300 disabled:cursor-not-allowed disabled:bg-cyan-800 disabled:text-slate-200"
                        >
                            <span id="submit-label">Send prompt</span>
                        </button>
                        <p id="submit-status" class="text-sm text-slate-400">The page uses the provider and model shown above.</p>
                    </div>
                </form>

                <section class="mt-8 grid gap-6 lg:grid-cols-[1.35fr_0.95fr]">
                    <div class="rounded-3xl border border-slate-800 bg-slate-950/70 p-5">
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <h2 class="text-lg font-semibold text-white">Latest response</h2>
                            @if ($result)
                                <span class="rounded-full border border-emerald-500/40 bg-emerald-500/10 px-3 py-1 text-xs font-medium uppercase tracking-[0.2em] text-emerald-200">
                                    Live result
                                </span>
                            @endif
                        </div>

                        @if ($result)
                            <div class="space-y-4">
                                <p class="rounded-2xl border border-slate-800 bg-slate-900 px-4 py-4 text-sm leading-7 text-slate-100">{{ $result['text'] }}</p>
                                <dl class="grid gap-3 text-sm text-slate-300 sm:grid-cols-2">
                                    <div class="rounded-2xl border border-slate-800 bg-slate-900 px-4 py-3">
                                        <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Provider used</dt>
                                        <dd class="mt-1 font-medium text-slate-100">{{ strtoupper($result['provider']) }}</dd>
                                    </div>
                                    <div class="rounded-2xl border border-slate-800 bg-slate-900 px-4 py-3">
                                        <dt class="text-xs uppercase tracking-[0.2em] text-slate-500">Model used</dt>
                                        <dd class="mt-1 break-all font-medium text-slate-100">{{ $result['model'] }}</dd>
                                    </div>
                                </dl>
                            </div>
                        @else
                            <p class="rounded-2xl border border-dashed border-slate-700 bg-slate-900/60 px-4 py-6 text-sm leading-6 text-slate-400">
                                Submit the form to see the provider's reply here.
                            </p>
                        @endif
                    </div>

                    <div class="rounded-3xl border border-slate-800 bg-slate-950/70 p-5">
                        <h2 class="mb-3 text-lg font-semibold text-white">Request notes</h2>
                        <ul class="space-y-3 text-sm leading-6 text-slate-300">
                            <li class="rounded-2xl border border-slate-800 bg-slate-900 px-4 py-3">
                                The page submits a single prompt and renders the provider response directly in Blade.
                            </li>
                            <li class="rounded-2xl border border-slate-800 bg-slate-900 px-4 py-3">
                                Supported providers: OpenAI, Anthropic, and Gemini.
                            </li>
                            <li class="rounded-2xl border border-slate-800 bg-slate-900 px-4 py-3">
                                If <code class="font-mono">AI_PROVIDER</code> is unset, the app infers the provider from the first available API key.
                            </li>
                        </ul>

                        @if ($result)
                            <details class="mt-4 rounded-2xl border border-slate-800 bg-slate-900 px-4 py-3">
                                <summary class="cursor-pointer text-sm font-medium text-slate-100">View raw provider payload</summary>
                                <pre class="mt-3 overflow-x-auto text-xs leading-6 text-slate-300">{{ json_encode($result['raw'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            </details>
                        @endif
                    </div>
                </section>
            </div>
        </main>

        <script>
            const form = document.getElementById('chat-form');
            const submitButton = document.getElementById('submit-button');
            const submitLabel = document.getElementById('submit-label');
            const submitStatus = document.getElementById('submit-status');

            form?.addEventListener('submit', () => {
                submitButton.disabled = true;
                submitLabel.textContent = 'Calling provider...';
                submitStatus.textContent = 'Waiting for the AI response.';
            });
        </script>
    </body>
</html>
