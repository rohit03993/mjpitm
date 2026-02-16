# Phase 4 Implementation – Validations (New Data Only)

## Goal

Prevent invalid data on create/update. No migrations; no changes to existing records. Validations apply only to form submissions.

---

## 1. Course–institute validation

**Requirement:** `course_id` must belong to the selected `institute_id`.

### PublicRegistrationController@store

- **Before:** `course_id` validated with `exists:courses,id`; a manual check after validation ensured the course belonged to the institute.
- **After:** `course_id` validated with `Rule::exists('courses', 'id')->where('institute_id', $instituteId)`. The manual check was removed (validation now enforces it).

### StudentController@store

- **Before:** `course_id` was `required|exists:courses,id`; no institute check.
- **After:** `course_id` is `required` and `Rule::exists('courses', 'id')->where('institute_id', $instituteId)` so the course must belong to the chosen institute (from request or session).

### StudentController@update

- **Before:** `course_id` was `required|exists:courses,id`.
- **After:** Same rule as store: `Rule::exists('courses', 'id')->where('institute_id', $instituteId)` so the course must belong to the institute (from request or existing student).

---

## 2. Session format validation

**Requirement:** Session must be in `YYYY-YY` format (e.g. `2025-26`).

- **Rule used:** `regex:/^\d{4}-\d{2}$/`
- **Custom message:** "Session must be in YYYY-YY format (e.g. 2025-26)."

### Where applied

| Location                         | Session rule |
|----------------------------------|--------------|
| PublicRegistrationController@store | Required, string, max:255, regex |
| StudentController@store            | Required, string, max:255, regex |
| StudentController@update           | Nullable, string, max:255, regex |

Existing records are not touched; invalid sessions in the database are not modified or deleted. Only new or updated submissions are validated.

---

## 3. Academic year in semester result form

**Requirement:** When creating a semester result, `academic_year` must match the value derived from student session and semester (Phase 3 helper).

- **Create form:** Already uses a hidden input with the computed `academicYear` from the Phase 3 helper (read-only display).
- **Store:** Phase 3 already overwrites `academic_year` with the computed value. Phase 4 adds a check: if the request sends an `academic_year` that does not match the computed value, the request is rejected with a validation error: "Academic year must match the value derived from student session and semester."

This blocks tampering with the hidden field while keeping server-side behaviour unchanged (computed value is still what is saved).

---

## Files changed

| File | Changes |
|------|---------|
| `app/Http/Controllers/PublicRegistrationController.php` | Course–institute rule for `course_id`; session regex; removed manual course check; custom message for `session.regex`. |
| `app/Http/Controllers/Admin/StudentController.php` | In `store` and `update`: course–institute rule for `course_id`; session regex; custom message for `session.regex`. |
| `app/Http/Controllers/Admin/SemesterResultController.php` | In `store`: after computing academic year, reject if submitted `academic_year` does not match computed. |

---

## Data impact

- **Existing:** None. No migrations; no updates to existing rows.
- **New/edits:** Invalid course–institute combination, invalid session format, or mismatched academic year are rejected at validation with clear messages.

Fee management, course structure, and subject structure are unchanged.
