<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BlotterRequest;
use App\Models\DocumentRequest;
use App\Models\AccountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestEmail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Mail\AccountApproved;

class AdminDashboardController extends Controller
{


    

    public function index()
    {
        // Dummy data for counts
        $totalBlotterReports = 10;
        $totalAccountRequests = 15;
        $totalAccomplishedProjects = 5;

        return view('admin.dashboard', compact('totalBlotterReports', 'totalAccountRequests', 'totalAccomplishedProjects'));
    }

    public function users(Request $request)
    {
        $query = User::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function userSuggestions(Request $request)
    {
        $search = $request->input('term', '');

        $suggestions = User::where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($suggestions);
    }

    public function liveSearchUsers(Request $request)
    {
        $search = $request->input('term', '');

        $query = User::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->limit(20)->get();

        return response()->json($users);
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit_user', compact('user'));
    }

   public function updateUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            // Add other fields as needed
            $user->save();

            return redirect()->route('admin.users')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.users')->with('error', 'Error updating user: ' . $e->getMessage());
        }
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }

    public function blotterReports()
    {
        $blotterRequests = BlotterRequest::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.blotter-reports', compact('blotterRequests'));
    }

    public function documentRequests()
    {
        $documentRequests = DocumentRequest::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.document-requests', compact('documentRequests'));
    }

    public function approveBlotterRequest($id)
    {
        $request = BlotterRequest::findOrFail($id);
        $request->status = 'approved';
        $request->save();

        return redirect()->route('admin.blotter-reports')->with('success', 'Blotter request approved successfully.');
    }

    public function accomplishedProjects()
    {
        return view('admin.accomplished-projects');
    }

    public function approveDocumentRequest($id)
    {
        $request = DocumentRequest::findOrFail($id);
        $request->status = 'approved';
        $request->save();

        return redirect()->route('admin.document-requests')->with('success', 'Document request approved successfully.');
    }

    public function liveSearchBlotterReports(Request $request)
    {
        $search = $request->input('term', '');

        $query = BlotterRequest::with('user');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                })
                ->orWhere('type', 'like', "%{$search}%");
            });
        }

        $blotterRequests = $query->orderBy('created_at')->limit(20)->get();

        return response()->json($blotterRequests);
    }

    public function liveSearchDocumentRequests(Request $request)
    {
        $search = $request->input('term', '');

        $query = DocumentRequest::with('user');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                })
                ->orWhere('document_type', 'like', "%{$search}%");
            });
        }

        $documentRequests = $query->orderBy('created_at')->limit(20)->get();

        return response()->json($documentRequests);
    }

    public function updateProfile(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->email = $request->input('email');

        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        $user->save();

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully.');
    }

    public function accountRequests()
    {
        $accountRequests = AccountRequest::orderBy('created_at', 'desc')->get();
        return view('admin.new-account-requests', compact('accountRequests'));
    }


    public function approveAccountRequest($id)
    {
        Log::info('approveAccountRequest method called with id: ' . $id);

        $accountRequest = AccountRequest::findOrFail($id);

        try {
            DB::beginTransaction();

            // Check if there is any pending account request with the same email, excluding the current request
            $existingRequest = AccountRequest::where('email', $accountRequest->email)
                ->where('status', 'pending')
                ->where('id', '!=', $accountRequest->id)
                ->first();

           if ($existingRequest) {
                // Reject the existing request
                AccountRequest::where('id', $existingRequest->id)->update(['status' => 'rejected']);
                DB::rollBack();
                Log::warning('Duplicate account request found for email: ' . $accountRequest->email . '. Existing request rejected.');
                return redirect()->route('admin.new-account-requests')->with('error', 'An account request with this email was already pending and has been rejected.');
            }

            // Generate a unique token if one doesn't already exist
            if (!$accountRequest->token) {
                $accountRequest->token = Str::uuid();
            }

            // Update the account request status to "approved"
            AccountRequest::where('id', $id)->update(['status' => 'approved']);

            // Send email with registration link
            $registrationLink = route('register.form', ['token' => $accountRequest->token]);

            $data = [
                'title' => 'Account Registration',
                'content' => "Click the following link to register: <a href='{$registrationLink}'>{$registrationLink}</a>",
            ];

           try {
                Mail::to($accountRequest->email)->send(new AccountApproved());
                Log::info('Email sent successfully to: ' . $accountRequest->email);
            } catch (\Exception $e) {
                Log::error('Error sending email: ' . $e->getMessage());
                return redirect()->route('admin.new-account-requests')->with('error', 'Account request approved, but email sending failed.');
            }

            DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $errorMessage = 'Error approving account request: ' . $e->getMessage();
                Log::error($errorMessage);
                return redirect()->route('admin.new-account-requests')->with('error', $errorMessage);
            }

            return redirect()->route('admin.new-account-requests')->with('success', 'Account request approved successfully.');
}

}
