<?php 

    require_once "../php/config.php";
    session_start();

    if(!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    if($_SESSION['role'] != 'patient'){
        header('Location: login.php');
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM appointment WHERE patientID = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $appointments = $stmt->fetchAll();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
</head>
<body>

    <header>
        <h1>Patient Dashboard</h1>
        <!-- Display patient name from session -->
        <p>Welcome, <?php echo $_SESSION['user_name']; ?></p>
        <a href="logout.php">Logout</a>
    </header>
    <hr>

    <nav>
        <a href="appointmentBooking.php">Book New Appointment</a> |
        <a href="#">My Appointments</a> |
        <a href="#">My Medical Records</a>
    </nav>
    <hr>

    <main>
        <h2>Your Upcoming Appointments:</h2>
        <?php if(count($appointments) > 0): ?>
            <table border="1">
                <tr>
                    <th>Appointment ID</th>
                    <th>Doctor</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
                <?php foreach($appointments as $appointment): ?>
                <tr>
                    <td><?php echo $appointment['appointmentID'] ?></td>
                    <td><?php echo $appointment['doctorID'] ?></td>
                    <td><?php echo $appointment['date'] ?></td>
                    <td><?php echo $appointment['time'] ?></td>
                    <td><?php echo $appointment['status'] ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>You Have no appointments yet</p>
        <?php endif; ?>
    </main>

</body>
</html>