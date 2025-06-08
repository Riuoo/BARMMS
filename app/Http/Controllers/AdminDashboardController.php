<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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

    public function users()
    {
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit_user', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        // Add other fields as needed
        $user->save();

        return redirect()->route('admin.users')->with('success', 'User updated successfully.');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }

    public function blotterReports()
    {
        return view('admin.blotter-reports');
    }

    public function documentRequests()
    {
        return view('admin.document-requests');
    }

    public function accomplishedProjects()
    {
        return view('admin.accomplished-projects');
    }
}
