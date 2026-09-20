<?php

declare(strict_types=1);

/**
 * ============================================================
 * ManipurApp Taxi - Gemini Route Test Worker
 * ============================================================
 *
 * Usage:
 *
 *   php taxi-route-test-worker.php --route=mao
 *   php taxi-route-test-worker.php --route=senapati
 *   php taxi-route-test-worker.php --route=ukhrul
 *   php taxi-route-test-worker.php --route=airport
 *
 * ============================================================
 */

// ============================================================
// GEMINI CONFIGURATION
// ============================================================

$geminiApiKey = trim((string) (getenv('MANIPURAPP_GEMINI_API_KEY') ?: ''));

if ($geminiApiKey === '') {
    fwrite(STDERR, "MANIPURAPP_GEMINI_API_KEY is not configured." . PHP_EOL);
    exit(1);
}

$geminiEndpoint = 'https://generativelanguage.googleapis.com/v1beta/interactions';

$geminiModel = 'gemini-3.5-flash';


// ============================================================
// AVAILABLE TEST ROUTES
// ============================================================

$routes = [

    'mao' => [
        'pickup' => [
            'name' => 'Mao, Manipur, India',
            'lat'  => 25.4180,
            'lng'  => 94.2480,
        ],
        'destination' => [
            'name' => 'Imphal, Manipur, India',
            'lat'  => 24.8170,
            'lng'  => 93.9368,
        ],
    ],

    'senapati' => [
        'pickup' => [
            'name' => 'Senapati, Manipur, India',
            'lat'  => 25.2640,
            'lng'  => 94.0270,
        ],
        'destination' => [
            'name' => 'Imphal, Manipur, India',
            'lat'  => 24.8170,
            'lng'  => 93.9368,
        ],
    ],

    'ukhrul' => [
        'pickup' => [
            'name' => 'Ukhrul, Manipur, India',
            'lat'  => 25.0960,
            'lng'  => 94.3610,
        ],
        'destination' => [
            'name' => 'Imphal, Manipur, India',
            'lat'  => 24.8170,
            'lng'  => 93.9368,
        ],
    ],

    'airport' => [
        'pickup' => [
            'name' => 'Imphal, Manipur, India',
            'lat'  => 24.8170,
            'lng'  => 93.9368,
        ],
        'destination' => [
            'name' => 'Imphal International Airport, Manipur, India',
            'lat'  => 24.7595,
            'lng'  => 93.8965,
        ],
    ],

];


// ============================================================
// GET ROUTE FROM COMMAND LINE
// ============================================================

$routeKey = null;

foreach (array_slice($argv, 1) as $arg) {

    if (str_starts_with($arg, '--route=')) {

        $routeKey = strtolower(
            trim(substr($arg, 8))
        );

        break;
    }
}


// ============================================================
// SHOW AVAILABLE ROUTES
// ============================================================

if ($routeKey === null) {

    echo PHP_EOL;
    echo "============================================================\n";
    echo " ManipurApp Taxi - Gemini Route Test\n";
    echo "============================================================\n";
    echo PHP_EOL;

    echo "Please select a route:\n";
    echo PHP_EOL;

    foreach ($routes as $key => $route) {

        echo "  "
            . str_pad($key, 12)
            . " "
            . $route['pickup']['name']
            . " -> "
            . $route['destination']['name']
            . PHP_EOL;
    }

    echo PHP_EOL;
    echo "Example:\n";
    echo PHP_EOL;

    echo "  php taxi-route-test-worker.php --route=mao\n";
    echo PHP_EOL;

    exit(1);
}


// ============================================================
// VALIDATE ROUTE
// ============================================================

if (!isset($routes[$routeKey])) {

    fwrite(
        STDERR,
        PHP_EOL
        . "ERROR: Unknown route: {$routeKey}"
        . PHP_EOL
        . PHP_EOL
    );

    echo "Available routes:\n";

    foreach (array_keys($routes) as $key) {
        echo "  - {$key}\n";
    }

    echo PHP_EOL;

    exit(1);
}


$route = $routes[$routeKey];

