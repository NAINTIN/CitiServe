<?php
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/core/CitiServeData.php';
require_once __DIR__ . '/../app/helpers/upload.php';

// Make sure the user is logged in
$authUser = require_login();

$data = new CitiServeData();
$user = $data->findUserById((int)$authUser['id']);

// If the user doesn't exist anymore, log them out
if (!$user) {
    session_start();
    session_unset();
    session_destroy();
    header('Location: /CitiServe/public/login.php');
    exit;
}

// Variables for errors and success message
$errors = [];
$success = '';

// Set the form fields to the current user values
$fullName = $user->full_name;
$address = ($user->address !== null) ? $user->address : '';
$contactNumber = ($user->contact_number !== null) ? $user->contact_number : '';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify the CSRF token to prevent cross-site attacks
    csrf_verify_or_die();

    // Get the values from the form
    $fullName = trim(isset($_POST['full_name']) ? $_POST['full_name'] : '');
    $address = trim(isset($_POST['address']) ? $_POST['address'] : '');
    $contactNumber = trim(isset($_POST['contact_number']) ? $_POST['contact_number'] : '');
    $proofFile = $_FILES['proof_of_id'] ?? null;

    // Validate the inputs
    if ($fullName === '') {
        $errors[] = 'Full name is required.';
    } elseif (mb_strlen($fullName) > 100) {
        $errors[] = 'Full name must be 100 characters or less.';
    }

    if ($address !== '' && mb_strlen($address) > 1000) {
        $errors[] = 'Address is too long.';
    }

    if ($contactNumber !== '' && mb_strlen($contactNumber) > 20) {
        $errors[] = 'Contact number must be 20 characters or less.';
    }

    $newProofPath = null;
    if ($proofFile && isset($proofFile['error']) && (int)$proofFile['error'] !== UPLOAD_ERR_NO_FILE) {
        $uploadErr = (int)$proofFile['error'];
        if ($uploadErr === UPLOAD_ERR_INI_SIZE || $uploadErr === UPLOAD_ERR_FORM_SIZE) {
            $errors[] = 'Proof of ID exceeds the maximum file size (5MB).';
        } elseif ($uploadErr !== UPLOAD_ERR_OK) {
            $errors[] = 'Proof of ID upload failed.';
        } else {
            try {
                $newProofPath = saveProofOfIdImage(
                    $proofFile,
                    __DIR__ . '/uploads/proof_of_id',
                    'uploads/proof_of_id'
                );
            } catch (Throwable $e) {
                $errors[] = 'Proof of ID: ' . $e->getMessage();
            }
        }
    }

    // If there are no errors, update the profile
    if (empty($errors)) {
        // Convert empty strings to null for the database
        $addressForDb = ($address !== '') ? $address : null;
        $contactForDb = ($contactNumber !== '') ? $contactNumber : null;

        $ok = $data->updateUserProfile(
            (int)$user->id,
            $fullName,
            $addressForDb,
            $contactForDb
        );

        if ($ok) {
            if ($newProofPath !== null) {
                $data->updateUserProofOfId((int)$user->id, $newProofPath);
            }
            $success = 'Profile updated successfully.';
            if ($newProofPath !== null) {
                $success = 'Profile updated successfully. Your proof of ID was re-uploaded and is now pending admin verification.';
            }
            // Reload the user data to show the updated values
            $user = $data->findUserById((int)$authUser['id']);
        } else {
            $errors[] = 'Failed to update profile.';
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit Profile</title>
</head>
<body>
    <h2>Edit Profile</h2>
    <p>
        <a href="/CitiServe/public/profile.php">Back to Profile</a> |
        <a href="/CitiServe/public/index.php">Home</a>
    </p>

    <?php if ($success): ?>
        <p style="color: green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div style="color: red;">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div>
            <label>Full Name</label><br>
            <input type="text" name="full_name" maxlength="100" required
                   value="<?= htmlspecialchars($fullName) ?>">
        </div>

        <div>
            <label>Address</label><br>
            <textarea name="address" rows="4" cols="50"><?= htmlspecialchars($address) ?></textarea>
        </div>

        <div>
            <label>Contact Number</label><br>
            <input type="text" name="contact_number" maxlength="20"
                   value="<?= htmlspecialchars($contactNumber) ?>">
        </div>

        <div id="proof-of-id">
            <label>Re-upload Proof of ID (JPG/PNG, max 5MB)</label><br>
            <?php if (!empty($user->proof_of_id)): ?>
                <small>Current file: <a href="/CitiServe/public/<?= htmlspecialchars((string)$user->proof_of_id) ?>" target="_blank">View Proof of ID</a></small><br>
            <?php endif; ?>
            <input type="file" name="proof_of_id" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
            <small>Uploading a new ID will reset your account to unverified until admin approval.</small>
        </div>

        <br>
        <button type="submit">Save Changes</button>
    </form>
</body>
</html>
