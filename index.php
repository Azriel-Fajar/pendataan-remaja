<?php
require_once 'includes/config.php';
require_once 'includes/storage.php';
require_once 'includes/header.php';

$today = date('Y-m-d');
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $date = $_POST['date'] ?? $today;

    if (empty($name)) {
        $message = '<div class="alert alert-danger">Mohon masukkan nama.</div>';
    } else {
        if (saveAttendance($name, $date)) {
            $message = '<div class="alert alert-success">Kehadiran berhasil dicatat!</div>';
        } else {
            $message = '<div class="alert alert-danger">Gagal menyimpan data.</div>';
        }
    }
}
?>

<!-- [Bagian HTML sama seperti sebelumnya] -->

<div class="row justify-content-center">
    <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
        <h2 class="text-center mb-4">Record Attendance</h2>

        <?php echo $message; ?>

        <div class="card shadow">
            <div class="card-body p-3 p-sm-4">
                <form method="POST" action="index.php">
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control form-control-lg" id="date" name="date"
                            value="<?php echo $today; ?>" max="<?php echo $today; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control form-control-lg" id="name" name="name"
                            placeholder="Enter member's name" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="fas fa-check-circle"></i> <span class="d-none d-sm-inline">Record</span> Attendance
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-4 text-center">
            <a href="view.php?date=<?php echo $today; ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-list"></i> <span class="d-none d-sm-inline">View</span> Today's Attendance
            </a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>