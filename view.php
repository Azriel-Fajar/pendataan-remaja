<?php
require_once 'includes/config.php';
require_once 'includes/storage.php';
require_once 'includes/header.php';

// Handle date input securely
$selectedDate = date('Y-m-d'); // Default to today
if (isset($_GET['date'])) {
    $inputDate = $_GET['date'];
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $inputDate)) {
        $selectedDate = $inputDate;
    }
}

$message = '';
$availableDates = [];
$attendance = [];

try {
    $availableDates = getAllDates();
    $attendance = getAttendancesByDate($selectedDate);
} catch (Exception $e) {
    $message = '<div class="alert alert-danger">Error loading attendance data: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>

<div class="row justify-content-center">
    <div class="col-12">
        <h2 class="text-center mb-3 mb-sm-4">Attendance Records</h2>

        <?php echo $message; ?>

        <div class="card shadow mb-3 mb-sm-4">
            <div class="card-body p-2 p-sm-3">
                <form method="GET" action="view.php" class="row g-2 g-sm-3 align-items-end">
                    <div class="col-12 col-sm-8 col-md-9">
                        <label for="date" class="form-label">Select Date</label>
                        <select class="form-select form-select-lg" id="date" name="date" required>
                            <?php if (empty($availableDates)): ?>
                                <option value="<?php echo $selectedDate; ?>" selected>
                                    <?php echo date('F j, Y', strtotime($selectedDate)); ?> (Only date available)
                                </option>
                            <?php else: ?>
                                <?php foreach ($availableDates as $date): ?>
                                    <option value="<?php echo htmlspecialchars($date); ?>" <?php echo $date === $selectedDate ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars(date('F j, Y', strtotime($date))); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-12 col-sm-4 col-md-3">
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-search"></i> <span class="d-none d-sm-inline">View</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-body p-2 p-sm-3">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-2 mb-sm-0">
                        Attendance for <?php echo htmlspecialchars(date('F j, Y', strtotime($selectedDate))); ?>
                    </h5>
                    <span class="badge bg-primary rounded-pill fs-6">
                        <?php echo count($attendance); ?> member<?php echo count($attendance) !== 1 ? 's' : ''; ?>
                    </span>
                </div>

                <?php if (empty($attendance)): ?>
                    <div class="alert alert-info my-2">
                        <i class="fas fa-info-circle me-2"></i>No attendance records found for this date.
                    </div>
                <?php else: ?>
                    <div class="table-responsive overflow-auto" style="max-height: 40vh;">
                        <table class="table table-striped table-hover mb-1">
                            <thead class="table-primary" style="position: sticky; top: 0; z-index: 1;">
                                <tr class="text-center">
                                    <th style="width: 10%">#</th>
                                    <th style="width: 70%">Name</th>
                                    <th style="width: 20%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($attendance as $index => $record): ?>
                                    <tr class="text-center">
                                        <td><?php echo $index + 1; ?></td>
                                        <td style="max-width: 200px;"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="<?php echo htmlspecialchars($record['name']); ?>">
                                            <?php echo htmlspecialchars($record['name']); ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-danger delete-btn"
                                                data-id="<?php echo htmlspecialchars($record['id']); ?>"
                                                data-name="<?php echo htmlspecialchars($record['name']); ?>">
                                                <i class="fas fa-trash-alt"></i> <span class="d-none d-md-inline">Delete</span>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-3 d-flex flex-column flex-sm-row justify-content-between align-items-center">
            <div class="mb-2 mb-sm-0">
                <a href="index.php" class="btn btn-outline-primary">
                    <i class="fas fa-plus-circle"></i> Add New Attendance
                </a>
            </div>
            <div>
                <small class="text-muted">
                    Last updated: <?php echo date('H:i:s'); ?>
                </small>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipList.map(el => new bootstrap.Tooltip(el));

        // Delete confirmation with member name
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const memberName = this.getAttribute('data-name');
                const memberId = this.getAttribute('data-id');

                if (confirm(`Are you sure you want to delete "${memberName}"?`)) {
                    fetch('process.php?action=delete&id=' + encodeURIComponent(memberId))
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                // Remove row and update count
                                const row = this.closest('tr');
                                row.parentNode.removeChild(row);

                                // Update badge count
                                const badge = document.querySelector('.badge');
                                const currentCount = parseInt(badge.textContent);
                                badge.textContent = (currentCount - 1) + ' member' + (currentCount - 1 !== 1 ? 's' : '');

                                // Show success toast
                                showToast('Success', `"${memberName}" deleted successfully`, 'success');
                            } else {
                                throw new Error(data.message || 'Unknown error');
                            }
                        })
                        .catch(error => {
                            showToast('Error', error.message, 'danger');
                        });
                }
            });
        });

        // Toast notification function
        function showToast(title, message, type) {
            const toastContainer = document.getElementById('toast-container') || createToastContainer();
            const toastId = 'toast-' + Date.now();

            const toastHTML = `
            <div id="${toastId}" class="toast show align-items-center text-white bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <strong>${title}</strong>: ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

            toastContainer.insertAdjacentHTML('beforeend', toastHTML);

            // Auto-remove toast after 5 seconds
            setTimeout(() => {
                const toastElement = document.getElementById(toastId);
                if (toastElement) {
                    bootstrap.Toast.getOrCreateInstance(toastElement).hide();
                    toastElement.addEventListener('hidden.bs.toast', () => {
                        toastElement.remove();
                    });
                }
            }, 5000);
        }

        function createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'position-fixed bottom-0 end-0 p-3';
            container.style.zIndex = '11';
            document.body.appendChild(container);
            return container;
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>