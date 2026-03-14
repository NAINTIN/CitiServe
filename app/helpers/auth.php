<?php
require_once __DIR__ . '/../repositories/UserRepository.php';

function require_login(): array
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['user_id'])) {
        header('Location: /CitiServe/public/login.php');
        exit;
    }

    $repo = new UserRepository();
    $user = $repo->findById((int)$_SESSION['user_id']);

    if (!$user) {
        session_unset();
        session_destroy();
        header('Location: /CitiServe/public/login.php');
        exit;
    }

    return [
        'id' => $user->id,
        'full_name' => $user->full_name,
        'email' => $user->email,
        'role' => $user->role
    ];
}

function require_admin(): array
{
    $user = require_login();

    if (!in_array($user['role'], ['admin', 'staff'], true)) {
        http_response_code(403);
        echo "<h2>403 Forbidden</h2><p>Admin/Staff access required.</p>";
        exit;
    }

    return $user;
}

function require_resident(): array
{
    $user = require_login();

    if ($user['role'] !== 'resident') {
        http_response_code(403);
        echo "<h2>403 Forbidden</h2><p>This page is for residents only.</p>";
        exit;
    }

    return $user;
}