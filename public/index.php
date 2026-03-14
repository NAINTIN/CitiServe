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
    <p>Welcome, <?=htmlspecialchars($user->full_name)?> (<?=htmlspecialchars($user->email)?>)</p>
    <p><a href="logout.php">Logout</a></p>
<?php else: ?>
    <p>You are not logged in. <a href="login.php">Login</a> or <a href="register.php">Register</a></p>
<?php endif; ?>

<h3>Quick links</h3>
<ul>
    <li><a href="register.php">Register</a></li>
    <li><a href="login.php">Login</a></li>
</ul>

</body>
</html>