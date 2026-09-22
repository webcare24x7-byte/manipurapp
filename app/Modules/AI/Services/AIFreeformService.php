<?php
declare(strict_types=1);

namespace App\Modules\AI\Services;

use App\Modules\AI\Models\AIQuestion;
use RuntimeException;
use Throwable;

final class AIFreeformService
{
    private GeminiService|GroqService $provider;
    private AIQuestion $questions;
    private AIFreeformTools $tools;

    public function __construct()
    {
        $this->provider = $this->makeProvider();
        $this->questions = new AIQuestion();
        $this->tools = new AIFreeformTools();
    }

    public function latestForUserQuestion(int $tenantId, int $userId, string $question, string $since): ?array
    {
        return $this->questions->latestForUserQuestion($tenantId, $userId, $question, $since);
    }

    public function ask(int $tenantId, int $userId, string $question): array
    {
        $question = trim($question);
        if ($question === '') {
            throw new RuntimeException('Please enter a question.');
        }
        if (mb_strlen($question) > 4000) {
            throw new RuntimeException('Please keep your free-form question within 4,000 characters.');
        }

        $started = microtime(true);
        $toolTrace = [];
        $allRecords = [];
        $result = $this->provider->runWithTools(
            $question,
            $this->tools->definitions(),
            function (string $name, array $arguments) use ($tenantId, &$toolTrace, &$allRecords): array {
                $started = microtime(true);
                try {
                    $data = $this->tools->execute($tenantId, $name, $arguments);
                    $toolTrace[] = [
                        'name' => $name,
                        'arguments' => $arguments,
                        'status' => 'COMPLETED',
                        'latency_ms' => (int)round((microtime(true) - $started) * 1000),
                        'records_found' => $this->countRecords($data),
                    ];
                    $this->collectRecords($data, $allRecords);
                    return $data;
                } catch (Throwable $e) {
                    $toolTrace[] = [
                        'name' => $name,
                        'arguments' => $arguments,
                        'status' => 'FAILED',
                        'latency_ms' => (int)round((microtime(true) - $started) * 1000),
                        'error' => $e->getMessage(),
                    ];
                    throw $e;
                }
            }
        );

        $answer = trim((string)($result['answer'] ?? ''));
        if ($answer === '') {
            throw new RuntimeException($this->providerName() . ' completed the free-form request but returned no answer.');
        }

        $recordsFound = count($allRecords);
        $result['scope'] = 'FREEFORM';
        $result['label'] = '🤖 Free-form Smart Ask';
        $result['provider'] = $this->providerName();
        $result['records_found'] = $recordsFound;
        $result['records'] = array_values($allRecords);
        $result['tools_used'] = $toolTrace;
        $result['message'] = trim((string)($result['message'] ?? $answer));
        $result['tool_calls'] = (array)($result['tool_calls'] ?? []);
        $result['results'] = (array)($result['results'] ?? []);
        $result['latency_ms'] = (int)round((microtime(true) - $started) * 1000);
        $result['id'] = $this->questions->create(
            $tenantId,
            $userId,
            $question,
            $answer,
            (string)($result['model'] ?? ''),
            (int)($result['latency_ms'] ?? 0),
            'FREEFORM',
            $recordsFound
        );

        return $result;
    }

