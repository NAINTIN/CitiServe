<?php
require_once __DIR__ . '/../app/repositories/UserRepository.php';
require_once __DIR__ . '/../app/repositories/NotificationRepository.php';
session_start();

$repo = new UserRepository();
$user = null;
$unreadCount = 0;

if (!empty($_SESSION['user_id'])) {
    $user = $repo->findById((int)$_SESSION['user_id']);

    
    if ($user) {
        try {
            $notifRepo = new NotificationRepository();
            $unreadCount = $notifRepo->unreadCount((int)$user->id);
        } catch (Throwable $e) {
            $unreadCount = 0;
        }
    }
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
    <p>
        <a href="/CitiServe/public/notifications.php">Notifications (<?= (int)$unreadCount ?>)</a> |
        <a href="/CitiServe/public/logout.php">Logout</a>
    </p>
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
    <li><a href="/CitiServe/public/notifications.php">My Notifications (<?= (int)$unreadCount ?>)</a></li>
    <li><a href="/CitiServe/public/profile.php">My Profile</a></li>
    <li><a href="/CitiServe/public/profile_edit.php">Edit Profile</a></li>
    <li><a href="/CitiServe/public/change_password.php">Change Password</a></li>

    <?php if ($user && in_array($user->role, ['admin', 'staff'], true)): ?>
        <li><a href="/CitiServe/public/admin/requests.php">Customer Requests</a></li>
        <li><a href="/CitiServe/public/admin/complaints.php">Customer Complaints</a></li>
        <li><a href="/CitiServe/public/admin/users.php">Manage User Roles</a></li>
    <?php else: ?>
        <li><a href="/CitiServe/public/services.php">Document Services</a></li>
        <li><a href="/CitiServe/public/request_create.php">Submit Document Request</a></li>
        <li><a href="/CitiServe/public/my_requests.php">My Requests</a></li>
        <li><a href="/CitiServe/public/complaint_create.php">Submit Complaint</a></li>
        <li><a href="/CitiServe/public/my_complaints.php">My Complaints</a></li>
    <?php endif; ?>
</ul>
</body>
</html>