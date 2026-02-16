# Phase 3 Implementation - Academic Year for New Results (Multi-Semester Logic)

## What was done

### 1. **Academic year computed from session + semester**

**File:** `app/Http/Controllers/Admin/SemesterResultController.php`

**New helper:** `getAcademicYearForSemester(string $session, int $semester): string`

- **Rule:** 2 semesters per academic year.
- **Sem 1–2** → first academic year = session as-is (e.g. `2025-26`).
- **Sem 3–4** → second academic year = session + 1 year (e.g. `2026-27`).
- **Sem 5–6** → third academic year = session + 2 years (e.g. `2027-28`).
- **Sem 7–8** → fourth year, and so on.

**Examples (session `2025-26`):**

| Semester | Academic year |
|----------|----------------|
| 1       | 2025-26        |
| 2       | 2025-26        |
| 3       | 2026-27        |
| 4       | 2026-27        |
| 5       | 2027-28        |
| 6       | 2027-28        |

---

### 2. **Create form uses computed academic year**

**Method:** `SemesterResultController@create`

- **Before:** `$academicYear = $student->session;` (same for every semester).
- **After:** `$academicYear = $this->getAcademicYearForSemester($student->session, $nextSemester);`

The create view already sends `academic_year` in a hidden input; it now receives the value from this helper, so the correct academic year is shown and submitted for each semester.

---

### 3. **Store always uses computed academic year**

**Method:** `SemesterResultController@store`

- Validation: `academic_year` is now `nullable` (form can send it, but it is not used as source of truth).
- After validation we set:
  - `$validated['academic_year'] = $this->getAcademicYearForSemester($student->session, $validated['semester']);`
- If `$student->session` is empty, the request is rejected with an error and no result is saved.

So:

- **SemesterResult** and each **Result** row are always saved with the same, computed `academic_year`.
- No manual override from the form; multi-semester logic is applied consistently.

---

## Data impact

- **Existing data:** No migration and no change to existing `semester_results` or `results`. Phase 3 only affects **new** result creation.
- **New results:** Every new semester result (and its child `Result` rows) gets `academic_year` from session + semester via the helper.
- **Courses/subjects:** Unchanged.

---

## Flow summary

1. Admin opens “Generate Semester Result” for a student.
2. Controller computes `academic_year` from `$student->session` and `$nextSemester` and passes it to the view.
3. User submits the form (marks only; `academic_year` in the form is effectively ignored on the server).
4. On store, controller recomputes `academic_year` from `$student->session` and `$validated['semester']`, then saves it on the semester result and on each result row.

Result: Semester 1 and 2 show the first year (e.g. 2025-26), Semester 3 and 4 the second (e.g. 2026-27), etc., with no manual input and no change to existing records.

---

## Testing checklist

- [ ] Create Semester 1 result for a student (session e.g. 2025-26). Confirm academic year **2025-26** in DB and on PDF.
- [ ] Create Semester 2 result for same student. Confirm academic year **2025-26**.
- [ ] Create Semester 3 result. Confirm academic year **2026-27**.
- [ ] Create Semester 4 result. Confirm academic year **2026-27**.
- [ ] Confirm existing semester results in DB are unchanged.

---

**Status:** Phase 3 complete  
**Date:** February 16, 2026
