<?php 
    require_once "../php/config.php";

    session_start();

    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    if($_SESSION['role'] != 'doctor'){
        header('Location: login.php');
        exit();
    }

    $success = "";
    $error = "";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $patientID = $_POST['patientID'];
        $doctorID = $_SESSION['user_id'];
        $diagnosis = $_POST['diagnosis'];
        $treatment = $_POST['treatment'];
        $date = $_POST['date'];

        $stmt = $pdo->prepare("INSERT INTO medical_record 
        (patientID, doctorID, diagnosis, treatment, `date`) 
        VALUES (?,?,?,?,?)");
        $stmt->execute([$patientID, $doctorID, $diagnosis, $treatment, $date]);
        $success = "Medical record updated successfully";

    } catch(PDOException $e) {
        $error = "Failed to update medical record. Please try again.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Management</title>
</head>
<body>
    <form action="" method="post">
        <header><h1>Schedule Management</h1></header><hr>
        <table>
            <tr>
                <td><label for="patient">Select Patient:</label></td>
                <td><select id="patient" name="patientID" required>
                    <option value="">Patient List</option>
                    <option value="1">John Smith</option>
                    <option value="2">Sarah Johnson</option>
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
                <td><button type="submit">Update Record</button></td>
            </tr>
        </table>
    </form>
    
</body>
</html>