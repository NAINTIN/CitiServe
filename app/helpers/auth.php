<?php
require_once __DIR__ . '/../core/CitiServeData.php';

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

    $data = new CitiServeData();
    $userId = (int)$_SESSION['user_id'];
    $user = $data->findUserById($userId);

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

function require_verified_resident($featureName = 'this feature')
{
    $user = require_resident();
    $data = new CitiServeData();
    $dbUser = $data->findUserById((int)$user['id']);

    if (!$dbUser || (int)$dbUser->is_verified !== 1) {
        http_response_code(403);
        $safeFeature = htmlspecialchars((string)$featureName, ENT_QUOTES, 'UTF-8');
        echo '<!doctype html><html><head><meta charset="utf-8"><title>Verification Required</title><style>body{font-family:Epilogue,Arial,sans-serif;background:#fff7f8;color:#2b2b2b;margin:0;padding:24px}.box{max-width:720px;margin:40px auto;background:#fff;border:1px solid #ffd0dc;border-radius:14px;padding:20px}.title{color:#f03871;font-size:24px;font-weight:700;margin:0 0 12px}.msg{font-size:15px;line-height:1.6;margin:0 0 18px}.btn{display:inline-block;padding:10px 16px;border-radius:10px;background:#f03871;color:#fff;text-decoration:none;font-weight:600}</style></head><body><div class="box"><h2 class="title">Account Verification Required</h2><p class="msg">You cannot access ' . $safeFeature . ' yet because your account is not verified. Please wait for admin approval or re-upload your ID in your profile if your previous submission was rejected.</p><a class="btn" href="/CitiServe/public/profile_edit.php#proof-of-id">Go to Profile Verification</a></div></body></html>';
        exit;
    }

    return $user;
}
