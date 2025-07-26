// Ensure the script runs after the DOM has loaded
document.addEventListener("DOMContentLoaded", function () {
    initializeEventListeners();
    storeOriginalUnitPositions();
});

// Store original positions of units
let originalUnitPositions = {};

// Function to initialize event listeners
function initializeEventListeners() {
    document.querySelectorAll(".openSemesterButton").forEach(button => {
        button.addEventListener("click", function () {
            openSemesterDetails(this.dataset.semester);
        });
    });

    document.getElementById("saveSelectionButton").addEventListener("click", saveSelectedUnits);
}


// Store the initial order of units in tables
function storeOriginalUnitPositions() {
    document.querySelectorAll("#selectedUnitsTable tbody tr, #coreMajorUnitsTable tbody tr, #electiveUnitsTable tbody tr").forEach((row, index) => {
        const unitCode = row.children[0].textContent.trim();
        originalUnitPositions[unitCode] = index;
    });
}

// Helper function to find unit details
function findUnitDetails(unitCode) {
    const coreUnit = semesterData[currentSemester].find(u => u.u_code === unitCode);
    if (coreUnit) return coreUnit;

    const allUnitsSearch = Object.values(semesterData).flat().find(u => u.u_code === unitCode);
    return allUnitsSearch || {};
}

function openSemesterDetails(semesterName) {
    const popup = document.getElementById("semesterPopup");
    const overlay = document.getElementById("overlay");
    const semesterContainer = document.querySelector(".semesters-container");
    const title = document.getElementById('semesterTitle');
    const coreMajorTableBody = document.querySelector('#coreMajorUnitsTable tbody');
    const electiveTableBody = document.querySelector('#electiveUnitsTable tbody');
    const selectedUnitsTableBody = document.querySelector('#selectedUnitsTable tbody');
    const electiveSection = document.getElementById('electiveSection');
    const enrollmentButton = document.getElementById('enrollmentButton');

    if (!popup || !overlay || !semesterContainer || !title || !coreMajorTableBody || !electiveTableBody || !selectedUnitsTableBody || !enrollmentButton) return;

    title.textContent = semesterName + " Details";
    semesterContainer.classList.add("popup-active");

    coreMajorTableBody.innerHTML = "";
    electiveTableBody.innerHTML = "";
    selectedUnitsTableBody.innerHTML = "";

    let electivesExist = false;
    let coreMajorCount = 0;
    let electiveCount = 0;

    //Create a Set of selected unit codes for quick lookup
    let selectedUnitCodes = new Set();
    if (selectedUnits[semesterName]) {
        selectedUnits[semesterName].forEach(unit => {
            selectedUnitCodes.add(unit.u_code);
        });
    }

    // Populate Core & Major / Elective Units from semesterData
    if (semesterData[semesterName]) {
        semesterData[semesterName].forEach(unit => {
            if (selectedUnitCodes.has(unit.unit_code)) {
                // Skip if the unit is already in selectedUnits
                return;
            }

            const row = document.createElement('tr');
            
            if (unit.type === 'Elective') {
                // Elective Units Table (No "Type" Column)
                row.innerHTML = `
                    <td>${unit.unit_code}</td>
                    <td>${unit.unit_name}</td>
                    <td>${unit.credits || '-'}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" 
                            onclick="moveUnitToSelected(this, '${unit.unit_code}', '${unit.unit_name}', '${unit.credits}', '${unit.type}', 'electiveUnitsTable')">
                            Add
                        </button>
                    </td>
                `;
                electivesExist = true;
                electiveCount++;
                electiveTableBody.appendChild(row);
            } else {
                // Core & Major Units Table (Includes "Type" Column)
                row.innerHTML = `
                    <td>${unit.unit_code}</td>
                    <td>${unit.unit_name}</td>
                    <td>${unit.credits || '-'}</td>
                    <td>${unit.type}</td> <!-- Type Column for Core & Major -->
                    <td>
                        <button class="btn btn-sm btn-primary" 
                            onclick="moveUnitToSelected(this, '${unit.unit_code}', '${unit.unit_name}', '${unit.credits}', '${unit.type}', 'coreMajorUnitsTable')">
                            Add
                        </button>
                    </td>
                `;
                coreMajorCount++;
                coreMajorTableBody.appendChild(row);
            }
        });
    }

    // Show "No available units" message if no core/major units
    if (coreMajorCount === 0) {
        const noUnitsRow = document.createElement('tr');
        noUnitsRow.innerHTML = `
            <td colspan="5" class="text-center">No available units</td>
        `;
        coreMajorTableBody.appendChild(noUnitsRow);
    }
    
    // Show "No available units" message if no elective units
    if (electiveCount === 0) {
        const noUnitsRow = document.createElement('tr');
        noUnitsRow.innerHTML = `
            <td colspan="4" class="text-center">No available units</td>
        `;
        electiveTableBody.appendChild(noUnitsRow);
    }

    // Populate Selected Units from selectedUnits
    let selectedCount = 0;
    if (selectedUnits[semesterName]) {
        selectedUnits[semesterName].forEach(unit => {
            const selectedRow = document.createElement('tr');
            selectedRow.innerHTML = `
                <td>${unit.u_code}</td>
                <td>${unit.unit_name}</td>
                <td>${unit.credits}</td>
                <td>${unit.type}</td>
                <td>
                    <button class="btn btn-sm btn-danger" 
                    onclick="removeUnitFromSelected(this, '${unit.u_code}', '${unit.unit_name}', '${unit.credits}', '${unit.type}', '${semesterName}')">
                    Remove
                    </button>
                </td>
            `;
            selectedUnitsTableBody.appendChild(selectedRow);
            selectedCount++;
        });
    }
    
    // Add empty rows to selected units table if less than 3 units
    for (let i = selectedCount; i < 3; i++) {
        const emptyRow = document.createElement('tr');
        emptyRow.innerHTML = `
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
        `;
        selectedUnitsTableBody.appendChild(emptyRow);
    }

    electiveSection.style.display = electivesExist ? 'block' : 'none';
    enrollmentButton.style.display = "block";
    enrollmentButton.disabled = semesterName !== currentSemester;
    enrollmentButton.classList[semesterName === currentSemester ? 'remove' : 'add']("disabled-semester");
    
    popup.classList.add("active");
    overlay.classList.add("active");
}

