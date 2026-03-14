# Safe implementation notes (no data loss)

These changes were implemented so that **existing data is not lost** and the system remains safe for live use.

---

## 1. Student “delete” (soft delete)

- **Migration:** `2026_02_17_000001_add_soft_deletes_to_students_table.php`  
  - Adds a nullable `deleted_at` column to `students`.  
  - **No existing rows are updated**; all current students keep `deleted_at = null`.

- **Behaviour:**  
  - “Remove student” (Super Admin only) only sets `deleted_at`. No row is removed.  
  - The student disappears from lists and can no longer log in.  
  - Results, fees, and other related data are unchanged; when viewing a result/fee we still load the student (via `withTrashed()`) so audit data is intact.

- **Deploy:** Run `php artisan migrate` so the new column exists. No need to change existing data.

---

## 2. Student password reset

- **Behaviour:**  
  - Student uses “Forgot password”, enters Registration/Enrollment No.  
  - A one-time link is shown on the next page (valid 60 minutes).  
  - Using the link, they set a new password.  
  - **Only the `password` field is updated**; no other columns or rows are changed or deleted.

- **Technical:** Token is stored in cache (no new table). No migration.

---

## 3. Rate limiting on public registration

- **Change:** `POST /registration-form` is throttled to **5 requests per minute per IP**.  
- **Data:** No database or data changes; only request limiting.

---

## 4. Date of birth validation

- **Change:** On student **create** and **update**, `date_of_birth` must be:  
  - a valid date,  
  - **before today**,  
  - **after 1900-01-01**.  
- **Data:** Validation only; no migration and no change to existing rows. Existing DOB values are not modified. Only new or edited submissions are validated.

---

## Summary

| Change              | Data loss risk | Migration required | Existing data |
|---------------------|----------------|--------------------|---------------|
| Soft delete         | None           | Yes (`deleted_at`) | Unchanged     |
| Password reset      | None           | No                 | Only password updated when user resets |
| Registration throttle | None        | No                 | N/A           |
| DOB validation     | None           | No                 | Unchanged     |

After deploy, run **`php artisan migrate`** once so soft delete works. All other changes work without further steps.
