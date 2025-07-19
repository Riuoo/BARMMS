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
        
        'Business Permit' => [
            'template' => 'admin.pdfs.business_permit_pdf',
            'description' => 'Permission to operate business in barangay',
            'validity' => '1 year',
            'requirements' => [
                'Barangay Clearance',
                'Community Tax Certificate',
                'Fire Safety Certificate',
                'Sanitary Permit',
                'Zoning Clearance'
            ]
        ],
        
        'Certificate of Good Moral Character' => [
            'template' => 'admin.pdfs.certificate_of_good_moral_character_pdf',
            'description' => 'Certification of good moral character',
            'validity' => '6 months',
            'requirements' => [
                'Valid ID',
                'Purpose of request',
                'Character references'
            ]
        ],
        
        'Certificate of Live Birth' => [
            'template' => 'admin.pdfs.certificate_of_live_birth_pdf',
            'description' => 'Certification of live birth in barangay',
            'validity' => '6 months',
            'requirements' => [
                'Valid ID',
                'Birth certificate (if available)',
                'Purpose of request'
            ]
        ],
        
        'Certificate of Death' => [
            'template' => 'admin.pdfs.certificate_of_death_pdf',
            'description' => 'Certification of death in barangay',
            'validity' => '6 months',
            'requirements' => [
                'Valid ID of requester',
                'Death certificate (if available)',
                'Purpose of request'
            ]
        ],
        
        'Certificate of Marriage' => [
            'template' => 'admin.pdfs.certificate_of_marriage_pdf',
            'description' => 'Certification of marriage status',
            'validity' => '6 months',
            'requirements' => [
                'Valid ID',
                'Marriage certificate (if available)',
                'Purpose of request'
            ]
        ],
        
        'Barangay ID' => [
            'template' => 'admin.pdfs.barangay_id_pdf',
            'description' => 'Official barangay identification card',
            'validity' => '1 year',
            'requirements' => [
                'Valid ID',
                'Recent photo',
                'Purpose of request'
            ]
        ],
        
        'Certificate of No Pending Case' => [
            'template' => 'admin.pdfs.certificate_of_no_pending_case_pdf',
            'description' => 'Certification of no pending cases',
            'validity' => '6 months',
            'requirements' => [
                'Valid ID',
                'Purpose of request'
            ]
        ],
        
        'Certificate of No Derogatory Record' => [
            'template' => 'admin.pdfs.certificate_of_no_derogatory_record_pdf',
            'description' => 'Certification of no derogatory records',
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