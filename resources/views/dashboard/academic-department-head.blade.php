<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Swinburne') }} | Academic Department Head</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
    <div class="side-nav">
        <div class="logo-container">
            <img src="{{ asset('asset/images/swinlogo.png') }}" alt="Swinburne logo">
        </div>
        <a href="{{ route('dashboard.academic-department-head') }}" class="active">Teaching Planner</a>
        <a href="{{ route('dashboard.academic-teachingstaffs') }}">Teaching Workload</a>
    </div>
    
    <div class="main-content">
        <div class="top-nav">
            <div>Swinburne Dashboard Department Head</div>
            <div>Hi, Department Head <i class="fas fa-user-circle"></i></div>
        </div>
        <div class="major-header">
            <div>Teaching Planner</div>
        </div>
        
        <div class="container mt-4">
            <div class="course-container">
                <div class="course-header">
                    Ba-CS: Bachelor of Computer Science - May 2025
                </div>
                
                @foreach($studentcohort as $index => $unit)
                <div class="course-item">
                    <div class="row m-0 course-details" onclick="toggleCourseExpand('course{{ $index }}')">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>{{ $unit->U_CODE }}</strong>
                                </div>
                                <div class="col-md-9">
                                    {{ $unit->U_NAME }}
                                    <div>Assigned teacher: Cong</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 course-stats">
                            <div>
                                <small>Est. Students</small>
                                <div>{{ $unit->estimate_no }}</div>
                            </div>
                            <div>
                                <small>Est. Class</small>
                                <div>{{ ceil($unit->estimate_no / 20) }}</div>
                            </div>
                            <div>
                                <small>Act. Students</small>
                                <div>{{ $unit->actual_no }}</div>
                            </div>
                            <div>
                                <small>Act. Class</small>
                                <div>{{ ceil($unit->actual_no / 20) }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="course{{ $index }}" class="course-expanded">
                        <div class="staff-row">
                            <div id="lecturer-schedules{{ $index }}">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>
                        
                        <div class="staff-row">
                            <div id="tutor-schedules{{ $index }}">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>
                        
                        <div class="text-end mt-3">
                            <button class="btn btn-primary" onclick="saveSchedule({{ $index }})">Save Schedule</button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/academic-department-head.js') }}" defer></script>
</body>
</html>
