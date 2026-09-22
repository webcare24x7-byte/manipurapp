<?php

declare(strict_types=1);

namespace App\Modules\ILP\Models;

use App\Core\Database;

final class ILPHelper
{
    private Database $db;

    public function __construct()
    {
        $this->db = app()->get('db');
    }

    public function permitTypes(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM ilp_permit_types WHERE status='Active' ORDER BY sort_order, id"
        );
    }

    public function permit(string $code): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM ilp_permit_types WHERE code=? AND status='Active' LIMIT 1",
            [$code]
        );
    }

    public function requirements(string $code): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM ilp_requirements WHERE permit_code=? AND status='Active' ORDER BY sort_order,id",
            [$code]
        );
    }
}
