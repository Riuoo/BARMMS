<?php

namespace App\Models;

use App\Services\BlotterTemplateDefaultsService;
use Illuminate\Database\Eloquent\Model;

class BlotterTemplate extends Model
{
    protected $fillable = [
        'template_type',
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

    /**
     * Get the default template content for a template type
     */
    public static function getDefaultTemplate($templateType)
    {
        return BlotterTemplateDefaultsService::getDefault($templateType);
    }

    /**
     * Get valid placeholders with validation info
     */
    public function getValidPlaceholders()
    {
        return BlotterTemplateDefaultsService::getValidPlaceholders();
    }

    /**
     * Get standard CSS for blotter templates
     */
    public function getStandardCss()
    {
        return '
            body {
                font-family: DejaVu Sans, sans-serif;
                font-size: 12px;
                color: #111;
            }
            h1 {
                font-size: 18px;
                margin: 0 0 6px 0;
            }
            h2 {
                font-size: 14px;
                margin: 14px 0 6px 0;
            }
            .meta {
                margin: 8px 0 14px 0;
            }
            .row {
                margin: 6px 0;
            }
            .box {
                border: 1px solid #444;
                padding: 10px;
                border-radius: 4px;
            }
            .muted {
                color: #555;
            }
            .footer {
                margin-top: 30px;
                font-size: 11px;
                color: #555;
            }
            .sig {
                margin-top: 40px;
            }
            .sig .line {
                border-top: 1px solid #333;
                width: 240px;
                margin-top: 40px;
            }
            .small {
                font-size: 11px;
            }
            .template-container {
                width: 100%;
            }
            .template-header {
                margin-bottom: 20px;
            }
            .template-body {
                margin: 20px 0;
            }
            .template-footer {
                margin-top: 40px;
            }
        ';
    }

    /**
     * Replace placeholders in content with values
     */
    public function replacePlaceholders($content, $values)
    {
        if (empty($content)) {
            return '';
        }

        // Escape values to prevent XSS
        $escapeValue = function($value) {
            if (is_null($value)) {
                return '';
            }
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        };

        // Replace all placeholders
        foreach ($values as $key => $value) {
            $placeholder = '[' . $key . ']';
            if (strpos($content, $placeholder) !== false) {
                if (!empty($value)) {
                    $content = str_replace($placeholder, $escapeValue($value), $content);
                } else {
                    $content = str_replace($placeholder, '', $content);
                }
            }
        }

        // Convert relative image paths to absolute paths for dompdf
        $content = preg_replace_callback(
            '/src=["\']\/images\/([^"\']+)["\']/i',
            function ($matches) {
                $imagePath = public_path('images/' . $matches[1]);
                if (file_exists($imagePath)) {
                    $absolutePath = str_replace('\\', '/', $imagePath);
                    return 'src="' . $absolutePath . '"';
                }
                return $matches[0];
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
        // Add logo path for PDF generation
        if (!isset($values['logo_path']) || empty($values['logo_path'])) {
            $logoFile = public_path('images/lower-malinao-brgy-logo.png');
            if (file_exists($logoFile)) {
                $values['logo_path'] = str_replace('\\', '/', $logoFile);
            } else {
                $values['logo_path'] = '';
            }
        }

        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>' . htmlspecialchars($this->template_type, ENT_QUOTES, 'UTF-8') . '</title>
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

        return $html;
    }
}

