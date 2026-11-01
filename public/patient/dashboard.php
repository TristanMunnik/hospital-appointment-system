<?php
require_once __DIR__ . '/../../src/bootstrap.php';
require_role('patient');

// JOIN so the page can show doctor names instead of raw IDs
$stmt = $pdo->prepare(
    "SELECT a.appointmentID, d.full_name AS doctor_name, a.`date`, a.time, a.status
     FROM appointment a
     JOIN doctor d ON d.doctorID = a.doctorID
     WHERE a.patientID = ?
     ORDER BY a.`date`, a.time"
);
$stmt->execute([$_SESSION['user_id']]);
$appointments = $stmt->fetchAll();

$stmt = $pdo->prepare(
    "SELECT r.recordID, d.full_name AS doctor_name, r.diagnosis, r.treatment, r.`date`
     FROM medical_record r
     JOIN doctor d ON d.doctorID = r.doctorID
     WHERE r.patientID = ?
     ORDER BY r.`date` DESC"
);
$stmt->execute([$_SESSION['user_id']]);
$records = $stmt->fetchAll();

$pageTitle = 'Patient Dashboard';
require __DIR__ . '/../../src/partials/head.php';
require __DIR__ . '/../../src/partials/dashboard_header.php';
?>

    <main>
        <h2>Your Appointments:</h2>
        <?php if (count($appointments) > 0): ?>
            <div class="table-wrap">
            <table>
                <tr>
                    <th>Appointment ID</th>
                    <th>Doctor</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td><?= e($appointment['appointmentID']) ?></td>
                    <td><?= e($appointment['doctor_name']) ?></td>
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

        <h2>My Medical Records:</h2>
        <?php if (count($records) > 0): ?>
            <div class="table-wrap">
            <table>
                <tr>
                    <th>Record ID</th>
                    <th>Doctor</th>
                    <th>Diagnosis</th>
                    <th>Treatment</th>
                    <th>Date</th>
                </tr>
                <?php foreach ($records as $record): ?>
                <tr>
                    <td><?= e($record['recordID']) ?></td>
                    <td><?= e($record['doctor_name']) ?></td>
                    <td><?= e($record['diagnosis']) ?></td>
                    <td><?= e($record['treatment']) ?></td>
                    <td><?= e($record['date']) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            </div>
        <?php else: ?>
            <p class="empty">No medical records found.</p>
        <?php endif; ?>

    </main>

</body>
</html>
