<?php
require_once __DIR__ . '/../app/core/CitiServeData.php';

session_start();

$data = new CitiServeData();

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($email === '' || $password === '') {
        $error = 'Email and password are required.';
    } else {
        $user = $data->findUserByEmail($email);

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
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | CitiServe</title>
    <link rel="stylesheet" href="/CitiServe/public/assets/css/auth.css">
</head>
<body>

<main class="auth-page">
    <section class="auth-hero auth-hero-login">
        <div class="auth-hero-content">
            <p class="auth-kicker">CitiServe</p>
            <h1>Welcome back</h1>
            <p>Log in to manage your requests, complaints, and account updates from any device size.</p>
        </div>
    </section>

    <section class="auth-panel">
        <div class="auth-card">
            <h2>Log in</h2>
            <p class="auth-subtext">Use your registered email and password.</p>

            <?php if ($error): ?>
                <div class="auth-alert auth-alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" action="" class="auth-form" novalidate>
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="<?= htmlspecialchars($email) ?>" required autocomplete="email">

                <label for="password">Password</label>
                <div class="auth-password-wrap">
                    <input id="password" type="password" name="password" required autocomplete="current-password">
                    <button type="button" class="auth-toggle-password" data-target="password" aria-label="Toggle password visibility">Show</button>
                </div>

                <button type="submit" class="auth-button">Log in</button>
            </form>

            <p class="auth-links">
                No account yet? <a href="/CitiServe/public/register.php">Create one</a>
            </p>
            <p class="auth-links">
                <a href="/CitiServe/public/index.php">Back to home</a>
            </p>
        </div>
    </section>
</main>
<script src="/CitiServe/public/assets/js/auth.js" defer></script>
</body>
</html>
