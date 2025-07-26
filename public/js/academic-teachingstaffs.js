document.addEventListener('DOMContentLoaded', function() {
    // Initialize page components
    initializeAvailableStaffsToggle();
    initializeAssignButtons();
    initializeFilterForm();
});

function initializeAvailableStaffsToggle() {
    const availableHeader = document.querySelector('.available-header');
    const staffCardsContainer = document.querySelector('.staff-cards-container');
    const chevronIcon = availableHeader.querySelector('.fas');
    
    availableHeader.addEventListener('click', function() {
        if (staffCardsContainer.style.display === 'none') {
            staffCardsContainer.style.display = 'block';
            chevronIcon.classList.replace('fa-chevron-down', 'fa-chevron-up');
        } else {
            staffCardsContainer.style.display = 'none';
            chevronIcon.classList.replace('fa-chevron-up', 'fa-chevron-down');
        }
    });
}

function initializeAssignButtons() {
    document.querySelectorAll('.btn-success').forEach(button => {
        button.addEventListener('click', function() {
            const staffCard = this.closest('.card-body');
            const staffName = staffCard.querySelector('h4').textContent;
            
            // Show a modal or form for assigning this staff to a course
            // This will be connected to the backend later
            console.log(`Assigning ${staffName} to a course`);
            
            // For demonstration purposes, show an alert
            alert(`You're about to assign ${staffName} to a course. This functionality will be connected to the backend later.`);
        });
    });
}

function initializeFilterForm() {
    const viewButton = document.querySelector('.filter-container .btn-light');
    
    viewButton.addEventListener('click', function() {
        const term = document.getElementById('term').value;
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;
        
        // For now, just log the filter values
        console.log(`Filtering by: Term=${term}, From=${dateFrom}, To=${dateTo}`);
        
        // Later, this will make an AJAX request to fetch filtered data
        // fetchFilteredStaffData(term, dateFrom, dateTo);
    });
}

// This function will be implemented later when connecting to the backend
function fetchFilteredStaffData(term, dateFrom, dateTo) {
    // Example AJAX request
    fetch(`/api/teaching-staffs?term=${term}&from=${dateFrom}&to=${dateTo}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update the staff table and available staffs with the fetched data
        updateStaffTable(data.assignedStaffs);
        updateAvailableStaffs(data.availableStaffs);
    })
    .catch(error => {
        console.error('Error fetching staff data:', error);
    });
}