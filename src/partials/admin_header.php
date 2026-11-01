<?php
// Shared header and navigation for every admin page. Set $pageTitle before including.
$currentPage = basename($_SERVER['SCRIPT_NAME']);
$adminLinks = [
    'dashboard.php'    => 'Dashboard',
    'users.php'        => 'Manage Users',
    'appointments.php' => 'Manage Appointments',
];
?>
    <header class="site-header">
        <h1><?= e($pageTitle) ?></h1>
        <p>Welcome, <?= e($_SESSION['user_name']) ?></p>
        <a href="<?= BASE_URL ?>/logout.php">Logout</a>
    </header>
    <nav class="site-nav">
        <?php foreach ($adminLinks as $file => $label): ?>
            <a href="<?= BASE_URL ?>/admin/<?= $file ?>"<?= $file === $currentPage ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
        <?php endforeach; ?>
    </nav>
