<?php

declare(strict_types=1);

/**
 * Taxi module Gemini configuration.
 *
 * Keep this file out of version control if it contains a real API key.
 */
return [
    'api_key' => trim((string) (getenv('MANIPURAPP_GEMINI_API_KEY') ?: '')),
    'model' => 'gemini-3.5-flash',
    'endpoint' => 'https://generativelanguage.googleapis.com/v1beta/interactions',
    'timeout' => 600,
];
