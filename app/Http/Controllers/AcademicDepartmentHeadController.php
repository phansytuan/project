<?php

namespace App\Http\Controllers;

use App\Models\StudentCohort;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class AcademicDepartmentHeadController extends Controller
{
    public function index()
    {
        $studentcohort = DB::table('a_course as ac')
            ->join('a_major as am', 'ac.C_CODE', '=', 'am.COURSE')
            ->join('a_unit_major as aum', 'am.M_CODE', '=', 'aum.m_code')
            ->join('a_course_unit as acu', 'aum.u_code', '=', 'acu.U_CODE')
            ->join('a_unit_semester as aus', 'acu.U_CODE', '=', 'aus.u_code')
            ->leftJoin(DB::raw('(
                SELECT U_CODE, SUM(not_enrolled_or_failed_students) AS total_not_enrolled_or_failed
                FROM a_student_cohort_view
                GROUP BY U_CODE
            ) as scv'), 'acu.U_CODE', '=', 'scv.U_CODE')
            ->leftJoin(DB::raw('(
                SELECT unit_id, COUNT(*) AS total_enrolled
                FROM a_student_unit_status
                WHERE status = "Enrolled"
                GROUP BY unit_id
            ) as sus'), 'acu.U_CODE', '=', 'sus.unit_id')
            ->where('ac.C_CODE', 'BA-CS')
            ->where('aus.semester_month', 'May')
            ->where('aus.semester_year', 2025)
            ->select(
                'acu.U_CODE',
                'acu.U_NAME',
                DB::raw('COALESCE(scv.total_not_enrolled_or_failed, 0) AS estimate_no'),
                DB::raw('COALESCE(sus.total_enrolled, 0) AS actual_no')
            )
            ->distinct()
            ->get();
        
        return view('dashboard.academic-department-head',compact ('studentcohort'));
    }
    
    public function saveSchedule(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'courseId' => 'required|string',
                'lecturers' => 'required|array',
                'lecturers.*.name' => 'required|string',
                'lecturers.*.classes' => 'required|integer|min:1',
                'tutors' => 'required|array',
                'tutors.*.name' => 'required|string',
                'tutors.*.classes' => 'required|integer|min:1',
            ]);
            
            // In a real application, you would save this data to your database
            // For example:
            /*
            // First delete existing schedule for this course
            DB::table('course_schedules')
                ->where('course_id', $validatedData['courseId'])
                ->delete();
                
            // Insert lecturer schedules
            foreach ($validatedData['lecturers'] as $lecturer) {
                DB::table('course_schedules')->insert([
                    'course_id' => $validatedData['courseId'],
                    'staff_name' => $lecturer['name'],
                    'staff_type' => 'lecturer',
                    'num_classes' => $lecturer['classes'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Insert tutor schedules
            foreach ($validatedData['tutors'] as $tutor) {
                DB::table('course_schedules')->insert([
                    'course_id' => $validatedData['courseId'],
                    'staff_name' => $tutor['name'],
                    'staff_type' => 'tutor',
                    'num_classes' => $tutor['classes'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            */
            
            // Log successful save
            Log::info('Course schedule saved successfully', [
                'course_id' => $validatedData['courseId'],
                'lecturers_count' => count($validatedData['lecturers']),
                'tutors_count' => count($validatedData['tutors']),
            ]);
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error('Save Schedule Error', [
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
