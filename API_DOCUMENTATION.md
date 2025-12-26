# BARMMS API Documentation

Complete API reference for the Barangay Management System (BARMMS).

## Table of Contents
1. [Public/Unauthenticated APIs](#publicunauthenticated-apis)
2. [Authentication APIs](#authentication-apis)
3. [Admin APIs](#admin-apis)
4. [Resident APIs](#resident-apis)
5. [Python Analytics Service APIs](#python-analytics-service-apis)
6. [AJAX/Internal API Endpoints](#ajaxinternal-api-endpoints)

---

## Public/Unauthenticated APIs

### Landing & Public Pages
- `GET /` - Landing page
- `GET /accomplishments` - Public accomplished projects listing
- `GET /accomplishments/project/{id}` - View specific project details
- `GET /accomplishments/activity/{id}` - View activity details
- `GET /privacy-policy` - Privacy policy page
- `GET /terms-of-service` - Terms of service page

### Account Request
- `GET /admin/contact` - Contact admin form
- `POST /admin/contact` - Submit account request (Rate limit: 30/min)

### QR Code Verification
- `GET /admin/qr/verify/{token}` - Verify QR code token (public endpoint)

---

## Authentication APIs

### Login
- `GET /login` - Show login form
- `POST /login` - Authenticate user (Rate limit: login.rate.limit)

### Password Reset
- `GET /forgot-password` - Show password reset request form
- `POST /forgot-password` - Send password reset email (Rate limit: 10/15min)
- `GET /reset-password/{token}` - Show password reset form
- `POST /reset-password` - Reset password (Rate limit: 5/15min)

### Two-Factor Authentication (2FA)
- `GET /2fa/setup` - Show 2FA setup page
- `POST /2fa/enable` - Enable 2FA
- `GET /2fa/verify` - Show 2FA verification form
- `POST /2fa/verify` - Verify 2FA code
- `GET /2fa/verify-operation` - Show operation verification form
- `POST /2fa/verify-operation` - Verify operation (for sensitive actions)
- `POST /2fa/disable` - Disable 2FA

### Registration
- `GET /register/{token}` - Show registration form (token-based)
- `POST /register` - Complete registration (Rate limit: 10/15min)

### Logout
- `POST /logout` - Logout user

---

## Admin APIs

### Profile Management
**Access:** All admin roles (secretary, captain, treasurer, councilor, nurse)
- `GET /admin/profile` - View admin profile
- `PUT /admin/profile/update` - Update admin profile

### Notifications
**Access:** All admin roles
- `GET /admin/notifications` - View all notifications
- `GET /admin/notifications/count` - Get notification counts (AJAX)
- `POST /admin/notifications/mark-all-as-read` - Mark all as read
- `POST /admin/notifications/mark-all-as-read-ajax` - Mark all as read (AJAX)
- `POST /admin/notifications/mark-as-read/{type}/{id}` - Mark specific notification as read
- `POST /admin/notifications/mark-as-read-by-type/{type}` - Mark all notifications of type as read

### Resident Search
**Access:** All admin roles
- `GET /admin/search/residents` - Search residents (AJAX)
- `GET /admin/residents/{resident}/summary` - Get resident summary

### Dashboard
**Access:** secretary, captain, treasurer, councilor
- `GET /admin/dashboard` - Admin dashboard

### User Management
**Access:** secretary, captain, treasurer, councilor (view), secretary only (modify)

#### Barangay Profiles
- `GET /admin/barangay-profiles` - List barangay profiles
- `GET /admin/barangay-profiles/create` - Create form (secretary only)
- `POST /admin/barangay-profiles` - Store new profile (secretary only, Rate limit: 20/min)
- `GET /admin/barangay-profiles/{id}/edit` - Edit form (secretary only)
- `PUT /admin/barangay-profiles/{id}` - Update profile (secretary only, Rate limit: 20/min)
- `PUT /admin/barangay-profiles/{id}/activate` - Activate profile (secretary only)
- `PUT /admin/barangay-profiles/{id}/deactivate` - Deactivate profile (secretary only)
- `DELETE /admin/barangay-profiles/{id}` - Delete profile (secretary only)

#### Residents
- `GET /admin/residents` - List residents
- `GET /admin/residents/check-email` - Check email availability (AJAX)
- `GET /admin/residents/{resident}/demographics` - Get demographics (2FA protected)
- `GET /admin/residents/create` - Create form (secretary only)
- `POST /admin/residents` - Store new resident (secretary only, Rate limit: 20/min)
- `GET /admin/residents/{id}/edit` - Edit form (secretary only, 2FA protected)
- `PUT /admin/residents/{id}` - Update resident (secretary only, 2FA protected, Rate limit: 20/min)
- `PUT /admin/residents/{id}/activate` - Activate resident (secretary only)
- `PUT /admin/residents/{id}/deactivate` - Deactivate resident (secretary only)
- `GET /admin/residents/{id}/delete-confirm` - Delete confirmation (secretary only, 2FA protected)
- `DELETE /admin/residents/{id}` - Delete resident (secretary only, 2FA protected)

### Analytics & Clustering
**Access:** secretary, captain, treasurer, councilor

#### Clustering
- `GET /admin/clustering` - Clustering analysis page
- `GET /admin/clustering/optimal-k` - Get optimal K value (AJAX)
- `GET /admin/clustering/export` - Export clustering data
- `GET /admin/clustering/stats` - Get cluster statistics (AJAX)
- `POST /admin/clustering/perform` - Perform clustering (secretary only)

#### Clustering Analysis
- `GET /admin/clustering/blotter/analysis` - Blotter-based clustering analysis
- `GET /admin/clustering/document/analysis` - Document request-based clustering analysis

#### Program Recommendations
- `GET /admin/programs` - List program recommendations
- `GET /admin/programs/{programId}` - View program details
- `GET /admin/programs/purok/{purok}` - Get recommendations by purok
- `GET /admin/programs/resident/{residentId}` - Get recommendations for resident
- `GET /admin/programs/{programId}/export` - Export program data
- `GET /admin/programs/{programId}/purok-groups` - Get purok groups for program

### Reports & Requests
**Access:** secretary, captain, councilor

#### Blotter Reports
- `GET /admin/blotter-reports` - List blotter reports
- `GET /admin/blotter-reports/{id}/details` - Get report details (AJAX)
- `GET /admin/blotter-reports/{id}/check-active` - Check if resident is active (AJAX)
- `GET /admin/blotter-reports/create` - Create form (secretary only)
- `POST /admin/blotter-reports` - Store new report (secretary only)
- `POST /admin/blotter-reports/{id}/approve` - Approve report (secretary only)
- `POST /admin/blotter-reports/{id}/new-summons` - Generate new summons (secretary only)
- `POST /admin/blotter-reports/{id}/complete` - Mark as complete (secretary only)

#### Community Concerns
- `GET /admin/community-concerns` - List community concerns
- `GET /admin/community-concerns/{id}/details` - Get concern details (AJAX)
- `POST /admin/community-concerns/{id}/update-status` - Update status (secretary only)

#### Document Requests
- `GET /admin/document-requests` - List document requests
- `GET /admin/document-requests/{id}/details` - Get request details (AJAX)
- `GET /admin/document-requests/{id}/check-active` - Check if resident is active (AJAX)
- `GET /admin/document-requests/{id}/pdf` - Generate PDF document
- `GET /admin/document-requests/download/{id}` - Download document
- `GET /admin/document-requests/create` - Create form (secretary only)
- `POST /admin/document-requests` - Store new request (secretary only)
- `POST /admin/document-requests/{id}/approve` - Approve request (secretary only)
- `POST /admin/document-requests/{id}/complete` - Mark as complete (secretary only)

#### Document Templates
- `GET /admin/templates` - List document templates
- `GET /admin/templates/{template}/preview` - Preview template
- `GET /admin/templates/{template}/form-config` - Get form configuration (AJAX)
- `POST /admin/templates/{template}/validate` - Validate template
- `GET /admin/templates/{template}/test` - Test template
- `GET /admin/templates/create` - Create form (secretary only)
- `POST /admin/templates` - Store new template (secretary only)
- `GET /admin/templates/{template}/edit` - Edit form (secretary only)
- `GET /admin/templates/{template}/builder` - Template builder (secretary only)
- `GET /admin/templates/{template}/word-integration` - Word integration page (secretary only)
- `GET /admin/templates/{template}/download-word` - Download Word template (secretary only)
- `POST /admin/templates/{template}/upload-word` - Upload Word template (secretary only)
- `POST /admin/templates/upload-word` - Store from Word upload (secretary only)
- `GET /admin/templates/{template}/download-docx` - Download DOCX template (secretary only)
- `POST /admin/templates/{template}/upload-docx` - Upload DOCX template (secretary only)
- `POST /admin/templates/upload-docx` - Store from DOCX upload (secretary only)
- `PUT /admin/templates/{template}` - Update template (secretary only)
- `POST /admin/templates/{template}/reset` - Reset template (secretary only)
- `POST /admin/templates/{template}/toggle-status` - Toggle template status (secretary only)
- `POST /admin/templates/validate` - Validate new template (secretary only)
- `DELETE /admin/templates/{template}` - Delete template (secretary only)

#### Account Requests
- `GET /admin/new-account-requests` - List account requests
- `PUT /admin/new-account-requests/{id}/approve` - Approve account request (secretary only)
- `POST /admin/new-account-requests/{id}/reject` - Reject account request (secretary only)

### Health Management
**Access:** nurse only

#### Health Reports
- `GET /admin/health-reports` - Health reports page
- `GET /admin/health-reports/comprehensive` - Generate comprehensive report
- `GET /admin/health-reports/export` - Export health report

#### Patient Health Profile
- `GET /admin/health/patient-search` - Search patients (AJAX)
- `GET /admin/health/patient/{resident}` - View patient profile

#### Medical Records
- `GET /admin/medical-records` - List medical records
- `GET /admin/medical-records/create` - Create form
- `POST /admin/medical-records` - Store new record
- `GET /admin/medical-records/{id}` - View record details
- `DELETE /admin/medical-records/{id}` - Delete record
- `GET /admin/medical-records/report` - Generate medical records report

#### Medicines Inventory
- `GET /admin/medicines` - List medicines
- `GET /admin/medicines/create` - Create form
- `POST /admin/medicines` - Store new medicine
- `GET /admin/medicines/{medicine}/edit` - Edit form
- `PUT /admin/medicines/{medicine}` - Update medicine
- `POST /admin/medicines/{medicine}/restock` - Restock medicine
- `DELETE /admin/medicines/{medicine}` - Delete medicine
- `GET /admin/medicines/report` - Generate medicine report

#### Medicine Requests
- `GET /admin/medicine-requests` - List medicine requests
- `GET /admin/medicine-requests/create` - Create form
- `POST /admin/medicine-requests` - Store new request
- `POST /admin/medicine-requests/{medicineRequest}/approve` - Approve request
- `POST /admin/medicine-requests/{medicineRequest}/reject` - Reject request

#### Medicine Transactions
- `GET /admin/medicine-transactions` - List transactions
- `POST /admin/medicine-transactions/{medicine}/adjust` - Adjust stock

#### Health Center Activities
- `GET /admin/health-center-activities` - List activities
- `GET /admin/health-center-activities/create` - Create form
- `POST /admin/health-center-activities` - Store new activity
- `GET /admin/health-center-activities/{id}` - View activity details
- `GET /admin/health-center-activities/{id}/edit` - Edit form
- `PUT /admin/health-center-activities/{id}` - Update activity
- `DELETE /admin/health-center-activities/{id}` - Delete activity
- `GET /admin/health-center-activities/upcoming` - List upcoming activities
- `GET /admin/health-center-activities/completed` - List completed activities
- `GET /admin/health-center-activities/report` - Generate activity report
- `POST /admin/health-center-activities/{id}/toggle-featured` - Toggle featured status

### Attendance Management
**Access:** secretary, captain, councilor, nurse
- `GET /admin/attendance/scanner` - Attendance scanner page
- `POST /admin/attendance/scan` - Scan QR code for attendance
- `POST /admin/attendance/add-manual` - Add manual attendance
- `GET /admin/attendance/get-attendance` - Get attendance data (AJAX)
- `GET /admin/attendance/logs` - View attendance logs
- `GET /admin/attendance/report` - Generate attendance report

### Accomplished Projects
**Access:** treasurer only
- `GET /admin/accomplished-projects` - List projects
- `GET /admin/accomplished-projects/create` - Create form
- `POST /admin/accomplished-projects` - Store new project
- `GET /admin/accomplished-projects/{id}` - View project details
- `GET /admin/accomplished-projects/{id}/edit` - Edit form
- `PUT /admin/accomplished-projects/{id}` - Update project
- `DELETE /admin/accomplished-projects/{id}` - Delete project
- `POST /admin/accomplished-projects/{id}/toggle-featured` - Toggle featured status

### FAQs
**Access:** All admin roles
- `GET /admin/faqs` - List FAQs
- `GET /admin/faqs/create` - Create form
- `POST /admin/faqs` - Store new FAQ
- `GET /admin/faqs/{faq}/edit` - Edit form
- `PUT /admin/faqs/{faq}` - Update FAQ
- `DELETE /admin/faqs/{faq}` - Delete FAQ
- `POST /admin/faqs/reorder` - Reorder FAQs
- `PATCH /admin/faqs/{faq}/toggle` - Toggle FAQ visibility

---

## Resident APIs

**Access:** All routes require `resident.role` middleware

### Dashboard
- `GET /resident/dashboard` - Resident dashboard

### Blotter Requests
- `GET /resident/request-blotter` - Request blotter form
- `POST /resident/request-blotter` - Submit blotter request (Rate limit: 10/5min)

### Community Concerns
- `GET /resident/request-community-concern` - Request concern form
- `POST /resident/request-community-concern` - Submit concern (Rate limit: 10/5min)
- `GET /resident/community-concerns/{id}/details` - Get concern details (AJAX)

### Document Requests
- `GET /resident/request-document` - Request document form
- `POST /resident/request-document` - Submit document request (Rate limit: 10/5min)
- `GET /resident/templates/{template}/form-config` - Get template form config (AJAX)

### My Requests
- `GET /resident/my-requests` - List all resident requests

### Notifications
- `GET /resident/notifications` - View notifications
- `GET /resident/notifications/count` - Get notification count (AJAX, polling every 30s)
- `POST /resident/notifications/mark-all` - Mark all as read
- `POST /resident/notifications/mark-as-read/{id}` - Mark specific notification as read

### Profile
- `GET /resident/profile` - View resident profile
- `PUT /resident/profile/update` - Update resident profile

### Announcements
- `GET /resident/announcements` - View announcements/community bulletin
- `GET /resident/announcements/project/{id}` - View project announcement
- `GET /resident/announcements/activity/{id}` - View activity announcement

### FAQs
- `GET /resident/faqs` - View FAQs

### QR Code
- `GET /resident/qr-code` - View resident QR code
- `GET /resident/qr-code/download` - Download QR code

### Resident Search
- `GET /resident/search/residents` - Search residents (for selecting respondents in blotter)

---

## Python Analytics Service APIs

**Base URL:** Configurable via `PYTHON_ANALYTICS_URL` (default: `http://localhost:5000`)

### Health Check
- `GET /health` - Service health check

### Clustering
- `POST /api/clustering/kmeans` - Perform K-Means clustering
  - **Request Body:**
    ```json
    {
      "samples": [[1, 2], [2, 3], ...],
      "k": 3,
      "max_iterations": 100,
      "num_runs": 3
    }
    ```
  - **Response:** Clustering results with labels and centroids

- `POST /api/clustering/optimal-k` - Find optimal K value
  - **Request Body:**
    ```json
    {
      "samples": [[1, 2], [2, 3], ...],
      "max_k": 10,
      "method": "elbow"
    }
    ```
  - **Response:** Optimal K value with metrics

- `POST /api/clustering/hierarchical` - Perform hierarchical clustering
  - **Request Body:**
    ```json
    {
      "samples": [[1, 2], [2, 3], ...],
      "n_clusters": 3,
      "linkage": "ward"
    }
    ```
  - **Response:** Hierarchical clustering results

**Note:** All clustering endpoints are called internally by Laravel via `PythonAnalyticsService` class.

---

## AJAX/Internal API Endpoints

These endpoints are primarily used by frontend JavaScript for dynamic content loading and real-time updates.

### Notification Polling
- `GET /admin/notifications/count` - Polled every 30 seconds for notification counts
- `GET /resident/notifications/count` - Polled every 30 seconds for notification counts

### Real-time Status Checks
- `GET /admin/blotter-reports/{id}/check-active` - Check if resident account is active
- `GET /admin/document-requests/{id}/check-active` - Check if resident account is active

### Search & Autocomplete
- `GET /admin/search/residents` - Resident search with autocomplete
- `GET /resident/search/residents` - Resident search (for blotter respondents)
- `GET /admin/health/patient-search` - Patient search for medical records

### Dynamic Content Loading
- `GET /admin/blotter-reports/{id}/details` - Load blotter report details in modal
- `GET /admin/community-concerns/{id}/details` - Load community concern details in modal
- `GET /admin/document-requests/{id}/details` - Load document request details in modal
- `GET /resident/community-concerns/{id}/details` - Load concern details in modal
- `GET /admin/templates/{template}/form-config` - Load dynamic form configuration
- `GET /resident/templates/{template}/form-config` - Load dynamic form configuration

### Clustering Analytics
- `GET /admin/clustering/optimal-k` - Calculate optimal K value (AJAX)
- `GET /admin/clustering/stats` - Get cluster statistics (AJAX)

### Email Validation
- `GET /admin/residents/check-email` - Check email availability

---

## API Authentication & Authorization

### Middleware
- **Rate Limiting:** Various endpoints have rate limiting middleware
  - `rate.limit:X,Y` - X requests per Y minutes
  - `login.rate.limit` - Special rate limit for login attempts
- **Input Sanitization:** `input.sanitize` middleware on POST/PUT routes
- **Role-Based Access:**
  - `admin.role:role1,role2` - Restrict to specific admin roles
  - `admin.secretary` - Secretary-only access
  - `resident.role` - Resident-only access
  - `2fa:action` - Requires 2FA verification for sensitive actions

### CSRF Protection
All POST/PUT/DELETE requests require CSRF token:
- Token available via `<meta name="csrf-token">` tag
- Include in headers: `X-CSRF-TOKEN`

---

## Rate Limits

| Endpoint Category | Rate Limit |
|------------------|------------|
| Account Contact | 30 requests/minute |
| Password Reset | 10 requests/15 minutes |
| Password Update | 5 requests/15 minutes |
| Registration | 10 requests/15 minutes |
| Resident/Barangay Profile Updates | 20 requests/minute |
| Blotter/Concern/Document Requests | 10 requests/5 minutes |

---

## Response Formats

### Success Response (JSON)
```json
{
  "success": true,
  "message": "Operation completed",
  "data": {...}
}
```

### Error Response (JSON)
```json
{
  "success": false,
  "error": "Error message",
  "errors": {
    "field": ["Validation error"]
  }
}
```

### Notification Response
```json
{
  "total": 5,
  "total_unread": 3,
  "total_read": 2,
  "notifications": [...]
}
```

---

## Notes

1. **Python Analytics Service:** The Flask service runs separately and is called internally by Laravel. It's not directly accessible from the frontend.

2. **Polling Intervals:** Notification endpoints are polled every 30 seconds by the frontend JavaScript.

3. **2FA Protection:** Certain sensitive operations (edit/delete residents) require 2FA verification even if the user is already logged in.

4. **File Uploads:** Document template uploads (Word/DOCX) use multipart/form-data.

5. **PDF Generation:** Document request PDFs are generated on-demand via `/admin/document-requests/{id}/pdf`.

6. **QR Code System:** QR codes are used for attendance tracking and resident verification.

---

## Version Information

- **Laravel Framework:** Based on Laravel structure
- **Python Analytics Service:** Flask-based microservice
- **Last Updated:** Generated from codebase scan

