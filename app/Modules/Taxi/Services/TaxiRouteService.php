<?php

declare(strict_types=1);

namespace App\Modules\Taxi\Services;

use RuntimeException;

final class TaxiRouteService
{
    private array $config;

    public function __construct()
    {
        $this->config = $this->loadGeminiConfig();

        $apiKey = trim((string) ($this->config['api_key'] ?? ''));
        $endpoint = trim((string) ($this->config['endpoint'] ?? ''));

        if ($apiKey === '' || $apiKey === 'YOUR_ACTUAL_GEMINI_API_KEY') {
            throw new RuntimeException('Gemini API key is not configured.');
        }

        if ($endpoint === '') {
            throw new RuntimeException('Gemini API endpoint is not configured.');
        }
    }

    /**
     * Estimate road distance and driving time using Gemini.
     *
     * This is intentionally an estimate only. It is informational and
     * does not calculate or change the taxi fare.
     *
     * @return array<string, mixed>
     */
    public function estimate(
        float $pickupLat,
        float $pickupLng,
        float $destinationLat,
        float $destinationLng,
        string $pickupAddress = '',
        string $destinationAddress = '',
        string $tripType = 'ONE_WAY'
    ): array {
        $this->validateCoordinate($pickupLat, 'Pickup latitude', -90, 90);
        $this->validateCoordinate($pickupLng, 'Pickup longitude', -180, 180);
        $this->validateCoordinate($destinationLat, 'Destination latitude', -90, 90);
        $this->validateCoordinate($destinationLng, 'Destination longitude', -180, 180);

        $tripType = strtoupper(trim($tripType));
        if (!in_array($tripType, ['ONE_WAY', 'ROUND_TRIP'], true)) {
            throw new RuntimeException('Invalid trip type for route estimation.');
        }

        $prompt = $this->buildPrompt(
            $pickupLat,
            $pickupLng,
            $destinationLat,
            $destinationLng,
            trim($pickupAddress),
            trim($destinationAddress),
            $tripType
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
                'schema' => $this->schema($tripType),
            ],
        ];

        $response = $this->request($payload);
        $result = $this->extractJson($response);

