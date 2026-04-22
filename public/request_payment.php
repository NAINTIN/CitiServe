<?php
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/helpers/upload.php';
require_once __DIR__ . '/../app/helpers/document_request.php';

$user = require_resident();

$draft = isset($_SESSION['document_request_draft']) ? $_SESSION['document_request_draft'] : null;
if (!$draft || (int)$draft['user_id'] !== (int)$user['id']) {
    header('Location: /CitiServe/public/request_select.php');
    exit;
}

$paymentMethods = document_request_payment_methods();
$errors = [];
$paymentMethod = isset($draft['payment_method']) ? (string)$draft['payment_method'] : '';
$paymentReference = isset($draft['payment_reference']) ? (string)$draft['payment_reference'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify_or_die();

    $paymentMethod = trim((string)($_POST['payment_method'] ?? ''));
    $paymentReference = trim((string)($_POST['payment_reference'] ?? ''));

    if (!in_array($paymentMethod, $paymentMethods, true)) {
        $errors[] = 'Please select a valid payment method.';
    }

    if ($paymentReference === '') {
        $errors[] = 'Payment reference is required.';
    }

    $uploadErr = isset($_FILES['payment_proof_screenshot']['error']) ? (int)$_FILES['payment_proof_screenshot']['error'] : UPLOAD_ERR_NO_FILE;
    if ($uploadErr === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Payment proof screenshot is required.';
    } elseif ($uploadErr === UPLOAD_ERR_INI_SIZE || $uploadErr === UPLOAD_ERR_FORM_SIZE) {
        $errors[] = 'Payment proof screenshot exceeds 5MB.';
    } elseif ($uploadErr !== UPLOAD_ERR_OK) {
        $errors[] = 'Failed to upload payment proof screenshot.';
    }

    $proofPath = null;
    if (empty($errors)) {
        try {
            $proofPath = savePaymentProofScreenshot(
                $_FILES['payment_proof_screenshot'],
                __DIR__ . '/uploads/payment_proofs',
                'uploads/payment_proofs'
            );
        } catch (Throwable $e) {
            $errors[] = $e->getMessage();
        }
    }

    if (empty($errors)) {
        $_SESSION['document_request_draft']['payment_method'] = $paymentMethod;
        $_SESSION['document_request_draft']['payment_reference'] = $paymentReference;
        $_SESSION['document_request_draft']['payment_proof_path'] = $proofPath;
        header('Location: /CitiServe/public/request_confirm.php');
        exit;
    }
}

function h($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function fake_qr_html($seed)
{
    $hash = hash('sha256', $seed);
    $index = 0;
    $html = '<table cellpadding="0" cellspacing="0" style="border:4px solid #000; border-collapse:collapse;">';
    for ($r = 0; $r < 16; $r++) {
        $html .= '<tr>';
        for ($c = 0; $c < 16; $c++) {
            $char = hexdec($hash[$index % strlen($hash)]);
            $color = ($char % 2 === 0) ? '#000' : '#fff';
            $html .= '<td style="width:12px;height:12px;background:' . $color . ';border:1px solid #ddd;"></td>';
            $index++;
        }
        $html .= '</tr>';
    }
    $html .= '</table>';
    return $html;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Request Payment</title>
</head>
<body>
    <h2>Step 3: Payment</h2>
    <p>
        Service: <strong><?= h($draft['service_name']) ?></strong><br>
        Amount: <strong>₱<?= h($draft['fee']) ?></strong>
    </p>

    <p>
        <a href="/CitiServe/public/request_form.php?service_id=<?= (int)$draft['service_id'] ?>">Back to Form</a>
    </p>

    <?php if ($errors): ?>
        <div style="color:red;">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= h($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <h3>Scan / Pay (Sample QR)</h3>
    <?= fake_qr_html($draft['service_name'] . '|' . $user['id']) ?>

    <form method="post" enctype="multipart/form-data" style="margin-top:12px;">
        <?= csrf_field() ?>

        <div style="margin-bottom:10px;">
            <label for="payment_method">Payment Method</label><br>
            <select id="payment_method" name="payment_method" required>
                <option value="">-- Select Payment Method --</option>
                <?php foreach ($paymentMethods as $method): ?>
                    <option value="<?= h($method) ?>" <?= ($paymentMethod === $method) ? 'selected' : '' ?>><?= h($method) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-bottom:10px;">
            <label for="payment_reference">Payment Reference</label><br>
            <input id="payment_reference" name="payment_reference" value="<?= h($paymentReference) ?>" required>
        </div>

        <div style="margin-bottom:10px;">
            <label for="payment_proof_screenshot">Payment Proof Screenshot (JPG/PNG, max 5MB)</label><br>
            <input type="file" id="payment_proof_screenshot" name="payment_proof_screenshot" accept=".jpg,.jpeg,.png,image/jpeg,image/png" required>
        </div>

        <button type="submit">Continue to Confirmation</button>
    </form>
</body>
</html>
