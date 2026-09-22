<?php
declare(strict_types=1);

return [
    'api_key' => '',
    'model' => 'openai/gpt-oss-120b',
    'endpoint' => 'https://api.groq.com/openai/v1/chat/completions',
    'timeout' => 60,
    'max_tool_result_bytes' => 5000,
    'parallel_tool_calls' => false,
];