function toggleElectiveTable() {
    let electiveSection = document.getElementById('electiveSection');
    let toggleButton = document.getElementById('toggleElectiveButton');

    if (electiveSection.style.maxHeight === '0px' || electiveSection.style.maxHeight === '') {
        electiveSection.style.maxHeight = electiveSection.scrollHeight + "px";
        electiveSection.style.opacity = '1';
        toggleButton.innerHTML = '▼ Hide Electives';
    } else {
        electiveSection.style.maxHeight = '0px';
        electiveSection.style.opacity = '0';
        toggleButton.innerHTML = '▲ Show Electives';
    }
}

// Move a unit to the selected table
function moveUnitToSelected(button, unitCode, unitName, credits, type, fromTableId) {
    const prerequisite = unitPrerequisites[unitCode];
    if (prerequisite && prerequisite !== 'No') {
        const confirmMove = confirm(`Warning: This unit has prerequisite(s): ${prerequisite}. Are you sure you want to select it?`);
        if (!confirmMove) {
            return; // User canceled the selection
        }
    }

    const fromTable = document.getElementById(fromTableId);
    const selectedTable = document.getElementById("selectedUnitsTable");

    if (!fromTable || !selectedTable) {
        console.error("Table not found!");
        return;
    }

    let selectedTbody = selectedTable.querySelector("tbody");
    if (!selectedTbody) {
        selectedTbody = document.createElement("tbody");
        selectedTable.appendChild(selectedTbody);
    }

    // Get all existing rows (excluding placeholder rows)
    let selectedRows = selectedTbody.querySelectorAll("tr");
    let nonPlaceholderRows = Array.from(selectedRows).filter(row => 
        !Array.from(row.cells).every(cell => cell.textContent.trim() === "-")
    );

    // Ensure the limit of 3 units is not exceeded
    if (nonPlaceholderRows.length >= 3) {
        alert("You can only select up to 3 units per semester.");
        return;
    }

    // Prevent duplicate selections
    if (Array.from(selectedTbody.querySelectorAll("tr td:first-child")).some(td => td.textContent.trim() === unitCode)) {
        alert("This unit is already selected.");
        return;
    }

    // Move the unit to the selected units table
    const unitRow = button.closest("tr");
    const newRow = document.createElement("tr");
    newRow.innerHTML = `
        <td>${unitCode}</td>
        <td>${unitName}</td>
        <td>${credits}</td>
        <td>${type}</td>
        <td>
            <button class="btn btn-sm btn-danger" onclick="removeUnitFromSelected(this, '${unitCode}', '${unitName}', '${credits}', '${type}', '${fromTableId}')">Remove</button>
        </td>
    `;

    // Find and replace the first placeholder row if it exists
    let placeholderRow = Array.from(selectedTbody.querySelectorAll("tr")).find(row => 
        Array.from(row.cells).every(cell => cell.textContent.trim() === "-")
    );

    if (placeholderRow) {
        selectedTbody.replaceChild(newRow, placeholderRow);
    } else {
        selectedTbody.appendChild(newRow);
    }

    unitRow.remove();
}

