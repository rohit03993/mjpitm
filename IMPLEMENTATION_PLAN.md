# CRM Fixes – Implementation Plan (No Data Loss)

**Scope:** All critical fixes from the analysis report  
**Out of scope:** Fee management (leave as-is)  
**Principle:** No data loss. Existing students, courses, subjects, and results stay intact. No manual changes to courses/subjects. All changes work automatically on existing data.

---

## Guiding rules

1. **No destructive migrations** – We do not delete or overwrite existing registration numbers, results, or session data.
2. **Backward compatibility** – Existing records keep working; new logic applies to new data and to safe, additive updates only.
3. **Courses and subjects** – No changes required to course/subject data or structure.
4. **Fee management** – Not part of this plan; leave untouched.

---

## Phase 1 – Session change: keep parent and child in sync

**Goal:** When a student’s session is changed, both `semester_results.academic_year` and `results.academic_year` are updated so parent and child stay consistent.

**What we’ll do**

1. **Code change (StudentController)**  
   In the “session change” block in `StudentController@update`, after updating `SemesterResult.academic_year`, also update all related `Result` rows for that student to the new `academic_year`.

2. **One-time data fix (optional migration or command)**  
   For data already out of sync (e.g. session was changed in the past and only `semester_results` were updated):  
   Set `results.academic_year = semester_results.academic_year` for each result linked to a semester result.  
   This only copies from parent to child; no data is deleted or overwritten with wrong values.

**Data impact**

- **Existing:** Optional one-time sync only; no deletion.
- **New:** Every future session change will keep `Result` and `SemesterResult` in sync.

**Order:** Do this first so all later work builds on consistent session/academic_year data.

---

## Phase 2 – Registration number: one format, no change to existing

**Goal:** One consistent format for all **new** registrations and for **session-change** regeneration. Existing registration numbers are left as-is.

**What we’ll do**

1. **Choose one format**  
   Use institute-based prefix + year + fixed-length sequence, e.g. `MJPITM-2025-00001` / `MJPIPS-2025-00001` (5-digit sequence), so it works for both institutes and is scalable.

2. **Public registration (PublicRegistrationController)**  
   - Keep using institute prefix (MJPITM / MJPIPS).  
   - Change sequence to 5 digits (e.g. `str_pad(..., 5, '0', STR_PAD_LEFT)`).  
   - Uniqueness and “next number” logic based on this format only.  
   - No migration; no change to existing rows.

3. **Admin registration (StudentController)**  
   - Replace `REG` with the same institute-based prefix (MJPITM / MJPIPS) and 5-digit sequence.  
   - Use the same format as public registration so both paths produce the same style of number.

4. **Session change (StudentController)**  
   - When regenerating registration number on session change, **preserve the student’s current prefix** (e.g. if they have `MJPITM-2025-00001`, new number stays `MJPITM-{newYear}-{seq}`; if they have `REG-...`, we can either keep REG for that student or treat as legacy and still use institute prefix – recommend using institute prefix for consistency).  
   - To avoid data loss: **do not overwrite existing registration_number with a different format**. Either:  
     - (A) Use the same format as current number (detect prefix from existing value), or  
     - (B) Use the new standard format (MJPITM/MJPIPS + 5 digits) for session-change as well, and only generate new numbers for that year; existing numbers in DB stay untouched.

   Recommended: (B) Use new standard format only when generating a **new** number (session change). So after this phase, any **new** or **regenerated** number is MJPITM/MJPIPS + year + 5 digits. Existing rows keep their current value.

**Data impact**

- **Existing:** No migration. All current registration numbers remain unchanged.
- **New:** All new and session-change registration numbers use the single format.

**Order:** After Phase 1.

---

## Phase 3 – Academic year for new results (multi-semester logic)

**Goal:** For **new** semester results only, set `academic_year` from student session and semester number (e.g. Sem 1–2 → first year of session, Sem 3–4 → second year). Existing results are not modified.

**What we’ll do**

1. **Helper (e.g. in SemesterResultController or a small service)**  
   Compute `academic_year` from:
   - Student’s `session` (e.g. `"2025-26"`),
   - Semester number,
   - Optional: course duration (e.g. 6 semesters → 3 years).  
   Example rule: semesters 1–2 = first academic year (session as-is), 3–4 = second (session + 1 year), etc. Exact rule can match your institute (e.g. 2 semesters per year).

2. **SemesterResultController@create**  
   - Pre-fill `academic_year` using this helper (read-only in UI or clearly auto-set).

3. **SemesterResultController@store**  
   - Use the same helper to set `academic_year` when creating a new semester result (ignore or override manual input for consistency).  
   - No changes to existing rows.

**Data impact**

- **Existing:** No change to existing `semester_results` or `results`.
- **New:** New results get correct academic year by semester.

**Order:** After Phase 2.

---

## Phase 4 – Validations (new data only)

**Goal:** Prevent invalid data on create/update; do not alter existing records.

**What we’ll do**

1. **Course–institute validation**  
   In `PublicRegistrationController@store` and `StudentController@store`:  
   Ensure `course_id` belongs to the selected `institute_id` (e.g. `Rule::exists('courses','id')->where('institute_id', $instituteId)`).  
   Applies only to new registrations/edits; no DB change to existing data.

2. **Session format validation**  
   On student create/update, validate session format (e.g. `regex:/^\d{4}-\d{2}$/` for `YYYY-YY`).  
   Apply only to form submit; do not run a migration that “fixes” or deletes existing sessions (to avoid risk). Optional: later add a separate, careful script to report invalid sessions if needed.

3. **Academic year in result form**  
   When creating a semester result, either make `academic_year` read-only (from Phase 3 helper) or validate it against the computed value. No change to existing results.

**Data impact**

- **Existing:** Unchanged. Validations only affect new submissions.

**Order:** After Phase 3.

---

## Phase 5 – Optional one-time sync (if you want to fix old session changes)

**Goal:** Fix any existing `results` that were left with old `academic_year` after a past session change (optional, for consistency).

**What we’ll do**

- One-time operation: for every `Result`, set  
  `results.academic_year = semester_results.academic_year`  
  where `results.semester_result_id = semester_results.id`.  
- Can be implemented as a migration (single UPDATE) or an Artisan command.  
- No deletes; only syncs child to parent.

**Data impact**

- **Existing:** Only updates `results.academic_year` to match existing `semester_results.academic_year`.
- **New:** N/A.

**Order:** After Phase 1 (or after Phase 4 if you prefer to run it once at the end).

---

## Summary order of work

| Phase | What | Data loss risk | Courses/subjects |
|-------|------|----------------|------------------|
| **1** | Session change: update `Result.academic_year` + optional one-time sync | None | No change |
| **2** | Registration number: one format for new/session-change; leave existing as-is | None | No change |
| **3** | Academic year for new results (multi-semester logic) | None | No change |
| **4** | Validations (course–institute, session format, academic year) | None | No change |
| **5** | (Optional) One-time sync of `results.academic_year` from `semester_results` | None | No change |

Fee management, course structure, and subject structure are unchanged. All steps are additive or validation-only, except the optional sync which only aligns existing child records to existing parent data.

---

## What we are not doing (as per your request)

- Fee management (no changes).
- Changing or migrating courses or subjects.
- Deleting or overwriting existing registration numbers.
- Forcing a single registration format on existing rows (we only standardize for new/regenerated numbers).
- Any change that would require manual correction of existing data (other than the optional one-time sync above).

If you confirm this plan, we can start with **Phase 1** (session change + optional sync) and then proceed phase by phase.
