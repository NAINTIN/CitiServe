<?php
require_once __DIR__ . '/../app/core/CitiServeData.php';

// Start the session
session_start();

$data = new CitiServeData();

// Variables to store error message and the email they typed
$error = '';
$email = '';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get the email and password from the form
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Make sure both fields are filled in
    if ($email === '' || $password === '') {
        $error = 'Email and password are required.';
    } else {
        // Try to find the user by email
        $user = $data->findUserByEmail($email);

        // Check if user exists and password is correct
        if ($user && password_verify($password, $user->password_hash)) {
            // Password is correct! Save the user ID in the session
            $_SESSION['user_id'] = $user->id;

            // Redirect to the home page
            header('Location: /CitiServe/public/index.php');
            exit;
        } else {
            // Wrong email or password
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
