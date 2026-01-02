<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

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
use App\Http\Controllers\AdminControllers\ReportRequestControllers\CommunityConcernController;
use App\Http\Controllers\AdminControllers\ReportRequestControllers\DocumentTemplateController;
use App\Http\Controllers\AdminControllers\ReportRequestControllers\BlotterTemplateController;
use App\Http\Controllers\AdminControllers\ProjectControllers\AccomplishProjectController;
use App\Http\Controllers\AdminControllers\NotificationControllers\AdminNotificationController;
use App\Http\Controllers\AdminControllers\HealthManagementControllers\HealthReportController;
    use App\Http\Controllers\AdminControllers\HealthManagementControllers\MedicalRecordController;
use App\Http\Controllers\AdminControllers\HealthManagementControllers\HealthCenterActivityController;
use App\Http\Controllers\AdminControllers\HealthManagementControllers\PatientHealthProfileController;
use App\Http\Controllers\AdminControllers\HealthManagementControllers\MedicineController;
use App\Http\Controllers\AdminControllers\HealthManagementControllers\MedicineRequestController;
use App\Http\Controllers\AdminControllers\HealthManagementControllers\MedicineTransactionController;
use App\Http\Controllers\AdminControllers\AlgorithmControllers\ClusteringController;
use App\Http\Controllers\AdminControllers\AlgorithmControllers\ClusteringAnalysisController;
use App\Http\Controllers\AdminControllers\ProgramControllers\ProgramRecommendationController;
use App\Http\Controllers\ResidentControllers\ResidentDashboardController;
use App\Http\Controllers\ResidentControllers\ResidentBlotterController;
use App\Http\Controllers\ResidentControllers\ResidentCommunityConcernController;
use App\Http\Controllers\ResidentControllers\ResidentDocumentRequestController;
use App\Http\Controllers\ResidentControllers\ResidentRequestListController;
use App\Http\Controllers\ResidentControllers\ResidentAnnouncementController;
use App\Http\Controllers\ResidentControllers\ResidentNotificationController;
use App\Http\Controllers\ResidentControllers\ResidentProfileController;
use App\Http\Controllers\ResidentControllers\ResidentFaqController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\AdminControllers\AttendanceController;

// Landing page route
Route::get('/', function () {
    return view('login.landing');
})->name('landing');

// Public accomplished projects page
Route::get('/accomplishments', [PublicController::class, 'accomplishments'])->name('public.accomplishments');
Route::get('/accomplishments/project/{id}', [PublicController::class, 'showProject'])->name('public.accomplishments.project');
Route::get('/accomplishments/activity/{id}', [PublicController::class, 'showActivity'])->name('public.accomplishments.activity');

// Privacy and Terms pages
Route::get('/privacy-policy', [PublicController::class, 'privacyPolicy'])->name('public.privacy');
Route::get('/terms-of-service', [PublicController::class, 'termsOfService'])->name('public.terms');

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');

// Route for guest users to request an account
Route::get('/admin/contact', [ContactAdminController::class, 'contactAdmin'])->name('admin.contact');
Route::post('/admin/contact', [ContactAdminController::class, 'store'])->name('admin.contact.store')->middleware(['input.sanitize', 'rate.limit:30,1']);

// Forgot Password Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email')->middleware(['input.sanitize', 'rate.limit:10,15']);
// Reset Password Routes
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update')->middleware(['input.sanitize', 'rate.limit:5,15']);

// Authentication route
Route::post('/login', [LoginController::class, 'login'])->name('login.post')->middleware(['input.sanitize', 'login.rate.limit']);

// Two-Factor Authentication routes
Route::prefix('2fa')->name('2fa.')->group(function () {
    Route::get('/setup', [\App\Http\Controllers\Auth\TwoFactorAuthController::class, 'showSetup'])->name('setup');
    Route::post('/enable', [\App\Http\Controllers\Auth\TwoFactorAuthController::class, 'enable'])->name('enable');
    Route::get('/verify', [\App\Http\Controllers\Auth\TwoFactorAuthController::class, 'showVerification'])->name('verify');
    Route::post('/verify', [\App\Http\Controllers\Auth\TwoFactorAuthController::class, 'verify'])->name('verify.post');
    Route::get('/verify-operation', [\App\Http\Controllers\Auth\TwoFactorAuthController::class, 'showOperationVerification'])->name('verify-operation');
    Route::post('/verify-operation', [\App\Http\Controllers\Auth\TwoFactorAuthController::class, 'verifyOperation'])->name('verify-operation.post');
    Route::post('/disable', [\App\Http\Controllers\Auth\TwoFactorAuthController::class, 'disable'])->name('disable');
});

