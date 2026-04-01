<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SemesterResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'marksheet_serial',
        'student_id',
        'course_id',
        'semester',
        'academic_year',
        'result_declaration_date',
        'date_of_issue',
        'total_subjects',
        'total_marks_obtained',
        'total_max_marks',
        'overall_percentage',
        'overall_grade',
        'status',
        'entered_by',
        'verified_by',
        'verified_at',
        'published_at',
        'remarks',
        'pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'marksheet_serial' => 'integer',
            'total_marks_obtained' => 'decimal:2',
            'total_max_marks' => 'decimal:2',
            'overall_percentage' => 'decimal:2',
            'result_declaration_date' => 'date',
            'date_of_issue' => 'date',
            'verified_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Padded Sr. No. for marksheet/PDF (uses marksheet_serial, falls back to id for legacy rows).
     */
    protected function formattedMarksheetSerial(): Attribute
    {
        return Attribute::get(function (): string {
            $stored = (int) ($this->marksheet_serial ?? $this->id);
            $n = $stored + (int) config('marksheet.serial_display_offset', 0);

            return str_pad((string) $n, 8, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Printed on marksheet next to "Semester/Year :" — e.g. "1ST SEMESTER / 1ST YEAR" (two semesters per year).
     */
    protected function marksheetSemesterYearLine(): Attribute
    {
        return Attribute::get(function (): string {
            $sem = max(1, (int) $this->semester);
            $yearNum = (int) ceil($sem / 2);

            return self::marksheetOrdinalUpper($sem).' SEMESTER / '.self::marksheetOrdinalUpper($yearNum).' YEAR';
        });
    }

    /**
     * Ordinal labels matching existing marksheet style (1ST, 2ND, 3RD, 4TH, …).
     */
    private static function marksheetOrdinalUpper(int $n): string
    {
        $n = max(1, $n);

        return match (true) {
            $n === 1 => '1ST',
            $n === 2 => '2ND',
            $n === 3 => '3RD',
            default => $n.'TH',
        };
    }

    /**
     * Next global marksheet serial (monotonic). Uses DB row lock so allocation stays correct inside transactions.
     * Call from within an open DB transaction when creating a semester result (recommended).
     */
    public static function nextMarksheetSerial(): int
    {
        $allocate = function (): int {
            $row = DB::table('marksheet_serial_sequences')->where('id', 1)->lockForUpdate()->first();
            if (! $row) {
                throw new \RuntimeException('marksheet_serial_sequences is not initialized (run migrations).');
            }
            $next = (int) $row->last_value + 1;
            DB::table('marksheet_serial_sequences')->where('id', 1)->update([
                'last_value' => $next,
                'updated_at' => now(),
            ]);

            return $next;
        };

        if (DB::transactionLevel() > 0) {
            return $allocate();
        }

        return (int) DB::transaction($allocate);
    }

    /**
     * Get the student this result belongs to
     */
    public function student()
    {
        return $this->belongsTo(Student::class)->withTrashed();
    }

    /**
     * Get the course this result is for
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get all subject results for this semester
     */
    public function results()
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Get the admin who entered this result
     */
    public function enteredBy()
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    /**
     * Get the admin who verified this result
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Calculate overall percentage
     */
    public function calculateOverall()
    {
        if ($this->total_max_marks > 0) {
            $this->overall_percentage = ($this->total_marks_obtained / $this->total_max_marks) * 100;
            // Grade calculation removed - not needed
        }
    }

    /**
     * Scope to get only truly published results
     * Ensures results went through the proper publish workflow
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTrulyPublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->whereNotNull('verified_at')
            ->whereNotNull('verified_by')
            ->where('published_at', '<=', now())
            ->where('verified_at', '<=', now())
            ->whereHas('results', function($q) {
                $q->where('status', 'published');
            });
    }

    /**
     * Check if this result is truly published
     * 
     * @return bool
     */
    public function isTrulyPublished()
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->verified_at !== null
            && $this->verified_by !== null
            && $this->published_at <= now()
            && $this->verified_at <= now()
            && $this->results()->where('status', 'published')->exists();
    }

    /**
     * Get academic year for a given admission session and semester (e.g. 2026-27 + sem 1 → 2026-27; sem 3 → 2027-28).
     */
    public static function getAcademicYearForSessionSemester(string $session, int $semester): string
    {
        $semester = max(1, (int) $semester);
        $yearIndex = (int) ceil($semester / 2) - 1;
        if ($yearIndex === 0) {
            return $session;
        }
        $parts = explode('-', trim($session));
        $startYear = isset($parts[0]) && is_numeric($parts[0]) ? (int) $parts[0] : (int) date('Y');
        $endShort = isset($parts[1]) && is_numeric($parts[1]) ? (int) $parts[1] : ($startYear % 100) + 1;
        $newStart = $startYear + $yearIndex;
        $newEnd = $endShort + $yearIndex;
        return $newStart . '-' . str_pad((string) $newEnd, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Calendar year when result/marksheet falls (academic_year 2026-27 → 2027).
     */
    public static function getResultCalendarYear(string $academicYear): int
    {
        if (preg_match('/^(\d{4})/', trim($academicYear), $m)) {
            return (int) $m[1] + 1;
        }
        return (int) date('Y');
    }

    /**
     * Default result declaration date (15 Feb or 15 Jul) for a session+semester.
     */
    public static function getDefaultResultDeclarationDate(string $session, int $semester): string
    {
        $academicYear = static::getAcademicYearForSessionSemester($session, $semester);
        $year = static::getResultCalendarYear($academicYear);
        $month = ($semester % 2) === 1 ? 2 : 7; // odd → Feb, even → Jul
        return sprintf('%04d-%02d-15', $year, $month);
    }

    /**
     * Default marksheet issue date (15 Mar or 15 Aug) for a session+semester.
     */
    public static function getDefaultMarksheetIssueDate(string $session, int $semester): string
    {
        $academicYear = static::getAcademicYearForSessionSemester($session, $semester);
        $year = static::getResultCalendarYear($academicYear);
        $month = ($semester % 2) === 1 ? 3 : 8; // odd → Mar, even → Aug
        return sprintf('%04d-%02d-15', $year, $month);
    }
}
