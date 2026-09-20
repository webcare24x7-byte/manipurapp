<?php

declare(strict_types=1);

namespace App\Modules\MemberApp\Services;

use RuntimeException;

/**
 * Fresh Food road-distance / ETA estimator.
 *
 * Uses the same Gemini Interactions API pattern as the existing Taxi route
 * estimator. When Taxi is installed, its Gemini configuration is reused so
 * there is only one API credential/configuration to maintain.
 */
final class MemberFreshFoodRouteService
{
    private array $config;

    public function __construct()
    {
        $this->config = $this->loadGeminiConfig();

        $apiKey = trim((string) ($this->config['api_key'] ?? ''));
        $endpoint = trim((string) ($this->config['endpoint'] ?? ''));

        if ($apiKey === '' || $apiKey === 'YOUR_GEMINI_API_KEY_HERE' || $apiKey === 'YOUR_ACTUAL_GEMINI_API_KEY') {
            throw new RuntimeException('Gemini API key is not configured.');
        }

        if ($endpoint === '') {
            throw new RuntimeException('Gemini API endpoint is not configured.');
        }
    }

    /**
     * Estimate REAL road/driving distance and ETA between a customer and a
     * Fresh Food business.
     *
     * @return array{distance_km:float,eta_minutes:int}
     */
    public function estimate(
        float $customerLat,
        float $customerLng,
        float $businessLat,
        float $businessLng,
        string $customerAddress = '',
        string $businessAddress = '',
        string $businessName = ''
    ): array {
        $this->validateCoordinate($customerLat, 'Customer latitude', -90, 90);
        $this->validateCoordinate($customerLng, 'Customer longitude', -180, 180);
        $this->validateCoordinate($businessLat, 'Business latitude', -90, 90);
        $this->validateCoordinate($businessLng, 'Business longitude', -180, 180);

        $prompt = $this->buildPrompt(
            $customerLat,
            $customerLng,
            $businessLat,
            $businessLng,
            trim($customerAddress),
            trim($businessAddress),
            trim($businessName)
        );

        $payload = [
            'model' => (string) ($this->config['model'] ?? 'gemini-3.5-flash'),
            'input' => [[
                'type' => 'text',
                'text' => $prompt,
            ]],
            'response_format' => [
                'type' => 'text',
                'mime_type' => 'application/json',
                'schema' => [
                    'type' => 'object',
                    'properties' => [
                        'distance_km' => ['type' => 'number'],
                        'eta_minutes' => ['type' => 'integer'],
                    ],
                    'required' => ['distance_km', 'eta_minutes'],
                ],
            ],
        ];

        return $this->normalizeResult($this->extractJson($this->request($payload)));
    }

    private function buildPrompt(
        float $customerLat,
        float $customerLng,
        float $businessLat,
        float $businessLng,
        string $customerAddress,
        string $businessAddress,
        string $businessName
    ): string {
        $customerLabel = $customerAddress !== '' ? $customerAddress : 'Not provided';
        $businessLabel = $businessAddress !== '' ? $businessAddress : 'Not provided';
        $name = $businessName !== '' ? $businessName : 'Fresh Food store';

        return <<<PROMPT
You are estimating delivery travel information for a Fresh Food order in Manipur, India.

Estimate the most reasonable REAL ROAD / DRIVING route for a delivery rider from the Fresh Food store to the customer's delivery location.

STORE
Name: {$name}
Address: {$businessLabel}
Coordinates: {$businessLat}, {$businessLng}

CUSTOMER
Address: {$customerLabel}
Coordinates: {$customerLat}, {$customerLng}

IMPORTANT:
- Use the supplied coordinates and address names as the location reference.
- Estimate road/driving distance, NOT straight-line/geographic distance.
- Estimate practical driving time for a delivery vehicle/rider.
- Return distance in kilometres.
- Return ETA in whole minutes.
- Do not calculate delivery fees.
- Do not provide route instructions.
- Do not provide explanations or commentary.
- Return ONLY valid JSON matching the supplied schema.
PROMPT;
    }

