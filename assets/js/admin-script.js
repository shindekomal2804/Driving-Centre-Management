/**
 * DriveEasy - Admin Dashboard JavaScript
 * Main script for admin dashboard
 */

// Document Ready
document.addEventListener('DOMContentLoaded', function() {
    initializeAdmin();
});

function initializeAdmin() {
    // Initialize responsive sidebar
    handleSidebarToggle();
    
    // Initialize data tables
    initializeDataTables();
    
    // Initialize charts if present
    initializeCharts();
    
    // Setup form handlers
    setupAdminForms();
    
    // Confirmation dialogs
    setupConfirmDialogs();
}

/**
 * Handle sidebar toggle on mobile
 */
function handleSidebarToggle() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
    }
}

/**
 * Initialize data tables with sorting and pagination
 */
function initializeDataTables() {
    const tables = document.querySelectorAll('.admin-table');
    
    tables.forEach(table => {
        // Add striped rows
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach((row, index) => {
            if (index % 2 === 0) {
                row.classList.add('table-light');
            }
        });
        
        // Add hover effect
        rows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f1f3f5';
            });
            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
        });
    });
}

/**
 * Initialize charts
 */
function initializeCharts() {
    // Revenue Chart
    const revenueChart = document.getElementById('revenueChart');
    if (revenueChart) {
        new Chart(revenueChart, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Monthly Revenue',
                    data: [12000, 15000, 13000, 18000, 22000, 20000, 24000, 25000, 23000, 26000, 28000, 30000],
                    borderColor: '#1e63d9',
                    backgroundColor: 'rgba(30, 99, 217, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#1e63d9',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Bookings Chart
    const bookingsChart = document.getElementById('bookingsChart');
    if (bookingsChart) {
        new Chart(bookingsChart, {
            type: 'bar',
            data: {
                labels: ['Pending', 'Approved', 'Completed', 'Cancelled'],
                datasets: [{
                    label: 'Bookings',
                    data: [15, 45, 32, 8],
                    backgroundColor: [
                        '#ffc107',
                        '#28a745',
                        '#1e63d9',
                        '#dc3545'
                    ],
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    // Course Enrollment Chart
    const enrollmentChart = document.getElementById('enrollmentChart');
    if (enrollmentChart) {
        new Chart(enrollmentChart, {
            type: 'doughnut',
            data: {
                labels: ['Beginner', 'Intermediate', 'Advanced'],
                datasets: [{
                    data: [45, 30, 25],
                    backgroundColor: ['#1e63d9', '#ffc107', '#28a745'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
}

/**
 * Setup admin forms
 */
function setupAdminForms() {
    const forms = document.querySelectorAll('.admin-form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateAdminForm(this)) {
                e.preventDefault();
            }
        });
    });
}

/**
 * Validate admin form
 */
function validateAdminForm(form) {
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (field.value.trim() === '') {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

/**
 * Setup confirmation dialogs for delete actions
 */
function setupConfirmDialogs() {
    const deleteLinks = document.querySelectorAll('[data-action="delete"]');
    
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const itemName = this.dataset.name || 'this item';
            if (confirm(`Are you sure you want to delete "${itemName}"? This action cannot be undone.`)) {
                window.location.href = this.href;
            }
        });
    });
}

/**
 * Show admin toast notification
 */
function showAdminToast(message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer') || createToastContainer();
    
    const toastId = 'toast_' + Date.now();
    const typeClass = type === 'success' ? 'bg-success' : 
                     type === 'error' ? 'bg-danger' : 
                     type === 'warning' ? 'bg-warning' : 'bg-info';
    
    const toastHTML = `
        <div id="${toastId}" class="toast align-items-center text-white ${typeClass} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-mdb-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    
    const toastElement = document.getElementById(toastId);
    const toast = new mdb.Toast(toastElement);
    toast.show();
    
    setTimeout(() => {
        toastElement.remove();
    }, 3000);
}

/**
 * Create toast container if it doesn't exist
 */
function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.style.position = 'fixed';
    container.style.top = '20px';
    container.style.right = '20px';
    container.style.zIndex = '9999';
    container.style.minWidth = '250px';
    document.body.appendChild(container);
    return container;
}

/**
 * Show loading overlay
 */
function showLoadingOverlay(show = true) {
    let overlay = document.getElementById('loadingOverlay');
    
    if (!overlay && show) {
        overlay = document.createElement('div');
        overlay.id = 'loadingOverlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        `;
        overlay.innerHTML = `
            <div class="spinner-border text-white" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        `;
        document.body.appendChild(overlay);
    }
    
    if (overlay) {
        overlay.style.display = show ? 'flex' : 'none';
    }
}

/**
 * Export data to CSV
 */
function exportToCSV(tableId, filename = 'export.csv') {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const csvRow = [];
        cols.forEach(col => {
            csvRow.push('"' + col.innerText.replace(/"/g, '""') + '"');
        });
        csv.push(csvRow.join(','));
    });
    
    downloadCSV(csv.join('\n'), filename);
}

/**
 * Download CSV file
 */
function downloadCSV(csv, filename) {
    const link = document.createElement('a');
    link.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
    link.download = filename;
    link.click();
}

/**
 * Admin logout confirmation
 */
function confirmLogout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = '../api/admin-logout.php';
    }
    return false;
}
