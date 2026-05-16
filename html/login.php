<?php 
require_once "../php/config.php";

session_start();
$error = "";

    if ($_SERVER["REQUEST_METHOD"] == 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $stmt = $pdo->prepare("SELECT * FROM patients WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        $role = 'patient';
        $idColumn = 'patientID';

        // If not found in patients, check doctors
        if (!$user) {
            $stmt = $pdo->prepare("SELECT * FROM doctor WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            $role = 'doctor';
            $idColumn = 'doctorID';
        }

        // If not found in doctors, check admins
        if (!$user) {
            $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            $role = 'admin';
            $idColumn = 'adminID';

        } 
        
        if($user) {
            if(password_verify($password, $user['password'])) {
                $_SESSION['userID'] = $user[$idColumn];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['role'] = $role;

                if($role == 'patient') {
                    header('location: patientDashboard.php');
                } elseif ($role == 'doctor') {
                    header('Location: doctorDashboard.php');
                } else {
                    header('Location: adminDashboard.php');
                }
                exit();
            } else {
                $error = "Your password was incorrect";
            }
        } else {
            $error = "There is no user with this email";
        }
    }


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <form method="post" action="">
        <header><h1>Login</h1></header><hr>

        <!-- displaying error login message -->
        <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>

        <table>
            <tr>
                <td><label for="loginEmail">Email:</label></td>
                <td><input type="email" id="loginEmail" name="email" required></td>
            </tr>
            <tr>
                <td><label for="password">Password:</label></td>
                <td><input type="password" id="password" name="password" required></td>
            </tr>
            <tr>
                <td><button type="submit" id="submitBtn">Submit</button></td>
            </tr>
        </table>

    </form>
</body>
</html>