<?php
require_once __DIR__ . '/../src/bootstrap.php';

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    verify_csrf();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Each role has its own table; check them in turn.
    foreach (ROLES as $role => $info) {
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
    flash('error', 'Incorrect email or password');
    redirect('/login.php');
}

$pageTitle = 'Login';
$scripts = ['validation.js'];
require __DIR__ . '/../src/partials/head.php';
?>
    <div class="login-container">
        <form method="post" action="" onsubmit="return validateLogin()">
            <header>
                <h1>Hospital System</h1>
                <p class="subtitle">Sign in to your account</p>
            </header>

            <?php render_flash(); ?>

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
