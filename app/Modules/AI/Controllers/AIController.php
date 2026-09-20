<?php
declare(strict_types=1);

namespace App\Modules\AI\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Modules\AI\Services\AIAdminService;
use Throwable;

final class AIController extends Controller
{
    private AIAdminService $service;

    public function __construct()
    {
        $this->service = new AIAdminService();
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
