<?php
require_once __DIR__ . '/../../src/bootstrap.php';
require_role('doctor');

$success = "";
$error = "";

// Patients for the dropdown come from the database, not hardcoded options
$patients = $pdo->query("SELECT patientID, full_name FROM patients ORDER BY full_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf();

    try {
        $stmt = $pdo->prepare("INSERT INTO medical_record
        (patientID, doctorID, diagnosis, treatment, `date`)
        VALUES (?,?,?,?,?)");
        $stmt->execute([
            $_POST['patientID'] ?? '',
            $_SESSION['user_id'],
            trim($_POST['diagnosis'] ?? ''),
            trim($_POST['treatment'] ?? ''),
            $_POST['date'] ?? '',
        ]);
        $success = "Medical record added successfully";
    } catch (PDOException $e) {
        error_log('Medical record insert failed: ' . $e->getMessage());
        $error = "Failed to add medical record. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Records</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
    <form action="" method="post">
        <header><h1>Add Medical Record</h1></header>

        <?php if ($success != ""): ?><p class="message success" role="status"><?= e($success) ?></p><?php endif; ?>
        <?php if ($error != ""): ?><p class="message error" role="alert"><?= e($error) ?></p><?php endif; ?>

        <table>
            <tr>
                <td><label for="patient">Select Patient:</label></td>
                <td><select id="patient" name="patientID" required>
                    <option value="">Patient List</option>
                    <?php foreach ($patients as $patient): ?>
                        <option value="<?= e($patient['patientID']) ?>"><?= e($patient['full_name']) ?></option>
                    <?php endforeach; ?>
                </select></td>
            </tr>
            <tr>
                <td><label for="diagnosis">Diagnosis:</label></td>
                <td><textarea name="diagnosis" id="diagnosis" rows="3" required></textarea></td>
            </tr>
            <tr>
                <td><label for="treatment">Treatment:</label></td>
                <td><textarea name="treatment" id="treatment" rows="3" required></textarea></td>
            </tr>
            <tr>
                <td><label for="date">Date:</label></td>
                <td><input type="date" name="date" id="date" required></td>
            </tr>
            <tr>
                <td><button type="submit">Add Record</button></td>
                <td><a href="dashboard.php" class="btn btn-secondary">Back to dashboard</a></td>
            </tr>
        </table>
        <?= csrf_field() ?>
    </form>

</body>
</html>
