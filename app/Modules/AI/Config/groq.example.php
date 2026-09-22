<?php
declare(strict_types=1);

return [
    'api_key' => 'YOUR_GROQ_API_KEY_HERE',
    'model' => 'openai/gpt-oss-120b',
    'endpoint' => 'https://api.groq.com/openai/v1/chat/completions',
    'timeout' => 60,
    'max_tool_result_bytes' => 5000,
    // GPT-OSS 120B does not support parallel local tool calls; keep false.
    'parallel_tool_calls' => false,
];
