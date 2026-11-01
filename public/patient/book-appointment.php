<?php
require_once __DIR__ . '/../../src/bootstrap.php';
require_role('patient');

$success = "";
$error = "";

// Doctors for the dropdown come from the database, not hardcoded options
$doctors = $pdo->query("SELECT doctorID, full_name, specialisation FROM doctor ORDER BY full_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf();

    $doctorID = $_POST['doctorID'] ?? '';
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';

    if ($doctorID === '' || $date === '' || $time === '') {
        $error = "Please fill in all fields.";
    } elseif ($date < date('Y-m-d')) {
        $error = "Appointments cannot be booked in the past.";
    } else {
        try {
            // The patient comes from the session and the status is always 'requested':
            // never trust the browser for either.
            $stmt = $pdo->prepare("INSERT INTO appointment
            (patientID, doctorID, `date`, time, status)
            VALUES (?,?,?,?,'requested')");
            $stmt->execute([$_SESSION['user_id'], $doctorID, $date, $time]);
            $success = "Appointment booked successfully";
        } catch (PDOException $e) {
            error_log('Booking failed: ' . $e->getMessage());
            $error = "Failed to book appointment. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <script src="<?= BASE_URL ?>/assets/js/validation.js" defer></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
    <form method="post" action="" onsubmit="return validateAppointment()">
        <header><h1>Book an appointment</h1></header>

        <?php if ($success != ""): ?><p class="message success" role="status"><?= e($success) ?></p><?php endif; ?>
        <?php if ($error != ""): ?><p class="message error" role="alert"><?= e($error) ?></p><?php endif; ?>

        <table>
            <tr>
                <td><label for="doctorID">Doctor:</label></td>
                <td><select id="doctorID" name="doctorID" required>
                    <option value="">Select a Doctor</option>
                    <?php foreach ($doctors as $doctor): ?>
                        <option value="<?= e($doctor['doctorID']) ?>">
                            <?= e($doctor['full_name']) ?> - <?= e($doctor['specialisation']) ?>
                        </option>
                    <?php endforeach; ?>
                </select></td>
            </tr>
            <tr>
                <td><label for="date">Date:</label></td>
                <td><input type="date" id="date" name="date" min="<?= date('Y-m-d') ?>" required></td>
            </tr>
            <tr>
                <td><label for="time">Time:</label></td>
                <td><input type="time" id="time" name="time" required></td>
            </tr>
            <tr>
                <td><button type="submit" id="submitBtn">Book</button></td>
                <td><a href="dashboard.php" class="btn btn-secondary">Back to dashboard</a></td>
            </tr>
        </table>
        <?= csrf_field() ?>
    </form>
</body>
</html>
