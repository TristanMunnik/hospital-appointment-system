<?php 
    require_once "../php/config.php";
    session_start();


    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    if ($_SESSION['role'] != 'patient') {
        header('Location: login.php');
        exit();
    }

    $success = "";
    $error = "";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $patientID = $_SESSION['user_id'];
        $doctorID = $_POST['doctorID'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $status = $_POST['status'];

        $stmt = $pdo->prepare("INSERT INTO appointment 
        (patientID, doctorID, `date`, time, status) 
        VALUES (?,?,?,?,?)");
        $stmt->execute([$patientID, $doctorID, $date, $time, $status]);
        $success = "Appointment booked successfully";

    } catch(PDOException $e) {
        $error = "Failed to book appointment. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments Booking</title>
    <script src="../js/validation.js"></script>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <form method="post" action="" onsubmit="return validateAppointment()">
        <header><h1>Book an appointment</h1></header><hr>

        <?php if ($success != "") echo "<p style='color:green'>$success</p>"; ?>
        <?php if ($error != "") echo "<p style='color:red'>$error</p>"; ?>

        <table>
            <tr>
                <td> <label for="name">Full Name:</label></td>
                <td><input type="text" id="name" name="name" required></td>
            </tr>
            <tr>
                <td><label for="email">Email:</label></td>
                <td><input type="email" name="email" id="email" required></td>
            </tr>
            <tr>
                <td><label for="doctorID">Doctor:</label></td>
                <td><select id="doctorID" name="doctorID" required>
                    <option value="">Select a Doctor</option>
                    <option value="1">Dr James Wilson - Cardiologist</option>
                    <option value="2">Dr Emily Brown - General Practitioner</option>
                </select></td>
            </tr>
            <tr>
                <td><label for="date">Date:</label></td>
                <td><input type="date" id="date" name="date" required></td>
            </tr>
            <tr>
                <td><label for="time">Time:</label></td>
                <td><input type="time" id="time" name="time" required></td>
            </tr>
            <tr>
                <td><label for="phoneNumber">Phone number:</label></td>
                <td><input type="tel" name="phoneNumber" id="phoneNumber" required></td>
            </tr>
            <tr>
                <td><button type="submit" id="submitBtn">Book</button></td>
            </tr>
        </table>

        <div id="appointmentListDisplay"></div>
        <input type="hidden" name="status" value="requested">
    </form>
</body>
</html>
