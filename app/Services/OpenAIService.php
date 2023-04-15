<?php

namespace App\Services;

use Exception;
use Illuminate\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Orhanerday\OpenAi\OpenAi;

/**
 * Call the OpenAI service
 */
class OpenAIService
{
    protected const BYPASS_CACHE = false;

    protected const CACHE_STORE = 'open-ai';

    protected const OPEN_AI_MODEL = 'gpt-3.5-turbo';

    protected const OPEN_AI_TEMPERATURE = 0.80;

    protected const MAX_WORD_COUNT = 400;

    protected Repository $cache;

    protected OpenAi $openAI;

    public function __construct()
    {
        $this->cache = Cache::store(self::CACHE_STORE);

        $this->openAI = new OpenAi(config('openai.api_key'));
    }

    public function getFeedback(string $markdown, array $tags = []): string
    {
        $key = sprintf('feedback-%s', md5($markdown));

        if (! self::BYPASS_CACHE && $this->cache->has($key)) {
            $feedback = $this->cache->get($key);
        } else {
            $feedback = $this->getFeedbackFromOpenAI($markdown, $tags);
        }

        $this->cache->set($key, $feedback);

        return $feedback;
    }

    public function getTitleSuggestions(string $markdown, array $tags = []): string
    {
        $key = sprintf('suggestions-%s', md5($markdown));

        if (! self::BYPASS_CACHE && $this->cache->has($key)) {
            $feedback = $this->cache->get($key);
        } else {
            $feedback = $this->getTitleSuggestionsFromOpenAI($markdown, $tags);
        }

        $this->cache->set($key, $feedback);

        return $feedback;
    }

    private function getFeedbackFromOpenAI(string $markdown, array $tags, int $subMaxTokens = 0): string
    {
        $promtLines = [
            sprintf('Act as an editor knowledgable in: %s.', implode(', ', $tags)),
            sprintf('Provide 10 short suggestion to improve the following article: %s', $markdown),
        ];

        $prompt = implode(' ', $promtLines);

        return $this->callOpenAI($prompt);
    }

    private function getTitleSuggestionsFromOpenAI(string $markdown, array $tags, int $subMaxTokens = 0): string
    {
        $promtLines = [
            sprintf('Act as an editor knowledgable in: %s.', implode(', ', $tags)),
            sprintf('Generate 3 titles with subtitles to make the following article trend: %s', $markdown),
        ];

        $prompt = implode(' ', $promtLines);

        return $this->callOpenAI($prompt);
    }

    private function callOpenAI(string $prompt, int $subMaxTokens = 0): string
    {
        $request = [
            'model' => self::OPEN_AI_MODEL,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $prompt,
                ],
            ],
            'temperature' => self::OPEN_AI_TEMPERATURE,
            'max_tokens' => (4097 - str_word_count($prompt)) - $subMaxTokens,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
        ];

        $jsonResponse = $this->openAI->chat($request);

        $response = json_decode($jsonResponse, true);

        if (data_get($response, 'error.code') === 'context_length_exceeded') {
            return $this->callOpenAI($prompt, $subMaxTokens += 100);
        }

        $answer = data_get($response, 'choices.0.message.content');
        if (! is_null($answer)) {
            return $answer;
        }

        throw new Exception(sprintf(
            "Something went wrong with OpenAI!\n\n %s",
            json_encode($response, JSON_PRETTY_PRINT)
        ));
    }
}
