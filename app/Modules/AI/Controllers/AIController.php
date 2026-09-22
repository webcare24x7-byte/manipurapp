<?php
declare(strict_types=1);

namespace App\Modules\AI\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Modules\AI\Services\AIAdminService;
use App\Modules\AI\Services\AIFreeformService;
use Throwable;

final class AIController extends Controller
{
    private AIAdminService $service;
    private AIFreeformService $freeform;

    public function __construct()
    {
        $this->service = new AIAdminService();
        $this->freeform = new AIFreeformService();
    }

    public function index(): void
    {
        $error = null;
        $rows = [];

        try {
            $rows = $this->service->recent((int)Auth::tenantId());
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }

        $this->view('AI::AI.index', [
            'title' => 'ManipurApp AI',
            'rows' => $rows,
            'error' => $error,
            'scopes' => $this->service->scopes(),
        ]);
    }


    public function freeform(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \RuntimeException('Invalid request method.');
            }

            $question = trim((string)($_POST['question'] ?? ''));
            $result = $this->freeform->ask(
                (int)Auth::tenantId(),
                (int)Auth::id(),
                $question
            );

            echo json_encode([
                'success' => true,
                'data' => $result,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    public function freeformLatest(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        try {
            $question = trim((string)($_GET['question'] ?? ''));
            $since = trim((string)($_GET['since'] ?? ''));
            if ($question === '' || $since === '') {
                throw new \RuntimeException('Missing Smart AI polling parameters.');
            }
            $row = $this->freeform->latestForUserQuestion((int)Auth::tenantId(), (int)Auth::id(), $question, $since);
            echo json_encode(['success'=>true,'data'=>$row], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            http_response_code(422);
            echo json_encode(['success'=>false,'error'=>$e->getMessage()], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
    }

    public function freeformStream(): void
    {
        $this->beginEventStream();
        $closed = false;
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new \RuntimeException('Invalid request method.');
            $question = trim((string)($_POST['question'] ?? ''));
            $tenantId = (int)Auth::tenantId();
            $userId = (int)Auth::id();

            // Release PHP's session lock before the long-running Gemini request.
            if (session_status() === PHP_SESSION_ACTIVE) session_write_close();

            $this->emitEvent('status', ['message' => 'Connected. Starting the configured Smart AI provider…']);
            $result = $this->freeform->askStreaming($tenantId, $userId, $question, function (array $event): void {
                $this->emitEvent('progress', $event);
            });

            // Keep the final SSE event small. The full result can contain many DB records;
            // sending those again over the long-running stream can delay/block the
            // browser from receiving the actual answer. The answer/history is already
            // persisted by AIFreeformService.
            $complete = [
                'answer' => (string)($result['answer'] ?? ''),
                'message' => (string)($result['message'] ?? $result['answer'] ?? ''),
                'tool_calls' => (array)($result['tool_calls'] ?? []),
                'results' => (array)($result['results'] ?? []),
                'records' => (array)($result['records'] ?? []),
                'model' => (string)($result['model'] ?? ''),
                'latency_ms' => (int)($result['latency_ms'] ?? 0),
                'records_found' => (int)($result['records_found'] ?? 0),
                'tools_used' => (array)($result['tools_used'] ?? []),
                'interaction_id' => (string)($result['interaction_id'] ?? ''),
                'provider' => (string)($result['provider'] ?? ''),
                'tool_rounds' => (int)($result['tool_rounds'] ?? 0),
                'rate_limit' => (array)($result['rate_limit'] ?? []),
            ];
            $this->emitEvent('complete', ['success' => true, 'data' => $complete]);
        } catch (Throwable $e) {
            $this->emitEvent('error', ['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    private function beginEventStream(): void
    {
        http_response_code(200);
        header('Content-Type: text/event-stream; charset=utf-8');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('X-Accel-Buffering: no');
        header('Content-Encoding: none');
        while (ob_get_level() > 0) @ob_end_flush();
        ob_implicit_flush(true);
        echo ": connected\n\n";
        flush();
    }

    private function emitEvent(string $event, array $data): void
    {
        echo 'event: ' . $event . "\n";
        echo 'data: ' . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n\n";
        flush();
    }

    public function ask(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \RuntimeException('Invalid request method.');
            }

            $question = trim((string)($_POST['question'] ?? ''));
            $scope = strtoupper(trim((string)($_POST['scope'] ?? 'TOURISM')));

            $result = $this->service->ask(
                (int)Auth::tenantId(),
                (int)Auth::id(),
                $question,
                $scope
            );

            echo json_encode([
                'success' => true,
                'data' => $result,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }
}
