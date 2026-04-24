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

function placeholder_qr_html($seed)
{
    $hash = hash('sha256', $seed);
    $index = 0;
    $html = '<table cellpadding="0" cellspacing="0" style="border:4px solid #000; border-collapse:collapse;">';
    for ($r = 0; $r < 16; $r++) {
        $html .= '<tr>';
        for ($c = 0; $c < 16; $c++) {
            $char = hexdec($hash[$index % strlen($hash)]);
            $color = ($char % 2 === 0) ? '#000' : '#fff';
            $html .= '<td style="width:12px;height:12px;background:' . htmlspecialchars($color, ENT_QUOTES, 'UTF-8') . ';border:1px solid #ddd;"></td>';
            $index++;
        }
        $html .= '</tr>';
    }
    $html .= '</table>';
    return $html;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Payment</title>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/CitiServe/frontend/document_request/css/docu_business_payment.css">
    <style>
        .top-links { margin-bottom: 12px; font-size: 13px; color: #6B7280; }
        .top-links a { color: #6B7280; text-decoration: none; margin-right: 12px; }
        .top-links a:hover { color: #E8265E; }
        .error-box { color: #B91C1C; background: #FEF2F2; border: 1px solid #FECACA; border-radius: 10px; padding: 12px; margin-bottom: 14px; }
        .select-like, .input-like { width: 100%; border: 1.5px solid #E5E7EB; border-radius: 3px; font-size: 12.5px; height: 35px; padding: 0 14px; }
        .upload-box { background: #fff; align-items: flex-start; }
        .upload-box input[type=file] { width: 100%; border: none; padding: 0; }
        .qr-holder { display: flex; justify-content: center; padding: 5px 0 10px; }
    </style>
</head>
<body>
<div class="content-area">
    <div class="top-links">
        <a href="/CitiServe/public/request_form.php?service_id=<?= (int)$draft['service_id'] ?>">Back to Form</a>
        <a href="/CitiServe/public/dashboard.php">Dashboard</a>
    </div>

    <div class="form-breadcrumb" id="form-breadcrumb"></div>

    <h1 class="form-title" id="pageTitle">Payment</h1>
    <p class="form-subtitle">Choose your preferred payment method and complete the transaction to proceed with your request.</p>

    <div class="form-stepper">
        <div class="form-step">
            <div class="step-icon"><img src="/CitiServe/frontend/document_request/images/docu-personal-info.png" class="step-img-only" alt=""></div>
            <div class="step-label done-text"><span class="step-name">Fill out Form</span><span class="step-sub">Enter your details</span></div>
        </div>
        <div class="step-arrow"><img src="/CitiServe/frontend/document_request/images/docu-arrow.png" alt=""></div>
        <div class="form-step">
            <div class="step-icon active-icon"><img src="/CitiServe/frontend/document_request/images/docu-full-payment.png" class="step-img-only" alt=""></div>
            <div class="step-label"><span class="step-name active-text">Payment</span><span class="step-sub">Complete your payment</span></div>
        </div>
        <div class="step-arrow"><img src="/CitiServe/frontend/document_request/images/docu-arrow.png" alt=""></div>
        <div class="form-step">
            <div class="step-icon"><img src="/CitiServe/frontend/document_request/images/docu-payment-info.png" class="step-img-only" alt=""></div>
            <div class="step-label inactive-text"><span class="step-name">Confirmation</span><span class="step-sub">Review and confirm</span></div>
        </div>
    </div>

    <?php if ($errors): ?>
        <div class="error-box">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= h($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="form-wrapper">
            <div class="form-main">
                <div class="form-card pink">
                    <div class="form-card-bar">
                        <img src="/CitiServe/frontend/document_request/images/docu-order-summary.png" alt="">
                        Order Summary
                    </div>
                    <div class="form-card-body">
                        <div class="order-row">
                            <div class="order-info">
                                <span class="order-name"><?= h($draft['service_name']) ?></span>
                                <span class="order-sub">Barangay Kalayaan, Angono, Rizal</span>
                            </div>
                            <span class="order-price">₱<?= h($draft['fee']) ?></span>
                        </div>
                        <div class="order-divider"></div>
                        <div class="order-total-row">
                            <span class="order-total-label">Total Amount Due</span>
                            <span class="order-price order-total-price">₱<?= h($draft['fee']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="form-card">
                    <div class="form-card-bar">
                        <img src="/CitiServe/frontend/document_request/images/docu-select-payment.png" alt="">
                        Select Payment Method
                    </div>
                    <div class="form-card-body">
                        <select class="select-like" id="payment_method" name="payment_method" required>
                            <option value="">-- Select Payment Method --</option>
                            <?php foreach ($paymentMethods as $method): ?>
                                <option value="<?= h($method) ?>" <?= ($paymentMethod === $method) ? 'selected' : '' ?>><?= h($method) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-card">
                    <div class="form-card-bar">
                        <img src="/CitiServe/frontend/document_request/images/docu-payment-proof.png" alt="">
                        QR Code & Payment Instructions
                    </div>
                    <div class="form-card-body">
                        <div class="qr-holder"><?= placeholder_qr_html($draft['service_name'] . '|' . $user['id']) ?></div>
                        <div class="form-group">
                            <label for="payment_reference">Reference / Transaction Number <span class="req">*</span></label>
                            <input class="input-like" id="payment_reference" name="payment_reference" value="<?= h($paymentReference) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="payment_proof_screenshot">Upload Payment Screenshot / Proof <span class="req">*</span></label>
                            <div class="upload-box">
                                <input type="file" id="payment_proof_screenshot" name="payment_proof_screenshot" accept=".jpg,.jpeg,.png,image/jpeg,image/png" required>
                            </div>
                            <small class="upload-note">JPG, PNG – max 5MB</small>
                        </div>
                    </div>
                    <div class="form-btn-divider"></div>
                    <div class="form-btn-row">
                        <div class="form-btn-group">
                            <a class="form-btn form-btn-back" href="/CitiServe/public/request_form.php?service_id=<?= (int)$draft['service_id'] ?>">
                                <img src="/CitiServe/frontend/document_request/images/docu-back.png" alt="Back">
                            </a>
                            <button class="form-btn" type="submit">
                                <img src="/CitiServe/frontend/document_request/images/docu-submit -report.png" alt="Continue">
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-side">
                <div class="form-card">
                    <div class="form-card-bar form-gradient">Order</div>
                    <div class="form-card-body summary-body">
                        <div class="summary-row">
                            <span class="summary-label">Document</span>
                            <span class="summary-value"><?= h($draft['service_name']) ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Total</span>
                            <span class="summary-fee">₱<?= h($draft['fee']) ?></span>
                        </div>
                    </div>
                </div>
                <div class="important-box">
                    <img src="/CitiServe/frontend/document_request/images/docu-important.png" style="width:100%; border-radius:14px;" alt="Important">
                </div>
            </div>
        </div>
    </form>

    <div class="form-logo">
        <img src="/CitiServe/frontend/document_request/images/docu-logo.png" alt="CitiServe">
        <div class="form-logo-text">
            <span class="logo-pink">CitiServe</span>
            <span class="logo-gray"> © 2026. All rights reserved.</span>
        </div>
    </div>
</div>

<script>
const trail = [
  { label: "Document Requests", href: "/CitiServe/public/request_select.php" },
  { label: "Request Document", href: "/CitiServe/public/request_select.php" },
  { label: <?= json_encode((string)$draft['service_name'], JSON_UNESCAPED_UNICODE) ?>, href: "/CitiServe/public/request_form.php?service_id=<?= (int)$draft['service_id'] ?>" },
  { label: "Payment", href: null }
];
(function renderBreadcrumb() {
  const el = document.getElementById("form-breadcrumb");
  el.innerHTML = trail.map((item, i) => {
    const isLast = i === trail.length - 1;
    const sep = i > 0 ? `<span class="form-sep">></span>` : "";
    if (isLast) return `${sep}<span class="form-active">${item.label}</span>`;
    return `${sep}<a href="${item.href}">${item.label}</a>`;
  }).join("");
})();
</script>
</body>
</html>
