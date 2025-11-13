# Local Setup Commands

## Step 1: Configure Hosts File (Manual - Requires Admin)
1. Open Notepad as Administrator
2. Open: `C:\Windows\System32\drivers\etc\hosts`
3. Add at bottom:
   ```
   127.0.0.1   mjpitm.local
   127.0.0.1   mjpips.local
   ```
4. Save and close
5. Flush DNS: `ipconfig /flushdns` (in PowerShell as Admin)

---

## Step 2: Verify Database Setup

### Command 1: Check if institutes exist
```powershell
cd "E:\Softwares DEV- Chiki\Result Management system\crm"
php artisan tinker
```

### In Tinker, run:
```php
\App\Models\Institute::all(['id', 'name', 'domain']);
```

### Expected Output:
- ID 1: Tech Institute (mjpitm.in)
- ID 2: Paramedical Institute (mjpips.in)

### If institutes don't exist or domains are wrong, run:
```powershell
php artisan db:seed --class=InstituteSeeder
```

### Exit Tinker:
Type `exit` and press Enter

---

## Step 3: Start Development Servers

### Terminal 1: Laravel Server
```powershell
cd "E:\Softwares DEV- Chiki\Result Management system\crm"
php artisan serve --host=127.0.0.1 --port=8000
```

### Terminal 2: Vite Server (Open new PowerShell window)
```powershell
cd "E:\Softwares DEV- Chiki\Result Management system\crm"
npm run dev
```

**Keep both terminals running!**

---

## Step 4: Access Both Websites

Open your browser and visit:

### Tech Institute (Blue Theme):
```
http://mjpitm.local:8000
```

### Paramedical Institute (Green Theme):
```
http://mjpips.local:8000
```

---

## Verification Commands

### Check if Laravel is running:
Visit: `http://127.0.0.1:8000` (should show default Laravel page or Tech Institute)

### Check if Vite is running:
Look at Terminal 2 - should show "VITE ready" message

### Test both domains:
1. Open two browser windows side by side
2. Window 1: `http://mjpitm.local:8000` (Tech - Blue)
3. Window 2: `http://mjpips.local:8000` (Paramedical - Green)

---

## Troubleshooting Commands

### If domain not resolving:
```powershell
# Check hosts file
Get-Content C:\Windows\System32\drivers\etc\hosts | Select-String "mjpitm\|mjpips"

# Flush DNS cache (run as Admin)
ipconfig /flushdns
```

### If port already in use:
```powershell
# Use different port
php artisan serve --host=127.0.0.1 --port=8001
# Then access: http://mjpitm.local:8001
```

### If database error:
```powershell
# Check database connection
php artisan migrate:status

# Run migrations if needed
php artisan migrate

# Run seeders if needed
php artisan db:seed
```

### Clear cache:
```powershell
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## Quick Test Command

### Test institute detection:
```powershell
php artisan tinker
```

Then in Tinker:
```php
// Test Tech Institute
$request = new \Illuminate\Http\Request();
$request->headers->set('Host', 'mjpitm.local');
echo "Tech Institute detected: " . \App\Models\Institute::where('domain', 'mjpitm.in')->first()->name . PHP_EOL;

// Test Paramedical Institute
$request2 = new \Illuminate\Http\Request();
$request2->headers->set('Host', 'mjpips.local');
echo "Paramedical Institute detected: " . \App\Models\Institute::where('domain', 'mjpips.in')->first()->name . PHP_EOL;
```

Exit Tinker: `exit`

---

## Summary Commands

### Full setup sequence:
```powershell
# 1. Navigate to project
cd "E:\Softwares DEV- Chiki\Result Management system\crm"

# 2. Check database (optional)
php artisan tinker
# Then: \App\Models\Institute::all(['id', 'name', 'domain']);
# Exit: exit

# 3. Start Laravel (Terminal 1)
php artisan serve --host=127.0.0.1 --port=8000

# 4. Start Vite (Terminal 2 - new window)
npm run dev

# 5. Access websites:
# - http://mjpitm.local:8000 (Tech Institute)
# - http://mjpips.local:8000 (Paramedical Institute)
```

---

## Notes

- **Both terminals must run simultaneously**
- **Laravel server** handles backend (PHP)
- **Vite server** handles frontend assets (CSS/JS)
- **Hosts file** maps local domains to 127.0.0.1
- **Middleware** detects domain and sets institute automatically
- **Both websites** use same backend, different frontend views