$pickup = $route['pickup'];

$destination = $route['destination'];


// ============================================================
// VALIDATE GEMINI CONFIGURATION
// ============================================================

if (
    $geminiApiKey === ''
    || $geminiApiKey === 'YOUR_GEMINI_API_KEY_HERE'
) {

    fwrite(
        STDERR,
        "ERROR: Configure \$geminiApiKey first.\n"
    );

    exit(1);
}

if (
    $geminiEndpoint === ''
    || $geminiEndpoint === 'YOUR_GEMINI_ENDPOINT_HERE'
) {

    fwrite(
        STDERR,
        "ERROR: Configure \$geminiEndpoint first.\n"
    );

    exit(1);
}


// ============================================================
// BUILD PROMPT
// ============================================================

$prompt = <<<PROMPT
You are assisting a taxi booking system in Manipur, India.

Determine the estimated real road/driving distance and estimated
driving time between the following two locations.

PICKUP LOCATION:
{$pickup['name']}

Pickup coordinates:
Latitude: {$pickup['lat']}
Longitude: {$pickup['lng']}

DESTINATION:
{$destination['name']}

Destination coordinates:
Latitude: {$destination['lat']}
Longitude: {$destination['lng']}

IMPORTANT INSTRUCTIONS:

1. Calculate the estimated driving/road distance.
2. Do NOT use straight-line distance.
3. Return distance in kilometres.
4. Return estimated driving time in minutes.
5. Do not calculate taxi fare.
6. Do not provide route instructions.
7. Do not explain your answer.
8. Return ONLY valid JSON.
9. The JSON must contain exactly these fields:
   distance_km
   eta_minutes

Return this format:

{
  "distance_km": 123.4,
  "eta_minutes": 180
}
PROMPT;


// ============================================================
// REQUEST PAYLOAD
// ============================================================

$payload = [

    'model' => $geminiModel,

    'input' => [[

        'type' => 'text',

        'text' => $prompt,

    ]],

    'response_format' => [

        'type' => 'text',

        'mime_type' => 'application/json',

    ],

];


// ============================================================
// ENCODE REQUEST
// ============================================================

$body = json_encode(
    $payload,
    JSON_UNESCAPED_UNICODE
    | JSON_UNESCAPED_SLASHES
);

if ($body === false) {

    throw new RuntimeException(
        'Unable to encode Gemini request.'
    );
}


// ============================================================
// DISPLAY TEST
// ============================================================

echo PHP_EOL;

echo "============================================================\n";
echo " ManipurApp Taxi - Gemini Route Test\n";
echo "============================================================\n";

echo PHP_EOL;

echo "Route:\n";
echo "  {$routeKey}\n";

echo PHP_EOL;

echo "Pickup:\n";
echo "  {$pickup['name']}\n";
echo "  {$pickup['lat']}, {$pickup['lng']}\n";

echo PHP_EOL;

echo "Destination:\n";
echo "  {$destination['name']}\n";
echo "  {$destination['lat']}, {$destination['lng']}\n";

echo PHP_EOL;

echo "Calling Gemini {$geminiModel}...\n";

echo PHP_EOL;


// ============================================================
// CURL
// ============================================================

$ch = curl_init($geminiEndpoint);

if ($ch === false) {

    throw new RuntimeException(
        'Unable to initialize cURL.'
    );
}

curl_setopt_array($ch, [

    CURLOPT_POST => true,

    CURLOPT_RETURNTRANSFER => true,

    CURLOPT_HTTPHEADER => [

        'Content-Type: application/json',

        'x-goog-api-key: ' . $geminiApiKey,

    ],

    CURLOPT_POSTFIELDS => $body,

    CURLOPT_CONNECTTIMEOUT => 30,

    CURLOPT_TIMEOUT => 120,

]);


$rawResponse = curl_exec($ch);

$httpCode = (int)curl_getinfo(
    $ch,
    CURLINFO_HTTP_CODE
);

$curlError = curl_error($ch);

curl_close($ch);


// ============================================================
// CURL ERROR
// ============================================================

