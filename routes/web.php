<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AcademicPlannerController; 
use App\Http\Controllers\AcademicDepartmentHeadController;
use App\Http\Controllers\AcademicTeachingstaffsController;
use App\Http\Controllers\AcademicDirectorController;
use App\Http\Controllers\LandingController;

Route::get('/', function (): mixed {
    return redirect()->route('dashboard.index');
});

// Dashboard routes
Route::prefix('dashboard')->name('dashboard.')->group(function (): void {
    Route::get('/', [LandingController::class, 'index'])->name('index');
    Route::get('/unit/overview', [DashboardController::class, 'unitOverview'])->name('unit.overview');
    Route::get('/academic-planner', [AcademicPlannerController::class, 'index'])->name('academic-planner'); 

    // New Academic Director routes
    Route::get('/academic-director', [AcademicDirectorController::class, 'index'])->name('academic-director');
});

Route::get('/get-selected-units/{studentId}', [AcademicPlannerController::class, 'getSelectedUnits'])->name('get.selected.units');
Route::post('/save-academic-plan', [AcademicPlannerController::class, 'savePlan'])
    ->name('save.academic.plan')
    ->middleware('web'); // Add web middleware to ensure CSRF protection

Route::get('/dashboard/academic-department-head', [AcademicDepartmentHeadController::class, 'index'])
    ->name('dashboard.academic-department-head');

// API endpoint to save course schedules
Route::post('/api/save-course-schedule', [AcademicDepartmentHeadController::class, 'saveSchedule'])
    ->name('save.course.schedule')
    ->middleware('web');

// Teaching staffs route
Route::get('/dashboard/academic-teachingstaffs', [AcademicTeachingstaffsController::class, 'teachingStaffs'])
    ->name('dashboard.academic-teachingstaffs');

// New API endpoints for Academic Director
Route::get('/api/semester-info/{semesterId}', [AcademicDirectorController::class, 'getSemesterInfo'])
    ->name('get.semester.info');

// Academic Director drop unit functionality
Route::post('/api/drop-unit', [AcademicDirectorController::class, 'dropUnit'])
    ->name('academic-director.drop-unit')
    ->middleware('web');

// Academic Director replace unit functionality
Route::post('/api/replace-unit', [AcademicDirectorController::class, 'replaceUnit'])
    ->name('academic-director.replace-unit')
    ->middleware('web');

// Academic Director save all changes
Route::post('/api/save-all-changes', [AcademicDirectorController::class, 'saveAll'])
    ->name('academic-director.save-all')
    ->middleware('web');

// Save academic director changes
Route::post('/api/save-academic-director', [AcademicDirectorController::class, 'save'])
    ->name('save.academic.director')
    ->middleware('web');

