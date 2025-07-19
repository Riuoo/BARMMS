<?php

namespace App\Http\Controllers\AdminControllers\ReportRequestControllers;

use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DocumentTemplateController
{
    /**
     * Display a listing of the templates
     */
    public function index()
    {
        $templates = DocumentTemplate::all();
        return view('admin.templates.index', compact('templates'));
    }

    /**
     * Show the form for editing a template
     */
    public function edit($id)
    {
        $template = DocumentTemplate::findOrFail($id);
        return view('admin.templates.edit', compact('template'));
    }

    /**
     * Show the form for creating a new template
     */
    public function create()
    {
        return view('admin.templates.create');
    }

    /**
     * Store a newly created template
     */
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
     * Update the specified template
     */
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

    /**
     * Preview the template with sample data
     */
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

    /**
     * Reset template to default
     */
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

    /**
     * Toggle template active status
     */
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