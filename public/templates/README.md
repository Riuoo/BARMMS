# Base Template Setup

To create the base template for Word document editing:

1. Open Microsoft Word
2. Create a new document with the following content:

```
REPUBLIC OF THE PHILIPPINES
Province of [province_name]
Municipality of [municipality_name]
BARANGAY [barangay_name]

OFFICE OF THE BARANGAY CHAIRMAN

CERTIFICATION

TO WHOM IT MAY CONCERN:

This is to certify that [resident_name], of legal age, Filipino, and a resident of [resident_address], Barangay [barangay_name], [municipality_name], [province_name], is known to me as a person of good moral character and law-abiding citizen of this Barangay.

This certification is being issued upon the request of the above-named person for [document_purpose].

Issued this [current_date] at the Office of the Barangay Chairman, Barangay [barangay_name], [municipality_name], [province_name].


[official_name]
[official_position]
```

3. Format the document as needed (fonts, spacing, alignment)
4. Save the file as `base_template.rtf` in this directory (use RTF format for maximum compatibility)
5. The system will use this as the default template for new document types

## Available Placeholders

- `[resident_name]` - Resident's full name
- `[resident_address]` - Resident's address
- `[current_date]` - Current date
- `[barangay_name]` - Barangay name
- `[municipality_name]` - Municipality name
- `[province_name]` - Province name
- `[document_purpose]` - Purpose of the document
- `[official_name]` - Name of the signing official
- `[official_position]` - Position of the signing official

## Notes

- Use square brackets `[]` for placeholders
- Format placeholders with the same styling as surrounding text
- The system will replace placeholders with actual data when generating documents
- You can add more placeholders as needed
- RTF format provides maximum compatibility with Microsoft Word 