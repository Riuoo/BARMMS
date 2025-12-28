<?php

namespace App\Services;

class BlotterTemplateDefaultsService
{
    /**
     * Get the default template content for a template type
     */
    public static function getDefault($templateType)
    {
        $defaults = [
            'Summons' => self::getSummonsTemplate(),
            'Resolution' => self::getResolutionTemplate(),
        ];

        return $defaults[$templateType] ?? null;
    }

    /**
     * Get all valid placeholders with descriptions
     */
    public static function getValidPlaceholders()
    {
        return [
            'case_id' => [
                'label' => 'Case ID',
                'description' => 'Blotter case ID number',
                'type' => 'auto',
                'source' => 'blotter.id'
            ],
            'complainant_name' => [
                'label' => 'Complainant Name',
                'description' => 'Name of the complainant',
                'type' => 'auto',
                'source' => 'blotter.complainant_name'
            ],
            'respondent_name' => [
                'label' => 'Respondent Name',
                'description' => 'Full name of the respondent',
                'type' => 'auto',
                'source' => 'blotter.respondent.full_name'
            ],
            'incident_type' => [
                'label' => 'Incident Type',
                'description' => 'Type of incident (e.g., Dispute, Harassment)',
                'type' => 'auto',
                'source' => 'blotter.type'
            ],
            'description' => [
                'label' => 'Incident Description',
                'description' => 'Description of the incident',
                'type' => 'auto',
                'source' => 'blotter.description'
            ],
            'status' => [
                'label' => 'Status',
                'description' => 'Status of the blotter (pending, approved, completed)',
                'type' => 'auto',
                'source' => 'blotter.status'
            ],
            'summon_date' => [
                'label' => 'Summon Date',
                'description' => 'Date and time of the summon/hearing',
                'type' => 'auto',
                'source' => 'blotter.summon_date'
            ],
            'approved_at' => [
                'label' => 'Approved At',
                'description' => 'Date and time when the blotter was approved',
                'type' => 'auto',
                'source' => 'blotter.approved_at'
            ],
            'completed_at' => [
                'label' => 'Completed At',
                'description' => 'Date and time when the case was completed',
                'type' => 'auto',
                'source' => 'blotter.completed_at'
            ],
            'captain_name' => [
                'label' => 'Punong Barangay Name',
                'description' => 'Name of the Punong Barangay',
                'type' => 'auto',
                'source' => 'barangay.captain'
            ],
            'prepared_by_name' => [
                'label' => 'Prepared By Name',
                'description' => 'Name of the Barangay Secretary',
                'type' => 'auto',
                'source' => 'admin.name'
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
        ];
    }

    /**
     * Summons Template
     */
    private static function getSummonsTemplate()
    {
        return [
            'header_content' => '
                <div style="margin-bottom: 15px;">
                    <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                        <tr>
                            <td style="width: 15%; vertical-align: middle; text-align: center;">
                                <img src="[logo_path]" alt="Barangay Logo" style="width: 100px; height: 100px;" />
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
                                <img src="/images/padada-logo.png" alt="Padada Logo" style="width: 100px; height: 100px;" />
                            </td>
                        </tr>
                    </table>
                    <div style="text-align: center; margin-top: 15px;">
                        <div style="font-weight: bold; font-size: 12pt;">
                            OFFICE OF THE PUNONG BARANGAY
                        </div>
                        <div style="font-weight: bold; font-size: 16pt; text-decoration: underline; margin-top: 10px;">
                            SUMMON NOTICE
                        </div>
                    </div>
                </div>
            ',
            'body_content' => '
                <div style="margin-top: 20px; line-height: 1.5;">
                    <div style="margin-bottom: 15px;">
                        <div class="row"><strong>Case ID:</strong> [case_id]</div>
                        <div class="row"><strong>Status:</strong> [status]</div>
                        <div class="row"><strong>Summon Date:</strong> [summon_date]</div>
                        <div class="row"><strong>Approved At:</strong> [approved_at]</div>
                    </div>

                    <div class="box">
                        <h2>Parties</h2>
                        <div class="row"><strong>Complainant:</strong> [complainant_name]</div>
                        <div class="row"><strong>Respondent:</strong> [respondent_name]</div>
                    </div>

                    <h2>Incident Description</h2>
                    <div class="box">
                        <div class="row">[description]</div>
                    </div>
                </div>
            ',
            'footer_content' => '
                <div style="padding-top: 100px;">
                    <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                        <tr>
                            <!-- Certified - Left side -->
                            <td style="width: 50%; vertical-align: top;">
                                <div style="text-align: left;">
                                    <div style="font-size: 10pt;">Certified:</div>
                                    <div style="margin-top: 100px;">
                                        <div style="font-weight: bold; font-size: 12pt; text-decoration: underline;">[captain_name]</div>
                                        <div style="font-size: 10pt;">Punong Barangay</div>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Prepared by - Right side -->
                            <td style="width: 50%; vertical-align: top;">
                                <div style="text-align: right;">
                                    <div style="font-size: 10pt;">Prepared by:</div>
                                    <div style="margin-top: 100px;">
                                        <div style="font-weight: bold; font-size: 12pt; text-decoration: underline;">[prepared_by_name]</div>
                                        <div style="font-size: 10pt;">Barangay Secretary</div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            ',
            'placeholders' => [
                'case_id' => 'Blotter case ID number',
                'complainant_name' => 'Name of the complainant',
                'respondent_name' => 'Full name of the respondent',
                'incident_type' => 'Type of incident',
                'description' => 'Description of the incident',
                'status' => 'Status of the blotter',
                'summon_date' => 'Date and time of the summon',
                'approved_at' => 'Date and time when approved',
                'captain_name' => 'Name of the Punong Barangay',
                'prepared_by_name' => 'Name of the Barangay Secretary',
                'barangay_name' => 'Name of the barangay',
                'municipality_name' => 'Name of the municipality',
                'province_name' => 'Name of the province',
            ]
        ];
    }

    /**
     * Resolution Template
     */
    private static function getResolutionTemplate()
    {
        return [
            'header_content' => '
                <div style="margin-bottom: 15px;">
                    <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                        <tr>
                            <td style="width: 15%; vertical-align: middle; text-align: center;">
                                <img src="[logo_path]" alt="Barangay Logo" style="width: 100px; height: 100px;" />
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
                                <img src="/images/padada-logo.png" alt="Padada Logo" style="width: 100px; height: 100px;" />
                            </td>
                        </tr>
                    </table>
                    <div style="text-align: center; margin-top: 15px;">
                        <div style="font-weight: bold; font-size: 12pt;">
                            OFFICE OF THE PUNONG BARANGAY
                        </div>
                        <div style="font-weight: bold; font-size: 16pt; text-decoration: underline; margin-top: 10px;">
                            CASE RESOLUTION
                        </div>
                    </div>
                </div>
            ',
            'body_content' => '
                <div style="margin-top: 20px; line-height: 1.5;">
                    <div class="meta">
                        <div class="row"><strong>Case ID:</strong> [case_id]</div>
                        <div class="row"><strong>Status:</strong> [status]</div>
                        <div class="row"><strong>Completed At:</strong> [completed_at]</div>
                    </div>

                    <div class="box">
                        <h2>Parties</h2>
                        <div class="row"><strong>Complainant:</strong> [complainant_name]</div>
                        <div class="row"><strong>Respondent:</strong> [respondent_name]</div>
                    </div>

                    <h2>Summary</h2>
                    <div class="box">
                        <div class="row">This case has been marked as completed. Details of mediation/conciliation are filed in the barangay records.</div>
                        <div class="row muted">Original incident description:</div>
                        <div class="row">[description]</div>
                    </div>
                </div>
            ',
            'footer_content' => '
                <div style="padding-top: 100px;">
                    <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                        <tr>
                            <!-- Certified - Left side -->
                            <td style="width: 50%; vertical-align: top;">
                                <div style="text-align: left;">
                                    <div style="font-size: 10pt;">Certified:</div>
                                    <div style="margin-top: 100px;">
                                        <div style="font-weight: bold; font-size: 12pt; text-decoration: underline;">[captain_name]</div>
                                        <div style="font-size: 10pt;">Punong Barangay</div>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Prepared by - Right side -->
                            <td style="width: 50%; vertical-align: top;">
                                <div style="text-align: right;">
                                    <div style="font-size: 10pt;">Prepared by:</div>
                                    <div style="margin-top: 100px;">
                                        <div style="font-weight: bold; font-size: 12pt; text-decoration: underline;">[prepared_by_name]</div>
                                        <div style="font-size: 10pt;">Barangay Secretary</div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            ',
            'placeholders' => [
                'case_id' => 'Blotter case ID number',
                'complainant_name' => 'Name of the complainant',
                'respondent_name' => 'Full name of the respondent',
                'description' => 'Description of the incident',
                'status' => 'Status of the blotter',
                'completed_at' => 'Date and time when completed',
                'captain_name' => 'Name of the Punong Barangay',
                'prepared_by_name' => 'Name of the Barangay Secretary',
                'barangay_name' => 'Name of the barangay',
                'municipality_name' => 'Name of the municipality',
                'province_name' => 'Name of the province',
            ]
        ];
    }
}

