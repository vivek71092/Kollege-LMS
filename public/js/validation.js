/* /public/js/validation.js */

/*
 * Kollege LMS - Form Validation Script
 * ------------------------------------
 * This script provides client-side form validation using
 * Bootstrap 5's built-in classes.
 *
 * It targets all forms with the class 'needs-validation'.
 * Add 'novalidate' attribute to your <form> tag to disable
 * default browser validation.
 */

(function () {
    'use strict';

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation');

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                form.classList.add('was-validated');
            }, false);
        });

    /**
     * Example: Password and Confirm Password Match Validation
     */
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');

    function validatePasswordMatch() {
        if (!confirmPassword) return; // Only run if the element exists

        if (password.value !== confirmPassword.value) {
            // Set a custom error message
            confirmPassword.setCustomValidity("Passwords do not match.");
            // Find the feedback element
            const feedback = confirmPassword.nextElementSibling; // Assumes .invalid-feedback is next sibling
            if (feedback) {
                feedback.textContent = "Passwords do not match.";
            }
        } else {
            confirmPassword.setCustomValidity("");
            const feedback = confirmPassword.nextElementSibling;
            if (feedback) {
                feedback.textContent = "Please confirm your password."; // Reset to default
            }
        }
    }

    if (password && confirmPassword) {
        password.addEventListener('input', validatePasswordMatch);
        confirmPassword.addEventListener('input', validatePasswordMatch);
    }
    
})();