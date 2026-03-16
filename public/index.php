<?php
// Include the repositories we need
require_once __DIR__ . '/../app/repositories/UserRepository.php';
require_once __DIR__ . '/../app/repositories/NotificationRepository.php';

// Start the session so we can check if the user is logged in
session_start();

// Create a UserRepository to look up user info
$repo = new UserRepository();
$user = null;
$unreadCount = 0;

// Check if the user is logged in (user_id stored in session)
if (!empty($_SESSION['user_id'])) {
    // Try to find the user in the database
    $user = $repo->findById((int)$_SESSION['user_id']);

    // If the user exists, count their unread notifications
    if ($user) {
        try {
            $notifRepo = new NotificationRepository();
            $unreadCount = $notifRepo->unreadCount((int)$user->id);
        } catch (Throwable $e) {
            // If something goes wrong, just show 0 notifications
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

    <?php
    // Show different links depending on the user's role
    if ($user && ($user->role === 'admin' || $user->role === 'staff')):
    ?>
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