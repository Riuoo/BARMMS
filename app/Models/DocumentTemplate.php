<?php

namespace App\Models;

use App\Services\TemplateDefaultsService;
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
        'is_active',
        'custom_css',
        'settings'
    ];

    protected $casts = [
        'placeholders' => 'array',
        'is_active' => 'boolean',
        'settings' => 'array'
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
        return TemplateDefaultsService::getDefault($documentType);
    }

    /**
     * Get valid placeholders with validation info
     */
    public function getValidPlaceholders()
    {
        return TemplateDefaultsService::getValidPlaceholders();
    }

    /**
     * Get title of respect based on gender and marital status
     * 
     * @param string|null $gender Gender of the resident (Male/Female or male/female or M/F)
     * @param string|null $maritalStatus Marital status (Single, Married, Widowed, Divorced, Separated)
     * @return string Title of respect (Mr., Mrs., or Ms.)
     */
    public static function getTitleOfRespect($gender = null, $maritalStatus = null)
    {
        // Normalize gender to lowercase for comparison
        $genderLower = strtolower(trim($gender ?? ''));
        
        // Handle various gender formats
        $isMale = in_array($genderLower, ['male', 'm', 'masculine']);
        $isFemale = in_array($genderLower, ['female', 'f', 'feminine']);
        
        // If gender is not clearly male or female, default to empty string
        if (!$isMale && !$isFemale) {
            return '';
        }
        
        // Male: always "Mr." regardless of marital status
        if ($isMale) {
            return 'Mr.';
        }
        
        // Female: determine based on marital status
        if ($isFemale) {
            $maritalStatusLower = strtolower(trim($maritalStatus ?? ''));
            
            // Single females use "Ms."
            if ($maritalStatusLower === 'single') {
                return 'Ms.';
            }
            
            // Married, Widowed, Divorced, or Separated females use "Mrs."
            if (in_array($maritalStatusLower, ['married', 'widowed', 'divorced', 'separated'])) {
                return 'Mrs.';
            }
            
            // Default to "Ms." if marital status is unknown or empty
            return 'Ms.';
        }
        
        return '';
    }

    /**
     * Extract placeholders from content with validation
     */
    public function extractPlaceholders($content)
    {
        // More robust placeholder extraction - only alphanumeric and underscores
        preg_match_all('/\[([a-z_][a-z0-9_]*)\]/i', $content, $matches);
        $placeholders = array_unique($matches[1] ?? []);
        
        // Validate placeholders against known list
        $validPlaceholders = $this->getValidPlaceholders();
        $invalid = array_diff($placeholders, array_keys($validPlaceholders));
        
        if (!empty($invalid)) {
            \Log::warning('Invalid placeholders found in template: ' . implode(', ', $invalid), [
                'template_id' => $this->id,
                'document_type' => $this->document_type
            ]);
        }
        
        return array_values($placeholders);
    }

    /**
     * Get standardized CSS for templates
     */
    public function getStandardCss()
    {
        return '
            @page {
                margin: 1.5cm 1.5cm;
                size: A4 portrait;
            }
            
            * {
                box-sizing: border-box;
            }
            
            body {
                font-family: "Times New Roman", serif;
                font-size: 11pt;
                line-height: 1.5;
                color: #000;
                margin: 0;
                padding: 0;
                background: #fff;
            }
            
            .template-container {
                max-width: 100%;
                margin: 0 auto;
                padding: 0;
            }
            
            .template-header {
                margin-bottom: 15px;
                page-break-inside: avoid;
            }
            
            .template-body {
                margin: 10px 0;
                text-align: justify;
                line-height: 1.6;
            }
            
            .template-footer {
                padding-top: 100px;
                page-break-inside: avoid;
            }
            
            .signature-section {
                margin-top: 20px;
            }
            
            .signature-table {
                width: 100%;
                border-collapse: collapse;
                margin: 0 auto;
            }
            
            .signature-cell {
                width: 50%;
                vertical-align: top;
                padding: 5px 15px;
            }
            
            .signature-line {
                border-top: 1px solid #000;
                width: 250px;
                margin: 0 auto 10px;
            }
            
            .official-name {
                font-weight: bold;
                font-size: 16px;
                margin-top: 5px;
            }
            
            .official-title {
                font-size: 14px;
                color: #666;
                margin-top: 5px;
            }
            
            /* Preserve spacing for paragraphs and headings */
            p {
                margin-top: 25px;
                margin-bottom: 10px;
            }
            
            h1, h2, h3, h4, h5, h6 {
                margin: 15px 0 10px 0;
            }
            
            /* Table styling for dompdf */
            table {
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed;
            }
            
            td, th {
                padding: 5px;
                vertical-align: top;
                vertical-align: top;
            }
            
            /* Print-specific styles */
            @media print {
                body {
                    margin: 0;
                    padding: 0;
                }
                
                .no-print {
                    display: none;
                }
                
                .template-header,
                .template-footer {
                    page-break-inside: avoid;
                }
            }
            
            /* Allow inline styles to override */
            *[style] {
                /* Inline styles take precedence */
            }
        ';
    }

    /**
     * Get the available placeholders for this template
     */
    public function getAvailablePlaceholders()
    {
        $validPlaceholders = $this->getValidPlaceholders();
        $result = [];
        
        foreach ($validPlaceholders as $key => $info) {
            $result[$key] = $info['label'] ?? $info['description'] ?? $key;
        }
        
        return $result;
    }

    /**
     * Replace placeholders in template content with actual values
     * Handles conditional sections for optional fields
     */
    public function replacePlaceholders($content, $values)
    {
        // #region agent log
        \Log::info('DEBUG replacePlaceholders entry', [
            'content_length' => strlen($content ?? ''),
            'has_province_placeholder' => strpos($content ?? '', '[province_name]') !== false,
            'has_municipality_placeholder' => strpos($content ?? '', '[municipality_name]') !== false,
            'has_barangay_placeholder' => strpos($content ?? '', '[barangay_name]') !== false,
            'values_province' => $values['province_name'] ?? 'NOT_SET',
            'values_municipality' => $values['municipality_name'] ?? 'NOT_SET',
            'values_barangay' => $values['barangay_name'] ?? 'NOT_SET',
            'values_birth_date' => $values['birth_date'] ?? 'NOT_SET',
        ]);
        // #endregion
        
        if (empty($content)) {
            // #region agent log
            \Log::info('DEBUG Content is empty, returning early');
            // #endregion
            return '';
        }

        // Escape values to prevent XSS
        $escapeValue = function($value) {
            if (is_null($value)) {
                return '';
            }
            // Allow HTML in values but escape dangerous tags
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        };

        // #region agent log
        \Log::info('DEBUG Checking birth_date before conditional', [
            'birth_date_value' => $values['birth_date'] ?? 'NOT_SET',
            'birth_date_isset' => isset($values['birth_date']),
            'birth_date_empty' => empty($values['birth_date'] ?? ''),
            'birth_date_type' => gettype($values['birth_date'] ?? null),
            'birth_date_is_not_provided' => ($values['birth_date'] ?? '') === 'NOT PROVIDED',
        ]);
        // #endregion

        // Handle conditional sections for Barangay Clearance
        // Birth Date section - show if birth_date exists and is not "NOT PROVIDED"
        $birthDateValue = $values['birth_date'] ?? '';
        $hasBirthDateSection = strpos($content, '[birth_date_section]') !== false;
        
        // #region agent log
        \Log::info('DEBUG Birth date processing', [
            'birth_date_value' => $birthDateValue,
            'has_birth_date_section_placeholder' => $hasBirthDateSection,
            'is_empty' => empty($birthDateValue),
            'is_not_provided' => ($birthDateValue === 'NOT PROVIDED'),
            'is_not_set' => ($birthDateValue === 'NOT_SET'),
        ]);
        // #endregion
        
        // Check if content uses table structure (for Barangay Clearance alignment)
        $usesTableStructure = strpos($content, '<table style="width: 100%; border-collapse: collapse;">') !== false 
            && strpos($content, '[birth_date_section]') !== false;
        
        if ($hasBirthDateSection) {
            // Template has [birth_date_section] placeholder - use conditional replacement
            if (!empty($birthDateValue) && ($birthDateValue !== 'NOT PROVIDED') && ($birthDateValue !== 'NOT_SET')) {
                // #region agent log
                \Log::info('DEBUG Birth date section WILL be added', ['birth_date_value' => $birthDateValue]);
                // #endregion
                if ($usesTableStructure) {
                    $content = str_replace(
                        '[birth_date_section]',
                        '<tr><td style="width: 30px; padding-bottom: 5px; vertical-align: top;"><strong>Birth Date</strong></td><td style="padding-bottom: 5px; vertical-align: top;">: ' . $escapeValue($birthDateValue) . '</td></tr>',
                        $content
                    );
                } else {
                    $content = str_replace(
                        '[birth_date_section]',
                        '<div style="margin-bottom: 5px;"><strong>Birth Date:</strong> ' . $escapeValue($birthDateValue) . '</div>',
                        $content
                    );
                }
            } else {
                // #region agent log
                \Log::info('DEBUG Birth date section WILL be removed', [
                    'birth_date_value' => $birthDateValue,
                    'is_empty' => empty($birthDateValue),
                    'is_not_provided' => ($birthDateValue === 'NOT PROVIDED'),
                ]);
                // #endregion
                $content = str_replace('[birth_date_section]', '', $content);
            }
        } elseif (!empty($birthDateValue) && ($birthDateValue !== 'NOT PROVIDED') && ($birthDateValue !== 'NOT_SET')) {
            // Template doesn't have [birth_date_section] placeholder but birth_date exists
            // Try to insert it before [birth_place_section] if that exists
            if (strpos($content, '[birth_place_section]') !== false) {
                // #region agent log
                \Log::info('DEBUG Inserting birth_date before birth_place_section', ['birth_date_value' => $birthDateValue]);
                // #endregion
                if ($usesTableStructure) {
                    $content = str_replace(
                        '[birth_place_section]',
                        '<tr><td style="width: 30px; padding-bottom: 5px; vertical-align: top;"><strong>Birth Date</strong></td><td style="padding-bottom: 5px; vertical-align: top;">: ' . $escapeValue($birthDateValue) . '</td></tr>' . "\n" . '[birth_place_section]',
                        $content
                    );
                } else {
                    $content = str_replace(
                        '[birth_place_section]',
                        '<div style="margin-bottom: 5px;"><strong>Birth Date:</strong> ' . $escapeValue($birthDateValue) . '</div>' . "\n" . '[birth_place_section]',
                        $content
                    );
                }
            }
        }
        
        // Birth Place section
        if (isset($values['birth_place']) && !empty($values['birth_place'])) {
            if ($usesTableStructure) {
                $content = str_replace(
                    '[birth_place_section]',
                    '<tr><td style="width: 30px; padding-bottom: 5px; vertical-align: top;"><strong>Birth Place</strong></td><td style="padding-bottom: 5px; vertical-align: top;">: ' . $escapeValue($values['birth_place']) . '</td></tr>',
                    $content
                );
            } else {
                $content = str_replace(
                    '[birth_place_section]',
                    '<div style="margin-bottom: 5px;"><strong>Birth Place:</strong> ' . $escapeValue($values['birth_place']) . '</div>',
                    $content
                );
            }
        } else {
            $content = str_replace('[birth_place_section]', '', $content);
        }
        
        // Status section
        if (isset($values['status']) && !empty($values['status'])) {
            if ($usesTableStructure) {
                $content = str_replace(
                    '[status_section]',
                    '<tr><td style="width: 30px; padding-bottom: 5px; vertical-align: top;"><strong>Status</strong></td><td style="padding-bottom: 5px; vertical-align: top;">: ' . $escapeValue($values['status']) . '</td></tr>',
                    $content
                );
            } else {
                $content = str_replace(
                    '[status_section]',
                    '<div style="margin-bottom: 5px;"><strong>Status:</strong> ' . $escapeValue($values['status']) . '</div>',
                    $content
                );
            }
        } else {
            $content = str_replace('[status_section]', '', $content);
        }
        
        // Remarks section
        if (isset($values['remarks']) && !empty($values['remarks'])) {
            if ($usesTableStructure) {
                $content = str_replace(
                    '[remarks_section]',
                    '<tr><td style="width: 30px; padding-bottom: 5px; vertical-align: top;"><strong>Remarks</strong></td><td style="padding-bottom: 5px; vertical-align: top;">: ' . $escapeValue($values['remarks']) . '</td></tr>',
                    $content
                );
            } else {
                $content = str_replace(
                    '[remarks_section]',
                    '<div style="margin-bottom: 5px;"><strong>Remarks:</strong> ' . $escapeValue($values['remarks']) . '</div>',
                    $content
                );
            }
        } else {
            $content = str_replace('[remarks_section]', '', $content);
        }
        
        // Purok Leader section (compact for single page)
        if (isset($values['purok_leader_name']) && !empty($values['purok_leader_name'])) {
            $purokNumber = isset($values['purok_number']) && !empty($values['purok_number']) ? $values['purok_number'] : '';
            $purokTitle = $purokNumber ? "Purok Leader-{$purokNumber}" : 'Purok Leader';
            $purokSection = '<div style="margin-top: 100px;">
                <div style="font-weight: bold; font-size: 12pt; text-decoration: underline;">' . $escapeValue($values['purok_leader_name']) . '</div>
                <div style="font-size: 10pt;">' . $escapeValue($purokTitle) . '</div>
            </div>';
            $content = str_replace('[purok_leader_section]', $purokSection, $content);
        } else {
            $content = str_replace('[purok_leader_section]', '', $content);
        }
        
        // Requester section - conditional text based on whether requester_name is provided
        $hasRequesterSection = strpos($content, '[requester_section]') !== false;
        if ($hasRequesterSection) {
            $requesterName = $values['requester_name'] ?? '';
            $requesterRelationship = $values['requester_relationship'] ?? 'sibling';
            
            // Determine gender-appropriate pronouns
            $gender = strtolower($values['gender'] ?? '');
            $possessive = ($gender === 'female' || $gender === 'f') ? 'her' : 'his';
            $objective = ($gender === 'female' || $gender === 'f') ? 'her' : 'him';
            
            if (!empty($requesterName)) {
                // With requester name - use relationship if provided, otherwise default to "sibling"
                $relationship = !empty($requesterRelationship) ? $requesterRelationship : 'sibling';
                $requesterSection = '<p style="margin: 25px 0 0 0; text-indent: 40px;">This certification is given upon the verbal request of the above mentioned-named for ' . $possessive . ' ' . $escapeValue($relationship) . ' ' . $escapeValue($requesterName) . ', as a requirement/s and for whatever legal purpose/s it may serve ' . $objective . ' best.</p>';
            } else {
                // Without requester name - generic text (Certificate of Indigency)
                $requesterSection = '<p style="margin: 25px 0 0 0; text-indent: 40px;">This certification is given upon the verbal request of the above-mentioned person as a requirement/s and for whatever legal purpose/s it may serve ' . $objective . ' best.</p>';
            }
            $content = str_replace('[requester_section]', $requesterSection, $content);
        }
        
        // Purpose details section - conditional text for Certificate of Low Income
        $hasPurposeDetailsSection = strpos($content, '[purpose_details_section]') !== false;
        if ($hasPurposeDetailsSection) {
            $purposeDetails = $values['purpose_details'] ?? '';
            if (!empty($purposeDetails)) {
                // With purpose details - include it in the sentence
                $purposeDetailsSection = '<p style="margin: 25px 0 0 0; text-indent: 40px;">This certification is given upon the verbal request of the above-mentioned named person for <strong>' . $escapeValue($purposeDetails) . '</strong> as a requirement/s and for whatever legal purpose/s it may serve her best.</p>';
            } else {
                // Without purpose details - generic text
                $purposeDetailsSection = '<p style="margin: 25px 0 0 0; text-indent: 40px;">This certification is given upon the verbal request of the above-mentioned named person as a requirement/s and for whatever legal purpose/s it may serve her best.</p>';
            }
            $content = str_replace('[purpose_details_section]', $purposeDetailsSection, $content);
        }
        
        // Dependents list section - optional numbered list
        $hasDependentsList = strpos($content, '[dependents_list]') !== false;
        if ($hasDependentsList) {
            $dependentsList = $values['dependents_list'] ?? '';
            if (!empty($dependentsList)) {
                // Format the dependents list - expects comma-separated or newline-separated names
                $names = preg_split('/[\n,]+/', $dependentsList);
                $formattedList = '<ol style="margin: 5px 0 5px 60px; padding: 0;">';
                foreach ($names as $name) {
                    $name = trim($name);
                    if (!empty($name)) {
                        $formattedList .= '<li>' . $escapeValue($name) . '</li>';
                    }
                }
                $formattedList .= '</ol>';
                $content = str_replace('[dependents_list]', $formattedList, $content);
            } else {
                $content = str_replace('[dependents_list]', '', $content);
            }
        }
        
        // #region agent log
        \Log::info('DEBUG Starting placeholder replacement loop', [
            'header_placeholders_found' => [
                'province_name' => strpos($content, '[province_name]') !== false,
                'municipality_name' => strpos($content, '[municipality_name]') !== false,
                'barangay_name' => strpos($content, '[barangay_name]') !== false,
            ],
            'header_values' => [
                'province_name' => $values['province_name'] ?? 'NOT_SET',
                'municipality_name' => $values['municipality_name'] ?? 'NOT_SET',
                'barangay_name' => $values['barangay_name'] ?? 'NOT_SET',
            ],
        ]);
        // #endregion
        
        // Auto-prepend title of respect to resident_name if title exists
        if (isset($values['resident_name']) && !empty($values['resident_name'])) {
            $titleOfRespect = $values['title_of_respect'] ?? '';
            if (!empty($titleOfRespect)) {
                $values['resident_name'] = $titleOfRespect . ' ' . $values['resident_name'];
            }
        }
        
        // Replace all other placeholders
        // First, ensure header fields have values (use config defaults if empty)
        // This ensures placeholders are always replaced, never removed
        $originalProvince = $values['province_name'] ?? '';
        $originalMunicipality = $values['municipality_name'] ?? '';
        $originalBarangay = $values['barangay_name'] ?? '';
        
        if (empty($values['province_name'])) {
            $values['province_name'] = config('app.default_province', 'Davao Del Sur');
        }
        if (empty($values['municipality_name'])) {
            $values['municipality_name'] = config('app.default_city', 'Padada');
        }
        if (empty($values['barangay_name'])) {
            $values['barangay_name'] = config('app.default_barangay', 'Lower Malinao');
        }
        
        // #region agent log
        \Log::info('=== HEADER VALUES FIX ===', [
            'province_original' => $originalProvince,
            'province_final' => $values['province_name'],
            'municipality_original' => $originalMunicipality,
            'municipality_final' => $values['municipality_name'],
            'barangay_original' => $originalBarangay,
            'barangay_final' => $values['barangay_name'],
        ]);
        // #endregion
        
        foreach ($values as $key => $value) {
            $placeholder = '[' . $key . ']';
            if (strpos($content, $placeholder) !== false) {
                // #region agent log
                if (in_array($key, ['province_name', 'municipality_name', 'barangay_name', 'birth_date'])) {
                    \Log::info('DEBUG Replacing placeholder', [
                        'placeholder' => $placeholder,
                        'value' => $value ?? 'NULL',
                        'value_empty' => empty($value),
                        'value_type' => gettype($value ?? null),
                        'value_length' => strlen($value ?? ''),
                    ]);
                }
                // #endregion
            if (!empty($value)) {
                    $content = str_replace($placeholder, $escapeValue($value), $content);
            } else {
                    // Remove placeholder if value is empty (for non-critical fields)
                    $content = str_replace($placeholder, '', $content);
                }
            }
        }
        
        // #region agent log
        \Log::info('DEBUG After placeholder replacement', [
            'final_content_has_province' => strpos($content, 'Province of') !== false,
            'final_content_has_empty_province' => preg_match('/Province of\s*</', $content) === 1,
            'content_snippet' => substr($content, strpos($content, 'Province of') ?: 0, 150),
        ]);
        // #endregion
        
        // Convert relative image paths to absolute paths for dompdf
        // This handles templates stored in DB with old /images/ paths
        $content = preg_replace_callback(
            '/src=["\']\/images\/([^"\']+)["\']/i',
            function ($matches) {
                $imagePath = public_path('images/' . $matches[1]);
                if (file_exists($imagePath)) {
                    // Convert backslashes to forward slashes for dompdf
                    $absolutePath = str_replace('\\', '/', $imagePath);
                    return 'src="' . $absolutePath . '"';
                }
                return $matches[0]; // Keep original if file not found
            },
            $content
        );
        
        return $content;
    }

    /**
     * Generate the complete HTML for the template
     */
    public function generateHtml($values)
    {
        // Add logo path for PDF generation (absolute path for dompdf)
        if (!isset($values['logo_path']) || empty($values['logo_path'])) {
            // Use absolute path for dompdf compatibility
            $logoFile = public_path('images/lower-malinao-brgy-logo.png');
            if (file_exists($logoFile)) {
                // Convert Windows path to proper format for dompdf
                // dompdf needs forward slashes and no drive letter issues
                $values['logo_path'] = str_replace('\\', '/', $logoFile);
                \Log::info('Logo path set to: ' . $values['logo_path']);
            } else {
                // Try base64 encoding the image if file exists via different path check
                $altPath = base_path('public/images/lower-malinao-brgy-logo.png');
                if (file_exists($altPath)) {
                    $values['logo_path'] = str_replace('\\', '/', $altPath);
                } else {
                    // Fallback - hide logo if not found
                    $values['logo_path'] = '';
                    \Log::warning('Logo file not found at: ' . $logoFile);
                }
            }
        }
        
        // #region agent log
        \Log::info('DEBUG generateHtml entry', [
            'document_type' => $this->document_type,
            'has_header_content' => !empty($this->header_content),
            'header_content_length' => strlen($this->header_content ?? ''),
            'header_has_placeholders' => strpos($this->header_content ?? '', '[province_name]') !== false,
            'header_content_preview' => substr($this->header_content ?? '', 0, 300),
            'values_province' => $values['province_name'] ?? 'NOT_SET',
            'values_municipality' => $values['municipality_name'] ?? 'NOT_SET',
            'values_barangay' => $values['barangay_name'] ?? 'NOT_SET',
            'values_birth_date' => $values['birth_date'] ?? 'NOT_SET',
            'logo_path' => $values['logo_path'] ?? 'NOT_SET',
        ]);
        // #endregion
        
        $html = '<!DOCTYPE html>
        <html>
        <head>
    <meta charset="UTF-8">
    <title>' . htmlspecialchars($this->document_type, ENT_QUOTES, 'UTF-8') . '</title>
            <style>
        ' . $this->getStandardCss() . '
                ' . ($this->custom_css ?? '') . '
            </style>
        </head>
<body>
    <div class="template-container">
        <div class="template-header">
            ' . $this->replacePlaceholders($this->header_content ?? '', $values) . '
        </div>
        
        <div class="template-body">
            ' . $this->replacePlaceholders($this->body_content ?? '', $values) . '
        </div>
        
        <div class="template-footer">
            ' . $this->replacePlaceholders($this->footer_content ?? '', $values) . '
        </div>
    </div>
</body>
</html>';

        // #region agent log
        \Log::info('DEBUG generateHtml exit', [
            'html_length' => strlen($html),
            'html_has_province_of' => strpos($html, 'Province of') !== false,
            'html_has_empty_province' => preg_match('/Province of\s*</', $html) === 1,
            'html_province_snippet' => substr($html, strpos($html, 'Province of') ?: 0, 200),
        ]);
        // #endregion

        return $html;
    }

    /**
     * Validate template structure and placeholders
     */
    public function validate()
    {
        $errors = [];
        $warnings = [];

        // Check required fields
        if (empty($this->body_content)) {
            $errors[] = 'Body content is required';
        }

        if (empty($this->document_type)) {
            $errors[] = 'Document type is required';
        }

        // Extract and validate placeholders
        $allContent = ($this->header_content ?? '') . ($this->body_content ?? '') . ($this->footer_content ?? '');
        $placeholders = $this->extractPlaceholders($allContent);
        $validPlaceholders = array_keys($this->getValidPlaceholders());

        foreach ($placeholders as $placeholder) {
            if (!in_array($placeholder, $validPlaceholders)) {
                $warnings[] = "Unknown placeholder: [{$placeholder}]";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'placeholders' => $placeholders
        ];
    }
} 