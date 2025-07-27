<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

use App\Http\Middleware\CheckAdminRole;
use App\Http\Middleware\CheckResidentRole;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ContactAdminController;
use App\Http\Controllers\AdminControllers\UserManagementControllers\RegistrationController;
use App\Http\Controllers\AdminControllers\ReportRequestControllers\AccountRequestController;
use App\Http\Controllers\AdminControllers\UserManagementControllers\AdminProfileController;
use App\Http\Controllers\AdminControllers\MainControllers\AdminDashboardController;
use App\Http\Controllers\AdminControllers\UserManagementControllers\BarangayProfileController;
use App\Http\Controllers\AdminControllers\UserManagementControllers\ResidentController;
use App\Http\Controllers\AdminControllers\ReportRequestControllers\BlotterReportController;
use App\Http\Controllers\AdminControllers\ReportRequestControllers\DocumentRequestController;
use App\Http\Controllers\AdminControllers\ReportRequestControllers\CommunityComplaintController;
use App\Http\Controllers\AdminControllers\ReportRequestControllers\DocumentTemplateController;
use App\Http\Controllers\AdminControllers\ProjectControllers\AccomplishProjectController;
use App\Http\Controllers\AdminControllers\NotificationControllers\AdminNotificationController;
use App\Http\Controllers\AdminControllers\HealthManagementControllers\HealthReportController;
use App\Http\Controllers\AdminControllers\HealthManagementControllers\PatientRecordController;
use App\Http\Controllers\AdminControllers\HealthManagementControllers\VaccinationRecordController;
use App\Http\Controllers\AdminControllers\HealthManagementControllers\MedicalLogbookController;
use App\Http\Controllers\AdminControllers\HealthManagementControllers\HealthCenterActivityController;
use App\Http\Controllers\AdminControllers\AlgorithmControllers\ClusteringController;
use App\Http\Controllers\AdminControllers\AlgorithmControllers\DecisionTreeController;
use App\Http\Controllers\AdminControllers\HealthManagementControllers\HealthStatusRequestController;
use App\Http\Controllers\ResidentControllers\ResidentController2;
use App\Http\Controllers\ResidentControllers\ResidentProfileController;
use App\Http\Controllers\PublicController;

// Landing page route
Route::get('/', function () {
    return view('landing');
})->name('landing');

// Public accomplished projects page
Route::get('/accomplishments', [PublicController::class, 'accomplishments'])->name('public.accomplishments');

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');

// Route for guest users to request an account
Route::get('/admin/contact', [ContactAdminController::class, 'contactAdmin'])->name('admin.contact');
Route::post('/admin/contact', [ContactAdminController::class, 'store'])->name('admin.contact.store');

// Forgot Password Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
// Reset Password Routes
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Authentication route
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

