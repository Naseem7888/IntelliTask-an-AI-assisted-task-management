<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected string $apiUrl;
    protected string $model;
    protected int $timeout;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->apiUrl = rtrim(config('services.gemini.api_url', 'https://generativelanguage.googleapis.com'), '/');
        $this->model = config('services.gemini.model', 'gemini-pro');
        $this->timeout = (int) config('services.gemini.timeout', 30);
        if (!$this->apiKey) {
            throw new Exception('Gemini API key not found. Please set it in your .env file.');
        }
    }

    /**
     * Generates a task breakdown using the Gemini API.
     *
     * @param string $taskDescription
     * @return array
     * @throws Exception
     */
    public function generateTaskBreakdown(string $taskDescription): array
    {
        $prompt = $this->formatTaskPrompt($taskDescription);
        $response = $this->callGeminiAPI($prompt);

        return $this->parseAIResponse($response);
    }

    /**
     * Formats the prompt for the Gemini API.
     *
     * @param string $description
     * @return string
     */
    protected function formatTaskPrompt(string $description): string
    {
        return "Break down the following task into a list of smaller, actionable sub-tasks. Return the list as a simple JSON array of strings. Task: \"{$description}\"";
    }

    /**
     * Calls the Gemini API with retry logic.
     *
     * @param string $prompt
     * @return array
     * @throws Exception
     */
    protected function callGeminiAPI(string $prompt): array
    {
        $endpoint = sprintf('%s/v1beta/models/%s:generateContent?key=%s', $this->apiUrl, $this->model, $this->apiKey);

        $response = Http::timeout($this->timeout)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($endpoint, [
            'contents' => [['parts' => [['text' => $prompt]]]]
        ]);

        if ($response->failed()) {
            Log::error('Gemini API request failed', ['response' => $response->body()]);
            throw new Exception('Failed to communicate with the Gemini API.');
        }

        return $response->json();
    }

    /**
     * Parses the JSON response from the Gemini API.
     *
     * @param array $response
     * @return array
     */
    protected function parseAIResponse(array $response): array
    {
        // Gemini generateContent shape: candidates[0].content.parts[0].text
        $text = $response['candidates'][0]['content']['parts'][0]['text']
            ?? $response['candidates'][0]['output_text']
            ?? '[]';
        $jsonText = trim($text, " \t\n\r\0\x0B`");
        $jsonText = str_replace(['json', "\n"], '', $jsonText);
        $decoded = json_decode($jsonText, true) ?: [];

        // Normalize to an array of strings
        if (is_array($decoded)) {
            $decoded = array_values(array_map(function ($item) {
                return is_array($item) ? ($item['title'] ?? json_encode($item)) : (string) $item;
            }, $decoded));
        }

        return $decoded;
    }
}


