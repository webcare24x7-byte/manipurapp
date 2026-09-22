<?php
declare(strict_types=1);

namespace App\Modules\AI\Models;

final class AIQuestion
{
    private $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    public function create(
        int $tenantId,
        int $userId,
        string $question,
        string $answer,
        string $model,
        int $latencyMs,
        string $scope = 'GENERAL',
        int $recordsFound = 0
    ): int {
        $this->db->execute(
            'INSERT INTO ai_questions
             (tenant_id,user_id,question,answer,model,latency_ms,status,ai_scope,records_found,created_at)
             VALUES (?,?,?,?,?,?,?,?,?,NOW())',
            [$tenantId,$userId,$question,$answer,$model,$latencyMs,'COMPLETED',$scope,$recordsFound]
        );
        return (int)$this->db->lastInsertId();
    }

    public function latestForUserQuestion(int $tenantId, int $userId, string $question, string $since): ?array
    {
        $rows = $this->db->fetchAll(
            'SELECT id,question,answer,model,latency_ms,status,ai_scope,records_found,created_at
             FROM ai_questions
             WHERE tenant_id=? AND user_id=? AND question=? AND created_at>=?
             ORDER BY id DESC LIMIT 1',
            [$tenantId, $userId, $question, $since]
        );
        return $rows[0] ?? null;
    }

    public function recent(int $tenantId, int $limit = 30): array
    {
        $limit = max(1,min(100,$limit));
        return $this->db->fetchAll(
            "SELECT id,question,answer,model,latency_ms,status,ai_scope,records_found,created_at
             FROM ai_questions WHERE tenant_id=? ORDER BY id DESC LIMIT {$limit}",
            [$tenantId]
        );
    }
}
