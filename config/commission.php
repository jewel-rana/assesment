<?php
return [
    'fees' => [
        'withdraw' => [
            'private' => '0.3',
            'business' => '0.5'
        ],
        'deposit' => [
            'private' => '0.03',
            'business' => '0.03'
        ]
    ],
    'free_of_charges' => [
        'amount' => 1000.00,
        'transaction_types' => ['withdraw'],
        'client_types' => ['private']
    ]
];
