# Applicability of Changes to Existing Students

All changes made so far apply to **both new and existing students**. Nothing is "new-only."

---

## 1. Password = Date of birth (DDMMYYYY)

- **New students:** Password is set from DOB at registration (public and admin create).
- **Existing students:** Handled by migration `2026_02_16_000002_set_existing_students_password_from_dob.php`.
  - When you run `php artisan migrate`, every student who has a `date_of_birth` gets their password updated to DOB in DDMMYYYY format (e.g. 09-02-2000 → `09022000`).
  - Only `password` and `password_plain_encrypted` are updated; no other data is changed.
- **If you haven’t run migrations yet:** Run `php artisan migrate` once. After that, all existing students can log in with their DOB as password.

---

## 2. Registration form view (PDF) – Reg. no. on top + institute theme

- The same view `pdf/registration-form.blade.php` is used whenever an admin **views** or **downloads** a student’s registration form (`/admin/view/registration-form/{id}` or download).
- So **every student** (existing or new) gets:
  - Registration number shown at the **top** of the form (below the title).
  - Institute-specific styling (Tech = blue, Paramedical = green) based on that student’s `institute_id`.
- No extra step or “migration” for existing students; it applies to all as soon as the code is deployed.

---

## 3. Public registration form and admin create form

- These are only for **creating** new students (session, DOB-as-password, modern layout, etc.).
- Existing students don’t fill these again; they only need to **log in** (with DOB as password) and **view** their registration form (which already uses the updated PDF for all).

---

## Summary

| Change                         | New students      | Existing students                          |
|--------------------------------|-------------------|--------------------------------------------|
| Password = DOB                 | At registration   | After running migration (see above)        |
| Reg. no. on top in PDF         | Yes               | Yes (same view for every student)         |
| Institute theme in PDF         | Yes               | Yes (same view for every student)         |
| Session / form behaviour      | On new registration only | N/A (already registered)           |

**Action for you:** Run `php artisan migrate` if you haven’t already, so all existing students get the DOB password. Everything else already applies to all students.
