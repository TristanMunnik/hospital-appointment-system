<?php
require_once __DIR__ . '/../../src/bootstrap.php';
require_role('admin');

$success = "";
$error = "";
$formType = $_POST['form_type'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf();

    if ($formType == 'add_user') {
        try {
            $role = $_POST['roleSelect'] ?? '';
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = password_hash($_POST['password'] ?? '', PASSWORD_BCRYPT);
            $specialisation = trim($_POST['specialisation'] ?? '');
            $dateOfBirth = $_POST['dateOfBirth'] ?? '';
            $phoneNumber = trim($_POST['phoneNumber'] ?? '');

            if ($role == 'patient') {
                $stmt = $pdo->prepare("INSERT INTO patients
                (full_name, email, `password`, phone, dateOfBirth)
                VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $email, $password, $phoneNumber, $dateOfBirth]);
            } elseif ($role == 'doctor') {
                $stmt = $pdo->prepare("INSERT INTO doctor
                (full_name, email, `password`, specialisation)
                VALUES (?,?,?,?)");
                $stmt->execute([$name, $email, $password, $specialisation]);
            } elseif ($role == 'admin') {
                $stmt = $pdo->prepare("INSERT INTO admin
                (full_name, email, `password`)
                VALUES (?,?,?)");
                $stmt->execute([$name, $email, $password]);
            } else {
                throw new InvalidArgumentException('Unknown role');
            }
            $success = "User has been added successfully";

        } catch (PDOException | InvalidArgumentException $e) {
            error_log('Add user failed: ' . $e->getMessage());
            $error = "Failed to add user. Email may already exist.";
        }

    } elseif ($formType == 'manage_appointment') {
        $allowedStatuses = ['requested', 'confirmed', 'completed', 'cancelled'];
        $status = $_POST['status'] ?? '';

        if (!in_array($status, $allowedStatuses, true)) {
            $error = "Invalid status.";
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE appointment SET status = ? WHERE appointmentID = ?");
                $stmt->execute([$status, $_POST['appointmentID'] ?? '']);
                $success = "Updated appointment successfully";
            } catch (PDOException $e) {
                error_log('Appointment update failed: ' . $e->getMessage());
                $error = "Failed to update appointment.";
            }
        }
    }
}

// Appointments for the dropdown come from the database (loaded after any update so labels are current)
$appointments = $pdo->query(
    "SELECT a.appointmentID, p.full_name AS patient_name, d.full_name AS doctor_name, a.`date`, a.status
     FROM appointment a
     JOIN patients p ON p.patientID = a.patientID
     JOIN doctor d ON d.doctorID = a.doctorID
     ORDER BY a.`date`"
)->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Controls</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

    <!-- Form to add a user to the system -->
    <form method="POST" action="">
        <header><h1>Admin Controls</h1></header>
        <?php if ($success != "" && $formType == 'add_user'): ?><p class="message success" role="status"><?= e($success) ?></p><?php endif; ?>
        <?php if ($error != "" && $formType == 'add_user'): ?><p class="message error" role="alert"><?= e($error) ?></p><?php endif; ?>

        <table>
            <tr>
                <td><label for="roleSelect">Role select:</label></td>
                <td><select id="roleSelect" name="roleSelect" required>
                    <option value="">Select role</option>
                    <option value="patient">Patient</option>
                    <option value="doctor">Doctor</option>
                    <option value="admin">Admin</option>
                </select></td>
            </tr>
            <tr>
                <td><label for="name">Full Name:</label></td>
                <td><input type="text" name="name" id="name" required></td>
            </tr>
            <tr>
                <td><label for="email">Email:</label></td>
                <td><input type="email" id="email" name="email" required></td>
            </tr>
            <tr>
                <td><label for="password">Password:</label></td>
                <td><input type="password" id="password" name="password" required minlength="6">
                <small>Set a temporary password for the user</small></td>
            </tr>
            <tr>
                <td><label for="specialisation">Specialisation:</label></td>
                <td><input type="text" id="specialisation" name="specialisation"></td>
            </tr>
            <tr>
                <td><label for="dateOfBirth">Date of Birth:</label></td>
                <td><input type="date" id="dateOfBirth" name="dateOfBirth"></td>
            </tr>
            <tr>
                <td><label for="phoneNumber">Phone Number:</label></td>
                <td><input type="tel" id="phoneNumber" name="phoneNumber"></td>
            </tr>
            <tr>
                <td><button type="submit">Add User</button></td>
                <td><a href="dashboard.php" class="btn btn-secondary">Back to dashboard</a></td>
            </tr>
        </table>
        <input type="hidden" name="form_type" value="add_user">
        <?= csrf_field() ?>
    </form>

    <!-- Form to manage Appointment status -->
    <form method="POST" action="">
        <header><h2>Manage Appointments</h2></header>

        <?php if ($success != "" && $formType == 'manage_appointment'): ?><p class="message success" role="status"><?= e($success) ?></p><?php endif; ?>
        <?php if ($error != "" && $formType == 'manage_appointment'): ?><p class="message error" role="alert"><?= e($error) ?></p><?php endif; ?>

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
        <input type="hidden" name="form_type" value="manage_appointment">
        <?= csrf_field() ?>
    </form>

</body>
</html>
