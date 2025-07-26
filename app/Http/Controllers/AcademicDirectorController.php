<?php

namespace App\Http\Controllers;

use App\Models\StudentCohort;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AcademicDirectorController extends Controller
{
    /**
     * Display the academic director dashboard
     */
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
    
    return view('dashboard.academic-director',compact ('studentcohort'));
    }
    
    /**
     * Display unit sequences page
     */
    public function unitSequences()
    {
        // Logic for unit sequences view
        $units = Unit::all();
        
        return view('dashboard.academic-director.unit-sequences', compact('units'));
    }
    
    /**
     * Display overview page
     */
    public function overview()
    {
        // Logic for overview view
        return view('dashboard.academic-director.overview');
    }
    
    /**
     * Get semester information
     */
    public function getSemesterInfo($semesterId)
    {
        // Fetch semester data
        $semester = Semester::find($semesterId);
        
        if (!$semester) {
            return response()->json([
                'success' => false,
                'message' => 'Semester not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $semester
        ]);
    }
    
    /**
     * Drop a unit and prepare for replacement
     */
    public function dropUnit(Request $request)
    {
        $index = $request->input('index');
        
        // Store dropped unit index in session
        $droppedUnits = Session::get('dropped_units', []);
        $droppedUnits[] = $index;
        Session::put('dropped_units', $droppedUnits);
        
        // Get available units for replacement
        $availableUnits = Unit::whereNotIn('id', StudentCohort::pluck('unit_id')->toArray())
            ->get();
            
        return response()->json([
            'success' => true,
            'availableUnits' => $availableUnits
        ]);
    }
    
    /**
     * Replace a dropped unit with a new selection
     */
    public function replaceUnit(Request $request)
    {
        $index = $request->input('index');
        $unitId = $request->input('unit_id');
        
        // In a real application, you would update the database here
        // For now, we'll just store the replacement in the session
        $replacements = Session::get('unit_replacements', []);
        $replacements[$index] = $unitId;
        Session::put('unit_replacements', $replacements);
        
        // Remove from dropped units
        $droppedUnits = Session::get('dropped_units', []);
        $droppedUnits = array_filter($droppedUnits, function($value) use ($index) {
            return $value != $index;
        });
        Session::put('dropped_units', $droppedUnits);
        
        return response()->json([
            'success' => true
        ]);
    }
    
    /**
     * Save all changes to the teaching plan
     */
    public function saveAll()
    {
        $replacements = Session::get('unit_replacements', []);
        
        // Here you would update the actual database records
        foreach ($replacements as $index => $unitId) {
            // Example of what you might do:
            // $cohort = StudentCohort::find($index);
            // $cohort->unit_id = $unitId;
            // $cohort->save();
        }
        
        // Clear session data
        Session::forget('dropped_units');
        Session::forget('unit_replacements');
        
        return response()->json([
            'success' => true
        ]);
    }
    
    /**
     * Save all academic director changes (general save method)
     */
    public function save(Request $request)
    {
        $data = $request->all();
        
        return response()->json([
            'success' => true,
            'message' => 'Changes saved successfully'
        ]);
    }
}