// Registration routes (accessible via token, not directly admin)
Route::get('/register/{token}', [RegistrationController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [RegistrationController::class, 'register'])->name('register')->middleware(['input.sanitize', 'rate.limit:10,15']);

Route::prefix('admin')->group(function () {
    // --- ADMIN ROUTES GROUP (Protected by 'admin.role' middleware) ---
    Route::middleware(['admin.role:secretary,captain,treasurer,councilor,nurse'])->group(function () {
        // Profile routes for viewing and updating profile
        Route::get('/profile', [AdminProfileController::class, 'profile'])->name('admin.profile');
        Route::put('/profile/update', [AdminProfileController::class, 'update'])->name('admin.profile.update');

        // Route to mark all notifications as read
        Route::post('/notifications/mark-all-as-read', [AdminNotificationController::class, 'markAllAsRead'])->name('admin.notifications.mark-all-as-read');
        Route::get('/notifications/count', [AdminNotificationController::class, 'getNotificationCounts'])->name('admin.notifications.count');
        Route::get('/notifications', [AdminNotificationController::class, 'showNotifications'])->name('admin.notifications');
        Route::post('/notifications/mark-as-read/{type}/{id}', [AdminNotificationController::class, 'markAsRead'])->name('admin.notifications.mark-as-read');
        Route::post('/notifications/mark-all-as-read-ajax', [AdminNotificationController::class, 'markAllAsReadAjax'])->name('admin.notifications.mark-all-as-read-ajax');
        Route::post('/notifications/mark-as-read-by-type/{type}', [AdminNotificationController::class, 'markAsReadByType'])->name('admin.notifications.mark-as-read-by-type');
        // Resident search (needed by medical forms) - allow nurse access
        Route::get('/search/residents', [ResidentController::class, 'search'])->name('admin.search.residents');
        Route::get('/residents/{resident}/summary', [ResidentController::class, 'summary'])->name('admin.residents.summary');
    });

    // Routes that all admin roles can view but only secretary can modify
    Route::middleware(['admin.role:secretary,captain,treasurer,councilor'])->group(function () {
        // Admin Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // User Management - View routes (all can access)
        Route::get('/barangay-profiles', [BarangayProfileController::class, 'barangayProfile'])->name('admin.barangay-profiles');
        Route::get('/residents', [ResidentController::class, 'residentProfile'])->name('admin.residents');
        Route::get('/residents/check-email', [ResidentController::class, 'checkEmailRequest'])->name('admin.residents.check-email');
        Route::get('/residents/check-duplicate-name', [ResidentController::class, 'checkDuplicateName'])->name('admin.residents.check-duplicate-name');
        Route::get('/residents/{resident}/demographics', [ResidentController::class, 'getDemographics'])->middleware('2fa:view_demographics')->name('admin.residents.demographics');

        // Analytics - View routes (all can access)
        Route::get('/clustering', [ClusteringController::class, 'index'])->name('admin.clustering');
        Route::get('/clustering/optimal-k', [ClusteringController::class, 'getOptimalK'])->name('admin.clustering.optimal-k');
        Route::get('/clustering/export', [ClusteringController::class, 'export'])->name('admin.clustering.export');
        Route::get('/clustering/stats', [ClusteringController::class, 'getClusterStats'])->name('admin.clustering.stats');
        
        // Clustering Analysis Routes (all admin roles can access)
        Route::prefix('clustering')->group(function () {
            Route::get('blotter/analysis', [ClusteringAnalysisController::class, 'blotterAnalysis'])->name('clustering.blotter.analysis');
            Route::get('document/analysis', [ClusteringAnalysisController::class, 'documentAnalysis'])->name('clustering.document.analysis');
        });

        // Program Recommendations Routes (all admin roles can access)
        Route::prefix('programs')->group(function () {
            Route::get('/', [ProgramRecommendationController::class, 'index'])->name('admin.programs.index');
            Route::get('/{programId}', [ProgramRecommendationController::class, 'byProgram'])->name('admin.programs.show');
            Route::get('/purok/{purok}', [ProgramRecommendationController::class, 'byPurok'])->name('admin.programs.purok');
            Route::get('/resident/{residentId}', [ProgramRecommendationController::class, 'byResident'])->name('admin.programs.resident');
            Route::get('/{programId}/export', [ProgramRecommendationController::class, 'export'])->name('admin.programs.export');
            Route::get('/{programId}/purok-groups', [ProgramRecommendationController::class, 'purokGroups'])->name('admin.programs.purok-groups');
        });
    });

    // Reports & Requests - View routes (exclude treasurer)
    Route::middleware(['admin.role:secretary,captain,councilor'])->group(function () {
        Route::get('/blotter-reports', [BlotterReportController::class, 'blotterReport'])->name('admin.blotter-reports');
        Route::get('/blotter-reports/{id}/details', [BlotterReportController::class, 'getDetails'])->name('admin.blotter-reports.details');
        
        Route::get('/community-concerns', [CommunityConcernController::class, 'index'])->name('admin.community-concerns');
        Route::get('/community-concerns/{id}/details', [CommunityConcernController::class, 'getDetails'])->name('admin.community-concerns.details');
        
        Route::get('/document-requests', [DocumentRequestController::class, 'documentRequest'])->name('admin.document-requests');
        Route::get('/document-requests/download/{id}', [DocumentRequestController::class, 'downloadRequest'])->name('document-requests.download');
        Route::get('/document-requests/{id}/details', [DocumentRequestController::class, 'getDetails'])->name('admin.document-requests.details');
        Route::get('/document-requests/{id}/pdf', [DocumentRequestController::class, 'generatePdf'])->name('admin.document-requests.pdf');
        
        Route::get('/templates', [DocumentTemplateController::class, 'index'])->name('admin.templates.index');
        Route::get('/templates/{template}/preview', [DocumentTemplateController::class, 'preview'])->name('admin.templates.preview');
        Route::get('/templates/{template}/form-config', [DocumentTemplateController::class, 'formConfig'])->name('admin.templates.form-config');
        Route::post('/templates/{template}/validate', [DocumentTemplateController::class, 'validateTemplate'])->name('admin.templates.validate');
        Route::get('/templates/{template}/test', [DocumentTemplateController::class, 'testTemplate'])->name('admin.templates.test');
        
        Route::get('/blotter-templates', [BlotterTemplateController::class, 'index'])->name('admin.blotter-templates.index');
        Route::get('/blotter-templates/{template}/preview', [BlotterTemplateController::class, 'preview'])->name('admin.blotter-templates.preview');
        
        Route::get('/new-account-requests', [AccountRequestController::class, 'accountRequest'])->name('admin.requests.new-account-requests');
    });

    // Transaction routes - only admin and secretary can access
    Route::middleware(['admin.secretary'])->group(function () {
        // User Management transactions
        Route::get('/barangay-profiles/create', [BarangayProfileController::class, 'create'])->name('admin.barangay-profiles.create');
        Route::post('/barangay-profiles', [BarangayProfileController::class, 'store'])->name('admin.barangay-profiles.store')->middleware(['input.sanitize', 'rate.limit:20,1']);
        Route::get('/barangay-profiles/{id}/edit', [BarangayProfileController::class, 'edit'])->name('admin.barangay-profiles.edit');
        Route::put('/barangay-profiles/{id}', [BarangayProfileController::class, 'update'])->name('admin.barangay-profiles.update')->middleware(['input.sanitize', 'rate.limit:20,1']);
        Route::put('/barangay-profiles/{id}/activate', [BarangayProfileController::class, 'activate'])->name('admin.barangay-profiles.activate');
        Route::put('/barangay-profiles/{id}/deactivate', [BarangayProfileController::class, 'deactivate'])->name('admin.barangay-profiles.deactivate');
        Route::delete('/barangay-profiles/{id}', [BarangayProfileController::class, 'delete'])->name('admin.barangay-profiles.delete');

        Route::get('/residents/create', [ResidentController::class, 'create'])->name('admin.residents.create');
        Route::post('/residents', [ResidentController::class, 'store'])->name('admin.residents.store')->middleware(['input.sanitize', 'rate.limit:20,1']);
        Route::get('/residents/{id}/edit', [ResidentController::class, 'edit'])->middleware('2fa:edit_resident')->name('admin.residents.edit');
        Route::put('/residents/{id}', [ResidentController::class, 'update'])->middleware(['2fa:edit_resident', 'input.sanitize', 'rate.limit:20,1'])->name('admin.residents.update');
        Route::put('/residents/{id}/activate', [ResidentController::class, 'activate'])->name('admin.residents.activate');
        Route::put('/residents/{id}/deactivate', [ResidentController::class, 'deactivate'])->name('admin.residents.deactivate');
        Route::delete('/residents/{id}', [ResidentController::class, 'delete'])->middleware('2fa:delete_resident')->name('admin.residents.delete');
        Route::get('/residents/{id}/delete-confirm', [ResidentController::class, 'deleteConfirm'])->middleware('2fa:delete_resident')->name('admin.residents.delete.confirm');

        // Blotter Reports transactions
        Route::get('/blotter-reports/create', [BlotterReportController::class, 'create'])->name('admin.blotter-reports.create');
        Route::post('/blotter-reports', [BlotterReportController::class, 'store'])->name('admin.blotter-reports.store');
        Route::post('/blotter-reports/{id}/approve', [BlotterReportController::class, 'approve'])->name('admin.blotter-reports.approve');
        Route::post('/blotter-reports/{id}/new-summons', [BlotterReportController::class, 'generateNewSummons'])->name('admin.blotter-reports.new-summons');
        Route::post('/blotter-reports/{id}/complete', [BlotterReportController::class, 'markAsComplete'])->name('admin.blotter-reports.complete');
        
        // Community Concerns transactions
        Route::post('/community-concerns/{id}/update-status', [CommunityConcernController::class, 'updateStatus'])->name('admin.community-concerns.update-status');
        
        // Document Requests transactions
        Route::get('/document-requests/create', [DocumentRequestController::class, 'create'])->name('admin.document-requests.create');
        Route::post('/document-requests', [DocumentRequestController::class, 'store'])->name('admin.document-requests.store');
        Route::post('/document-requests/{id}/approve', [DocumentRequestController::class, 'approve'])->name('admin.document-requests.approve');
        Route::post('/document-requests/{id}/complete', [DocumentRequestController::class, 'markAsComplete'])->name('admin.document-requests.complete');

        // Document Templates transactions
        Route::get('/templates/create', [DocumentTemplateController::class, 'create'])->name('admin.templates.create');
        Route::post('/templates', [DocumentTemplateController::class, 'store'])->name('admin.templates.store');
        Route::get('/templates/{template}/edit', [DocumentTemplateController::class, 'edit'])->name('admin.templates.edit');
        Route::get('/templates/{template}/builder', [DocumentTemplateController::class, 'builder'])->name('admin.templates.builder');
        Route::get('/templates/{template}/word-integration', [DocumentTemplateController::class, 'wordIntegration'])->name('admin.templates.word-integration');
        Route::get('/templates/{template}/download-word', [DocumentTemplateController::class, 'downloadWord'])->name('admin.templates.download-word');
        Route::post('/templates/{template}/upload-word', [DocumentTemplateController::class, 'uploadWord'])->name('admin.templates.upload-word');
        Route::post('/templates/upload-word', [DocumentTemplateController::class, 'storeFromWord'])->name('admin.templates.store-from-word');
        
        // New DOCX routes
        Route::get('/templates/{template}/download-docx', [DocumentTemplateController::class, 'downloadDocx'])->name('admin.templates.download-docx');
        Route::post('/templates/{template}/upload-docx', [DocumentTemplateController::class, 'uploadDocx'])->name('admin.templates.upload-docx');
        Route::post('/templates/upload-docx', [DocumentTemplateController::class, 'storeFromDocx'])->name('admin.templates.store-from-docx');
        
        Route::put('/templates/{template}', [DocumentTemplateController::class, 'update'])->name('admin.templates.update');
        Route::post('/templates/{template}/reset', [DocumentTemplateController::class, 'reset'])->name('admin.templates.reset');
        Route::post('/templates/{template}/toggle-status', [DocumentTemplateController::class, 'toggleStatus'])->name('admin.templates.toggle-status');
        Route::post('/templates/validate', [DocumentTemplateController::class, 'validateTemplate'])->name('admin.templates.validate-new');
        Route::delete('/templates/{template}', [DocumentTemplateController::class, 'destroy'])->name('admin.templates.destroy');
        
        // Blotter Templates transactions
        Route::get('/blotter-templates/{template}/edit', [BlotterTemplateController::class, 'edit'])->name('admin.blotter-templates.edit');
        Route::get('/blotter-templates/{template}/builder', [BlotterTemplateController::class, 'builder'])->name('admin.blotter-templates.builder');
        Route::put('/blotter-templates/{template}', [BlotterTemplateController::class, 'update'])->name('admin.blotter-templates.update');
        Route::post('/blotter-templates/{template}/reset', [BlotterTemplateController::class, 'reset'])->name('admin.blotter-templates.reset');
        Route::post('/blotter-templates/{template}/toggle-status', [BlotterTemplateController::class, 'toggleStatus'])->name('admin.blotter-templates.toggle-status');
        
        // Account Requests transactions
        Route::put('/new-account-requests/{id}/approve', [AccountRequestController::class, 'approveAccountRequest'])->name('admin.account-requests.approve');
        Route::post('/new-account-requests/{id}/reject', [AccountRequestController::class, 'rejectAccountRequest'])->name('admin.account-requests.reject');

        // Analytics transactions
        Route::post('/clustering/perform', [ClusteringController::class, 'performClustering'])->name('admin.clustering.perform');
    });

    // --- ADMIN ROUTES GROUP (Protected by 'admin.role' middleware) ---
    Route::middleware(['admin.role:nurse'])->group(function () {
        // Health Reports Route
        Route::get('/health-reports', [HealthReportController::class, 'healthReport'])->name('admin.health-reports');
        Route::get('/health-reports/comprehensive', [HealthReportController::class, 'generateComprehensiveReport'])->name('admin.health-reports.comprehensive');
        Route::get('/health-reports/export', [HealthReportController::class, 'exportReport'])->name('admin.health-reports.export');
        Route::get('/health/patient-search', [PatientHealthProfileController::class, 'search'])
            ->name('admin.health.patient-search');
        Route::get('/health/patient/{resident}', [PatientHealthProfileController::class, 'show'])
            ->whereNumber('resident')
            ->name('admin.health.patient-profile');

        // Medical Records Routes
        Route::get('/medical-records', [MedicalRecordController::class, 'index'])->name('admin.medical-records.index');
        Route::get('/medical-records/create', [MedicalRecordController::class, 'create'])->name('admin.medical-records.create');
        Route::post('/medical-records', [MedicalRecordController::class, 'store'])->name('admin.medical-records.store');
        Route::get('/medical-records/{id}', [MedicalRecordController::class, 'show'])->name('admin.medical-records.show');
        Route::delete('/medical-records/{id}', [MedicalRecordController::class, 'destroy'])->name('admin.medical-records.destroy');
        Route::get('/medical-records/report', [MedicalRecordController::class, 'generateReport'])->name('admin.medical-records.report');

        // Medicines Inventory Routes
        Route::get('/medicines', [MedicineController::class, 'index'])->name('admin.medicines.index');
        Route::get('/medicines/create', [MedicineController::class, 'create'])->name('admin.medicines.create');
        Route::post('/medicines', [MedicineController::class, 'store'])->name('admin.medicines.store');
        Route::get('/medicines/{medicine}/edit', [MedicineController::class, 'edit'])->name('admin.medicines.edit');
        Route::put('/medicines/{medicine}', [MedicineController::class, 'update'])->name('admin.medicines.update');
        Route::post('/medicines/{medicine}/restock', [MedicineController::class, 'restock'])->name('admin.medicines.restock');
        Route::delete('/medicines/{medicine}', [MedicineController::class, 'destroy'])->name('admin.medicines.destroy');
        Route::get('/medicines/report', [MedicineController::class, 'report'])->name('admin.medicines.report');

        // Medicine Requests Routes
        Route::get('/medicine-requests', [MedicineRequestController::class, 'index'])->name('admin.medicine-requests.index');
        Route::get('/medicine-requests/create', [MedicineRequestController::class, 'create'])->name('admin.medicine-requests.create');
        Route::post('/medicine-requests', [MedicineRequestController::class, 'store'])->name('admin.medicine-requests.store');
        Route::post('/medicine-requests/{medicineRequest}/approve', [MedicineRequestController::class, 'approve'])->name('admin.medicine-requests.approve');
        Route::post('/medicine-requests/{medicineRequest}/reject', [MedicineRequestController::class, 'reject'])->name('admin.medicine-requests.reject');

        // Medicine Transactions Routes
        Route::get('/medicine-transactions', [MedicineTransactionController::class, 'index'])->name('admin.medicine-transactions.index');
        Route::post('/medicine-transactions/{medicine}/adjust', [MedicineTransactionController::class, 'adjustStock'])->name('admin.medicine-transactions.adjust');

        // Health Center Activities Routes
        Route::get('/health-center-activities', [HealthCenterActivityController::class, 'index'])->name('admin.health-center-activities.index');
        Route::get('/health-center-activities/create', [HealthCenterActivityController::class, 'create'])->name('admin.health-center-activities.create');
        Route::post('/health-center-activities', [HealthCenterActivityController::class, 'store'])->name('admin.health-center-activities.store');
        Route::get('/health-center-activities/{id}', [HealthCenterActivityController::class, 'show'])->name('admin.health-center-activities.show');
        Route::get('/health-center-activities/{id}/edit', [HealthCenterActivityController::class, 'edit'])->name('admin.health-center-activities.edit');
        Route::put('/health-center-activities/{id}', [HealthCenterActivityController::class, 'update'])->name('admin.health-center-activities.update');
        Route::delete('/health-center-activities/{id}', [HealthCenterActivityController::class, 'destroy'])->name('admin.health-center-activities.destroy');
        // merged search into index; route no longer needed
        Route::get('/health-center-activities/upcoming', [HealthCenterActivityController::class, 'upcoming'])->name('admin.health-center-activities.upcoming');
        Route::get('/health-center-activities/completed', [HealthCenterActivityController::class, 'completed'])->name('admin.health-center-activities.completed');
        Route::get('/health-center-activities/report', [HealthCenterActivityController::class, 'generateReport'])->name('admin.health-center-activities.report');
        Route::post('/health-center-activities/{id}/toggle-featured', [HealthCenterActivityController::class, 'toggleFeatured'])->name('admin.health-center-activities.toggle-featured');
    });

    // QR Code Verification (public endpoint for scanning)
    Route::get('/qr/verify/{token}', [QRCodeController::class, 'verify'])->name('qr.verify');
    
    // QR Code & Attendance Routes (for admin/staff)
    Route::middleware(['admin.role:secretary,captain,councilor,nurse'])->group(function () {
        // Attendance Scanner
        Route::get('/attendance/scanner', [AttendanceController::class, 'scanner'])->name('admin.attendance.scanner');
        Route::post('/attendance/scan', [AttendanceController::class, 'scan'])->name('admin.attendance.scan');
        Route::post('/attendance/add-manual', [AttendanceController::class, 'addManualAttendance'])->name('admin.attendance.add-manual');
        Route::get('/attendance/get-attendance', [AttendanceController::class, 'getAttendance'])->name('admin.attendance.get');
        Route::get('/attendance/logs', [AttendanceController::class, 'logs'])->name('admin.attendance.logs');
        Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('admin.attendance.report');
        
        // Events Management (retired - use Barangay Activities based on Accomplished Projects instead)
        // Route::get('/events', [EventController::class, 'index'])->name('admin.events.index');
        // Route::get('/events/create', [EventController::class, 'create'])->name('admin.events.create');
        // Route::post('/events', [EventController::class, 'store'])->name('admin.events.store');
        // Route::get('/events/{id}', [EventController::class, 'show'])->name('admin.events.show');
        // Route::get('/events/{id}/edit', [EventController::class, 'edit'])->name('admin.events.edit');
        // Route::put('/events/{id}', [EventController::class, 'update'])->name('admin.events.update');
        // Route::delete('/events/{id}', [EventController::class, 'destroy'])->name('admin.events.destroy');
    });

    Route::middleware(['admin.role:treasurer'])->group(function () {

        // Accomplished Projects Routes
        Route::get('/accomplished-projects', [AccomplishProjectController::class, 'accomplishProject'])->name('admin.accomplished-projects');
        Route::get('/accomplished-projects/create', [AccomplishProjectController::class, 'create'])->name('admin.accomplished-projects.create');
        Route::get('/accomplished-projects/{id}', [AccomplishProjectController::class, 'show'])->name('admin.accomplished-projects.show');
        Route::get('/accomplished-projects/{id}/edit', [AccomplishProjectController::class, 'edit'])->name('admin.accomplished-projects.edit');
        Route::post('/accomplished-projects', [AccomplishProjectController::class, 'store'])->name('admin.accomplished-projects.store');
        Route::put('/accomplished-projects/{id}', [AccomplishProjectController::class, 'update'])->name('admin.accomplished-projects.update');
        Route::delete('/accomplished-projects/{id}', [AccomplishProjectController::class, 'destroy'])->name('admin.accomplished-projects.destroy');
        Route::post('/accomplished-projects/{id}/toggle-featured', [AccomplishProjectController::class, 'toggleFeatured'])->name('admin.accomplished-projects.toggle-featured');
        
    });
});

Route::middleware(['admin.role'])->prefix('admin/faqs')->name('admin.faqs.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminControllers\Settings\FaqController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\AdminControllers\Settings\FaqController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\AdminControllers\Settings\FaqController::class, 'store'])->name('store');
    Route::get('/{faq}/edit', [\App\Http\Controllers\AdminControllers\Settings\FaqController::class, 'edit'])->name('edit');
    Route::put('/{faq}', [\App\Http\Controllers\AdminControllers\Settings\FaqController::class, 'update'])->name('update');
    Route::delete('/{faq}', [\App\Http\Controllers\AdminControllers\Settings\FaqController::class, 'destroy'])->name('destroy');
    Route::post('/reorder', [\App\Http\Controllers\AdminControllers\Settings\FaqController::class, 'reorder'])->name('reorder');
    Route::patch('/{faq}/toggle', [\App\Http\Controllers\AdminControllers\Settings\FaqController::class, 'toggle'])->name('toggle');
});

