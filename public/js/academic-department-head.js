// academic-department-head.js
document.addEventListener('DOMContentLoaded', function() {
    // Populate the staff dropdown options
    populateStaffDropdowns();
    
    // Initialize the course items
    document.querySelectorAll('.course-item').forEach(function(item) {
        const courseExpanded = item.querySelector('.course-expanded');
        if (courseExpanded) {
            const courseId = courseExpanded.id;
            initializeStaffSection(courseId);
        }
    });
});

// Function to populate the lecturer and tutor dropdowns with data
function populateStaffDropdowns() {
    // In a real application, this data would come from an AJAX call to your backend
    const lecturers = [
        { id: 1, name: "Dr. John Smith" },
        { id: 2, name: "Dr. Emily Johnson" },
        { id: 3, name: "Prof. Michael Williams" },
        { id: 4, name: "Dr. Sarah Davis" },
        { id: 5, name: "Prof. Robert Chen" }
    ];
    
    const tutors = [
        { id: 1, name: "Alex Turner" },
        { id: 2, name: "Jessica Lee" },
        { id: 3, name: "Mark Wilson" },
        { id: 4, name: "Lisa Chen" },
        { id: 5, name: "David Garcia" }
    ];
    
    // Store the data globally for later use when creating new dropdown elements
    window.staffData = {
        lecturers: lecturers,
        tutors: tutors
    };
}

// Create options HTML for a staff dropdown
function createStaffOptionsHTML(role) {
    const staffList = window.staffData[role + 's']; // lecturers or tutors
    let optionsHTML = '<option value="">Select a ' + role + '</option>';
    
    staffList.forEach(staff => {
        optionsHTML += `<option value="${staff.name}">${staff.name}</option>`;
    });
    
    return optionsHTML;
}

// Toggle course expanded section
function toggleCourseExpand(courseId) {
    const courseExpanded = document.getElementById(courseId);
    if (courseExpanded.style.display === 'block') {
        courseExpanded.style.display = 'none';
    } else {
        // Hide all other expanded sections
        document.querySelectorAll('.course-expanded').forEach(function(item) {
            item.style.display = 'none';
        });
        courseExpanded.style.display = 'block';
    }
}

// Initialize staff section with one lecturer and one tutor
function initializeStaffSection(courseId) {
    // Extract the course number from the courseId
    const courseNum = courseId.replace('course', '');
    
    // Clear existing content in the containers
    const lecturerContainer = document.getElementById(`lecturer-schedules${courseNum}`);
    const tutorContainer = document.getElementById(`tutor-schedules${courseNum}`);
    
    if (lecturerContainer) lecturerContainer.innerHTML = '';
    if (tutorContainer) tutorContainer.innerHTML = '';
    
    // Initialize with just one lecturer and one tutor
    addStaffMember('lecturer', courseNum);
    addStaffMember('tutor', courseNum);
}

// Add a staff member (lecturer or tutor)
function addStaffMember(role, courseNum) {
    const container = document.getElementById(`${role}-schedules${courseNum}`);
    if (!container) return;
    
    const newIndex = container.children.length;
    
    const staffRow = document.createElement('div');
    staffRow.className = 'schedule-form-group';
    staffRow.id = `${role}-row-${courseNum}-${newIndex}`;
    
    // Use a select dropdown instead of an input text field
    staffRow.innerHTML = `
        <label for="${role}${courseNum}-${newIndex}">${newIndex === 0 ? role.charAt(0).toUpperCase() + role.slice(1) + ':' : ''}</label>
        <select id="${role}${courseNum}-${newIndex}" class="form-control" style="width: 200px;" required>
            ${createStaffOptionsHTML(role)}
        </select>
        <label for="${role}-classes${courseNum}-${newIndex}" style="margin-left: 20px;">Number of classes</label>
        <input type="number" id="${role}-classes${courseNum}-${newIndex}" class="form-control" style="width: 80px;" min="1" required>
        <button class="btn btn-circle btn-green ms-2" onclick="addStaffMember('${role}', ${courseNum})"><i class="fas fa-plus"></i></button>
        ${newIndex > 0 ? `<button class="btn btn-circle btn-red ms-2" onclick="removeStaffMember('${role}', ${courseNum}, ${newIndex})"><i class="fas fa-minus"></i></button>` : ''}
    `;
    
    container.appendChild(staffRow);
}

// Remove a staff member
function removeStaffMember(role, courseNum, index) {
    // Prevent removing the first staff member (index 0)
    if (index === 0) return;
    
    const staffRow = document.getElementById(`${role}-row-${courseNum}-${index}`);
    if (staffRow) {
        staffRow.remove();
    }
}

