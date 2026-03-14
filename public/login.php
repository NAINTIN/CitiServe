<?php
require_once __DIR__ . '/../app/repositories/UserRepository.php';
session_start();

$repo = new UserRepository();
$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Email and password are required.';
    } else {
        $user = $repo->findByEmail($email);
        if ($user && password_verify($password, $user->password_hash)) {
            $_SESSION['user_id'] = $user->id;
            header('Location: /CitiServe/public/index.php');
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Login</title></head>
<body>
<h2>Login</h2>

<?php if ($error): ?>
    <div style="color:red"><?=htmlspecialchars($error)?></div>
<?php endif; ?>

<form method="post" action="">
    <div>
        <label>Email<br>
            <input type="email" name="email" value="<?=htmlspecialchars($email)?>">
        </label>
    </div>
    <div>
        <label>Password<br>
            <input type="password" name="password">
        </label>
    </div>
    <div>
        <button type="submit">Login</button>
    </div>
</form>

<p>No account? <a href="register.php">Register</a></p>
</body>
</html>