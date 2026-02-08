/* ============================================
   VMS Admin Dashboard - Global JavaScript
   UI Interactions, Sidebar, Charts & Utilities
   ============================================ */

(function () {
    'use strict';

    /* ============================================
       UI EFFECTS
       ============================================ */
    const UIEffects = {
        init() {
            this.animateOnScroll();
            this.addRippleEffect();
            this.smoothScroll();
            this.initTooltips();
            this.handleButtonLoading();
        },

        animateOnScroll() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.dashboard-card, .table-container')
                .forEach(el => observer.observe(el));
        },

        addRippleEffect() {
            document.querySelectorAll('.btn:not(.btn-link)').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);

                    ripple.className = 'ripple';
                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
                    ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';

                    this.querySelector('.ripple')?.remove();
                    this.appendChild(ripple);
                    setTimeout(() => ripple.remove(), 600);
                });
            });
        },

        smoothScroll() {
            document.querySelectorAll('a[href^="#"]').forEach(a => {
                a.addEventListener('click', e => {
                    const target = document.querySelector(a.getAttribute('href'));
                    if (target) {
                        e.preventDefault();
                        target.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });
        },

        initTooltips() {
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                    new bootstrap.Tooltip(el);
                });
            }
        },

        handleButtonLoading() {
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', () => {
                    const btn = form.querySelector('[type="submit"]');
                    if (!btn || btn.disabled) return;

                    const text = btn.textContent || btn.value;
                    btn.disabled = true;

                    if (btn.tagName === 'BUTTON') {
                        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Processing...`;
                    } else {
                        btn.value = 'Processing...';
                    }

                    setTimeout(() => {
                        btn.disabled = false;
                        btn.textContent = text;
                        btn.value = text;
                    }, 5000);
                });
            });
        }
    };

    /* ============================================
       SIDEBAR SYSTEM
       ============================================ */
    function toggleSidebar() {
        try {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const body = document.body;

            if (sidebar && overlay) {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
                body.classList.toggle('sidebar-open');
            }
        } catch (e) {
            console.warn("Sidebar toggle error:", e);
        }
    }

    function initSidebar() {
        const overlay = document.getElementById('sidebarOverlay');
        if (overlay) {
            overlay.addEventListener('click', toggleSidebar);
        }
    }

    /* ============================================
       CHART MANAGER
       ============================================ */
    const ChartManager = {
        charts: {},

        init() {
            if (typeof Chart === 'undefined') return console.warn("Chart.js not loaded");
            this.initDashboardCharts();
        },

        initDashboardCharts() {
            const ctx = document.getElementById('dashboardChart');
            if (!ctx) return;

            this.charts.dashboard = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan','Feb','Mar','Apr','May','Jun'],
                    datasets: [{
                        label: 'Vaccinations',
                        data: [12,19,15,25,22,30],
                        borderColor: 'blue',
                        backgroundColor: 'rgba(0,0,255,0.1)',
                        fill: true
                    }]
                }
            });
        }
    };

    /* ============================================
       UTILITIES
       ============================================ */
    const Utils = {
        debounce(fn, delay) {
            let t;
            return (...args) => {
                clearTimeout(t);
                t = setTimeout(() => fn(...args), delay);
            };
        }
    };

    /* ============================================
       RIPPLE CSS
       ============================================ */
    function addRippleStyles() {
        if (document.getElementById('ripple-css')) return;
        const style = document.createElement('style');
        style.id = 'ripple-css';
        style.innerHTML = `
            .btn{position:relative;overflow:hidden}
            .ripple{
                position:absolute;
                border-radius:50%;
                background:rgba(255,255,255,.6);
                transform:scale(0);
                animation:ripple .6s linear;
            }
            @keyframes ripple{to{transform:scale(4);opacity:0}}
        `;
        document.head.appendChild(style);
    }

    /* ============================================
       MAIN INIT
       ============================================ */
    function start() {
        addRippleStyles();
        UIEffects.init();
        initSidebar();
        ChartManager.init();

        window.toggleSidebar = toggleSidebar;
        window.AdminUtils = Utils;
        window.ChartManager = ChartManager;

        console.log("VMS Admin JS Loaded");
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }

})();