// Registration routes (accessible via token, not directly admin)
Route::get('/register/{token}', [RegistrationController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [RegistrationController::class, 'register'])->name('register');

// --- ADMIN ROUTES GROUP (Protected by 'admin.role' middleware) ---
Route::middleware([CheckAdminRole::class])->prefix('admin')->group(function () {

    // Admin Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Barangay Profiles routes
    Route::get('/barangay-profiles', [BarangayProfileController::class, 'barangayProfile'])->name('admin.barangay-profiles');
    Route::get('/barangay-profiles/create', [BarangayProfileController::class, 'create'])->name('admin.barangay-profiles.create');
    Route::post('/barangay-profiles', [BarangayProfileController::class, 'store'])->name('admin.barangay-profiles.store');
    Route::get('/barangay-profiles/{id}/edit', [BarangayProfileController::class, 'edit'])->name('admin.barangay-profiles.edit');
    Route::put('/barangay-profiles/{id}', [BarangayProfileController::class, 'update'])->name('admin.barangay-profiles.update');
    Route::put('/barangay-profiles/{id}/activate', [BarangayProfileController::class, 'activate'])->name('admin.barangay-profiles.activate');
    Route::put('/barangay-profiles/{id}/deactivate', [BarangayProfileController::class, 'deactivate'])->name('admin.barangay-profiles.deactivate');
    Route::delete('/barangay-profiles/{id}', [BarangayProfileController::class, 'delete'])->name('admin.barangay-profiles.delete');

    // Residents routes
    Route::get('/residents', [ResidentController::class, 'residentProfile'])->name('admin.residents');
    Route::get('/residents/create', [ResidentController::class, 'create'])->name('admin.residents.create');
    Route::post('/residents', [ResidentController::class, 'store'])->name('admin.residents.store');
    Route::get('/residents/{id}/edit', [ResidentController::class, 'edit'])->name('admin.residents.edit');
    Route::put('/residents/{id}', [ResidentController::class, 'update'])->name('admin.residents.update');
    Route::put('/residents/{id}/activate', [ResidentController::class, 'activate'])->name('admin.residents.activate');
    Route::put('/residents/{id}/deactivate', [ResidentController::class, 'deactivate'])->name('admin.residents.deactivate');
    Route::delete('/residents/{id}', [ResidentController::class, 'delete'])->name('admin.residents.delete');
    Route::get('/residents/{resident}/demographics', [ResidentController::class, 'getDemographics'])->name('admin.residents.demographics');
    
    // Profile routes for viewing and updating profile
    Route::get('/profile', [AdminProfileController::class, 'profile'])->name('admin.profile');
    Route::put('/profile/update', [AdminProfileController::class, 'update'])->name('admin.profile.update');
    
    // Blotter Reports route
    Route::get('/blotter-reports', [BlotterReportController::class, 'blotterReport'])->name('admin.blotter-reports');
    Route::get('/blotter-reports/create', [BlotterReportController::class, 'create'])->name('admin.blotter-reports.create');
    Route::post('/blotter-reports', [BlotterReportController::class, 'store'])->name('admin.blotter-reports.store');
    Route::get('/blotter-reports/{id}/details', [BlotterReportController::class, 'getDetails'])->name('admin.blotter-reports.details');
    Route::post('/blotter-reports/{id}/approve', [BlotterReportController::class, 'approve'])->name('admin.blotter-reports.approve');
    Route::post('/blotter-reports/{id}/new-summons', [BlotterReportController::class, 'generateNewSummons'])->name('admin.blotter-reports.new-summons');
    Route::post('/blotter-reports/{id}/complete', [BlotterReportController::class, 'markAsComplete'])->name('admin.blotter-reports.complete');
    
    // Community Complaints route
    Route::get('/community-complaints', [CommunityComplaintController::class, 'index'])->name('admin.community-complaints');
    Route::get('/community-complaints/{id}/details', [CommunityComplaintController::class, 'getDetails'])->name('admin.community-complaints.details');
    Route::post('/community-complaints/{id}/update-status', [CommunityComplaintController::class, 'updateStatus'])->name('admin.community-complaints.update-status');
    
    // Document Requests route
    Route::get('/document-requests', [DocumentRequestController::class, 'documentRequest'])->name('admin.document-requests');
    Route::get('/document-requests/create', [DocumentRequestController::class, 'create'])->name('admin.document-requests.create');
    Route::post('/document-requests', [DocumentRequestController::class, 'store'])->name('admin.document-requests.store');
    Route::get('/document-requests/{id}/details', [DocumentRequestController::class, 'getDetails'])->name('admin.document-requests.details');
    Route::post('/document-requests/{id}/approve', [DocumentRequestController::class, 'approve'])->name('admin.document-requests.approve');
    Route::get('/document-requests/{id}/pdf', [DocumentRequestController::class, 'generatePdf'])->name('admin.document-requests.pdf');
    Route::post('/document-requests/{id}/complete', [DocumentRequestController::class, 'markAsComplete'])->name('admin.document-requests.complete');

    //TODO
    // Document Templates Management
    Route::get('/templates', [DocumentTemplateController::class, 'index'])->name('admin.templates.index');
    Route::get('/templates/create', [DocumentTemplateController::class, 'create'])->name('admin.templates.create');
    Route::post('/templates', [DocumentTemplateController::class, 'store'])->name('admin.templates.store');
    Route::get('/templates/{template}/edit', [DocumentTemplateController::class, 'edit'])->name('admin.templates.edit');
    Route::put('/templates/{template}', [DocumentTemplateController::class, 'update'])->name('admin.templates.update');
    Route::post('/templates/{template}/reset', [DocumentTemplateController::class, 'reset'])->name('admin.templates.reset');
    Route::post('/templates/{template}/toggle-status', [DocumentTemplateController::class, 'toggleStatus'])->name('admin.templates.toggle-status');
    
    // Account Requests listing and approval
    Route::get('/new-account-requests', [AccountRequestController::class, 'accountRequest'])->name('admin.requests.new-account-requests');
    Route::put('/new-account-requests/{id}/approve', [AccountRequestController::class, 'approveAccountRequest'])->name('admin.account-requests.approve');
    
    // Accomplished Projects Routes
    Route::get('/accomplished-projects', [AccomplishProjectController::class, 'accomplishProject'])->name('admin.accomplished-projects');
    Route::get('/accomplished-projects/create', [AccomplishProjectController::class, 'create'])->name('admin.accomplished-projects.create');
    Route::get('/accomplished-projects/{id}', [AccomplishProjectController::class, 'show'])->name('admin.accomplished-projects.show');
    Route::get('/accomplished-projects/{id}/edit', [AccomplishProjectController::class, 'edit'])->name('admin.accomplished-projects.edit');
    Route::post('/accomplished-projects', [AccomplishProjectController::class, 'store'])->name('admin.accomplished-projects.store');
    Route::put('/accomplished-projects/{id}', [AccomplishProjectController::class, 'update'])->name('admin.accomplished-projects.update');
    Route::delete('/accomplished-projects/{id}', [AccomplishProjectController::class, 'destroy'])->name('admin.accomplished-projects.destroy');
    Route::post('/accomplished-projects/{id}/toggle-featured', [AccomplishProjectController::class, 'toggleFeatured'])->name('admin.accomplished-projects.toggle-featured');
    
    // Health Status Requests from Residents
    Route::get('/health-status-requests', [HealthStatusRequestController::class, 'index'])->name('admin.health-status-requests');
    Route::get('/health-status-requests/search', [HealthStatusRequestController::class, 'search'])->name('admin.health-status-requests.search');
    Route::get('/health-status-requests/{id}', [HealthStatusRequestController::class, 'show'])->name('admin.health-status-requests.show');
    Route::put('/health-status-requests/{id}/status', [HealthStatusRequestController::class, 'updateStatus'])->name('admin.health-status-requests.update-status');
    Route::get('/health-status-requests/export', [HealthStatusRequestController::class, 'export'])->name('admin.health-status-requests.export');

    // Health Reports Route
    Route::get('/health-reports', [HealthReportController::class, 'healthReport'])->name('admin.health-reports');
    Route::get('/health-reports/comprehensive', [HealthReportController::class, 'generateComprehensiveReport'])->name('admin.health-reports.comprehensive');
    Route::get('/health-reports/export', [HealthReportController::class, 'exportReport'])->name('admin.health-reports.export');

    // Patient Records Routes
    Route::get('/patient-records', [PatientRecordController::class, 'index'])->name('admin.patient-records.index');
    Route::get('/patient-records/create', [PatientRecordController::class, 'create'])->name('admin.patient-records.create');
    Route::post('/patient-records', [PatientRecordController::class, 'store'])->name('admin.patient-records.store');
    Route::get('/patient-records/{id}', [PatientRecordController::class, 'show'])->name('admin.patient-records.show');
    Route::get('/patient-records/{id}/edit', [PatientRecordController::class, 'edit'])->name('admin.patient-records.edit');
    Route::put('/patient-records/{id}', [PatientRecordController::class, 'update'])->name('admin.patient-records.update');
    Route::delete('/patient-records/{id}', [PatientRecordController::class, 'destroy'])->name('admin.patient-records.destroy');
    Route::get('/patient-records/search', [PatientRecordController::class, 'search'])->name('admin.patient-records.search');

    // Vaccination Records Routes
    Route::get('/vaccination-records', [VaccinationRecordController::class, 'index'])->name('admin.vaccination-records.index');
    Route::get('/vaccination-records/create', [VaccinationRecordController::class, 'create'])->name('admin.vaccination-records.create');
    Route::post('/vaccination-records', [VaccinationRecordController::class, 'store'])->name('admin.vaccination-records.store');
    Route::get('/vaccination-records/{id}', [VaccinationRecordController::class, 'show'])->name('admin.vaccination-records.show');
    Route::get('/vaccination-records/{id}/edit', [VaccinationRecordController::class, 'edit'])->name('admin.vaccination-records.edit');
    Route::put('/vaccination-records/{id}', [VaccinationRecordController::class, 'update'])->name('admin.vaccination-records.update');
    Route::delete('/vaccination-records/{id}', [VaccinationRecordController::class, 'destroy'])->name('admin.vaccination-records.destroy');
    Route::get('/vaccination-records/search', [VaccinationRecordController::class, 'search'])->name('admin.vaccination-records.search');
    Route::get('/vaccination-records/due', [VaccinationRecordController::class, 'dueVaccinations'])->name('admin.vaccination-records.due');
    Route::get('/vaccination-records/report', [VaccinationRecordController::class, 'generateReport'])->name('admin.vaccination-records.report');

    // Medical Logbooks Routes
    Route::get('/medical-logbooks', [MedicalLogbookController::class, 'index'])->name('admin.medical-logbooks.index');
    Route::get('/medical-logbooks/create', [MedicalLogbookController::class, 'create'])->name('admin.medical-logbooks.create');
    Route::post('/medical-logbooks', [MedicalLogbookController::class, 'store'])->name('admin.medical-logbooks.store');
    Route::get('/medical-logbooks/{id}', [MedicalLogbookController::class, 'show'])->name('admin.medical-logbooks.show');
    Route::get('/medical-logbooks/{id}/edit', [MedicalLogbookController::class, 'edit'])->name('admin.medical-logbooks.edit');
    Route::put('/medical-logbooks/{id}', [MedicalLogbookController::class, 'update'])->name('admin.medical-logbooks.update');
    Route::delete('/medical-logbooks/{id}', [MedicalLogbookController::class, 'destroy'])->name('admin.medical-logbooks.destroy');
    Route::get('/medical-logbooks/report', [MedicalLogbookController::class, 'generateReport'])->name('admin.medical-logbooks.report');
    Route::get('/medical-logbooks/search', [MedicalLogbookController::class, 'search'])->name('admin.medical-logbooks.search');

    // Health Center Activities Routes
    Route::get('/health-center-activities', [HealthCenterActivityController::class, 'index'])->name('admin.health-center-activities.index');
    Route::get('/health-center-activities/create', [HealthCenterActivityController::class, 'create'])->name('admin.health-center-activities.create');
    Route::post('/health-center-activities', [HealthCenterActivityController::class, 'store'])->name('admin.health-center-activities.store');
    Route::get('/health-center-activities/{id}', [HealthCenterActivityController::class, 'show'])->name('admin.health-center-activities.show');
    Route::get('/health-center-activities/{id}/edit', [HealthCenterActivityController::class, 'edit'])->name('admin.health-center-activities.edit');
    Route::put('/health-center-activities/{id}', [HealthCenterActivityController::class, 'update'])->name('admin.health-center-activities.update');
    Route::delete('/health-center-activities/{id}', [HealthCenterActivityController::class, 'destroy'])->name('admin.health-center-activities.destroy');
    Route::get('/health-center-activities/search', [HealthCenterActivityController::class, 'search'])->name('admin.health-center-activities.search');
    Route::get('/health-center-activities/upcoming', [HealthCenterActivityController::class, 'upcoming'])->name('admin.health-center-activities.upcoming');
    Route::get('/health-center-activities/completed', [HealthCenterActivityController::class, 'completed'])->name('admin.health-center-activities.completed');
    Route::get('/health-center-activities/report', [HealthCenterActivityController::class, 'generateReport'])->name('admin.health-center-activities.report');

    // Route to mark all notifications as read
    Route::post('/notifications/mark-all-as-read', [AdminNotificationController::class, 'markAllAsRead'])->name('admin.notifications.mark-all-as-read');
    Route::get('/notifications/count', [AdminNotificationController::class, 'getNotificationCounts'])->name('admin.notifications.count');
    Route::get('/notifications', [AdminNotificationController::class, 'showNotifications'])->name('admin.notifications');
    Route::post('/notifications/mark-as-read/{type}/{id}', [AdminNotificationController::class, 'markAsRead'])->name('admin.notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read-ajax', [AdminNotificationController::class, 'markAllAsReadAjax'])->name('admin.notifications.mark-all-as-read-ajax');

    // Clustering Analysis Routes
    Route::get('/clustering', [ClusteringController::class, 'index'])->name('admin.clustering');
    Route::post('/clustering/perform', [ClusteringController::class, 'performClustering'])->name('admin.clustering.perform');
    Route::get('/clustering/optimal-k', [ClusteringController::class, 'getOptimalK'])->name('admin.clustering.optimal-k');
    Route::get('/clustering/export', [ClusteringController::class, 'export'])->name('admin.clustering.export');
    Route::get('/clustering/stats', [ClusteringController::class, 'getClusterStats'])->name('admin.clustering.stats');

    // Decision Tree Analysis Routes
    Route::get('/decision-tree', [DecisionTreeController::class, 'index'])->name('admin.decision-tree');
    Route::post('/decision-tree/perform', [DecisionTreeController::class, 'performAnalysis'])->name('admin.decision-tree.perform');
    Route::post('/decision-tree/predict', [DecisionTreeController::class, 'predictForResident'])->name('admin.decision-tree.predict');
    Route::get('/decision-tree/stats', [DecisionTreeController::class, 'getStatistics'])->name('admin.decision-tree.stats');
    Route::get('/decision-tree/export', [DecisionTreeController::class, 'exportRules'])->name('admin.decision-tree.export');
    Route::get('/decision-tree/features', [DecisionTreeController::class, 'getFeatureImportance'])->name('admin.decision-tree.features');
    Route::get('/decision-tree/visualization', [DecisionTreeController::class, 'getTreeVisualization'])->name('admin.decision-tree.visualization');

});




Route::middleware([CheckResidentRole::class])->prefix('resident')->group(function () {
    Route::get('/dashboard', [ResidentController2::class, 'dashboard'])->name('resident.dashboard');
    // Blotter Requests
    Route::get('/request_blotter_report', [ResidentController2::class, 'requestBlotter'])->name('resident.request_blotter_report');
    Route::post('/request_blotter_report', [ResidentController2::class, 'storeBlotter'])->name('resident.request_blotter_report');
    // Community Complaints
    Route::get('/request_community_complaint', [ResidentController2::class, 'requestCommunityComplaint'])->name('resident.request_community_complaint');
    Route::post('/request_community_complaint', [ResidentController2::class, 'storeCommunityComplaint'])->name('resident.request_community_complaint');
    // Document Requests
    Route::get('/request_document_request', [ResidentController2::class, 'requestDocument'])->name('resident.request_document_request');
    Route::post('/request_document_request', [ResidentController2::class, 'storeDocument'])->name('resident.request_document_request');
    // My Requests (Tracking)
    Route::get('/my-requests', [ResidentController2::class, 'myRequests'])->name('resident.my-requests');
    // Resident Profile
    Route::get('/profile', [ResidentProfileController::class, 'profile'])->name('resident.profile');
    Route::put('/profile', [ResidentProfileController::class, 'updateProfile'])->name('resident.profile.update');
    // Health Status (Recommendation)
    Route::get('/health-status', [ResidentController2::class, 'healthStatus'])->name('resident.health-status');
    Route::post('/health-status', [ResidentController2::class, 'storeHealthStatus'])->name('resident.health-status.store');
    // Announcements (Recommendation)
    Route::get('/announcements', [ResidentController2::class, 'announcements'])->name('resident.announcements');
});

// Logout route
Route::post('/logout', function () {
    Session::flush();
    return redirect()->route('landing');
})->name('logout');
