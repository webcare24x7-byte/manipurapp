<?php
declare(strict_types=1);

namespace App\Modules\AI\Services;

use RuntimeException;

final class GeminiService
{
    private array $config;

    public function __construct()
    {
        $this->config = $this->loadConfig();
        $key = trim((string)($this->config['api_key'] ?? ''));
        if ($key === '' || $key === 'YOUR_GEMINI_API_KEY_HERE' || $key === 'YOUR_ACTUAL_GEMINI_API_KEY') {
            throw new RuntimeException('Gemini API key is not configured. Configure the existing Taxi/Config/gemini.php or AI/Config/gemini.php.');
        }
    }

    public function answer(string $question, string $scope = 'GENERAL', array $databaseContext = []): array
    {
        $question = trim($question);
        if ($question === '') {
            throw new RuntimeException('Please enter a question.');
        }
        if (mb_strlen($question) > 8000) {
            throw new RuntimeException('Question is too long. Please keep it under 8,000 characters.');
        }

        $contextJson = json_encode($databaseContext, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($contextJson === false) {
            throw new RuntimeException('Unable to prepare database context for Gemini.');
        }

        $prompt = <<<PROMPT
You are ManipurApp AI for the platform administrator.

Your job is to answer using the supplied ManipurApp database context when it is relevant.

IMPORTANT RULES:
1. The database context is the source of truth for live ManipurApp records.
2. Never invent a business, restaurant, menu item, destination, stay, package, guide, experience, event, price, availability, rating, verification status, or other database fact.
3. If the database context contains no matching records, clearly say that no matching records were found in the selected ManipurApp category.
4. You may provide general planning advice when useful, but clearly distinguish it from database-backed facts.
5. Do not claim that something is booked, available, verified, or completed unless the supplied data explicitly supports it.
6. For prices, quote the exact database value and explain the pricing unit when known.
7. Keep the answer practical and concise. If the user asks for an itinerary, use only database-backed options for the selected category when possible.
8. The selected ManipurApp category is {$scope}. For Restaurant categories, treat restaurant profiles, menu items, vegetarian flags, prices, variants, modifiers, delivery/pickup flags and cuisine data as database-backed facts when supplied.

ADMIN QUESTION:
{$question}

MANIPURAPP DATABASE CONTEXT:
{$contextJson}
PROMPT;

        $payload = [
            'model' => (string)($this->config['model'] ?? 'gemini-3.5-flash'),
            'input' => [['type' => 'text', 'text' => $prompt]],
            'response_format' => ['type' => 'text', 'mime_type' => 'text/plain'],
        ];

        $started = microtime(true);
        $response = $this->request($payload);
        $answer = $this->extractText($response);

        if ($answer === '') {
            throw new RuntimeException('Gemini completed the request but returned no answer.');
        }

        return [
            'answer' => $answer,
            'model' => (string)($this->config['model'] ?? ''),
            'latency_ms' => (int)round((microtime(true) - $started) * 1000),
        ];
    }

    private function loadConfig(): array
    {
        $moduleRoot = dirname(__DIR__, 2);
        $taxi = $moduleRoot . '/Taxi/Config/gemini.php';
        if (is_file($taxi)) {
            $c = require $taxi;
            if (is_array($c) && trim((string)($c['api_key'] ?? '')) !== '') return $c;
        }

        $member = $moduleRoot . '/MemberApp/Config/gemini.php';
        if (is_file($member)) {
            $c = require $member;
            if (is_array($c) && trim((string)($c['api_key'] ?? '')) !== '') return $c;
        }

        $local = dirname(__DIR__) . '/Config/gemini.php';
        if (is_file($local)) {
            $c = require $local;
            if (is_array($c)) return $c;
        }

        return [];
    }

    private function request(array $payload): array
    {
        $endpoint = (string)$this->config['endpoint'];
        $key = (string)$this->config['api_key'];
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($body === false) throw new RuntimeException('Unable to encode Gemini request.');

        $ch = curl_init($endpoint);
        if ($ch === false) throw new RuntimeException('Unable to initialize cURL.');

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'x-goog-api-key: ' . $key],
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_TIMEOUT => (int)($this->config['timeout'] ?? 120),
        ]);

        $raw = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($raw === false) throw new RuntimeException('Gemini request failed: ' . $error);

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) throw new RuntimeException('Gemini returned an invalid response.');

        if ($code < 200 || $code >= 300) {
            throw new RuntimeException('Gemini API error (' . $code . '): ' . ($decoded['error']['message'] ?? 'Unknown API error.'));
        }

        return $decoded;
    }

    private function extractText(array $response): string
    {
        $parts = [];
        foreach (($response['steps'] ?? []) as $step) {
            if (($step['type'] ?? '') !== 'model_output') continue;
            foreach (($step['content'] ?? []) as $content) {
                if (($content['type'] ?? '') === 'text') {
                    $t = trim((string)($content['text'] ?? ''));
                    if ($t !== '') $parts[] = $t;
                }
            }
        }
        return trim(implode("\n\n", $parts));
    }
}
