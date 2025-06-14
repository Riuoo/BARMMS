<?php

namespace App\Http\Controllers;

use App\Models\BarangayProfile;
use App\Models\Residence;
use App\Models\BlotterRequest;
use App\Models\DocumentRequest;
use App\Models\AccountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Mail\AccountApproved;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Fetch counts
        $totalBlotterReports = BlotterRequest::count();
        $totalDocumentRequests = DocumentRequest::count();
        $totalAccountRequests = AccountRequest::count();

        $totalAccomplishedProjects = 0; // Placeholder
        $totalHealthReports = 0; // Placeholder

        return view('admin.dashboard', compact(
            'totalBlotterReports', 
            'totalDocumentRequests', 
            'totalAccountRequests', 
            'totalAccomplishedProjects', 
            'totalHealthReports'
        ));
    }

    // Separate update method for BarangayProfile
    public function updateBarangayProfile(Request $request, $id)
    {
        try {
            $barangayProfile = BarangayProfile::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:barangay_profiles,email,' . $id,
                'role' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'password' => 'nullable|string|min:6|confirmed',
            ]);

            if (!empty($validatedData['password'])) {
                $barangayProfile->password = bcrypt($validatedData['password']);
            }

            $barangayProfile->name = $validatedData['name'];
            $barangayProfile->email = $validatedData['email'];
            $barangayProfile->role = $validatedData['role'];
            $barangayProfile->address = $validatedData['address'];
            $barangayProfile->save();

            return redirect()->route('admin.barangay-profiles')->with('success', 'Barangay profile updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error updating Barangay profile: ' . $e->getMessage()])->withInput();
        }
    }

    // Separate update method for Residence
    public function updateResidence(Request $request, $id)
    {
        try {
            $residence = Residence::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:residences,email,' . $id,
                'role' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'password' => 'nullable|string|min:6|confirmed',
            ]);

            if (!empty($validatedData['password'])) {
                $residence->password = bcrypt($validatedData['password']);
            }

            $residence->name = $validatedData['name'];
            $residence->email = $validatedData['email'];
            $residence->role = $validatedData['role'];
            $residence->address = $validatedData['address'];
            $residence->save();

            return redirect()->route('admin.residences')->with('success', 'Residence updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error updating Residence: ' . $e->getMessage()])->withInput();
        }
    }

    // Delete BarangayProfile
    public function deleteBarangayProfile($id)
    {
        try {
            $barangayProfile = BarangayProfile::findOrFail($id);
            $barangayProfile->delete();
            return redirect()->route('admin.barangay-profiles')->with('success', 'Barangay profile deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.barangay-profiles')->with('error', 'Error deleting Barangay profile: ' . $e->getMessage());
        }
    }

    // Delete Residence
    public function deleteResidence($id)
    {
        try {
            $residence = Residence::findOrFail($id);
            $residence->delete();
            return redirect()->route('admin.residences')->with('success', 'Residence deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.residences')->with('error', 'Error deleting Residence: ' . $e->getMessage());
        }
    }

    // Handle account request approval (unchanged)
    public function approveAccountRequest($id)
    {
        Log::info('approveAccountRequest method called with id: ' . $id);

        $accountRequest = AccountRequest::findOrFail($id);

        try {
            DB::beginTransaction();

            // Check if pending duplicate request exists
            $existingRequest = AccountRequest::where('email', $accountRequest->email)
                ->where('status', 'pending')
                ->where('id', '!=', $accountRequest->id)
                ->first();

            if ($existingRequest) {
                AccountRequest::where('id', $existingRequest->id)->update(['status' => 'rejected']);
                DB::rollBack();
                Log::warning('Duplicate account request found for email: ' . $accountRequest->email);
                return redirect()->route('admin.new-account-requests')
                    ->with('error', 'An account request with this email was already pending and has been rejected.');
            }

            if (!$accountRequest->token) {
                $accountRequest->token = Str::uuid();
            }

            $accountRequest->update(['status' => 'approved']);

            $registrationLink = route('register.form', ['token' => $accountRequest->token]);

            try {
                Mail::to($accountRequest->email)->send(new AccountApproved($registrationLink));
                Log::info('Email sent successfully to: ' . $accountRequest->email);
            } catch (\Exception $e) {
                Log::error('Error sending email: ' . $e->getMessage());
                return redirect()->route('admin.new-account-requests')->with('error', 'Account request approved, but email sending failed.');
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving account request: ' . $e->getMessage());
            return redirect()->route('admin.new-account-requests')->with('error', 'Error approving account request: ' . $e->getMessage());
        }

        return redirect()->route('admin.new-account-requests')->with('success', 'Account request approved successfully.');
    }

    // New method to list account requests (needed)
    public function accountRequests()
    {
        $accountRequests = AccountRequest::orderBy('created_at', 'desc')->get();
        return view('admin.new-account-requests', compact('accountRequests'));
    }

}