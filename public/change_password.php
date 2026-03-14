<?php
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/repositories/UserRepository.php';

$authUser = require_login();
$userRepo = new UserRepository();
$user = $userRepo->findById((int)$authUser['id']);

if (!$user) {
    session_start();
    session_unset();
    session_destroy();
    header('Location: /CitiServe/public/login.php');
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify_or_die();

    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
        $errors[] = 'All password fields are required.';
    }

    if (!password_verify($currentPassword, $user->password_hash)) {
        $errors[] = 'Current password is incorrect.';
    }

    if (strlen($newPassword) < 8) {
        $errors[] = 'New password must be at least 8 characters.';
    }

    if ($newPassword !== $confirmPassword) {
        $errors[] = 'New password and confirmation do not match.';
    }

    if ($currentPassword === $newPassword) {
        $errors[] = 'New password must be different from current password.';
    }

    if (empty($errors)) {
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $ok = $userRepo->updatePasswordHash((int)$user->id, $newHash);

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