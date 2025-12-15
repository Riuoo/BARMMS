# Terminology Review - Adjustment Table

This document lists all UI labels and user-facing text that need to be adjusted for formality and brevity.

## Page Titles & Headings

| Current | Replacement | Location | Reason |
|---------|-------------|----------|--------|
| Create New Blotter Report | Create Blotter Report | `create_blotter_report.blade.php` | Remove redundant "New" |
| New Medicine Request | Create Medicine Request | `medicine-requests/create.blade.php` | More formal, consistent |
| Add New Resident | Add Resident | `residents.blade.php` | Remove redundant "New" |
| Project Details | Project Details | `show_accomplished_project.blade.php` | Keep as is |
| Health Center Activity Details | Activity Details | `show.blade.php` | Remove redundant "Health Center" |
| Medicine Dispense Report | Dispensing Report | `medicines/report.blade.php` | More concise |
| Medicine Transactions | Medicine Transactions | `medicine-transactions/index.blade.php` | Keep as is |
| Resident Information | Residents | `residents.blade.php` | More concise for list page |
| Welcome back, [Name] | Dashboard | `main/dashboard.blade.php` | More formal |
| Here's what's happening in your barangay today | Overview | `main/dashboard.blade.php` | More formal, concise |

## Section Headings

| Current | Replacement | Location | Reason |
|---------|-------------|----------|--------|
| Complainant Information | Complainant | `create_blotter_report.blade.php` | Remove redundant word |
| Respondent Information | Respondent | `create_blotter_report.blade.php` | Remove redundant word |
| Supporting Documents | Attachments | `create_blotter_report.blade.php` | More concise |
| Summon Information | Summon Details | `create_blotter_report.blade.php` | More formal |
| Incident Details | Incident Details | `create_blotter_report.blade.php` | Keep as is |
| Project Information | Project Information | `show_accomplished_project.blade.php` | Keep as is |
| Activity Information | Activity Information | `show.blade.php` | Keep as is |
| Impact & Beneficiaries | Impact & Beneficiaries | `show_accomplished_project.blade.php` | Keep as is |
| Funding Details | Funding Details | `show_accomplished_project.blade.php` | Keep as is |
| Objectives & Resources | Objectives & Resources | `show.blade.php` | Keep as is |
| Additional Notes | Notes | `show.blade.php` | More concise |

## Button Labels

| Current | Replacement | Location | Reason |
|---------|-------------|----------|--------|
| Create | Create | Various | Keep as is |
| Add New Resident | Add Resident | `residents.blade.php` | Remove redundant "New" |
| Edit Project | Edit Project | `show_accomplished_project.blade.php` | Keep as is |
| Edit Activity | Edit Activity | `show.blade.php` | Keep as is |
| Delete Project | Delete Project | `show_accomplished_project.blade.php` | Keep as is |
| Delete Activity | Delete Activity | `show.blade.php` | Keep as is |
| Feature Project | Feature Project | `show_accomplished_project.blade.php` | Keep as is |
| Unfeature Project | Remove Feature | `show_accomplished_project.blade.php` | More formal |
| Feature Activity | Feature Activity | `show.blade.php` | Keep as is |
| Unfeature Activity | Remove Feature | `show.blade.php` | More formal |
| Back to Projects | Back to Projects | `show_accomplished_project.blade.php` | Keep as is |
| Back to Activities | Back to Activities | `show.blade.php` | Keep as is |
| View all | View All | `main/dashboard.blade.php` | Capitalize for consistency |
| Manage | Manage | `main/dashboard.blade.php` | Keep as is |
| View | View | `main/dashboard.blade.php` | Keep as is |
| Track status | View Status | `main/dashboard.blade.php` | More formal |

## Descriptive Text

| Current | Replacement | Location | Reason |
|---------|-------------|----------|--------|
| Submit an incident report for barangay resolution | File incident report | `create_blotter_report.blade.php` | More concise |
| Create a dispensing request for a resident | Create medicine request | `medicine-requests/create.blade.php` | More concise |
| Manage resident profiles and information | Manage residents | `residents.blade.php` | More concise |
| Manage and track community concerns from residents | Manage community concerns | `community-concerns.blade.php` | Remove redundant "track" |
| Track stock movements and dispensing | Stock movements | `medicine-transactions/index.blade.php` | More concise |
| Analyze medicine requests and dispensing trends | Medicine request analysis | `medicines/report.blade.php` | More concise |
| View detailed information about this accomplished project | View project details | `show_accomplished_project.blade.php` | More concise |
| View detailed information about this health center activity | View activity details | `show.blade.php` | More concise |

## Chart & Report Labels

