<?php

declare(strict_types=1);

namespace App\Modules\ILP\Controllers;

use App\Core\Authorization;
use App\Core\Controller;
use App\Modules\ILP\Services\ILPHelperService;
use RuntimeException;
use Throwable;

final class ILPController extends Controller
{
    private ILPHelperService $service;
    private Authorization $authorization;

    public function __construct()
    {
        $this->service = new ILPHelperService();
        $this->authorization = new Authorization();
    }

    public function index(): void
    {
        if (!$this->allow()) {
            return;
        }

        $this->view('ILP::ILP.index', [
            'title' => 'Digital ILP Helper',
            'permitTypes' => $this->service->permitTypes(),
            'result' => null,
            'old' => [
                'outside_manipur' => '1',
                'purpose' => 'TOURISM',
                'duration_days' => '7',
                'sponsor_available' => '0',
            ],
        ]);
    }

    public function analyze(): void
    {
        if (!$this->allow()) {
            return;
        }

        try {
            $input = [
                'outside_manipur' => isset($_POST['outside_manipur']) && $_POST['outside_manipur'] === '1',
                'purpose' => trim((string)($_POST['purpose'] ?? '')),
                'duration_days' => trim((string)($_POST['duration_days'] ?? '')),
                'sponsor_available' => isset($_POST['sponsor_available']) && $_POST['sponsor_available'] === '1',
            ];

            $result = $this->service->analyze($input);

            $this->view('ILP::ILP.index', [
                'title' => 'Digital ILP Helper',
                'permitTypes' => $this->service->permitTypes(),
                'result' => $result,
                'old' => $input,
            ]);
        } catch (Throwable $e) {
            $this->view('ILP::ILP.index', [
                'title' => 'Digital ILP Helper',
                'permitTypes' => $this->service->permitTypes(),
                'result' => [
                    'success' => false,
                    'status' => 'error',
                    'message' => $e->getMessage(),
                    'missing_information' => [],
                    'reasons' => [],
                    'requirements' => [],
                ],
                'old' => $_POST,
            ]);
        }
    }

    public function apiAnalyze(): void
    {
        if (!$this->allow()) {
            return;
        }

        header('Content-Type: application/json; charset=utf-8');
        try {
            $payload = json_decode((string)file_get_contents('php://input'), true);
            if (!is_array($payload)) {
                $payload = $_POST;
            }

            $input = [
                'outside_manipur' => filter_var($payload['outside_manipur'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'purpose' => trim((string)($payload['purpose'] ?? '')),
                'duration_days' => trim((string)($payload['duration_days'] ?? '')),
                'sponsor_available' => filter_var($payload['sponsor_available'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ];

            echo json_encode($this->service->analyze($input), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage(),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    private function allow(): bool
    {
        try {
            $this->authorization->authorize('ilp.helper.view');
            return true;
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
            redirect('/dashboard');
            return false;
        }
    }
}
