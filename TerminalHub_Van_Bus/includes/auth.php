<?php
require_once __DIR__ . '/../config.php';

function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function require_login(): array {
    $user = current_user();
    if (!$user) redirect('login.php');
    return $user;
}

function require_role(string ...$roles): array {
    $user = require_login();
    if (!in_array($user['role'], $roles, true)) {
        http_response_code(403);
        exit('403 Forbidden: your account does not have access to this page.');
    }
    return $user;
}

function dashboard_for_role(string $role): string {
    return match ($role) {
        'admin' => 'admin/index.php',
        'staff' => 'staff/index.php',
        default => 'passenger/index.php',
    };
}
