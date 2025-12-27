<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Institute;
use App\Models\CourseCategory;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Course::with(['institute', 'category'])
            ->withCount('students');

        // Search functionality - search across name, code, institute, and category
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('institute', function ($iq) use ($search) {
                      $iq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('category', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $courses = $query->latest()->paginate(15)->withQueryString();
        
        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get all active institutes for the dropdown
        $institutes = Institute::where('status', 'active')->get();
        
        // Get all active categories with their institutes for the dropdown
        $categories = CourseCategory::where('status', 'active')
            ->with('institute')
            ->orderBy('institute_id')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();
        
        // Pass categories as JSON for JavaScript filtering
        $categoriesJson = $categories->map(function($category) {
            return [
                'id' => $category->id,
                'institute_id' => $category->institute_id,
                'name' => $category->name,
            ];
        })->toJson();
        
        return view('admin.courses.create', compact('institutes', 'categories', 'categoriesJson'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Get institute ID - All admins can select institute
        $instituteId = $request->input('institute_id') ?? session('current_institute_id');
        
        // If no institute selected, try to use user's institute (for Institute Admin as fallback)
        if (!$instituteId && $user->institute_id) {
            $instituteId = $user->institute_id;
        }
        
        // Validate the request
        $validated = $request->validate([
            'institute_id' => ['required', 'exists:institutes,id'],
            'category_id' => ['nullable', 'exists:course_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:courses,code'],
            'duration_value' => ['required', 'numeric', 'min:0.1'],
            'duration_type' => ['required', 'in:months,years'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
            
            // Fee fields
            'registration_fee' => ['nullable', 'numeric', 'min:0'],
            'entrance_fee' => ['nullable', 'numeric', 'min:0'],
            'enrollment_fee' => ['nullable', 'numeric', 'min:0'],
            'tuition_fee' => ['nullable', 'numeric', 'min:0'],
            'caution_money' => ['nullable', 'numeric', 'min:0'],
            'hostel_fee_amount' => ['nullable', 'numeric', 'min:0'],
            'late_fee' => ['nullable', 'numeric', 'min:0'],
        ]);
        
        // Convert duration to months
        if ($validated['duration_type'] === 'years') {
            $validated['duration_months'] = (int)($validated['duration_value'] * 12);
        } else {
            $validated['duration_months'] = (int)$validated['duration_value'];
        }
        
        // Remove temporary fields
        unset($validated['duration_value'], $validated['duration_type']);
        
        // Set default registration fee to ₹1000 if not provided
        if (!isset($validated['registration_fee']) || $validated['registration_fee'] == null || $validated['registration_fee'] == 0) {
            $validated['registration_fee'] = 1000.00;
        }
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('courses', 'public');
        }
        
        // Create the course
        $course = Course::create($validated);
        
        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        $course->load(['institute', 'students', 'subjects']);
        
        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        // Get all active institutes for the dropdown
        $institutes = Institute::where('status', 'active')->get();
        
        // Get all active categories with their institutes for the dropdown
        $categories = CourseCategory::where('status', 'active')
            ->with('institute')
            ->orderBy('institute_id')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();
        
        // Pass categories as JSON for JavaScript filtering
        $categoriesJson = $categories->map(function($category) {
            return [
                'id' => $category->id,
                'institute_id' => $category->institute_id,
                'name' => $category->name,
            ];
        })->toJson();
        
        // Calculate duration value and type for the form
        $durationMonths = $course->duration_months ?? 0;
        $durationValue = $durationMonths;
        $durationType = 'months';
        
        // If duration is a multiple of 12, show as years
        if ($durationMonths > 0 && $durationMonths % 12 == 0) {
            $durationValue = $durationMonths / 12;
            $durationType = 'years';
        }
        
        // Ensure registration fee defaults to 1000 if null or 0
        $registrationFee = $course->registration_fee;
        if (!$registrationFee || $registrationFee == 0) {
            $registrationFee = 1000.00;
        }
        
        return view('admin.courses.edit', compact('course', 'institutes', 'categories', 'categoriesJson', 'durationValue', 'durationType', 'registrationFee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        // Validate the request
        $validated = $request->validate([
            'institute_id' => ['required', 'exists:institutes,id'],
            'category_id' => ['nullable', 'exists:course_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:courses,code,' . $course->id],
            'duration_value' => ['required', 'numeric', 'min:0.1'],
            'duration_type' => ['required', 'in:months,years'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
            
            // Fee fields
            'registration_fee' => ['nullable', 'numeric', 'min:0'],
            'entrance_fee' => ['nullable', 'numeric', 'min:0'],
            'enrollment_fee' => ['nullable', 'numeric', 'min:0'],
            'tuition_fee' => ['nullable', 'numeric', 'min:0'],
            'caution_money' => ['nullable', 'numeric', 'min:0'],
            'hostel_fee_amount' => ['nullable', 'numeric', 'min:0'],
            'late_fee' => ['nullable', 'numeric', 'min:0'],
        ]);
        
        // Convert duration to months
        if ($validated['duration_type'] === 'years') {
            $validated['duration_months'] = (int)($validated['duration_value'] * 12);
        } else {
            $validated['duration_months'] = (int)$validated['duration_value'];
        }
        
        // Remove temporary fields
        unset($validated['duration_value'], $validated['duration_type']);
        
        // Set default registration fee to ₹1000 if not provided or set to 0
        if (!isset($validated['registration_fee']) || $validated['registration_fee'] == null || $validated['registration_fee'] == 0) {
            $validated['registration_fee'] = 1000.00;
        }
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($course->image) {
                Storage::disk('public')->delete($course->image);
            }
            $validated['image'] = $request->file('image')->store('courses', 'public');
        }
        
        // Update the course
        $course->update($validated);
        
        return redirect()->route('admin.courses.index')
            ->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        // Check if course has students
        if ($course->students()->count() > 0) {
            return redirect()->route('admin.courses.index')
                ->with('error', 'Cannot delete course. There are students enrolled in this course.');
        }
        
        $course->delete();
        
        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    /**
     * Show the bulk import form
     */
    public function showImport()
    {
        $institutes = Institute::where('status', 'active')->get();
        return view('admin.courses.import', compact('institutes'));
    }

    /**
     * Preview Excel file and show field mapping interface
     */
    public function previewImport(Request $request)
    {
        $request->validate([
            'institute_id' => ['required', 'exists:institutes,id'],
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'], // 10MB max
        ]);

        try {
            $file = $request->file('excel_file');
            $instituteId = $request->input('institute_id');
            
            // Load Excel file
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (empty($rows) || count($rows) < 2) {
                return redirect()->back()
                    ->withErrors(['excel_file' => 'Excel file is empty or has no data rows.'])
                    ->withInput();
            }

            // Get headers (first row)
            $headers = array_map('trim', array_filter($rows[0]));
            
            // Get sample data (next 5 rows for preview)
            $sampleData = array_slice($rows, 1, 5);
            
            // Store file temporarily for processing
            $tempPath = $file->storeAs('temp', 'import_' . time() . '.' . $file->getClientOriginalExtension(), 'local');
            
            // System fields that need to be mapped
            $systemFields = [
                'category_name' => 'Category Name (Required)',
                'course_name' => 'Course Name (Required)',
                'duration' => 'Duration (Optional - smart parser: "1 Year Program", "6 months", "1 year 6 months")',
                'tuition_fee' => 'Fee/Total Fee (Required - can parse "Rs. 11,500" format)',
                'registration_fee' => 'Registration Fee (Optional - if not provided, will be ₹1000)',
                'description' => 'Description (Optional)',
                'status' => 'Status (Optional, default: active)',
            ];

            $institute = Institute::find($instituteId);

            return view('admin.courses.import-preview', compact(
                'headers',
                'sampleData',
                'systemFields',
                'instituteId',
                'institute',
                'tempPath'
            ));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['excel_file' => 'Error reading Excel file: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Process the import with field mappings
     */
    public function processImport(Request $request)
    {
        $request->validate([
            'institute_id' => ['required', 'exists:institutes,id'],
            'temp_path' => ['required', 'string'],
            'mappings' => ['required', 'array'],
            'mappings.category_name' => ['required', 'string'],
            'mappings.course_name' => ['required', 'string'],
            'mappings.tuition_fee' => ['required', 'string'],
            'mappings.registration_fee' => ['nullable', 'string'],
        ]);

        try {
            $instituteId = $request->input('institute_id');
            $tempPath = $request->input('temp_path');
            $mappings = $request->input('mappings');

            // Load Excel file from temp storage
            $fullPath = storage_path('app/' . $tempPath);
            if (!file_exists($fullPath)) {
                return redirect()->route('admin.courses.import')
                    ->withErrors(['error' => 'Temporary file not found. Please upload again.']);
            }

            $spreadsheet = IOFactory::load($fullPath);
            $worksheet = $spreadsheet->getActiveSheet();
            
            // Get all rows with proper value extraction
            $rows = [];
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            
            for ($row = 1; $row <= $highestRow; $row++) {
                $rowData = [];
                for ($col = 'A'; $col <= $highestColumn; $col++) {
                    $cell = $worksheet->getCell($col . $row);
                    
                    // Always get the formatted value to preserve currency symbols, commas, etc.
                    // This ensures we get "Rs. 11,500" or "11,500" instead of potentially incorrect numeric values
                    $formattedValue = $cell->getFormattedValue();
                    
                    // If formatted value is empty, try to get the calculated value
                    if (empty($formattedValue) || trim($formattedValue) === '') {
                        $formattedValue = $cell->getCalculatedValue();
                    }
                    
                    $rowData[] = $formattedValue;
                }
                $rows[] = $rowData;
            }

            // Skip header row
            $dataRows = array_slice($rows, 1);
            
            $results = [
                'successful' => [],
                'failed' => [],
                'skipped' => [],
            ];

            foreach ($dataRows as $rowIndex => $row) {
                $actualRowNumber = $rowIndex + 2; // +2 because array is 0-indexed and we skipped header
                
                try {
                    // Extract data based on mappings
                    $categoryName = $this->getMappedValue($row, $mappings['category_name']);
                    $courseName = $this->getMappedValue($row, $mappings['course_name']);
                    
                    // Smart duration parsing - check for single "duration" field first, then fallback to separate fields
                    $durationText = $this->getMappedValue($row, $mappings['duration'] ?? null);
                    $durationYears = 0;
                    $durationMonths = 0;
                    
                    if ($durationText && !empty(trim($durationText))) {
                        // Smart parse from single duration column (e.g., "1 Year Program", "6 months", "1 year 6 months")
                        $parsedDuration = $this->parseDuration($durationText);
                        $durationYears = $parsedDuration['years'];
                        $durationMonths = $parsedDuration['months'];
                        
                        // Debug: Log duration parsing for verification
                        if ($durationYears == 0 && $durationMonths == 0) {
                            \Log::warning("Duration parsing returned 0 for both years and months", [
                                'raw' => $durationText,
                                'row' => $actualRowNumber
                            ]);
                        }
                    } else {
                        // Fallback: check for separate duration_years and duration_months fields (backward compatibility)
                        $durationYearsRaw = $this->getMappedValue($row, $mappings['duration_years'] ?? null);
                        $durationMonthsRaw = $this->getMappedValue($row, $mappings['duration_months'] ?? null);
                        
                        if ($durationYearsRaw && !empty(trim($durationYearsRaw))) {
                            $parsedDuration = $this->parseDuration($durationYearsRaw);
                            $durationYears = $parsedDuration['years'];
                            $durationMonths += $parsedDuration['months'];
                        }
                        
                        if ($durationMonthsRaw && !empty(trim($durationMonthsRaw))) {
                            $parsedDuration = $this->parseDuration($durationMonthsRaw);
                            $durationYears += $parsedDuration['years'];
                            $durationMonths += $parsedDuration['months'];
                        }
                    }
                    
                    $tuitionFeeRaw = $this->getMappedValue($row, $mappings['tuition_fee']);
                    $registrationFeeRaw = $this->getMappedValue($row, $mappings['registration_fee'] ?? null);
                    $description = $this->getMappedValue($row, $mappings['description'] ?? null);
                    $status = $this->getMappedValue($row, $mappings['status'] ?? null, 'active');

                    // Validate required fields
                    if (empty($categoryName) || empty($courseName) || empty($tuitionFeeRaw)) {
                        $results['failed'][] = [
                            'row' => $actualRowNumber,
                            'reason' => 'Missing required fields (Category, Course Name, or Fee)',
                            'data' => $row
                        ];
                        continue;
                    }

                    // Parse fees - handle "Rs. 11,500" format or total fee
                    $tuitionFee = $this->parseFee($tuitionFeeRaw);
                    
                    // Debug: Log the raw value and parsed value for verification
                    \Log::info("Fee parsing", [
                        'row' => $actualRowNumber,
                        'course' => $courseName,
                        'raw_tuition_fee' => $tuitionFeeRaw,
                        'parsed_tuition_fee' => $tuitionFee,
                        'raw_type' => gettype($tuitionFeeRaw)
                    ]);
                    
                    // Debug: Log if fee seems suspiciously small (might indicate parsing issue)
                    if ($tuitionFee > 0 && $tuitionFee < 10) {
                        // This might be a parsing issue - log for debugging
                        \Log::warning("Suspiciously small tuition fee parsed", [
                            'raw' => $tuitionFeeRaw,
                            'parsed' => $tuitionFee,
                            'row' => $actualRowNumber,
                            'course' => $courseName
                        ]);
                    }
                    
                    $registrationFee = 1000; // Default registration fee is ₹1000 for all courses
                    
                    // If registration fee is provided separately, use it; otherwise use default 1000
                    if ($registrationFeeRaw && !empty($registrationFeeRaw)) {
                        $registrationFee = $this->parseFee($registrationFeeRaw);
                    }

                    // Find or create category
                    $category = CourseCategory::firstOrCreate(
                        [
                            'institute_id' => $instituteId,
                            'name' => trim($categoryName),
                        ],
                        [
                            'status' => 'active',
                            'display_order' => CourseCategory::where('institute_id', $instituteId)->max('display_order') + 1 ?? 1,
                        ]
                    );

                    // Calculate duration in months (using parsed values)
                    $totalMonths = ($durationYears * 12) + $durationMonths;
                    if ($totalMonths == 0) {
                        // Default to 1 month if no duration specified, but log it
                        \Log::warning("No duration found for course, defaulting to 1 month", [
                            'course' => $courseName,
                            'row' => $actualRowNumber,
                            'duration_text' => $durationText ?? 'not provided'
                        ]);
                        $totalMonths = 1; // Default to 1 month if no duration specified
                    }

                    // Generate course code
                    $categoryCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $categoryName), 0, 3));
                    $courseCode = $categoryCode . '-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $courseName), 0, 3)) . '-' . str_pad($category->courses()->count() + 1, 3, '0', STR_PAD_LEFT);

                    // Check if course already exists (by name in same category)
                    $existingCourse = Course::where('institute_id', $instituteId)
                        ->where('category_id', $category->id)
                        ->where('name', trim($courseName))
                        ->first();

                    if ($existingCourse) {
                        $results['skipped'][] = [
                            'row' => $actualRowNumber,
                            'reason' => 'Course already exists: ' . $courseName,
                            'data' => $row
                        ];
                        continue;
                    }

                    // Create course
                    $course = Course::create([
                        'institute_id' => $instituteId,
                        'category_id' => $category->id,
                        'name' => trim($courseName),
                        'code' => $courseCode,
                        'duration_months' => (int)$totalMonths,
                        'description' => $description ? trim($description) : null,
                        'tuition_fee' => (float)$tuitionFee,
                        'registration_fee' => (float)$registrationFee,
                        'status' => in_array(strtolower($status), ['active', 'inactive']) ? strtolower($status) : 'active',
                    ]);

                    $results['successful'][] = [
                        'row' => $actualRowNumber,
                        'category' => $category->name,
                        'course' => $course->name,
                        'code' => $course->code,
                    ];

                } catch (\Exception $e) {
                    $results['failed'][] = [
                        'row' => $actualRowNumber,
                        'reason' => $e->getMessage(),
                        'data' => $row
                    ];
                }
            }

            // Clean up temp file
            Storage::disk('local')->delete($tempPath);

            return view('admin.courses.import-results', compact('results', 'instituteId'));

        } catch (\Exception $e) {
            return redirect()->route('admin.courses.import')
                ->withErrors(['error' => 'Import failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Helper method to get mapped value from row
     */
    private function getMappedValue($row, $columnIndex, $default = null)
    {
        if ($columnIndex === null || $columnIndex === '' || $columnIndex === 'none') {
            return $default;
        }

        $index = (int)$columnIndex;
        if (isset($row[$index])) {
            $value = $row[$index];
            
            // Handle different Excel cell types
            // If it's a number, convert to string first to preserve precision
            if (is_numeric($value)) {
                $value = (string)$value;
            } else {
                $value = trim((string)$value);
            }
            
            return $value !== '' ? $value : $default;
        }

        return $default;
    }

    /**
     * Parse fee from various formats (e.g., "Rs. 11,500", "11500", "11,500.00", "₹11,500")
     * Always parses as string to handle Excel formatting correctly
     */
    private function parseFee($feeValue)
    {
        // If it's empty or null, return 0
        if (empty($feeValue) && $feeValue !== '0' && $feeValue !== 0) {
            return 0;
        }

        // ALWAYS convert to string first to preserve formatting from Excel
        // This handles cases where Excel has "Rs. 11,500" or "11,500" formatted
        $feeString = trim((string)$feeValue);
        
        // Handle empty string after trimming
        if ($feeString === '' || $feeString === '0') {
            return 0;
        }

        // Remove currency symbols (Rs., ₹, $, etc.), spaces, and other non-numeric characters except digits, commas, and dots
        $cleaned = preg_replace('/[^\d.,]/', '', $feeString);
        
        // Remove commas (thousand separators like "11,500")
        $cleaned = str_replace(',', '', $cleaned);
        
        // Handle cases where there might be multiple dots
        // If there are multiple dots, they might be thousand separators (some locales use . for thousands)
        $parts = explode('.', $cleaned);
        if (count($parts) > 2) {
            // Multiple dots - likely thousand separators, remove all dots
            $cleaned = str_replace('.', '', $cleaned);
        } elseif (count($parts) == 2) {
            // Single dot - check if it's a decimal point or thousand separator
            // If the part after dot has more than 2 digits, it's likely a thousand separator
            if (strlen($parts[1]) > 2) {
                // Remove the dot (it's a thousand separator)
                $cleaned = str_replace('.', '', $cleaned);
            }
            // Otherwise, keep it as decimal point
        }
        
        // Convert to float
        $fee = (float)$cleaned;
        
        // Log if the parsed value seems suspiciously small (might indicate parsing error)
        if ($fee > 0 && $fee < 1) {
            \Log::warning("Parsed fee is suspiciously small", [
                'original' => $feeValue,
                'cleaned' => $cleaned,
                'parsed' => $fee
            ]);
        }
        
        return $fee;
    }

    /**
     * Parse duration from text (e.g., "1 Year Program", "6 months", "2 years 3 months", "1 year 6 months")
     * Smart parser that extracts both years and months from a single text field
     */
    private function parseDuration($durationText)
    {
        $years = 0;
        $months = 0;

        if (empty($durationText)) {
            return ['years' => 0, 'months' => 0];
        }

        // Convert to string and trim
        $text = strtolower(trim((string)$durationText));
        
        if ($text === '') {
            return ['years' => 0, 'months' => 0];
        }

        // Extract years - handle "year", "years", "yr", "yrs", "year program", etc.
        // Use case-insensitive matching
        if (preg_match('/(\d+)\s*year/i', $text, $matches)) {
            $years = (int)$matches[1];
        } elseif (preg_match('/(\d+)\s*yr/i', $text, $matches)) {
            $years = (int)$matches[1];
        } elseif (preg_match('/(\d+)\s*y\b/i', $text, $matches)) {
            // Handle single "y" abbreviation (with word boundary to avoid matching "year")
            $years = (int)$matches[1];
        }

        // Extract months - handle "month", "months", "mo", "mos", etc.
        // Check for "months" first (longer match), then "month", then "mo"
        if (preg_match('/(\d+)\s*months?/i', $text, $matches)) {
            $months = (int)$matches[1];
        } elseif (preg_match('/(\d+)\s*mo\b/i', $text, $matches)) {
            $months = (int)$matches[1];
        } elseif (preg_match('/(\d+)\s*m\b/i', $text, $matches)) {
            // Handle single "m" abbreviation (with word boundary)
            // Only match if not already matched
            if ($months == 0) {
                $months = (int)$matches[1];
            }
        }

        // Special case: If only "Year Program" format without number, assume 1 year
        if ($years == 0 && $months == 0 && (stripos($text, 'year') !== false || stripos($text, 'program') !== false)) {
            $years = 1;
        }

        // Special case: If only "month" or "months" without number, assume 1 month
        if ($years == 0 && $months == 0 && (stripos($text, 'month') !== false)) {
            $months = 1;
        }

        // Debug: Log if we couldn't parse anything
        if ($years == 0 && $months == 0) {
            \Log::warning("Could not parse duration", [
                'text' => $durationText,
                'cleaned' => $text
            ]);
        }

        return ['years' => $years, 'months' => $months];
    }

    /**
     * Show form to manage subjects for a specific course semester
     */
    public function manageSemesterSubjects(Course $course, $semester)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                abort(401, 'You must be logged in to access this page.');
            }
            
            // Validate semester is a positive integer
            $semester = (int) $semester;
            if ($semester < 1) {
                abort(404, 'Invalid semester number.');
            }
            
            // Ensure course is loaded
            if (!$course || !$course->exists) {
                abort(404, 'Course not found.');
            }
            
            // Check permission
            if (!$user->isSuperAdmin()) {
                $userInstituteId = $user->institute_id ?? session('current_institute_id');
                $courseInstituteId = $course->institute_id;
                
                if ($userInstituteId != $courseInstituteId) {
                    abort(403, 'You are not authorized to manage subjects for this course.');
                }
            }

            // Get existing subjects for this semester
            $subjects = Subject::where('course_id', $course->id)
                ->where('semester', $semester)
                ->orderBy('name')
                ->get();

            return view('admin.courses.manage-semester-subjects', compact('course', 'semester', 'subjects'));
            
        } catch (\Exception $e) {
            \Log::error('Error in manageSemesterSubjects: ' . $e->getMessage(), [
                'course_id' => $course->id ?? null,
                'semester' => $semester ?? null,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            abort(500, 'An error occurred while loading the page. Please check the logs for details.');
        }
    }

    /**
     * Store subjects for a specific course semester
     */
    public function storeSemesterSubjects(Request $request, Course $course, $semester)
    {
        $user = Auth::user();
        
        // Check permission
        if (!$user->isSuperAdmin() && $course->institute_id !== $user->institute_id) {
            abort(403, 'You are not authorized to manage subjects for this course.');
        }

        $validated = $request->validate([
            'subjects' => ['required', 'array', 'min:1'],
            'subjects.*.name' => ['required', 'string', 'max:255'],
            'subjects.*.code' => ['required', 'string', 'max:255'],
            'subjects.*.theory_marks' => ['required', 'numeric', 'min:0'],
            'subjects.*.practical_marks' => ['required', 'numeric', 'min:0'],
        ]);

        // Delete existing subjects for this course and semester first
        // This prevents duplicate code errors since code has a unique constraint
        Subject::where('course_id', $course->id)
            ->where('semester', $semester)
            ->delete();

        // Create new subjects
        foreach ($validated['subjects'] as $subjectData) {
            $totalMarks = $subjectData['theory_marks'] + $subjectData['practical_marks'];
            
            Subject::create([
                'course_id' => $course->id,
                'semester' => $semester,
                'name' => $subjectData['name'],
                'code' => $subjectData['code'],
                'theory_marks' => $subjectData['theory_marks'],
                'practical_marks' => $subjectData['practical_marks'],
                'total_marks' => $totalMarks,
                'credits' => 0, // Default to 0 as credits are removed
                'status' => 'active',
            ]);
        }

        return redirect()->route('admin.courses.show', $course)
            ->with('success', "Subjects for Semester {$semester} saved successfully.");
    }
}
