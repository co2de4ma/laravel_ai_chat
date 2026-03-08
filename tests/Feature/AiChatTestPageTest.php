<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiChatTestPageTest extends TestCase
{
    public function test_the_ai_chat_test_page_loads(): void
    {
        config([
            'services.ai.provider' => 'openai',
            'services.ai.openai.api_key' => 'test-openai-key',
            'services.ai.openai.model' => 'gpt-test-mini',
        ]);

        $response = $this->get('/ai-chat-test');

        $response
            ->assertOk()
            ->assertSee('AI Chat Test Page')
            ->assertSee('OPENAI')
            ->assertSee('gpt-test-mini');
    }

    public function test_it_renders_an_openai_response(): void
    {
        config([
            'services.ai.provider' => 'openai',
            'services.ai.openai.api_key' => 'test-openai-key',
            'services.ai.openai.model' => 'gpt-test-mini',
            'services.ai.openai.base_url' => 'https://api.openai.com/v1',
        ]);

        Http::fake([
            'https://api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'OpenAI connection works.',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->post('/ai-chat-test', [
            'prompt' => 'Confirm the provider connection.',
        ]);

        $response
            ->assertOk()
            ->assertSee('OpenAI connection works.')
            ->assertSee('Live result');
    }

    public function test_it_can_infer_openai_when_the_provider_is_not_explicitly_set(): void
    {
        config([
            'services.ai.provider' => null,
            'services.ai.openai.api_key' => 'test-openai-key',
            'services.ai.openai.model' => 'gpt-test-mini',
            'services.ai.openai.base_url' => 'https://api.openai.com/v1',
            'services.ai.anthropic.api_key' => null,
            'services.ai.gemini.api_key' => null,
        ]);

        Http::fake([
            'https://api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Provider inference works.',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->post('/ai-chat-test', [
            'prompt' => 'Confirm provider inference.',
        ]);

        $response
            ->assertOk()
            ->assertSee('Provider inference works.')
            ->assertSee('OPENAI');
    }
}
