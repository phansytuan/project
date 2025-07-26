<!-- resources/views/dashboard/academic-planner.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <input type="hidden" id="saveAcademicPlanRoute" value="{{ route('save.academic.plan') }}">
    <input type="hidden" id="studentId" value="{{ $student->student_id }}">
    <title>{{ config('app.name', 'Swinburne') }} | Academic Planner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">

<body>
    <!-- Side Navigation -->
    <div class="side-nav">
        <div class="logo-container">
            <img src="{{ asset('asset/images/swinlogo.png') }}" alt="Swinburne logo">
        </div>
        <a href="{{ route('dashboard.unit.overview') }}" >Study Progress</a>
        <a href="{{ route('dashboard.academic-planner') }}" class="active">Study Planner</a>    
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
    <div class="top-nav">
            <div>Swinburne Dashboard Students Overview</div>
            <div>Hi, {{ $student->student_name }} <i class="fas fa-user-circle"></i></div>
        </div>

        <div class="major-header">
            <div>{{ $course->C_NAME }} - {{ $major->M_NAME }} ({{ $major->M_CODE }})</div>
            <div>Passed Credits: {{ $totalCredits }} / 300</div>
        </div>

<!-- Semesters Flow -->
<div class="semesters-container"> <!-- NEW wrapper -->
    <div class="semesters-flow">
        @php
            // Get the earliest future semester dynamically
            $currentSemester = $selectedUnits->keys()->first() ?? null;
        @endphp

        <div class="semester-row">
            @foreach ($selectedUnits as $semesterName => $units)
                <div class="semester-box {{ $semesterName === $currentSemester ? 'current-semester' : '' }}" onclick="openSemesterDetails('{{ $semesterName }}')">
                    <div class="semester-header">{{ $semesterName }}</div>
                    <div class="semester-content">
                        @if ($units->count() > 0)
                            @foreach ($units->sortBy('type')->sortBy('u_code') as $unit)
                                <div class="unit-row">
                                    <div class="unit-type">{{ $unit->type }}</div>
                                    <div class="unit-details">
                                        <div>{{ $unit->u_code }}</div>
                                        <div>{{ $unit->unit_name }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <!-- Placeholder when no units are selected -->
                            <div class="unit-row">
                                <div class="unit-details text-center">
                                    <div>No units selected</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if (!$loop->last)
                    <div class="flow-arrow"></div>
                @endif
            @endforeach
        </div>
    </div>
</div>

<!-- Overlay -->
<div class="overlay" id="overlay" onclick="closePopups()"></div>

<!-- Semester Details Sidebar -->
<div class="sidebar-popup" id="semesterPopup">
    <div class="popup-close" onclick="closeSemesterDetails()">
        <i class="fas fa-times"></i>
    </div>
    <div class="popup-header">
        <h3 id="semesterTitle">Semester Details</h3>
    </div>
    <div class="popup-content">
        <!-- Selected Units Table -->
        <div id="unit-header">
            <h4>Selected Units</h4>
        </div>
        <table class="table table-bordered selectedUnitsTable" id="selectedUnitsTable">
            <thead>
                <tr>
                    <th>Unit Code</th>
                    <th>Unit Name</th>
                    <th>Credit</th>
                    <th>Type</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <div class="button-container">
            <button id="saveSelectionButton" class="save-button">Save Selection</button>
            <button id="enrollmentButton" class="enrollment-button">Submit Enrollment</button>
        </div>

        <!-- Available Core & Major Units Table -->
        <div id="unit-header">
            <h4>Core & Major Units </h4>
        </div>
    <table class="table table-bordered" id="coreMajorUnitsTable">
        <thead>
            <tr>
                <th>Unit Code</th>
                <th>Unit Name</th>
                <th>Credit Points</th>
                <th>Type</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <h4>Elective Units</h4>
    <button id="toggleElectiveButton" class="btn btn-secondary" onclick="toggleElectiveTable()">▲ Show Electives</button>

    <div id="electiveSection">
    <table class="table table-bordered" id="electiveUnitsTable">
    <thead>
        <tr>
            <th>Unit Code</th>
            <th>Unit Name</th>
            <th>Credit Points</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
</div>
</div>
</div>

<script>
    const semesterData = @json($futureSemesters);
    const availableUnits = @json($allUnits);

    const unitPrerequisites = {
    @foreach($allUnits as $unit)
        "{{ $unit->U_CODE }}": "{{ $unit->PREREQUISITE }}",
    @endforeach
    };

    let selectedUnits = @json($selectedUnits);
    var currentSemester = @json($currentSemester);
    var studentId = {{ $student->student_id }};
    var savePlanUrl = "{{ route('save.academic.plan') }}";
    var csrfToken = "{{ csrf_token() }}";
</script>

<script src="{{ asset('js/academic-planner.js') }}" defer></script>

</body>
</html><!-- resources/views/dashboard/academic-planner.blade.php -->
<!DOCTYPE html>