// Move a unit back to its original table
function removeUnitFromSelected(button, unitCode, unitName, credits, unitType, fromTableId) {
    console.log(`Moving unit: ${unitCode} back to ${fromTableId}`);

    const tableMapping = {
        "Core": "coreMajorUnitsTable",
        "Elective": "electiveUnitsTable",
        "Major": "coreMajorUnitsTable"
    };

    const correctTableId = tableMapping[unitType] || fromTableId || "coreMajorUnitsTable";
    console.log("Corrected Table ID:", correctTableId);

    let fromTable = document.getElementById(correctTableId);
    
    // Fallback if table not found
    if (!fromTable) {
        console.error(`Table with ID ${correctTableId} not found. Falling back to Core Units table.`);
        fromTable = document.getElementById("coreMajorUnitsTable");
    }

    if (!fromTable) {
        console.error("No suitable table found for returning unit");
        return;
    }

    let fromTbody = fromTable.querySelector("tbody");
    
    // Create tbody if not exists
    if (!fromTbody) {
        fromTbody = document.createElement("tbody");
        fromTable.appendChild(fromTbody);
    }

    // Create new row for the unit being returned
    const newRow = document.createElement("tr");
    if (correctTableId === "coreMajorUnitsTable") {
        newRow.innerHTML = `
            <td>${unitCode}</td>
            <td>${unitName}</td>
            <td>${credits}</td>
            <td>${unitType}</td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="moveUnitToSelected(this, '${unitCode}', '${unitName}', '${credits}', '${unitType}', '${correctTableId}')">
                    Add
                </button>
            </td>
        `;
    } else {
        // Elective table has one less column
        newRow.innerHTML = `
            <td>${unitCode}</td>
            <td>${unitName}</td>
            <td>${credits}</td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="moveUnitToSelected(this, '${unitCode}', '${unitName}', '${credits}', '${unitType}', '${correctTableId}')">
                    Add
                </button>
            </td>
        `;
    }

    // Restore unit to the original position (if tracking original positions)
    const rowIndex = originalUnitPositions[unitCode];
    const rows = Array.from(fromTbody.querySelectorAll("tr"));

    if (rowIndex !== undefined && rowIndex < rows.length) {
        rows[rowIndex].insertAdjacentElement("beforebegin", newRow);
    } else {
        fromTbody.appendChild(newRow);
    }

    // Remove the selected unit row
    const selectedTbody = button.closest("tbody");
    button.closest("tr").remove();

    // Restore a placeholder row if the total selected units are less than 3
    let selectedRows = selectedTbody.querySelectorAll("tr");
    if (selectedRows.length < 3) {
        const placeholderRow = document.createElement("tr");
        placeholderRow.innerHTML = `
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
        `;
        selectedTbody.appendChild(placeholderRow);
    }
}

