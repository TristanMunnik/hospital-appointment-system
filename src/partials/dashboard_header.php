<?php
// Shared header and navigation for the dashboard-style pages of every role.
// Set $pageTitle before including. The nav links depend on who is logged in.
$navByRole = [
    'patient' => [
        '/patient/book-appointment.php' => 'Book New Appointment',
    ],
    'doctor' => [
        '/doctor/medical-records.php' => 'Add Medical Record',
    ],
    'admin' => [
        '/admin/dashboard.php'    => 'Dashboard',
        '/admin/users.php'        => 'Manage Users',
        '/admin/appointments.php' => 'Manage Appointments',
    ],
];
?>
    <header class="site-header">
        <h1><?= e($pageTitle) ?></h1>
        <p>Welcome, <?= e($_SESSION['user_name']) ?></p>
        <a href="<?= BASE_URL ?>/logout.php">Logout</a>
    </header>
    <nav class="site-nav">
        <?php foreach ($navByRole[$_SESSION['role']] as $path => $label): ?>
            <a href="<?= BASE_URL . $path ?>"<?= BASE_URL . $path === $_SERVER['SCRIPT_NAME'] ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
        <?php endforeach; ?>
    </nav>
