<?php
require_once __DIR__ . '/../src/bootstrap.php';

// Send logged-in users to their dashboard, everyone else to the login page
$homes = [
    'patient' => '/patient/dashboard.php',
    'doctor'  => '/doctor/dashboard.php',
    'admin'   => '/admin/dashboard.php',
];
redirect($homes[$_SESSION['role'] ?? ''] ?? '/login.php');
