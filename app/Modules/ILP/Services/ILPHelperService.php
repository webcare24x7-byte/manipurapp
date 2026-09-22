<?php

declare(strict_types=1);

namespace App\Modules\ILP\Services;

use App\Modules\ILP\Models\ILPHelper;

final class ILPHelperService
{
    private ILPHelper $model;
    private ILPEligibilityService $eligibility;
    private ILPChecklistService $checklist;

    public function __construct()
    {
        $this->model = new ILPHelper();
        $this->eligibility = new ILPEligibilityService();
        $this->checklist = new ILPChecklistService();
    }

    public function permitTypes(): array
    {
        return $this->model->permitTypes();
    }

    public function analyze(array $input): array
    {
        return $this->eligibility->analyze($input);
    }

    public function checklist(string $code): array
    {
        return $this->checklist->forPermit($code);
    }
}
