<?php
// Include helpers and repositories
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/repositories/UserRepository.php';
require_once __DIR__ . '/../app/repositories/NotificationRepository.php';

define('RESIDENCY_PROOF_MAX_SIZE_BYTES', 5 * 1024 * 1024);
define('RESIDENCY_PROOF_STORAGE_RELATIVE_DIR', 'storage/residency_proofs');
define('RESIDENCY_PROOF_FILENAME_RANDOM_BYTES', 16);

function bytes_from_ini($value)
{
    $value = trim((string)$value);
    if ($value === '') {
        return 0;
    }

    $unit = strtolower(substr($value, -1));
    $number = (float)$value;

    if ($unit === 'g') {
        return (int)($number * 1024 * 1024 * 1024);
    } elseif ($unit === 'm') {
        return (int)($number * 1024 * 1024);
    } elseif ($unit === 'k') {
        return (int)($number * 1024);
    }

    return (int)$number;
}

function max_size_label($bytes)
{
    return number_format($bytes / (1024 * 1024), 2) . 'MB';
}

// Make sure the user is a resident
$authUser = require_resident();
$userRepo = new UserRepository();
$notifRepo = new NotificationRepository();
$user = $userRepo->findById((int)$authUser['id']);

// Safety: if the user disappeared, force login again
if (!$user) {
    session_start();
    session_unset();
    session_destroy();
    header('Location: /CitiServe/public/login.php');
    exit;
}

$errors = [];
$success = '';
$uploadMax = bytes_from_ini((string)ini_get('upload_max_filesize'));
$postMax = bytes_from_ini((string)ini_get('post_max_size'));
$effectiveMaxBytes = RESIDENCY_PROOF_MAX_SIZE_BYTES;
if ($uploadMax > 0) {
    $effectiveMaxBytes = min($effectiveMaxBytes, $uploadMax);
}
if ($postMax > 0) {
    $effectiveMaxBytes = min($effectiveMaxBytes, $postMax);
}
$effectiveMaxLabel = max_size_label($effectiveMaxBytes);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contentLen = isset($_SERVER['CONTENT_LENGTH']) ? (int)$_SERVER['CONTENT_LENGTH'] : 0;
    $isPostMaxExceeded = $postMax > 0 && $contentLen > $postMax && empty($_POST) && empty($_FILES);
    if ($isPostMaxExceeded) {
        $errors[] = 'Uploaded data is too large for server limit. Please upload a file up to ' . $effectiveMaxLabel . '.';
    } else {
        csrf_verify_or_die();

        if ((int)$user->is_verified === 1) {
            $errors[] = 'Your account is already verified.';
        } elseif ($user->residency_verification_status === 'pending') {
            $errors[] = 'Your proof is already pending review.';
        } else {
            if (!isset($_FILES['residency_proof']) || (int)$_FILES['residency_proof']['error'] === UPLOAD_ERR_NO_FILE) {
                $errors[] = 'Please upload a proof of residency photo.';
            } else {
                $err = (int)$_FILES['residency_proof']['error'];
                if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
                    $errors[] = 'File is too large. Maximum allowed is ' . $effectiveMaxLabel . '.';
                } elseif ($err !== UPLOAD_ERR_OK) {
                    $errors[] = 'Proof upload failed (code ' . $err . ').';
                } else {
                    $size = isset($_FILES['residency_proof']['size']) ? (int)$_FILES['residency_proof']['size'] : 0;
                    $tmp = isset($_FILES['residency_proof']['tmp_name']) ? $_FILES['residency_proof']['tmp_name'] : '';

                    if ($size <= 0 || $size > $effectiveMaxBytes || $tmp === '' || !is_uploaded_file($tmp)) {
                        $errors[] = 'Please upload a valid image file up to ' . $effectiveMaxLabel . '.';
                    } else {
                        $finfo = new finfo(FILEINFO_MIME_TYPE);
                        $mime = (string)$finfo->file($tmp);
                        $allowedMimeToExt = [
                            'image/jpeg' => 'jpg',
                            'image/png'  => 'png',
                        ];

                        if (!isset($allowedMimeToExt[$mime])) {
                            $errors[] = 'Proof must be a JPG or PNG photo.';
                        } else {
                            $uploadDir = __DIR__ . '/../' . RESIDENCY_PROOF_STORAGE_RELATIVE_DIR;
                            if (!is_dir($uploadDir)) {
                                $created = mkdir($uploadDir, 0750, true);
                                if (!$created && !is_dir($uploadDir)) {
                                    $errors[] = 'Failed to create upload directory.';
                                }
                            }

                            if (empty($errors) && !is_writable($uploadDir)) {
                                $errors[] = 'Upload directory is not writable.';
                            }

                            if (empty($errors)) {
                                $ext = $allowedMimeToExt[$mime];
                                $safeName = bin2hex(random_bytes(RESIDENCY_PROOF_FILENAME_RANDOM_BYTES)) . '.' . $ext;
                                $dest = rtrim($uploadDir, '/\\') . DIRECTORY_SEPARATOR . $safeName;

                                if (!move_uploaded_file($tmp, $dest)) {
                                    $errors[] = 'Failed to save uploaded proof file.';
                                } else {
                                    try {
                                        $relativePath = RESIDENCY_PROOF_STORAGE_RELATIVE_DIR . '/' . $safeName;
                                        $saved = $userRepo->submitResidencyProof((int)$user->id, $relativePath);

                                        if (!$saved) {
                                            $errors[] = 'Failed to submit proof. Please try again.';
                                        } else {
                                            $adminIds = $userRepo->getAdminIds();
                                            foreach ($adminIds as $adminId) {
                                                $notifRepo->create(
                                                    (int)$adminId,
                                                    'Residency proof submitted',
                                                    $user->full_name . ' submitted a proof of residency for verification.',
                                                    '/CitiServe/public/admin/users.php'
                                                );
                                            }

                                            $success = 'Proof submitted successfully. Please wait for admin review.';
                                            $user = $userRepo->findById((int)$authUser['id']);
                                        }
                                    } catch (Throwable $e) {
                                        if (is_file($dest)) {
                                            if (!unlink($dest)) {
                                                error_log('Failed to remove uploaded residency proof after DB error: ' . basename($dest));
                                            }
                                        }
                                        error_log('Residency proof submit failed: ' . $e->getMessage());
                                        $errors[] = 'Failed to submit proof due to a system error. Please contact support.';
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Residency Verification</title>
</head>
<body>
    <h2>Residency Verification</h2>
    <p>
        <a href="/CitiServe/public/index.php">Home</a> |
        <a href="/CitiServe/public/profile.php">My Profile</a> |
        <a href="/CitiServe/public/logout.php">Logout</a>
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

    <p>
        Current verification status:
        <strong><?= htmlspecialchars($user->residency_verification_status) ?></strong>
    </p>

    <?php if ((int)$user->is_verified === 1): ?>
        <p style="color: green;">Your account is verified. You can submit complaints.</p>
    <?php elseif ($user->residency_verification_status === 'pending'): ?>
        <p>Your proof is pending admin review. Please wait for an update in your notifications.</p>
    <?php else: ?>
        <form method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div>
                <label>Upload proof of residency (JPG/PNG, max <?= htmlspecialchars($effectiveMaxLabel) ?>)</label><br>
                <input type="file" name="residency_proof" accept=".jpg,.jpeg,.png,image/jpeg,image/png" required>
            </div>
            <br>
            <button type="submit">Submit Proof</button>
        </form>
    <?php endif; ?>
</body>
</html>
