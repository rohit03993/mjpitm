# Result Publish & Marksheet System – Research & Current Behaviour

This document describes how the result publication and marksheet flow works in the codebase so you can confirm it matches what you want and we can fix any gaps.

---

## 1. High-Level Flow (What You Wanted vs What Exists)

| Step | Intended | Current implementation |
|------|----------|-------------------------|
| 1 | **Publish result** – set declaration date (Feb/Jul), result visible online, no PDF | ✅ Implemented: Super Admin sets `result_declaration_date` (validated Feb for odd sem, Jul for even). Status → published, result visible to student. No PDF. |
| 2 | **Issue marksheet** – later, set issue date (Mar/Aug), generate PDF, only admin can print | ✅ Implemented: Super Admin sets `date_of_issue` (validated Mar/Aug), PDF generated and stored. Only Super Admin can view/download PDF. |

So the **two-step flow** (publish result → issue marksheet) is implemented as designed.

---

## 2. Data Model

- **SemesterResult** (one per student per semester):  
  `result_declaration_date`, `date_of_issue`, `status` (draft → published), `published_at`, `verified_by`, `verified_at`, `pdf_path`.
- **Result** (subject-wise, linked to SemesterResult via `semester_result_id`):  
  When the semester is published, all its `Result` rows are set to `status = 'published'` and get `verified_by`, `verified_at`, `published_at`.
- **“Truly published”**: A SemesterResult is treated as published for display (student dashboard, admin student show) only if:  
  `status === 'published'`, `published_at` and `verified_at` and `verified_by` are set, and it has at least one child `Result` with `status = 'published'`.

---

## 3. Step-by-Step Current Behaviour

### 3.1 Creating a semester result (Super Admin only)

- **Entry:** Student detail → “Generate Result” (only if `auth()->user()->isSuperAdmin()` and student is active with roll number).
- **Route:** `GET/POST admin/students/{student}/generate-semester-result` → `SemesterResultController@create` / `store`.
- **Logic:** Picks next semester (draft, or no result, or published with zero marks for regeneration). Builds form with subjects for that semester. Academic year is computed from student session + semester (Phase 3 logic).
- **After store:** SemesterResult is created with `status = 'draft'`. Child `Result` rows are `status = 'pending_verification'`. Redirect to semester result **show** page.

### 3.2 Publish result (Super Admin only)

- **Entry:** Semester result show page → “✓ Publish result” (only when `status !== 'published'` and user is Super Admin).
- **Routes:**  
  - `GET admin/semester-results/{semesterResult}/publish-form` → `showPublishForm`  
  - `POST admin/semester-results/{semesterResult}/publish` → `publish`
- **Validation:**  
  - Odd semester (1,3,5…): `result_declaration_date` must be in **February**.  
  - Even semester (2,4,6…): **July**.
- **On success:**  
  - SemesterResult: `result_declaration_date`, `status = 'published'`, `verified_by`, `verified_at`, `published_at`.  
  - All related `Result` rows: `status = 'published'`, `verified_by`, `verified_at`, `published_at`.  
  - No PDF is generated; `pdf_path` and `date_of_issue` stay null until “Issue marksheet”.
- **Message:** “Result published. You can issue the marksheet later from this page.”

### 3.3 Issue marksheet (Super Admin only)

- **Entry:** Semester result show page → “Issue marksheet” / “Re-issue marksheet” (only when `status === 'published'` and user is Super Admin).
- **Routes:**  
  - `GET admin/semester-results/{semesterResult}/issue-marksheet-form` → `showIssueMarksheetForm`  
  - `POST admin/semester-results/{semesterResult}/issue-marksheet` → `issueMarksheet`
- **Validation:**  
  - Odd semester: `date_of_issue` must be in **March**.  
  - Even semester: **August**.
- **On success:**  
  - SemesterResult: `date_of_issue` set, PDF generated and saved to `storage/app/public/results/{student_id}/{semester_result_id}.pdf`, `pdf_path` updated.
- **View/Download PDF:**  
  - `GET admin/semester-results/{id}/view` → HTML preview.  
  - `GET admin/semester-results/{id}/download` → file download.  
  Both require Super Admin; both require `date_of_issue` to be set (otherwise redirect to issue-marksheet form).

### 3.4 Who can do what

