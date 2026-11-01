<?php
require_once __DIR__ . '/../src/bootstrap.php';

// Send logged-in users to their dashboard, everyone else to the login page
redirect(ROLES[$_SESSION['role'] ?? '']['home'] ?? '/login.php');
