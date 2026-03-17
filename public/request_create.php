<?php
// Don't show errors on the page (for security), but still log them
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../storage/request_create_error.log');

// Include all the files we need
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/repositories/DocumentServiceRepository.php';
require_once __DIR__ . '/../app/repositories/DocumentRequestRepository.php';
require_once __DIR__ . '/../app/helpers/upload.php';
require_once __DIR__ . '/../app/repositories/UserRepository.php';
require_once __DIR__ . '/../app/repositories/NotificationRepository.php';

// This function converts PHP ini values like "8M" or "2G" into bytes
// We need this to check if the uploaded file is too big
function bytes_from_ini($value)
{
    $value = trim($value);
    if ($value === '') {
        return 0;
    }

    // Get the last character (the unit: g, m, or k)
    $unit = strtolower(substr($value, -1));
    $number = (float)$value;

    // Convert based on the unit
    if ($unit === 'g') {
        return (int)($number * 1024 * 1024 * 1024);
    } elseif ($unit === 'm') {
        return (int)($number * 1024 * 1024);
    } elseif ($unit === 'k') {
        return (int)($number * 1024);
    } else {
        return (int)$number;
    }
}

// Make sure the user is a resident (only residents can create requests)
$user = require_resident();

// Get the list of available services and create a request repository
$serviceRepo = new DocumentServiceRepository();
$requestRepo = new DocumentRequestRepository();
$notificationRepo = new NotificationRepository();
$userRepo = new UserRepository();
$services = $serviceRepo->getAllActive();

// Variables for errors and success message
$errors = [];
$success = '';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if the uploaded data is too large (server limit)
    $contentLen = 0;
    if (isset($_SERVER['CONTENT_LENGTH'])) {
        $contentLen = (int)$_SERVER['CONTENT_LENGTH'];
    }
    $postMaxSetting = (string)ini_get('post_max_size');
    $postMax = bytes_from_ini($postMaxSetting);

    if ($contentLen > 0 && $postMax > 0 && $contentLen > $postMax && empty($_POST) && empty($_FILES)) {
        $errors[] = 'Uploaded data is too large for server limit. Please upload a smaller file (max 5MB).';
    } else {
        // Verify the CSRF token
        csrf_verify_or_die();

        // Get the selected service ID and notes from the form
        $serviceId = 0;
        if (isset($_POST['service_id'])) {
            $serviceId = (int)$_POST['service_id'];
        }

        $notes = '';
        if (isset($_POST['notes'])) {
            $notes = trim($_POST['notes']);
        }

        // Make sure a service was selected
        if ($serviceId <= 0) {
            $errors[] = 'Please select a document service.';
        }

        // Handle the payment proof upload (optional)
        $paymentProofPath = null;

        if (isset($_FILES['payment_proof']) && (int)$_FILES['payment_proof']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadErr = (int)$_FILES['payment_proof']['error'];

            if ($uploadErr === UPLOAD_ERR_INI_SIZE || $uploadErr === UPLOAD_ERR_FORM_SIZE) {
                $errors[] = 'Payment proof exceeds the maximum allowed size (max 5MB).';
            } elseif ($uploadErr !== UPLOAD_ERR_OK) {
                $errors[] = 'Upload failed. Please try again.';
            } else {
                // Try to save the uploaded file
                try {
                    $paymentProofPath = savePaymentProof(
                        $_FILES['payment_proof'],
                        __DIR__ . '/uploads/payment_proofs',
                        'uploads/payment_proofs'
                    );
                } catch (Throwable $e) {
                    $errors[] = $e->getMessage();
                }
            }
        }

        

        // If there are no errors, create the document request
        if (empty($errors)) {
            // Set purpose to null if empty
            $purpose = null;
            if ($notes !== '') {
                $purpose = $notes;
            }

            $requestId = $requestRepo->create([
                'user_id' => (int)$user['id'],
                'document_service_id' => $serviceId,
                'purpose' => $purpose,
                'payment_proof_path' => $paymentProofPath,
            ]);

            if ($requestId > 0) {
        $admins = $userRepo->getByRole('admin');
        foreach ($admins as $admin) {
            $notificationRepo->create(
                (int)$admin['id'],
                'New Document Request',
                'A resident submitted a document request.',
                '/CitiServe/public/admin/requests.php'
            );
        }
    }

            $success = "Request submitted successfully. Request #{$requestId}";
            $_POST = [];
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Create Document Request</title>
</head>
<body>
    <h2>Create Document Request</h2>
    <p>
        <a href="/CitiServe/public/services.php">Back to Services</a> |
        <a href="/CitiServe/public/my_requests.php">My Requests</a> |
        <a href="/CitiServe/public/index.php">Home</a>
    </p>

    <?php if ($success): ?>
        <p style="color:green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div style="color:red;">
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
            <label>Document Service</label><br>
            <select name="service_id" required>
                <option value="">-- Select Service --</option>
                <?php foreach ($services as $s): ?>
                    <?php
                    // Check if this service was previously selected
                    $selectedServiceId = 0;
                    if (isset($_POST['service_id'])) {
                        $selectedServiceId = (int)$_POST['service_id'];
                    }
                    $isSelected = ($selectedServiceId === (int)$s['id']) ? 'selected' : '';
                    ?>
                    <option value="<?= (int)$s['id'] ?>" <?= $isSelected ?>>
                        <?= htmlspecialchars($s['name']) ?> - ₱<?= htmlspecialchars((string)$s['price']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label>Notes / Additional info</label><br>
            <?php
            $previousNotes = '';
            if (isset($_POST['notes'])) {
                $previousNotes = $_POST['notes'];
            }
            ?>
            <textarea name="notes" rows="4" cols="50"><?= htmlspecialchars($previousNotes) ?></textarea>
        </div>

        <div>
            <label>Payment Proof (optional: jpg/jpeg/png/pdf, max 5MB)</label><br>
            <input type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.pdf,application/pdf,image/jpeg,image/png">
        </div>

        <br>
        <button type="submit">Submit Request</button>
    </form>
</body>
</html>