| Current | Replacement | Location | Reason |
|---------|-------------|----------|--------|
| Top Requested by Purok (People) | Top Requests by Purok | `medicines/report.blade.php` | Remove redundant parentheses |
| Overall Top Requested | Most Requested | `medicines/report.blade.php` | More concise |
| Requests by Age Bracket | Requests by Age | `medicines/report.blade.php` | More concise |
| Top Dispensed (30 days) | Top Dispensed | `medicines/report.blade.php` | Remove redundant time frame |
| Monthly Dispensed | Monthly Dispensing | `medicines/report.blade.php` | More formal |
| Category Distribution | Category Distribution | `medicines/report.blade.php` | Keep as is |

## Navigation & Menu Items

| Current | Replacement | Location | Reason |
|---------|-------------|----------|--------|
| User management | User Management | `main/layout.blade.php` | Capitalize for consistency |
| Reports & Requests | Reports & Requests | `main/layout.blade.php` | Keep as is |
| Resident Information | Residents | `main/layout.blade.php` | More concise |
| Account Requests | Account Requests | `main/layout.blade.php` | Keep as is (remove "New" from route) |
| Barangay Activities & Projects | Projects & Activities | `main/layout.blade.php` | More concise |
| Health Management | Health Management | `main/layout.blade.php` | Keep as is |
| Health Center Activities | Health Activities | `main/layout.blade.php` | More concise |

## Status Labels

| Current | Replacement | Location | Reason |
|---------|-------------|----------|--------|
| Under Review | Under Review | Various | Keep as is |
| In Progress | In Progress | Various | Keep as is |
| Pending | Pending | Various | Keep as is |
| Resolved | Resolved | Various | Keep as is |
| Closed | Closed | Various | Keep as is |
| Active | Active | Various | Keep as is |
| Inactive | Inactive | Various | Keep as is |
| Approved | Approved | Various | Keep as is |
| Rejected | Rejected | Various | Keep as is |
| Completed | Completed | Various | Keep as is |

## Form Labels & Placeholders

| Current | Replacement | Location | Reason |
|---------|-------------|----------|--------|
| Complainant Name | Complainant | `create_blotter_report.blade.php` | More concise |
| Respondent (Registered Resident) | Respondent | `create_blotter_report.blade.php` | Remove redundant text |
| Report Type | Type | `create_blotter_report.blade.php` | More concise |
| Detailed Description | Description | `create_blotter_report.blade.php` | Remove redundant word |
| Attach Evidence (Optional) | Attachments (Optional) | `create_blotter_report.blade.php` | More formal |
| Summon Date | Summon Date | `create_blotter_report.blade.php` | Keep as is |
| Search by submitted by (resident name) or location | Search by resident or location | `community-concerns.blade.php` | More concise |
| Search medicine name... | Search medicine... | `medicine-transactions/index.blade.php` | More concise |

## Table Headers

| Current | Replacement | Location | Reason |
|---------|-------------|----------|--------|
| Date | Date | Various | Keep as is |
| Medicine | Medicine | Various | Keep as is |
| Type | Type | Various | Keep as is |
| Qty | Quantity | `medicine-transactions/index.blade.php` | More formal |
| Resident | Resident | Various | Keep as is |
| Notes | Notes | Various | Keep as is |

## Summary Statistics Labels

| Current | Replacement | Location | Reason |
|---------|-------------|----------|--------|
| Total Residents | Total Residents | `main/dashboard.blade.php` | Keep as is |
| Account Requests | Account Requests | `main/dashboard.blade.php` | Keep as is |
| Blotter Reports | Blotter Reports | `main/dashboard.blade.php` | Keep as is |
| Document Requests | Document Requests | `main/dashboard.blade.php` | Keep as is |
| Total In | Total In | `medicine-transactions/index.blade.php` | Keep as is |
| Total Out | Total Out | `medicine-transactions/index.blade.php` | Keep as is |
| Adjustments | Adjustments | `medicine-transactions/index.blade.php` | Keep as is |
| Expired | Expired | `medicine-transactions/index.blade.php` | Keep as is |

## Action Messages

| Current | Replacement | Location | Reason |
|---------|-------------|----------|--------|
| The report will be created and can be managed from the blotter reports list | Report will be created and available in reports list | `create_blotter_report.blade.php` | More concise |
| No selection yet | No selection | `create_blotter_report.blade.php` | More concise |
| No resident selected yet | No resident selected | `create_blotter_report.blade.php` | More concise |
| Selected: [Name] | [Name] | `create_blotter_report.blade.php` | More concise |
| Custom Name: [Name] | [Name] (Custom) | `create_blotter_report.blade.php` | More concise |
| Selected: [Name] (Registered Resident) | [Name] (Registered) | `create_blotter_report.blade.php` | More concise |

## Notes

- All changes focus on making terminology more formal and concise
- Redundant words like "New", "Information", "Details" are removed where context is clear
- Inconsistent capitalization is standardized
- Informal phrases are replaced with formal alternatives
- Parentheses and redundant explanations are removed where possible

