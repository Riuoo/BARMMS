<?php

namespace App\Http\Controllers\AdminControllers\ReportRequestControllers;

use App\Models\DocumentTemplate;
use App\Models\DocumentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DocumentTemplateController
{
    public function index(Request $request)
    {
        $query = DocumentTemplate::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('document_type', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $category = strtolower($request->input('category', ''));
        if ($category) {
            $categoryKeywords = [
                'certificates' => ['certificate'],
                'clearances' => ['clearance'],
                'permits' => ['permit'],
                'identifications' => ['identification', 'id'],
                'reports' => ['report'],
            ];

            if ($category === 'other') {
                $query->where(function ($q) use ($categoryKeywords) {
                    foreach ($categoryKeywords as $keywords) {
                        foreach ($keywords as $keyword) {
                            $q->whereRaw('LOWER(document_type) NOT LIKE ?', ['%' . $keyword . '%']);
                        }
                    }
                });
            } elseif (isset($categoryKeywords[$category])) {
                $keywords = $categoryKeywords[$category];
                $query->where(function ($q) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $q->orWhereRaw('LOWER(document_type) LIKE ?', ['%' . $keyword . '%']);
                    }
                });
            } else {
                $query->whereRaw('LOWER(document_type) LIKE ?', ['%' . $category . '%']);
            }
        }

        if ($status = strtolower($request->input('status'))) {
            if (in_array($status, ['active', 'inactive'], true)) {
                $query->where('is_active', $status === 'active');
            }
        }

        if ($request->boolean('recent')) {
            $query->where('updated_at', '>=', now()->subDays(7));
        }

        $templates = $query->orderBy('document_type')->get();
        $totalTemplates = DocumentTemplate::count();
        $activeTemplates = DocumentTemplate::where('is_active', true)->count();
        $recentTemplates = DocumentTemplate::where('updated_at', '>=', now()->subDays(7))->count();

        return view('admin.templates.index', compact(
            'templates',
            'totalTemplates',
            'activeTemplates',
            'recentTemplates'
        ));
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

    /**
     * Download template as DOCX file for editing in Microsoft Word
     */
    public function downloadDocx($id)
    {
        $template = DocumentTemplate::findOrFail($id);
        
        try {
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $section = $phpWord->addSection([
                'marginTop' => 1134, // 0.8 inch
                'marginRight' => 1134,
                'marginBottom' => 1134,
                'marginLeft' => 1134,
            ]);
            
            // Add header content
            if ($template->header_content) {
                $this->addHtmlToSection($section, $template->header_content);
            }
            
            // Add body content
            if ($template->body_content) {
                $this->addHtmlToSection($section, $template->body_content);
            }
            
            // Add footer content
            if ($template->footer_content) {
                $this->addHtmlToSection($section, $template->footer_content);
            }
            
            // Generate filename
            $filename = str_replace(' ', '_', $template->document_type) . '_template.docx';
            
            // Create temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'template_') . '.docx';
            
            // Save as DOCX
            $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($tempFile);
            
            // Return file download
            return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            Log::error('Error creating DOCX: ' . $e->getMessage());
            notify()->error('Failed to create DOCX file: ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Helper method to add HTML content to a PhpWord section
     */
    private function addHtmlToSection($section, $htmlContent)
    {
        // Clean and parse HTML
        $dom = new \DOMDocument();
        @$dom->loadHTML('<div>' . $htmlContent . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        $this->processDomNode($dom->documentElement, $section);
    }

    /**
     * Recursively process DOM nodes and add them to PhpWord section
     */
    private function processDomNode($node, $section)
    {
        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_TEXT_NODE) {
                $text = trim($child->textContent);
                if (!empty($text)) {
                    $section->addText($text);
                }
            } elseif ($child->nodeType === XML_ELEMENT_NODE) {
                $tagName = strtolower($child->nodeName);
                
                switch ($tagName) {
                    case 'p':
                        $this->processDomNode($child, $section);
                        $section->addTextBreak();
                        break;
                    case 'br':
                        $section->addTextBreak();
                        break;
                    case 'h1':
                    case 'h2':
                    case 'h3':
                    case 'h4':
                    case 'h5':
                    case 'h6':
                        $text = trim($child->textContent);
                        if (!empty($text)) {
                            $section->addText($text, ['bold' => true, 'size' => 16]);
                            $section->addTextBreak();
                        }
                        break;
                    case 'b':
                    case 'strong':
                        $text = trim($child->textContent);
                        if (!empty($text)) {
                            $section->addText($text, ['bold' => true]);
                        }
                        break;
                    case 'i':
                    case 'em':
                        $text = trim($child->textContent);
                        if (!empty($text)) {
                            $section->addText($text, ['italic' => true]);
                        }
                        break;
                    case 'u':
                        $text = trim($child->textContent);
                        if (!empty($text)) {
                            $section->addText($text, ['underline' => 'single']);
                        }
                        break;
                    case 'ul':
                    case 'ol':
                        $this->processList($child, $section, $tagName === 'ol');
                        break;
                    case 'table':
                        $this->processTable($child, $section);
                        break;
                    default:
                        $this->processDomNode($child, $section);
                        break;
                }
            }
        }
    }

    /**
     * Process list elements
     */
    private function processList($listNode, $section, $isOrdered = false)
    {
        $listItems = $listNode->getElementsByTagName('li');
        foreach ($listItems as $index => $item) {
            $text = trim($item->textContent);
            if (!empty($text)) {
                $prefix = $isOrdered ? ($index + 1) . '. ' : '• ';
                $section->addText($prefix . $text);
                $section->addTextBreak();
            }
        }
    }

    /**
     * Process table elements
     */
    private function processTable($tableNode, $section)
    {
        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
        
        $rows = $tableNode->getElementsByTagName('tr');
        foreach ($rows as $row) {
            $cells = $row->getElementsByTagName('td');
            $headers = $row->getElementsByTagName('th');
            
            if ($cells->length > 0 || $headers->length > 0) {
                $table->addRow();
                
                // Process td elements
                foreach ($cells as $cell) {
                    $text = trim($cell->textContent);
                    $table->addCell()->addText($text);
                }
                
                // Process th elements
                foreach ($headers as $header) {
                    $text = trim($header->textContent);
                    $table->addCell()->addText($text, ['bold' => true]);
                }
            }
        }
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
     * Upload DOCX file and update template
     */
    public function uploadDocx(Request $request, $id)
    {
        $template = DocumentTemplate::findOrFail($id);
        
        $request->validate([
            'docx_file' => 'required|file|mimes:docx|max:10240' // 10MB max
        ]);
        
        try {
            $file = $request->file('docx_file');
            
            // Convert DOCX to HTML using PhpWord
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($file->getPathname());
            $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
            
            ob_start();
            $htmlWriter->save('php://output');
            $rawHtml = ob_get_clean();
            
            // Clean the HTML
            $content = $this->sanitizeWordHtml($rawHtml);
            
            // Extract placeholders from content
            preg_match_all('/\[([^\]]+)\]/', $content, $matches);
            $placeholders = array_values(array_unique($matches[1] ?? []));
            
            // Update template
            $template->update([
                'body_content' => $content,
                'placeholders' => $placeholders
            ]);
            
            notify()->success('DOCX file uploaded and template updated successfully.');
            return redirect()->route('admin.templates.index');
            
        } catch (\Throwable $e) {
            Log::error('Error uploading DOCX: ' . $e->getMessage());
            notify()->error('Failed to upload DOCX file: ' . $e->getMessage());
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
        
        // Remove style blocks completely - they're causing the raw CSS to show in content
        $clean = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $clean) ?? $clean;
        $clean = preg_replace('/<\/?(meta|link|script|xml)[^>]*>/is', '', $clean) ?? $clean;
        
        // Remove PHPWord watermark/label
        $clean = preg_replace('/<!--\s*Generated by PHPWord\s*-->/i', '', $clean) ?? $clean;
        $clean = preg_replace('/<span[^>]*>PHPWord<\/span>/i', '', $clean) ?? $clean;
        $clean = preg_replace('/<div[^>]*>PHPWord<\/div>/i', '', $clean) ?? $clean;
        $clean = preg_replace('/PHPWord/i', '', $clean) ?? $clean;

        $dom = new \DOMDocument();
        // Suppress warnings for malformed Word HTML
        @$dom->loadHTML($clean, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $xpath = new \DOMXPath($dom);

        // Remove namespaced elements like o:p, v:*, w:*
        foreach ($xpath->query('//*[contains(name(), ":")]') as $node) {
            $node->parentNode->removeChild($node);
        }

        // Remove any remaining script tags for safety
        foreach ($dom->getElementsByTagName('script') as $script) {
            $script->parentNode->removeChild($script);
        }

        // Allowed tags and attributes - expanded to preserve formatting
        $allowedTags = ['h1','h2','h3','h4','h5','h6','p','div','span','b','strong','i','em','u','br','hr','ul','ol','li','table','thead','tbody','tr','td','th','center'];
        $allowedAttrs = ['style','class','align','valign','width','height','border','cellpadding','cellspacing','colspan','rowspan','dir'];

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

            // Preserve important formatting attributes
            if ($el instanceof \DOMElement && $el->hasAttributes()) {
                foreach (iterator_to_array($el->attributes) as $attr) {
                    $attrName = strtolower($attr->name);
                    if (!in_array($attrName, $allowedAttrs, true)) {
                        $el->removeAttribute($attr->name);
                    }
                }
                
                // Enhance alignment preservation
                if ($el->hasAttribute('align')) {
                    $align = $el->getAttribute('align');
                    if (in_array($align, ['left', 'center', 'right', 'justify'])) {
                        // Convert align attribute to inline style for better preservation
                        $currentStyle = $el->getAttribute('style') ?: '';
                        $currentStyle .= 'text-align: ' . $align . '; ';
                        $el->setAttribute('style', $currentStyle);
                    }
                }
            }
        }

        // Convert multiple empty paragraphs into single break
        $html = $dom->saveHTML();
        $html = preg_replace('/(\s*<p>\s*<\/p>\s*){2,}/i', '<br>', $html) ?? $html;
        
        // Preserve important whitespace and formatting
        $html = preg_replace('/\s{3,}/', '  ', $html) ?? $html; // Allow double spaces but not excessive
        $html = str_replace(['&nbsp;&nbsp;&nbsp;&nbsp;'], '&nbsp;&nbsp;', $html); // Reduce excessive non-breaking spaces
        
        // Preserve paragraph spacing
        $html = preg_replace('/<p([^>]*)>\s*<\/p>/i', '<br>', $html) ?? $html;
        
        // Ensure proper line breaks after headings
        $html = preg_replace('/<\/h([1-6])>/i', '</h$1><br>', $html) ?? $html;

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

    /**
     * Create template from DOCX upload
     */
    public function storeFromDocx(Request $request)
    {
        $request->validate([
            'document_type' => 'required|string',
            'docx_file' => 'required|file|mimes:docx|max:10240'
        ]);

        try {
            $docType = trim($request->input('document_type'));
            $file = $request->file('docx_file');
            
            // Convert DOCX to HTML using PhpWord
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($file->getPathname());
            $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
            
            ob_start();
            $htmlWriter->save('php://output');
            $rawHtml = ob_get_clean();
            
            // Clean the HTML
            $content = $this->sanitizeWordHtml($rawHtml);
            
            // Extract placeholders from content
            preg_match_all('/\[([^\]]+)\]/', $content, $matches);
            $placeholders = array_values(array_unique($matches[1] ?? []));
            
            // Check if template already exists
            $existingTemplate = DocumentTemplate::where('document_type', $docType)->first();
            
            if ($existingTemplate) {
                // Update existing template - preserve header and footer, update body
                $existingTemplate->update([
                    'body_content' => $content,
                    'placeholders' => $placeholders,
                    'is_active' => true
                ]);
                
                notify()->success('Template updated successfully from DOCX file.');
            } else {
                // Create new template
                $template = DocumentTemplate::create([
                    'document_type' => $docType,
                    'header_content' => '',
                    'body_content' => $content,
                    'footer_content' => '',
                    'placeholders' => $placeholders,
                    'is_active' => true
                ]);
                
                notify()->success('Template created successfully from DOCX file.');
            }
            
            return redirect()->route('admin.templates.index');
            
        } catch (\Throwable $e) {
            Log::error('Error creating template from DOCX: ' . $e->getMessage());
            notify()->error('Failed to create template from DOCX: ' . $e->getMessage());
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