Route::get('/admin/blotter-reports/{id}/check-active', [App\Http\Controllers\AdminControllers\ReportRequestControllers\BlotterReportController::class, 'checkActive'])->name('admin.blotter-reports.check-active');
Route::get('/admin/document-requests/{id}/check-active', [App\Http\Controllers\AdminControllers\ReportRequestControllers\DocumentRequestController::class, 'checkActive'])->name('admin.document-requests.check-active');


Route::middleware(['resident.role'])->prefix('resident')->group(function () {
    // Resident Dashboard
    Route::get('/dashboard', [ResidentDashboardController::class, 'dashboard'])->name('resident.dashboard');

    // Blotter Requests
    Route::get('/request-blotter', [ResidentBlotterController::class, 'requestBlotter'])->name('resident.request_blotter_report');
    Route::post('/request-blotter', [ResidentBlotterController::class, 'storeBlotter'])->middleware(['input.sanitize', 'rate.limit:10,5']);

    // Community Concerns
    Route::get('/request-community-concern', [ResidentCommunityConcernController::class, 'requestCommunityConcern'])->name('resident.request_community_concern');
    Route::post('/request-community-concern', [ResidentCommunityConcernController::class, 'storeCommunityConcern'])->middleware(['input.sanitize', 'rate.limit:10,5']);
    Route::get('/community-concerns/{id}/details', [ResidentCommunityConcernController::class, 'getDetails'])->name('resident.community-concerns.details');

    // Document Requests
    Route::get('/request-document', [ResidentDocumentRequestController::class, 'requestDocument'])->name('resident.request_document_request');
    Route::post('/request-document', [ResidentDocumentRequestController::class, 'storeDocument'])->middleware(['input.sanitize', 'rate.limit:10,5']);
    Route::get('/templates/{template}/form-config', [DocumentTemplateController::class, 'formConfig'])->name('resident.templates.form-config');

    // My Requests (Listing & Filtering)
    Route::get('/my-requests', [ResidentRequestListController::class, 'myRequests'])->name('resident.my-requests');

    // Resident Notifications
    Route::get('/notifications', [ResidentNotificationController::class, 'index'])->name('resident.notifications');
    Route::get('/notifications/count', [ResidentNotificationController::class, 'count'])->name('resident.notifications.count');
    Route::post('/notifications/mark-all', [ResidentNotificationController::class, 'markAllAsRead'])->name('resident.notifications.mark-all');
    Route::post('/notifications/mark-as-read/{id}', [ResidentNotificationController::class, 'markAsRead'])->name('resident.notifications.mark-as-read');

    // Resident search (for selecting respondents in blotter reports)
    Route::get('/search/residents', [ResidentController::class, 'search'])->name('resident.search.residents');

    // Profile
    Route::get('/profile', [ResidentProfileController::class, 'profile'])->name('resident.profile');

    // Community Bulletin Board
    Route::get('/announcements', [ResidentAnnouncementController::class, 'announcements'])->name('resident.announcements');
    Route::get('/announcements/project/{id}', [ResidentAnnouncementController::class, 'showProject'])->name('resident.announcements.project');
    Route::get('/announcements/activity/{id}', [ResidentAnnouncementController::class, 'showActivity'])->name('resident.announcements.activity');
    Route::put('/profile/update', [ResidentProfileController::class, 'updateProfile'])->name('resident.profile.update');

    // Resident FAQs
    Route::get('/faqs', [ResidentFaqController::class, 'index'])->name('resident.faqs');

    // QR Code
    Route::get('/qr-code', [QRCodeController::class, 'show'])->name('resident.qr-code');
    Route::get('/qr-code/download', [QRCodeController::class, 'download'])->name('resident.qr-code.download');
});

