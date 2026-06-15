<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function app_base(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }

    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $scriptDir = rtrim($scriptDir, '/');
    $parts = $scriptDir === '' ? [] : explode('/', trim($scriptDir, '/'));
    $section = end($parts);

    if (in_array($section, ['admin', 'auth', 'login', 'tuition', 'tutor', 'tutors', 'student', 'includes'], true)) {
        array_pop($parts);
    }

    $base = $parts ? '/' . implode('/', $parts) : '';
    return $base;
}

function url(string $path): string
{
    return app_base() . '/' . ltrim($path, '/');
}

function redirect_to(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_login(?string $role = null): void
{
    $user = current_user();
    if (!$user) {
        redirect_to('/login');
    }

    if ($role !== null && $user['role'] !== $role) {
        redirect_to('/index.php');
    }
}

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

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        die('Invalid CSRF token.');
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function flash_messages(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function render_flash_messages(): void
{
    foreach (flash_messages() as $message) {
        $type = in_array($message['type'], ['success', 'danger', 'warning', 'info'], true) ? $message['type'] : 'info';
        echo '<div class="alert alert-' . e($type) . ' alert-dismissible fade show" role="alert">';
        echo e($message['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
}

function redirect_by_role(string $role): void
{
    $targets = [
        'admin' => '/admin/dashboard.php',
        'tutor' => '/tutor/dashboard.php',
        'student' => '/student/dashboard.php',
    ];

    redirect_to($targets[$role] ?? '/login');
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function role_home(): string
{
    $user = current_user();
    return $user ? url('/' . $user['role'] . '/dashboard.php') : url('/index.php');
}
