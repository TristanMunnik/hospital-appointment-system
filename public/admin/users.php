<?php
require_once __DIR__ . '/../../src/bootstrap.php';
require_role('admin');

// Each role lives in its own table. Only these whitelisted names ever reach a query.
$roleTables = [
    'patient' => ['table' => 'patients', 'idColumn' => 'patientID'],
    'doctor'  => ['table' => 'doctor',   'idColumn' => 'doctorID'],
    'admin'   => ['table' => 'admin',    'idColumn' => 'adminID'],
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action == 'add_user') {
        $role = $_POST['roleSelect'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $plainPassword = $_POST['password'] ?? '';

        // Logins look in every table by email, so an email must be unique across all three
        $stmt = $pdo->prepare(
            "SELECT 1 FROM patients WHERE email = ?
             UNION ALL SELECT 1 FROM doctor WHERE email = ?
             UNION ALL SELECT 1 FROM admin WHERE email = ?"
        );
        $stmt->execute([$email, $email, $email]);

        if (!isset($roleTables[$role])) {
            flash('error', 'Please choose a role.');
        } elseif ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Please enter a name and a valid email.');
        } elseif (strlen($plainPassword) < 6) {
            flash('error', 'Password must have at least 6 characters.');
        } elseif ($stmt->fetch()) {
            flash('error', 'That email is already in use.');
        } else {
            $password = password_hash($plainPassword, PASSWORD_BCRYPT);
            try {
                if ($role == 'patient') {
                    $stmt = $pdo->prepare("INSERT INTO patients
                    (full_name, email, `password`, phone, dateOfBirth)
                    VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $name, $email, $password,
                        trim($_POST['phoneNumber'] ?? '') ?: null,
                        ($_POST['dateOfBirth'] ?? '') ?: null,
                    ]);
                } elseif ($role == 'doctor') {
                    $stmt = $pdo->prepare("INSERT INTO doctor
                    (full_name, email, `password`, specialisation)
                    VALUES (?,?,?,?)");
                    $stmt->execute([$name, $email, $password, trim($_POST['specialisation'] ?? '') ?: null]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO admin
                    (full_name, email, `password`)
                    VALUES (?,?,?)");
                    $stmt->execute([$name, $email, $password]);
                }
                flash('success', ucfirst($role) . ' account for ' . $name . ' was added.');
            } catch (PDOException $e) {
                error_log('Add user failed: ' . $e->getMessage());
                flash('error', 'Failed to add user. Please try again.');
            }
        }

    } elseif ($action == 'remove_user') {
        $role = $_POST['role'] ?? '';
        $id = $_POST['id'] ?? '';

        if (!isset($roleTables[$role])) {
            flash('error', 'Unknown user type.');
        } elseif ($role == 'admin' && $id == $_SESSION['user_id']) {
            // Stops the system ending up with no way to log in as an admin
            flash('error', 'You cannot remove your own admin account.');
        } else {
            try {
                $info = $roleTables[$role];
                $stmt = $pdo->prepare("DELETE FROM {$info['table']} WHERE {$info['idColumn']} = ?");
                $stmt->execute([$id]);
                flash(
                    $stmt->rowCount() > 0 ? 'success' : 'error',
                    $stmt->rowCount() > 0 ? ucfirst($role) . ' removed.' : 'That user no longer exists.'
                );
            } catch (PDOException $e) {
                error_log('Remove user failed: ' . $e->getMessage());
                flash('error', 'Failed to remove user. Please try again.');
            }
        }
    }

    // Post/Redirect/Get: a page refresh won't resubmit the form
    redirect('/admin/users.php');
}

$flash = pull_flash();

$users = $pdo->query(
    "SELECT 'patient' AS role, patientID AS id, full_name, email FROM patients
     UNION ALL SELECT 'doctor', doctorID, full_name, email FROM doctor
     UNION ALL SELECT 'admin', adminID, full_name, email FROM admin
     ORDER BY FIELD(role, 'admin', 'doctor', 'patient'), full_name"
)->fetchAll();

$pageTitle = 'Manage Users';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <script src="<?= BASE_URL ?>/assets/js/admin-users.js" defer></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

    <?php require __DIR__ . '/../../src/partials/admin_header.php'; ?>

    <?php if ($flash): ?>
        <p class="message <?= e($flash['type']) ?>" role="<?= $flash['type'] == 'error' ? 'alert' : 'status' ?>"><?= e($flash['message']) ?></p>
    <?php endif; ?>

    <main>
        <h2>All Users (<?= count($users) ?>)</h2>
        <div class="table-wrap">
            <table>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($users as $user): ?>
                <?php $isYou = $user['role'] == 'admin' && $user['id'] == $_SESSION['user_id']; ?>
                <tr>
                    <td><?= e($user['full_name']) ?><?= $isYou ? ' (you)' : '' ?></td>
                    <td><?= e($user['email']) ?></td>
                    <td><span class="badge badge-<?= e($user['role']) ?>"><?= e($user['role']) ?></span></td>
                    <td>
                        <?php if ($isYou): ?>
                            <span class="muted">Signed in</span>
                        <?php else: ?>
                            <form method="post" action="" onsubmit="return confirm('Remove this user? This also deletes their appointments and medical records.')">
                                <input type="hidden" name="action" value="remove_user">
                                <input type="hidden" name="role" value="<?= e($user['role']) ?>">
                                <input type="hidden" name="id" value="<?= e($user['id']) ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-danger">Remove</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </main>

    <form method="post" action="" class="stacked-form">
        <header><h2>Add a User</h2></header>

        <table>
            <tr>
                <td><label for="roleSelect">Role:</label></td>
                <td><select id="roleSelect" name="roleSelect" required>
                    <option value="">Select role</option>
                    <option value="patient">Patient</option>
                    <option value="doctor">Doctor</option>
                    <option value="admin">Admin</option>
                </select></td>
            </tr>
            <tr>
                <td><label for="name">Full Name:</label></td>
                <td><input type="text" name="name" id="name" required></td>
            </tr>
            <tr>
                <td><label for="email">Email:</label></td>
                <td><input type="email" id="email" name="email" required></td>
            </tr>
            <tr>
                <td><label for="password">Password:</label></td>
                <td><input type="password" id="password" name="password" required minlength="6">
                <small>Set a temporary password for the user (min. 6 characters)</small></td>
            </tr>
            <tr data-roles="doctor">
                <td><label for="specialisation">Specialisation:</label></td>
                <td><input type="text" id="specialisation" name="specialisation"></td>
            </tr>
            <tr data-roles="patient">
                <td><label for="dateOfBirth">Date of Birth:</label></td>
                <td><input type="date" id="dateOfBirth" name="dateOfBirth"></td>
            </tr>
            <tr data-roles="patient">
                <td><label for="phoneNumber">Phone Number:</label></td>
                <td><input type="tel" id="phoneNumber" name="phoneNumber"></td>
            </tr>
            <tr>
                <td><button type="submit">Add User</button></td>
            </tr>
        </table>
        <input type="hidden" name="action" value="add_user">
        <?= csrf_field() ?>
    </form>

</body>
</html>
