/*
 * Global UI interactions for Vaccination Management System.
 * This file is safe to include across pages; handlers are attached only when elements exist.
 */
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('statusUpdateForm');
    if (!form) {
        return;
    }

    // Cached elements for update status page interactions.
    var statusSelect = document.getElementById('appointmentStatus');
    var vaccinationDate = document.getElementById('vaccinationDate');
    var notesField = document.getElementById('doctorNotes');
    var submitButton = document.getElementById('updateStatusBtn');
    var resetButton = document.getElementById('resetStatusBtn');

    var currentStatusBadge = document.getElementById('currentStatusBadge');
    var timelineStatusBadge = document.getElementById('timelineStatusBadge');
    var timelineStatusIcon = document.getElementById('timelineStatusIcon');
    var timelineStatusTime = document.getElementById('timelineStatusTime');

    var successAlert = document.getElementById('statusSuccessAlert');
    var errorAlert = document.getElementById('statusErrorAlert');
    var successMessage = document.getElementById('statusSuccessMessage');
    var errorMessage = document.getElementById('statusErrorMessage');

    var quickActionButtons = {
        print: document.getElementById('printRecordBtn'),
        profile: document.getElementById('viewProfileBtn'),
        back: document.getElementById('backAppointmentsBtn')
    };

    var statusConfig = {
        completed: {
            label: 'Completed',
            badgeClass: 'bg-success-subtle text-success-emphasis',
            iconClass: 'bg-success-subtle text-success-emphasis',
            icon: 'fas fa-circle-check'
        },
        pending: {
            label: 'Pending',
            badgeClass: 'bg-warning-subtle text-warning-emphasis',
            iconClass: 'bg-warning-subtle text-warning-emphasis',
            icon: 'fas fa-hourglass-half'
        },
        missed: {
            label: 'Missed',
            badgeClass: 'bg-danger-subtle text-danger-emphasis',
            iconClass: 'bg-danger-subtle text-danger-emphasis',
            icon: 'fas fa-user-xmark'
        },
        cancelled: {
            label: 'Cancelled',
            badgeClass: 'bg-secondary-subtle text-secondary-emphasis',
            iconClass: 'bg-secondary-subtle text-secondary-emphasis',
            icon: 'fas fa-ban'
        }
    };

    function hideAlerts() {
        successAlert.classList.add('d-none');
        errorAlert.classList.add('d-none');
    }

    function showSuccess(message) {
        errorAlert.classList.add('d-none');
        successMessage.textContent = message;
        successAlert.classList.remove('d-none');
    }

    function showError(message) {
        successAlert.classList.add('d-none');
        errorMessage.textContent = message;
        errorAlert.classList.remove('d-none');
    }

    function getReadableDate(dateValue) {
        if (!dateValue) {
            return 'Date not provided';
        }

        var date = new Date(dateValue + 'T00:00:00');
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: '2-digit'
        });
    }

    function getReadableTime() {
        return new Date().toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function applyStatus(statusValue) {
        var config = statusConfig[statusValue];
        if (!config) {
            return;
        }

        currentStatusBadge.className = 'badge rounded-pill px-3 py-2 ' + config.badgeClass;
        currentStatusBadge.textContent = config.label;

        timelineStatusBadge.className = 'badge rounded-pill ' + config.badgeClass;
        timelineStatusBadge.textContent = config.label;

        timelineStatusIcon.className = 'timeline-icon ' + config.iconClass;
        timelineStatusIcon.innerHTML = '<i class="' + config.icon + '"></i>';

        timelineStatusTime.textContent = getReadableDate(vaccinationDate.value) + ' - ' + getReadableTime();
    }

    function validateForm(statusValue) {
        if (!statusValue) {
            return 'Please select a status before updating.';
        }

        if (!vaccinationDate.value) {
            return 'Please select the vaccination date.';
        }

        if (statusValue === 'completed' && notesField.value.trim().length < 8) {
            return 'Please provide at least a short completion note (8+ characters).';
        }

        if (statusValue === 'cancelled' && notesField.value.trim().length < 12) {
            return 'Please provide a cancellation reason (12+ characters).';
        }

        return '';
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        hideAlerts();

        var selectedStatus = statusSelect.value;
        var validationMessage = validateForm(selectedStatus);

        if (validationMessage) {
            showError(validationMessage);
            return;
        }

        submitButton.disabled = true;
        var originalButtonHtml = submitButton.innerHTML;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Updating...';

        // Simulate an async frontend-only update process.
        setTimeout(function () {
            applyStatus(selectedStatus);
            showSuccess('Appointment status updated to "' + statusConfig[selectedStatus].label + '" (UI simulation only).');
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonHtml;
        }, 700);
    });

    resetButton.addEventListener('click', function () {
        // Reset hides alerts and sets timeline time back to placeholder after browser resets fields.
        setTimeout(function () {
            hideAlerts();
            timelineStatusTime.textContent = 'Waiting for update';
            applyStatus(statusSelect.value || 'pending');
        }, 0);
    });

    if (quickActionButtons.print) {
        quickActionButtons.print.addEventListener('click', function () {
            showSuccess('Print action clicked. Printing is disabled in this frontend demo.');
        });
    }

    if (quickActionButtons.profile) {
        quickActionButtons.profile.addEventListener('click', function () {
            showSuccess('View Child Profile clicked. Navigation is disabled in this frontend demo.');
        });
    }

    if (quickActionButtons.back) {
        quickActionButtons.back.addEventListener('click', function () {
            showSuccess('Back to Appointments clicked. Navigation is disabled in this frontend demo.');
        });
    }

    statusSelect.addEventListener('change', function () {
        hideAlerts();
    });
});
