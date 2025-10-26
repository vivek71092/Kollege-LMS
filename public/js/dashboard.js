/* /public/js/dashboard.js */



/*

 * Kollege LMS - Dashboard JavaScript

 * ----------------------------------

 * This file contains scripts common to all logged-in dashboards

 * (Student, Teacher, Admin), such as sidebar toggling.

 */



document.addEventListener("DOMContentLoaded", function() {



    /**

     * Sidebar Toggle for Mobile

     * We need a button (e.g., in a top-navbar) to trigger this.

     * Let's assume the button has id="sidebarToggle" and the

     * sidebar has class ".dashboard-sidebar"

     */

    const sidebarToggle = document.getElementById('sidebarToggle');

    const sidebar = document.querySelector('.dashboard-sidebar');

    const mainContent = document.querySelector('.dashboard-main-content');

    const body = document.body;



    if (sidebarToggle && sidebar) {

        sidebarToggle.addEventListener('click', function(event) {

            event.preventDefault();

            

            // Add a class to the body to manage state

            body.classList.toggle('sidebar-toggled');

            

            // You can use CSS to hide/show the sidebar based on .sidebar-toggled

            // e.g., @media (max-width: 767.98px) {

            //          body.sidebar-toggled .dashboard-sidebar { display: none; }

            //      }

            

            // Or force it with JS:

            if (window.innerWidth < 768) {

                if (sidebar.style.display === 'none' || sidebar.style.display === '') {

                    sidebar.style.display = 'block';

                } else {

                    sidebar.style.display = 'none';

                }

            } else {

                // On desktop, toggle a "minified" sidebar state

                body.classList.toggle('sidebar-minified');

                // You'd use CSS to shrink the sidebar and expand main content

                // .sidebar-minified .dashboard-sidebar { width: 80px; }

                // .sidebar-minified .dashboard-main-content { margin-left: 80px; }

            }

        });

    }



    /**

     * Set Active Sidebar Link

     * Similar to the public navbar, but for the dashboard sidebar

     */

    function setActiveSidebarLink() {

        let currentPath = window.location.pathname;

        

        const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');



        navLinks.forEach(link => {

            let linkPath = new URL(link.href).pathname;



            // Use endsWith to match parent pages

            // e.g., /dashboard/admin/users/edit-user.php should light up /dashboard/admin/users/list-users.php

            // This logic needs to be more complex for a real app, maybe using data-attributes

            

            if (currentPath === linkPath) {

                link.classList.add('active');

            } else {

                link.classList.remove('active');

            }

        });

    }



    setActiveSidebarLink();



    // Add more shared dashboard scripts here...

    // e.g., initializing Bootstrap tooltips

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));

    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {

      return new bootstrap.Tooltip(tooltipTriggerEl);

    });



});