| Action | Super Admin | Institute Admin / Staff |
|--------|-------------|--------------------------|
| Generate semester result | ✅ | ❌ (403) |
| Open publish form / Publish result | ✅ | ❌ (403) |
| Open issue marksheet form / Issue marksheet | ✅ | ❌ (403) |
| View semester result details (show) | ✅ | ✅ (if they can view that student) |
| View PDF | ✅ (only if marksheet issued) | ❌ (403) |
| Download PDF | ✅ (only if marksheet issued) | ❌ (403) |

Student:

- Sees **only** result data (percentage, marks) on dashboard via `trulyPublished()` semester results.
- **No** marksheet PDF: `studentView` and `studentDownload` always `abort(403)` with message that the marksheet is issued by the institute and can be collected from the office.

---

## 4. Where Results Are Shown

- **Student dashboard:** `publishedSemesterResults` = `semesterResults()->trulyPublished()`. Shows semester, academic year, percentage, total marks; message: “Result published online. The official marksheet is issued by the institute and can be collected from the office.” No PDF links.
- **Admin – Student show:** “Published Semester Results” lists each published semester with “View Details” (semester result show), “View PDF”, “Download”. These PDF links are **not** restricted by role or by “marksheet issued” in the view.

---

## 5. Issues / Gaps Found

### 5.1 Admin Student show page – View PDF / Download (UX and permission)

- **File:** `resources/views/admin/students/show.blade.php` (Past Results section).
- **Current:** For every published semester result, “View PDF” and “Download” are shown to **any admin** who can see the student (including Institute Admin).
- **Backend:** `viewPdf` and `downloadPdf` abort with 403 for non–Super Admin, so Institute Admin gets an error when clicking.
- **Also:** Links are shown even when `date_of_issue` is null. For Super Admin that leads to redirect to issue-marksheet form; for others, 403.
- **Recommended:**  
  - Show “View PDF” and “Download” **only** when `auth()->user()->isSuperAdmin()` **and** `$semesterResult->date_of_issue` is set.  
  - Optionally show a short note for Institute Admin like “Marksheet (PDF) is available only to Super Admin after it is issued.”

### 5.2 Redirect to issue-marksheet form uses ID

- **File:** `SemesterResultController`: `viewPdf` and `downloadPdf` redirect with `route('admin.semester-results.issue-marksheet-form', $semesterResult->id)`.
- Laravel route model binding expects the key; passing `id` is valid and works. No change required unless you standardise on passing the model.

### 5.3 Old result system (admin/results)

- There is still an **old** flow: `admin/results` resource and routes for verify/publish on **Result** (subject-level) via `ResultController`. That is separate from the **Semester Result** flow above. If you only use the semester flow now, the old verify/publish routes may be redundant or used for legacy data; worth confirming.

---

## 6. Date Rules (Current Code)

- **Result declaration date (publish):**  
  - Odd semesters (1, 3, 5…): month must be **February (2)**.  
  - Even semesters (2, 4, 6…): month must be **July (7)**.
- **Marksheet issue date:**  
  - Odd: **March (3)**.  
  - Even: **August (8)**.  
  This date is the one printed on the marksheet (e.g. “Date of issue: …”).

---

## 7. Academic Year and Course Semesters

- **Academic year** for a semester is derived from the student’s **session** and **semester** (e.g. first year sem 1 & 2 → session year; second year sem 3 & 4 → next year).
- **Max semesters** for a course: `duration_months / 6` (6 months → 1 sem, 1 year → 2, 2 years → 4, 3 years → 6). Used when generating results and in validation.

---

## 8. Summary Table

| Item | Status |
|------|--------|
| Two-step flow (publish then issue marksheet) | ✅ Implemented |
| Declaration date Feb/Jul, issue date Mar/Aug | ✅ Enforced |
| Super Admin only: generate, publish, issue, view/download PDF | ✅ Enforced in controller |
| Student sees result data only, no PDF | ✅ Enforced; message to collect from office |
| Institute Admin can see result details but not PDF | ✅ 403 on PDF; ⚠️ but student show page shows PDF buttons to them |
| PDF buttons on admin student show | ⚠️ Should be Super Admin + only when marksheet issued |

Once you confirm this matches how you want the system to work, the only change suggested is to fix the **Admin Student show** page so View PDF and Download are shown only to Super Admin and only when the marksheet has been issued (`date_of_issue` set). If you want different rules (e.g. different dates, or Institute Admin allowed to view PDF in some cases), we can adjust the logic and UI accordingly.
