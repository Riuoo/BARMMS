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
                    <div class="header text-center">
                        <h1>Republic of the Philippines</h1>
                        <h2>Province of [province_name]</h2>
                        <h2>Municipality of [municipality_name]</h2>
                        <h1>BARANGAY CLEARANCE</h1>
                    </div>
                ',
                'body_content' => '
                    <div class="content">
                        <p>TO WHOM IT MAY CONCERN:</p>
                        <p>This is to certify that [resident_name], of legal age, [civil_status], Filipino, and a resident of [resident_address], has no pending case/s or record on file at the Office of the Barangay.</p>
                        <p>This certification is being issued upon the request of the above-named person for [purpose].</p>
                        <p>Issued this [day] day of [month] [year] at Barangay [barangay_name], [municipality_name], [province_name], Philippines.</p>
                    </div>
                ',
                'footer_content' => '
                    <div class="signature-section">
                        <div class="signature-line"></div>
                        <div class="official-name">[official_name]</div>
                        <div class="official-title">Barangay Captain</div>
                    </div>
                ',
                'placeholders' => [
                    'resident_name' => 'Name of the resident',
                    'resident_address' => 'Address of the resident',
                    'civil_status' => 'Civil status of the resident',
                    'purpose' => 'Purpose of the clearance',
                    'day' => 'Day of issuance',
                    'month' => 'Month of issuance',
                    'year' => 'Year of issuance',
                    'barangay_name' => 'Name of the barangay',
                    'municipality_name' => 'Name of the municipality',
                    'province_name' => 'Name of the province',
                    'official_name' => 'Name of the barangay captain'
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
                        <p>Issued this [day] day of [month] [year] at Barangay [barangay_name], [municipality_name], [province_name], Philippines.</p>
                    </div>
                ',
                'footer_content' => '
                    <div class="signature-section">
                        <div class="signature-line"></div>
                        <div class="official-name">[official_name]</div>
                        <div class="official-title">Barangay Captain</div>
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
                    'official_name' => 'Name of the barangay captain'
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
                        <p>This certification is being issued upon the request of the above-named person for [purpose].</p>
                        <p>Issued this [day] day of [month] [year] at Barangay [barangay_name], [municipality_name], [province_name], Philippines.</p>
                    </div>
                ',
                'footer_content' => '
                    <div class="signature-section">
                        <div class="signature-line"></div>
                        <div class="official-name">[official_name]</div>
                        <div class="official-title">Barangay Captain</div>
                    </div>
                ',
                'placeholders' => [
                    'resident_name' => 'Name of the resident',
                    'resident_address' => 'Address of the resident',
                    'civil_status' => 'Civil status of the resident',
                    'monthly_income' => 'Monthly income amount',
                    'purpose' => 'Purpose of the certificate',
                    'day' => 'Day of issuance',
                    'month' => 'Month of issuance',
                    'year' => 'Year of issuance',
                    'barangay_name' => 'Name of the barangay',
                    'municipality_name' => 'Name of the municipality',
                    'province_name' => 'Name of the province',
                    'official_name' => 'Name of the barangay captain'
                ]
            ],
            'Business Permit' => [
                'header_content' => '
                    <div class="header text-center">
                        <h1>Republic of the Philippines</h1>
                        <h2>Province of [province_name]</h2>
                        <h2>Municipality of [municipality_name]</h2>
                        <h1>BUSINESS PERMIT</h1>
                    </div>
                ',
                'body_content' => '
                    <div class="content">
                        <p>TO WHOM IT MAY CONCERN:</p>
                        <p>This is to certify that [resident_name], of legal age, [civil_status], Filipino, and a resident of [resident_address], is hereby granted permission to operate a [business_type] business in Barangay [barangay_name] located at [business_address].</p>
                        <p>This permit is being issued upon the request of the above-named person for [purpose] and is valid until [expiry_date].</p>
                        <p>Issued this [day] day of [month] [year] at Barangay [barangay_name], [municipality_name], [province_name], Philippines.</p>
                    </div>
                ',
                'footer_content' => '
                    <div class="signature-section">
                        <div class="signature-line"></div>
                        <div class="official-name">[official_name]</div>
                        <div class="official-title">Barangay Captain</div>
                    </div>
                ',
                'placeholders' => [
                    'resident_name' => 'Name of the resident',
                    'resident_address' => 'Address of the resident',
                    'civil_status' => 'Civil status of the resident',
                    'business_type' => 'Type of business',
                    'business_address' => 'Business address',
                    'purpose' => 'Purpose of the permit',
                    'expiry_date' => 'Permit expiry date',
                    'day' => 'Day of issuance',
                    'month' => 'Month of issuance',
                    'year' => 'Year of issuance',
                    'barangay_name' => 'Name of the barangay',
                    'municipality_name' => 'Name of the municipality',
                    'province_name' => 'Name of the province',
                    'official_name' => 'Name of the barangay captain'
                ]
            ],
            'Certificate of Good Moral Character' => [
                'header_content' => '
                    <div class="header text-center">
                        <h1>Republic of the Philippines</h1>
                        <h2>Province of [province_name]</h2>
                        <h2>Municipality of [municipality_name]</h2>
                        <h1>CERTIFICATE OF GOOD MORAL CHARACTER</h1>
                    </div>
                ',
                'body_content' => '
                    <div class="content">
                        <p>TO WHOM IT MAY CONCERN:</p>
                        <p>This is to certify that [resident_name], of legal age, [civil_status], Filipino, and a resident of [resident_address], is a person of good moral character and has no derogatory record in this barangay.</p>
                        <p>Based on the records and testimonies of barangay officials and residents, the above-named person has been known to be law-abiding, honest, and of good reputation in the community.</p>
                        <p>This certification is being issued upon the request of the above-named person for [purpose].</p>
                        <p>Issued this [day] day of [month] [year] at Barangay [barangay_name], [municipality_name], [province_name], Philippines.</p>
                    </div>
                ',
                'footer_content' => '
                    <div class="signature-section">
                        <div class="signature-line"></div>
                        <div class="official-name">[official_name]</div>
                        <div class="official-title">Barangay Captain</div>
                    </div>
                ',
                'placeholders' => [
                    'resident_name' => 'Name of the resident',
                    'resident_address' => 'Address of the resident',
                    'civil_status' => 'Civil status of the resident',
                    'purpose' => 'Purpose of the certificate',
                    'day' => 'Day of issuance',
                    'month' => 'Month of issuance',
                    'year' => 'Year of issuance',
                    'barangay_name' => 'Name of the barangay',
                    'municipality_name' => 'Name of the municipality',
                    'province_name' => 'Name of the province',
                    'official_name' => 'Name of the barangay captain'
                ]
            ],
            'Certificate of Live Birth' => [
                'header_content' => '
                    <div class="header text-center">
                        <h1>Republic of the Philippines</h1>
                        <h2>Province of [province_name]</h2>
                        <h2>Municipality of [municipality_name]</h2>
                        <h1>CERTIFICATE OF LIVE BIRTH</h1>
                    </div>
                ',
                'body_content' => '
                    <div class="content">
                        <p>TO WHOM IT MAY CONCERN:</p>
                        <p>This is to certify that a live birth occurred in Barangay [barangay_name], [municipality_name], [province_name] on [birth_date] at [birth_time].</p>
                        <p>Child\'s Name: [child_name]<br>
                        Sex: [child_sex]<br>
                        Father\'s Name: [father_name]<br>
                        Mother\'s Name: [mother_name]<br>
                        Place of Birth: [birth_place]</p>
                        <p>This certification is being issued upon the request of [resident_name] for [purpose].</p>
                        <p>Issued this [day] day of [month] [year] at Barangay [barangay_name], [municipality_name], [province_name], Philippines.</p>
                    </div>
                ',
                'footer_content' => '
                    <div class="signature-section">
                        <div class="signature-line"></div>
                        <div class="official-name">[official_name]</div>
                        <div class="official-title">Barangay Captain</div>
                    </div>
                ',
                'placeholders' => [
                    'resident_name' => 'Name of the requester',
                    'child_name' => 'Name of the child',
                    'child_sex' => 'Sex of the child',
                    'father_name' => 'Father\'s name',
                    'mother_name' => 'Mother\'s name',
                    'birth_date' => 'Date of birth',
                    'birth_time' => 'Time of birth',
                    'birth_place' => 'Place of birth',
                    'purpose' => 'Purpose of the certificate',
                    'day' => 'Day of issuance',
                    'month' => 'Month of issuance',
                    'year' => 'Year of issuance',
                    'barangay_name' => 'Name of the barangay',
                    'municipality_name' => 'Name of the municipality',
                    'province_name' => 'Name of the province',
                    'official_name' => 'Name of the barangay captain'
                ]
            ],
            'Certificate of Death' => [
                'header_content' => '
                    <div class="header text-center">
                        <h1>Republic of the Philippines</h1>
                        <h2>Province of [province_name]</h2>
                        <h2>Municipality of [municipality_name]</h2>
                        <h1>CERTIFICATE OF DEATH</h1>
                    </div>
                ',
                'body_content' => '
                    <div class="content">
                        <p>TO WHOM IT MAY CONCERN:</p>
                        <p>This is to certify that a death occurred in Barangay [barangay_name], [municipality_name], [province_name] on [death_date] at [death_time].</p>
                        <p>Deceased Person\'s Name: [deceased_name]<br>
                        Age: [deceased_age]<br>
                        Civil Status: [deceased_civil_status]<br>
                        Cause of Death: [cause_of_death]<br>
                        Place of Death: [death_place]</p>
                        <p>This certification is being issued upon the request of [resident_name] for [purpose].</p>
                        <p>Issued this [day] day of [month] [year] at Barangay [barangay_name], [municipality_name], [province_name], Philippines.</p>
                    </div>
                ',
                'footer_content' => '
                    <div class="signature-section">
                        <div class="signature-line"></div>
                        <div class="official-name">[official_name]</div>
                        <div class="official-title">Barangay Captain</div>
                    </div>
                ',
                'placeholders' => [
                    'resident_name' => 'Name of the requester',
                    'deceased_name' => 'Name of the deceased',
                    'deceased_age' => 'Age of the deceased',
                    'deceased_civil_status' => 'Civil status of the deceased',
                    'death_date' => 'Date of death',
                    'death_time' => 'Time of death',
                    'death_place' => 'Place of death',
                    'cause_of_death' => 'Cause of death',
                    'purpose' => 'Purpose of the certificate',
                    'day' => 'Day of issuance',
                    'month' => 'Month of issuance',
                    'year' => 'Year of issuance',
                    'barangay_name' => 'Name of the barangay',
                    'municipality_name' => 'Name of the municipality',
                    'province_name' => 'Name of the province',
                    'official_name' => 'Name of the barangay captain'
                ]
            ],
            'Certificate of Marriage' => [
                'header_content' => '
                    <div class="header text-center">
                        <h1>Republic of the Philippines</h1>
                        <h2>Province of [province_name]</h2>
                        <h2>Municipality of [municipality_name]</h2>
                        <h1>CERTIFICATE OF MARRIAGE</h1>
                    </div>
                ',
                'body_content' => '
                    <div class="content">
                        <p>TO WHOM IT MAY CONCERN:</p>
                        <p>This is to certify that a marriage ceremony was conducted in Barangay [barangay_name], [municipality_name], [province_name] on [marriage_date] at [marriage_time].</p>
                        <p>Groom\'s Name: [groom_name]<br>
                        Groom\'s Age: [groom_age]<br>
                        Groom\'s Address: [groom_address]<br>
                        Bride\'s Name: [bride_name]<br>
                        Bride\'s Age: [bride_age]<br>
                        Bride\'s Address: [bride_address]<br>
                        Place of Ceremony: [ceremony_place]</p>
                        <p>This certification is being issued upon the request of [resident_name] for [purpose].</p>
                        <p>Issued this [day] day of [month] [year] at Barangay [barangay_name], [municipality_name], [province_name], Philippines.</p>
                    </div>
                ',
                'footer_content' => '
                    <div class="signature-section">
                        <div class="signature-line"></div>
                        <div class="official-name">[official_name]</div>
                        <div class="official-title">Barangay Captain</div>
                    </div>
                ',
                'placeholders' => [
                    'resident_name' => 'Name of the requester',
                    'groom_name' => 'Groom\'s name',
                    'groom_age' => 'Groom\'s age',
                    'groom_address' => 'Groom\'s address',
                    'bride_name' => 'Bride\'s name',
                    'bride_age' => 'Bride\'s age',
                    'bride_address' => 'Bride\'s address',
                    'marriage_date' => 'Date of marriage',
                    'marriage_time' => 'Time of marriage',
                    'ceremony_place' => 'Place of ceremony',
                    'purpose' => 'Purpose of the certificate',
                    'day' => 'Day of issuance',
                    'month' => 'Month of issuance',
                    'year' => 'Year of issuance',
                    'barangay_name' => 'Name of the barangay',
                    'municipality_name' => 'Name of the municipality',
                    'province_name' => 'Name of the province',
                    'official_name' => 'Name of the barangay captain'
                ]
            ],
            'Barangay ID' => [
                'header_content' => '
                    <div class="header text-center">
                        <h1>Republic of the Philippines</h1>
                        <h2>Province of [province_name]</h2>
                        <h2>Municipality of [municipality_name]</h2>
                        <h1>BARANGAY IDENTIFICATION CARD</h1>
                    </div>
                ',
                'body_content' => '
                    <div class="content">
                        <p>TO WHOM IT MAY CONCERN:</p>
                        <p>This is to certify that [resident_name], of legal age, [civil_status], Filipino, and a resident of [resident_address], is a bonafide resident of Barangay [barangay_name], [municipality_name], [province_name].</p>
                        <p>ID Number: [id_number]<br>
                        Date of Birth: [birth_date]<br>
                        Place of Birth: [birth_place]<br>
                        Contact Number: [contact_number]</p>
                        <p>This identification card is being issued upon the request of the above-named person for [purpose] and is valid until [expiry_date].</p>
                        <p>Issued this [day] day of [month] [year] at Barangay [barangay_name], [municipality_name], [province_name], Philippines.</p>
                    </div>
                ',
                'footer_content' => '
                    <div class="signature-section">
                        <div class="signature-line"></div>
                        <div class="official-name">[official_name]</div>
                        <div class="official-title">Barangay Captain</div>
                    </div>
                ',
                'placeholders' => [
                    'resident_name' => 'Name of the resident',
                    'resident_address' => 'Address of the resident',
                    'civil_status' => 'Civil status of the resident',
                    'id_number' => 'Barangay ID number',
                    'birth_date' => 'Date of birth',
                    'birth_place' => 'Place of birth',
                    'contact_number' => 'Contact number',
                    'purpose' => 'Purpose of the ID',
                    'expiry_date' => 'ID expiry date',
                    'day' => 'Day of issuance',
                    'month' => 'Month of issuance',
                    'year' => 'Year of issuance',
                    'barangay_name' => 'Name of the barangay',
                    'municipality_name' => 'Name of the municipality',
                    'province_name' => 'Name of the province',
                    'official_name' => 'Name of the barangay captain'
                ]
            ],
            'Certificate of No Pending Case' => [
                'header_content' => '
                    <div class="header text-center">
                        <h1>Republic of the Philippines</h1>
                        <h2>Province of [province_name]</h2>
                        <h2>Municipality of [municipality_name]</h2>
                        <h1>CERTIFICATE OF NO PENDING CASE</h1>
                    </div>
                ',
                'body_content' => '
                    <div class="content">
                        <p>TO WHOM IT MAY CONCERN:</p>
                        <p>This is to certify that [resident_name], of legal age, [civil_status], Filipino, and a resident of [resident_address], has no pending case/s or legal issues on file at the Office of the Barangay.</p>
                        <p>Based on the records of the Barangay Justice System and the Office of the Barangay Captain, the above-named person has no pending complaints, cases, or legal proceedings filed against him/her.</p>
                        <p>This certification is being issued upon the request of the above-named person for [purpose].</p>
                        <p>Issued this [day] day of [month] [year] at Barangay [barangay_name], [municipality_name], [province_name], Philippines.</p>
                    </div>
                ',
                'footer_content' => '
                    <div class="signature-section">
                        <div class="signature-line"></div>
                        <div class="official-name">[official_name]</div>
                        <div class="official-title">Barangay Captain</div>
                    </div>
                ',
                'placeholders' => [
                    'resident_name' => 'Name of the resident',
                    'resident_address' => 'Address of the resident',
                    'civil_status' => 'Civil status of the resident',
                    'purpose' => 'Purpose of the certificate',
                    'day' => 'Day of issuance',
                    'month' => 'Month of issuance',
                    'year' => 'Year of issuance',
                    'barangay_name' => 'Name of the barangay',
                    'municipality_name' => 'Name of the municipality',
                    'province_name' => 'Name of the province',
                    'official_name' => 'Name of the barangay captain'
                ]
            ],
            'Certificate of No Derogatory Record' => [
                'header_content' => '
                    <div class="header text-center">
                        <h1>Republic of the Philippines</h1>
                        <h2>Province of [province_name]</h2>
                        <h2>Municipality of [municipality_name]</h2>
                        <h1>CERTIFICATE OF NO DEROGATORY RECORD</h1>
                    </div>
                ',
                'body_content' => '
                    <div class="content">
                        <p>TO WHOM IT MAY CONCERN:</p>
                        <p>This is to certify that [resident_name], of legal age, [civil_status], Filipino, and a resident of [resident_address], has no derogatory record or negative information on file at the Office of the Barangay.</p>
                        <p>Based on the records and background check conducted by the barangay officials, the above-named person has maintained a clean record and good standing in the community.</p>
                        <p>This certification is being issued upon the request of the above-named person for [purpose].</p>
                        <p>Issued this [day] day of [month] [year] at Barangay [barangay_name], [municipality_name], [province_name], Philippines.</p>
                    </div>
                ',
                'footer_content' => '
                    <div class="signature-section">
                        <div class="signature-line"></div>
                        <div class="official-name">[official_name]</div>
                        <div class="official-title">Barangay Captain</div>
                    </div>
                ',
                'placeholders' => [
                    'resident_name' => 'Name of the resident',
                    'resident_address' => 'Address of the resident',
                    'civil_status' => 'Civil status of the resident',
                    'purpose' => 'Purpose of the certificate',
                    'day' => 'Day of issuance',
                    'month' => 'Month of issuance',
                    'year' => 'Year of issuance',
                    'barangay_name' => 'Name of the barangay',
                    'municipality_name' => 'Name of the municipality',
                    'province_name' => 'Name of the province',
                    'official_name' => 'Name of the barangay captain'
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
     */
    public function replacePlaceholders($content, $values)
    {
        foreach ($values as $key => $value) {
            $content = str_replace("[$key]", $value, $content);
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
                    text-align: justify;
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