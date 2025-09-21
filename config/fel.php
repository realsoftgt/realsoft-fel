<?php
return [
    'default_provider' => env('FEL_PROVIDER', 'infile'),
    'default_country'  => env('FEL_COUNTRY', 'GT'),

    'providers' => [
        'infile' => [
            'gt' => [
                'base_uri' => env('INFILE_GT_BASE_URI'),
                'apikey'   => env('INFILE_GT_APIKEY'),
                'user'     => env('INFILE_GT_USER'),
                'emitter'  => env('INFILE_GT_EMITTER_NIT'),
                'timeout'  => 20,
            ],
            'sv' => [
                'base_uri' => env('INFILE_SV_BASE_URI'),
                'apikey'   => env('INFILE_SV_APIKEY'),
                'user'     => env('INFILE_SV_USER'),
                'emitter'  => env('INFILE_SV_EMITTER_NIT'),
                'timeout'  => 20,
            ],
        ],
    ],

    'storage' => [
        'disk' => env('FEL_DISK', 'local'),
        'path' => 'fel/',
    ],

    'signing' => [
        'engine' => env('FEL_SIGN_ENGINE', 'provider'),
        'local' => [
            'cert_path' => env('FEL_CERT_PATH'),
            'key_path'  => env('FEL_KEY_PATH'),
            'key_pass'  => env('FEL_KEY_PASS'),
        ],
    ],

    'webhooks' => [
        'enabled' => true,
        'secret'  => env('FEL_WEBHOOK_SECRET',''),
        'route'   => '/fel/webhooks',
    ],

    'contingency' => [
        'enabled' => true,
        'access_number' => [
            'per_establishment' => true,
            'length' => 18,
        ],
        'daily_closure' => [
            'enabled' => true,
            'at' => '23:55',
            'mailbox' => 'efactura@sat.gob.gt',
            'format' => 'csv',
        ],
    ],

    'gt_policy' => [
        'cf_max_amount' => 2500.00,
        'require_id_over_cf_max' => true,
    ],
];
