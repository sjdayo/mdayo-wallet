<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the queue connections below you wish
    | to use as your default connection for wallet jobs.
    |
    */
    'default' => env('WALLET_QUEUE_CONNECTION', 'rabbitmq'),

    /*
    |--------------------------------------------------------------------------
    | Wallet Queue Jobs
    |--------------------------------------------------------------------------
    |
    | Define the queue name, retries, and backoff for credit/debit jobs.
    |
    */
    'queue' => [
        'connection' => env('WALLET_QUEUE_CONNECTION', 'rabbitmq'),
        'queue' => env('WALLET_QUEUE_NAME', 'wallet-jobs'),
        'retry_after' => env('WALLET_QUEUE_RETRY_AFTER', 90),
        'tries' => env('WALLET_QUEUE_TRIES', 5),
        'backoff' => [5, 15, 30], // seconds between retries
        'failed_job_exchange' => env('WALLET_QUEUE_FAILED_EXCHANGE', 'wallet-failed'),
    ],

    /*
    |--------------------------------------------------------------------------
    | RabbitMQ Connection Settings
    |--------------------------------------------------------------------------
    |
    | These settings are used if your WALLET_QUEUE_CONNECTION is rabbitmq.
    |
    */
    'rabbitmq' => [
        'hosts' => [
            [
                'host' => env('RABBITMQ_HOST', '127.0.0.1'),
                'port' => env('RABBITMQ_PORT', 5672),
                'user' => env('RABBITMQ_USER', 'guest'),
                'password' => env('RABBITMQ_PASSWORD', 'guest'),
                'vhost' => env('RABBITMQ_VHOST', '/'),
            ],
        ],
        'options' => [
            'exchange' => [
                'name' => env('RABBITMQ_EXCHANGE', 'wallet-exchange'),
                'type' => env('RABBITMQ_EXCHANGE_TYPE', 'direct'),
                'declare' => true,
            ],
            'queue' => [
                'declare' => true,
                'arguments' => [
                    // optional dead-letter exchange
                    'x-dead-letter-exchange' => env('RABBITMQ_DEAD_LETTER_EXCHANGE', 'wallet-failed'),
                    'x-dead-letter-routing-key' => env('RABBITMQ_DEAD_LETTER_ROUTING_KEY', 'wallet-failed'),
                    // optional prefetch limit
                    'x-prefetch-count' => 5,
                ],
            ],
        ],
        'retry_after' => env('WALLET_QUEUE_RETRY_AFTER', 90),
        'block_for' => null,
    ],
];
