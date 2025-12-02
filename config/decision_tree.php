<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Program Descriptions
    |--------------------------------------------------------------------------
    |
    | Descriptions for different program types recommended by the decision tree.
    | These descriptions are displayed in the decision tree analytics view.
    |
    */
    'program_descriptions' => [
        'Senior Care Program' => 'For residents aged 60 and above',
        'Youth Education Support' => 'For young residents with low income levels',
        'Health Assistance Program' => 'For residents with critical or poor health conditions',
        'Employment Training Program' => 'For unemployed residents',
        'Financial Assistance Program' => 'For residents with low or lower middle income',
        'General Community Program' => 'General community support program',
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Badge Colors
    |--------------------------------------------------------------------------
    |
    | Tailwind CSS classes for different status values to ensure consistent
    | styling across the application.
    |
    */
    'status_colors' => [
        'income_level' => [
            'High' => 'bg-green-100 text-green-800',
            'Low' => 'bg-red-100 text-red-800',
            'Lower Middle' => 'bg-yellow-100 text-yellow-800',
            'Middle' => 'bg-blue-100 text-blue-800',
            'Upper Middle' => 'bg-purple-100 text-purple-800',
            'default' => 'bg-gray-100 text-gray-800',
        ],
        'employment_status' => [
            'Full-time' => 'bg-green-100 text-green-800',
            'Unemployed' => 'bg-red-100 text-red-800',
            'Part-time' => 'bg-yellow-100 text-yellow-800',
            'Self-employed' => 'bg-blue-100 text-blue-800',
            'default' => 'bg-gray-100 text-gray-800',
        ],
        'is_pwd' => [
            'Yes' => 'bg-red-100 text-red-800',
            'No' => 'bg-green-100 text-green-800',
            'default' => 'bg-gray-100 text-gray-800',
        ],
        'risk_level' => [
            'Low' => 'bg-green-100 text-green-800',
            'Medium' => 'bg-yellow-100 text-yellow-800',
            'High' => 'bg-red-100 text-red-800',
            'default' => 'bg-gray-100 text-gray-800',
        ],
        'eligibility' => [
            'Eligible' => 'bg-green-100 text-green-800',
            'Not Eligible' => 'bg-red-100 text-red-800',
            'Ineligible' => 'bg-red-100 text-red-800',
            'default' => 'bg-gray-100 text-gray-800',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Model Type Display Names
    |--------------------------------------------------------------------------
    |
    | Display names for different model types used in the decision tree.
    |
    */
    'model_type_names' => [
        'decision_tree' => 'Decision Tree',
        'random_forest' => 'Random Forest',
        'xgboost' => 'XGBoost',
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Names
    |--------------------------------------------------------------------------
    |
    | Display names for features used in the decision tree models.
    | Index corresponds to feature position in the feature array.
    |
    */
    'feature_display_names' => [
        0 => 'Age',
        1 => 'Family Size',
        2 => 'Education Level',
        3 => 'Income Level',
        4 => 'Employment Status',
        5 => 'Health Status',
    ],
];

