<?php
// Include the UserRepository so we can look up users from the database
require_once __DIR__ . '/../repositories/UserRepository.php';

// This function checks if the user is logged in.
// If not logged in, it redirects them to the login page.
// If logged in, it returns the user's info as an array.
function require_login()
{
    // Start the session if it hasn't been started yet
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if the user_id is stored in the session
    if (empty($_SESSION['user_id'])) {
        // No user_id means they're not logged in, so send them to login page
        header('Location: /CitiServe/public/login.php');
        exit;
    }

    // Try to find the user in the database using their session user_id
    $repo = new UserRepository();
    $userId = (int)$_SESSION['user_id'];
    $user = $repo->findById($userId);

    // If the user doesn't exist in the database anymore, log them out
    if (!$user) {
        session_unset();
        session_destroy();
        header('Location: /CitiServe/public/login.php');
        exit;
    }

    // Return the user's basic info as a simple array
    $userInfo = [
        'id' => $user->id,
        'full_name' => $user->full_name,
        'email' => $user->email,
        'role' => $user->role
    ];

    return $userInfo;
}

// This function checks if the user is an admin or staff member.
// If they're not, it shows a "403 Forbidden" error.
function require_admin()
{
    // First, make sure they're logged in
    $user = require_login();

    // Check if their role is 'admin' or 'staff'
    $role = $user['role'];
    if ($role !== 'admin' && $role !== 'staff') {
        http_response_code(403);
        echo "<h2>403 Forbidden</h2><p>Admin/Staff access required.</p>";
        exit;
    }

    return $user;
}

// This function checks if the user is a resident.
// If they're not, it shows a "403 Forbidden" error.
function require_resident()
{
    // First, make sure they're logged in
    $user = require_login();

    // Check if their role is 'resident'
    if ($user['role'] !== 'resident') {
        http_response_code(403);
        echo "<h2>403 Forbidden</h2><p>This page is for residents only.</p>";
        exit;
    }

    return $user;
}