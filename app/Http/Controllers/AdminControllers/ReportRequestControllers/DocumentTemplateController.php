<?php

namespace App\Http\Controllers\AdminControllers\ReportRequestControllers;

use App\Models\DocumentTemplate;
use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DocumentTemplateController
{
    public function index()
    {
        $templates = DocumentTemplate::all();
        return view('admin.templates.index', compact('templates'));
    }

    public function edit($id)
    {
        $template = DocumentTemplate::findOrFail($id);
        return view('admin.templates.edit', compact('template'));
    }

    public function wordIntegration($id)
    {
        $template = DocumentTemplate::findOrFail($id);
        return view('admin.templates.word-integration', compact('template'));
    }

    public function downloadWord($id)
    {
        $template = DocumentTemplate::findOrFail($id);
        
        // Create HTML content with Word-compatible formatting
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>' . $template->document_type . '</title>
    <style>
        body { font-family: "Times New Roman", serif; font-size: 12pt; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 20px; }
        .body { margin: 20px 0; }
        .footer { text-align: center; margin-top: 20px; }
        .placeholder { background-color: #ffffcc; padding: 2px 4px; border: 1px dashed #999; }
    </style>
</head>
<body>';
        
        // Add header content
        if ($template->header_content) {
            $html .= '<div class="header">' . $template->header_content . '</div>';
        }
        
        // Add body content
        if ($template->body_content) {
            $html .= '<div class="body">' . $template->body_content . '</div>';
        }
        
        // Add footer content
        if ($template->footer_content) {
            $html .= '<div class="footer">' . $template->footer_content . '</div>';
        }
        
        $html .= '</body></html>';
        
        // Generate filename
        $filename = str_replace(' ', '_', $template->document_type) . '_template.html';
        
        // Set headers for download
        header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        // Output file
        echo $html;
        exit;
    }

    public function uploadWord(Request $request, $id)
    {
        $template = DocumentTemplate::findOrFail($id);
        
        $request->validate([
            'word_file' => 'required|file|mimes:html,htm,txt,docx,doc|max:10240' // 10MB max
        ]);
        
        try {
            $file = $request->file('word_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $content = '';

            if (in_array($extension, ['html', 'htm'])) {
                $raw = file_get_contents($file->getPathname());
                // Clean Word HTML while preserving basic structure and placeholders
                $content = $this->sanitizeWordHtml($raw);
            } elseif ($extension === 'txt') {
                $content = file_get_contents($file->getPathname());
                // Convert newlines to paragraphs for better formatting
                $lines = preg_split('/\r\n|\r|\n/', $content);
                $content = '';
                foreach ($lines as $line) {
                    $trimmed = trim($line);
                    if ($trimmed === '') {
                        $content .= "<br>";
                    } else {
                        $content .= '<p>' . $trimmed . '</p>';
                    }
                }
            } elseif ($extension === 'docx') {
                // Use PHPWord for higher-fidelity HTML conversion
                try {
                    $phpWord = \PhpOffice\PhpWord\IOFactory::load($file->getPathname());
                    $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
                    ob_start();
                    $htmlWriter->save('php://output');
                    $raw = ob_get_clean();

                    // Extract body inner HTML
                    $dom = new \DOMDocument();
                    @$dom->loadHTML($raw, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                    $body = $dom->getElementsByTagName('body')->item(0);
                    $content = '';
                    if ($body) {
                        foreach (iterator_to_array($body->childNodes) as $child) {
                            $content .= $dom->saveHTML($child);
                        }
                    } else {
                        $content = $raw;
                    }
                } catch (\Throwable $e) {
                    throw new \RuntimeException('Failed to parse DOCX: ' . $e->getMessage());
                }
            } else {
                // .doc not supported without external libs; advise converting
                throw new \RuntimeException('DOC format not supported. Please upload HTML, TXT, or DOCX.');
            }
            
            // Ensure content is valid UTF-8 (handles UTF-16/UTF-32/Windows-1252 files from Word)
            $encoding = mb_detect_encoding($content, ['UTF-8', 'UTF-16LE', 'UTF-16BE', 'UTF-32LE', 'UTF-32BE', 'ISO-8859-1', 'Windows-1252'], true);
            if ($encoding !== 'UTF-8') {
                try {
                    $content = mb_convert_encoding($content, 'UTF-8', $encoding ?: 'UTF-8');
                } catch (\Throwable $e) {
                    // Fallback to iconv if mbstring conversion fails
                    $content = @iconv($encoding ?: 'UTF-8', 'UTF-8//IGNORE', $content) ?: $content;
                }
            }
            // Strip UTF-8 BOM if present
            if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
                $content = substr($content, 3);
            }

            // Extract placeholders from content
            preg_match_all('/\[([^\]]+)\]/', $content, $matches);
            $placeholders = array_values(array_unique($matches[1] ?? []));
            
            // Update template
            $template->update([
                'body_content' => $content,
                'placeholders' => $placeholders
            ]);
            
            notify()->success('Document uploaded and template updated successfully.');
            return redirect()->route('admin.templates.index');
            
        } catch (\Throwable $e) {
            Log::error('Error uploading document: ' . $e->getMessage());
            notify()->error('Failed to upload document: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Sanitize Microsoft Word HTML while preserving basic structure and placeholders.
     */
    private function sanitizeWordHtml(string $rawHtml): string
    {
        // Remove MSO conditional comments and Office-specific XML blocks fast
        $clean = preg_replace('/<!--\s*\[if.*?endif\]\s*-->/is', '', $rawHtml) ?? $rawHtml;
        $clean = preg_replace('/<\/?(meta|link|script|style|xml)[^>]*>/is', '', $clean) ?? $clean;

        $dom = new \DOMDocument();
        // Suppress warnings for malformed Word HTML
        @$dom->loadHTML($clean, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $xpath = new \DOMXPath($dom);

        // Remove namespaced elements like o:p, v:*, w:*
        foreach ($xpath->query('//*[contains(name(), ":")]') as $node) {
            $node->parentNode->removeChild($node);
        }

        // Keep inline styles; style blocks can remain minimal. Remove <script> for safety
        foreach ($dom->getElementsByTagName('script') as $script) {
            $script->parentNode->removeChild($script);
        }

        // Allowed tags and attributes
        $allowedTags = ['h1','h2','h3','p','div','span','b','strong','i','em','u','br','hr','ul','ol','li','table','thead','tbody','tr','td','th'];
        $allowedAttrs = ['style','class','align','valign','width','height','border','cellpadding','cellspacing','colspan','rowspan'];

        // Walk all elements
        $all = $dom->getElementsByTagName('*');
        // Because live NodeList changes during iteration, copy first
        $nodes = [];
        foreach ($all as $el) { $nodes[] = $el; }

        foreach ($nodes as $el) {
            $tag = strtolower($el->nodeName);
            if (!in_array($tag, $allowedTags, true)) {
                // Unwrap unknown tags: move children up, then remove
                while ($el->firstChild) {
                    $el->parentNode->insertBefore($el->firstChild, $el);
                }
                $el->parentNode->removeChild($el);
                continue;
            }

            // Drop all attributes except allowed ones and keep placeholders intact
            $attrs = [];
            if ($el instanceof \DOMElement && $el->hasAttributes()) {
                foreach (iterator_to_array($el->attributes) as $attr) {
                    if (!in_array(strtolower($attr->name), $allowedAttrs, true)) {
                        $el->removeAttribute($attr->name);
                    }
                }
            }
        }

        // Convert multiple empty paragraphs into single break
        $html = $dom->saveHTML();
        $html = preg_replace('/(\s*<p>\s*<\/p>\s*){2,}/i', '<br>', $html) ?? $html;
        // Compress excessive whitespace and remove MSO inline artifacts
        $html = preg_replace('/\s{2,}/', ' ', $html) ?? $html;
        $html = str_replace(['&nbsp;'], ' ', $html);

        return trim($html);
    }

    public function create()
    {
        return view('admin.templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_type' => 'required|string|unique:document_templates',
            'header_content' => 'required|string',
            'body_content' => 'required|string',
            'footer_content' => 'required|string',
            'custom_css' => 'nullable|string',
            'placeholders' => 'nullable|array',
            'settings' => 'nullable|array'
        ]);

        try {
            $template = DocumentTemplate::create($validated);
            notify()->success('Template created successfully.');
            return redirect()->route('admin.templates.edit', $template->id);
        } catch (\Exception $e) {
            Log::error('Error creating template: ' . $e->getMessage());
            notify()->error('Failed to create template.');
            return back()->withInput();
        }

    }

    /**
     * Create or update a template directly from an uploaded Word/HTML/TXT file.
     */
    public function storeFromWord(Request $request)
    {
        $request->validate([
            'document_type' => 'required|string',
            'word_file' => 'required|file|mimes:html,htm,txt,docx,doc|max:10240'
        ]);

        try {
            $docType = trim($request->input('document_type'));
            $file = $request->file('word_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $content = '';

            if (in_array($extension, ['html', 'htm'])) {
                $raw = file_get_contents($file->getPathname());
                $content = $this->sanitizeWordHtml($raw);
            } elseif ($extension === 'txt') {
                $text = file_get_contents($file->getPathname());
                $lines = preg_split('/\r\n|\r|\n/', $text);
                $html = '';
                foreach ($lines as $line) {
                    $trimmed = trim($line);
                    $html .= $trimmed === '' ? '<br>' : '<p>' . e($trimmed) . '</p>';
                }
                $content = $html;
            } elseif ($extension === 'docx') {
                // High-fidelity conversion using PHPWord
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($file->getPathname());
                $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
                ob_start();
                $htmlWriter->save('php://output');
                $raw = ob_get_clean();
                $content = $this->sanitizeWordHtml($raw);
            } else {
                throw new \RuntimeException('DOC format not supported. Please upload HTML, TXT, or DOCX.');
            }

            // Normalize encoding and strip BOM
            $encoding = mb_detect_encoding($content, ['UTF-8','UTF-16LE','UTF-16BE','UTF-32LE','UTF-32BE','ISO-8859-1','Windows-1252'], true);
            if ($encoding && $encoding !== 'UTF-8') {
                $content = mb_convert_encoding($content, 'UTF-8', $encoding);
            }
            if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
                $content = substr($content, 3);
            }

            // Extract placeholders like [resident_name]
            preg_match_all('/\[([^\]]+)\]/', $content, $matches);
            $placeholders = array_values(array_unique($matches[1] ?? []));

            // Create or update template by document_type
            $template = DocumentTemplate::firstOrNew(['document_type' => $docType]);
            $template->header_content = '';
            $template->body_content = $content;
            $template->footer_content = '';
            $template->placeholders = $placeholders;
            $template->is_active = true;
            $template->save();

            notify()->success('Template ' . ($template->wasRecentlyCreated ? 'created' : 'updated') . ' successfully from uploaded file.');
            return redirect()->route('admin.templates.index');

        } catch (\Throwable $e) {
            Log::error('Error creating template from file: ' . $e->getMessage());
            notify()->error('Failed to create template: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $template = DocumentTemplate::findOrFail($id);

        $validated = $request->validate([
            'header_content' => 'nullable|string',
            'body_content' => 'required|string',
            'footer_content' => 'nullable|string',
            'custom_css' => 'nullable|string',
            'placeholders' => 'nullable|array',
            'settings' => 'nullable|array'
        ]);

        try {
            $template->update($validated);
            notify()->success('Template updated successfully.');
            return back();
        } catch (\Exception $e) {
            Log::error('Error updating template: ' . $e->getMessage());
            notify()->error('Failed to update template.');
            return back()->withInput();
        }
    }

    public function preview($id)
    {
        $template = DocumentTemplate::findOrFail($id);
        
        // Sample data for preview
        $sampleData = [
            'resident_name' => 'Juan Dela Cruz',
            'resident_address' => '123 Sample Street, Barangay Sample',
            'civil_status' => 'Married',
            'purpose' => 'employment purposes',
            'day' => date('jS'),
            'month' => date('F'),
            'year' => date('Y'),
            'barangay_name' => 'Sample Barangay',
            'municipality_name' => 'Sample Municipality',
            'province_name' => 'Sample Province',
            'official_name' => 'Hon. Sample Official'
        ];

        $html = $template->generateHtml($sampleData);
        
        return response($html)->header('Content-Type', 'text/html');
    }

    public function reset($id)
    {
        $template = DocumentTemplate::findOrFail($id);
        
        try {
            $default = DocumentTemplate::getDefaultTemplate($template->document_type);
            if (!$default) {
                throw new \Exception('No default template found for this document type.');
            }

            $template->update([
                'header_content' => $default['header_content'],
                'body_content' => $default['body_content'],
                'footer_content' => $default['footer_content'],
                'placeholders' => $default['placeholders']
            ]);

            notify()->success('Template reset to default successfully.');
            return back();
        } catch (\Exception $e) {
            Log::error('Error resetting template: ' . $e->getMessage());
            notify()->error('Failed to reset template.');
            return back();
        }
    }

    public function toggleStatus($id)
    {
        $template = DocumentTemplate::findOrFail($id);
        
        try {
            $template->is_active = !$template->is_active;
            $template->save();

            notify()->success('Template status updated successfully.');
            return back();
        } catch (\Exception $e) {
            Log::error('Error toggling template status: ' . $e->getMessage());
            notify()->error('Failed to update template status.');
            return back();
        }
    }

    public function destroy($id)
    {
        try {
            $template = DocumentTemplate::findOrFail($id);

            DB::transaction(function () use ($template) {
                // Detach references in document_requests to avoid FK constraint errors
                DocumentRequest::where('document_template_id', $template->id)
                    ->update(['document_template_id' => null]);

                $template->delete();
            });

            notify()->success('Template deleted successfully. Any linked requests have been detached.');
            return redirect()->route('admin.templates.index');
        } catch (\Throwable $e) {
            Log::error('Error deleting template: ' . $e->getMessage());
            notify()->error('Failed to delete template: ' . $e->getMessage());
            return back();
        }
    }
}