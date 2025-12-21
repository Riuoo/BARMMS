<?php

namespace App\Services;

class TemplateDefaultsService
{
    /**
     * Get the default template content for a document type
     */
    public static function getDefault($documentType)
    {
        $defaults = [
            'Barangay Clearance' => self::getBarangayClearanceTemplate(),
            'Certificate of Residency' => self::getCertificateOfResidencyTemplate(),
            'Certificate of Indigency' => self::getCertificateOfIndigencyTemplate(),
            'Certificate of Low Income' => self::getCertificateOfLowIncomeTemplate(),
            'Certification' => self::getCertificationTemplate(),
        ];

        return $defaults[$documentType] ?? null;
    }

    /**
     * Get recommended template structure for a document type
     */
    public static function getTemplateStructure($documentType)
    {
        return [
            'header' => [
                'recommended_placeholders' => ['barangay_name', 'municipality_name', 'province_name'],
                'description' => 'Header section typically contains logo, official titles, and document title'
            ],
            'body' => [
                'recommended_placeholders' => ['resident_name', 'resident_address', 'purpose'],
                'description' => 'Body section contains the main content and certification text'
            ],
            'footer' => [
                'recommended_placeholders' => ['prepared_by_name', 'captain_name', 'day', 'month', 'year'],
                'description' => 'Footer section contains signatures and official information'
            ]
        ];
    }

    /**
     * Get all valid placeholders with descriptions
     */
    public static function getValidPlaceholders()
    {
        return [
            'resident_name' => [
                'label' => 'Resident Full Name',
                'description' => 'Full name of the resident (Auto-filled from demographics). Automatically includes title of respect (Mr., Mrs., Ms.) based on gender and marital status.',
                'type' => 'auto',
                'source' => 'resident.name'
            ],
            'resident_address' => [
                'label' => 'Resident Address',
                'description' => 'Complete address of the resident (Auto-filled from demographics)',
                'type' => 'auto',
                'source' => 'resident.address'
            ],
            'birth_date' => [
                'label' => 'Birth Date',
                'description' => 'Birth date (Auto-filled from demographics)',
                'type' => 'auto',
                'source' => 'resident.birth_date'
            ],
            'birth_place' => [
                'label' => 'Birth Place',
                'description' => 'Birth place (Required - e.g., Upper Malinao, Padada, Davao del Sur)',
                'type' => 'manual',
                'required' => true
            ],
            'status' => [
                'label' => 'Civil Status',
                'description' => 'Civil status (Auto-filled from demographics - marital_status)',
                'type' => 'auto',
                'source' => 'resident.marital_status'
            ],
            'civil_status' => [
                'label' => 'Civil Status',
                'description' => 'Civil status of the resident',
                'type' => 'auto',
                'source' => 'resident.marital_status'
            ],
            'remarks' => [
                'label' => 'Remarks',
                'description' => 'Remarks (Required - e.g., NO INCRIMINATORY RECORD OR ANY PENDING CASE/COMPLAINT FILED AGAINST HIM.)',
                'type' => 'manual',
                'required' => true
            ],
            'day' => [
                'label' => 'Day',
                'description' => 'Day of issuance (e.g., 25TH)',
                'type' => 'auto',
                'source' => 'date.day'
            ],
            'month' => [
                'label' => 'Month',
                'description' => 'Month of issuance (e.g., February)',
                'type' => 'auto',
                'source' => 'date.month'
            ],
            'year' => [
                'label' => 'Year',
                'description' => 'Year of issuance (e.g., 2025)',
                'type' => 'auto',
                'source' => 'date.year'
            ],
            'barangay_name' => [
                'label' => 'Barangay Name',
                'description' => 'Name of the barangay',
                'type' => 'auto',
                'source' => 'barangay.barangay_name'
            ],
            'municipality_name' => [
                'label' => 'Municipality Name',
                'description' => 'Name of the municipality',
                'type' => 'auto',
                'source' => 'barangay.municipality_name'
            ],
            'province_name' => [
                'label' => 'Province Name',
                'description' => 'Name of the province',
                'type' => 'auto',
                'source' => 'barangay.province_name'
            ],
            'prepared_by_name' => [
                'label' => 'Prepared By Name',
                'description' => 'Name of the Barangay Secretary',
                'type' => 'auto',
                'source' => 'admin.name'
            ],
            'captain_name' => [
                'label' => 'Punong Barangay Name',
                'description' => 'Name of the Punong Barangay',
                'type' => 'auto',
                'source' => 'barangay.captain'
            ],
            'purok_leader_name' => [
                'label' => 'Purok Leader Name',
                'description' => 'Name of the Purok Leader - Optional',
                'type' => 'manual',
                'required' => false
            ],
            'purok_number' => [
                'label' => 'Purok Number',
                'description' => 'Purok number (Auto-filled from address - e.g., 5)',
                'type' => 'auto',
                'source' => 'resident.address'
            ],
            'purpose' => [
                'label' => 'Purpose',
                'description' => 'Purpose of the document',
                'type' => 'manual',
                'required' => true
            ],
            'residence_years' => [
                'label' => 'Residence Years',
                'description' => 'Number of years of residence',
                'type' => 'manual',
                'required' => false
            ],
            'monthly_income' => [
                'label' => 'Monthly Income',
                'description' => 'Monthly income amount',
                'type' => 'manual',
                'required' => false
            ],
            'purpose_location' => [
                'label' => 'Purpose Location',
                'description' => 'Location where this certificate will be used',
                'type' => 'manual',
                'required' => false
            ],
            'verbal_request' => [
                'label' => 'Verbal Request',
                'description' => 'Was this a verbal request?',
                'type' => 'manual',
                'required' => false
            ],
            'gender' => [
                'label' => 'Gender',
                'description' => 'Gender of the resident (Auto-filled from demographics)',
                'type' => 'auto',
                'source' => 'resident.gender'
            ],
            'age' => [
                'label' => 'Age',
                'description' => 'Age of the resident (Auto-filled from demographics)',
                'type' => 'auto',
                'source' => 'resident.age'
            ],
            'requester_name' => [
                'label' => 'Requester Name',
                'description' => 'Name of the person requesting on behalf (Optional)',
                'type' => 'manual',
                'required' => false
            ],
            'requester_relationship' => [
                'label' => 'Requester Relationship',
                'description' => 'Relationship of the requester to the resident (e.g., sibling, parent, child)',
                'type' => 'manual',
                'required' => false
            ],
            'dependents_list' => [
                'label' => 'Dependents List',
                'description' => 'List of dependents (Optional - e.g., 1. Name, 2. Name)',
                'type' => 'manual',
                'required' => false
            ],
            'purpose_details' => [
                'label' => 'Purpose Details',
                'description' => 'Specific purpose details (e.g., School Purposes, Medical Assistance)',
                'type' => 'manual',
                'required' => false
            ],
            'title_of_respect' => [
                'label' => 'Title of Respect',
                'description' => 'Title of respect (Mr., Mrs., Ms.) - automatically determined from gender and marital status',
                'type' => 'auto',
                'source' => 'calculated'
            ],
        ];
    }

