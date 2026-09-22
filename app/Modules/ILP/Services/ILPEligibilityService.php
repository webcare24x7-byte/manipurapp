<?php

declare(strict_types=1);

namespace App\Modules\ILP\Services;

use App\Modules\ILP\Models\ILPHelper;

final class ILPEligibilityService
{
    private ILPHelper $model;

    public function __construct()
    {
        $this->model = new ILPHelper();
    }

    public function analyze(array $input): array
    {
        $purpose = strtoupper(trim((string)($input['purpose'] ?? '')));
        $days = isset($input['duration_days']) && $input['duration_days'] !== ''
            ? max(0, (int)$input['duration_days'])
            : null;
        $sponsor = filter_var($input['sponsor_available'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $outsideManipur = filter_var($input['outside_manipur'] ?? true, FILTER_VALIDATE_BOOLEAN);

        $missing = [];
        if ($outsideManipur && $purpose === '') {
            $missing[] = 'purpose';
        }
        if ($outsideManipur && $days === null) {
            $missing[] = 'duration_days';
        }

        if (!$outsideManipur) {
            return $this->response('NOT_APPLICABLE', 'ILP does not appear to apply based on the information provided.', 'high', [], [
                'The current helper is intended for Indian citizens entering Manipur from outside the state.',
            ], $input);
        }

        if ($missing !== []) {
            return $this->response(null, 'I need a little more information before suggesting a permit type.', 'needs_more_information', $missing, [], $input);
        }

        $code = null;
        $reasons = [];
        $confidence = 'high';

        switch ($purpose) {
            case 'TOURISM':
            case 'BUSINESS_VISIT':
            case 'SHORT_TERM':
                if ($days !== null && $days <= 30) {
                    $code = 'TEMPORARY';
                    $reasons[] = 'The stated purpose is a short-term visit and the planned stay is within 30 days.';
                } elseif ($days !== null && $days > 30 && $days <= 90) {
                    $code = 'REGULAR';
                    $reasons[] = 'The planned stay is longer than the stated Temporary permit period and within the generally 90-day Regular permit period.';
                    if (!$sponsor) {
                        $confidence = 'medium';
                        return $this->response('REGULAR', 'A Regular ILP may be applicable, but a valid Manipur sponsor is required before applying.', $confidence, ['sponsor_available'], $reasons, $input, true);
                    }
                } else {
                    $confidence = 'medium';
                    $reasons[] = 'The planned stay is longer than the general Regular permit period.';
                    return $this->response(null, 'The planned stay is outside the simple short-term guidance. A more specific permit case should be reviewed against current Government rules.', $confidence, [], $reasons, $input);
                }
                break;

            case 'FREQUENT_VISITOR':
                $code = 'REGULAR';
                $reasons[] = 'The stated purpose is frequent visitation, which matches the published Regular ILP category.';
                if (!$sponsor) {
                    return $this->response('REGULAR', 'A Regular ILP may be applicable, but a valid Manipur sponsor is required before applying.', 'medium', ['sponsor_available'], $reasons, $input, true);
                }
                break;

            case 'BUSINESS_ESTABLISHMENT':
            case 'INVESTOR':
            case 'TRADER':
            case 'CONTRACTOR':
                $code = 'SPECIAL';
                $reasons[] = 'The stated purpose matches the published Special Category ILP description for business, investment, trading or contractor activities.';
                if (!$sponsor) {
                    return $this->response('SPECIAL', 'A Special Category ILP may be applicable, but a valid Manipur sponsor is required before applying.', 'medium', ['sponsor_available'], $reasons, $input, true);
                }
                break;

            case 'LABOUR':
                $code = 'LABOUR';
                $reasons[] = 'The stated purpose is labour engagement, which has a dedicated Labour ILP workflow.';
                if (!$sponsor) {
                    return $this->response('LABOUR', 'The Labour ILP workflow requires the registered engaging firm, agency, contractor or individual to have the required Agency ID.', 'medium', ['agency_or_sponsor'], $reasons, $input, true);
                }
                break;

            case 'TEMPORARY_WORK':
                if ($days !== null && $days <= 180) {
                    $code = 'HALF_YEARLY';
                    $reasons[] = 'The stated purpose is temporary employment/work and the planned period is within the published Half-Yearly Working Permit period.';
                    if (!$sponsor) {
                        return $this->response('HALF_YEARLY', 'A Half-Yearly Working ILP may be applicable, but a valid Sponsor ID is required before applying.', 'medium', ['sponsor_available'], $reasons, $input, true);
                    }
                } else {
                    $confidence = 'medium';
                    return $this->response(null, 'The stated temporary work period needs a more specific review against current working-permit rules.', $confidence, [], $reasons, $input);
                }
                break;

            case 'REGULAR_EMPLOYEE':
                if ($days !== null && $days <= 360) {
                    $code = 'ANNUAL';
                    $reasons[] = 'The stated purpose is regular employment, which matches the published Annual Working ILP category.';
                    if (!$sponsor) {
                        return $this->response('ANNUAL', 'An Annual Working ILP may be applicable, but a valid Sponsor ID is required before applying.', 'medium', ['sponsor_available'], $reasons, $input, true);
                    }
                } else {
                    $code = 'ANNUAL';
                    $confidence = 'medium';
                    $reasons[] = 'The stated purpose matches regular employment, but the planned period should be checked against the approved employment period.';
                    if (!$sponsor) {
                        return $this->response('ANNUAL', 'An Annual Working ILP may be applicable, but a valid Sponsor ID is required before applying.', 'medium', ['sponsor_available'], $reasons, $input, true);
                    }
                }
                break;

            default:
                return $this->response(null, 'The purpose does not match a rule that this first version can determine safely. Please select a more specific purpose.', 'needs_more_information', ['purpose'], [], $input);
        }

        $permit = $code !== null ? $this->model->permit($code) : null;
        if ($permit === null) {
            return $this->response(null, 'No active permit rule was found for the selected case.', 'error', [], [], $input);
        }

        return [
            'success' => true,
            'status' => 'recommended',
            'confidence' => $confidence,
            'message' => 'Based on the information provided, this is the applicable permit category to review. The official Government portal remains authoritative.',
            'missing_information' => [],
            'reasons' => $reasons,
            'permit' => $permit,
            'requirements' => $this->model->requirements($code),
            'input' => $input,
            'source' => [
                'title' => $permit['source_title'],
                'url' => $permit['official_url'],
                'verified_at' => $permit['source_verified_at'],
            ],
        ];
    }

    private function response(?string $code, string $message, string $confidence, array $missing, array $reasons, array $input, bool $needsSponsor = false): array
    {
        $permit = $code !== null ? $this->model->permit($code) : null;
        return [
            'success' => true,
            'status' => $code !== null && $needsSponsor ? 'sponsor_required' : ($code === null ? 'needs_information' : 'not_applicable'),
            'confidence' => $confidence,
            'message' => $message,
            'missing_information' => $missing,
            'reasons' => $reasons,
            'permit' => $permit,
            'requirements' => $permit ? $this->model->requirements($code) : [],
            'input' => $input,
            'source' => $permit ? [
                'title' => $permit['source_title'],
                'url' => $permit['official_url'],
                'verified_at' => $permit['source_verified_at'],
            ] : [
                'title' => 'Government of Manipur ILP Portal',
                'url' => 'https://manipurilponline.mn.gov.in/',
                'verified_at' => '2026-09-22',
            ],
        ];
    }
}
