<?php
declare(strict_types=1);

namespace App\Modules\AI\Services;

use App\Modules\AI\Models\AIQuestion;

final class AIAdminService
{
    private GeminiService $gemini;
    private AIQuestion $questions;
    private AITourismTools $tools;

    public function __construct()
    {
        $this->gemini = new GeminiService();
        $this->questions = new AIQuestion();
        $this->tools = new AITourismTools();
    }

    public function scopes(): array
    {
        return $this->tools->allowedScopes();
    }

    public function tourismScopes(): array
    {
        return $this->scopes();
    }

    public function ask(
        int $tenantId,
        int $userId,
        string $question,
        string $scope = 'TOURISM'
    ): array {
        $context = $this->tools->query($tenantId, $scope, $question);

        $result = $this->gemini->answer(
            $question,
            $scope,
            $context
        );

        $recordsFound = (int)($context['records_found'] ?? 0);
        if ($scope === 'TOURISM' && isset($context['summary'])) {
            $recordsFound = array_sum(array_map('intval', $context['summary']));
        }

        $result['scope'] = $scope;
        $result['label'] = $context['label'] ?? $scope;
        $result['records_found'] = $recordsFound;
        $result['records'] = $context['records'] ?? [];
        $result['id'] = $this->questions->create(
            $tenantId,
            $userId,
            $question,
            $result['answer'],
            $result['model'],
            $result['latency_ms'],
            $scope,
            $recordsFound
        );

        return $result;
    }

    public function recent(int $tenantId): array
    {
        return $this->questions->recent($tenantId);
    }
}
