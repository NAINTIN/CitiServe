<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/repositories/DocumentServiceRepository.php';
require_once __DIR__ . '/../app/repositories/DocumentRequestRepository.php';

$user = require_login();
$serviceRepo = new DocumentServiceRepository();
$requestRepo = new DocumentRequestRepository();

$services = $serviceRepo->getAllActive();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify_or_die();

    $serviceId = (int)($_POST['service_id'] ?? 0);
    $notes = trim($_POST['notes'] ?? '');

    if ($serviceId <= 0) {
        $errors[] = 'Please select a document service.';
    }

    $paymentProofPath = null;

    if (!empty($_FILES['payment_proof']['name'])) {
        $uploadDir = __DIR__ . '/uploads/payment_proofs';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $tmp = $_FILES['payment_proof']['tmp_name'];
        $original = $_FILES['payment_proof']['name'];
        $size = (int)$_FILES['payment_proof']['size'];
        $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];

        if (!in_array($ext, $allowed, true)) {
            $errors[] = 'Payment proof must be jpg, jpeg, png, or pdf.';
        } elseif ($size > 10 * 1024 * 1024) {
            $errors[] = 'Payment proof must be 10MB or less.';
        } else {
            $safeName = uniqid('pay_', true) . '.' . $ext;
            $dest = $uploadDir . '/' . $safeName;
            if (move_uploaded_file($tmp, $dest)) {
                $paymentProofPath = 'uploads/payment_proofs/' . $safeName;
            } else {
                $errors[] = 'Failed to upload payment proof.';
            }
        }
    }

    if (empty($errors)) {
        $requestId = $requestRepo->create([
            'user_id' => (int)$user['id'],
            'document_service_id' => $serviceId,
            'notes' => $notes !== '' ? $notes : null,
            'payment_proof_path' => $paymentProofPath
        ]);

        $success = "Request submitted successfully. Request #{$requestId}";
        $_POST = [];
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
                        <?= htmlspecialchars($s['name']) ?> - ₱<?= htmlspecialchars($s['price']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label>Notes / Additional info</label><br>
            <textarea name="notes" rows="4" cols="50"><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
        </div>

        <div>
            <label>Payment Proof (optional: jpg/jpeg/png/pdf, max 10MB)</label><br>
            <input type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.pdf">
        </div>

        <br>
        <button type="submit">Submit Request</button>
    </form>
</body>
</html>