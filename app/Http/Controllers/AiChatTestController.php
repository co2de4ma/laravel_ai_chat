<?php

namespace App\Http\Controllers;

use App\Services\AiChatService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Throwable;

class AiChatTestController extends Controller
{
    public function __construct(
        private readonly AiChatService $aiChatService,
    ) {}

    public function index(): View
    {
        return view('ai-chat-test', $this->viewData());
    }

    public function store(Request $request): View
    {
        $validated = $request->validate([
            'prompt' => ['required', 'string', 'max:4000'],
        ]);

        $result = null;
        $error = null;

        try {
            $result = $this->aiChatService->send($validated['prompt']);
        } catch (Throwable $throwable) {
            report($throwable);
            $error = $throwable->getMessage();
        }

        return view('ai-chat-test', $this->viewData([
            'prompt' => $validated['prompt'],
            'result' => $result,
            'error' => $error,
        ]));
    }

    private function viewData(array $overrides = []): array
    {
        $provider = $this->aiChatService->configuredProvider();
        $model = $this->aiChatService->configuredModel();

        return array_merge([
            'configuredProvider' => $provider,
            'configuredModel' => $model,
            'prompt' => old('prompt', 'Reply in one short sentence confirming the AI provider connection and mention today\'s weekday.'),
            'result' => null,
            'error' => null,
        ], $overrides);
    }
}
