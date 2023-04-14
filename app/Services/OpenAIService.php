<?php

namespace App\Services;

use Exception;
use Illuminate\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Orhanerday\OpenAi\OpenAi;

/**
 * Call the OpenAI service
 *
 * TODO:
 * - Check why the
 */
class OpenAIService
{
    protected const BYPASS_CACHE = true;
    protected const CACHE_STORE = 'open-ai';
    protected const OPEN_AI_MODEL = 'gpt-3.5-turbo';
    protected const OPEN_AI_TEMPERATURE = 0.80;
    protected const MAX_WORD_COUNT = 400;

    protected Repository $cache;

    public function __construct()
    {
        $this->cache = Cache::store(self::CACHE_STORE);
    }

    public function getFeedback(string $markdown, array $tags = []): string
    {
        $key = md5($markdown);

        if (! self::BYPASS_CACHE && $this->cache->has($key)) {
            $feedback = $this->cache->get($key);
        } else {
            $feedback = $this->getFeedbackFromOpenAI($markdown, $tags);
        }

        $this->cache->set($key, $feedback);

        return $feedback;
    }

    /**
     * Get feedback from OpenAI
     */
    private function getFeedbackFromOpenAI(string $markdown, array $tags, int $subMaxTokens = 0): string
    {
        $openAiClient = new OpenAi(config('openai.api_key'));

        $promtLines = [
            sprintf('Act as an editor knowledgable in: %s.', implode(', ', $tags)),
            sprintf('Provide 10 short suggestion to improve the following article: %s', $markdown),
        ];

        $content = implode(' ', $promtLines);

        $request = [
            'model' => self::OPEN_AI_MODEL,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $content
                ],
            ],
            'temperature' => self::OPEN_AI_TEMPERATURE,
            'max_tokens' => (4097 - str_word_count($content)) - $subMaxTokens,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
        ];

        $jsonResponse = $openAiClient->chat($request);

        $response = json_decode($jsonResponse, true);

        if (data_get($response, 'error.code') === 'context_length_exceeded') {
            return $this->getFeedbackFromOpenAI($markdown, $tags, $subMaxTokens += 100);
        }

        $answer = data_get($response, 'choices.0.message.content');
        if (!is_null($answer)) {
            return $answer;
        }

        throw new Exception(sprintf(
            "Something went wrong with OpenAI!\n\n %s",
            json_encode($response, JSON_PRETTY_PRINT)
        ));
    }
}
