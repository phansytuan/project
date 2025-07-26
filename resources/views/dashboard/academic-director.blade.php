<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Swinburne') }} | Academic Director</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <style>
        .drop-btn {
            background-color: #ff4757;
            color: white;
            border: none;
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
        }
        .unit-dropdown {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ced4da;
        }
        .course-stats {
            display: flex;
            justify-content: space-between;
        }
        .course-stats div {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="side-nav">
        <div class="logo-container">
            <img src="{{ asset('asset/images/swinlogo.png') }}" alt="Swinburne logo">
        </div>
        <a href="{{ route('dashboard.academic-director') }}" class="active">Unit planner overview</a>
        <a href="{{ route('dashboard.unit.overview') }}" >Student Progress</a>
        <a href="{{ route('dashboard.academic-planner') }}">Student Planner</a>     
        <a href="{{ route('dashboard.academic-department-head') }}">Department Head</a>
    </div>
    
    <div class="main-content">
        <div class="top-nav">
            <div>Swinburne Dashboard Academic Director</div>
            <div>Hi, Academic Director <i class="fas fa-user-circle"></i></div>
        </div>
        <div class="major-header">
            <div>Unit planner overview</div>
        </div>
        
        <div class="container mt-4">
            <div class="course-container">
                <div class="course-header">
                    Ba-CS: Bachelor of Computer Science - May 2025
                </div>
                
                @foreach($studentcohort as $index => $unit)
                <div class="course-item" id="course-item-{{ $index }}">
                    @if(!isset($unit->dropped) || !$unit->dropped)
                    <div class="row m-0 course-details">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>{{ $unit->U_CODE }}</strong>
                                </div>
                                <div class="col-md-9">
                                    {{ $unit->U_NAME }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 course-stats">
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
                        <div class="col-md-1 text-end">
                            <button class="drop-btn" onclick="dropUnit({{ $index }})">Drop</button>
                        </div>
                    </div>
                    @else
                    <div class="row m-0 p-3">
                        <div class="col-md-11">
                            <select class="unit-dropdown" id="replacement-unit-{{ $index }}">
                                <option value="">Select replacement unit</option>
                                @foreach($availableUnits as $availableUnit)
                                <option value="{{ $availableUnit->id }}">{{ $availableUnit->U_CODE }} - {{ $availableUnit->U_NAME }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 text-end">
                            <button class="btn btn-primary" onclick="replaceUnit({{ $index }})">Save</button>
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            <div class="text-end mt-4 mb-4">
                <button class="btn btn-primary" onclick="saveAllChanges()">Save All Changes</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // CSRF token setup for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Function to drop a unit and show the replacement dropdown
        function dropUnit(index) {
            $.ajax({
                url: '{{ route("academic-director.drop-unit") }}',
                type: 'POST',
                data: { index: index },
                success: function(response) {
                    if (response.success) {
                        let html = `
                            <div class="row m-0 p-3">
                                <div class="col-md-11">
                                    <select class="unit-dropdown" id="replacement-unit-${index}">
                                        <option value="">Select replacement unit</option>`;
                                
                        response.availableUnits.forEach(unit => {
                            html += `<option value="${unit.id}">${unit.U_CODE} - ${unit.U_NAME}</option>`;
                        });
                                
                        html += `</select>
                                </div>
                                <div class="col-md-1 text-end">
                                    <button class="btn btn-primary" onclick="replaceUnit(${index})">Save</button>
                                </div>
                            </div>`;
                        
                        $(`#course-item-${index}`).html(html);
                    } else {
                        alert('Failed to drop unit. Please try again.');
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        }

        // Function to replace a dropped unit with a new selection
        function replaceUnit(index) {
            const unitId = $(`#replacement-unit-${index}`).val();
            if (!unitId) {
                alert('Please select a replacement unit.');
                return;
            }

            $.ajax({
                url: '{{ route("academic-director.replace-unit") }}',
                type: 'POST',
                data: { 
                    index: index,
                    unit_id: unitId
                },
                success: function(response) {
                    if (response.success) {
                        // Refresh the page to show the updated unit
                        window.location.reload();
                    } else {
                        alert('Failed to replace unit. Please try again.');
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        }

        // Function to save all changes made to the teaching plan
        function saveAllChanges() {
            $.ajax({
                url: '{{ route("academic-director.save-all") }}',
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        alert('All changes saved successfully!');
                        window.location.reload();
                    } else {
                        alert('Failed to save changes. Please try again.');
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        }
    </script>
</body>
</html>