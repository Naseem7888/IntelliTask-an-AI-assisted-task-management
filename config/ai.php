<?php

return [
    // Feature toggles
    'suggestions_enabled' => (bool) env('AI_SUGGESTIONS_ENABLED', true),
    'mock_responses' => (bool) env('AI_MOCK_RESPONSES', false),
    'debug_mode' => (bool) env('AI_DEBUG_MODE', false),
    'verbose_logging' => (bool) env('AI_VERBOSE_LOGGING', false),

    // Caching and limits
    'cache_enabled' => (bool) env('AI_CACHE_ENABLED', true),
    'cache_ttl' => (int) env('AI_CACHE_TTL', 3600), // seconds
    'rate_limit' => (int) env('AI_RATE_LIMIT', 10),
    'max_suggestions' => (int) env('AI_MAX_SUGGESTIONS', 5),

    // Queue/retry behavior (if you later switch QUEUE_CONNECTION from sync)
    'queue_enabled' => (bool) env('AI_QUEUE_ENABLED', false),
    'retry_attempts' => (int) env('AI_RETRY_ATTEMPTS', 3),
    'retry_delay' => (int) env('AI_RETRY_DELAY', 1000), // ms

    // Safety, auditing, logging
    'content_filter' => (bool) env('AI_CONTENT_FILTER', true),
    'log_requests' => (bool) env('AI_LOG_REQUESTS', false),
    'audit_enabled' => (bool) env('AI_AUDIT_ENABLED', false),
];
