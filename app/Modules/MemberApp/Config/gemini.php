<?php

declare(strict_types=1);

/**
 * Optional standalone MemberApp Gemini configuration.
 *
 * When the Taxi module is installed, MemberApp automatically reuses
 * Taxi/Config/gemini.php instead, so normally this file does not need a key.
 */
return [
    'api_key' => '',
    'model' => 'gemini-3.5-flash',
    'endpoint' => 'https://generativelanguage.googleapis.com/v1beta/interactions',
    'timeout' => 600,
];
