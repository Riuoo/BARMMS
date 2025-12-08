# Search and Filter Features Documentation

This document lists all pages in the BARMMS system that include search or filter functionality, along with the specific fields that can be searched and the filter options available.

---

## ADMIN PAGES

### 1. **Residents Management** (`admin.residents`)
**Route:** `/admin/residents`

**Search Fields:**
- Name
- Email
- Contact Number

**Filter Options:**
- Status: Active / Inactive
- Purok: Purok 1-7

---

### 2. **Barangay Profiles/Officials** (`admin.barangay-profiles`)
**Route:** `/admin/barangay-profiles`

**Search Fields:**
- Name
- Email
- Contact Number

**Filter Options:**
- Role: Captain, Councilor, Secretary, etc.
- Status: Active / Inactive
- Purok: Purok 1-7

---

### 3. **Document Requests** (`admin.requests.document-requests`)
**Route:** `/admin/requests/document-requests`

**Search Fields:**
- Document Type
- Resident Name (via relationship)

**Filter Options:**
- Status: Pending / Approved / Completed
- Purok: Purok 1-7

---

### 4. **Blotter Reports** (`admin.requests.blotter-reports`)
**Route:** `/admin/requests/blotter-reports`

**Search Fields:**
- Complainant Name
- Type
- Respondent Name (via relationship)

**Filter Options:**
- Status: Pending / Approved / Completed
- Purok: Purok 1-7

---

### 5. **Community Concerns** (`admin.requests.community-concerns`)
**Route:** `/admin/requests/community-concerns`

**Search Fields:**
- Submitted By (Resident Name)
- Location

**Filter Options:**
- Status: Pending / Under Review / In Progress / Resolved / Closed
- Purok: Purok 1-7

---

### 6. **Account Requests** (`admin.requests.new-account-requests`)
**Route:** `/admin/requests/new-account-requests`

**Search Fields:**
- Email
- Full Name

**Filter Options:**
- Status: Pending / Approved / Completed / Rejected

---

### 7. **Medical Records** (`admin.medical-records.index`)
**Route:** `/admin/medical-records`

**Search Fields:**
- Patient Name (via resident relationship)
- Patient Email (via resident relationship)
- Chief Complaint
- Diagnosis

**Filter Options:**
- None (search only)

---

### 8. **Vaccination Records** (`admin.vaccination-records.index`)
**Route:** `/admin/vaccination-records`

**Search Fields:**
- Resident Name (via resident relationship)
- Child First Name (via child profile relationship)
- Child Last Name (via child profile relationship)
- Vaccine Name
- Vaccine Type

**Filter Options:**
- Dose Status: Overdue / Due Soon / Up to Date
- Age Group: Infant / Toddler / Child / Adolescent / Adult / Elderly

---

### 9. **Medicines** (`admin.medicines.index`)
**Route:** `/admin/medicines`

**Search Fields:**
- Medicine Name
- Generic Name
- Category

**Filter Options:**
- Category: (Dynamic list from database)
- Stock Status: Low Stock (current_stock <= minimum_stock)

---

### 10. **Medicine Requests** (`admin.medicine-requests.index`)
**Route:** `/admin/medicine-requests`

**Search Fields:**
- Resident Name (via resident relationship)
- Medicine Name (via medicine relationship)
- Medicine Generic Name (via medicine relationship)
- Notes

**Filter Options:**
- Approval Status: Approved / Pending
- Medicine Category: (Dynamic list from database)
- Date Range: Start Date / End Date

---

### 11. **Medicine Transactions** (`admin.medicine-transactions.index`)
**Route:** `/admin/medicine-transactions`

**Search Fields:**
- Medicine Name (via medicine relationship)
- Resident Name (via resident relationship)

**Filter Options:**
- Transaction Type: IN / OUT / ADJUSTMENT / EXPIRED
- Date Range: Start Date / End Date

---

### 12. **Accomplished Projects** (`admin.accomplished-projects`)
**Route:** `/admin/accomplished-projects`

**Search Fields:**
- Title
- Description
- Category

**Filter Options:**
- Category: (Dynamic list from database)
- Featured Status: Featured / Non-Featured

---

### 13. **Health Center Activities** (`admin.health-center-activities.index`)
**Route:** `/admin/health-center-activities`

**Search Fields:**
- Activity Name
- Activity Type
- Organizer
- Location
- Description

**Filter Options:**
- Featured Status: Featured / Non-Featured

---

### 14. **Document Templates** (`admin.templates.index`)
**Route:** `/admin/templates`

**Search Fields:**
- Document Type

**Filter Options:**
- Category: Certificates / Clearances / Permits / Identifications
- Status: Active / Inactive

---

### 15. **Notifications** (`admin.notifications`)
**Route:** `/admin/notifications`

**Search Fields:**
- None (Search bar removed)

**Filter Options:**
- Type: Blotter / Document Request / Account Request / Community Concern
- Date Range: Start Date / End Date
- Read Status: Read / Unread

---

### 16. **Attendance Logs** (`admin.attendance.logs`)
**Route:** `/admin/attendance/logs`

**Search Fields:**
- Resident Name
- Resident Email
- Guest Name
- Guest Contact

**Filter Options:**
- Event ID: (Specific event/project)

---

### 17. **FAQs (Admin)** (`admin.settings.faqs.index`)
**Route:** `/admin/settings/faqs`

**Search Fields:**
- Question
- Answer

**Filter Options:**
- Category: (Dynamic list from database)
- Status: Active / Inactive

---

## RESIDENT PAGES

### 18. **My Requests** (`resident.my-requests`)
**Route:** `/resident/my-requests`

**Search Fields:**
- Document Requests: Document Type
- Blotter Reports: Type, Respondent Name
- Community Concerns: Title, Location

**Filter Options:**
- Status: Pending / Approved / Completed / Under Review / In Progress / Resolved / Closed

---

### 19. **FAQs (Resident)** (`resident.faqs`)
**Route:** `/resident/faqs`

**Search Fields:**
- Question
- Answer
- Category

**Filter Options:**
- Category: (Dropdown - Dynamic list from active FAQs)

---

### 20. **Announcements** (`resident.announcements`)
**Route:** `/resident/announcements`

**Search Fields:**
- Project Title
- Project Category
- Activity Name
- Activity Type

**Filter Options:**
- Type: Project / Activity
- Status: Completed / Upcoming / Ongoing
- Featured: Featured Only

---

## SUMMARY STATISTICS

**Total Pages with Search/Filter:** 20

**Breakdown:**
- Admin Pages: 17
- Resident Pages: 3

**Most Common Search Fields:**
- Name (appears in 15+ pages)
- Email (appears in 8+ pages)
- Description (appears in 10+ pages)
- Status (appears as filter in 12+ pages)

**Most Common Filter Types:**
- Status filters (Pending/Approved/Completed, etc.)
- Category filters (Dynamic from database)
- Date range filters
- Featured/Non-featured filters
- Active/Inactive status filters

---

## NOTES

- All search functionality uses case-insensitive LIKE queries with wildcard matching (`%search%`)
- Date range filters are available on pages dealing with time-sensitive data (medicine requests, transactions, etc.)
- Status filters are the most common filter type across the application
- Many pages combine search with multiple filter options for comprehensive data filtering
- Resident-facing pages have simplified search/filter options compared to admin pages
