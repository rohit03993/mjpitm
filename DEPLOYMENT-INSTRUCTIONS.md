# Deployment Instructions - Courses & Categories Migration

## ⚠️ Important: Database Data vs Code

**Git only contains CODE, NOT database data.**

When you push to Git and pull on the server:
- ✅ All code changes will be updated
- ✅ All migrations will be available
- ❌ **Courses and Categories will NOT come with Git** (they're in the database)

## 📋 Step-by-Step Deployment Process

### Step 1: Export Courses & Categories from Local

Run this command in your local project:

```bash
cd crm
php export-courses-categories.php
```

This will create a file like: `courses-categories-export-2025-12-07_143022.sql`

### Step 2: Push Code to Git

```bash
git add .
git commit -m "Add notification system and unified student view"
git push origin main
```

### Step 3: On Server - Pull Code

```bash
cd /path/to/your/project/crm
git pull origin main
```

### Step 4: On Server - Run Migrations

```bash
php artisan migrate
```

This will create the tables (if they don't exist) and add any new columns.

### Step 5: On Server - Import Courses & Categories

**Option A: Using MySQL Command Line**
```bash
mysql -u [your_db_user] -p [your_database_name] < courses-categories-export-2025-12-07_143022.sql
```

**Option B: Using phpMyAdmin**
1. Open phpMyAdmin
2. Select your database
3. Click "Import" tab
4. Choose the SQL file
5. Click "Go"

**Option C: Using Laravel Tinker (if you prefer)**
```bash
php artisan tinker
```
Then manually import or use the Excel import feature.

### Step 6: Copy Images (if any)

If you have category/course images, copy them to the server:

```bash
# From local
scp -r storage/app/public/categories user@server:/path/to/project/crm/storage/app/public/
scp -r storage/app/public/courses user@server:/path/to/project/crm/storage/app/public/
```

Or use FTP/SFTP to upload the `storage/app/public/categories` and `storage/app/public/courses` folders.

### Step 7: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## 🔄 Alternative: Use Excel Import on Server

If you prefer to use the Excel import feature on the server:

1. Pull the code (Step 3)
2. Run migrations (Step 4)
3. Upload your Excel file via the admin panel: `/admin/courses/import`
4. Map the fields and import

**Note:** This will recreate all courses and categories, so make sure your Excel file is up-to-date.

## ✅ Verification Checklist

After deployment, verify:

- [ ] All migrations ran successfully
- [ ] Courses are visible in `/admin/courses`
- [ ] Categories are visible in `/admin/categories`
- [ ] Public course pages show courses correctly
- [ ] Images are displaying (if you uploaded them)
- [ ] Notification system is working
- [ ] Student registration forms are working

## 🆘 Troubleshooting

**If courses/categories are missing:**
- Check database connection on server
- Verify the SQL import was successful
- Check if institute IDs match between local and server

**If images are missing:**
- Run `php artisan storage:link` on server
- Verify images were copied to `storage/app/public/`
- Check file permissions

**If you get errors:**
- Check Laravel logs: `storage/logs/laravel.log`
- Verify `.env` file is configured correctly
- Ensure all dependencies are installed: `composer install`

