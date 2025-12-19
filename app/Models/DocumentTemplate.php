<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    protected $fillable = [
        'document_type',
        'description',
        'header_content',
        'body_content',
        'footer_content',
        'placeholders',
        'is_active'
    ];

    protected $casts = [
        'placeholders' => 'array',
        'is_active' => 'boolean'
    ];

    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class);
    }

    /**
     * Get the default template content for a document type
     */
    public static function getDefaultTemplate($documentType)
    {
        $defaults = [
            'Barangay Clearance' => [
                'header_content' => '
                    <div style="text-align: right; margin-bottom: 20px;">
                        <div style="font-size: 14px; line-height: 1.6;">
                            <div>Republic of the Philippines</div>
                            <div>Province of [province_name]</div>
                            <div>Municipality of [municipality_name]</div>
                            <div>Barangay of [barangay_name]</div>
                        </div>
                    </div>
                    <div style="text-align: center; margin-bottom: 30px;">
                        <div style="font-weight: bold; font-size: 18px; margin-bottom: 10px;">OFFICE OF THE PUNONG BARANGAY</div>
                        <div style="font-weight: bold; font-size: 24px; text-decoration: underline; margin-top: 15px;">BARANGAY CLEARANCE</div>
                    </div>
                ',
                'body_content' => '
                    <div style="margin-top: 30px; line-height: 1.8;">
                        <div style="text-align: center; font-weight: bold; margin-bottom: 20px; font-size: 14px;">TO WHOM IT MAY CONCERN:</div>
                        
                        <div style="margin-bottom: 20px; text-align: justify;">
                            <p style="margin-bottom: 10px;">This is to certify that the person whose name below has requested a <strong>RECORD CLEARANCE</strong> from this office of the Punong Barangay and result is listed below:</p>
                        </div>
                        
                        <div style="margin-top: 25px; margin-bottom: 20px;">
                            <div style="margin-bottom: 8px;"><strong>NAME:</strong> [resident_name]</div>
                            <div style="margin-bottom: 8px;"><strong>ADDRESS:</strong> [resident_address]</div>
                            [birth_date_section]
                            [birth_place_section]
                            [status_section]
                            [remarks_section]
                        </div>
                        
                        <div style="margin-top: 25px; text-align: justify;">
                            <p>This certification is being issued this <strong>[day] day of [month] [year]</strong>, as a requirement/s and for whatever legal purpose/s it may serve him best.</p>
                        </div>
                    </div>
                ',
                'footer_content' => '
                    <div style="margin-top: 100px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <!-- Certified by (Punong Barangay) - Left side -->
                                <td style="width: 50%; vertical-align: top; padding-right: 20px;">
                                    <div style="text-align: center;">
                                        <div style="margin-bottom: 60px;">
                                            <div style="border-top: 1px solid #000; width: 280px; margin: 0 auto 10px;"></div>
                                            <div style="font-weight: bold; font-size: 16px; margin-top: 5px;">[captain_name]</div>
                                            <div style="font-size: 14px; margin-top: 5px;">Punong Barangay</div>
                                        </div>
                                        <div style="font-weight: bold; font-size: 14px; margin-top: 10px;">Certified:</div>
                                    </div>
                                </td>
                                
                                <!-- Prepared by (Secretary and Purok Leader) - Right side -->
                                <td style="width: 50%; vertical-align: top; padding-left: 20px;">
                                    <div style="text-align: center;">
                                        <div style="margin-bottom: 30px;">
                                            <div style="border-top: 1px solid #000; width: 280px; margin: 0 auto 10px;"></div>
                                            <div style="font-weight: bold; font-size: 16px; margin-top: 5px;">[prepared_by_name]</div>
                                            <div style="font-size: 14px; margin-top: 5px;">Barangay Secretary</div>
                                        </div>
                                        [purok_leader_section]
                                        <div style="font-weight: bold; font-size: 14px; margin-top: 10px;">Prepared by:</div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                ',
                'placeholders' => [
                    'resident_name' => 'Full name of the resident (Auto-filled from demographics)',
                    'resident_address' => 'Complete address of the resident (Auto-filled from demographics)',
                    'birth_date' => 'Birth date (Auto-filled from demographics, can be edited)',
                    'birth_place' => 'Birth place (Required - e.g., Upper Malinao, Padada, Davao del Sur)',
                    'status' => 'Civil status (Auto-filled from demographics - marital_status)',
                    'remarks' => 'Remarks (Required - e.g., NO INCRIMINATORY RECORD OR ANY PENDING CASE/COMPLAINT FILED AGAINST HIM.)',
                    'day' => 'Day of issuance (e.g., 25TH)',
                    'month' => 'Month of issuance (e.g., February)',
                    'year' => 'Year of issuance (e.g., 2025)',
                    'barangay_name' => 'Name of the barangay',
                    'municipality_name' => 'Name of the municipality',
                    'province_name' => 'Name of the province',
                    'prepared_by_name' => 'Name of the Barangay Secretary',
                    'captain_name' => 'Name of the Punong Barangay',
                    'purok_leader_name' => 'Name of the Purok Leader - Optional',
                    'purok_number' => 'Purok number (Auto-filled from address - e.g., 5)'
                ]
            ],
            'Certificate of Residency' => [
                'header_content' => '
                    <div class="header text-center">
                        <h1>Republic of the Philippines</h1>
                        <h2>Province of [province_name]</h2>
                        <h2>Municipality of [municipality_name]</h2>
                        <h1>CERTIFICATE OF RESIDENCY</h1>
                    </div>
                ',
                'body_content' => '
                    <div class="content">
                        <p>TO WHOM IT MAY CONCERN:</p>
                        <p>This is to certify that [resident_name], of legal age, [civil_status], Filipino, is a bonafide resident of [resident_address] in Barangay [barangay_name], [municipality_name], [province_name] for the past [residence_years] years.</p>
                        <p>This certification is being issued upon the request of the above-named person for [purpose].</p>
                        <p>Issued this [day] day of [month] [year] at Barangay Lower Malinao, Padada, Davao Del Sur.</p>
                    </div>
                ',
                'footer_content' => '
                    <div class="signature-section" style="margin-top: 80px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <!-- Prepared by (Secretary) -->
                                <td style="width: 45%; vertical-align: top;">
                                    <div style="text-align: center;">
                                        <div style="border-top: 1px solid #000; width: 250px; margin: 0 auto 10px;"></div>
                                        <div style="font-weight: bold; font-size: 16px;">[prepared_by_name]</div>
                                        <div style="font-size: 14px; color: #666;">Barangay Secretary</div>
                                        <div style="font-size: 12px; color: #666; margin-top: 5px;">Prepared by</div>
                                    </div>
                                </td>
                                
                                <!-- Certified by (Punong Barangay/Captain) -->
                                <td style="width: 45%; vertical-align: top;">
                                    <div style="text-align: center;">
                                        <div style="border-top: 1px solid #000; width: 250px; margin: 0 auto 10px;"></div>
                                        <div style="font-weight: bold; font-size: 16px;">[captain_name]</div>
                                        <div style="font-size: 14px; color: #666;">Punong Barangay</div>
                                        <div style="font-size: 12px; color: #666; margin-top: 5px;">Certified by</div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                ',
                'placeholders' => [
                    'resident_name' => 'Name of the resident',
                    'resident_address' => 'Address of the resident',
                    'civil_status' => 'Civil status of the resident',
                    'residence_years' => 'Number of years of residence',
                    'purpose' => 'Purpose of the certificate',
                    'day' => 'Day of issuance',
                    'month' => 'Month of issuance',
                    'year' => 'Year of issuance',
                    'barangay_name' => 'Name of the barangay',
                    'municipality_name' => 'Name of the municipality',
                    'province_name' => 'Name of the province',
                    'official_name' => 'Name of the barangay captain',
                    'prepared_by_name' => 'Name of the person who prepared the document',
                    'captain_name' => 'Name of the Punong Barangay'
                ]
            ],
            'Certificate of Indigency' => [
                'header_content' => '
                    <div class="header text-center">
                        <h1>Republic of the Philippines</h1>
                        <h2>Province of [province_name]</h2>
                        <h2>Municipality of [municipality_name]</h2>
                        <h1>CERTIFICATE OF INDIGENCY</h1>
                    </div>
                ',
                'body_content' => '
                    <div class="content">
                        <p>TO WHOM IT MAY CONCERN:</p>
                        <p>This is to certify that [resident_name], of legal age, [civil_status], Filipino, and a resident of [resident_address], is indigent and belongs to the low-income family in this barangay with an estimated monthly income of [monthly_income] pesos.</p>
                        <p>This certification is being issued upon the request of the above-named person for [purpose] to be used at [purpose_location].</p>
                        <p>Request type: [verbal_request]</p>
                        <p>Issued this [day] day of [month] [year] at Barangay Lower Malinao, Padada, Davao Del Sur.</p>
                    </div>
                ',
                'footer_content' => '
                    <div class="signature-section" style="margin-top: 80px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <!-- Prepared by (Secretary) -->
                                <td style="width: 45%; vertical-align: top;">
                                    <div style="text-align: center;">
                                        <div style="border-top: 1px solid #000; width: 250px; margin: 0 auto 10px;"></div>
                                        <div style="font-weight: bold; font-size: 16px;">[prepared_by_name]</div>
                                        <div style="font-size: 14px; color: #666;">Barangay Secretary</div>
                                        <div style="font-size: 12px; color: #666; margin-top: 5px;">Prepared by</div>
                                    </div>
                                </td>
                                
                                <!-- Certified by (Punong Barangay/Captain) -->
                                <td style="width: 45%; vertical-align: top;">
                                    <div style="text-align: center;">
                                        <div style="border-top: 1px solid #000; width: 250px; margin: 0 auto 10px;"></div>
                                        <div style="font-weight: bold; font-size: 16px;">[captain_name]</div>
                                        <div style="font-size: 14px; color: #666;">Punong Barangay</div>
                                        <div style="font-size: 12px; color: #666; margin-top: 5px;">Certified by</div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                ',
                'placeholders' => [
                    'resident_name' => 'Name of the resident',
                    'resident_address' => 'Address of the resident',
                    'civil_status' => 'Civil status of the resident',
                    'monthly_income' => 'Monthly income amount',
                    'purpose' => 'Purpose of the certificate',
                    'purpose_location' => 'Location where this certificate will be used',
                    'verbal_request' => 'Was this a verbal request?',
                    'day' => 'Day of issuance',
                    'month' => 'Month of issuance',
                    'year' => 'Year of issuance',
                    'barangay_name' => 'Name of the barangay',
                    'municipality_name' => 'Name of the municipality',
                    'province_name' => 'Name of the province',
                    'official_name' => 'Name of the barangay captain',
                    'prepared_by_name' => 'Name of the person who prepared the document',
                    'captain_name' => 'Name of the Punong Barangay'
                ]
            ],
            'Certificate of Low Income' => [
                'header_content' => '
                    <div class="header text-center">
                        <h1>Republic of the Philippines</h1>
                        <h2>Province of [province_name]</h2>
                        <h2>Municipality of [municipality_name]</h2>
                        <h1>CERTIFICATE OF LOW INCOME</h1>
                    </div>
                ',
                'body_content' => '
                    <div class="content">
                        <p>TO WHOM IT MAY CONCERN:</p>
                        <p>This is to certify that [resident_name], of legal age, [civil_status], Filipino, and a resident of [resident_address], belongs to a low-income family in this barangay with an estimated monthly income of [monthly_income] pesos.</p>
                        <p>This certification is being issued upon the request of the above-named person for [purpose] to be used at [purpose_location].</p>
                        <p>Issued this [day] day of [month] [year] at Barangay Lower Malinao, Padada, Davao Del Sur.</p>
                    </div>
                ',
                'footer_content' => '
                    <div class="signature-section" style="margin-top: 80px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <!-- Prepared by (Secretary) -->
                                <td style="width: 45%; vertical-align: top;">
                                    <div style="text-align: center;">
                                        <div style="border-top: 1px solid #000; width: 250px; margin: 0 auto 10px;"></div>
                                        <div style="font-weight: bold; font-size: 16px;">[prepared_by_name]</div>
                                        <div style="font-size: 14px; color: #666;">Barangay Secretary</div>
                                        <div style="font-size: 12px; color: #666; margin-top: 5px;">Prepared by</div>
                                    </div>
                                </td>
                                
                                <!-- Certified by (Punong Barangay/Captain) -->
                                <td style="width: 45%; vertical-align: top;">
                                    <div style="text-align: center;">
                                        <div style="border-top: 1px solid #000; width: 250px; margin: 0 auto 10px;"></div>
                                        <div style="font-weight: bold; font-size: 16px;">[captain_name]</div>
                                        <div style="font-size: 14px; color: #666;">Punong Barangay</div>
                                        <div style="font-size: 12px; color: #666; margin-top: 5px;">Certified by</div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                ',
                'placeholders' => [
                    'resident_name' => 'Name of the resident',
                    'resident_address' => 'Address of the resident',
                    'civil_status' => 'Civil status of the resident',
                    'monthly_income' => 'Monthly income amount',
                    'purpose' => 'Purpose of the certificate',
                    'purpose_location' => 'Location where this certificate will be used',
                    'day' => 'Day of issuance',
                    'month' => 'Month of issuance',
                    'year' => 'Year of issuance',
                    'barangay_name' => 'Name of the barangay',
                    'municipality_name' => 'Name of the municipality',
                    'province_name' => 'Name of the province',
                    'official_name' => 'Name of the barangay captain',
                    'prepared_by_name' => 'Name of the person who prepared the document',
                    'captain_name' => 'Name of the Punong Barangay'
                ]
            ],
            'Certification' => [
                'header_content' => '
                    <div class="header text-center">
                        <h1>Republic of the Philippines</h1>
                        <h2>Province of [province_name]</h2>
                        <h2>Municipality of [municipality_name]</h2>
                        <h1>CERTIFICATION</h1>
                    </div>
                ',
                'body_content' => '
                    <div class="content">
                        <p>TO WHOM IT MAY CONCERN:</p>
                        <p>This is to certify that [resident_name], of legal age, [civil_status], Filipino, and a resident of [resident_address], is a bonafide resident of Barangay [barangay_name], [municipality_name], [province_name].</p>
                        <p>This certification is being issued upon the request of the above-named person for [purpose].</p>
                        <p>Issued this [day] day of [month] [year] at Barangay Lower Malinao, Padada, Davao Del Sur.</p>
                    </div>
                ',
                'footer_content' => '
                    <div class="signature-section" style="margin-top: 80px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <!-- Prepared by (Secretary) -->
                                <td style="width: 45%; vertical-align: top;">
                                    <div style="text-align: center;">
                                        <div style="border-top: 1px solid #000; width: 250px; margin: 0 auto 10px;"></div>
                                        <div style="font-weight: bold; font-size: 16px;">[prepared_by_name]</div>
                                        <div style="font-size: 14px; color: #666;">Barangay Secretary</div>
                                        <div style="font-size: 12px; color: #666; margin-top: 5px;">Prepared by</div>
                                    </div>
                                </td>
                                
                                <!-- Certified by (Punong Barangay/Captain) -->
                                <td style="width: 45%; vertical-align: top;">
                                    <div style="text-align: center;">
                                        <div style="border-top: 1px solid #000; width: 250px; margin: 0 auto 10px;"></div>
                                        <div style="font-weight: bold; font-size: 16px;">[captain_name]</div>
                                        <div style="font-size: 14px; color: #666;">Punong Barangay</div>
                                        <div style="font-size: 12px; color: #666; margin-top: 5px;">Certified by</div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                ',
                'placeholders' => [
                    'resident_name' => 'Name of the resident',
                    'resident_address' => 'Address of the resident',
                    'civil_status' => 'Civil status of the resident',
                    'purpose' => 'Purpose of the certification',
                    'day' => 'Day of issuance',
                    'month' => 'Month of issuance',
                    'year' => 'Year of issuance',
                    'barangay_name' => 'Name of the barangay',
                    'municipality_name' => 'Name of the municipality',
                    'province_name' => 'Name of the province',
                    'official_name' => 'Name of the barangay captain',
                    'prepared_by_name' => 'Name of the person who prepared the document',
                    'captain_name' => 'Name of the Punong Barangay'
                ]
            ]
        ];

        return $defaults[$documentType] ?? null;
    }

    /**
     * Get the available placeholders for this template
     */
    public function getAvailablePlaceholders()
    {
        // Return standard placeholders for Word templates
        return [
            'resident_name' => 'Resident\'s full name',
            'resident_address' => 'Resident\'s complete address',
            'current_date' => 'Current date (formatted)',
            'barangay_name' => 'Name of the barangay',
            'municipality_name' => 'Name of the municipality',
            'province_name' => 'Name of the province',
            'document_purpose' => 'Purpose of the document',
            'official_name' => 'Name of the signing official',
            'official_position' => 'Position of the signing official'
        ];
    }

    /**
     * Replace placeholders in template content with actual values
     * Handles conditional sections for optional fields
     */
    public function replacePlaceholders($content, $values)
    {
        // Handle conditional sections for Barangay Clearance
        // Birth Date section
        if (isset($values['birth_date']) && !empty($values['birth_date'])) {
            $content = str_replace('[birth_date_section]', '<div style="margin-bottom: 8px;"><strong>Birth Date:</strong> ' . $values['birth_date'] . '</div>', $content);
        } else {
            $content = str_replace('[birth_date_section]', '', $content);
        }
        
        // Birth Place section
        if (isset($values['birth_place']) && !empty($values['birth_place'])) {
            $content = str_replace('[birth_place_section]', '<div style="margin-bottom: 8px;"><strong>Birth Place:</strong> ' . $values['birth_place'] . '</div>', $content);
        } else {
            $content = str_replace('[birth_place_section]', '', $content);
        }
        
        // Status section
        if (isset($values['status']) && !empty($values['status'])) {
            $content = str_replace('[status_section]', '<div style="margin-bottom: 8px;"><strong>Status:</strong> ' . $values['status'] . '</div>', $content);
        } else {
            $content = str_replace('[status_section]', '', $content);
        }
        
        // Remarks section
        if (isset($values['remarks']) && !empty($values['remarks'])) {
            $content = str_replace('[remarks_section]', '<div style="margin-bottom: 8px;"><strong>Remarks:</strong> ' . $values['remarks'] . '</div>', $content);
        } else {
            $content = str_replace('[remarks_section]', '', $content);
        }
        
        // Purok Leader section
        if (isset($values['purok_leader_name']) && !empty($values['purok_leader_name'])) {
            $purokNumber = isset($values['purok_number']) && !empty($values['purok_number']) ? $values['purok_number'] : '';
            $purokTitle = $purokNumber ? "Purok Leader- {$purokNumber}" : 'Purok Leader';
            $purokSection = '<div style="margin-bottom: 30px;">
                <div style="border-top: 1px solid #000; width: 280px; margin: 0 auto 10px;"></div>
                <div style="font-weight: bold; font-size: 16px; margin-top: 5px;">' . $values['purok_leader_name'] . '</div>
                <div style="font-size: 14px; margin-top: 5px;">' . $purokTitle . '</div>
            </div>';
            $content = str_replace('[purok_leader_section]', $purokSection, $content);
        } else {
            $content = str_replace('[purok_leader_section]', '', $content);
        }
        
        // Replace all other placeholders
        foreach ($values as $key => $value) {
            if (!empty($value)) {
                $content = str_replace("[$key]", $value, $content);
            } else {
                // Remove placeholder if value is empty
                $content = str_replace("[$key]", '', $content);
            }
        }
        
        return $content;
    }

    /**
     * Generate the complete HTML for the template
     */
    public function generateHtml($values)
    {
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <title>' . $this->document_type . '</title>
            <style>
                body {
                    font-family: "Times New Roman", serif;
                    margin: 40px;
                    line-height: 1.6;
                    color: #333;
                }
                .header {
                    text-align: center;
                    margin-bottom: 40px;
                    border-bottom: 2px solid #000;
                    padding-bottom: 20px;
                }
                .content {
                    margin: 30px 0;
                    line-height: 2;
                }
                .signature-section {
                    margin-top: 80px;
                    text-align: right;
                }
                .signature-line {
                    border-top: 1px solid #000;
                    width: 250px;
                    margin-left: auto;
                    margin-bottom: 10px;
                }
                .official-name {
                    font-weight: bold;
                    font-size: 16px;
                }
                .official-title {
                    font-size: 14px;
                    color: #666;
                }
                /* Preserve original formatting from uploaded content */
                p, div, span, h1, h2, h3, h4, h5, h6 {
                    margin: 0;
                    padding: 0;
                }
                /* Allow inline styles to override default styles */
                *[style] {
                    /* Inline styles take precedence */
                }
                ' . ($this->custom_css ?? '') . '
            </style>
        </head>
        <body>';

        // Add header content
        $html .= $this->replacePlaceholders($this->header_content, $values);

        // Add body content
        $html .= $this->replacePlaceholders($this->body_content, $values);

        // Add footer content
        $html .= $this->replacePlaceholders($this->footer_content, $values);

        $html .= '</body></html>';

        return $html;
    }
} 