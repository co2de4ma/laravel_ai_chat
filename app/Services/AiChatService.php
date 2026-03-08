<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AiChatService
{
    public function send(string $prompt): array
    {
        $provider = $this->resolveProvider();

        return match ($provider) {
            'openai' => $this->sendOpenAi($prompt),
            'anthropic' => $this->sendAnthropic($prompt),
            'gemini' => $this->sendGemini($prompt),
            default => throw new RuntimeException("Unsupported AI provider [{$provider}]."),
        };
    }

    public function configuredProvider(): ?string
    {
        try {
            return $this->resolveProvider();
        } catch (RuntimeException) {
            return null;
        }
    }

    public function configuredModel(): ?string
    {
        $provider = $this->configuredProvider();

        return $provider ? config("services.ai.{$provider}.model") : null;
    }

    private function sendOpenAi(string $prompt): array
    {
        $model = $this->providerConfig('openai', 'model');

        $response = Http::acceptJson()
            ->asJson()
            ->timeout(60)
            ->withToken($this->providerConfig('openai', 'api_key'))
            ->post($this->providerUrl('openai', '/chat/completions'), [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => config('services.ai.system_prompt'),
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
            ]);

        $payload = $this->decodeResponse($response);
        $text = trim((string) data_get($payload, 'choices.0.message.content', ''));

        return $this->buildResult('openai', $model, $text, $payload);
    }

    private function sendAnthropic(string $prompt): array
    {
        $model = $this->providerConfig('anthropic', 'model');

        $response = Http::acceptJson()
            ->asJson()
            ->timeout(60)
            ->withHeaders([
                'x-api-key' => $this->providerConfig('anthropic', 'api_key'),
                'anthropic-version' => '2023-06-01',
            ])
            ->post($this->providerUrl('anthropic', '/v1/messages'), [
                'model' => $model,
                'max_tokens' => 300,
                'system' => config('services.ai.system_prompt'),
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
            ]);

        $payload = $this->decodeResponse($response);
        $text = $this->collectTextBlocks(data_get($payload, 'content', []));

        return $this->buildResult('anthropic', $model, $text, $payload);
    }

    private function sendGemini(string $prompt): array
    {
        $model = $this->providerConfig('gemini', 'model');

        $response = Http::acceptJson()
            ->asJson()
            ->timeout(60)
            ->post($this->providerUrl('gemini', "/models/{$model}:generateContent"), [
                'systemInstruction' => [
                    'parts' => [
                        ['text' => config('services.ai.system_prompt')],
                    ],
                ],
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
            ]);

        $payload = $this->decodeResponse($response);
        $text = trim((string) collect(data_get($payload, 'candidates.0.content.parts', []))
            ->pluck('text')
            ->implode("\n\n"));

        return $this->buildResult('gemini', $model, $text, $payload);
    }

    private function resolveProvider(): string
    {
        $configuredProvider = strtolower(trim((string) config('services.ai.provider')));

        if ($configuredProvider !== '') {
            return $configuredProvider;
        }

        return match (true) {
            filled(config('services.ai.openai.api_key')) => 'openai',
            filled(config('services.ai.anthropic.api_key')) => 'anthropic',
            filled(config('services.ai.gemini.api_key')) => 'gemini',
            default => throw new RuntimeException(
                'No AI provider is configured. Set AI_PROVIDER or add an API key such as OPENAI_API_KEY.'
            ),
        };
    }

    private function providerConfig(string $provider, string $key): string
    {
        $value = trim((string) config("services.ai.{$provider}.{$key}"));

        if ($value === '') {
            throw new RuntimeException("Missing {$provider} configuration value [{$key}].");
        }

        return $value;
    }

    private function providerUrl(string $provider, string $path): string
    {
        $baseUrl = rtrim($this->providerConfig($provider, 'base_url'), '/');

        if ($provider === 'gemini') {
            $separator = str_contains($path, '?') ? '&' : '?';

            return "{$baseUrl}{$path}{$separator}key={$this->providerConfig('gemini', 'api_key')}";
        }

        return "{$baseUrl}{$path}";
    }

    private function decodeResponse(Response $response): array
    {
        if ($response->failed()) {
            $message = data_get($response->json(), 'error.message')
                ?? data_get($response->json(), 'error')
                ?? $response->body();

            throw new RuntimeException("AI request failed: {$message}");
        }

        $payload = $response->json();

        if (! is_array($payload)) {
            throw new RuntimeException('AI request returned an unexpected response payload.');
        }

        return $payload;
    }

    private function buildResult(string $provider, string $model, string $text, array $payload): array
    {
        if ($text === '') {
            throw new RuntimeException('AI request succeeded but returned no text content.');
        }

        return [
            'provider' => $provider,
            'model' => $model,
            'text' => $text,
            'raw' => $payload,
        ];
    }

    private function collectTextBlocks(array $blocks): string
    {
        return trim((string) collect($blocks)
            ->filter(fn (array $block): bool => data_get($block, 'type') === 'text')
            ->pluck('text')
            ->implode("\n\n"));
    }
}
