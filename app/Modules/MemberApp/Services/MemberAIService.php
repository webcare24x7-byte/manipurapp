<?php

declare(strict_types=1);

namespace App\Modules\MemberApp\Services;

use App\Modules\AI\Services\AIAdminService;
use App\Modules\AI\Services\AIFreeformService;
use RuntimeException;

final class MemberAIService
{
    private AIAdminService $ai;
    private ?AIFreeformService $freeform = null;

    public function __construct()
    {
        $this->ai = new AIAdminService();
    }

    public function freeform(int $tenantId, int $userId, string $question): array
    {
        $question = trim($question);
        if ($question === '') {
            throw new RuntimeException('Please enter a question.');
        }

        if (mb_strlen($question) > 4000) {
            throw new RuntimeException('Please keep your question within 4,000 characters.');
        }

        $this->freeform ??= new AIFreeformService();
        return $this->freeform->ask($tenantId, $userId, $question);
    }

    public function ask(int $tenantId, int $userId, string $question, string $scope = 'TOURISM'): array
    {
        $scope = strtoupper(trim($scope));

        $allowed = [
            'TOURISM',
            'DESTINATIONS',
            'STAYS',
            'PACKAGES',
            'GUIDES',
            'EXPERIENCES',
            'EVENTS',
            'RESTAURANTS',
            'RESTAURANT_MENU',
        ];

        if (!in_array($scope, $allowed, true)) {
            throw new RuntimeException('This AI topic is not available here.');
        }

        $question = trim($question);
        if ($question === '') {
            throw new RuntimeException('Please enter a question.');
        }

        if (mb_strlen($question) > 1000) {
            throw new RuntimeException('Please keep your question within 1000 characters.');
        }

        return $this->ai->ask($tenantId, $userId, $question, $scope);
    }
}