// Logout route
Route::post('/logout', function () {
    Session::flush();
    return redirect()->route('landing');
})->name('logout');

// Test Email Route (Using Brevo API)
Route::get('/test-email', function () {
    try {
        $email = request('email', 'rodericktajos02@gmail.com');
        $useQueue = request('queue', 'true'); // Default to queue to avoid timeout
        
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Error: Invalid email address: ' . $email;
        }
        
        $message = request('message', 'Hello from Laravel via Brevo API!');
        $apiKey = env('BREVO_API_KEY');
        
        if (empty($apiKey)) {
            return 'Error: BREVO_API_KEY is not set in .env file.<br><br>' .
                   'Get your API key from: <a href="https://app.brevo.com/settings/keys/api" target="_blank">Brevo API Settings</a>';
        }
        
        if ($useQueue === 'true' || $useQueue === '1') {
            // Dispatch job to queue (prevents 504 timeout)
            \App\Jobs\SendTestEmailJob::dispatch($email, $message);
            
            return 'Email queued successfully for ' . $email . '!<br><br>' .
                   'The email will be sent in the background. Check your queue worker or logs to confirm delivery.<br><br>' .
                   'To send immediately (may timeout), use: <code>?queue=false</code>';
        } else {
            // Send immediately with short timeout
            $response = Http::timeout(5) // 5 second timeout
                ->withHeaders([
                    'accept' => 'application/json',
                    'api-key' => $apiKey,
                    'content-type' => 'application/json',
                ])->post('https://api.brevo.com/v3/smtp/email', [
                    'sender' => [
                        'name' => env('MAIL_FROM_NAME', 'BARMMS'),
                        'email' => env('MAIL_FROM_ADDRESS', 'no-reply@barmmslowermalinao.app'),
                    ],
                    'to' => [
                        [
                            'email' => $email,
                        ],
                    ],
                    'subject' => 'Test Email from BARMMS',
                    'htmlContent' => '<html><body><h1>Test Email</h1><p>' . htmlspecialchars($message) . '</p><p>If you received this email, it means your Brevo API configuration is working correctly.</p></body></html>',
                    'textContent' => $message . "\n\nIf you received this email, it means your Brevo API configuration is working correctly.",
                ]);
            
            if ($response->successful()) {
                return 'Email sent successfully to ' . $email . '!<br><br>' .
                       'Response: ' . json_encode($response->json(), JSON_PRETTY_PRINT);
            } else {
                return 'Error sending email:<br>' .
                       'Status: ' . $response->status() . '<br>' .
                       'Response: ' . $response->body();
            }
        }
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage() . '<br><br>' .
               'Make sure your Brevo API key is configured correctly in .env file:<br>' .
               '<code>BREVO_API_KEY=your-brevo-api-key<br>' .
               'MAIL_FROM_ADDRESS=your-verified-sender@example.com<br>' .
               'MAIL_FROM_NAME="BARMMS"</code><br><br>' .
               'Get your API key from: <a href="https://app.brevo.com/settings/keys/api" target="_blank">Brevo API Settings</a><br><br>' .
               '<strong>Note:</strong> If you still get 504 errors, you may need to increase server timeouts. See instructions below.<br><br>' .
               'Stack trace: <pre>' . $e->getTraceAsString() . '</pre>';
    }
});

