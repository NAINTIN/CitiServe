<?php
// Include the auth helper and UserRepository
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/repositories/UserRepository.php';

// Make sure the user is logged in
$authUser = require_login();

// Get the full user info from the database
$userRepo = new UserRepository();
$user = $userRepo->findById((int)$authUser['id']);

// If the user doesn't exist anymore, log them out
if (!$user) {
    session_start();
    session_unset();
    session_destroy();
    header('Location: /CitiServe/public/login.php');
    exit;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Profile</title>
</head>
<body>
    <h2>My Profile</h2>
    <p>
        <a href="/CitiServe/public/index.php">Home</a> |
        <a href="/CitiServe/public/profile_edit.php">Edit Profile</a> |
        <a href="/CitiServe/public/change_password.php">Change Password</a> |
        <a href="/CitiServe/public/logout.php">Logout</a>
    </p>

    <table border="1" cellpadding="6" cellspacing="0">
        <tr>
            <th>User ID</th>
            <td><?= (int)$user->id ?></td>
        </tr>
        <tr>
            <th>Full Name</th>
            <td><?= htmlspecialchars($user->full_name) ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?= htmlspecialchars($user->email) ?></td>
        </tr>
        <tr>
            <th>Role</th>
            <td><?= htmlspecialchars($user->role) ?></td>
        </tr>
        <tr>
            <th>Address</th>
            <td><?php
                // Show the address, or a dash if it's empty
                if ($user->address !== null) {
                    echo htmlspecialchars($user->address);
                } else {
                    echo '-';
                }
            ?></td>
        </tr>
        <tr>
            <th>Contact Number</th>
            <td><?php
                if ($user->contact_number !== null) {
                    echo htmlspecialchars($user->contact_number);
                } else {
                    echo '-';
                }
            ?></td>
        </tr>
        <tr>
            <th>Created At</th>
            <td><?php
                if ($user->created_at !== null) {
                    echo htmlspecialchars($user->created_at);
                } else {
                    echo '-';
                }
            ?></td>
        </tr>
        <tr>
            <th>Updated At</th>
            <td><?php
                if ($user->updated_at !== null) {
                    echo htmlspecialchars($user->updated_at);
                } else {
                    echo '-';
                }
            ?></td>
        </tr>
    </table>
</body>
</html>