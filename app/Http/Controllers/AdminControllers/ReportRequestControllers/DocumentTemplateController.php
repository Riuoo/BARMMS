<?php

namespace App\Http\Controllers\AdminControllers\ReportRequestControllers;

use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
                // Keep placeholders, strip tags for storage as plain content
                $content = strip_tags($raw);
            } elseif ($extension === 'txt') {
                $content = file_get_contents($file->getPathname());
            } elseif ($extension === 'docx') {
                // Parse DOCX without external libraries using ZipArchive
                $zip = new \ZipArchive();
                if ($zip->open($file->getPathname()) === true) {
                    $xml = $zip->getFromName('word/document.xml');
                    $zip->close();
                    if ($xml !== false) {
                        // Extract text from <w:t> nodes
                        if (preg_match_all('/<w:t[^>]*>(.*?)<\/w:t>/si', $xml, $matches)) {
                            $text = implode('', $matches[1]);
                        } else {
                            // Fallback: strip tags from entire XML
                            $text = strip_tags($xml);
                        }
                        // Decode entities and normalize whitespace
                        $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
                        $text = preg_replace('/[\r\n\t]+/', ' ', $text);
                        $text = preg_replace('/\s{2,}/', ' ', $text);
                        // Fix placeholders potentially split by spaces
                        $text = preg_replace('/\[\s+/', '[', $text);
                        $text = preg_replace('/\s+\]/', ']', $text);
                        $content = trim($text);
                    } else {
                        throw new \RuntimeException('Unable to read document.xml from DOCX file.');
                    }
                } else {
                    throw new \RuntimeException('Failed to open DOCX file.');
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

    public function update(Request $request, $id)
    {
        $template = DocumentTemplate::findOrFail($id);

        $validated = $request->validate([
            'header_content' => 'required|string',
            'body_content' => 'required|string',
            'footer_content' => 'required|string',
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
}