// Add schedule input fields when the plus button is clicked
function addSchedule(role, courseNum) {
    // Get the form values
    const rowIndex = document.getElementById(`${role}-schedules${courseNum}`).children.length - 1;
    const nameSelect = document.getElementById(`${role}${courseNum}-${rowIndex}`);
    const classesInput = document.getElementById(`${role}-classes${courseNum}-${rowIndex}`);
    const numClasses = parseInt(classesInput.value) || 0;
    
    if (!nameSelect || nameSelect.value === '') {
        alert('Please select a staff member first');
        return;
    }
    
    if (numClasses <= 0) {
        alert('Please enter a valid number of classes');
        return;
    }
    
    // Find or create a container for this staff member's schedule
    let scheduleContainer = document.getElementById(`${role}-schedule-container-${courseNum}-${rowIndex}`);
    
    if (!scheduleContainer) {
        scheduleContainer = document.createElement('div');
        scheduleContainer.id = `${role}-schedule-container-${courseNum}-${rowIndex}`;
        scheduleContainer.className = 'schedule-container ms-4 mt-2';
        
        // Insert the container after the staff row
        const staffRow = document.getElementById(`${role}-row-${courseNum}-${rowIndex}`);
        if (staffRow && staffRow.nextSibling) {
            staffRow.parentNode.insertBefore(scheduleContainer, staffRow.nextSibling);
        } else if (staffRow) {
            staffRow.parentNode.appendChild(scheduleContainer);
        }
    }
    
    // Clear existing schedule container
    scheduleContainer.innerHTML = '';
    
    // Add schedule rows based on number of classes
    for (let i = 0; i < numClasses; i++) {
        const scheduleRow = document.createElement('div');
        scheduleRow.className = 'schedule-form-group ms-4 mt-2';
        
        scheduleRow.innerHTML = `
            <label for="${role}-day${courseNum}-${rowIndex}-${i}">Class ${i+1}:</label>
            <select id="${role}-day${courseNum}-${rowIndex}-${i}" class="form-select me-2" style="width: 150px;">
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
            </select>
            <label for="${role}-time${courseNum}-${rowIndex}-${i}" class="me-2">Time:</label>
            <input type="time" id="${role}-time${courseNum}-${rowIndex}-${i}" class="form-control me-2" style="width: 120px;">
            <label for="${role}-location${courseNum}-${rowIndex}-${i}" class="me-2">Location:</label>
            <input type="text" id="${role}-location${courseNum}-${rowIndex}-${i}" class="form-control" style="width: 120px;">
        `;
        
        scheduleContainer.appendChild(scheduleRow);
    }
}

// Save the course schedule
function saveSchedule(courseNum) {
    // Validate that at least one lecturer is assigned
    const lecturerSelects = document.querySelectorAll(`select[id^="lecturer${courseNum}-"]`);
    let hasLecturer = false;
    
    lecturerSelects.forEach(function(select) {
        if (select.value.trim() !== '') {
            hasLecturer = true;
        }
    });
    
    if (!hasLecturer) {
        alert('At least one lecturer must be assigned to this course!');
        return;
    }
    
    // Collect all the data
    const scheduleData = {
        courseId: courseNum,
        lecturers: collectStaffData('lecturer', courseNum),
        tutors: collectStaffData('tutor', courseNum)
    };
    
    // Send data to server (mocked for this example)
    console.log('Schedule data to be saved:', scheduleData);
    
    // Simulate successful save
    alert('Schedule saved successfully!');
    
    // Here you would normally make an AJAX call to save the data
    // Example:
    /*
    fetch('/api/save-schedule', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(scheduleData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Schedule saved successfully!');
        } else {
            alert('Error saving schedule: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error saving schedule: ' + error);
    });
    */
}

// Collect staff data (lecturers or tutors)
function collectStaffData(role, courseNum) {
    const staffData = [];
    const containers = document.querySelectorAll(`[id^="${role}-row-${courseNum}-"]`);
    
    containers.forEach(function(container, index) {
        const nameSelect = document.getElementById(`${role}${courseNum}-${index}`);
        const classesInput = document.getElementById(`${role}-classes${courseNum}-${index}`);
        
        if (nameSelect && nameSelect.value.trim() !== '') {
            const staff = {
                name: nameSelect.value,
                numClasses: parseInt(classesInput?.value) || 0,
                schedules: []
            };
            
            // Collect schedules if they exist
            const scheduleContainer = document.getElementById(`${role}-schedule-container-${courseNum}-${index}`);
            if (scheduleContainer) {
                for (let i = 0; i < staff.numClasses; i++) {
                    const daySelect = document.getElementById(`${role}-day${courseNum}-${index}-${i}`);
                    const timeInput = document.getElementById(`${role}-time${courseNum}-${index}-${i}`);
                    const locationInput = document.getElementById(`${role}-location${courseNum}-${index}-${i}`);
                    
                    if (daySelect && timeInput && locationInput) {
                        staff.schedules.push({
                            day: daySelect.value,
                            time: timeInput.value,
                            location: locationInput.value
                        });
                    }
                }
            }
            
            staffData.push(staff);
        }
    });
    
    return staffData;
}