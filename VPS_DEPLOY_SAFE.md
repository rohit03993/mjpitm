# Safe VPS Deploy (No Data Loss)

This guide is for deploying the current code to a VPS that already has production data.

## Rules for safety

- Do **not** run `migrate:fresh`, `db:wipe`, `schema:drop`, or truncate commands.
- Always take a DB backup before migration.
- Use `php artisan migrate --force` only (forward migrations).
- Seed only the required seeder: `RolePermissionSeeder`.

## 1) Go to project directory

```bash
cd /path/to/your/crm
```

## 2) Put app in maintenance mode (recommended)

```bash
php artisan down --render="errors::503"
```

## 3) Take database backup (MANDATORY)

Use the command matching your DB.

### MySQL/MariaDB

```bash
mkdir -p storage/backups
mysqldump -u DB_USER -p DB_NAME > storage/backups/pre_deploy_$(date +%F_%H%M%S).sql
```

### PostgreSQL

```bash
mkdir -p storage/backups
pg_dump -U DB_USER -d DB_NAME > storage/backups/pre_deploy_$(date +%F_%H%M%S).sql
```

## 4) Pull latest code

```bash
git pull origin master
```

## 5) Install/update dependencies

```bash
composer install --no-dev --optimize-autoloader
```

## 6) Run database migrations (safe, non-destructive)

```bash
php artisan migrate --force
```

This includes additive migrations only (e.g. `marksheet_serial_sequences` for safe marksheet numbering, permission tables, audits). It does **not** delete or truncate user data.

## 7) Sync roles/permissions for existing users

```bash
php artisan db:seed --class=RolePermissionSeeder --force
```

## 8) Clear/rebuild caches

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 9) If using queues, restart workers

```bash
php artisan queue:restart
```

## 10) Bring app back online

```bash
php artisan up
```

---

## Quick post-deploy checks

```bash
php artisan about
php artisan migrate:status
```

Then in browser:

- Super Admin login works.
- Student listing works.
- Student details page shows history section.
- Fees/Results pages load on mobile and desktop.

---

## If something fails

1. Keep app in maintenance mode.
2. Check logs:

```bash
tail -n 200 storage/logs/laravel.log
```

3. Fix issue and rerun only failed step.
4. If critical DB issue, restore from backup taken in step 3.

---

## One-line deployment block

```bash
php artisan down --render="errors::503" && git pull origin master && composer install --no-dev --optimize-autoloader && php artisan migrate --force && php artisan db:seed --class=RolePermissionSeeder --force && php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan queue:restart && php artisan up
```

Use this one-liner only after confirming backup is already taken.

