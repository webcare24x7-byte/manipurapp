<?php
declare(strict_types=1);

namespace App\Modules\AI\Services;

use RuntimeException;
use Throwable;

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

    /**
     * Run a free-form interaction with application-side function tools.
     *
     * The model may call one or more read-only tools. PHP executes those tools
     * against the tenant database, then sends the function results back through
     * previous_interaction_id until Gemini produces the final answer.
     */
    public function runWithTools(
        string $question,
        array $tools,
        callable $executor,
        int $maxRounds = 5
    ): array {
        $question = trim($question);
        if ($question === '') {
            throw new RuntimeException('Please enter a question.');
        }

        $started = microtime(true);
        $model = (string)($this->config['model'] ?? 'gemini-3.5-flash');
        $systemInstruction = <<<'PROMPT'
You are ManipurApp Smart Ask, a travel-planning assistant for the ManipurApp platform.

Your job is to answer the user's free-form question by using the available ManipurApp application tools when the answer depends on live platform data.

CRITICAL RULES:
1. Treat tool results as the source of truth for ManipurApp live records.
2. Never invent a restaurant, menu item, destination, stay, package, guide, experience, event, taxi service, vehicle, price, configured fare, or other platform record.
3. You may combine results from multiple tools in one answer. For example, a trip-planning question may require tourism + restaurants + taxi.
4. If a tool returns no matching records, say so. Do not fill the gap by inventing a platform record.
5. Distinguish database-backed facts from general travel suggestions.
6. Taxi service pricing is configured pricing, not a promise of real-time availability. Never claim that a ride is booked or currently available unless the tool explicitly says so.
7. Do not expose private customer bookings, member information, credentials, internal implementation details, or raw SQL.
8. Keep the final answer practical and easy to understand. When useful, organize it as a short itinerary or plan.
9. When a relevant record contains an application link/path, use the record's supplied path as the basis for the action-oriented reference; never invent an ID or route.
10. The current platform focus is Manipur tourism, food, and transport.
PROMPT;

        $payload = [
            'model' => $model,
            'input' => $question,
            'system_instruction' => $systemInstruction,
            'tools' => $tools,
            'generation_config' => [
                'tool_choice' => 'auto',
                'thinking_level' => 'low',
                'max_output_tokens' => 1200,
            ],
        ];

        $response = $this->request($payload);
        $round = 0;
        $lastResponse = $response;

        while ($round < $maxRounds) {
            $calls = $this->functionCalls($lastResponse);
            if (!$calls) break;

            $results = [];
            foreach ($calls as $call) {
                $name = (string)($call['name'] ?? '');
                $callId = (string)($call['id'] ?? '');
                $arguments = $call['arguments'] ?? [];
                if (is_string($arguments)) {
                    $decoded = json_decode($arguments, true);
                    $arguments = is_array($decoded) ? $decoded : [];
                }
                if (!is_array($arguments)) $arguments = [];

                try {
                    $toolResult = $executor($name, $arguments);
                    $results[] = [
                        'type' => 'function_result',
                        'name' => $name,
                        'call_id' => $callId,
                        'result' => [
                            [
                                'type' => 'text',
                                'text' => $this->encodeToolResult($toolResult),
                            ],
                        ],
                    ];
                } catch (Throwable $e) {
                    $results[] = [
                        'type' => 'function_result',
                        'name' => $name,
                        'call_id' => $callId,
                        'is_error' => true,
                        'result' => [
                            [
                                'type' => 'text',
                                'text' => $this->encodeToolResult([
                                    'error' => $e->getMessage(),
                                ]),
                            ],
                        ],
                    ];
                }
            }

            $lastResponse = $this->request([
                'model' => $model,
                'previous_interaction_id' => (string)($lastResponse['id'] ?? ''),
                'input' => $results,
                'system_instruction' => $systemInstruction,
                'tools' => $tools,
                'generation_config' => [
                    'tool_choice' => 'auto',
                    'thinking_level' => 'low',
                    'max_output_tokens' => 1200,
                ],
            ]);
            $round++;
        }

        $status = (string)($lastResponse['status'] ?? '');
        if ($status === 'failed') {
            $message = '';
            foreach ((array)($lastResponse['errors'] ?? []) as $error) {
                $message = trim((string)($error['message'] ?? ''));
                if ($message !== '') break;
            }
            throw new RuntimeException('Gemini interaction failed' . ($message !== '' ? ': ' . $message : '.'));
        }

        $answer = $this->extractText($lastResponse);
        if ($answer === '') {
            $statusText = $status !== '' ? ' Status: ' . $status . '.' : '';
            throw new RuntimeException('Gemini completed the tool workflow but returned no final answer.' . $statusText);
        }

        return [
            'answer' => $answer,
            'model' => $model,
            'latency_ms' => (int)round((microtime(true) - $started) * 1000),
            'interaction_id' => (string)($lastResponse['id'] ?? ''),
            'tool_rounds' => $round,
        ];
    }

    /**
     * Streaming variant for the admin Smart Ask UI. Progress events are emitted
     * as soon as Gemini reports a function call and as soon as each application
     * tool finishes. The final answer is streamed as text deltas.
     */
    public function runWithToolsStreaming(
        string $question,
        array $tools,
        callable $executor,
        callable $progress,
        int $maxRounds = 5
    ): array {
        $question = trim($question);
        if ($question === '') throw new RuntimeException('Please enter a question.');

        $started = microtime(true);
        $model = (string)($this->config['model'] ?? 'gemini-3.5-flash');
        $systemInstruction = <<<'PROMPT'
You are ManipurApp Smart Ask, a travel-planning assistant for the ManipurApp platform.
Use the available ManipurApp application tools whenever the answer depends on live platform data.
Never invent platform records, prices, configured fares, availability, restaurants, destinations, or taxi services.
You may combine results from multiple tools. Distinguish database-backed facts from general advice.
Never expose private customer data, credentials, or raw SQL.
Keep the final answer practical and easy to understand.
PROMPT;

        $generationConfig = [
            'tool_choice' => 'auto',
            'thinking_level' => 'low',
            'max_output_tokens' => 1200,
        ];

        $round = 0;
        $lastInteractionId = '';
        $finalAnswer = '';
        $allToolCalls = [];

        while ($round < $maxRounds) {
            $isFirst = ($round === 0);
            $payload = [
                'model' => $model,
                'stream' => true,
                'generation_config' => $generationConfig,
            ];

            if ($isFirst) {
                $payload['input'] = $question;
                $payload['system_instruction'] = $systemInstruction;
                $payload['tools'] = $tools;
            } else {
                // Every resumed interaction is its own generation request. Re-send
                // interaction-scoped configuration (tools, system instruction and
                // generation config) so Gemini can continue selecting tools if needed.
                $payload['previous_interaction_id'] = $lastInteractionId;
                $payload['input'] = $allToolCalls['results'];
                $payload['system_instruction'] = $systemInstruction;
                $payload['tools'] = $tools;
                $payload['generation_config'] = $generationConfig;
                $progress(['type' => 'gemini_resume', 'message' => 'Gemini is processing the live tool results…']);
            }

            $functionCalls = [];
            $currentCalls = [];
            $roundText = '';
            $requestStarted = microtime(true);
            $progress([
                'type' => 'diagnostic',
                'level' => 'info',
                'phase' => 'provider_request_started',
                'message' => 'Gemini streaming API request started (round ' . ($round + 1) . '). Waiting for Gemini response…',
            ]);
            try {
                $completed = $this->streamRequest($payload, function (array $event) use (&$functionCalls, &$currentCalls, &$roundText, &$lastInteractionId, $progress): void {
                $type = (string)($event['event_type'] ?? '');

                if ($type === 'error') {
                    $message = (string)($event['error']['message'] ?? 'Gemini streaming error.');
                    throw new RuntimeException($message);
                }

                if ($type === 'interaction.created') {
                    $lastInteractionId = (string)($event['interaction']['id'] ?? '');
                    $progress(['type' => 'interaction', 'message' => 'Connected to Gemini.']);
                    return;
                }

                if ($type === 'step.start') {
                    $step = (array)($event['step'] ?? []);
                    if (($step['type'] ?? '') === 'function_call') {
                        $index = (int)($event['index'] ?? count($currentCalls));
                        $currentCalls[$index] = [
                            'id' => (string)($step['id'] ?? ''),
                            'name' => (string)($step['name'] ?? ''),
                            'arguments_json' => '',
                            'arguments' => $step['arguments'] ?? [],
                        ];
                        $progress([
                            'type' => 'tool_selected',
                            'name' => $currentCalls[$index]['name'],
                            'message' => 'Gemini selected ' . $currentCalls[$index]['name'] . '…',
                        ]);
                    }
                    return;
                }

                if ($type === 'step.delta') {
                    $delta = (array)($event['delta'] ?? []);
                    if (($delta['type'] ?? '') === 'arguments_delta') {
                        $index = (int)($event['index'] ?? 0);
                        if (isset($currentCalls[$index])) {
                            $currentCalls[$index]['arguments_json'] .= (string)($delta['arguments'] ?? '');
                        }
                    } elseif (($delta['type'] ?? '') === 'text') {
                        $text = (string)($delta['text'] ?? '');
                        if ($text !== '') {
                            $roundText .= $text;
                            $progress(['type' => 'answer_delta', 'text' => $text]);
                        }
                    }
                    return;
                }

                if ($type === 'interaction.completed') {
                    $lastInteractionId = (string)($event['interaction']['id'] ?? $lastInteractionId);
                    $status = (string)($event['interaction']['status'] ?? '');
                    $progress([
                        'type' => 'interaction_completed',
                        'status' => $status,
                        'message' => $status === 'requires_action'
                            ? 'Gemini requested application tool results.'
                            : ($status === 'completed' ? 'Gemini completed this step.' : 'Gemini interaction status: ' . $status),
                    ]);
                }
                });
                $progress([
                    'type' => 'diagnostic',
                    'level' => 'success',
                    'phase' => 'provider_response_received',
                    'message' => 'Gemini streaming API response completed after ' . (int)round((microtime(true) - $requestStarted) * 1000) . ' ms.',
                ]);
            } catch (Throwable $e) {
                $progress([
                    'type' => 'diagnostic',
                    'level' => 'error',
                    'phase' => 'provider_request_failed',
                    'message' => 'Gemini streaming API request failed after ' . (int)round((microtime(true) - $requestStarted) * 1000) . ' ms: ' . $e->getMessage(),
                ]);
                throw $e;
            }

            $status = (string)($completed['interaction']['status'] ?? '');
            if ($status === 'failed') {
                $message = '';
                foreach ((array)($completed['interaction']['errors'] ?? []) as $error) {
                    $message = trim((string)($error['message'] ?? ''));
                    if ($message !== '') break;
                }
                throw new RuntimeException('Gemini interaction failed' . ($message !== '' ? ': ' . $message : '.'));
            }

            $progress([
                'type' => 'diagnostic',
                'level' => 'info',
                'phase' => 'provider_response_parsed',
                'message' => 'Gemini interaction status: ' . ($status !== '' ? $status : 'unknown') . '; function calls detected: ' . count($currentCalls) . '; answer text received: ' . ($roundText !== '' ? 'yes' : 'no') . '.',
            ]);

            foreach ($currentCalls as $call) {
                $args = $call['arguments'] ?? [];
                if (!is_array($args) && $call['arguments_json'] !== '') {
                    $decoded = json_decode($call['arguments_json'], true);
                    $args = is_array($decoded) ? $decoded : [];
                } elseif ($call['arguments_json'] !== '') {
                    $decoded = json_decode($call['arguments_json'], true);
                    if (is_array($decoded)) $args = $decoded;
                }
                $call['arguments'] = is_array($args) ? $args : [];
                $functionCalls[] = $call;
            }

            if (!$functionCalls) {
                $finalAnswer = trim($roundText);
                break;
            }

            $results = [];
            foreach ($functionCalls as $call) {
                $name = (string)$call['name'];
                $arguments = (array)$call['arguments'];
                $progress(['type' => 'tool_running', 'name' => $name, 'message' => 'Querying live ' . $this->toolLabel($name) . ' data…']);
                $toolStarted = microtime(true);
                try {
                    $toolResult = $executor($name, $arguments);
                    $progress([
                        'type' => 'tool_completed',
                        'name' => $name,
                        'records_found' => $this->countToolRecords($toolResult),
                        'latency_ms' => (int)round((microtime(true) - $toolStarted) * 1000),
                        'message' => $this->toolLabel($name) . ' data loaded.',
                    ]);
                    $results[] = [
                        'type' => 'function_result',
                        'name' => $name,
                        'call_id' => (string)$call['id'],
                        'result' => [
                            [
                                'type' => 'text',
                                'text' => $this->encodeToolResult($toolResult),
                            ],
                        ],
                    ];
                } catch (Throwable $e) {
                    $progress(['type' => 'tool_failed', 'name' => $name, 'message' => $e->getMessage()]);
                    $results[] = [
                        'type' => 'function_result',
                        'name' => $name,
                        'call_id' => (string)$call['id'],
                        'is_error' => true,
                        'result' => [
                            [
                                'type' => 'text',
                                'text' => $this->encodeToolResult(['error' => $e->getMessage()]),
                            ],
                        ],
                    ];
                }
            }

            $allToolCalls = ['results' => $results];
            $round++;
            $progress(['type' => 'continue', 'message' => 'Sending tool results back to Gemini…']);
            $progress(['type' => 'debug', 'message' => 'Function results prepared; resuming the Gemini interaction.']);
        }

        if ($finalAnswer === '') {
            throw new RuntimeException('Gemini completed the tool workflow but returned no final answer.');
        }

        return [
            'answer' => $finalAnswer,
            'model' => $model,
            'latency_ms' => (int)round((microtime(true) - $started) * 1000),
            'interaction_id' => $lastInteractionId,
            'tool_rounds' => $round,
        ];
    }

    private function streamRequest(array $payload, callable $onEvent): array
    {
        $endpoint = (string)$this->config['endpoint'];
        $key = (string)$this->config['api_key'];
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($body === false) throw new RuntimeException('Unable to encode Gemini streaming request.');

        $ch = curl_init($endpoint);
        if ($ch === false) throw new RuntimeException('Unable to initialize cURL.');

        $buffer = '';
        $rawResponse = '';
        $httpCode = 0;
        $streamError = null;
        $lastInteraction = [];

        $dispatchBlock = function (string $block) use (&$streamError, &$lastInteraction, $onEvent): void {
            $dataLines = [];
            foreach (preg_split('/\n/', str_replace("\r", '', $block)) as $line) {
                if (str_starts_with($line, 'data:')) {
                    $dataLines[] = ltrim(substr($line, 5));
                }
            }
            if (!$dataLines) return;
            $data = trim(implode("\n", $dataLines));
            if ($data === '' || $data === '[DONE]') return;

            $decoded = json_decode($data, true);
            if (!is_array($decoded)) return;
            if (isset($decoded['interaction']) && is_array($decoded['interaction'])) {
                $lastInteraction = $decoded['interaction'];
            }
            try {
                $onEvent($decoded);
            } catch (Throwable $e) {
                $streamError = $e;
            }
        };

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: text/event-stream',
                'Cache-Control: no-cache',
                'x-goog-api-key: ' . $key,
            ],
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_CONNECTTIMEOUT => 20,
            CURLOPT_TIMEOUT => (int)($this->config['timeout'] ?? 60),
            CURLOPT_WRITEFUNCTION => function ($curl, string $chunk) use (&$buffer, &$rawResponse, $dispatchBlock, &$streamError): int {
                $rawResponse .= $chunk;
                $buffer .= $chunk;
                $buffer = str_replace("\r\n", "\n", $buffer);
                $buffer = str_replace("\r", "\n", $buffer);

                while (($pos = strpos($buffer, "\n\n")) !== false) {
                    $block = substr($buffer, 0, $pos);
                    $buffer = substr($buffer, $pos + 2);
                    $dispatchBlock($block);
                    if ($streamError instanceof Throwable) return 0;
                }
                return strlen($chunk);
            },
        ]);

        $ok = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // Gemini/proxies can finish the stream without a final blank line.
        // Process any remaining SSE event before deciding the interaction ended.
        if ($streamError === null && trim($buffer) !== '') {
            $dispatchBlock($buffer);
        }

        if ($streamError instanceof Throwable) throw $streamError;
        if ($ok === false) {
            if ($curlError !== '') {
                throw new RuntimeException('Gemini streaming request failed: ' . $curlError);
            }
            throw new RuntimeException('Gemini streaming request failed before a response was received.');
        }
        if ($httpCode < 200 || $httpCode >= 300) {
            $errorMessage = 'Unknown API error.';
            $json = json_decode($rawResponse !== '' ? $rawResponse : $buffer, true);
            if (is_array($json)) {
                $errorMessage = (string)($json['error']['message'] ?? $errorMessage);
            }
            throw new RuntimeException('Gemini streaming API error (' . $httpCode . '): ' . $errorMessage);
        }

        return ['interaction' => $lastInteraction ?: ['status' => 'completed']];
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

    private function functionCalls(array $response): array
    {
        $calls = [];
        foreach ((array)($response['steps'] ?? []) as $step) {
            if (($step['type'] ?? '') !== 'function_call') continue;
            $calls[] = [
                'id' => (string)($step['id'] ?? ''),
                'name' => (string)($step['name'] ?? ''),
                'arguments' => $step['arguments'] ?? [],
            ];
        }
        return $calls;
    }

    private function encodeToolResult(mixed $result): string
    {
        $json = json_encode(
            $result,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
        );
        if ($json === false) {
            return '{"error":"Unable to encode tool result."}';
        }
        // Keep model context bounded even when a tenant has many records.
        if (strlen($json) > 60000) {
            $json = substr($json, 0, 60000) . '...';
        }
        return $json;
    }

    private function loadConfig(): array
    {
        $moduleRoot = dirname(__DIR__, 2);
        $taxi = $moduleRoot . '/Taxi/Config/gemini.php';
        if (is_file($taxi)) {
            $c = require $taxi;
            if (is_array($c) && trim((string)($c['api_key'] ?? '')) !== '') {
                $c['timeout'] = min(60, max(10, (int)($c['timeout'] ?? 60)));
                return $c;
            }
        }

        $member = $moduleRoot . '/MemberApp/Config/gemini.php';
        if (is_file($member)) {
            $c = require $member;
            if (is_array($c) && trim((string)($c['api_key'] ?? '')) !== '') {
                $c['timeout'] = min(60, max(10, (int)($c['timeout'] ?? 60)));
                return $c;
            }
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

        // The Interactions streaming REST endpoint uses alt=sse. Keep the
        // normal endpoint untouched for non-streaming requests.
        $streamEndpoint = $endpoint;
        if (strpos($streamEndpoint, '?') === false) {
            $streamEndpoint .= '?alt=sse';
        } elseif (strpos($streamEndpoint, 'alt=sse') === false) {
            $streamEndpoint .= '&alt=sse';
        }

        $ch = curl_init($streamEndpoint);
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
