<?php
require_once __DIR__ . '/../../src/bootstrap.php';
require_role('doctor');

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
                </tr>
                <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td><?= e($appointment['appointmentID']) ?></td>
                    <td><?= e($appointment['patient_name']) ?></td>
                    <td><?= e($appointment['date']) ?></td>
                    <td><?= e($appointment['time']) ?></td>
                    <td><span class="badge badge-<?= e($appointment['status']) ?>"><?= e($appointment['status']) ?></span></td>
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
