<?php
// Shared setup included at the top of every page: database, session, and helpers.
require_once __DIR__ . '/config.php';

session_start();

// URL path to the public/ folder, so links work whether the site is served
// from the domain root (php -S) or a sub-folder (Laragon/XAMPP).
$publicDir = str_replace('\\', '/', (string) realpath(__DIR__ . '/../public'));
$docRoot   = str_replace('\\', '/', (string) realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
define('BASE_URL', rtrim(str_replace($docRoot, '', $publicDir), '/'));

/** Escape a value for safe output in HTML. */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Redirect to a path inside public/ and stop the script. */
function redirect(string $path): never
{
    header('Location: ' . BASE_URL . $path);
    exit();
}

/** Only let logged-in users with the given role through; everyone else goes to login. */
function require_role(string $role): void
{
    if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== $role) {
        redirect('/login.php');
    }
}

/** Per-session CSRF token, embedded in every POST form. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/** Stop the request if the submitted CSRF token is missing or wrong. */
function verify_csrf(): void
{
    $sent = $_POST['csrf_token'] ?? '';
    if (!hash_equals(csrf_token(), $sent)) {
        http_response_code(403);
        exit('Invalid or missing security token. Go back, refresh the page and try again.');
    }
}

/** Store a one-time message to show after a redirect (Post/Redirect/Get). */
function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/** Read and clear the stored message, if any. */
function pull_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}
