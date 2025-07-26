<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AcademicTeachingstaffsController extends Controller
{ 
    public function teachingStaffs(Request $request)
    {
        // Get filter parameters or use defaults
        $term = $request->query('term', 'Spring2025');
        $dateFrom = $request->query('from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->query('to', now()->endOfMonth()->format('Y-m-d'));
        
        // Mock data for assigned teaching staff
        $assignedStaff = collect([
            [
                'id' => 1,
                'name' => 'Nguyen Van Mot',
                'subject_code' => 'INF10003',
                'subject_name' => 'Introduction to Programming',
                'group_code' => 'INF10003',
                'planned_hours' => 33,
                'performed_hours' => 28
            ],
            [
                'id' => 2,
                'name' => 'Nguyen Van Hai',
                'subject_code' => 'SWE30010',
                'subject_name' => 'Software Engineering',
                'group_code' => 'SWE30010',
                'planned_hours' => 26,
                'performed_hours' => 33
            ],
            [
                'id' => 3,
                'name' => 'Nguyen Van Ba',
                'subject_code' => 'INF10002',
                'subject_name' => 'Database Concepts',
                'group_code' => 'INF10002',
                'planned_hours' => 33,
                'performed_hours' => 28
            ],
            [
                'id' => 4,
                'name' => 'Nguyen Van Bon',
                'subject_code' => 'INF10003',
                'subject_name' => 'Introduction to Programming',
                'group_code' => 'INF10003',
                'planned_hours' => 26,
                'performed_hours' => 33
            ],
            [
                'id' => 5,
                'name' => 'Nguyen Van Nam',
                'subject_code' => 'COS10005',
                'subject_name' => 'Web Development',
                'group_code' => 'COS10005',
                'planned_hours' => 33,
                'performed_hours' => 28
            ],
            [
                'id' => 6,
                'name' => 'Nguyen Van Sau',
                'subject_code' => 'ICT10001',
                'subject_name' => 'Information Technology',
                'group_code' => 'ICT10001',
                'planned_hours' => 26,
                'performed_hours' => 33
            ],
            [
                'id' => 7,
                'name' => 'Nguyen Van Bay',
                'subject_code' => 'TNE10005',
                'subject_name' => 'Network Administration',
                'group_code' => 'TNE10005',
                'planned_hours' => 33,
                'performed_hours' => 28
            ],
            [
                'id' => 8,
                'name' => 'Nguyen Van Tam',
                'subject_code' => 'COS1004',
                'subject_name' => 'Computer Systems',
                'group_code' => 'COS1004',
                'planned_hours' => 26,
                'performed_hours' => 33
            ]
        ]);
        
        // Mock data for available staff
        $availableStaff = collect([
            [
                'id' => 9,
                'name' => 'Nguyen Van E',
                'position' => 'Main Teaching staff',
                'skills' => [
                    ['skill_name' => 'web'],
                    ['skill_name' => 'Computer science'],
                    ['skill_name' => 'Network']
                ]
            ],
            [
                'id' => 10,
                'name' => 'Nguyen Van F',
                'position' => 'Main Teaching staff',
                'skills' => [
                    ['skill_name' => 'web'],
                    ['skill_name' => 'Computer science'],
                    ['skill_name' => 'Network']
                ]
            ],
            [
                'id' => 11,
                'name' => 'Nguyen Van G',
                'position' => 'Teaching staff',
                'skills' => [
                    ['skill_name' => 'Design'],
                    ['skill_name' => 'Data science'],
                    ['skill_name' => 'Comp-sci']
                ]
            ],
            [
                'id' => 12,
                'name' => 'Nguyen Van H',
                'position' => 'Main Teaching staff',
                'skills' => [
                    ['skill_name' => 'Design'],
                    ['skill_name' => 'Data science'],
                    ['skill_name' => 'Comp-sci']
                ]
            ],
            [
                'id' => 13,
                'name' => 'Nguyen Van K',
                'position' => 'Main Teaching staff',
                'skills' => [
                    ['skill_name' => 'web'],
                    ['skill_name' => 'Computer science'],
                    ['skill_name' => 'Network']
                ]
            ]
        ]);
        
        // Return view with mock data
        return view('dashboard.academic-teachingstaffs', [
            'assignedStaff' => $assignedStaff,
            'availableStaff' => $availableStaff,
            'term' => $term,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ]);
    }
}