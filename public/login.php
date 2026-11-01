<?php
require_once __DIR__ . '/../src/bootstrap.php';

$error = "";

// Each role lives in its own table; check them in turn.
$roleTables = [
    'patient' => ['table' => 'patients', 'idColumn' => 'patientID', 'home' => '/patient/dashboard.php'],
    'doctor'  => ['table' => 'doctor',   'idColumn' => 'doctorID',  'home' => '/doctor/dashboard.php'],
    'admin'   => ['table' => 'admin',    'idColumn' => 'adminID',   'home' => '/admin/dashboard.php'],
];

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    verify_csrf();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    foreach ($roleTables as $role => $info) {
        $stmt = $pdo->prepare("SELECT * FROM {$info['table']} WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // New session ID on login prevents session fixation attacks
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user[$info['idColumn']];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['role'] = $role;
            redirect($info['home']);
        }
    }

    // Same message for "no such user" and "wrong password" so attackers can't discover which emails exist
    $error = "Incorrect email or password";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="<?= BASE_URL ?>/assets/js/validation.js" defer></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <form method="post" action="" onsubmit="return validateLogin()">
            <header>
                <h1>Hospital System</h1>
                <p class="subtitle">Sign in to your account</p>
            </header>

            <?php if ($error != ""): ?>
                <p class="message error" role="alert"><?= e($error) ?></p>
            <?php endif; ?>

            <table>
                <tr>
                    <td><label for="email">Email:</label></td>
                    <td><input type="email" id="email" name="email" required></td>
                </tr>
                <tr>
                    <td><label for="password">Password:</label></td>
                    <td><input type="password" id="password" name="password" required></td>
                </tr>
                <tr>
                    <td><button type="submit" id="submitBtn">Submit</button></td>
                </tr>
            </table>
            <?= csrf_field() ?>
        </form>
    </div>
</body>
</html>
