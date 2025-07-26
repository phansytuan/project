<!-- resources/views/dashboard/academic-teachingstaffs.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Swinburne') }} | Teaching Staffs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
    <!-- Side Navigation -->
    <div class="side-nav">
        <div class="logo-container">
            <img src="{{ asset('asset/images/swinlogo.png') }}" alt="Swinburne logo">
        </div>
        <a href="{{ route('dashboard.academic-department-head') }}" >Teaching Planner</a>
        <a href="{{ route('dashboard.academic-teachingstaffs') }}"class="active">Teaching Workload</a>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="top-nav">
            <div>Swinburne Academic View</div>
            <div>Hi, Nguyen Van C <i class="fas fa-user-circle"></i></div>
        </div>
        
        <div class="major-header">
            <div>Teaching staffs</div>
        </div>

        <div class="container mt-4">
            <!-- Term and Date Selection -->
            <div class="filter-container p-3 mb-4 bg-dark text-white">
                <div class="row align-items-center">
                    <div class="col-md-2">
                        <label for="term">Semester:</label>
                        <select id="term" class="form-select">
                            <option selected>Spring2025</option>
                            <option>Fall2024</option>
                            <option>Summer2024</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="dateFrom">From:</label>
                        <input type="date" id="dateFrom" class="form-control" value="2025-02-01">
                    </div>
                    <div class="col-md-3">
                        <label for="dateTo">To:</label>
                        <input type="date" id="dateTo" class="form-control" value="2025-02-28">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-light mt-4">View</button>
                    </div>
                </div>
            </div>

            <!-- Current Teaching Staff Table -->
            <div class="current-staff-container mb-5">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="bg-danger text-white">No</th>
                                <th class="bg-danger text-white">Lecturer</th>
                                <th class="bg-danger text-white">Subject</th>
                                <th class="bg-danger text-white">GROUP</th>
                                <th class="bg-danger text-white">Plan</th>
                                <th class="bg-danger text-white">Performed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignedStaff as $index => $staff)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $staff['name'] }}</td>
                                <td>{{ $staff['subject_code'] }}</td>
                                <td>{{ $staff['group_code'] }}</td>
                                <td>{{ $staff['planned_hours'] }}</td>
                                <td>{{ $staff['performed_hours'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

           <!-- Available Staffs Section -->
    <div class="available-staffs-container">
        <div class="available-header bg-dark text-white p-3 d-flex justify-content-between align-items-center">
            <h3 class="m-0">Available staffs</h3>
        </div>
        
        <div class="staff-cards-container mt-3">
            <div class="row">
                @foreach($availableStaff as $staff)
                <!-- Staff Card -->
                <div class="col-md-4 mb-4">
                    <div class="card staff-card">
                        <div class="card-body bg-danger bg-opacity-25">
                            <div class="text-center mb-3">
                                <div class="staff-avatar bg-warning rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                    <i class="fas fa-user fa-3x text-dark"></i>
                                </div>
                            </div>
                            <h4 class="text-center">{{ $staff['name'] }}</h4>
                            <p class="text-center text-primary">{{ $staff['position'] }}</p>
                            <div class="staff-skills d-flex justify-content-center gap-2 mb-3">
                                @foreach($staff['skills'] as $skill)
                                <span class="badge bg-light text-dark p-2">{{ $skill['skill_name'] }}</span>
                                @endforeach
                            </div>
                            <div class="text-center">
                                <button class="btn btn-success" data-staff-id="{{ $staff['id'] }}">Assign</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
</div>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/academic-teachingstaffs.js') }}" defer></script>
    
    <script>
        // Toggle available staffs section
        document.querySelector('.available-header').addEventListener('click', function() {
            const staffCardsContainer = document.querySelector('.staff-cards-container');
            const chevronIcon = this.querySelector('.fa-chevron-down');
            
            if (staffCardsContainer.style.display === 'none') {
                staffCardsContainer.style.display = 'block';
                chevronIcon.classList.replace('fa-chevron-down', 'fa-chevron-up');
            } else {
                staffCardsContainer.style.display = 'none';
                chevronIcon.classList.replace('fa-chevron-up', 'fa-chevron-down');
            }
        });
        
        // Assign button functionality
        document.querySelectorAll('.btn-success').forEach(button => {
            button.addEventListener('click', function() {
                const staffName = this.closest('.card-body').querySelector('h4').textContent;
                alert(`Assign ${staffName} to a course. This will be connected to backend later.`);
            });
        });
    </script>
</body>
</html>