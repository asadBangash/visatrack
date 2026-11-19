<?php

return [
    'default_visa_type' => env('VISA_DEFAULT_TYPE', 'NIV'),

    /*
    | Surname verification for public status checks (first 5 letters, uppercase).
    | Strategies: last_token (last whitespace-delimited token), configured_token (admin override only).
    | When strategy cannot produce a value, passport-only verification is used for that record
    | (surname_verification_hash left null — public form must still submit surname; see VisaStatusService).
    */
    'surname_strategy' => env('VISA_SURNAME_STRATEGY', 'last_token'),

    'confirmation_version' => '01.02.00',

    'default_required_documents' => 'Passport; Evidence of approved I-129',

    'statuses' => [
        'application_received' => 'Application Received',
        'approved' => 'Approved',
        'issued' => 'Issued',
    ],

    'demo_admin_email' => env('DEMO_ADMIN_EMAIL', 'admin@visa-demo.local'),
];