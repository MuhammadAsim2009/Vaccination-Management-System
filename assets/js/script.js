/* ============================================
   VMS Admin Dashboard - Global JavaScript
   UI Interactions & Enhancements
   ============================================ */

(function() {
    'use strict';

    /* ============================================
       SIDEBAR TOGGLE FUNCTIONALITY (Mobile)
       ============================================ */
    const SidebarManager = {
        sidebar: null,
        overlay: null,
        toggleButton: null,

        init: function() {
            this.sidebar = document.getElementById('adminSidebar');
            this.overlay = document.getElementById('sidebarOverlay');
            this.toggleButton = document.querySelector('[data-sidebar-toggle]');

            if (!this.sidebar) return;

            // Initialize toggle button if exists
            if (this.toggleButton) {
                this.toggleButton.addEventListener('click', () => this.toggle());
            }

            // Close sidebar when clicking overlay
            if (this.overlay) {
                this.overlay.addEventListener('click', () => this.close());
            }

            // Close sidebar on window resize (if mobile)
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 992) {
                    this.close();
                }
            });

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', (e) => {
                if (window.innerWidth < 992 && 
                    this.sidebar && 
                    this.sidebar.classList.contains('show') &&
                    !this.sidebar.contains(e.target) &&
                    !(this.toggleButton && this.toggleButton.contains(e.target))) {
                    this.close();
                }
            });
        },

        toggle: function() {
            if (!this.sidebar) return;

            const isOpen = this.sidebar.classList.contains('show');
            
            if (isOpen) {
                this.close();
            } else {
                this.open();
            }
        },

        open: function() {
            if (this.sidebar) {
                this.sidebar.classList.add('show');
            }
            if (this.overlay) {
                this.overlay.classList.add('show');
            }
            document.body.classList.add('sidebar-open');
        },

        close: function() {
            if (this.sidebar) {
                this.sidebar.classList.remove('show');
            }
            if (this.overlay) {
                this.overlay.classList.remove('show');
            }
            document.body.classList.remove('sidebar-open');
        }
    };

    /* ============================================
       DROPDOWN HANDLING
       ============================================ */
    const DropdownManager = {
        init: function() {
            // Handle Bootstrap dropdowns
            const dropdowns = document.querySelectorAll('.dropdown-toggle');
            
            dropdowns.forEach(dropdown => {
                dropdown.addEventListener('click', function(e) {
                    // Close other dropdowns when opening a new one (optional)
                    const isExpanded = this.getAttribute('aria-expanded') === 'true';
                    
                    if (!isExpanded) {
                        // Close all other dropdowns
                        document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                            const toggle = menu.previousElementSibling;
                            if (toggle && toggle !== this) {
                                toggle.setAttribute('aria-expanded', 'false');
                                menu.classList.remove('show');
                            }
                        });
                    }
                });
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                        menu.classList.remove('show');
                        const toggle = menu.previousElementSibling;
                        if (toggle) {
                            toggle.setAttribute('aria-expanded', 'false');
                        }
                    });
                }
            });

            // Handle keyboard navigation for dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        this.classList.remove('show');
                        const toggle = this.previousElementSibling;
                        if (toggle) {
                            toggle.setAttribute('aria-expanded', 'false');
                            toggle.focus();
                        }
                    }
                });
            });
        }
    };

    /* ============================================
       ACTIVE MENU HIGHLIGHTING
       ============================================ */
    const MenuHighlighter = {
        init: function() {
            const currentPath = window.location.pathname;
            const sidebarLinks = document.querySelectorAll('.sidebar-link');

            sidebarLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (!href) return;

                // Remove active class from all links first
                link.classList.remove('active');

                // Check if current path matches link href
                if (this.isActiveLink(currentPath, href)) {
                    link.classList.add('active');
                }
            });
        },

        isActiveLink: function(currentPath, linkHref) {
            // Convert relative paths to absolute
            const linkPath = new URL(linkHref, window.location.origin).pathname;
            
            // Exact match
            if (currentPath === linkPath) {
                return true;
            }

            // Check if current path includes the link path segment
            const linkSegments = linkPath.split('/').filter(seg => seg);
            const currentSegments = currentPath.split('/').filter(seg => seg);

            // Match if any segment matches
            for (let i = 0; i < linkSegments.length; i++) {
                if (linkSegments[i] && currentSegments.includes(linkSegments[i])) {
                    return true;
                }
            }

            // Check data-page attribute
            const linkPage = document.querySelector(`[href="${linkHref}"]`)?.getAttribute('data-page');
            if (linkPage && currentPath.includes(linkPage)) {
                return true;
            }

            return false;
        }
    };

    /* ============================================
       SMOOTH UI INTERACTIONS
       ============================================ */
    const UIEffects = {
        init: function() {
            // Add fade-in animation to cards on load
            this.animateOnScroll();
            
            // Add ripple effect to buttons
            this.addRippleEffect();
            
            // Smooth scroll for anchor links
            this.smoothScroll();
            
            // Tooltip initialization (if using Bootstrap tooltips)
            this.initTooltips();
            
            // Loading states for buttons
            this.handleButtonLoading();
        },

        animateOnScroll: function() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Observe dashboard cards and table containers
            document.querySelectorAll('.dashboard-card, .table-container').forEach(el => {
                observer.observe(el);
            });
        },

        addRippleEffect: function() {
            document.querySelectorAll('.btn:not(.btn-link)').forEach(button => {
                button.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;

                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = x + 'px';
                    ripple.style.top = y + 'px';
                    ripple.classList.add('ripple');

                    const existingRipple = this.querySelector('.ripple');
                    if (existingRipple) {
                        existingRipple.remove();
                    }

                    this.appendChild(ripple);

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
        },

        smoothScroll: function() {
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href === '#') return;

                    const target = document.querySelector(href);
                    if (target) {
                        e.preventDefault();
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        },

        initTooltips: function() {
            // Initialize Bootstrap tooltips if available
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        },

        handleButtonLoading: function() {
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
                    if (submitButton && !submitButton.disabled) {
                        const originalText = submitButton.textContent || submitButton.value;
                        submitButton.disabled = true;
                        
                        if (submitButton.tagName === 'BUTTON') {
                            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
                        } else {
                            submitButton.value = 'Processing...';
                        }

                        // Re-enable after 5 seconds as fallback (form should handle this normally)
                        setTimeout(() => {
                            submitButton.disabled = false;
                            if (submitButton.tagName === 'BUTTON') {
                                submitButton.textContent = originalText;
                            } else {
                                submitButton.value = originalText;
                            }
                        }, 5000);
                    }
                });
            });
        }
    };

    /* ============================================
       CHART.JS INITIALIZATION PLACEHOLDERS
       ============================================ */
    const ChartManager = {
        charts: {},

        init: function() {
            // Check if Chart.js is loaded
            if (typeof Chart === 'undefined') {
                console.warn('Chart.js is not loaded. Charts will not be initialized.');
                return;
            }

            // Initialize dashboard charts
            this.initDashboardCharts();
            
            // Initialize vaccination charts
            this.initVaccinationCharts();
            
            // Initialize report charts
            this.initReportCharts();
        },

        initDashboardCharts: function() {
            // Dashboard Statistics Chart (Line Chart)
            const dashboardCtx = document.getElementById('dashboardChart');
            if (dashboardCtx) {
                this.charts.dashboard = new Chart(dashboardCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Vaccinations',
                            data: [12, 19, 15, 25, 22, 30],
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // Vaccination Status Chart (Doughnut Chart)
            const statusCtx = document.getElementById('vaccinationStatusChart');
            if (statusCtx) {
                this.charts.status = new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Completed', 'Pending', 'Scheduled'],
                        datasets: [{
                            data: [65, 25, 10],
                            backgroundColor: [
                                'rgb(34, 197, 94)',
                                'rgb(245, 158, 11)',
                                'rgb(59, 130, 246)'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        },

        initVaccinationCharts: function() {
            // Vaccination Trends Chart (Bar Chart)
            const trendsCtx = document.getElementById('vaccinationTrendsChart');
            if (trendsCtx) {
                this.charts.trends = new Chart(trendsCtx, {
                    type: 'bar',
                    data: {
                        labels: ['BCG', 'DPT', 'Polio', 'Measles', 'Hepatitis B'],
                        datasets: [{
                            label: 'Vaccinations',
                            data: [120, 150, 130, 110, 140],
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                            borderColor: 'rgb(59, 130, 246)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        },

        initReportCharts: function() {
            // Monthly Report Chart (Area Chart)
            const reportCtx = document.getElementById('monthlyReportChart');
            if (reportCtx) {
                this.charts.report = new Chart(reportCtx, {
                    type: 'line',
                    data: {
                        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                        datasets: [{
                            label: 'Appointments',
                            data: [45, 52, 48, 60],
                            borderColor: 'rgb(20, 184, 166)',
                            backgroundColor: 'rgba(20, 184, 166, 0.2)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        },

        // Method to update chart data
        updateChart: function(chartId, newData) {
            if (this.charts[chartId]) {
                this.charts[chartId].data = newData;
                this.charts[chartId].update();
            }
        },

        // Method to destroy a chart
        destroyChart: function(chartId) {
            if (this.charts[chartId]) {
                this.charts[chartId].destroy();
                delete this.charts[chartId];
            }
        }
    };

    /* ============================================
       UTILITY FUNCTIONS
       ============================================ */
    const Utils = {
        // Debounce function for performance
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        // Format numbers with commas
        formatNumber: function(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        },

        // Show toast notification (if using Bootstrap toasts)
        showToast: function(message, type = 'info') {
            const toastContainer = document.getElementById('toastContainer') || this.createToastContainer();
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type} border-0`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            toastContainer.appendChild(toast);
            
            if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
                const bsToast = new bootstrap.Toast(toast);
                bsToast.show();
                
                toast.addEventListener('hidden.bs.toast', () => {
                    toast.remove();
                });
            }
        },

        createToastContainer: function() {
            const container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
            return container;
        }
    };

    /* ============================================
       RIPPLE EFFECT STYLES (Dynamic CSS)
       ============================================ */
    const addRippleStyles = function() {
        if (!document.getElementById('ripple-styles')) {
            const style = document.createElement('style');
            style.id = 'ripple-styles';
            style.textContent = `
                .btn {
                    position: relative;
                    overflow: hidden;
                }
                .ripple {
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.6);
                    transform: scale(0);
                    animation: ripple-animation 0.6s ease-out;
                    pointer-events: none;
                }
                @keyframes ripple-animation {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }
    };

    /* ============================================
       INITIALIZATION
       ============================================ */
    const AdminDashboard = {
        init: function() {
            // Wait for DOM to be ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => this.start());
            } else {
                this.start();
            }
        },

        start: function() {
            // Add ripple styles
            addRippleStyles();

            // Initialize all managers
            SidebarManager.init();
            DropdownManager.init();
            MenuHighlighter.init();
            UIEffects.init();
            ChartManager.init();

            // Expose utilities globally
            window.AdminUtils = Utils;
            window.ChartManager = ChartManager;
            window.toggleSidebar = () => SidebarManager.toggle();

            console.log('VMS Admin Dashboard initialized');
        }
    };

    // Auto-initialize
    AdminDashboard.init();

})();

