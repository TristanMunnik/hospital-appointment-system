<?php 
    require_once "../php/config.php";
    session_start();

    $success = "";
    $error = "";

    if(!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    if($_SESSION['role'] != 'admin'){
        header('Location: login.php');
        exit();
    }

    if(isset($_GET['appointmentID'])) {

        try {
            $appointmentID = $_GET['appointmentID'];
            $stmt = $pdo->prepare("DELETE FROM appointment WHERE appointmentID = ?");
            $stmt->execute([$appointmentID]);
            $success = "Appointment deleted successfully";
        } catch(PDOException $e) {
            $error = "Failed to delete appointment.";
        }
    }

    $stmt = $pdo->prepare("SELECT * FROM appointment");
    $stmt->execute([]);
    $appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>

    <header>
        <h1>Admin Dashboard</h1>
        <!-- Display Admin name from session -->
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
        <a href="logout.php">Logout</a>
    </header>
    <hr>

    <nav>
        <a href="adminControls.php">Admin Controls</a>
    </nav>
    <hr>
    
    <main>
    <h2>Admin Panel</h2>
    <p>Welcome to the admin panel. Use the navigation above to manage the system.</p>

    <?php if($success != "") echo "<p style='color:green'>$success</p>"; ?>
    <?php if($error != "") echo "<p style='color:red'>$error</p>"; ?>

    <?php if(count($appointments) > 0): ?>
            <table border="1">
                <tr>
                    <th>Appointment ID</th>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php foreach($appointments as $appointment): ?>
                <tr>
                    <td><?php echo htmlspecialchars($appointment['appointmentID']) ?></td>
                    <td><?php echo htmlspecialchars($appointment['patientID']) ?></td>
                    <td><?php echo htmlspecialchars($appointment['doctorID']) ?></td>
                    <td><?php echo htmlspecialchars($appointment['date'] )?></td>
                    <td><?php echo htmlspecialchars($appointment['time']) ?></td>
                    <td><?php echo htmlspecialchars($appointment['status']) ?></td>
                    <td>
                        <a href="adminDashboard.php?appointmentID=<?php echo htmlspecialchars($appointment['appointmentID']); ?>" 
                        onclick="return confirm('Are you sure you want to delete this appointment?')">
                        Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>You Have no appointments yet</p>
        <?php endif; ?>
        
    </main>

</body>
</html>