if ($rawResponse === false) {

    throw new RuntimeException(
        'Gemini request failed: ' . $curlError
    );
}


// ============================================================
// DECODE GEMINI RESPONSE
// ============================================================

$response = json_decode(
    $rawResponse,
    true
);

if (!is_array($response)) {

    echo "RAW RESPONSE:\n";
    echo $rawResponse;
    echo PHP_EOL;

    throw new RuntimeException(
        'Gemini returned invalid JSON.'
    );
}


// ============================================================
// API ERROR
// ============================================================

if ($httpCode < 200 || $httpCode >= 300) {

    $message =
        $response['error']['message']
        ?? 'Unknown Gemini API error.';

    throw new RuntimeException(
        'Gemini API error ('
        . $httpCode
        . '): '
        . $message
    );
}


// ============================================================
// EXTRACT MODEL OUTPUT
// ============================================================

$modelText = null;

foreach (($response['steps'] ?? []) as $step) {

    if (
        ($step['type'] ?? '')
        !== 'model_output'
    ) {
        continue;
    }

    foreach (($step['content'] ?? []) as $content) {

        if (
            ($content['type'] ?? '')
            !== 'text'
        ) {
            continue;
        }

        $text = trim(
            (string)(
                $content['text']
                ?? ''
            )
        );

        if ($text !== '') {

            $modelText = $text;

            break 2;
        }
    }
}


if ($modelText === null) {

    echo "FULL GEMINI RESPONSE:\n";

    echo json_encode(
        $response,
        JSON_PRETTY_PRINT
        | JSON_UNESCAPED_UNICODE
    );

    echo PHP_EOL;

    throw new RuntimeException(
        'Gemini returned no model output.'
    );
}


// ============================================================
// REMOVE MARKDOWN FENCES IF GEMINI ADDS THEM
// ============================================================

$modelText = trim($modelText);

if (str_starts_with($modelText, '```')) {

    $modelText = preg_replace(
        '/^```(?:json)?\s*/i',
        '',
        $modelText
    );

    $modelText = preg_replace(
        '/\s*```$/',
        '',
        $modelText
    );

    $modelText = trim($modelText);
}


// ============================================================
// PARSE RESULT
// ============================================================

$result = json_decode(
    $modelText,
    true
);

if (!is_array($result)) {

    echo "GEMINI OUTPUT:\n";

    echo $modelText;

    echo PHP_EOL;

    throw new RuntimeException(
        'Gemini did not return valid route JSON.'
    );
}


// ============================================================
// VALIDATE DISTANCE
// ============================================================

if (
    !isset($result['distance_km'])
    || !is_numeric($result['distance_km'])
) {

    throw new RuntimeException(
        'Invalid distance_km returned by Gemini.'
    );
}


// ============================================================
// VALIDATE ETA
// ============================================================

if (
    !isset($result['eta_minutes'])
    || !is_numeric($result['eta_minutes'])
) {

    throw new RuntimeException(
        'Invalid eta_minutes returned by Gemini.'
    );
}


$distanceKm = (float)$result['distance_km'];

$etaMinutes = (int)round(
    (float)$result['eta_minutes']
);


if ($distanceKm <= 0) {

    throw new RuntimeException(
        'Gemini returned an invalid distance.'
    );
}


if ($etaMinutes <= 0) {

    throw new RuntimeException(
        'Gemini returned an invalid ETA.'
    );
}

// ============================================================
// RESULT
// ============================================================

echo PHP_EOL;

echo "============================================================\n";
echo " RESULT\n";
echo "============================================================\n";

echo PHP_EOL;

echo "Estimated road distance : "
    . number_format($distanceKm, 2)
    . " km\n";

echo "Estimated driving time  : "
    . $etaMinutes
    . " minutes\n";

echo PHP_EOL;

echo "Structured result:\n";

echo json_encode(

    [
        'distance_km' => $distanceKm,

        'eta_minutes' => $etaMinutes,
    ],

    JSON_PRETTY_PRINT
    | JSON_UNESCAPED_UNICODE

);

echo PHP_EOL;

echo PHP_EOL;

exit(0);