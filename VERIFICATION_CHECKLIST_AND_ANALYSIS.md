# Full System Verification – Checklist and Analysis

This document lets you run the **whole check** across the three main perspectives (Super Admin, Institute Admin, Student), then see **how everything works** and **how it turned out**.

---

## Part 1 – Verification checklist (run these flows)

Use this as a step-by-step list. Session must be in **YYYY-YY** format (e.g. `2025-26`). Course must belong to the selected institute (enforced by Phase 4).

---

### Flow A – Super Admin perspective

| Step | Action | Where | Expected |
|------|--------|--------|----------|
| 1 | Log in as Super Admin | `/superadmin/login` | Redirect to Super Admin dashboard |
| 2 | Open “Add student” | Dashboard or **Students** → **Add student** (or `/admin/students/create`) | Create form with institute + course dropdowns (all institutes) |
| 3 | Select an **institute**, then a **course** (must be of that institute), fill required fields, set **session** e.g. `2025-26`, submit | Create form | Student created; redirect to student list or success |
| 4 | Find the new student (e.g. **Students** list or **Website registrations**), open their **card** (profile) | `/admin/students/{id}` (Show) | Student detail page (personal info, course, status, documents, results) |
| 5 | **Make student active** | Same page: **Edit** (or `/admin/students/{id}/edit`) | Edit form: set **Status** = Active; if roll number was empty, it can be auto-generated on save (Phase 2 format) |
| 6 | Save and open student **card** again | `/admin/students/{id}` | Status = Active; roll number shown if generated |

**Done when:** You can add a student as Super Admin, make them active, and see their admin card (show page) with correct institute/course and status.

---

### Flow B – Institute Admin (Staff) perspective

| Step | Action | Where | Expected |
|------|--------|--------|----------|
| 1 | Log in as **Institute Admin** (staff user for one institute) | `/staff/login` | Redirect to Staff dashboard |
| 2 | Ensure **institute context** is set | Staff dashboard / institute switcher (if any) | Session `current_institute_id` = that institute (list/show are scoped to this institute) |
| 3 | **Add student** | **Students** → **Add student** (`/admin/students/create`) | Form: institute may be fixed or pre-selected; **course** dropdown only shows courses for that institute (Phase 4 validation) |
| 4 | Fill form (session e.g. `2025-26`), submit | Create form | Student created for that institute |
| 5 | Open the new student’s **card** | **Students** list → click student → `/admin/students/{id}` | Student show page; you see only students you’re allowed to see (your institute or created by you) |
| 6 | **Make student active** | **Edit** → Status = Active → Save | Same as Super Admin flow; roll number can auto-generate |
| 7 | Confirm on **student card** | `/admin/students/{id}` | Status Active; roll number visible |

**Done when:** As Institute Admin you can add a student (course limited to your institute), make them active, and see their admin card without errors.

---

### Flow C – Student perspective

| Step | Action | Where | Expected |
|------|--------|--------|----------|
| 1 | Log in as **student** (use credentials of a student you created above) | `/student/login` | Redirect to Student dashboard |
| 2 | View **dashboard** | `/student/dashboard` | Dashboard with their info, documents (registration form, ID card), and any published semester results |
| 3 | Open **registration form** (view/download) | Dashboard links | Their own registration form only |
| 4 | Open **ID card** (view/download) | Dashboard links | Their own ID card only |
| 5 | If semester results are published, open **result** (view/download) | Dashboard links | Only their own results; view/download work |

**Done when:** Student can log in, see their dashboard (their “card” from student side), and access only their own documents and results.

---

## Part 2 – How it works (short analysis)

### Access and roles

- **Super Admin:** Logs in at `/superadmin/login`; middleware `EnsureUserIsSuperAdmin` restricts Super Admin–only routes (institutes, categories, courses, subjects, user management, etc.). Can see all students and all institutes; can add student for any institute and any course of that institute.
- **Institute Admin (Staff):** Logs in at `/staff/login`; same `auth` guard as Super Admin but not Super Admin. Sees **Staff dashboard**; student list and website registrations are filtered by `session('current_institute_id')` (and/or their `user.institute_id`). Can add students only for courses belonging to the selected institute (Phase 4 course–institute validation).
- **Student:** Logs in at `/student/login`; guard `auth:student`. Can only access `/student/dashboard` and their own documents/results; no access to admin or other students.

### Add student → Make active → See card

- **Create student:** `StudentController@store`. Institute from request or session; course must exist and belong to that institute (`Rule::exists('courses','id')->where('institute_id', $instituteId)`). Session required and validated as `YYYY-YY`.
- **Make active:** `StudentController@update` (Super Admin only for edit). Status set to `active`; if roll number is empty, it is generated on save (Phase 2: enrollment number format; registration number format on create/session change).
- **Admin card:** `StudentController@show`. Super Admin sees any student; Institute Admin sees student if they created them or student’s institute matches `current_institute_id`. “Card” = student detail page (show view).

### Student dashboard (“student card” from student side)

- **StudentDashboardController@index:** Loads the logged-in student, their course/institute, documents, and published semester results. All links (registration form, ID card, results) are scoped to that student; no way to access another student’s data from the student UI.

### What to watch when you run the check

1. **Institute context (Institute Admin):** If the app has an institute switcher, ensure the correct institute is selected before adding a student; otherwise you may get “Please select an institute” or wrong course list.
2. **Session format:** Use `2025-26` style; invalid format is rejected with a clear message (Phase 4).
3. **Course–institute:** If you pick a course from another institute, validation will fail (Phase 4).
4. **Roll number:** Generated when status is set to Active and roll number is empty; generation requires student to have session and institute/course (Phase 2).

---

## Part 3 – How it turned out (summary)

After you run the three flows:

- **Super Admin:** Add student (any institute + course of that institute) → Make active → Open student card. **Smooth if** you see the new student, can edit status to Active, and the show page displays correct data and (when applicable) generated roll number.
- **Institute Admin:** Same flow with institute-scoped list and course dropdown; add student → make active → open card. **Smooth if** you only see your institute’s students (or those you created), courses are limited to your institute, and the student card opens without 403.
- **Student:** Login → Dashboard → Open registration form, ID card, and (if any) results. **Smooth if** the student sees only their own data and view/download work.

If anything in the checklist fails (e.g. 403, validation error, wrong list, missing roll number), note the step and the exact message; that pinpoints where to fix. The analysis above describes how the app is intended to behave so you can compare “how it turned out” to “how it works.”
