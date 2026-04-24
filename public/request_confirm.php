<?php
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../storage/request_confirm_error.log');

require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/core/CitiServeData.php';
require_once __DIR__ . '/../app/helpers/document_request.php';

$user = require_verified_resident('document request pages');
$data = new CitiServeData();
$db = $data->getPdo();

$draft = isset($_SESSION['document_request_draft']) ? $_SESSION['document_request_draft'] : null;
if (!$draft || (int)$draft['user_id'] !== (int)$user['id']) {
    header('Location: /CitiServe/public/request_select.php');
    exit;
}

if (empty($draft['form_values']) || empty($draft['files']) || empty($draft['payment_method']) || empty($draft['payment_reference']) || empty($draft['payment_proof_path'])) {
    header('Location: /CitiServe/public/request_payment.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify_or_die();

    $action = trim((string)($_POST['action'] ?? ''));
    if ($action === 'cancel') {
        unset($_SESSION['document_request_draft']);
        header('Location: /CitiServe/public/request_select.php');
        exit;
    }

    if ($action !== 'submit') {
        $errors[] = 'Invalid action.';
    }

    if (empty($errors)) {
        $purpose = null;
        $values = (array)$draft['form_values'];
        foreach (['purpose', 'purpose_of_request', 'purpose_of_clearance'] as $purposeField) {
            if (!empty($values[$purposeField])) {
                $purpose = trim((string)$values[$purposeField]);
                break;
            }
        }

        $db->beginTransaction();
        try {
            $requestId = $data->createDocumentRequest([
                'user_id' => (int)$user['id'],
                'document_service_id' => (int)$draft['service_id'],
                'purpose' => $purpose,
                'payment_method' => (string)$draft['payment_method'],
                'payment_reference' => (string)$draft['payment_reference'],
                'payment_proof_path' => (string)$draft['payment_proof_path'],
                'form_data_json' => json_encode($values, JSON_UNESCAPED_UNICODE),
            ]);

            foreach ((array)$draft['files'] as $f) {
                $data->addDocumentRequestFile(
                    $requestId,
                    (string)$f['file_type'],
                    (string)$f['file_path'],
                    isset($f['original_name']) ? (string)$f['original_name'] : null
                );
            }

            try {
                $admins = $data->getUsersByRole('admin');
                foreach ($admins as $admin) {
                    try {
                        $data->createNotification(
                            (int)$admin['id'],
                            'New Document Request Submitted',
                            'A resident submitted a document request.',
                            '/CitiServe/public/admin/requests.php'
                        );
                    } catch (Throwable $notificationError) {
                        error_log('REQUEST ADMIN NOTIFICATION ERROR: ' . $notificationError->getMessage());
                    }
                }
            } catch (Throwable $adminLookupError) {
                error_log('REQUEST ADMIN LOOKUP ERROR: ' . $adminLookupError->getMessage());
            }

            $db->commit();

            $_SESSION['document_request_success'] = [
                'request_id' => $requestId,
                'service_name' => $draft['service_name'],
                'fee' => $draft['fee'],
                'payment_method' => $draft['payment_method'],
                'status' => 'received',
                'submitted_at' => date('Y-m-d H:i:s'),
            ];

            unset($_SESSION['document_request_draft']);
            header('Location: /CitiServe/public/request_success.php?request_id=' . (int)$requestId);
            exit;
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log('REQUEST CONFIRM ERROR: ' . $e->getMessage());
            $errors[] = 'Failed to submit request. Please try again.';
        }
    }
}

function h($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Request</title>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/CitiServe/frontend/document_request/css/docu_business_payment.css">
    <style>
        .top-links { margin-bottom: 12px; font-size: 13px; color: #6B7280; }
        .top-links a { color: #6B7280; text-decoration: none; margin-right: 12px; }
        .top-links a:hover { color: #E8265E; }
        .error-box { color: #B91C1C; background: #FEF2F2; border: 1px solid #FECACA; border-radius: 10px; padding: 12px; margin-bottom: 14px; }
        .summary-list { list-style: none; padding: 0; margin: 0; display: grid; gap: 8px; }
        .summary-list li { display: flex; justify-content: space-between; gap: 14px; font-size: 13px; border-bottom: 1px solid #F3F4F6; padding-bottom: 8px; }
        .summary-list li:last-child { border-bottom: none; padding-bottom: 0; }
        .summary-label { color: #6B7280; font-weight: 600; }
        .summary-value { color: #111827; text-align: right; word-break: break-word; }
        .files-list { list-style: none; padding: 0; margin: 0; display: grid; gap: 10px; }
        .files-list a { color: #E8265E; font-weight: 600; text-decoration: none; }
        .files-list a:hover { text-decoration: underline; }
        .action-buttons { display: flex; gap: 10px; flex-wrap: wrap; }
        .submit-btn, .cancel-btn { border: 0; border-radius: 10px; padding: 10px 16px; font-family: inherit; cursor: pointer; font-weight: 700; }
        .submit-btn { background: #E8265E; color: #fff; }
        .cancel-btn { background: #F3F4F6; color: #374151; }
    </style>
</head>
<body>
<div class="content-area">
    <div class="top-links">
        <a href="/CitiServe/public/request_payment.php">Back to Payment</a>
        <a href="/CitiServe/public/dashboard.php">Dashboard</a>
    </div>

    <div class="form-breadcrumb" id="form-breadcrumb"></div>

    <h1 class="form-title">Review and Confirm</h1>
    <p class="form-subtitle">Please review your details before final submission.</p>

    <div class="form-stepper">
        <div class="form-step">
            <div class="step-icon"><img src="/CitiServe/frontend/document_request/images/docu-personal-info.png" class="step-img-only" alt=""></div>
            <div class="step-label done-text"><span class="step-name">Fill out Form</span><span class="step-sub">Enter your details</span></div>
        </div>
        <div class="step-arrow"><img src="/CitiServe/frontend/document_request/images/docu-arrow.png" alt=""></div>
        <div class="form-step">
            <div class="step-icon"><img src="/CitiServe/frontend/document_request/images/docu-full-payment.png" class="step-img-only" alt=""></div>
            <div class="step-label done-text"><span class="step-name">Payment</span><span class="step-sub">Complete your payment</span></div>
        </div>
        <div class="step-arrow"><img src="/CitiServe/frontend/document_request/images/docu-arrow.png" alt=""></div>
        <div class="form-step">
            <div class="step-icon active-icon"><img src="/CitiServe/frontend/document_request/images/docu-payment-info.png" class="step-img-only" alt=""></div>
            <div class="step-label"><span class="step-name active-text">Confirmation</span><span class="step-sub">Review and confirm</span></div>
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

    <div class="form-wrapper">
        <div class="form-main">
            <div class="form-card">
                <div class="form-card-bar">
                    <img src="/CitiServe/frontend/document_request/images/docu-order-summary.png" alt="">
                    Request Summary
                </div>
                <div class="form-card-body">
                    <ul class="summary-list">
                        <li><span class="summary-label">Service</span><span class="summary-value"><?= h($draft['service_name']) ?></span></li>
                        <li><span class="summary-label">Amount</span><span class="summary-value">₱<?= h($draft['fee']) ?></span></li>
                        <li><span class="summary-label">Payment Method</span><span class="summary-value"><?= h($draft['payment_method']) ?></span></li>
                        <li><span class="summary-label">Payment Reference</span><span class="summary-value"><?= h($draft['payment_reference']) ?></span></li>
                    </ul>
                </div>
            </div>

            <div class="form-card">
                <div class="form-card-bar">
                    <img src="/CitiServe/frontend/document_request/images/docu-req-info.png" alt="">
                    Form Details
                </div>
                <div class="form-card-body">
                    <ul class="summary-list">
                        <?php foreach ((array)$draft['form_values'] as $key => $value): ?>
                            <?php if ($value === ''): continue; endif; ?>
                            <li>
                                <span class="summary-label"><?= h(document_request_readable_field_name($key)) ?></span>
                                <span class="summary-value"><?= nl2br(h($value)) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <div class="form-card">
                <div class="form-card-bar">
                    <img src="/CitiServe/frontend/document_request/images/docu-payment-proof.png" alt="">
                    Uploaded Files
                </div>
                <div class="form-card-body">
                    <ul class="files-list">
                        <?php foreach ((array)$draft['files'] as $file): ?>
                            <li><?= h($file['label']) ?> - <a href="/CitiServe/public/<?= h($file['file_path']) ?>" target="_blank">View</a></li>
                        <?php endforeach; ?>
                        <li>Payment Proof Screenshot - <a href="/CitiServe/public/<?= h($draft['payment_proof_path']) ?>" target="_blank">View</a></li>
                    </ul>
                </div>
                <div class="form-btn-divider"></div>
                <div class="form-card-body">
                    <form method="post" onsubmit="return confirm('Submit this document request?');" class="action-buttons">
                        <?= csrf_field() ?>
                        <button class="submit-btn" type="submit" name="action" value="submit">Submit Request</button>
                        <button class="cancel-btn" type="submit" name="action" value="cancel" onclick="return confirm('Cancel this request?');">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
  { label: <?= json_encode((string)$draft['service_name'], JSON_UNESCAPED_UNICODE) ?>, href: "/CitiServe/public/request_payment.php" },
  { label: "Confirmation", href: null }
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
