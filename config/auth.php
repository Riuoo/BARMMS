<?php

return [
    'defaults' => [
        'guard' => 'barangay',
        'passwords' => 'barangay_profiles',
    ],

    'guards' => [
        'barangay' => [
            'driver' => 'session',
            'provider' => 'barangay_profiles',
        ],
        'residents' => [
            'driver' => 'session',
            'provider' => 'residents',
        ],
    ],

    'providers' => [
        'barangay_profiles' => [
            'driver' => 'eloquent',
            'model' => App\Models\BarangayProfile::class,
        ],
        'residents' => [
            'driver' => 'eloquent',
            'model' => App\Models\Residents::class,
        ],
    ],

    'passwords' => [
        'barangay_profiles' => [
            'provider' => 'barangay_profiles',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
        'residents' => [
            'provider' => 'residents',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),
];
