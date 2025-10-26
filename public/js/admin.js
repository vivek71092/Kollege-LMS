/* /public/js/admin.js */

/*
 * Kollege LMS - Admin JavaScript
 * ------------------------------
 * This file contains scripts *only* for the Admin panel.
 * It's loaded after dashboard.js.
 * Assumes libraries like jQuery, DataTables, and Chart.js
 * have been included on the admin pages that need them via footer.php.
 */

// Use jQuery's document ready function to ensure HTML is fully loaded
// and jQuery is available before running jQuery-dependent code like DataTables.
$(document).ready(function() {

    // --- Optional Debugging ---
    // console.log("Admin JS: DOM ready, initializing components...");

    /**
     * Initialize DataTables
     * ---------------------
     * Turns HTML tables with the class .data-table into enhanced tables.
     */
    function initDataTables() {
        // Double-check if DataTables plugin is loaded on jQuery
        if (typeof $.fn.DataTable !== 'undefined') {

            // Initialize all tables with the class 'data-table'
            $('.data-table').each(function() {
                var tableId = this.id || 'undefined'; // Get table ID for logging
                try {
                    // --- Optional Debugging ---
                    // console.log("Initializing DataTable for table #" + tableId);

                    $(this).DataTable({
                        // Your preferred options:
                        responsive: true,
                        pagingType: "simple_numbers",
                        language: {
                            search: "_INPUT_", // Hides the default label, uses placeholder
                            searchPlaceholder: "Search records..."
                        }
                        // Add other common options if needed:
                        // "order": [], // Disable initial sorting
                        // "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]], // Page length options
                    });

                    // --- Optional Debugging ---
                    // console.log("DataTable initialized successfully for table #" + tableId);

                } catch (e) {
                    // Log error if initialization fails for a specific table
                    console.error("Error initializing DataTable for table #" + tableId + ":", e);
                    // Optionally alert the user
                    // alert("Could not initialize table features for table #" + tableId + ". Please check console for errors.");
                }
            });

        } else {
            console.warn('DataTables plugin is not loaded on jQuery. Skipping table initialization.');
        }
    }

    /**
     * Initialize Chart.js Charts
     * --------------------------
     * Renders charts on canvases (e.g., #enrollmentChart).
     */
    function initAnalyticsCharts() {
        // Check if Chart object exists globally
        if (typeof Chart !== 'undefined') {

            // --- Optional Debugging ---
            // console.log("Chart.js found, attempting chart initialization.");

            // Example: User Enrollment Chart (from your original code)
            const enrollmentChartCanvas = document.getElementById('enrollmentChart');
            if (enrollmentChartCanvas) {
                try {
                    const ctx = enrollmentChartCanvas.getContext('2d');
                    // !! IMPORTANT: Fetch this data dynamically via AJAX in a real app !!
                    const enrollmentData = {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'], // Example labels
                        datasets: [{
                            label: 'New Students',
                            data: [12, 19, 3, 5, 2, 3, 7, 8, 5, 9], // Example data
                            backgroundColor: 'rgba(13, 110, 253, 0.6)',
                            borderColor: 'rgba(13, 110, 253, 1)',
                            borderWidth: 1
                        }]
                    };
                    new Chart(ctx, {
                        type: 'bar',
                        data: enrollmentData,
                        options: {
                            scales: { y: { beginAtZero: true } },
                            responsive: true,
                            plugins: {
                                legend: { position: 'top' },
                                title: { display: true, text: 'Monthly Student Signups (Sample)' }
                            }
                        }
                    });
                     // --- Optional Debugging ---
                    // console.log("Initialized enrollmentChart.");
                } catch (e) {
                     console.error("Error initializing enrollmentChart:", e);
                }
            } else {
                // --- Optional Debugging ---
                // console.log("Canvas element with ID 'enrollmentChart' not found.");
            }

            // Example: User Roles Chart (Add similar logic if you have #userRolesChart canvas)
            const userRolesChartCanvas = document.getElementById('userRolesChart');
            if (userRolesChartCanvas) {
                 try {
                    const ctxRoles = userRolesChartCanvas.getContext('2d');
                    // !! IMPORTANT: Fetch this data dynamically via AJAX in a real app !!
                    const rolesData = {
                        labels: ['Students', 'Teachers', 'Admins'], // Example labels
                        datasets: [{
                            label: 'User Roles',
                            data: [500, 20, 1], // Example data (use actual counts)
                            backgroundColor: [
                                'rgba(13, 110, 253, 0.7)',  // Blue
                                'rgba(25, 135, 84, 0.7)', // Green
                                'rgba(220, 53, 69, 0.7)'   // Red
                            ],
                            borderColor: [
                                'rgba(13, 110, 253, 1)',
                                'rgba(25, 135, 84, 1)',
                                'rgba(220, 53, 69, 1)'
                            ],
                            borderWidth: 1
                        }]
                    };
                    new Chart(ctxRoles, {
                        type: 'doughnut', // Or 'pie'
                        data: rolesData,
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { position: 'top' },
                                title: { display: true, text: 'User Distribution by Role (Sample)' }
                            }
                        }
                    });
                    // --- Optional Debugging ---
                    // console.log("Initialized userRolesChart.");
                 } catch (e) {
                      console.error("Error initializing userRolesChart:", e);
                 }
            } else {
                 // --- Optional Debugging ---
                 // console.log("Canvas element with ID 'userRolesChart' not found.");
            }

        } else {
            console.warn('Chart.js library is not loaded. Skipping chart initialization.');
        }
    }

    // --- Initialize all components ---
    initDataTables();
    initAnalyticsCharts();

    // --- Optional Debugging ---
    // console.log("Admin JS: Component initialization potentially complete.");

    // --- Add other admin-specific event listeners or functions here ---
    // Example:
    // $('#someAdminButton').on('click', function() {
    //     // Do something specific to admin panel
    // });

}); // --- End of $(document).ready ---