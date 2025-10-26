// /public/js/confirm-modal.js

document.addEventListener('DOMContentLoaded', function () {
    var confirmModal = document.getElementById('confirmModal');
    // Debug: Check if the modal element is found
    // console.log("Confirm modal element:", confirmModal);

    if (confirmModal) {
        confirmModal.addEventListener('show.bs.modal', function (event) {
            // Debug: Log when the modal event fires
            // console.log("Modal show event triggered.");

            // Button that triggered the modal
            var button = event.relatedTarget;
            // Debug: Check the button that triggered the modal
            // console.log("Trigger button:", button);

            // Extract info from data-* attributes
            var title = button.getAttribute('data-title');
            var body = button.getAttribute('data-body');
            var url = button.getAttribute('data-confirm-url'); // Get the URL
            // Debug: Check the extracted URL
            // console.log("Extracted URL:", url);


            // Update the modal's content.
            var modalTitle = confirmModal.querySelector('.modal-title');
            var modalBody = confirmModal.querySelector('.modal-body');
            var confirmButton = confirmModal.querySelector('#confirmModalButton'); // Find the button by ID

            // Debug: Check if the confirm button element is found
            // console.log("Confirm button element:", confirmButton);

            if (modalTitle) modalTitle.textContent = title;
            if (modalBody) modalBody.textContent = body;

            // IMPORTANT: Set the href attribute of the Confirm button
            if (confirmButton && url) {
                confirmButton.setAttribute('href', url);
                // Debug: Log the URL being set
                // console.log("Set confirmButton href to:", url);
            } else if (confirmButton) {
                // Fallback or error handling if URL is missing
                console.error("Confirm URL ('data-confirm-url') not found on trigger button or confirm button ('#confirmModalButton') not found.");
                confirmButton.setAttribute('href', '#'); // Prevent accidental clicks, make the # explicit
            }
        });
    } else {
        // Use console.error for clearer debugging
        console.error("Confirmation modal element with ID 'confirmModal' not found in the DOM.");
    }
});