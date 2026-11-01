<?php
require_once __DIR__ . '/../../src/bootstrap.php';
require_role('admin');

$success = "";
$error = "";

// Deleting changes data, so it must be a POST with a CSRF token (a GET link can be triggered by any other site)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['appointmentID'])) {
    verify_csrf();

    try {
        $stmt = $pdo->prepare("DELETE FROM appointment WHERE appointmentID = ?");
        $stmt->execute([$_POST['appointmentID']]);
        $success = "Appointment deleted successfully";
    } catch (PDOException $e) {
        error_log('Appointment delete failed: ' . $e->getMessage());
        $error = "Failed to delete appointment.";
    }
}

$appointments = $pdo->query(
    "SELECT a.appointmentID, p.full_name AS patient_name, d.full_name AS doctor_name,
            a.`date`, a.time, a.status
     FROM appointment a
     JOIN patients p ON p.patientID = a.patientID
     JOIN doctor d ON d.doctorID = a.doctorID
     ORDER BY a.`date`, a.time"
)->fetchAll();

$pageTitle = 'Admin Dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

    <?php require __DIR__ . '/../../src/partials/admin_header.php'; ?>

    <main>
        <h2>All Appointments</h2>
        <p>Use the navigation above to manage users and appointments.</p>

        <?php if ($success != ""): ?><p class="message success" role="status"><?= e($success) ?></p><?php endif; ?>
        <?php if ($error != ""): ?><p class="message error" role="alert"><?= e($error) ?></p><?php endif; ?>

        <?php if (count($appointments) > 0): ?>
            <div class="table-wrap">
            <table>
                <tr>
                    <th>Appointment ID</th>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td><?= e($appointment['appointmentID']) ?></td>
                    <td><?= e($appointment['patient_name']) ?></td>
                    <td><?= e($appointment['doctor_name']) ?></td>
                    <td><?= e($appointment['date']) ?></td>
                    <td><?= e($appointment['time']) ?></td>
                    <td><span class="badge badge-<?= e($appointment['status']) ?>"><?= e($appointment['status']) ?></span></td>
                    <td>
                        <form method="post" action="" onsubmit="return confirm('Are you sure you want to delete this appointment?')">
                            <input type="hidden" name="appointmentID" value="<?= e($appointment['appointmentID']) ?>">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            </div>
        <?php else: ?>
            <p class="empty">There are no appointments yet.</p>
        <?php endif; ?>

    </main>

</body>
</html>
