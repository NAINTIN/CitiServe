<?php
require_once __DIR__ . '/../app/core/CitiServeData.php';

session_start();

$errors = [];
$old = ['full_name' => '', 'email' => '', 'address' => '', 'contact_number' => '', 'accept_terms' => '0'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim(isset($_POST['full_name']) ? $_POST['full_name'] : '');
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $address = trim(isset($_POST['address']) ? $_POST['address'] : '');
    $contact_number = trim(isset($_POST['contact_number']) ? $_POST['contact_number'] : '');
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
    $accept_terms = isset($_POST['accept_terms']) ? '1' : '0';

    $old['full_name'] = $full_name;
    $old['email'] = $email;
    $old['address'] = $address;
    $old['contact_number'] = $contact_number;
    $old['accept_terms'] = $accept_terms;

    if ($full_name === '') {
        $errors[] = 'Full name is required.';
    }
    if ($address === '') {
        $errors[] = 'Address is required.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }
    if ($password === '' || strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if ($password !== $password_confirm) {
        $errors[] = 'Passwords do not match.';
    }
    if ($accept_terms !== '1') {
        $errors[] = 'You must agree to the Terms and Privacy Policy.';
    }
    if ($contact_number !== '') {
        $hasValidCharacters = preg_match('/^\+?[0-9\s\-()]+$/', $contact_number) === 1;
        $digitsOnly = preg_replace('/\D/', '', $contact_number);
        $hasValidDigitLength = strlen($digitsOnly) >= 7 && strlen($digitsOnly) <= 15;
        if (!$hasValidCharacters || !$hasValidDigitLength) {
            $errors[] = 'Contact number format is invalid.';
        }
    }

    if (empty($errors)) {
        $data = new CitiServeData();
        $existingUser = $data->findUserByEmail($email);

        if ($existingUser) {
            $errors[] = 'Email already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $userId = $data->createUser([
                'full_name' => $full_name,
                'email' => $email,
                'password_hash' => $hash,
                'address' => $address,
                'contact_number' => $contact_number ?: null,
            ]);

            $_SESSION['user_id'] = $userId;
            header('Location: /CitiServe/public/index.php');
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | CitiServe</title>
    <link rel="stylesheet" href="assets/css/auth.css">
</head>
<body>
<main class="auth-page">
    <section class="auth-hero auth-hero-register">
        <div class="auth-hero-content">
            <p class="auth-kicker">CitiServe</p>
            <h1>Create your account</h1>
            <p>Register to submit requests and complaints with a responsive interface that adapts to screen size and zoom.</p>
        </div>
    </section>

    <section class="auth-panel">
        <div class="auth-card">
            <h2>Register</h2>
            <p class="auth-subtext">Fill in your details to create a resident account.</p>

            <?php if ($errors): ?>
                <div class="auth-alert auth-alert-error">
                    <ul>
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="" class="auth-form" novalidate>
                <label for="full_name">Full name</label>
                <input id="full_name" type="text" name="full_name" value="<?= htmlspecialchars($old['full_name']) ?>" required autocomplete="name">

                <label for="address">Address</label>
                <input id="address" type="text" name="address" value="<?= htmlspecialchars($old['address']) ?>" required autocomplete="street-address">

                <div class="auth-grid-2">
                    <div>
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="<?= htmlspecialchars($old['email']) ?>" required autocomplete="email">
                    </div>
                    <div>
                        <label for="contact_number">Contact number (optional)</label>
                        <input id="contact_number" type="text" name="contact_number" value="<?= htmlspecialchars($old['contact_number']) ?>" autocomplete="tel">
                    </div>
                </div>

                <div class="auth-grid-2">
                    <div>
                        <label for="password">Password</label>
                        <div class="auth-password-wrap">
                            <input id="password" type="password" name="password" required minlength="6" autocomplete="new-password">
                            <button type="button" class="auth-toggle-password" data-target="password" aria-label="Toggle password visibility">Show</button>
                        </div>
                    </div>
                    <div>
                        <label for="password_confirm">Confirm password</label>
                        <div class="auth-password-wrap">
                            <input id="password_confirm" type="password" name="password_confirm" required minlength="6" autocomplete="new-password">
                            <button type="button" class="auth-toggle-password" data-target="password_confirm" aria-label="Toggle confirm password visibility">Show</button>
                        </div>
                    </div>
                </div>

                <label class="auth-checkbox-row">
                    <input type="checkbox" name="accept_terms" value="1" required <?= $old['accept_terms'] === '1' ? 'checked' : '' ?>>
                    <span>I agree to the Terms and Privacy Policy.</span>
                </label>

                <button type="submit" class="auth-button">Create account</button>
            </form>

            <p class="auth-links">
                Already have an account? <a href="/CitiServe/public/login.php">Log in</a>
            </p>
            <p class="auth-links">
                <a href="/CitiServe/public/index.php">Back to home</a>
            </p>
        </div>
    </section>
</main>
<script src="assets/js/auth.js" defer></script>
</body>
</html>
