<?php 
    session_start();

    if(!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    if($_SESSION['role'] != 'admin'){
        header('Location: login.php');
        exit();
    }
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
        <p>Welcome, <?php echo $_SESSION['user_name']; ?></p>
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
    </main>

</body>
</html>