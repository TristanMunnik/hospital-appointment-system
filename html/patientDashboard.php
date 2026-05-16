<?php 
    session_start();

    if(!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    if($_SESSION['role'] != 'patient'){
        header('Location: login.php');
        exit();
    }
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
        <h2>Your Upcoming Appointments</h2>
        <p>Appointment list will appear here...</p>
    </main>

</body>
</html>