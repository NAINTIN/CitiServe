<?php
require_once __DIR__ . '/../app/repositories/UserRepository.php';
session_start();

$repo = new UserRepository();
$errors = [];
$old = ['full_name' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    $old['full_name'] = $full_name;
    $old['email'] = $email;

    if ($full_name === '') $errors[] = 'Full name is required.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if ($password === '' || strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $password_confirm) $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        if ($repo->findByEmail($email)) {
            $errors[] = 'Email already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $userId = $repo->create([
                'full_name' => $full_name,
                'email' => $email,
                'password_hash' => $hash
            ]);

            // login the user
            $_SESSION['user_id'] = $userId;
            header('Location: /CitiServe/public/index.php');
            exit;
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Register</title></head>
<body>
<h2>Register</h2>

<?php if ($errors): ?>
    <div style="color:red">
        <ul>
            <?php foreach ($errors as $e): ?><li><?=htmlspecialchars($e)?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="">
    <div>
        <label>Full name<br>
            <input type="text" name="full_name" value="<?=htmlspecialchars($old['full_name'])?>">
        </label>
    </div>
    <div>
        <label>Email<br>
            <input type="email" name="email" value="<?=htmlspecialchars($old['email'])?>">
        </label>
    </div>
    <div>
        <label>Password<br>
            <input type="password" name="password">
        </label>
    </div>
    <div>
        <label>Confirm password<br>
            <input type="password" name="password_confirm">
        </label>
    </div>
    <div>
        <button type="submit">Register</button>
    </div>
</form>

<p>Already have an account? <a href="login.php">Login</a></p>
</body>
</html>