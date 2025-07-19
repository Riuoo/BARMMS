# Text Template Editing Guide for Non-Technical Staff

## Overview
This guide explains how to edit document templates using any text editor, making it easy for non-technical staff to customize templates without needing to understand HTML or web editing.

## How to Edit Templates in Text Editor

### Step 1: Download the Template
1. Go to **Admin Dashboard** → **Document Templates**
2. Find the template you want to edit
3. Click the **"Text"** button next to the template
4. The template will download as a `.txt` file

### Step 2: Edit in Text Editor
1. Open the downloaded file in any text editor (Notepad, WordPad, etc.)
2. You'll see the template divided into sections:
   - **=== HEADER SECTION ===** - Edit the header content
   - **=== BODY SECTION ===** - Edit the main content
   - **=== FOOTER SECTION ===** - Edit the footer content
   - **=== INSTRUCTIONS ===** - Read-only instructions

3. **Edit the content** in each section as needed
4. **Use placeholders** like `[resident_name]`, `[address]`, etc. for dynamic information
5. **Save the file** when you're done editing

### Step 3: Upload the Edited Template
1. Go back to the template edit page
2. Click **"Upload Text Template"**
3. Select your edited text file
4. Click **"Upload and Update Template"**
5. The system will automatically update the template with your changes

## Available Placeholders

You can use these placeholders in your templates:

- `[resident_name]` - Name of the resident
- `[resident_address]` - Address of the resident
- `[civil_status]` - Civil status (Single, Married, etc.)
- `[purpose]` - Purpose of the document
- `[day]` - Day of issuance
- `[month]` - Month of issuance
- `[year]` - Year of issuance
- `[barangay_name]` - Name of the barangay
- `[municipality_name]` - Name of the municipality
- `[province_name]` - Name of the province
- `[official_name]` - Name of the barangay captain/official

## Tips for Text Editing

### Formatting
- Use simple text formatting
- Use proper paragraph spacing with line breaks
- Keep formatting simple and professional
- Avoid complex formatting that might not transfer well

### Content Guidelines
- Write clear, professional language
- Use proper grammar and spelling
- Keep sentences concise
- Maintain official tone appropriate for government documents

### Placeholder Usage
- Placeholders are case-sensitive: use `[resident_name]` not `[Resident_Name]`
- Don't add extra spaces around placeholders
- You can use the same placeholder multiple times
- Placeholders will be automatically replaced with actual data when documents are generated

## Troubleshooting

### File Won't Upload
- Make sure the file is in `.txt` format
- Check that the file size is under 10MB
- Ensure the file isn't corrupted

### Content Not Updating
- Make sure you saved the text file before uploading
- Check that you edited the correct sections
- Verify the file format is supported

### Placeholders Not Working
- Check spelling and case of placeholders
- Make sure there are no extra spaces
- Verify the placeholder is in the list above

## Support

If you encounter any issues:
1. Try downloading a fresh template and editing it
2. Check the instructions section in the Word document
3. Contact your system administrator for technical support

## Example Template Structure

```
=== HEADER SECTION ===
Republic of the Philippines
Province of [province_name]
Municipality of [municipality_name]
BARANGAY CLEARANCE

=== BODY SECTION ===
TO WHOM IT MAY CONCERN:

This is to certify that [resident_name], of legal age, [civil_status], 
Filipino, and a resident of [resident_address], has no pending case/s 
or record on file at the Office of the Barangay.

This certification is being issued upon the request of the above-named 
person for [purpose].

Issued this [day] day of [month] [year] at Barangay [barangay_name], 
[municipality_name], [province_name], Philippines.

=== FOOTER SECTION ===
_________________________
[official_name]
Barangay Captain
```

This structure will create a professional-looking document that automatically fills in the resident's information when generated. 