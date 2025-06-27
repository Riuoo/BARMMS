<?php

namespace App\Http\Controllers\AdminControllers;

use App\Models\DocumentRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class DocumentRequestController
{
    /**
     * Approve a document request.
     */

    public function documentRequest()
    {
        $documentRequests = DocumentRequest::with('user')->get();
        return view('admin.document-requests', compact('documentRequests'));
    }
    public function approve($id)
    {
        try {
            $documentRequest = DocumentRequest::findOrFail($id);

            if ($documentRequest->status !== 'pending') {
                return redirect()->back()->with('error', 'Document request already processed.');
            }

            $documentRequest->status = 'approved';
            $documentRequest->save();

            return redirect()->back()->with('success', 'Document request approved successfully.');
        } catch (\Exception $e) {
            Log::error('Error approving document request: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to approve document request.');
        }
    }
}
