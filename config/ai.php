<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Integration Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file acts as the single source of truth for the
    | AI Enhancement Layer integration (n8n). It follows the architecture
    | principle where AI is not part of the Core Business Logic.
    |
    */

    'enabled' => env('N8N_ENABLED', false),

    'n8n' => [
        'base_url' => env('N8N_BASE_URL', 'http://localhost:5678'),
        'timeout' => env('N8N_TIMEOUT', 30),
    ],

];
