/**
 * TOKO UMI — Main JavaScript
 * Bootstrap 5 + jQuery + Custom Utilities
 */

// Bootstrap 5
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// jQuery (global)
import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;

// Axios
import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Chart.js
import Chart from 'chart.js/auto';
window.Chart = Chart;

// ============================================================
// SIDEBAR TOGGLE (Mobile)
// ============================================================
document.addEventListener('DOMContentLoaded', function () {
    const sidebar    = document.getElementById('appSidebar');
    const backdrop   = document.getElementById('sidebarBackdrop');
    const toggleBtns = document.querySelectorAll('[data-sidebar-toggle]');

    function openSidebar() {
        if (sidebar)   sidebar.classList.add('is-open');
        if (backdrop)  backdrop.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        if (sidebar)   sidebar.classList.remove('is-open');
        if (backdrop)  backdrop.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    toggleBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (sidebar && sidebar.classList.contains('is-open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    });

    if (backdrop) {
        backdrop.addEventListener('click', closeSidebar);
    }

    // Close on resize to desktop
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 992) {
            closeSidebar();
        }
    });

    // Close sidebar when nav link clicked on mobile
    const sidebarLinks = document.querySelectorAll('.sidebar-nav .nav-link');
    sidebarLinks.forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth < 992) {
                closeSidebar();
            }
        });
    });
});

// ============================================================
// AUTO-HIDE ALERTS
// ============================================================
document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert-auto-hide');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        }, 4000);
    });
});

// ============================================================
// CONFIRM DELETE
// ============================================================
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-confirm-delete]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            const message = el.dataset.confirmDelete || 'Apakah Anda yakin ingin menghapus data ini?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
});

// ============================================================
// FORM LOADING STATE
// ============================================================
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form[data-loading]').forEach(function (form) {
        form.addEventListener('submit', function () {
            const btn = form.querySelector('[type="submit"]');
            if (btn) {
                btn.disabled = true;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...';
                // Restore after 10 seconds as fallback
                setTimeout(function () {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }, 10000);
            }
        });
    });
});

// ============================================================
// TOAST HELPER
// ============================================================
window.showToast = function (message, type = 'success') {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const icons = {
        success: 'bi-check-circle-fill',
        danger:  'bi-x-circle-fill',
        warning: 'bi-exclamation-triangle-fill',
        info:    'bi-info-circle-fill',
    };

    const id   = 'toast-' + Date.now();
    const icon = icons[type] || icons.info;

    const toastHtml = `
        <div id="${id}" class="toast align-items-center text-bg-${type} border-0 shadow-sm" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="bi ${icon}"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', toastHtml);
    const toastEl = document.getElementById(id);
    const toast   = new bootstrap.Toast(toastEl, { delay: 4000 });
    toast.show();

    toastEl.addEventListener('hidden.bs.toast', function () {
        toastEl.remove();
    });
};

// ============================================================
// FORMAT CURRENCY (Rupiah)
// ============================================================
window.formatRupiah = function (amount) {
    return 'Rp ' + Number(amount).toLocaleString('id-ID');
};

// ============================================================
// NUMBER INPUT: Only allow numbers
// ============================================================
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-numeric]').forEach(function (el) {
        el.addEventListener('keypress', function (e) {
            if (!/[0-9.]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Tab') {
                e.preventDefault();
            }
        });
    });
});

// ============================================================
// TOOLTIP INIT
// ============================================================
document.addEventListener('DOMContentLoaded', function () {
    const tooltipEls = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipEls.forEach(function (el) {
        new bootstrap.Tooltip(el, { trigger: 'hover' });
    });
});
