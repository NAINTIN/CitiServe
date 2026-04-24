<?php
require_once __DIR__ . '/../app/helpers/auth.php';
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
            <th>Verification Status</th>
            <td>
                <?php if ((int)$user->is_verified === 1): ?>
                    <span style="color:green; font-weight:700;">Fully Verified</span>
                <?php else: ?>
                    <span style="color:#d97706; font-weight:700;">Not Verified</span><br>
                    <small>Your account is pending verification or your previous ID was rejected. Please re-upload your ID for review.</small><br>
                    <a href="/CitiServe/public/profile_edit.php#proof-of-id">Re-upload Proof of ID</a>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>Proof of ID</th>
            <td>
                <?php if (!empty($user->proof_of_id)): ?>
                    <a href="/CitiServe/public/<?= htmlspecialchars((string)$user->proof_of_id) ?>" target="_blank">View Uploaded ID</a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
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
