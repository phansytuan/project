<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentMajor;
use App\Models\Major;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

class DashboardServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share student data across all views
        View::composer('*', function ($view) {
            $studentName = config('student.name'); // Adjust this based on your auth system
            $student = StudentMajor::where('student_name', $studentName)->first();

            if ($student) {
                $major = Major::with('course')->where('M_CODE', $student->m_code)->first();
                $course = $major->course;

                // Calculate total credits passed
                $totalCredits = floatval(DB::table('a_student_unit_status')
                    ->join('a_course_unit', 'a_student_unit_status.unit_id', '=', 'a_course_unit.U_CODE')
                    ->where('a_student_unit_status.student_id', $student->student_id)
                    ->where('a_student_unit_status.status', 'Passed')
                    ->sum('a_course_unit.CREDITS'));

                $view->with([
                    'student' => $student,
                    'major' => $major,
                    'course' => $course,
                    'totalCredits' => $totalCredits
                ]);
            }
        });
    }
}
