/* /public/js/main.js */

/*
 * Kollege LMS - Main JavaScript
 * -----------------------------
 * This file contains global scripts for the public-facing pages
 * (homepage, about, contact, etc.).
 */

document.addEventListener("DOMContentLoaded", function() {

    /**
     * Set the 'active' class on the correct navigation link based on the current page.
     */
    function setActiveNavLink() {
        // Get the current page URL path
        let currentPath = window.location.pathname;

        // Handle the root path (index.php or /)
        if (currentPath === '/' || currentPath.endsWith('index.php')) {
            currentPath = '/index.php';
        }

        // Find all links in the main navbar
        const navLinks = document.querySelectorAll('.navbar .nav-link');

        navLinks.forEach(link => {
            let linkPath = new URL(link.href).pathname;

            // Check if the link's href path matches the current page path
            if (linkPath === currentPath) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    }

    /**
     * Handles the 'Contact Us' form submission via AJAX (example).
     * This prevents a full page reload.
     */
    function handleContactForm() {
        const contactForm = document.getElementById('contactForm'); // Assuming your form has id="contactForm"
        if (!contactForm) return;

        contactForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Stop the default form submission

            const feedbackEl = document.getElementById('form-feedback');
            const formData = new FormData(this);

            // You would replace this URL with your actual API endpoint
            const apiEndpoint = this.action; 

            // Show loading state (e.g., disable button)
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.textContent = 'Sending...';

            fetch(apiEndpoint, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    feedbackEl.className = 'alert alert-success mt-3';
                    feedbackEl.textContent = data.message || 'Message sent successfully!';
                    contactForm.reset();
                } else {
                    feedbackEl.className = 'alert alert-danger mt-3';
                    feedbackEl.textContent = data.message || 'An error occurred. Please try again.';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                feedbackEl.className = 'alert alert-danger mt-3';
                feedbackEl.textContent = 'A network error occurred. Please try again later.';
            })
            .finally(() => {
                // Restore button
                submitButton.disabled = false;
                submitButton.textContent = 'Send Message';
            });
        });
    }

    // Initialize scripts
    setActiveNavLink();
    handleContactForm();

    // Add more public-facing scripts here...

});