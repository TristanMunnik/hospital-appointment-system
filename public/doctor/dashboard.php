<?php
require_once __DIR__ . '/../../src/bootstrap.php';
require_role('doctor');

// Doctors can move their own appointments through these statuses.
// Deleting is admin-only: cancelling keeps the history.
$doctorStatuses = ['confirmed', 'completed', 'cancelled'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf();

    $appointmentID = $_POST['appointmentID'] ?? '';
    $status = $_POST['status'] ?? '';

    if (!in_array($status, $doctorStatuses, true)) {
        flash('error', 'Invalid status.');
    } else {
        // Ownership check: the appointment must belong to the logged-in doctor
        $stmt = $pdo->prepare("SELECT 1 FROM appointment WHERE appointmentID = ? AND doctorID = ?");
        $stmt->execute([$appointmentID, $_SESSION['user_id']]);

        if (!$stmt->fetch()) {
            flash('error', 'Appointment not found.');
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE appointment SET status = ? WHERE appointmentID = ? AND doctorID = ?");
                $stmt->execute([$status, $appointmentID, $_SESSION['user_id']]);
                flash('success', 'Appointment marked as ' . $status . '.');
            } catch (PDOException $e) {
                error_log('Doctor appointment update failed: ' . $e->getMessage());
                flash('error', 'Failed to update appointment. Please try again.');
            }
        }
    }

    // Post/Redirect/Get: a page refresh won't resubmit the form
    redirect('/doctor/dashboard.php');
}

$flash = pull_flash();

$stmt = $pdo->prepare(
    "SELECT a.appointmentID, p.full_name AS patient_name, a.`date`, a.time, a.status
     FROM appointment a
     JOIN patients p ON p.patientID = a.patientID
     WHERE a.doctorID = ?
     ORDER BY a.`date`, a.time"
);
$stmt->execute([$_SESSION['user_id']]);
$appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

    <header class="site-header">
        <h1>Doctor Dashboard</h1>
        <p>Welcome, <?= e($_SESSION['user_name']) ?></p>
        <a href="<?= BASE_URL ?>/logout.php">Logout</a>
    </header>
    <nav class="site-nav">
        <a href="medical-records.php">Add Medical Record</a>
    </nav>
    <?php if ($flash): ?>
        <p class="message <?= e($flash['type']) ?>" role="<?= $flash['type'] == 'error' ? 'alert' : 'status' ?>"><?= e($flash['message']) ?></p>
    <?php endif; ?>

    <main>
        <h2>Your Appointments</h2>
        <?php if (count($appointments) > 0): ?>
            <div class="table-wrap">
            <table>
                <tr>
                    <th>Appointment ID</th>
                    <th>Patient</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Update status</th>
                </tr>
                <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td><?= e($appointment['appointmentID']) ?></td>
                    <td><?= e($appointment['patient_name']) ?></td>
                    <td><?= e($appointment['date']) ?></td>
                    <td><?= e($appointment['time']) ?></td>
                    <td><span class="badge badge-<?= e($appointment['status']) ?>"><?= e($appointment['status']) ?></span></td>
                    <td>
                        <form method="post" action="" class="inline-form">
                            <input type="hidden" name="appointmentID" value="<?= e($appointment['appointmentID']) ?>">
                            <label class="visually-hidden" for="status-<?= e($appointment['appointmentID']) ?>">New status</label>
                            <select id="status-<?= e($appointment['appointmentID']) ?>" name="status">
                                <?php foreach ($doctorStatuses as $option): ?>
                                    <option value="<?= $option ?>"<?= $option == $appointment['status'] ? ' selected' : '' ?>><?= ucfirst($option) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?= csrf_field() ?>
                            <button type="submit" class="btn-small">Save</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            </div>
        <?php else: ?>
            <p class="empty">You have no appointments yet.</p>
        <?php endif; ?>
    </main>

</body>
</html>