    public function askStreaming(int $tenantId, int $userId, string $question, callable $emit): array
    {
        $question = trim($question);
        if ($question === '') throw new RuntimeException('Please enter a question.');
        if (mb_strlen($question) > 4000) throw new RuntimeException('Please keep your free-form question within 4,000 characters.');

        $started = microtime(true);
        $toolTrace = [];
        $allRecords = [];

        $result = $this->provider->runWithToolsStreaming(
            $question,
            $this->tools->definitions(),
            function (string $name, array $arguments) use ($tenantId, &$toolTrace, &$allRecords, $emit): array {
                $startedTool = microtime(true);
                try {
                    $data = $this->tools->execute($tenantId, $name, $arguments);
                    $toolTrace[] = [
                        'name' => $name,
                        'arguments' => $arguments,
                        'status' => 'COMPLETED',
                        'latency_ms' => (int)round((microtime(true) - $startedTool) * 1000),
                        'records_found' => $this->countRecords($data),
                    ];
                    $this->collectRecords($data, $allRecords);
                    return $data;
                } catch (Throwable $e) {
                    $toolTrace[] = [
                        'name' => $name,
                        'arguments' => $arguments,
                        'status' => 'FAILED',
                        'latency_ms' => (int)round((microtime(true) - $startedTool) * 1000),
                        'error' => $e->getMessage(),
                    ];
                    throw $e;
                }
            },
            $emit
        );

        $answer = trim((string)($result['answer'] ?? ''));
        if ($answer === '') throw new RuntimeException($this->providerName() . ' completed the free-form request but returned no answer.');

        $recordsFound = count($allRecords);
        $result['scope'] = 'FREEFORM';
        $result['label'] = '🤖 Free-form Smart Ask';
        $result['provider'] = $this->providerName();
        $result['records_found'] = $recordsFound;
        $result['records'] = array_values($allRecords);
        $result['tools_used'] = $toolTrace;
        $result['message'] = trim((string)($result['message'] ?? $answer));
        $result['tool_calls'] = (array)($result['tool_calls'] ?? []);
        $result['results'] = (array)($result['results'] ?? []);
        $result['latency_ms'] = (int)round((microtime(true) - $started) * 1000);
        $emit(['type' => 'db_saving', 'message' => 'AI provider returned the message + tool plan. Saving the result to the database…']);
        try {
            $result['id'] = $this->questions->create(
                $tenantId,
                $userId,
                $question,
                $answer,
                (string)($result['model'] ?? ''),
                (int)$result['latency_ms'],
                'FREEFORM',
                $recordsFound
            );
        } catch (Throwable $e) {
            $emit(['type' => 'db_error', 'message' => 'Database save failed: ' . $e->getMessage()]);
            throw $e;
        }
        $emit(['type' => 'db_saved', 'id' => (int)$result['id'], 'message' => 'Database save completed. AI result ID #' . (int)$result['id'] . '.']);
        return $result;
    }

    private function makeProvider(): GeminiService|GroqService
    {
        $configPath = dirname(__DIR__) . '/Config/provider.php';
        $config = is_file($configPath) ? require $configPath : [];
        $provider = strtolower(trim((string)($config['freeform_provider'] ?? 'gemini')));

        return match ($provider) {
            'groq' => new GroqService(),
            default => new GeminiService(),
        };
    }

    private function providerName(): string
    {
        return $this->provider instanceof GroqService ? 'Groq' : 'Gemini';
    }

    private function countRecords(array $data): int
    {
        if (isset($data['records_found'])) return (int)$data['records_found'];

        $count = 0;
        foreach ((array)($data['record_counts'] ?? []) as $n) {
            $count += (int)$n;
        }
        foreach ((array)($data['records'] ?? []) as $rows) {
            if (is_array($rows)) $count += count($rows);
        }
        foreach ((array)($data['matching_menu_items'] ?? []) as $row) {
            if (is_array($row)) $count++;
        }
        return $count;
    }

    private function collectRecords(array $data, array &$out): void
    {
        $append = static function (array $row) use (&$out): void {
            $ai = (array)($row['_ai'] ?? []);
            $type = (string)($ai['record_type'] ?? '');
            $id = (int)($ai['record_id'] ?? ($row['id'] ?? 0));
            $key = $type . ':' . $id;
            if ($type !== '' && $id > 0) {
                $out[$key] = $row;
            }
        };

        foreach ((array)($data['records'] ?? []) as $scopeRows) {
            if (is_array($scopeRows)) {
                foreach ($scopeRows as $row) {
                    if (is_array($row)) $append($row);
                }
            }
        }
        foreach ((array)($data['restaurants'] ?? []) as $row) {
            if (is_array($row)) {
                $append($row);
                foreach ((array)($row['menu_items'] ?? []) as $item) {
                    if (is_array($item)) $append($item);
                }
            }
        }
        foreach ((array)($data['matching_menu_items'] ?? []) as $row) {
            if (is_array($row)) $append($row);
        }
        foreach ((array)($data['services'] ?? []) as $row) {
            if (is_array($row)) $append($row);
        }
    }
}
