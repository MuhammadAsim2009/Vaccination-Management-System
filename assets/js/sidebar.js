/**
 * Admin Panel Scripts
 * Handles sidebar toggling, active state management, and tooltips
 */

(function () {
    'use strict';

    // Sidebar Toggle Functionality (for mobile)
    function toggleSidebar() {
        try {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const body = document.body;

            if (sidebar && overlay) {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
                if (body) {
                    body.classList.toggle('sidebar-open');
                }
            }
        } catch (error) {
            console.warn('Sidebar toggle error:', error);
        }
    }

    // Initialize sidebar interactions
    function initSidebar() {
        try {
            // Close sidebar when clicking overlay
            const overlay = document.getElementById('sidebarOverlay');
            if (overlay) {
                overlay.addEventListener('click', function () {
                    toggleSidebar();
                });
            }
        } catch (error) {
            console.warn('Sidebar initialization error:', error);
        }
    }

    // Initialize Bootstrap Tooltips
    function initTooltips() {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    }

    // Main Initialization
    function init() {
        initSidebar();
        initTooltips();
    }

    // Wait for DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose Global Functions
    window.toggleSidebar = toggleSidebar;

})();