    private function loadGeminiConfig(): array
    {
        // Prefer the existing Taxi configuration so both modules use the same
        // tested Gemini endpoint/model/API credential.
        $moduleRoot = dirname(__DIR__, 2);
        $taxiConfig = $moduleRoot . '/Taxi/Config/gemini.php';
        if (is_file($taxiConfig)) {
            $config = require $taxiConfig;
            if (is_array($config)) return $config;
        }

        $memberConfig = dirname(__DIR__) . '/Config/gemini.php';
        if (is_file($memberConfig)) {
            $config = require $memberConfig;
            if (is_array($config)) return $config;
        }

        throw new RuntimeException(
            'Gemini configuration was not found. Configure Taxi/Config/gemini.php or MemberApp/Config/gemini.php.'
        );
    }

    private function request(array $payload): array
    {
        $endpoint = (string) ($this->config['endpoint'] ?? '');
        $apiKey = (string) ($this->config['api_key'] ?? '');
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($body === false) {
            throw new RuntimeException('Unable to encode Gemini request.');
        }

        $ch = curl_init($endpoint);
        if ($ch === false) {
            throw new RuntimeException('Unable to initialize cURL.');
        }

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'x-goog-api-key: ' . $apiKey,
            ],
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => (int) ($this->config['timeout'] ?? 120),
        ]);

        $raw = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            throw new RuntimeException('Gemini request failed: ' . $error);
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            throw new RuntimeException('Gemini returned an invalid response.');
        }

        if ($code < 200 || $code >= 300) {
            throw new RuntimeException(
                'Gemini API error (' . $code . '): ' .
                ($decoded['error']['message'] ?? 'Unknown Gemini API error.')
            );
        }

        return $decoded;
    }

    private function extractJson(array $response): array
    {
        foreach (($response['steps'] ?? []) as $step) {
            if (($step['type'] ?? '') !== 'model_output') continue;

            foreach (($step['content'] ?? []) as $content) {
                if (($content['type'] ?? '') !== 'text') continue;

                $text = trim((string) ($content['text'] ?? ''));
                if ($text === '') continue;

                if (str_starts_with($text, '```')) {
                    $text = preg_replace('/^```(?:json)?\s*/i', '', $text) ?? $text;
                    $text = preg_replace('/\s*```$/', '', $text) ?? $text;
                    $text = trim($text);
                }

                $decoded = json_decode($text, true);
                if (is_array($decoded)) return $decoded;
            }
        }

        throw new RuntimeException('Gemini completed the request but returned no valid Fresh Food route JSON.');
    }

    /** @return array{distance_km:float,eta_minutes:int} */
    private function normalizeResult(array $result): array
    {
        if (!isset($result['distance_km']) || !is_numeric($result['distance_km'])) {
            throw new RuntimeException('Gemini returned an invalid Fresh Food route distance.');
        }
        if (!isset($result['eta_minutes']) || !is_numeric($result['eta_minutes'])) {
            throw new RuntimeException('Gemini returned an invalid Fresh Food route ETA.');
        }

        $distance = round((float) $result['distance_km'], 2);
        $eta = (int) round((float) $result['eta_minutes']);

        if ($distance <= 0) throw new RuntimeException('Gemini returned an invalid Fresh Food route distance.');
        if ($eta <= 0) throw new RuntimeException('Gemini returned an invalid Fresh Food route ETA.');
        if ($distance > 1000) throw new RuntimeException('Gemini returned an implausible Fresh Food route distance.');
        if ($eta > 1440) throw new RuntimeException('Gemini returned an implausible Fresh Food route ETA.');

        return ['distance_km' => $distance, 'eta_minutes' => $eta];
    }

    private function validateCoordinate(float $value, string $label, float $min, float $max): void
    {
        if ($value < $min || $value > $max) {
            throw new RuntimeException($label . ' is outside the valid range.');
        }
    }
}
