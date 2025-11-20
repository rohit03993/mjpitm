# Registration Form Setup Guide

## Overview
The registration form download system allows students and staff to download the student registration form PDF.

## Setup Instructions

### Step 1: Upload the Registration Form PDF

1. Place your registration form PDF file in the following location:
   ```
   crm/storage/app/public/documents/registration-form.pdf
   ```

2. If the `documents` folder doesn't exist, it will be created automatically.

3. **Important**: Make sure the file is named exactly `registration-form.pdf`

### Step 2: Create Storage Link (if not already done)

Run this command to create a symbolic link for public access:
```bash
php artisan storage:link
```

### Step 3: Verify File Access

After uploading the PDF, you can access it via:
- **Public URL**: `http://your-domain.com/storage/documents/registration-form.pdf`
- **Download Route**: `http://your-domain.com/download/registration-form`
- **View Route**: `http://your-domain.com/registration-form`

## Access Points

### For Students:
- **Student Dashboard** → Quick Actions → "Registration Form" button
- **Direct Link**: `/registration-form`
- **Download Link**: `/download/registration-form`

### For Staff:
- **Staff Dashboard** → Quick Actions → "Registration Form" button
- **Direct Link**: `/registration-form`
- **Download Link**: `/download/registration-form`

### For Admin:
- **Admin Dashboard** → Quick Actions → "Registration Form" button
- **Direct Link**: `/registration-form`
- **Download Link**: `/download/registration-form`

### Public Access:
- Anyone can access: `/registration-form` (public page)
- Download link is also public: `/download/registration-form`

## Features

1. **Download PDF**: Direct download of the registration form
2. **View in Browser**: View the PDF inline in the browser
3. **Public Access**: Students don't need to login to download
4. **Staff Sharing**: Staff can easily share the link with students

## File Structure

```
crm/
├── storage/
│   └── app/
│       └── public/
│           └── documents/
│               └── registration-form.pdf  ← Place your PDF here
├── app/
│   └── Http/
│       └── Controllers/
│           └── DocumentsController.php
└── resources/
    └── views/
        └── documents/
            └── registration-form.blade.php
```

## Troubleshooting

### File Not Found Error
If you see "Registration form is not available":
1. Check if the file exists at: `storage/app/public/documents/registration-form.pdf`
2. Verify the file name is exactly `registration-form.pdf`
3. Check file permissions (should be readable)

### Download Not Working
1. Run `php artisan storage:link` to create the symbolic link
2. Check if `public/storage` directory exists and is linked correctly
3. Verify file permissions

### Permission Issues
On Linux/Mac, you may need to set proper permissions:
```bash
chmod 755 storage/app/public/documents
chmod 644 storage/app/public/documents/registration-form.pdf
```

## Notes

- The system checks if the file exists before allowing download
- If file doesn't exist, users see a friendly error message
- The form page is accessible to everyone (public route)
- Download functionality works for both authenticated and guest users

