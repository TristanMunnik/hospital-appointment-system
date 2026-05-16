<?php 
    session_start();

    if(!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    if($_SESSION['role'] != 'doctor'){
        header('Location: login.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
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
    <p>Welcome to the doctor panel. Use the navigation above to manage the system.</p>
    </main>

</body>
</html>