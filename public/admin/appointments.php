<?php
require_once __DIR__ . '/../../src/bootstrap.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf();

    $allowedStatuses = ['requested', 'confirmed', 'completed', 'cancelled'];
    $status = $_POST['status'] ?? '';

    if (!in_array($status, $allowedStatuses, true)) {
        flash('error', 'Invalid status.');
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE appointment SET status = ? WHERE appointmentID = ?");
            $stmt->execute([$status, $_POST['appointmentID'] ?? '']);
            flash('success', 'Updated appointment successfully');
        } catch (PDOException $e) {
            error_log('Appointment update failed: ' . $e->getMessage());
            flash('error', 'Failed to update appointment.');
        }
    }

    redirect('/admin/appointments.php');
}

// Appointments for the dropdown come from the database
$appointments = $pdo->query(
    "SELECT a.appointmentID, p.full_name AS patient_name, d.full_name AS doctor_name, a.`date`, a.status
     FROM appointment a
     JOIN patients p ON p.patientID = a.patientID
     JOIN doctor d ON d.doctorID = a.doctorID
     ORDER BY a.`date`"
)->fetchAll();

$pageTitle = 'Manage Appointments';
require __DIR__ . '/../../src/partials/head.php';
require __DIR__ . '/../../src/partials/dashboard_header.php';
?>

    <form method="POST" action="" class="stacked-form">
        <header><h2>Update Appointment Status</h2></header>

        <?php render_flash(); ?>

        <table>
            <tr>
                <td><label for="appointmentID">Select Appointment:</label></td>
                <td>
                    <select id="appointmentID" name="appointmentID" required>
                        <option value="">Select Appointment</option>
                        <?php foreach ($appointments as $appointment): ?>
                            <option value="<?= e($appointment['appointmentID']) ?>">
                                <?= e($appointment['patient_name']) ?> - Dr <?= e($appointment['doctor_name']) ?> - <?= e($appointment['date']) ?> (<?= e($appointment['status']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="status">Update Status:</label></td>
                <td>
                    <select id="status" name="status" required>
                        <option value="">Select Status</option>
                        <option value="requested">Requested</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><button type="submit">Update Appointment</button></td>
            </tr>
        </table>
        <?= csrf_field() ?>
    </form>

</body>
</html>
