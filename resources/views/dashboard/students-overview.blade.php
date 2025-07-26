<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Swinburne') }} | Study Progress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">

</head>
<body>
    <!-- Side Navigation -->
    <div class="side-nav">
    <div class="logo-container">
                <img src="{{ asset('asset/images/swinlogo.png') }}" alt="Swinburne logo">
            </div>
        <a href="{{ route('dashboard.unit.overview') }}" class="active">Study Progress</a>
        <a href="{{ route('dashboard.academic-planner') }}">Study Planner</a>     
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

        <div class="table-container">
        @php
            $statusOrder = ['Studying' => 1, 'Enrolled' => 2, 'Passed' => 3, 'Failed' => 4, 'Not Enrolled' => 5];

            // Reusable sorting function
            $sortUnits = function ($units) use ($statusOrder) {
                return $units
                    ->sortBy(fn($unit) => strtolower($unit->U_CODE)) // Step 1: Sort by Unit Code
                    ->sortBy(fn($unit) => $statusOrder[$unit->status] ?? 6); // Step 2: Then sort by Status
            };


            // Apply sorting to all unit types
            $sortedCoreUnits = $sortUnits($coreUnits);
            $sortedMajorUnits = $sortUnits($majorUnits);
            $sortedElectiveUnits = $sortUnits($electiveUnits);
        @endphp
            
            <!-- Core Units Table -->
            <h3>Core Units</h3>
            <table class="table table-bordered course-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Unit Code</th>
                        <th>Unit Name</th>
                        <th>Credit Points</th>
                        <th>Prerequisite</th>
                        <th>Status</th>
                        <th>No. of Enrollment</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sortedCoreUnits as $index => $unit)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $unit->U_CODE }}</td>
                        <td>{{ $unit->U_NAME }}</td>
                        <td>{{ $unit->CREDITS }}</td>
                        <td>{{ $unit->PREREQUISITE }}</td>
                        <td>
                            @if($unit->status == 'Passed')
                                <span class="status-completed">{{ $unit->status }}</span>
                            @elseif($unit->status == 'Enrolled' || $unit->status == 'Studying')
                                <span class="status-enrolled">{{ $unit->status }}</span>
                            @elseif($unit->status == 'Failed' || $unit->status == 'Not Enrolled')
                            <span class="status-failed future-schedule" data-unit="{{ $unit->U_CODE }}">
                                    {{ $unit->status }}
                                </span>
                                <div class="next-semester-info" id="semester-{{ $unit->U_CODE }}"></div>
                            @else
                                {{ $unit->status }}
                            @endif
                        </td>
                        <td>{{ $unit->enrollment_count }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Major Units Table -->
            <h3>Major Units</h3>
            <table class="table table-bordered course-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Unit Code</th>
                        <th>Unit Name</th>
                        <th>Credit Points</th>
                        <th>Prerequisite</th>
                        <th>Status</th>
                        <th>No. of Enrollment</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sortedMajorUnits as $index => $unit)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $unit->U_CODE }}</td>
                        <td>{{ $unit->U_NAME }}</td>
                        <td>{{ $unit->CREDITS }}</td>
                        <td>{{ $unit->PREREQUISITE }}</td>
                        <td>
                            @if($unit->status == 'Passed')
                                <span class="status-completed">{{ $unit->status }}</span>
                            @elseif($unit->status == 'Enrolled' || $unit->status == 'Studying')
                                <span class="status-enrolled">{{ $unit->status }}</span>
                            @elseif($unit->status == 'Failed')
                                <span class="status-failed">{{ $unit->status }}</span>
                            @elseif($unit->status == 'Failed' || $unit->status == 'Not Enrolled')
                                <span class="status-failed future-schedule" data-unit="{{ $unit->U_CODE }}">
                                    {{ $unit->status }}
                                </span>
                                <div class="next-semester-info" id="semester-{{ $unit->U_CODE }}"></div>
                            @else
                                {{ $unit->status }}
                            @endif
                        </td>
                        <td>{{ $unit->enrollment_count }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Elective Units Table -->
            <h3>Elective Units</h3>
            <table class="table table-bordered course-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Unit Code</th>
                        <th>Unit Name</th>
                        <th>Credit Points</th>
                        <th>Prerequisite</th>
                        <th>Status</th>
                        <th>No. of Enrollment</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sortedElectiveUnits as $index => $unit)                  
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $unit->U_CODE }}</td>
                            <td>{{ $unit->U_NAME }}</td>
                            <td>{{ $unit->CREDITS }}</td>
                            <td>{{ $unit->PREREQUISITE }}</td>
                            <td>
                                @if($unit->status == 'Passed')
                                    <span class="status-completed">{{ $unit->status }}</span>
                                @elseif($unit->status == 'Enrolled' || $unit->status == 'Studying')
                                    <span class="status-enrolled">{{ $unit->status }}</span>
                                @elseif($unit->status == 'Failed')
                                    <span class="status-failed future-schedule" data-unit="{{ $unit->U_CODE }}">
                                        {{ $unit->status }}
                                    </span>
                                    <div class="next-semester-info" id="semester-{{ $unit->U_CODE }}"></div>
                                @else
                                    {{ $unit->status }}
                                @endif
                            </td>
                            <td>{{ $unit->enrollment_count }}</td>
                        </tr>
                    @endforeach

                    {{-- Fill remaining rows with "Elective" as a single merged column --}}
                    @for ($i = count($sortedElectiveUnits) + 1; $i <= 8; $i++)
                        <tr>
                            <td>{{ $i }}</td>
                            <td colspan="6" class="elective-text">Elective Unit</td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>

    <script id="unitSchedulesData" type="application/json">
        {!! json_encode($unitSchedules) !!}
    </script>
    <script> window.unitSchedules = @json($unitSchedules); </script>
    <script src="{{ asset('js/schedule-tooltip.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>