    /**
     * Barangay Clearance Template
     */
    private static function getBarangayClearanceTemplate()
    {
        return [
            'header_content' => '
                <div style="margin-bottom: 15px;">
                    <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                        <tr>
                            <td style="width: 15%; vertical-align: middle; text-align: center;">
                                <img src="[logo_path]" alt="Barangay Logo" style="width: 65px; height: 65px;" />
                            </td>
                            <td style="width: 70%; vertical-align: middle; text-align: center;">
                                <div style="font-size: 11pt; line-height: 1.3;">
                                    <div style="font-weight: bold;">Republic of the Philippines</div>
                                    <div>Province of [province_name]</div>
                                    <div>Municipality of [municipality_name]</div>
                                    <div style="font-weight: bold;">Barangay [barangay_name]</div>
                                </div>
                            </td>
                            <td style="width: 15%; vertical-align: middle; text-align: center;">
                                <img src="/images/padada-logo.png" alt="Padada Logo" style="width: 65px; height: 65px;" />
                            </td>
                        </tr>
                    </table>
                    <div style="text-align: center; margin-top: 15px;">
                        <div style="font-weight: bold; font-size: 12pt;">
                            OFFICE OF THE PUNONG BARANGAY
                        </div>
                        <div style="font-weight: bold; font-size: 16pt; text-decoration: underline; margin-top: 10px;">
                            BARANGAY CLEARANCE
                        </div>
                    </div>
                </div>
            ',
            'body_content' => '
                <div style="margin-top: 20px; line-height: 1.5;">
                    <div style="text-align: center; font-weight: bold; margin-bottom: 15px; font-size: 12pt;">TO WHOM IT MAY CONCERN:</div>
                    
                    <div style="margin-bottom: 12px; text-align: justify;">
                        <p style="margin: 0; text-indent: 40px;">This is to certify that the person whose name below has requested a <strong>RECORD CLEARANCE</strong> from this office of the Punong Barangay and result is listed below:</p>
                    </div>
                    
                    <div style="margin-top: 15px; margin-bottom: 12px;">
                        <div style="margin-bottom: 5px;"><strong>NAME</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: [resident_name]</div>
                        <div style="margin-bottom: 5px;"><strong>ADDRESS</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: [resident_address]</div>
                        [birth_date_section]
                        [birth_place_section]
                        [status_section]
                        [remarks_section]
                    </div>
                    
                    <div style="margin-top: 15px; text-align: justify;">
                        <p style="margin: 0; text-indent: 40px;">This certification is being issued this <strong>[day] day of [month] [year]</strong>, for [purpose], as a requirement/s and for whatever legal purpose/s it may serve him best.</p>
                    </div>
                </div>
            ',
            'footer_content' => '
                <div style="margin-top: 40px;">
                    <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                        <tr>
                            <!-- Certified - Left side -->
                            <td style="width: 50%; vertical-align: top;">
                                <div style="text-align: left;">
                                    <div style="font-size: 10pt;">Certified:</div>
                                    <div style="margin-top: 30px;">
                                        <div style="font-weight: bold; font-size: 12pt;">[captain_name]</div>
                                        <div style="font-size: 10pt;">Punong Barangay</div>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Prepared by - Right side -->
                            <td style="width: 50%; vertical-align: top;">
                                <div style="text-align: right;">
                                    <div style="font-size: 10pt;">Prepared by:</div>
                                    <div style="margin-top: 30px;">
                                        <div style="font-weight: bold; font-size: 12pt;">[prepared_by_name]</div>
                                        <div style="font-size: 10pt;">Barangay Secretary</div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                    [purok_leader_section]
                </div>
            ',
            'placeholders' => [
                'resident_name' => 'Full name of the resident (Auto-filled from demographics)',
                'resident_address' => 'Complete address of the resident (Auto-filled from demographics)',
                'birth_date' => 'Birth date (Auto-filled from demographics)',
                'birth_place' => 'Birth place (Required - e.g., Upper Malinao, Padada, Davao del Sur)',
                'status' => 'Civil status (Auto-filled from demographics - marital_status)',
                'remarks' => 'Remarks (Required - e.g., NO INCRIMINATORY RECORD OR ANY PENDING CASE/COMPLAINT FILED AGAINST HIM.)',
                'purpose' => 'Purpose of the document (e.g., job application, travel, etc.)',
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
        ];
    }

    /**
     * Certificate of Residency Template
     */
    private static function getCertificateOfResidencyTemplate()
    {
        return [
            'header_content' => '
                <div style="margin-bottom: 15px;">
                    <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                        <tr>
                            <td style="width: 15%; vertical-align: middle; text-align: center;">
                                <img src="[logo_path]" alt="Barangay Logo" style="width: 65px; height: 65px;" />
                            </td>
                            <td style="width: 70%; vertical-align: middle; text-align: center;">
                                <div style="font-size: 11pt; line-height: 1.3;">
                                    <div style="font-weight: bold;">Republic of the Philippines</div>
                                    <div>Province of [province_name]</div>
                                    <div>Municipality of [municipality_name]</div>
                                    <div style="font-weight: bold;">Barangay [barangay_name]</div>
                                </div>
                            </td>
                            <td style="width: 15%; vertical-align: middle; text-align: center;">
                                <img src="/images/padada-logo.png" alt="Padada Logo" style="width: 65px; height: 65px;" />
                            </td>
                        </tr>
                    </table>
                    <div style="text-align: center; margin-top: 15px;">
                        <div style="font-weight: bold; font-size: 12pt;">
                            OFFICE OF THE PUNONG BARANGAY
                        </div>
                        <div style="font-weight: bold; font-size: 16pt; text-decoration: underline; margin-top: 10px;">
                            CERTIFICATE of RESIDENCY
                        </div>
                    </div>
                </div>
            ',
            'body_content' => '
                <div style="margin-top: 20px; line-height: 1.5;">
                    <div style="font-weight: bold; margin-bottom: 15px; font-size: 12pt;">TO WHOM IT MAY CONCERN:</div>
                    
                    <div style="margin-bottom: 12px; text-align: justify;">
                        <p style="margin: 0; text-indent: 40px;">This is to certify that <strong>[resident_name]</strong>, of legal age, [civil_status], Filipino, this person is still living and a resident of [resident_address] at Barangay [barangay_name], [municipality_name], [province_name].</p>
                    </div>
                    
                    <div style="margin-bottom: 12px; text-align: justify;">
                        <p style="margin: 0; text-indent: 40px;">This certification is being issued upon the verbal request of the above-mentioned named person as per requirements, and for whatever legal purpose this may serve her best.</p>
                    </div>
                    
                    <div style="margin-top: 15px; text-align: justify;">
                        <p style="margin: 0; text-indent: 40px;">Issued this <strong>[day] day of [month] [year]</strong>, at Barangay [barangay_name], [municipality_name], [province_name].</p>
                    </div>
                </div>
            ',
            'footer_content' => '
                <div style="margin-top: 40px;">
                    <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                        <tr>
                            <!-- Certified - Left side -->
                            <td style="width: 50%; vertical-align: top;">
                                <div style="text-align: left;">
                                    <div style="font-size: 10pt;">Certified:</div>
                                    <div style="margin-top: 30px;">
                                        <div style="font-weight: bold; font-size: 12pt; text-decoration: underline;">[captain_name]</div>
                                        <div style="font-size: 10pt;">Punong Barangay</div>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Prepared by - Right side -->
                            <td style="width: 50%; vertical-align: top;">
                                <div style="text-align: right;">
                                    <div style="font-size: 10pt;">Prepared by:</div>
                                    <div style="margin-top: 30px;">
                                        <div style="font-weight: bold; font-size: 12pt;">[prepared_by_name]</div>
                                        <div style="font-size: 10pt;">Barangay Secretary</div>
                                    </div>
                                    [purok_leader_section]
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            ',
            'placeholders' => [
                'resident_name' => 'Full name of the resident (Auto-filled from demographics)',
                'resident_address' => 'Complete address of the resident (Auto-filled from demographics)',
                'civil_status' => 'Civil status of the resident (Auto-filled from demographics)',
                'purpose' => 'Purpose of the certificate',
                'day' => 'Day of issuance (e.g., 8th)',
                'month' => 'Month of issuance (e.g., October)',
                'year' => 'Year of issuance (e.g., 2025)',
                'barangay_name' => 'Name of the barangay',
                'municipality_name' => 'Name of the municipality',
                'province_name' => 'Name of the province',
                'prepared_by_name' => 'Name of the Barangay Secretary',
                'captain_name' => 'Name of the Punong Barangay',
                'purok_leader_name' => 'Name of the Purok Leader (Optional)',
                'purok_number' => 'Purok number (Auto-filled from address)'
            ]
        ];
    }

    /**
     * Certificate of Indigency Template
     */
    private static function getCertificateOfIndigencyTemplate()
    {
        return [
            'header_content' => '
                <div style="margin-bottom: 15px;">
                    <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                        <tr>
                            <td style="width: 15%; vertical-align: middle; text-align: center;">
                                <img src="[logo_path]" alt="Barangay Logo" style="width: 65px; height: 65px;" />
                            </td>
                            <td style="width: 70%; vertical-align: middle; text-align: center;">
                                <div style="font-size: 11pt; line-height: 1.3;">
                                    <div style="font-weight: bold;">Republic of the Philippines</div>
                                    <div>Province of [province_name]</div>
                                    <div>Municipality of [municipality_name]</div>
                                    <div style="font-weight: bold;">Barangay [barangay_name]</div>
                                </div>
                            </td>
                            <td style="width: 15%; vertical-align: middle; text-align: center;">
                                <img src="/images/padada-logo.png" alt="Padada Logo" style="width: 65px; height: 65px;" />
                            </td>
                        </tr>
                    </table>
                    <div style="text-align: center; margin-top: 15px;">
                        <div style="font-weight: bold; font-size: 12pt;">
                            OFFICE OF THE PUNONG BARANGAY
                        </div>
                        <div style="font-weight: bold; font-size: 16pt; text-decoration: underline; margin-top: 10px;">
                            CERTIFICATE OF INDIGENCY
                        </div>
                    </div>
                </div>
            ',
            'body_content' => '
                <div style="margin-top: 20px; line-height: 1.5;">
                    <div style="font-weight: bold; margin-bottom: 15px; font-size: 12pt;">TO WHOM IT MAY CONCERN:</div>
                    
                    <div style="margin-bottom: 12px; text-align: justify;">
                        <p style="margin: 0; text-indent: 40px;">This is to certify that <strong>[resident_name]</strong>, Filipino, of legal age, [civil_status], [gender] and a resident of [resident_address], [municipality_name], [province_name].</p>
                    </div>
                    
                    <div style="margin-bottom: 12px; text-align: justify;">
                        <p style="margin: 0; text-indent: 40px;">This certifies further that <strong>[resident_name]</strong> has Low Income due to irregular source of income in this barangay, he is asking a <strong>[purpose]</strong> to [purpose_location].</p>
                    </div>
                    
                    <div style="margin-bottom: 12px; text-align: justify;">
                        [requester_section]
                    </div>
                    
                    <div style="margin-top: 15px; text-align: justify;">
                        <p style="margin: 0; text-indent: 40px;">Issued this <strong>[day] day of [month] [year]</strong>, at Barangay [barangay_name], [municipality_name], [province_name].</p>
                    </div>
                </div>
            ',
            'footer_content' => '
                <div style="margin-top: 40px;">
                    <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                        <tr>
                            <!-- Certified - Left side -->
                            <td style="width: 50%; vertical-align: top;">
                                <div style="text-align: left;">
                                    <div style="font-size: 10pt;">Certified:</div>
                                    <div style="margin-top: 30px;">
                                        <div style="font-weight: bold; font-size: 12pt; text-decoration: underline;">[captain_name]</div>
                                        <div style="font-size: 10pt;">Punong Barangay</div>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Prepared by - Right side -->
                            <td style="width: 50%; vertical-align: top;">
                                <div style="text-align: right;">
                                    <div style="font-size: 10pt;">Prepared by:</div>
                                    <div style="margin-top: 30px;">
                                        <div style="font-weight: bold; font-size: 12pt;">[prepared_by_name]</div>
                                        <div style="font-size: 10pt;">Barangay Secretary</div>
                                    </div>
                                    [purok_leader_section]
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            ',
            'placeholders' => [
                'resident_name' => 'Full name of the resident (Auto-filled from demographics)',
                'resident_address' => 'Complete address of the resident (Auto-filled from demographics)',
                'civil_status' => 'Civil status of the resident (Auto-filled from demographics)',
                'gender' => 'Gender of the resident (Auto-filled from demographics)',
                'purpose' => 'Purpose of the certificate (e.g., Cash Assistance for Hospital Bill)',
                'purpose_location' => 'Location where this certificate will be used (e.g., DSWD, Digos City)',
                'requester_name' => 'Name of the person requesting on behalf (Optional)',
                'requester_relationship' => 'Relationship of the requester to the resident (e.g., sibling, parent, child)',
                'day' => 'Day of issuance (e.g., 19th)',
                'month' => 'Month of issuance (e.g., December)',
                'year' => 'Year of issuance (e.g., 2025)',
                'barangay_name' => 'Name of the barangay',
                'municipality_name' => 'Name of the municipality',
                'province_name' => 'Name of the province',
                'prepared_by_name' => 'Name of the Barangay Secretary',
                'captain_name' => 'Name of the Punong Barangay',
                'purok_leader_name' => 'Name of the Purok Leader (Optional)',
                'purok_number' => 'Purok number (Auto-filled from address)'
            ]
        ];
    }

    /**
     * Certificate of Low Income Template
     */
    private static function getCertificateOfLowIncomeTemplate()
    {
        return [
            'header_content' => '
                <div style="margin-bottom: 15px;">
                    <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                        <tr>
                            <td style="width: 15%; vertical-align: middle; text-align: center;">
                                <img src="[logo_path]" alt="Barangay Logo" style="width: 65px; height: 65px;" />
                            </td>
                            <td style="width: 70%; vertical-align: middle; text-align: center;">
                                <div style="font-size: 11pt; line-height: 1.3;">
                                    <div style="font-weight: bold;">Republic of the Philippines</div>
                                    <div>Province of [province_name]</div>
                                    <div>Municipality of [municipality_name]</div>
                                    <div style="font-weight: bold;">Barangay [barangay_name]</div>
                                </div>
                            </td>
                            <td style="width: 15%; vertical-align: middle; text-align: center;">
                                <img src="/images/padada-logo.png" alt="Padada Logo" style="width: 65px; height: 65px;" />
                            </td>
                        </tr>
                    </table>
                    <div style="text-align: center; margin-top: 15px;">
                        <div style="font-weight: bold; font-size: 12pt;">
                            OFFICE OF THE PUNONG BARANGAY
                        </div>
                        <div style="font-weight: bold; font-size: 16pt; font-style: italic; text-decoration: underline; margin-top: 10px;">
                            CERTIFICATE OF LOW INCOME
                        </div>
                    </div>
                </div>
            ',
            'body_content' => '
                <div style="margin-top: 20px; line-height: 1.5;">
                    <div style="font-weight: bold; margin-bottom: 15px; font-size: 12pt;">TO WHOM IT MAY CONCERN:</div>
                    
                    <div style="margin-bottom: 12px; text-align: justify;">
                        <p style="margin: 0; text-indent: 40px;">THIS IS TO CERTIFY that <strong>[resident_name]</strong>, Filipino, of legal age, [civil_status], [gender], and a resident of [resident_address], [municipality_name], [province_name].</p>
                    </div>
                    
                    <div style="margin-bottom: 12px; text-align: justify;">
                        <p style="margin: 0; text-indent: 40px;">This is to certify further that <strong>[resident_name]</strong>, has a low income due to irregular source of income in this barangay, she asking for <strong>[purpose]</strong>.</p>
                    </div>
                    
                    <div style="margin-bottom: 12px; text-align: justify;">
                        [purpose_details_section]
                    </div>
                    
                    <div style="margin-top: 15px; text-align: justify;">
                        <p style="margin: 0; text-indent: 40px;">Issued this <strong>[day] day of [month], [year]</strong>, at Barangay [barangay_name], [municipality_name], [province_name].</p>
                    </div>
                </div>
            ',
            'footer_content' => '
                <div style="margin-top: 40px;">
                    <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                        <tr>
                            <!-- Certified - Left side -->
                            <td style="width: 50%; vertical-align: top;">
                                <div style="text-align: left;">
                                    <div style="font-size: 10pt;">Certified:</div>
                                    <div style="margin-top: 30px;">
                                        <div style="font-weight: bold; font-size: 12pt; text-decoration: underline;">[captain_name]</div>
                                        <div style="font-size: 10pt;">Punong Barangay</div>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Prepared by - Right side -->
                            <td style="width: 50%; vertical-align: top;">
                                <div style="text-align: right;">
                                    <div style="font-size: 10pt;">Prepared by:</div>
                                    <div style="margin-top: 30px;">
                                        <div style="font-weight: bold; font-size: 12pt;">[prepared_by_name]</div>
                                        <div style="font-size: 10pt;">Barangay Secretary</div>
                                    </div>
                                    [purok_leader_section]
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            ',
            'placeholders' => [
                'resident_name' => 'Full name of the resident (Auto-filled from demographics)',
                'resident_address' => 'Complete address of the resident (Auto-filled from demographics)',
                'civil_status' => 'Civil status of the resident (Auto-filled from demographics)',
                'gender' => 'Gender of the resident (Auto-filled from demographics)',
                'purpose' => 'Purpose of the certificate (e.g., School Assistance)',
                'purpose_details' => 'Specific purpose details (e.g., School Purposes, Medical Assistance)',
                'day' => 'Day of issuance (e.g., 7th)',
                'month' => 'Month of issuance (e.g., April)',
                'year' => 'Year of issuance (e.g., 2025)',
                'barangay_name' => 'Name of the barangay',
                'municipality_name' => 'Name of the municipality',
                'province_name' => 'Name of the province',
                'prepared_by_name' => 'Name of the Barangay Secretary',
                'purok_leader_name' => 'Name of the Purok Leader (Optional)',
                'purok_number' => 'Purok number (Auto-filled from address)',
                'captain_name' => 'Name of the Punong Barangay'
            ]
        ];
    }

    /**
     * Certification Template
     */
    private static function getCertificationTemplate()
    {
        return [
            'header_content' => '
                <div style="margin-bottom: 15px;">
                    <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                        <tr>
                            <td style="width: 15%; vertical-align: middle; text-align: center;">
                                <img src="[logo_path]" alt="Barangay Logo" style="width: 65px; height: 65px;" />
                            </td>
                            <td style="width: 70%; vertical-align: middle; text-align: center;">
                                <div style="font-size: 11pt; line-height: 1.3;">
                                    <div style="font-weight: bold;">Republic of the Philippines</div>
                                    <div>Province of [province_name]</div>
                                    <div>Municipality of [municipality_name]</div>
                                    <div style="font-weight: bold;">Barangay of [barangay_name]</div>
                                </div>
                            </td>
                            <td style="width: 15%; vertical-align: middle; text-align: center;">
                                <img src="/images/padada-logo.png" alt="Padada Logo" style="width: 65px; height: 65px;" />
                            </td>
                        </tr>
                    </table>
                    <div style="text-align: center; margin-top: 15px;">
                        <div style="font-weight: bold; font-size: 12pt;">
                            OFFICE OF THE PUNONG BARANGAY
                        </div>
                        <div style="font-weight: bold; font-size: 16pt; margin-top: 10px;">
                            CERTIFICATION
                        </div>
                    </div>
                </div>
            ',
            'body_content' => '
                <div style="margin-top: 20px; line-height: 1.5;">
                    <div style="font-weight: bold; margin-bottom: 15px; font-size: 12pt;">TO WHOM IT MAY CONCERN:</div>
                    
                    <div style="margin-bottom: 12px; text-align: justify;">
                        <p style="margin: 0; text-indent: 40px;">This is to certify that <strong>[resident_name]</strong>, [age] years old, [gender], [civil_status], a bonafide resident of [resident_address], [municipality_name], [province_name].</p>
                    </div>
                    
                    <div style="margin-bottom: 12px; text-align: justify;">
                        <p style="margin: 0; text-indent: 40px;">This is to certify further that the following names below are the list of her dependents and subject to moved out for household.</p>
                        <div style="margin-left: 40px; margin-top: 10px;">
                            <div>Name :</div>
                            [dependents_list]
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 12px; text-align: justify;">
                        <p style="margin: 0; text-indent: 40px;">This certification is being issued upon the request of <strong>the above-mentioned named</strong>, as a requirement and for whatever legal purpose it may serve her best.</p>
                    </div>
                    
                    <div style="margin-top: 15px; text-align: justify;">
                        <p style="margin: 0; text-indent: 40px;">Issued this <strong>[day] day of [month] [year]</strong>, at the office of the Punong Barangay, Barangay [barangay_name], [municipality_name], [province_name].</p>
                    </div>
                </div>
            ',
            'footer_content' => '
                <div style="margin-top: 40px;">
                    <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                        <tr>
                            <!-- Certified by - Left side -->
                            <td style="width: 50%; vertical-align: top;">
                                <div style="text-align: left;">
                                    <div style="font-size: 10pt;">Certified by:</div>
                                    <div style="margin-top: 30px;">
                                        <div style="font-weight: bold; font-size: 12pt; text-decoration: underline;">[captain_name]</div>
                                        <div style="font-size: 10pt;">Punong Barangay</div>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Prepared by - Right side -->
                            <td style="width: 50%; vertical-align: top;">
                                <div style="text-align: right;">
                                    <div style="font-size: 10pt;">Prepared by:</div>
                                    <div style="margin-top: 30px;">
                                        <div style="font-weight: bold; font-size: 12pt;">[prepared_by_name]</div>
                                        <div style="font-size: 10pt;">Barangay Secretary</div>
                                    </div>
                                    [purok_leader_section]
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            ',
            'placeholders' => [
                'resident_name' => 'Full name of the resident (Auto-filled from demographics)',
                'resident_address' => 'Complete address of the resident (Auto-filled from demographics)',
                'civil_status' => 'Civil status of the resident (Auto-filled from demographics)',
                'age' => 'Age of the resident (Auto-filled from demographics)',
                'gender' => 'Gender of the resident (Auto-filled from demographics)',
                'dependents_list' => 'List of dependents (Optional - e.g., 1. Name, 2. Name)',
                'purpose' => 'Purpose of the certification',
                'day' => 'Day of issuance (e.g., 21st)',
                'month' => 'Month of issuance (e.g., February)',
                'year' => 'Year of issuance (e.g., 2025)',
                'barangay_name' => 'Name of the barangay',
                'municipality_name' => 'Name of the municipality',
                'province_name' => 'Name of the province',
                'prepared_by_name' => 'Name of the Barangay Secretary',
                'captain_name' => 'Name of the Punong Barangay',
                'purok_leader_name' => 'Name of the Purok Leader (Optional)',
                'purok_number' => 'Purok number (Auto-filled from address)'
            ]
        ];
    }
}

