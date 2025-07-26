<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentMajor;
use App\Models\Major;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AcademicPlannerController extends Controller
{
    private function fetchSelectedUnits($studentId) {
        // Get student's major
        $student = StudentMajor::where('student_id', $studentId)->firstOrFail();
        
        return DB::table('a_student_enroll')
            ->join('a_course_unit', 'a_student_enroll.u_code', '=', 'a_course_unit.U_CODE')
            ->leftJoin('a_unit_major', function($join) use ($student) {
                $join->on('a_student_enroll.u_code', '=', 'a_unit_major.u_code')
                     ->where('a_unit_major.m_code', '=', $student->m_code); // 🔥 Ensure only the student's major is considered
            })
            ->where('a_student_enroll.student_id', $studentId)
            ->select(
                'a_student_enroll.u_code',
                'a_course_unit.U_NAME as unit_name',
                'a_course_unit.CREDITS as credits',
                'a_course_unit.PREREQUISITE',
                DB::raw("COALESCE(a_unit_major.type, 'Elective') as type"), // If unit is not in major, mark it as elective
                'a_student_enroll.semester_month',
                'a_student_enroll.semester_year'
            )
            ->distinct()
            ->get();
    }

    private function getNotEnrolledUnits($student) {
        return DB::table('a_unit_major')
            ->join('a_course_unit', 'a_unit_major.u_code', '=', 'a_course_unit.U_CODE')
            ->leftJoin('a_student_unit_status', function ($join) use ($student) {
                $join->on('a_student_unit_status.unit_id', '=', 'a_course_unit.U_CODE')
                     ->where('a_student_unit_status.student_id', '=', $student->student_id);
            })
            ->where('a_unit_major.m_code', $student->m_code)
            ->whereNull('a_student_unit_status.unit_id')
            ->select(
                'a_course_unit.U_CODE', 
                'a_course_unit.U_NAME', 
                'a_unit_major.type', 
                'a_course_unit.CREDITS',
                'a_course_unit.PREREQUISITE'
            )
            ->distinct()
            ->get();
    }    
    
    public function index()
    {
        $studentName = config('student.name');
        $student = StudentMajor::where('student_name', $studentName)->firstOrFail();
        
        // Fetch the currently enrolled units for this student
    
        // Fetch all NOT enrolled units
        $notEnrolledUnits = $this->getNotEnrolledUnits($student);

        // Separate core units
        $coreUnits = $notEnrolledUnits->where('type', 'Core');

        // Fetch NOT enrolled elective units
        $notEnrolledElectiveUnits = DB::table('a_course_unit')
            ->whereNotIn('U_CODE', function ($query) use ($student) {
                $query->select('u_code')->from('a_unit_major')->where('m_code', $student->m_code);
            })
            ->whereNotIn('U_CODE', function ($query) use ($student) {
                $query->select('unit_id')->from('a_student_unit_status')->where('student_id', $student->student_id);
            })
            ->select('U_CODE', 'U_NAME', DB::raw("'Elective' as type"), 'CREDITS', 'PREREQUISITE')
            ->distinct()
            ->get();

        // Fetch major units separately
        $majorUnits = DB::table('a_unit_major')
            ->join('a_course_unit', 'a_unit_major.u_code', '=', 'a_course_unit.U_CODE')
            ->where('a_unit_major.m_code', $student->m_code)
            ->where('a_unit_major.type', 'major')
            ->select('a_course_unit.U_CODE', 'a_course_unit.U_NAME', 'a_course_unit.CREDITS', 'a_course_unit.PREREQUISITE', DB::raw("'Major' as type"))
            ->distinct()
            ->get();

        // Merge all unit types
        $allUnits = $notEnrolledUnits->merge($notEnrolledElectiveUnits);

        // Fetch future unit schedules
        $unitSchedules = DB::table('a_unit_semester')
            ->whereIn('u_code', $allUnits->pluck('U_CODE')->toArray())
            ->where(fn($query) => $query->where('semester_year', '>', now()->year)
                ->orWhere(fn($query) => $query->where('semester_year', '=', now()->year)
                    ->whereRaw("FIELD(semester_month, 'Jan', 'May', 'Sep') > FIELD(?, 'Jan', 'May', 'Sep')", [now()->format('M')])))
            ->select('u_code', 'semester_month', 'semester_year')
            ->orderBy('semester_year')->orderByRaw("FIELD(semester_month, 'Jan', 'May', 'Sep')")
            ->get()
            ->groupBy('u_code');

        
        $semesterOrder = ['May' => 5, 'Sep' => 9, 'Jan' => 1];

        $futureSemesters = collect($unitSchedules)
            ->flatMap(fn($schedules, $unitCode) => collect($schedules)->map(fn($schedule) => [
                'unit_code'  => $unitCode,
                'unit_name'  => optional($coreUnits->where('U_CODE', $unitCode)->first())->U_NAME 
                    ?: optional($majorUnits->where('U_CODE', $unitCode)->first())->U_NAME 
                    ?: optional($notEnrolledElectiveUnits->where('U_CODE', $unitCode)->first())->U_NAME 
                    ?: 'Unknown Unit',
                'credits'    => optional($coreUnits->where('U_CODE', $unitCode)->first())->CREDITS
                    ?: optional($majorUnits->where('U_CODE', $unitCode)->first())->CREDITS
                    ?: optional($notEnrolledElectiveUnits->where('U_CODE', $unitCode)->first())->CREDITS
                    ?: '-',
                'semester'   => $schedule->semester_month,
                'year'       => $schedule->semester_year,
                'type'       => $coreUnits->where('U_CODE', $unitCode)->isNotEmpty() ? 'Core' 
                            : ($majorUnits->where('U_CODE', $unitCode)->isNotEmpty() ? 'Major' : 'Elective')
            ]))
            ->filter(fn($s) => now()->lt(\Carbon\Carbon::create($s['year'], $semesterOrder[$s['semester']] ?? 1, 1)))
            ->sortBy(function($s) use ($semesterOrder) {
                // Create a sortable value that combines year and month
                return ($s['year'] * 12) + ($semesterOrder[$s['semester']] ?? 1);
            })
            ->groupBy(fn($s) => "{$s['semester']} {$s['year']}");
            
        // Get selected units grouped by semester
        $selectedUnits = $this->fetchSelectedUnits($student->student_id)
            ->sortBy([['semester_year', 'asc'], fn($s) => $semesterOrder[$s->semester_month] ?? 1])
            ->groupBy(fn($s) => "{$s->semester_month} {$s->semester_year}");

        // Make sure future semesters with no selected units are also included
        foreach ($futureSemesters as $semesterName => $units) {
            if (!$selectedUnits->has($semesterName)) {
                // Add empty collection for semesters with no selected units
                $selectedUnits[$semesterName] = collect([]);
            }
        }

        // Create a temporary collection with proper indexes for sorting
        $tempSelectedUnits = collect();
        foreach ($selectedUnits as $semesterName => $units) {
            list($month, $year) = explode(' ', $semesterName);
            $tempSelectedUnits[] = [
                'semester' => $month,
                'year' => (int)$year,
                'name' => $semesterName,
                'units' => $units
            ];
        }

        // Apply the same sorting logic used for futureSemesters
        $sortedSelectedUnits = $tempSelectedUnits
            ->sortBy(function($s) use ($semesterOrder) {
                // Create a sortable value that combines year and month
                return ($s['year'] * 12) + ($semesterOrder[$s['semester']] ?? 1);
            })
            ->pluck('units', 'name');

        // Replace with sorted collection
        $selectedUnits = $sortedSelectedUnits;

        return view('dashboard.academic-planner', array_merge(
            compact(
                'coreUnits', 'majorUnits', 'notEnrolledElectiveUnits', 
                'unitSchedules', 'allUnits', 'semesterOrder', 
                'futureSemesters', 'student'
                ),
            ['selectedUnits' => $selectedUnits]
        ));
    }

    public function getSelectedUnits($studentId) {
        try {
            $selectedUnits = $this->fetchSelectedUnits($studentId);
    
            return response()->json($selectedUnits);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching selected units'], 500);
        }
    }
    
    public function savePlan(Request $request) {
        try {
            // Decode JSON request body
            $jsonContent = $request->json()->all(); // 🔥 FIX: Now $jsonContent is assigned properly
    
            // Validate required fields
            if (!isset($jsonContent['student_id']) || !isset($jsonContent['units'])) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Invalid request. Missing student_id or units.'
                ], 400);
            }
    
            // Process enrollment
            $studentId = $jsonContent['student_id'];
            $units = $jsonContent['units'];
            
            // Start a database transaction
            DB::beginTransaction();
    
            // Group units by semester
            $semesterGroups = collect($units)->groupBy(function($unit) {
                return $unit['semester_month'] . '_' . $unit['semester_year'];
            });
    
            // Delete existing enrollments for each semester
            foreach ($semesterGroups as $semesterKey => $semesterUnits) {
                // Extract semester month and year
                list($semester_month, $semester_year) = explode('_', $semesterKey);
    
                // Delete existing enrollments for this specific student and semester
                DB::table('a_student_enroll')
                    ->where('student_id', $studentId)
                    ->where('semester_month', $semester_month)
                    ->where('semester_year', $semester_year)
                    ->delete();
    
                // Insert new enrollments for this semester
                foreach ($semesterUnits as $unit) {
                    DB::table('a_student_enroll')->insert([
                        'student_id' => $studentId,
                        'u_code' => $unit['u_code'],
                        'semester_month' => $semester_month,
                        'semester_year' => $semester_year
                    ]);
                }
            }
    
            // Commit the transaction
            DB::commit();
    
            // Log successful save
            \Log::info('Academic plan saved successfully', [
                'student_id' => $studentId,
                'units_count' => count($units)
            ]);
    
            return response()->json(['success' => true]);
    
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollback();
    
            // Log the full error
            \Log::error('Save Plan Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return response()->json([
                'success' => false, 
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }    
}
