# Result Generation & Marksheet Issue – Execution Plan

## Implementation status (done)

- **Migration** `2026_02_18_000001_add_result_declaration_date_and_backfill_dates.php`: adds `result_declaration_date` and **backfills existing** semester results with default dates (Feb/July for result, March/August for issue) so already-generated results are updated.
- **Publish flow:** "Publish result" now goes to a form (`/admin/semester-results/{id}/publish-form`) where user sets result declaration date and marksheet issue date; both are validated by month (Feb/July and March/August) and saved before generating the PDF.
- **Marksheet PDF:** Shows "Date of issue" in the result summary; `result_declaration_date` is not printed.
- **Max semesters:** Enforced in create (subject list filtered by course duration) and in store validation (`semester` ≤ `course->max_semesters`).

---

## 1. Summary of behaviour

- **Result declaration** = internal step (date stored, **not printed**).  
  - Odd semesters (1, 3, 5…): result declaration month = **February** (user picks day).  
  - Even semesters (2, 4, 6…): result declaration month = **July** (user picks day).

- **Marksheet** = what gets **printed**.  
  - Odd semesters: marksheet issue month = **March** (user picks day).  
  - Even semesters: marksheet issue month = **August** (user picks day).  
  - Printed marksheet shows: **“Date of issue: [e.g.] 15 March 2024”** (from `date_of_issue`).

- **Duration → number of semesters** (from course `duration_months`):
  - **6 months** → **1 semester** (sem 1 only).
  - **1 year (12 months)** → **2 semesters** (1, 2).
  - **2 years (24 months)** → **4 semesters** (1, 2, 3, 4).
  - **3 years (36 months)** → **6 semesters** (1–6).  
  Formula: **max_semesters = duration_months / 6** (with validation so 6→1, 12→2, 24→4, 36→6).

---

## 2. Database changes

| Item | Action |
|------|--------|
| **result_declaration_date** | Add to `semester_results`: `date` nullable. Used only for records; not printed on marksheet. |
| **date_of_issue** | Already exists on `semester_results`. Use for **marksheet** issue date (March/August + day). **Printed** on marksheet. |

- **Migration:** Add column `result_declaration_date` (nullable date) to `semester_results`.
- **Model:** Add `result_declaration_date` to `SemesterResult` `$fillable` and `$casts` (as `date`).

---

## 3. Result declaration (publish) – not printed

**Where:** Publish flow in `SemesterResultController::publish()` (or a dedicated “declare result” step that runs before/at publish).

**Behaviour:**

1. Before/at publish, show a form or modal: **“Result declaration date”**.
2. **Default/suggested month from semester:**
   - Odd sem (1, 3, 5…): month = **February**. Year = first part of `academic_year` (e.g. 2023 for 2023-24).
   - Even sem (2, 4, 6…): month = **July**. Same year rule.
3. User selects **day** (and optionally confirms month/year). Backend builds full date and validates month (Feb or July).
4. Save to `semester_results.result_declaration_date`.
5. **Do not** show this date on the printed marksheet.

---

## 4. Marksheet issue date – printed

**Where:** When generating the marksheet PDF (e.g. at publish, or via “Issue marksheet” action).

**Behaviour:**

1. When issuing/generating marksheet, ask for **“Marksheet issue date”**.
2. **Default/suggested month from semester:**
   - Odd sem: **March**. Year = first part of `academic_year`.
   - Even sem: **August**. Same year.
3. User selects **day** (and optionally confirms month/year). Backend validates month (March or August).
4. Save to `semester_results.date_of_issue`.
5. **Printed marksheet** (PDF) must show this date, e.g. **“Date of issue: 15 March 2024”** or **“Issued on: 15 March 2024”** (format: d M Y or dd/mm/yyyy as per institute preference).

---

## 5. Duration and max semesters

**Where:** Wherever semester options or validation exist (e.g. result create form, dropdowns, backend validation).

**Logic:**

- From course: `duration_months` (e.g. 6, 12, 24, 36).
- **Max semesters** = `duration_months / 6` (integer).
  - 6 → 1, 12 → 2, 24 → 4, 36 → 6.
- Validation: semester number must be between 1 and max_semesters (inclusive).
- Optional: add helper on `Course` model, e.g. `getMaxSemestersAttribute()` or `total_semesters()`.

---

## 6. Suggested implementation order

| Phase | Task | Notes |
|-------|------|--------|
| **A** | Migration: add `result_declaration_date` to `semester_results` | Nullable date. |
| **B** | Model: add `result_declaration_date` to fillable and casts | `SemesterResult`. |
| **C** | Publish flow: ask for result declaration date (Feb/July + day), save it | Odd sem → Feb, even → July; validate month; store only, no print. |
| **D** | Marksheet issue: ask for issue date (March/August + day), save to `date_of_issue` | Odd sem → March, even → August; can be same step as “generate marksheet” or after publish. |
| **E** | PDF (marksheet): show `date_of_issue` as “Date of issue” / “Issued on” | In `semester-result.blade.php` and preview if used. |
| **F** | Duration: enforce max semesters from `duration_months / 6` | In create result form and any semester dropdown/validation. |

---

## 7. UI flow (concise)

1. **Enter result** – Select semester (1 to max_semesters), enter marks. Academic year auto from session + semester. No dates yet.
2. **Publish result** – Prompt: “Result declaration date.” Month fixed (Feb/July), user picks day. Save `result_declaration_date`. Do not print.
3. **Issue marksheet / Generate PDF** – Prompt: “Marksheet issue date.” Month fixed (March/August), user picks day. Save `date_of_issue`. Generate PDF. **Print** marksheet; it shows “Date of issue: [date_of_issue]”.

Result declaration and marksheet issue can be two steps on the same page (e.g. “Publish & issue marksheet”) or separate actions; in both cases, only the marksheet PDF is printed and only `date_of_issue` is shown on it.

---

## 8. File touch list (for implementation)

- `database/migrations/xxxx_add_result_declaration_date_to_semester_results_table.php` (new).
- `app/Models/SemesterResult.php` (fillable, casts).
- `app/Http/Controllers/Admin/SemesterResultController.php` (publish: ask result date; marksheet: ask issue date; validation; max semesters where needed).
- `resources/views/admin/semester-results/*.blade.php` (forms for result date and issue date, semester dropdown capped by duration).
- `resources/views/pdf/semester-result.blade.php` (print “Date of issue: {{ date_of_issue }}”).
- `resources/views/pdf/semester-result-preview.blade.php` (same, if used for preview).
- `app/Models/Course.php` (optional: `maxSemesters` or `totalSemesters` helper from `duration_months`).

Once you confirm this plan, implementation can follow the order in Section 6.
