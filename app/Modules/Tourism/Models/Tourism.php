<?php
declare(strict_types=1);
namespace App\Modules\Tourism\Models;
use App\Core\Database;
final class Tourism {
    private Database $db;
    public function __construct(){ $this->db=app()->get('db'); }

    private function count(string $table, int $t, bool $softDelete = true): int
    {
        $sql = "SELECT COUNT(*) c FROM {$table} WHERE tenant_id=?";
        if ($softDelete) {
            $sql .= " AND deleted_at IS NULL";
        }
        $r = $this->db->fetch($sql, [$t]);
        return (int)($r['c'] ?? 0);
    }

    public function dashboard(int $t): array
    {
        return [
            'providers' => $this->count('tourism_providers', $t),
            'destinations' => $this->count('tourism_destinations', $t),
            'stays' => $this->count('tourism_stays', $t),
            'rooms' => $this->count('tourism_stay_room_types', $t),
            'packages' => $this->count('tourism_packages', $t),
            'guides' => $this->count('tourism_guides', $t),
            'experiences' => $this->count('tourism_experiences', $t),
            'events' => $this->count('tourism_events', $t),
            'reviews' => $this->count('tourism_reviews', $t, false),
            'trips' => $this->count('tourism_trip_plans', $t, false),
        ];
    }
}