// Save selected units
function saveSelectedUnits() {
    // Get the current popup's semester (which might be different from currentSemester)
    const popupSemester = document.getElementById('semesterTitle')?.textContent.replace(" Details", "");

    // Flatten the units into a single array
    let selectedUnits = [];

    document.querySelectorAll("#selectedUnitsTable tbody tr").forEach(row => {
        let unitCode = row.children[0]?.textContent.trim();
        let unitName = row.children[1]?.textContent.trim();
        let credits = row.children[2]?.textContent.trim();
        let unitType = row.children[3]?.textContent.trim();

        // Skip the row if it contains placeholder ("-") values
        if (unitCode === "-" || unitName === "-") {
            return; // Skip this iteration
        }
        
        // Use the semester from the popup instead of currentSemester
        let semesterParts = popupSemester.split(" ");
        let semesterMonth = semesterParts[0];
        let semesterYear = parseInt(semesterParts[1]);

        if (unitCode && unitCode !== "-") {
            selectedUnits.push({
                u_code: unitCode,
                unit_name: unitName,
                CREDITS: parseFloat(credits), // Convert to number
                type: unitType,
                semester_month: semesterMonth,
                semester_year: semesterYear
            });
        }
    });

    console.log("Comprehensive Selected Units Data:", JSON.stringify(selectedUnits, null, 2));

    let studentId = document.getElementById("studentId")?.value;
    let savePlanUrl = document.getElementById("saveAcademicPlanRoute")?.value;

    if (!studentId || selectedUnits.length === 0) {
        alert("⚠️ Please select units before saving.");
        return;
    }

    console.log("Sending data:", { student_id: studentId, units: selectedUnits });

    fetch(savePlanUrl, {
        method: "POST",
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            student_id: studentId,
            units: selectedUnits
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(errorData => {
                throw new Error(JSON.stringify(errorData));
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Save Plan - Response Data:', data);

        if (data.success) {
            // ✅ Show success message & reload after 1.5 seconds
            alert("✅ Units saved successfully! The page will now reload.");
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            throw new Error(data.message || "Unknown error occurred.");
        }
    })
    .catch(error => {
        console.error("🚨 Full error:", error);
        alert("❌ Error saving units. See console for details.");
    });
}

function closeSemesterDetails() {
    const popup = document.getElementById("semesterPopup");
    const overlay = document.getElementById("overlay");
    const coreMajorTableBody = document.querySelector('#coreMajorUnitsTable tbody');
    const electiveTableBody = document.querySelector('#electiveUnitsTable tbody');
    const selectedUnitsTableBody = document.querySelector('#selectedUnitsTable tbody');
    const semesterContainer = document.querySelector(".semesters-container");

    if (popup && overlay) {
        popup.classList.remove("active");
        overlay.classList.remove("active");

        // ✅ Restore horizontal semester layout
        if (semesterContainer) {
            semesterContainer.classList.remove("popup-active");
        }

        // ✅ Clear the table data when closing
        setTimeout(() => { // Delay to ensure smooth closing animation
            if (coreMajorTableBody) coreMajorTableBody.innerHTML = '';
            if (electiveTableBody) electiveTableBody.innerHTML = '';
            if (selectedUnitsTableBody) selectedUnitsTableBody.innerHTML = '';
        }, 300); // Adjust timeout if needed to match CSS animation time

    } else {
        console.error("Error: semesterPopup or overlay not found.");
    }
}

function closePopups() {
    closeSemesterDetails(); // If there are multiple popups, close them here too
}

