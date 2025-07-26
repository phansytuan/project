document.addEventListener("DOMContentLoaded", function () {
    const unitSchedules = window.unitSchedules || {};

    document.querySelectorAll(".status-failed, .status-not-enrolled").forEach((element) => {
        const unitCode = element.dataset.unit;
        const semesterInfoContainer = document.getElementById(`semester-${unitCode}`);

        if (unitSchedules[unitCode] && unitSchedules[unitCode].length > 0) {
            // Get the nearest available semester (first in sorted order)
            const nearestSemester = unitSchedules[unitCode][0]; 

            if (nearestSemester) {
                const semesterText = `${nearestSemester.semester_month} ${nearestSemester.semester_year}`;
                semesterInfoContainer.innerHTML = `<small class="text-muted">Next available: ${semesterText}</small>`;
            }
        }
    });
});
