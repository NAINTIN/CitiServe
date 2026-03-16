<?php
// Include the UserRepository so we can create new users
require_once __DIR__ . '/../app/repositories/UserRepository.php';

// Start the session (we need it to log in the user after registration)
session_start();

// Create a UserRepository to interact with the users table
$repo = new UserRepository();

// This array will hold any error messages
$errors = [];

// This array remembers what the user typed so we can show it again if there's an error
$old = ['full_name' => '', 'email' => ''];

// Check if the form was submitted (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get the values from the form and trim whitespace
    $full_name = trim(isset($_POST['full_name']) ? $_POST['full_name'] : '');
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

    // Save the values so we can show them again in the form
    $old['full_name'] = $full_name;
    $old['email'] = $email;

    // Validate the form inputs
    if ($full_name === '') {
        $errors[] = 'Full name is required.';
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

    // If there are no errors so far, try to create the account
    if (empty($errors)) {
        // Check if the email is already taken
        $existingUser = $repo->findByEmail($email);

        if ($existingUser) {
            $errors[] = 'Email already registered.';
        } else {
            // Hash the password for safe storage
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // Create the user in the database
            $userId = $repo->create([
                'full_name' => $full_name,
                'email' => $email,
                'password_hash' => $hash
            ]);

            // Log the user in by saving their ID in the session
            $_SESSION['user_id'] = $userId;

            // Redirect to the home page
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