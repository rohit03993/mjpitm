# Local Development Setup Guide

## 🎯 Goal
Set up local development environment to view both websites (Tech Institute and Paramedical Institute) side by side using the same backend.

---

## 📋 Step-by-Step Setup

### Step 1: Configure Hosts File (Windows)

1. **Open Notepad as Administrator:**
   - Press `Win + X` → Select "Windows PowerShell (Admin)" or "Terminal (Admin)"
   - Or right-click Notepad → "Run as administrator"

2. **Open hosts file:**
   - In Notepad: File → Open
   - Navigate to: `C:\Windows\System32\drivers\etc\`
   - Change file type filter to "All Files (*.*)"
   - Open `hosts` file

3. **Add local domains:**
   - Scroll to the bottom of the file
   - Add these lines:
     ```
     127.0.0.1   mjpitm.local
     127.0.0.1   mjpips.local
     ```

4. **Save the file:**
   - File → Save
   - Close Notepad

5. **Flush DNS cache (optional but recommended):**
   - Open PowerShell as Administrator
   - Run: `ipconfig /flushdns`

---

### Step 2: Verify Database Setup

1. **Check if institutes exist in database:**
   ```powershell
   cd "E:\Softwares DEV- Chiki\Result Management system\crm"
   php artisan tinker
   ```

2. **In tinker, run:**
   ```php
   \App\Models\Institute::all();
   ```

3. **Expected output:**
   - Institute ID 1: `mjpitm.in` (Tech Institute)
   - Institute ID 2: `mjpips.in` (Paramedical Institute)

4. **If institutes don't exist or domains are wrong, run:**
   ```powershell
   php artisan db:seed --class=InstituteSeeder
   ```

5. **Exit tinker:**
   - Type `exit` and press Enter

---

### Step 3: Start Development Servers

You need **TWO terminal windows** running simultaneously:

#### Terminal 1: Laravel Server
```powershell
cd "E:\Softwares DEV- Chiki\Result Management system\crm"
php artisan serve --host=127.0.0.1 --port=8000
```

#### Terminal 2: Vite (Frontend Assets)
```powershell
cd "E:\Softwares DEV- Chiki\Result Management system\crm"
npm run dev
```

**Keep both terminals running!**

---

### Step 4: Access Both Websites

Open your browser and visit:

#### Tech Institute (mjpitm.local)
- **URL:** `http://mjpitm.local:8000`
- **Expected:** Blue-themed landing page for Tech Institute
- **Institute ID:** 1

#### Paramedical Institute (mjpips.local)
- **URL:** `http://mjpips.local:8000`
- **Expected:** Green-themed landing page for Paramedical Institute
- **Institute ID:** 2

---

## ✅ Verification Checklist

- [ ] Hosts file updated with both domains
- [ ] DNS cache flushed (optional)
- [ ] Database has both institutes with correct domains
- [ ] Laravel server running on port 8000
- [ ] Vite dev server running
- [ ] Tech Institute accessible at `http://mjpitm.local:8000`
- [ ] Paramedical Institute accessible at `http://mjpips.local:8000`
- [ ] Both websites show different themes (blue vs green)
- [ ] Both websites show correct institute information

---

## 🔧 Troubleshooting

### Issue: Domain not resolving
**Solution:**
1. Verify hosts file entries are correct
2. Flush DNS cache: `ipconfig /flushdns`
3. Restart browser
4. Try accessing with IP: `http://127.0.0.1:8000?institute_id=1`

### Issue: Port already in use
**Solution:**
```powershell
# Change port in Laravel serve command
php artisan serve --host=127.0.0.1 --port=8001
# Then access: http://mjpitm.local:8001
```

### Issue: Wrong institute showing
**Solution:**
1. Check database: `php artisan tinker`
2. Verify domains: `\App\Models\Institute::all();`
3. Check middleware: `app/Http/Middleware/DetectInstitute.php`
4. Clear cache: `php artisan config:clear && php artisan cache:clear`

### Issue: Assets not loading
**Solution:**
1. Make sure `npm run dev` is running
2. Clear browser cache (Ctrl + F5)
3. Check browser console for errors
4. Verify Vite is running on correct port

### Issue: Database connection error
**Solution:**
1. Check `.env` file for database credentials
2. Verify database exists
3. Run migrations: `php artisan migrate`
4. Run seeders: `php artisan db:seed`

---

## 🚀 Quick Start Commands

```powershell
# 1. Navigate to project
cd "E:\Softwares DEV- Chiki\Result Management system\crm"

# 2. Check database (optional)
php artisan tinker
# Then: \App\Models\Institute::all();
# Exit: exit

# 3. Start Laravel server (Terminal 1)
php artisan serve --host=127.0.0.1 --port=8000

# 4. Start Vite server (Terminal 2)
npm run dev

# 5. Access websites:
# - http://mjpitm.local:8000 (Tech Institute)
# - http://mjpips.local:8000 (Paramedical Institute)
```

---

## 📝 Notes

- **Both websites use the same backend** - all data is stored in one database
- **Institute isolation** - Data is separated by `institute_id` in the database
- **Domain-based routing** - The middleware detects the domain and sets the institute automatically
- **Local development only** - The `.local` domains only work on your computer
- **Production domains** - `mjpitm.in` and `mjpips.in` continue to work normally on the live server

---

## 🎨 Testing Both Websites Side by Side

1. **Open two browser windows side by side:**
   - Window 1: `http://mjpitm.local:8000` (Tech Institute - Blue theme)
   - Window 2: `http://mjpips.local:8000` (Paramedical Institute - Green theme)

2. **Verify differences:**
   - Different color themes
   - Different institute names
   - Different course listings (once courses are added)
   - Different content

3. **Test navigation:**
   - Click on "About" page on both
   - Click on "Courses" page on both
   - Verify each shows correct institute-specific content

---

## 🔄 Restarting Development Servers

If you need to restart:

1. **Stop servers:**
   - Press `Ctrl + C` in both terminal windows

2. **Clear cache (optional):**
   ```powershell
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```

3. **Start servers again:**
   - Terminal 1: `php artisan serve --host=127.0.0.1 --port=8000`
   - Terminal 2: `npm run dev`

---

## 📞 Next Steps

Once both websites are accessible locally:

1. ✅ Verify both landing pages load correctly
2. ✅ Test navigation (About, Courses pages)
3. ✅ Start building admin dashboards
4. ✅ Implement course management
5. ✅ Implement student management
6. ✅ Implement fee management
7. ✅ Implement result management

---

## 🎯 Development Workflow

1. **Start servers** (Laravel + Vite)
2. **Open both websites** in browser
3. **Make code changes**
4. **Refresh browser** to see changes
5. **Test on both websites** to ensure institute-specific behavior
6. **Commit changes** to Git when done

---

Happy coding! 🚀

