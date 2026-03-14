<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/repositories/DocumentServiceRepository.php';
require_once __DIR__ . '/../app/repositories/DocumentRequestRepository.php';
require_once __DIR__ . '/../app/helpers/upload.php';

function bytes_from_ini(string $v): int
{
    $v = trim($v);
    if ($v === '') return 0;
    $unit = strtolower(substr($v, -1));
    $num = (float)$v;

    return match ($unit) {
        'g' => (int)($num * 1024 * 1024 * 1024),
        'm' => (int)($num * 1024 * 1024),
        'k' => (int)($num * 1024),
        default => (int)$num,
    };
}

$user = require_resident();
$serviceRepo = new DocumentServiceRepository();
$requestRepo = new DocumentRequestRepository();

$services = $serviceRepo->getAllActive();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle request body too large (post_max_size overflow) BEFORE CSRF check
    $contentLen = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
    $postMax = bytes_from_ini((string)ini_get('post_max_size'));

    if ($contentLen > 0 && $postMax > 0 && $contentLen > $postMax && empty($_POST) && empty($_FILES)) {
        $errors[] = 'Uploaded data is too large for server limit. Please upload a smaller file (max 5MB).';
    } else {
        csrf_verify_or_die();

        $serviceId = (int)($_POST['service_id'] ?? 0);
        $notes = trim($_POST['notes'] ?? '');

        if ($serviceId <= 0) {
            $errors[] = 'Please select a document service.';
        }

        $paymentProofPath = null;

        // Upload handling (friendly size/type errors)
        if (isset($_FILES['payment_proof']) && (int)$_FILES['payment_proof']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadErr = (int)$_FILES['payment_proof']['error'];

            if ($uploadErr === UPLOAD_ERR_INI_SIZE || $uploadErr === UPLOAD_ERR_FORM_SIZE) {
                $errors[] = 'Payment proof exceeds the maximum allowed size (max 5MB).';
            } elseif ($uploadErr !== UPLOAD_ERR_OK) {
                $errors[] = 'Upload failed. Please try again.';
            } else {
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

        if (empty($errors)) {
            $requestId = $requestRepo->create([
                'user_id' => (int)$user['id'],
                'document_service_id' => $serviceId,
                'purpose' => $notes !== '' ? $notes : null,
                'payment_proof_path' => $paymentProofPath,
            ]);

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
                    <option value="<?= (int)$s['id'] ?>"
                        <?= ((int)($_POST['service_id'] ?? 0) === (int)$s['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['name']) ?> - ₱<?= htmlspecialchars((string)$s['price']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label>Notes / Additional info</label><br>
            <textarea name="notes" rows="4" cols="50"><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
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