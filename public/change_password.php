<?php
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/core/CitiServeData.php';

// Make sure the user is logged in
$authUser = require_login();

$data = new CitiServeData();
$user = $data->findUserById((int)$authUser['id']);

// If the user doesn't exist anymore, log them out
if (!$user) {
    session_start();
    session_unset();
    session_destroy();
    header('Location: /CitiServe/public/login.php');
    exit;
}

// Variables for errors and success message
$errors = [];
$success = '';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify the CSRF token
    csrf_verify_or_die();

    // Get the passwords from the form
    $currentPassword = isset($_POST['current_password']) ? $_POST['current_password'] : '';
    $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Make sure all fields are filled in
    if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
        $errors[] = 'All password fields are required.';
    }

    // Check if the current password is correct
    if (!password_verify($currentPassword, $user->password_hash)) {
        $errors[] = 'Current password is incorrect.';
    }

    // Check if the new password is long enough
    if (strlen($newPassword) < 8) {
        $errors[] = 'New password must be at least 8 characters.';
    }

    // Check if the new password and confirmation match
    if ($newPassword !== $confirmPassword) {
        $errors[] = 'New password and confirmation do not match.';
    }

    // Make sure the new password is different from the current one
    if ($currentPassword === $newPassword) {
        $errors[] = 'New password must be different from current password.';
    }

    // If there are no errors, update the password
    if (empty($errors)) {
        // Hash the new password
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);

        // Save the new password hash to the database
        $ok = $data->updateUserPasswordHash((int)$user->id, $newHash);

        if ($ok) {
            $success = 'Password changed successfully.';
        } else {
            $errors[] = 'Failed to change password.';
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Change Password</title>
</head>
<body>
    <h2>Change Password</h2>
    <p>
        <a href="/CitiServe/public/profile.php">Back to Profile</a> |
        <a href="/CitiServe/public/index.php">Home</a>
    </p>

    <?php if ($success): ?>
        <p style="color: green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div style="color: red;">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post">
        <?= csrf_field() ?>

        <div>
            <label>Current Password</label><br>
            <input type="password" name="current_password" required>
        </div>

        <div>
            <label>New Password</label><br>
            <input type="password" name="new_password" required>
        </div>

        <div>
            <label>Confirm New Password</label><br>
            <input type="password" name="confirm_password" required>
        </div>

        <br>
        <button type="submit">Change Password</button>
    </form>
</body>
</html>
