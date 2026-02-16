# Phase 5 Implementation – Optional One-Time Sync (results.academic_year)

## Goal

Fix any existing `Result` rows whose `academic_year` was left out of date after a past session change. Sync child to parent only: set `results.academic_year = semester_results.academic_year` where they are linked. No deletes; no other data changed.

---

## What was already in place (Phase 1)

Phase 1 added both the migration and the Artisan command. Phase 5 is fulfilled by those; no new code was added.

### 1. Migration (one-time UPDATE)

**File:** `database/migrations/2026_02_16_000001_sync_result_academic_years.php`

- **Action:** Single SQL UPDATE: for each `results` row with a `semester_result_id`, set `results.academic_year = semester_results.academic_year` from the joined `semester_results` row.
- **Condition:** Only rows where `r.academic_year != sr.academic_year` are updated (and `semester_result_id IS NOT NULL`).
- **Rollback:** `down()` is a no-op; original values are not stored, so sync cannot be reversed.

**When to run:** When you want a one-time, global sync during deployment (e.g. `php artisan migrate`).

### 2. Artisan command (optional, for control)

**Command:** `php artisan results:sync-academic-years`

**File:** `app/Console/Commands/SyncResultAcademicYears.php`

- **Action:** For each `Result` with a parent `SemesterResult`, sets `result.academic_year = semesterResult.academic_year` when they differ.
- **Options:**
  - `--dry-run` – list what would be updated; no DB changes.
  - `--student-id=<id>` – sync only results for that student.

**When to use:** When you prefer not to run the migration (e.g. already migrated), or when you want to preview changes or sync a single student.

---

## How to run the sync

**Option A – Migration (one-time, all data)**  
Run your migrations as usual. The sync runs once when this migration is executed:

```bash
php artisan migrate
```

**Option B – Command only (no migration)**  
If you do not run the migration (or already ran migrations before it existed), use the command:

```bash
# Preview only
php artisan results:sync-academic-years --dry-run

# Apply sync for all results
php artisan results:sync-academic-years

# Apply for one student
php artisan results:sync-academic-years --student-id=123
```

---

## Data impact

- **Existing:** Only `results.academic_year` is updated to match the current `semester_results.academic_year`. No deletes; no change to semester_results or other columns.
- **New:** N/A. Future consistency is handled by Phase 1 (session change updates both SemesterResult and Result).

---

## Summary

| Deliverable | Status |
|-------------|--------|
| One-time sync: results.academic_year ← semester_results.academic_year | Done (migration + command from Phase 1) |
| No deletes; child aligned to parent only | Yes |
| Optional run (migration or command) | Yes |
| Phase 5 documentation | This file |
