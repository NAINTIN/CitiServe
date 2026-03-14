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

$fullName = $user->full_name;
$address = $user->address ?? '';
$contactNumber = $user->contact_number ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify_or_die();

    $fullName = trim($_POST['full_name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $contactNumber = trim($_POST['contact_number'] ?? '');

    if ($fullName === '') {
        $errors[] = 'Full name is required.';
    } elseif (mb_strlen($fullName) > 100) {
        $errors[] = 'Full name must be 100 characters or less.';
    }

    if ($address !== '' && mb_strlen($address) > 1000) {
        $errors[] = 'Address is too long.';
    }

    if ($contactNumber !== '' && mb_strlen($contactNumber) > 20) {
        $errors[] = 'Contact number must be 20 characters or less.';
    }

    if (empty($errors)) {
        $ok = $userRepo->updateProfile(
            (int)$user->id,
            $fullName,
            $address !== '' ? $address : null,
            $contactNumber !== '' ? $contactNumber : null
        );

        if ($ok) {
            $success = 'Profile updated successfully.';
            $user = $userRepo->findById((int)$authUser['id']);
        } else {
            $errors[] = 'Failed to update profile.';
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit Profile</title>
</head>
<body>
    <h2>Edit Profile</h2>
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
            <label>Full Name</label><br>
            <input type="text" name="full_name" maxlength="100" required
                   value="<?= htmlspecialchars($fullName) ?>">
        </div>

        <div>
            <label>Address</label><br>
            <textarea name="address" rows="4" cols="50"><?= htmlspecialchars($address) ?></textarea>
        </div>

        <div>
            <label>Contact Number</label><br>
            <input type="text" name="contact_number" maxlength="20"
                   value="<?= htmlspecialchars($contactNumber) ?>">
        </div>

        <br>
        <button type="submit">Save Changes</button>
    </form>
</body>
</html>