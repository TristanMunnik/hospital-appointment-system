<?php 
    require_once "../php/config.php";
    session_start();

    if(!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    if($_SESSION['role'] != 'doctor'){
        header('Location: login.php');
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM appointment WHERE doctorID = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $appointments = $stmt->fetchAll();
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <header>
        <h1>Doctor Dashboard</h1>
        <!-- Display doctor name from session -->
        <p>Welcome, <?php echo $_SESSION['user_name']; ?></p>
        <a href="logout.php">Logout</a>
    </header>
    <hr>

    <nav>
        <a href="scheduleManagement.php">Manage Schedules</a>
    </nav>
    <hr>

    <main>
    <h2>Doctor Panel</h2>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
    <?php if(count($appointments) > 0): ?>
            <table border="1">
                <tr>
                    <th>Appointment ID</th>
                    <th>Patient</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
                <?php foreach($appointments as $appointment): ?>
                <tr>
                    <td><?php echo htmlspecialchars($appointment['appointmentID']) ?></td>
                    <td><?php echo htmlspecialchars($appointment['patientID']) ?></td>
                    <td><?php echo htmlspecialchars($appointment['date']) ?></td>
                    <td><?php echo htmlspecialchars($appointment['time']) ?></td>
                    <td><?php echo htmlspecialchars($appointment['status']) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>You Have no appointments yet</p>
        <?php endif; ?>
    </main>

</body>
</html>