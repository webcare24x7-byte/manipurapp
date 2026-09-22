<?php

declare(strict_types=1);

namespace App\Modules\ILP\Services;

use App\Modules\ILP\Models\ILPHelper;

final class ILPChecklistService
{
    private ILPHelper $model;

    public function __construct()
    {
        $this->model = new ILPHelper();
    }

    public function forPermit(string $code): array
    {
        $permit = $this->model->permit($code);
        if ($permit === null) {
            return [];
        }
        return [
            'permit' => $permit,
            'requirements' => $this->model->requirements($code),
            'source' => [
                'title' => $permit['source_title'],
                'url' => $permit['official_url'],
                'verified_at' => $permit['source_verified_at'],
            ],
        ];
    }
}
