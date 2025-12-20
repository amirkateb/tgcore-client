<?php

return [
    'route' => [
        'path' => env('TGCORE_CLIENT_PATH', '/tgcore/ingest'),
        'middleware' => array_filter(explode(',', env('TGCORE_CLIENT_MIDDLEWARE', 'api,tgcore-client.verify'))),
    ],

    'signature' => [
        'tolerance_seconds' => (int) env('TGCORE_CLIENT_TOLERANCE_SECONDS', 300),
        'replay_ttl_seconds' => (int) env('TGCORE_CLIENT_REPLAY_TTL_SECONDS', 600),
    ],

    'rate_limits' => [
        'ingest_per_minute' => (int) env('TGCORE_CLIENT_INGEST_RATE_PER_MINUTE', 120),
        'gateway_per_minute' => (int) env('TGCORE_CLIENT_GATEWAY_RATE_PER_MINUTE', 120),
    ],

    'resolver' => env('TGCORE_CLIENT_RESOLVER', 'config'),

    'bots' => [
    ],

    'database' => [
        'bots_table' => 'tgcore_client_bots',
        'receipts_table' => 'tgcore_client_update_receipts',
    ],

    'queue' => [
        'enabled' => (bool) env('TGCORE_CLIENT_QUEUE_ENABLED', true),
        'connection' => env('TGCORE_CLIENT_QUEUE_CONNECTION', null),
        'name' => env('TGCORE_CLIENT_QUEUE_NAME', 'default'),
    ],

    'gateway' => [
        'base_url' => env('TGCORE_GATEWAY_BASE_URL', ''),
        'path_prefix' => env('TGCORE_GATEWAY_PATH_PREFIX', '/api/tgcore/consumer'),
        'timeout_seconds' => (int) env('TGCORE_GATEWAY_TIMEOUT', 15),
        'connect_timeout_seconds' => (int) env('TGCORE_GATEWAY_CONNECT_TIMEOUT', 7),
    ],

    'handlers' => [
        'types' => [
        ],
        'commands' => [
        ],
        'default' => [
        ],
    ],

    'behavior' => [
        'throw_on_handler_exception' => (bool) env('TGCORE_CLIENT_THROW_ON_HANDLER_EXCEPTION', false),
    ],
];