<?php
declare(strict_types=1);

namespace App\Modules\AI\Services;

use RuntimeException;
use Throwable;

/**
 * Groq OpenAI-compatible provider for ManipurApp Smart Ask.
 *
 * Tool execution remains inside ManipurApp/PHP. Groq produces a short user
 * message plus a tool plan; PHP executes the tools and renders the verified results.
 */
final class GroqService
{
    private array $config;

    public function __construct()
    {
        $this->config = $this->loadConfig();
        $key = trim((string)($this->config['api_key'] ?? ''));
        if ($key === '' || str_starts_with($key, 'YOUR_')) {
            throw new RuntimeException('Groq API key is not configured. Add it to app/Modules/AI/Config/groq.php.');
        }
    }

    public function runWithTools(
        string $question,
        array $tools,
        callable $executor,
        int $maxRounds = 5
    ): array {
        return $this->runPlanningRequest($question, $tools, $executor, null);
    }

    public function runWithToolsStreaming(
        string $question,
        array $tools,
        callable $executor,
        callable $progress,
        int $maxRounds = 5
    ): array {
        return $this->runPlanningRequest($question, $tools, $executor, $progress);
    }

    /**
     * One-shot Smart AI planner.
     *
     * Groq is asked for two things in a single request:
     *  - a short natural-language message for the user
     *  - the application tools it wants PHP to execute
     *
     * PHP then executes those tools locally. We intentionally do NOT send the
     * tool results back to Groq for a second/final generation pass.
     */
    private function runPlanningRequest(
        string $question,
        array $tools,
        callable $executor,
        ?callable $progress
    ): array {
        $question = trim($question);
        if ($question === '') throw new RuntimeException('Please enter a question.');

        $started = microtime(true);
        $model = (string)($this->config['model'] ?? 'openai/gpt-oss-120b');
        $groqTools = $this->normalizeTools($tools);

        if ($progress) {
            $progress(['type' => 'provider', 'provider' => 'groq', 'model' => $model, 'message' => 'Connected to Groq. Planning the Smart AI response…']);
            $progress(['type' => 'diagnostic', 'level' => 'info', 'phase' => 'provider_request_started', 'message' => 'Groq planning request started (one-shot). Waiting for message + tool calls…']);
        }

        $requestStarted = microtime(true);
        try {
            $response = $this->request([
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $this->planningInstruction($groqTools)],
                    ['role' => 'user', 'content' => $question],
                ],
                // Do not use Groq JSON-constrained response modes here.
                // Some models can reject otherwise valid planning requests with
                // 'Failed to generate JSON'. We ask for one plain-text JSON
                // object and validate/extract it in PHP instead.
                'max_tokens' => 700,
                'temperature' => 0.1,
            ]);
        } catch (Throwable $e) {
            if ($progress) {
                $progress(['type' => 'diagnostic', 'level' => 'error', 'phase' => 'provider_request_failed', 'message' => 'Groq API request failed after ' . (int)round((microtime(true) - $requestStarted) * 1000) . ' ms: ' . $e->getMessage()]);
            }
            throw $e;
        }

        $rate = (array)($response['_rate_limit'] ?? []);
        if ($progress) {
            $progress(['type' => 'diagnostic', 'level' => 'success', 'phase' => 'provider_response_received', 'message' => 'Groq returned the planning response after ' . (int)round((microtime(true) - $requestStarted) * 1000) . ' ms.']);
            if ($rate) {
                $progress(['type' => 'diagnostic', 'level' => 'info', 'phase' => 'rate_limit', 'message' => $this->formatRateLimitMessage($rate)]);
            }
        }

        $message = (array)($response['choices'][0]['message'] ?? []);
        $content = trim((string)($message['content'] ?? ''));
        if ($progress) {
            $keys = array_keys($message);
            $progress(['type' => 'diagnostic', 'level' => 'info', 'phase' => 'provider_message_shape', 'message' => 'Groq assistant message fields: ' . implode(', ', $keys) . '; content length: ' . strlen($content) . '.']);
        }
        if ($content === '') {
            $reasoning = trim((string)($message['reasoning'] ?? ''));
            $reasoningLen = strlen($reasoning);
            throw new RuntimeException('Groq returned no JSON content for the Smart AI plan (assistant fields: ' . implode(', ', array_keys($message)) . ', reasoning length: ' . $reasoningLen . ').');
        }

        $plan = $this->decodePlanningJson($content);
        if (!is_array($plan)) throw new RuntimeException('Groq returned an invalid Smart AI plan JSON.');

        $userMessage = trim((string)($plan['message'] ?? ''));
        if ($userMessage === '') throw new RuntimeException('Groq Smart AI plan did not include a user message.');
        if (mb_strlen($userMessage) > 300) $userMessage = mb_substr($userMessage, 0, 297) . '...';

        $requestedCalls = [];
        foreach ((array)($plan['tool_calls'] ?? []) as $call) {
            if (!is_array($call)) continue;
            $name = trim((string)($call['name'] ?? ''));
            if ($name === '') continue;
            $rawArguments = $call['arguments'] ?? '{}';
            if (is_string($rawArguments)) {
                $decodedArguments = json_decode($rawArguments, true);
                $arguments = is_array($decodedArguments) ? $decodedArguments : [];
            } else {
                $arguments = is_array($rawArguments) ? $rawArguments : [];
            }
            $requestedCalls[] = ['name' => $name, 'arguments' => $this->normalizeToolArguments($arguments)];
        }

        if ($progress) {
            $progress(['type' => 'diagnostic', 'level' => 'success', 'phase' => 'provider_response_parsed', 'message' => 'Groq plan parsed: ' . count($requestedCalls) . ' tool call(s), message present.']);
            $progress(['type' => 'answer_delta', 'text' => $userMessage]);
        }

        $toolResults = [];
        foreach ($requestedCalls as $call) {
            $name = $call['name'];
            $arguments = $call['arguments'];
            $toolStarted = microtime(true);
            if ($progress) {
                $progress(['type' => 'diagnostic', 'level' => 'info', 'phase' => 'tool_execution', 'message' => 'Groq requested application tool: ' . $name . '.']);
                $progress(['type' => 'tool_selected', 'name' => $name, 'message' => 'Groq selected ' . $name . '…']);
                $progress(['type' => 'tool_running', 'name' => $name, 'message' => 'Querying live ' . $this->toolLabel($name) . ' data…']);
            }
            try {
                $toolResult = $executor($name, $arguments);
                $toolResults[] = ['name' => $name, 'arguments' => $arguments, 'result' => $toolResult];
                if ($progress) {
                    $progress(['type' => 'tool_completed', 'name' => $name, 'records_found' => $this->countToolRecords($toolResult), 'latency_ms' => (int)round((microtime(true) - $toolStarted) * 1000), 'message' => $this->toolLabel($name) . ' data loaded.']);
                }
            } catch (Throwable $e) {
                $toolResults[] = ['name' => $name, 'arguments' => $arguments, 'error' => $e->getMessage()];
                if ($progress) {
                    $progress(['type' => 'diagnostic', 'level' => 'error', 'phase' => 'tool_execution_failed', 'message' => $name . ' failed: ' . $e->getMessage()]);
                }
            }
        }

        if ($progress) $progress(['type' => 'final_received', 'message' => 'Groq returned the Smart AI message and tool plan. No second Groq response was requested.']);

        return [
            'answer' => $userMessage,
            'message' => $userMessage,
            'tool_calls' => $requestedCalls,
            'results' => $toolResults,
            'model' => $model,
            'provider' => 'groq',
            'latency_ms' => (int)round((microtime(true) - $started) * 1000),
            'tool_rounds' => 1,
            'rate_limit' => $rate,
        ];
    }

    private function planningInstruction(array $groqTools): string
    {
        $catalog = [];
        foreach ($groqTools as $tool) {
            $fn = (array)($tool['function'] ?? []);
            $catalog[] = [
                'name' => (string)($fn['name'] ?? ''),
                'description' => (string)($fn['description'] ?? ''),
                'parameters' => (array)($fn['parameters'] ?? []),
            ];
        }
        return "You are the ManipurApp Smart Ask planner.\n\n" .
            "Return exactly ONE JSON object and nothing else.\n" .
            "The object must contain exactly these top-level keys: message, tool_calls.\n" .
            "message must be one short sentence (maximum 220 characters) describing what ManipurApp will show. Do not name records, prices, IDs, URLs, or factual results.\n" .
            "tool_calls must be an array. Each item must have name and arguments. arguments must be a JSON object.\n" .
            "Choose all tools needed for the user's request. Do not execute tools yourself and do not invent data.\n" .
            "Available tools:\n" . json_encode($catalog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) .
            "\nIf no tools are needed, use tool_calls: [].";
    }

    /**
     * Decode the planner's JSON without relying on provider-side constrained
     * decoding. Accept a plain JSON object or a fenced ```json block.
     */
    private function decodePlanningJson(string $content): ?array
    {
        $content = trim($content);
        $decoded = json_decode($content, true);
        if (is_array($decoded)) return $decoded;

        if (preg_match('/```(?:json)?\s*(\{.*\})\s*```/is', $content, $m)) {
            $decoded = json_decode(trim($m[1]), true);
            if (is_array($decoded)) return $decoded;
        }

        $start = strpos($content, '{');
        $end = strrpos($content, '}');
        if ($start !== false && $end !== false && $end > $start) {
            $candidate = substr($content, $start, $end - $start + 1);
            $decoded = json_decode($candidate, true);
            if (is_array($decoded)) return $decoded;
        }

        return null;
    }

    private function formatRateLimitMessage(array $rate): string
    {
        $remaining = $rate['remaining_tokens'] ?? null;
        $limit = $rate['limit_tokens'] ?? null;
        $reset = (string)($rate['reset_tokens'] ?? '');
        $parts = [];
        if ($remaining !== null && $limit !== null) $parts[] = 'TPM remaining: ' . $remaining . ' / ' . $limit;
        elseif ($remaining !== null) $parts[] = 'TPM remaining: ' . $remaining;
        if ($reset !== '') $parts[] = 'resets in ' . $reset;
        $requestRemaining = $rate['remaining_requests'] ?? null;
        if ($requestRemaining !== null) $parts[] = 'daily requests remaining: ' . $requestRemaining;
        return $parts ? 'Groq rate limits — ' . implode(' · ', $parts) . '.' : 'Groq rate-limit headers were not available.';
    }

    private function normalizeTools(array $tools): array
    {
        $out = [];
        foreach ($tools as $tool) {
            if (($tool['type'] ?? '') !== 'function') continue;

            $parameters = (array)($tool['parameters'] ?? ['type' => 'object']);
            $parameters = $this->makeOptionalPropertiesNullable($parameters);

            $out[] = [
                'type' => 'function',
                'function' => [
                    'name' => (string)($tool['name'] ?? ''),
                    'description' => (string)($tool['description'] ?? ''),
                    'parameters' => $parameters,
                ],
            ];
        }
        return $out;
    }

    /**
     * Groq validates generated tool arguments against the JSON schema before
     * returning the tool call. Some models naturally emit null for an optional
     * parameter instead of omitting it. Allow both forms for optional scalar
     * properties while leaving required properties strict.
     */
    private function makeOptionalPropertiesNullable(array $schema): array
    {
        if (($schema['type'] ?? '') !== 'object' || !isset($schema['properties']) || !is_array($schema['properties'])) {
            return $schema;
        }

        $required = array_fill_keys(array_map('strval', (array)($schema['required'] ?? [])), true);
        foreach ($schema['properties'] as $name => $property) {
            if (!is_array($property) || isset($required[(string)$name])) continue;
            $type = $property['type'] ?? null;
            if (is_string($type) && in_array($type, ['string', 'number', 'integer', 'boolean'], true)) {
                $property['type'] = [$type, 'null'];
                $schema['properties'][$name] = $property;
            }
        }
        return $schema;
    }

    /**
     * Normalize model-generated null optional arguments before the PHP tool
     * executes. Omitted and explicit-null optional values have the same meaning
     * to ManipurApp tools.
     */
    private function normalizeToolArguments(array $arguments): array
    {
        foreach ($arguments as $key => $value) {
            if ($value === null) unset($arguments[$key]);
        }
        return $arguments;
    }

    private function request(array $payload): array
    {
        $endpoint = (string)($this->config['endpoint'] ?? 'https://api.groq.com/openai/v1/chat/completions');
        $key = trim((string)($this->config['api_key'] ?? ''));
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($body === false) throw new RuntimeException('Unable to encode Groq request.');

        $headers = [];
        $ch = curl_init($endpoint);
        if ($ch === false) throw new RuntimeException('Unable to initialize cURL for Groq.');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $key,
            ],
            CURLOPT_HEADERFUNCTION => static function ($curl, string $headerLine) use (&$headers): int {
                $parts = explode(':', $headerLine, 2);
                if (count($parts) === 2) $headers[strtolower(trim($parts[0]))] = trim($parts[1]);
                return strlen($headerLine);
            },
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_TIMEOUT => min(120, max(10, (int)($this->config['timeout'] ?? 60))),
        ]);
        $raw = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($raw === false) throw new RuntimeException('Groq request failed: ' . ($error !== '' ? $error : 'Unknown cURL error.'));
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) throw new RuntimeException('Groq returned an invalid JSON response.');

        $decoded['_rate_limit'] = [
            'limit_tokens' => isset($headers['x-ratelimit-limit-tokens']) ? (int)$headers['x-ratelimit-limit-tokens'] : null,
            'remaining_tokens' => isset($headers['x-ratelimit-remaining-tokens']) ? (int)$headers['x-ratelimit-remaining-tokens'] : null,
            'reset_tokens' => $headers['x-ratelimit-reset-tokens'] ?? null,
            'limit_requests' => isset($headers['x-ratelimit-limit-requests']) ? (int)$headers['x-ratelimit-limit-requests'] : null,
            'remaining_requests' => isset($headers['x-ratelimit-remaining-requests']) ? (int)$headers['x-ratelimit-remaining-requests'] : null,
            'reset_requests' => $headers['x-ratelimit-reset-requests'] ?? null,
            'retry_after' => $headers['retry-after'] ?? null,
        ];

        if ($code < 200 || $code >= 300) {
            $message = (string)($decoded['error']['message'] ?? 'Unknown Groq API error.');
            $rate = $decoded['_rate_limit'];
            $extra = '';
            if ($rate['remaining_tokens'] !== null) $extra .= ' TPM remaining: ' . $rate['remaining_tokens'] . '/' . ($rate['limit_tokens'] ?? '?') . '.';
            if ($rate['retry_after'] !== null) $extra .= ' Retry after: ' . $rate['retry_after'] . 's.';
            throw new RuntimeException('Groq API error (' . $code . '): ' . $message . $extra);
        }
        return $decoded;
    }

    private function systemInstruction(): string
    {
        return $this->planningInstruction($this->normalizeTools([]));
    }

    private function toolLabel(string $name): string
    {
        return match ($name) {
            'search_tourism' => 'tourism',
            'search_restaurants' => 'restaurant',
            'search_taxi' => 'taxi',
            default => $name,
        };
    }

    private function countToolRecords(array $data): int
    {
        if (isset($data['records_found'])) return (int)$data['records_found'];
        $count = 0;
        foreach ((array)($data['record_counts'] ?? []) as $n) $count += (int)$n;
        foreach ((array)($data['records'] ?? []) as $rows) if (is_array($rows)) $count += count($rows);
        foreach ((array)($data['restaurants'] ?? []) as $row) if (is_array($row)) $count++;
        foreach ((array)($data['services'] ?? []) as $row) if (is_array($row)) $count++;
        return $count;
    }

    /**
     * Keep the provider context small. Groq's on-demand free/developer limits can
     * reject an otherwise valid tool-calling round when accumulated tool results
     * are too large. The full tool result remains available to PHP/UI; only the
     * copy sent back to the model is compacted.
     */
    private function encodeCompactToolResult(mixed $result, string $toolName): string
    {
        $maxBytes = max(2500, (int)($this->config['max_tool_result_bytes'] ?? 5000));
        $compact = $this->compactToolResult($result, $toolName);
        $json = json_encode($compact, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
        if ($json === false) return '{"error":"Unable to encode tool result."}';
        if (strlen($json) <= $maxBytes) return $json;

        // Last-resort deterministic trimming. Preserve the metadata and the first
        // records so the model still has useful grounding rather than a truncated
        // JSON document that cannot be parsed.
        $fallback = [
            'tool' => $compact['tool'] ?? $toolName,
            'query' => $compact['query'] ?? null,
            'records_found' => $compact['records_found'] ?? null,
            'record_counts' => $compact['record_counts'] ?? null,
            'records' => [],
            'matching_menu_items' => [],
            'note' => 'Result was compacted for model context limits. Use the returned records as the available live evidence.'
        ];
        foreach ((array)($compact['records'] ?? []) as $key => $rows) {
            if (is_array($rows) && array_is_list($rows)) {
                foreach (array_slice($rows, 0, 3) as $row) $fallback['records'][$key][] = $row;
            } elseif (is_array($rows)) {
                $fallback['records'][$key] = $rows;
            }
        }
        foreach (array_slice((array)($compact['restaurants'] ?? []), 0, 4) as $row) {
            $fallback['restaurants'][] = $row;
        }
        foreach (array_slice((array)($compact['matching_menu_items'] ?? []), 0, 8) as $row) {
            $fallback['matching_menu_items'][] = $row;
        }
        $json = json_encode($fallback, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
        if ($json === false) return '{"error":"Unable to encode compact tool result."}';
        return strlen($json) > $maxBytes ? substr($json, 0, $maxBytes - 64) . '..."}' : $json;
    }

    private function compactToolResult(mixed $result, string $toolName): mixed
    {
        if (!is_array($result)) return $result;

        if ($toolName === 'search_restaurants') {
            $out = [
                'tool' => $result['tool'] ?? $toolName,
                'query' => $result['query'] ?? null,
                'records_found' => $result['records_found'] ?? 0,
                'restaurants' => [],
                'matching_menu_items' => [],
            ];
            foreach (array_slice((array)($result['restaurants'] ?? []), 0, 6) as $r) {
                $out['restaurants'][] = [
                    'id' => $r['id'] ?? null,
                    'name' => $r['name'] ?? null,
                    'city' => $r['city'] ?? null,
                    'district' => $r['district'] ?? null,
                    'cuisine_type' => $r['cuisine_type'] ?? null,
                    'description' => isset($r['description']) ? mb_substr((string)$r['description'], 0, 180) : null,
                    'minimum_order_amount' => $r['minimum_order_amount'] ?? null,
                    'delivery_available' => $r['delivery_available'] ?? null,
                    'pickup_available' => $r['pickup_available'] ?? null,
                    'delivery_fee' => $r['delivery_fee'] ?? null,
                    'estimated_prep_minutes' => $r['estimated_prep_minutes'] ?? null,
                    '_ai' => $r['_ai'] ?? null,
                ];
            }
            foreach (array_slice((array)($result['matching_menu_items'] ?? []), 0, 12) as $item) {
                $out['matching_menu_items'][] = [
                    'id' => $item['id'] ?? null,
                    'restaurant_id' => $item['restaurant_id'] ?? null,
                    'restaurant_name' => $item['restaurant_name'] ?? null,
                    'name' => $item['name'] ?? null,
                    'description' => isset($item['description']) ? mb_substr((string)$item['description'], 0, 140) : null,
                    'price' => $item['price'] ?? null,
                    'is_veg' => $item['is_veg'] ?? null,
                    'category_name' => $item['category_name'] ?? null,
                    '_ai' => $item['_ai'] ?? null,
                ];
            }
            return $out;
        }

        if ($toolName === 'search_tourism') {
            $out = [
                'tool' => $result['tool'] ?? $toolName,
                'query' => $result['query'] ?? null,
                'record_counts' => $result['record_counts'] ?? [],
                'records' => [],
            ];
            foreach ((array)($result['records'] ?? []) as $scope => $rows) {
                $out['records'][$scope] = [];
                foreach (array_slice((array)$rows, 0, 5) as $r) {
                    if (!is_array($r)) continue;
                    $keep = [];
                    foreach (['id','name','title','slug','city','district','state','description','starting_price_per_night','base_price','discount_price','price_per_day','duration_days','duration_nights','latitude','longitude','status','_ai'] as $k) {
                        if (array_key_exists($k, $r)) $keep[$k] = is_string($r[$k]) ? mb_substr($r[$k], 0, 220) : $r[$k];
                    }
                    if (!$keep) $keep = array_slice($r, 0, 10, true);
                    $out['records'][$scope][] = $keep;
                }
            }
            return $out;
        }

        if ($toolName === 'search_taxi') {
            // Taxi results can also contain verbose metadata. Keep the model-facing
            // representation focused on planning facts and clickable identifiers.
            $out = ['tool' => $result['tool'] ?? $toolName, 'query' => $result['query'] ?? null];
            foreach (['records_found','record_counts'] as $k) if (array_key_exists($k, $result)) $out[$k] = $result[$k];
            foreach (['services','records'] as $bucket) {
                if (!isset($result[$bucket]) || !is_array($result[$bucket])) continue;
                $out[$bucket] = [];
                foreach (array_slice($result[$bucket], 0, 8) as $r) {
                    if (!is_array($r)) continue;
                    $keep=[];
                    foreach (['id','name','service_type','vehicle_type','seats','capacity','base_fare','price','fare','estimated_fare','city','district','_ai'] as $k) if (array_key_exists($k,$r)) $keep[$k]=$r[$k];
                    $out[$bucket][]=$keep ?: array_slice($r,0,10,true);
                }
            }
            return $out;
        }
        return $result;
    }

    private function encodeToolResult(mixed $result): string
    {
        $json = json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
        if ($json === false) return '{"error":"Unable to encode tool result."}';
        if (strlen($json) > 60000) $json = substr($json, 0, 60000) . '...';
        return $json;
    }

    private function loadConfig(): array
    {
        $local = dirname(__DIR__) . '/Config/groq.php';
        if (is_file($local)) {
            $c = require $local;
            if (is_array($c)) return $c;
        }
        return [];
    }
}
