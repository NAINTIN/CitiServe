<?php
require_once __DIR__ . '/../app/repositories/UserRepository.php';
session_start();

$repo = new UserRepository();
$user = null;

if (!empty($_SESSION['user_id'])) {
    $user = $repo->findById((int)$_SESSION['user_id']);
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>CitiServe Home</title></head>
<body>
<?php if ($user): ?>
    <p>
        Welcome, <?= htmlspecialchars($user->full_name) ?>
        (<?= htmlspecialchars($user->email) ?>) - Role: <?= htmlspecialchars($user->role) ?>
    </p>
    <p><a href="/CitiServe/public/logout.php">Logout</a></p>
<?php else: ?>
    <p>You are not logged in.
        <a href="/CitiServe/public/login.php">Login</a> or
        <a href="/CitiServe/public/register.php">Register</a>
    </p>
<?php endif; ?>

<h3>Quick links</h3>
<ul>
    <li><a href="/CitiServe/public/register.php">Register</a></li>
    <li><a href="/CitiServe/public/login.php">Login</a></li>

    <li><a href="/CitiServe/public/profile.php">My Profile</a></li>
    <li><a href="/CitiServe/public/profile_edit.php">Edit Profile</a></li>
    <li><a href="/CitiServe/public/change_password.php">Change Password</a></li>

    <li><a href="/CitiServe/public/services.php">Document Services</a></li>
    <li><a href="/CitiServe/public/my_requests.php">My Requests</a></li>
    <li><a href="/CitiServe/public/admin/requests.php">Admin Requests</a> (admin/staff only)</li>

    <li><a href="/CitiServe/public/complaint_create.php">Submit Complaint</a></li>
    <li><a href="/CitiServe/public/my_complaints.php">My Complaints</a></li>
    <li><a href="/CitiServe/public/admin/complaints.php">Admin Complaints</a> (admin/staff only)</li>

    <li><a href="/CitiServe/public/admin/users.php">Manage User Roles</a> (admin/staff only)</li>
</ul>
</body>
</html>