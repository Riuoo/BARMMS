<?php

namespace App\Http\Controllers\AdminControllers\ReportRequestControllers;

use App\Models\BlotterTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BlotterTemplateController
{
    public function index(Request $request)
    {
        $query = BlotterTemplate::query();

        if ($search = $request->input('search')) {
            $query->where('template_type', 'like', '%' . $search . '%');
        }

        if ($status = strtolower($request->input('status'))) {
            if (in_array($status, ['active', 'inactive'], true)) {
                $query->where('is_active', $status === 'active');
            }
        }

        $templates = $query->orderBy('template_type')->get();
        $totalTemplates = BlotterTemplate::count();
        $activeTemplates = BlotterTemplate::where('is_active', true)->count();

        return view('admin.blotter-templates.index', compact(
            'templates',
            'totalTemplates',
            'activeTemplates'
        ));
    }

    public function edit($id)
    {
        $template = BlotterTemplate::findOrFail($id);
        return view('admin.blotter-templates.edit', compact('template'));
    }

    public function builder($id)
    {
        $template = BlotterTemplate::findOrFail($id);
        return view('admin.blotter-templates.builder', compact('template'));
    }

    public function update(Request $request, $id)
    {
        $template = BlotterTemplate::findOrFail($id);

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
            Log::error('Error updating blotter template: ' . $e->getMessage());
            notify()->error('Failed to update template.');
            return back()->withInput();
        }
    }

    public function preview($id)
    {
        $template = BlotterTemplate::findOrFail($id);
        
        // Sample data for preview
        $sampleData = [
            'case_id' => '12345',
            'complainant_name' => 'Juan Dela Cruz',
            'respondent_name' => 'Maria Santos',
            'incident_type' => 'Dispute',
            'description' => 'Sample incident description for preview purposes.',
            'status' => 'Approved',
            'summon_date' => now()->format('F d, Y g:i A'),
            'approved_at' => now()->subDays(1)->format('F d, Y g:i A'),
            'completed_at' => now()->format('F d, Y g:i A'),
            'captain_name' => 'Hon. Sample Captain',
            'prepared_by_name' => 'Sample Secretary',
            'barangay_name' => 'Lower Malinao',
            'municipality_name' => 'Padada',
            'province_name' => 'Davao Del Sur',
        ];

        $html = $template->generateHtml($sampleData);
        
        return response($html)->header('Content-Type', 'text/html');
    }

    public function reset($id)
    {
        $template = BlotterTemplate::findOrFail($id);
        
        try {
            $default = BlotterTemplate::getDefaultTemplate($template->template_type);
            if (!$default) {
                throw new \Exception('No default template found for this template type.');
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
            Log::error('Error resetting blotter template: ' . $e->getMessage());
            notify()->error('Failed to reset template.');
            return back();
        }
    }

    public function toggleStatus($id)
    {
        try {
            $template = BlotterTemplate::findOrFail($id);
            $template->is_active = !$template->is_active;
            $template->save();
            
            $status = $template->is_active ? 'activated' : 'deactivated';
            notify()->success("Template {$status} successfully.");
            return back();
        } catch (\Exception $e) {
            Log::error('Error toggling template status: ' . $e->getMessage());
            notify()->error('Failed to toggle template status.');
            return back();
        }
    }
}

