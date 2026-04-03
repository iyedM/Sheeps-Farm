/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * or Webpack Encore if you're using it (which we are).
 */

import './styles/app.css';
import './styles/custom.css';

// Global Initialization
document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initThemeToggle();
    initToasts();
    initAOS();
    initCountUp();
    initSelect2();
    initFlatpickr();
    initDataTables();
    initFormLoading();
    initChartJsConfig();
    initCollectionForms();
});

/* ── Sidebar Toggle & Collapse ── */
function initSidebar() {
    const sidebar = document.getElementById('sidebar');
    const collapseBtn = document.querySelector('.sidebar-collapse-btn');
    const mobileToggle = document.querySelector('.topbar-mobile-toggle');
    const backdrop = document.createElement('div');

    backdrop.className = 'sidebar-backdrop';
    document.body.appendChild(backdrop);

    // Desktop Collapse
    if (collapseBtn) {
        collapseBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            const icon = collapseBtn.querySelector('i');
            if (icon) {
                if (sidebar.classList.contains('collapsed')) {
                    icon.classList.replace('bi-layout-sidebar', 'bi-layout-sidebar-inset');
                } else {
                    icon.classList.replace('bi-layout-sidebar-inset', 'bi-layout-sidebar');
                }
            }
        });
    }

    // Mobile Toggle
    if (mobileToggle) {
        mobileToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            backdrop.classList.toggle('show');
        });
    }

    // Close mobile sidebar when clicking backdrop
    backdrop.addEventListener('click', () => {
        sidebar.classList.remove('show');
        backdrop.classList.remove('show');
    });

    // Active link highlighting based on current URL path
    const currentPath = window.location.pathname;
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        if (item.getAttribute('href') === currentPath) {
            item.classList.add('active');
        } else {
            // Check if it's a parent path (basic heuristic)
            const href = item.getAttribute('href');
            if (href !== '/' && href !== '#' && currentPath.startsWith(href)) {
                item.classList.add('active');
            }
        }
    });
}

/* ── Theme Toggle ── */
function initThemeToggle() {
    const themeBtn = document.querySelector('.theme-toggle');
    if (!themeBtn) return;

    // Load saved theme or system preference
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.setAttribute('data-theme', 'dark');
        themeBtn.innerHTML = '<i class="bi bi-sun-fill"></i>';
    } else {
        document.documentElement.removeAttribute('data-theme');
        themeBtn.innerHTML = '<i class="bi bi-moon-stars-fill"></i>';
    }

    themeBtn.addEventListener('click', () => {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        if (currentTheme === 'dark') {
            document.documentElement.removeAttribute('data-theme');
            localStorage.setItem('theme', 'light');
            themeBtn.innerHTML = '<i class="bi bi-moon-stars-fill"></i>';
        } else {
            document.documentElement.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
            themeBtn.innerHTML = '<i class="bi bi-sun-fill"></i>';
        }
    });
}

/* ── Flash Toasts ── */
function initToasts() {
    const toasts = document.querySelectorAll('.app-toast');
    toasts.forEach(toast => {
        const autoDismissTime = parseInt(toast.getAttribute('data-auto-dismiss') || '4000', 10);
        const progressBar = toast.querySelector('.toast-progress');
        const closeBtn = toast.querySelector('.toast-close');

        if (progressBar && autoDismissTime > 0) {
            progressBar.style.animationDuration = `${autoDismissTime}ms`;
        }

        let dismissTimeout;
        if (autoDismissTime > 0) {
            dismissTimeout = setTimeout(() => {
                dismissToast(toast);
            }, autoDismissTime);
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                if (dismissTimeout) clearTimeout(dismissTimeout);
                dismissToast(toast);
            });
        }
    });
}

function dismissToast(toast) {
    toast.style.animation = 'fadeOut 300ms var(--spring) forwards';
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 300);
}

/* ── AOS (Animate On Scroll) ── */
function initAOS() {
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 400,
            easing: 'ease-out-cubic',
            once: true,
            offset: 40
        });
    }
}

/* ── CountUp.js ── */
function initCountUp() {
    if (typeof CountUp !== 'undefined') {
        document.querySelectorAll('[data-countup]').forEach(el => {
            const target = el.dataset.target || el.innerText.replace(/[^0-9.-]+/g, '');
            const countUp = new CountUp(el, parseFloat(target), {
                duration: 1.8,
                separator: ' ',
                decimal: ',',
                useEasing: true,
                easingFn: (t, b, c, d) => c * (1 - Math.pow(1 - t / d, 3)) + b
            });
            if (!countUp.error) {
                countUp.start();
            } else {
                console.error(countUp.error);
            }
        });
    }
}

