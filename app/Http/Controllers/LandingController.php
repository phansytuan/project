<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Show the landing page with user type selection.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('dashboard.landing');
    }
    
    /**
     * Redirect to student dashboard.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function studentRedirect()
    {
        return redirect()->route('dashboard.academic-planner');
    }
    
    /**
     * Redirect to department head dashboard.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function departmentHeadRedirect()
    {
        return redirect()->route('dashboard.academic-department-head');
    }
    
    /**
     * Redirect to academic director dashboard.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function academicDirectorRedirect()
    {
        return redirect()->route('dashboard.academic-director');
    }
}