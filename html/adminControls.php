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

    </form><hr>

    <!-- Form to manage Appointment status -->
    <form method="POST" action="">
        <header><h2>Manage Appointments</h2></header>
        
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

    </form>
    
</body>
</html>