/* ── Select2 ── */
function initSelect2() {
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
        jQuery('.select2-enabled').select2({
            width: '100%',
            minimumResultsForSearch: 5
        });
    }
}

/* ── Flatpickr ── */
function initFlatpickr() {
    if (typeof flatpickr !== 'undefined') {
        flatpickr('.datepicker', {
            dateFormat: "Y-m-d",
            allowInput: true
        });

        flatpickr('.datetimepicker', {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            allowInput: true
        });
    }
}

/* ── DataTables Premium Style ── */
function initDataTables() {
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.DataTable !== 'undefined') {
        jQuery('table.dt-table').each(function () {
            const table = jQuery(this);
            const isServer = table.hasClass('dt-server');

            table.DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
                },
                pageLength: 10,
                responsive: true,
                autoWidth: false,
                dom: isServer ? 'rt' : 'rt<"dt-footer"ip>',
                paging: !isServer,
                info: !isServer,
                ordering: !isServer,
                columnDefs: [{ orderable: false, targets: [-1] }],
                initComplete: function () {
                    const api = this.api();
                    // Connect external search input if it exists
                    const searchInput = document.getElementById('tableSearch');
                    if (searchInput) {
                        searchInput.addEventListener('input', function () {
                            api.search(this.value).draw();
                        });
                    }
                }
            });
        });
    }
}

/* ── SweetAlert2 Delete Confirmation ── */
window.confirmDelete = function (formId, label = 'cet élément') {
    if (typeof Swal === 'undefined') return;

    Swal.fire({
        title: 'Supprimer ' + label + ' ?',
        html: `<p style="font-size:14px;color:#64748B;margin:0">
          Cette action est <strong>irréversible</strong>. 
          Toutes les données liées seront perdues.</p>`,
        icon: 'warning',
        iconColor: '#FF4757',
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-trash-fill"></i>&nbsp; Supprimer',
        cancelButtonText: 'Annuler',
        confirmButtonColor: '#FF4757',
        cancelButtonColor: '#64748B',
        reverseButtons: true,
        focusCancel: true,
        customClass: {
            popup: 'swal2-farm-popup',
            title: 'swal2-farm-title',
            actions: 'swal2-farm-actions',
            confirmButton: 'swal2-confirm',
            cancelButton: 'swal2-cancel'
        },
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
};

/* ── Form Submit Loading State ── */
function initFormLoading() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function () {
            // basic check for html5 validity before adding loading state
            if (form.checkValidity()) {
                const submitBtns = form.querySelectorAll('.btn-submit');
                submitBtns.forEach(btn => {
                    btn.classList.add('loading');
                    // Prevent double submission visually
                    btn.disabled = true;
                    // Note: if disabled = true stops the form submission in some browsers,
                    // we might need a hidden input or handle via JS.
                    // For safe fallback, just remove pointer events.
                    btn.style.pointerEvents = 'none';
                });
            }
        });
    });
}

/* ── Chart.js Global Config ── */
function initChartJsConfig() {
    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.font.size = 12;
        Chart.defaults.color = '#94A3B8';
        Chart.defaults.plugins.legend.labels.boxWidth = 8;
        Chart.defaults.plugins.legend.labels.borderRadius = 4;
        Chart.defaults.plugins.legend.labels.usePointStyle = true;
        Chart.defaults.plugins.tooltip.backgroundColor = '#0D1426';
        Chart.defaults.plugins.tooltip.padding = 12;
        Chart.defaults.plugins.tooltip.cornerRadius = 10;
        Chart.defaults.plugins.tooltip.titleFont = { weight: '600', size: 13 };
        Chart.defaults.plugins.tooltip.bodyColor = 'rgba(255,255,255,0.7)';

        // Disable responsive resizing animation for snappier feel occasionally
        // Chart.defaults.animation = false;
    }
}

