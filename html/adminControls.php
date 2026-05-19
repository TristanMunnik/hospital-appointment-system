<?php 
    require_once "../php/config.php";
    session_start();

    $success = "";
    $error = "";

    if(!isset($_SESSION['user_id'])){
        header('Location: login.php');
        exit();
    }
    if($_SESSION['role'] != 'admin'){
        header('Location: login.php');
        exit();
    }

    if(isset($_POST['form_type']) && $_POST['form_type'] == 'add_user') {
    try {
        $role = $_POST['roleSelect'];
        $name = $_POST['name'];
        $email = $_POST['email'];

        // password hashing
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $specialisation = $_POST['specialisation'];
        $dateOfBirth = $_POST['dateOfBirth'];
        $phoneNumber = $_POST['phoneNumber'];

        if($role == 'patient') {
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
        }
        $success = "User has been added successfully";

    } catch(PDOException $e) {
        $error = "Failed to add user. Email may already exist.";
    }

    } else if(isset($_POST['form_type']) && $_POST['form_type'] == 'manage_appointment') {
        try {
            $appointmentID = $_POST['appointmentID'];
            $status = $_POST['status'];

            $stmt = $pdo->prepare("UPDATE appointment SET status = ? WHERE appointmentID = ?");
            $stmt->execute([$status, $appointmentID]);
            $success = "Updated appointment successfully";

        } catch(PDOException $e) {
            $error = "Failed to update appointment.";
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
</head>
<body>

    <!-- Form to add a user to the system -->
    <form method="POST" action="">
        <header><h1>Admin</h1></header><hr>
        <?php if($success != "" && isset($_POST['form_type']) && $_POST['form_type'] == 'add_user') echo "<p style='color:green'>$success</p>"; ?>
        <?php if($error != "" && isset($_POST['form_type']) && $_POST['form_type'] == 'add_user') echo "<p style='color:red'>$error</p>"; ?>

        <table>
            <tr>
                <td><label for="roleSelect">Role select:</label></td>
                <td><select id="roleSelect" name="roleSelect" required>
                    <option value="">Select role</option>
                    <option value="patient" >Patient</option>
                    <option value="doctor">Doctor</option>
                    <option value="admin">Admin</option>
                </select></td>
            </tr>
            <tr>
                <td><label for="name" >Full Name:</label></td>
                <td><input type="text" name="name" id="name" required></td>
            </tr> 
            <tr>
                <td><label for="email">Email:</label></td>
                <td><input type="email" id="email" name="email" required></td>
            </tr>
            <tr>
                <td><label for="password">Password:</label></td>
                <td><input type="password" id="password" name="password" required>
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
                <td><button type="submit">submit</button></td>
                <td></td>
            </tr>
        </table>
        <input type="hidden" name="form_type" value="add_user">
    </form><hr>

    <!-- Form to manage Appointment status -->
    <form method="POST" action="">
        <header><h2>Manage Appointments</h2></header>

        <?php if($success != "" && isset($_POST['form_type']) && $_POST['form_type'] == 'manage_appointment') echo "<p style='color:green'>$success</p>"; ?>
        <?php if($error != "" && isset($_POST['form_type']) && $_POST['form_type'] == 'manage_appointment') echo "<p style='color:red'>$error</p>"; ?>
        <table>
            <tr>
                <td><label for="appointmentID">Select Appointment:</label></td>
                <td>
                    <select id="appointmentID" name="appointmentID" required>
                        <option value="">Select Appointment</option>
                        <option value="1">John Smith - Dr James Wilson - 2026-06-01</option>
                        <option value="2">Sarah Johnson - Dr Emily Brown - 2026-06-02</option>
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
    </form>
    
</body>
</html>