        return $this->normalizeResult($result, $tripType);
    }

    /**
     * Load the Taxi module's own Gemini configuration.
     *
     * Keeping this configuration inside the Taxi module makes the module
     * self-contained and prevents route estimation from depending on
     * another module's configuration.
     *
     * @return array<string, mixed>
     */
    private function loadGeminiConfig(): array
    {
        $path = dirname(__DIR__) . '/Config/gemini.php';

        if (!is_file($path)) {
            throw new RuntimeException(
                'Taxi Gemini configuration file was not found: ' . $path
            );
        }

        $config = require $path;

        if (!is_array($config)) {
            throw new RuntimeException(
                'Taxi Gemini configuration must return an array.'
            );
        }

        return $config;
    }

    private function buildPrompt(
        float $pickupLat,
        float $pickupLng,
        float $destinationLat,
        float $destinationLng,
        string $pickupAddress,
        string $destinationAddress,
        string $tripType
    ): string {
        $pickupLabel = $pickupAddress !== '' ? $pickupAddress : 'Not provided';
        $destinationLabel = $destinationAddress !== '' ? $destinationAddress : 'Not provided';

        if ($tripType === 'ROUND_TRIP') {
            return <<<PROMPT
You are estimating taxi travel information for a booking in Manipur, India.

Use the supplied location names and coordinates as the location reference.
Estimate the most reasonable REAL ROAD / DRIVING route, not straight-line distance.

OUTBOUND
Pickup: {$pickupLabel}
Pickup coordinates: {$pickupLat}, {$pickupLng}
Destination: {$destinationLabel}
Destination coordinates: {$destinationLat}, {$destinationLng}

RETURN
The return journey is from the destination back to the pickup location.

IMPORTANT:
- Estimate road/driving distance, not geographic straight-line distance.
- Estimate driving time in minutes.
- Return distances in kilometres.
- Do not calculate taxi fare.
- Do not give route instructions.
- Do not provide explanations or commentary.
- Return ONLY valid JSON matching the supplied schema.
PROMPT;
        }

        return <<<PROMPT
You are estimating taxi travel information for a booking in Manipur, India.

Use the supplied location names and coordinates as the location reference.
Estimate the most reasonable REAL ROAD / DRIVING route, not straight-line distance.

Pickup: {$pickupLabel}
Pickup coordinates: {$pickupLat}, {$pickupLng}

Destination: {$destinationLabel}
Destination coordinates: {$destinationLat}, {$destinationLng}

IMPORTANT:
- Estimate road/driving distance, not geographic straight-line distance.
- Estimate driving time in minutes.
- Return distance in kilometres.
- Do not calculate taxi fare.
- Do not give route instructions.
- Do not provide explanations or commentary.
- Return ONLY valid JSON matching the supplied schema.
PROMPT;
    }

    /**
     * @return array<string, mixed>
     */
    private function schema(string $tripType): array
    {
        if ($tripType === 'ROUND_TRIP') {
            return [
                'type' => 'object',
                'properties' => [
                    'outbound_distance_km' => ['type' => 'number'],
                    'outbound_eta_minutes' => ['type' => 'integer'],
                    'return_distance_km' => ['type' => 'number'],
                    'return_eta_minutes' => ['type' => 'integer'],
                    'total_distance_km' => ['type' => 'number'],
                    'total_eta_minutes' => ['type' => 'integer'],
                ],
                'required' => [
                    'outbound_distance_km',
                    'outbound_eta_minutes',
                    'return_distance_km',
                    'return_eta_minutes',
                    'total_distance_km',
                    'total_eta_minutes',
                ],
            ];
        }

        return [
            'type' => 'object',
            'properties' => [
                'distance_km' => ['type' => 'number'],
                'eta_minutes' => ['type' => 'integer'],
            ],
            'required' => [
                'distance_km',
                'eta_minutes',
            ],
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
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

    /**
     * @param array<string, mixed> $response
     * @return array<string, mixed>
     */
    private function extractJson(array $response): array
    {
        foreach (($response['steps'] ?? []) as $step) {
            if (($step['type'] ?? '') !== 'model_output') {
                continue;
            }

            foreach (($step['content'] ?? []) as $content) {
                if (($content['type'] ?? '') !== 'text') {
                    continue;
                }

                $text = trim((string) ($content['text'] ?? ''));
                if ($text === '') {
                    continue;
                }

                if (str_starts_with($text, '```')) {
                    $text = preg_replace('/^```(?:json)?\s*/i', '', $text) ?? $text;
                    $text = preg_replace('/\s*```$/', '', $text) ?? $text;
                    $text = trim($text);
                }

                $decoded = json_decode($text, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
        }

        throw new RuntimeException('Gemini completed the request but returned no valid route JSON.');
    }

    /**
     * @param array<string, mixed> $result
     * @return array<string, mixed>
     */
    private function normalizeResult(array $result, string $tripType): array
    {
        if ($tripType === 'ROUND_TRIP') {
            $keys = [
                'outbound_distance_km',
                'outbound_eta_minutes',
                'return_distance_km',
                'return_eta_minutes',
                'total_distance_km',
                'total_eta_minutes',
            ];

            foreach ($keys as $key) {
                if (!isset($result[$key]) || !is_numeric($result[$key])) {
                    throw new RuntimeException('Gemini returned an invalid ' . $key . '.');
                }
            }

            $normalized = [
                'outbound_distance_km' => round((float) $result['outbound_distance_km'], 2),
                'outbound_eta_minutes' => (int) round((float) $result['outbound_eta_minutes']),
                'return_distance_km' => round((float) $result['return_distance_km'], 2),
                'return_eta_minutes' => (int) round((float) $result['return_eta_minutes']),
                'total_distance_km' => round((float) $result['total_distance_km'], 2),
                'total_eta_minutes' => (int) round((float) $result['total_eta_minutes']),
            ];
        } else {
            if (!isset($result['distance_km']) || !is_numeric($result['distance_km'])) {
                throw new RuntimeException('Gemini returned an invalid distance_km.');
            }

            if (!isset($result['eta_minutes']) || !is_numeric($result['eta_minutes'])) {
                throw new RuntimeException('Gemini returned an invalid eta_minutes.');
            }

            $normalized = [
                'distance_km' => round((float) $result['distance_km'], 2),
                'eta_minutes' => (int) round((float) $result['eta_minutes']),
            ];
        }

        foreach ($normalized as $key => $value) {
            if (str_contains($key, 'distance') && (float) $value <= 0) {
                throw new RuntimeException('Gemini returned an invalid route distance.');
            }
            if (str_contains($key, 'eta') && (int) $value <= 0) {
                throw new RuntimeException('Gemini returned an invalid route ETA.');
            }
        }

        return $normalized;
    }

    private function validateCoordinate(float $value, string $label, float $min, float $max): void
    {
        if ($value < $min || $value > $max) {
            throw new RuntimeException($label . ' is outside the valid range.');
        }
    }
}
