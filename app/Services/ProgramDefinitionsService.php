<?php

namespace App\Services;

class ProgramDefinitionsService
{
    /**
     * Get all hardcoded program definitions
     */
    public static function getProgramDefinitions(): array
    {
        return [
            // Employment Programs
            [
                'name' => 'Job Training Program',
                'type' => 'employment',
                'description' => 'Provides job training and skills development for unemployed residents.',
                'criteria' => [
                    'operator' => 'AND',
                    'conditions' => [
                        ['field' => 'employment_status', 'operator' => 'equals', 'value' => 'Unemployed'],
                        ['field' => 'age', 'operator' => 'greater_than', 'value' => 17],
                        ['field' => 'age', 'operator' => 'less_than', 'value' => 66],
                        [
                            'operator' => 'OR',
                            'conditions' => [
                                ['field' => 'income_level', 'operator' => 'equals', 'value' => 'Low'],
                                ['field' => 'income_level', 'operator' => 'equals', 'value' => 'Lower Middle'],
                            ]
                        ],
                    ]
                ],
                'priority' => 5,
            ],
            [
                'name' => 'Entrepreneurship Support',
                'type' => 'employment',
                'description' => 'Support program for residents interested in starting their own business.',
                'criteria' => [
                    'operator' => 'AND',
                    'conditions' => [
                        ['field' => 'employment_status', 'operator' => 'equals', 'value' => 'Unemployed'],
                        ['field' => 'age', 'operator' => 'greater_than', 'value' => 24],
                        ['field' => 'age', 'operator' => 'less_than', 'value' => 51],
                        [
                            'operator' => 'OR',
                            'conditions' => [
                                ['field' => 'education_level', 'operator' => 'equals', 'value' => 'High School'],
                                ['field' => 'education_level', 'operator' => 'equals', 'value' => 'Vocational'],
                                ['field' => 'education_level', 'operator' => 'equals', 'value' => 'College'],
                                ['field' => 'education_level', 'operator' => 'equals', 'value' => 'Post Graduate'],
                            ]
                        ],
                    ]
                ],
                'priority' => 4,
            ],

            // Health Programs
            [
                'name' => 'Chronic Disease Management',
                'type' => 'health',
                'description' => 'Program for residents with chronic health conditions requiring ongoing management.',
                'criteria' => [
                    'operator' => 'AND',
                    'conditions' => [
                        ['field' => 'medical.has_chronic_conditions', 'operator' => 'equals', 'value' => true],
                    ]
                ],
                'priority' => 6,
            ],
            [
                'name' => 'Preventive Health Check',
                'type' => 'health',
                'description' => 'Encourages regular health checkups for residents over 40 who have not had recent medical visits.',
                'criteria' => [
                    'operator' => 'AND',
                    'conditions' => [
                        ['field' => 'age', 'operator' => 'greater_than', 'value' => 40],
                        ['field' => 'medical.has_recent_visits', 'operator' => 'equals', 'value' => false],
                    ]
                ],
                'priority' => 3,
            ],

            // Education Programs
            [
                'name' => 'Scholarship Program',
                'type' => 'education',
                'description' => 'Scholarship opportunities for young residents pursuing education.',
                'criteria' => [
                    'operator' => 'AND',
                    'conditions' => [
                        ['field' => 'age', 'operator' => 'greater_than', 'value' => 15],
                        ['field' => 'age', 'operator' => 'less_than', 'value' => 26],
                        [
                            'operator' => 'OR',
                            'conditions' => [
                                ['field' => 'education_level', 'operator' => 'equals', 'value' => 'No Education'],
                                ['field' => 'education_level', 'operator' => 'equals', 'value' => 'Elementary'],
                                ['field' => 'education_level', 'operator' => 'equals', 'value' => 'High School'],
                            ]
                        ],
                        ['field' => 'income_level', 'operator' => 'equals', 'value' => 'Low'],
                    ]
                ],
                'priority' => 5,
            ],
            [
                'name' => 'Adult Education',
                'type' => 'education',
                'description' => 'Educational support for adult residents with limited education.',
                'criteria' => [
                    'operator' => 'AND',
                    'conditions' => [
                        ['field' => 'age', 'operator' => 'greater_than', 'value' => 25],
                        [
                            'operator' => 'OR',
                            'conditions' => [
                                ['field' => 'education_level', 'operator' => 'equals', 'value' => 'No Education'],
                                ['field' => 'education_level', 'operator' => 'equals', 'value' => 'Elementary'],
                            ]
                        ],
                        ['field' => 'employment_status', 'operator' => 'equals', 'value' => 'Unemployed'],
                    ]
                ],
                'priority' => 4,
            ],

            // Social Programs
            [
                'name' => 'Financial Assistance',
                'type' => 'social',
                'description' => 'Financial support for residents in need.',
                'criteria' => [
                    'operator' => 'AND',
                    'conditions' => [
                        ['field' => 'income_level', 'operator' => 'equals', 'value' => 'Low'],
                        [
                            'operator' => 'OR',
                            'conditions' => [
                                ['field' => 'employment_status', 'operator' => 'equals', 'value' => 'Unemployed'],
                                ['field' => 'is_pwd', 'operator' => 'equals', 'value' => true],
                                ['field' => 'family_size', 'operator' => 'greater_than', 'value' => 5],
                            ]
                        ],
                    ]
                ],
                'priority' => 6,
            ],
            [
                'name' => 'Family Support Program',
                'type' => 'social',
                'description' => 'Support program for large families with limited income.',
                'criteria' => [
                    'operator' => 'AND',
                    'conditions' => [
                        ['field' => 'family_size', 'operator' => 'greater_than', 'value' => 4],
                        ['field' => 'income_level', 'operator' => 'equals', 'value' => 'Low'],
                    ]
                ],
                'priority' => 4,
            ],

            // Safety Programs
            [
                'name' => 'Crime Prevention Program',
                'type' => 'safety',
                'description' => 'Program for residents with multiple blotter reports to prevent further incidents.',
                'criteria' => [
                    'operator' => 'AND',
                    'conditions' => [
                        ['field' => 'blotter.total_count', 'operator' => 'greater_than', 'value' => 1],
                    ]
                ],
                'priority' => 5,
            ],
            [
                'name' => 'Community Watch',
                'type' => 'safety',
                'description' => 'Community watch program for responsible residents to help maintain safety.',
                'criteria' => [
                    'operator' => 'AND',
                    'conditions' => [
                        ['field' => 'age', 'operator' => 'greater_than', 'value' => 17],
                        ['field' => 'blotter.has_recent_incidents', 'operator' => 'equals', 'value' => false],
                    ]
                ],
                'priority' => 3,
            ],
        ];
    }
}

