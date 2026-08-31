<?php

return [
    'khalti' => [
        'secret_key' => getenv('KHALTI_SECRET_KEY') ?: 'test_secret_key',
        'payment_url' => 'https://a.khalti.com/api/v2/epayment/initiate/',
        'verification_url' => 'https://a.khalti.com/api/v2/epayment/verify/',
    ],
    'esewa' => [
        'secret_key' => getenv('ESEWA_SECRET_KEY') ?: 'test_esewa_secret',
        'payment_url' => 'https://rc-epay.esewa.com.np/api/epay/main/v2/',
    ],
    'shipping_threshold' => 3000,
    'shipping_fee' => 250,
];
