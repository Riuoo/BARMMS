<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Document Types Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for different document types
    | and their corresponding PDF templates.
    |
    */

    'types' => [
        'Certificate of Residency' => [
            'template' => 'admin.pdfs.certificate_of_residency_pdf',
            'description' => 'Certification that person is a bonafide resident',
            'validity' => '6 months',
            'requirements' => [
                'Valid ID',
                'Proof of residence',
                'Purpose of request'
            ]
        ],
        
        'Certificate of Indigency' => [
            'template' => 'admin.pdfs.certificate_of_indigency_pdf',
            'description' => 'Certification for indigent families',
            'validity' => '6 months',
            'requirements' => [
                'Valid ID',
                'Income certificate',
                'Purpose of request'
            ]
        ],
        
        'Certificate of Low Income' => [
            'template' => 'admin.pdfs.certificate_of_low_income_pdf',
            'description' => 'Certification for low-income families',
            'validity' => '6 months',
            'requirements' => [
                'Valid ID',
                'Income certificate',
                'Purpose of request'
            ]
        ],
        
        'Barangay Clearance' => [
            'template' => 'admin.pdfs.barangay_clearance_pdf',
            'description' => 'Official clearance from barangay showing no pending cases',
            'validity' => '6 months',
            'requirements' => [
                'Valid ID',
                'Community Tax Certificate',
                'Purpose of request'
            ]
        ],
        
        'Certification' => [
            'template' => 'admin.pdfs.certification_pdf',
            'description' => 'General certification document',
            'validity' => '6 months',
            'requirements' => [
                'Valid ID',
                'Purpose of request'
            ]
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for document generation
    |
    */

    'defaults' => [
        'template' => 'admin.pdfs.document_request_pdf',
        'validity' => '6 months',
        'requirements' => [
            'Valid ID',
            'Purpose of request'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Document Statuses
    |--------------------------------------------------------------------------
    |
    | Available statuses for document requests
    |
    */

    'statuses' => [
        'pending' => [
            'label' => 'Pending',
            'color' => 'yellow',
            'icon' => 'fas fa-clock'
        ],
        'approved' => [
            'label' => 'Approved',
            'color' => 'blue',
            'icon' => 'fas fa-check'
        ],
        'completed' => [
            'label' => 'Completed',
            'color' => 'green',
            'icon' => 'fas fa-check-circle'
        ]
    ]
]; 