/* ── Dynamic Collection (Symfony Forms) ── */
function initCollectionForms() {
    const addSublotBtns = document.querySelectorAll('.btn-add-sublot');

    addSublotBtns.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const collectionHolder = document.querySelector(this.dataset.collectionHolderClass);
            if (!collectionHolder) return;

            const template = collectionHolder.dataset.prototype;
            const index = collectionHolder.dataset.index;

            // Replace '__name__' with the current index
            let newForm = template.replace(/__name__/g, index);

            // Create DOM element for row wrapper
            const rowDiv = document.createElement('div');
            rowDiv.className = 'sublot-row';
            rowDiv.setAttribute('data-aos', 'fade-up');

            // Sublot number
            const numDiv = document.createElement('div');
            numDiv.className = 'sublot-number';
            numDiv.innerText = (parseInt(index) + 1).toString().padStart(2, '0');

            // Fields wrapper
            const fieldsDiv = document.createElement('div');
            fieldsDiv.className = 'sublot-fields';
            fieldsDiv.innerHTML = newForm;

            // Remove button
            const removeBtn = document.createElement('button');
            removeBtn.className = 'sublot-remove';
            removeBtn.type = 'button';
            removeBtn.innerHTML = '<i class="bi bi-x-lg"></i>';
            removeBtn.addEventListener('click', function () {
                rowDiv.remove();
                updateCollectionTotal();
                updateSublotNumbers(collectionHolder);
            });

            rowDiv.appendChild(numDiv);
            rowDiv.appendChild(fieldsDiv);
            rowDiv.appendChild(removeBtn);

            collectionHolder.appendChild(rowDiv);

            collectionHolder.dataset.index = parseInt(index) + 1;

            // Re-initialize plugins for new elements
            initSelect2();
            initFlatpickr();

            // Attach calculation listeners to new fields
            attachCalculationListeners(rowDiv);
        });
    });

    // Handle existing remove buttons
    document.querySelectorAll('.sublot-remove').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const row = this.closest('.sublot-row');
            const holder = row.closest('.collection-holder');
            row.remove();
            updateCollectionTotal();
            if (holder) updateSublotNumbers(holder);
        });
    });

    // Initial attach
    document.querySelectorAll('.sublot-row').forEach(row => {
        attachCalculationListeners(row);
    });

    updateCollectionTotal();
}

function updateSublotNumbers(holder) {
    const rows = holder.querySelectorAll('.sublot-row');
    rows.forEach((row, idx) => {
        const numDiv = row.querySelector('.sublot-number');
        if (numDiv) numDiv.innerText = (idx + 1).toString().padStart(2, '0');
    });
}

function attachCalculationListeners(row) {
    // Modify selectors based on your actual form field classes/names 
    const qtyInputs = row.querySelectorAll('.calc-qty');
    const priceInputs = row.querySelectorAll('.calc-price');
    const totalInput = row.querySelector('.calc-total'); // if read-only field

    const calculateRow = () => {
        let total = 0;
        let qty = 0;
        let price = 0;
        if (qtyInputs.length > 0) qty = parseFloat(qtyInputs[0].value) || 0;
        if (priceInputs.length > 0) price = parseFloat(priceInputs[0].value) || 0;

        total = qty * price;
        if (totalInput) totalInput.value = total.toFixed(3);
        updateCollectionTotal();
    };

    qtyInputs.forEach(input => input.addEventListener('input', calculateRow));
    priceInputs.forEach(input => input.addEventListener('input', calculateRow));
}

function updateCollectionTotal() {
    const grandTotalEl = document.getElementById('grandTotal');
    if (!grandTotalEl) return;

    let total = 0;
    // Assume each row has a .calc-total input or we calculate it manually if it doesn't
    const rows = document.querySelectorAll('.sublot-row');
    rows.forEach(row => {
        const qtyInputs = row.querySelectorAll('.calc-qty');
        const priceInputs = row.querySelectorAll('.calc-price');

        let qty = 0;
        let price = 0;
        if (qtyInputs.length > 0) qty = parseFloat(qtyInputs[0].value) || 0;
        if (priceInputs.length > 0) price = parseFloat(priceInputs[0].value) || 0;

        total += (qty * price);
    });

    // Format TND
    const formatted = new Intl.NumberFormat('fr-TN', { style: 'currency', currency: 'TND' }).format(total);
    grandTotalEl.innerText = formatted;
}

/* ── Helper: Remove Filter Chip ── */
window.removeFilter = function (filterName) {
    // Logic to update the form and submit or redirect
    console.log('Remove filter:', filterName);
    const chip = document.querySelector(`.filter-chip[data-filter="${filterName}"]`);
    if (chip) chip.remove();
    // Submit form/reload here...
};
