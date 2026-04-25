<?php
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/core/CitiServeData.php';
require_once __DIR__ . '/../app/helpers/resident_navbar.php';

$user = require_verified_resident('document request pages');
$navCtx = build_resident_navbar_context((int)$user['id']);
$data = new CitiServeData();

$requestId = isset($_GET['request_id']) ? (int)$_GET['request_id'] : 0;
$success = isset($_SESSION['document_request_success']) ? $_SESSION['document_request_success'] : null;

if ($requestId > 0) {
    $row = $data->getDocumentRequestByIdWithService($requestId);
    if (!$row || (int)$row['user_id'] !== (int)$user['id']) {
        header('Location: /CitiServe/public/my_requests.php');
        exit;
    }

    if (!$success || (int)$success['request_id'] !== $requestId) {
        $success = [
            'request_id' => (int)$row['id'],
            'service_name' => (string)$row['service_name'],
            'fee' => (string)$row['fee'],
            'payment_method' => (string)$row['payment_method'],
            'status' => (string)$row['status'],
            'submitted_at' => (string)$row['created_at'],
        ];
    }
}

if (!$success) {
    header('Location: /CitiServe/public/request_select.php');
    exit;
}

unset($_SESSION['document_request_success']);

function h($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

$submittedTs = strtotime((string)$success['submitted_at']);
$dateLabel = $submittedTs ? date('F j, Y', $submittedTs) : (string)$success['submitted_at'];
$timeLabel = $submittedTs ? date('g:i A', $submittedTs) : '-';
$refLabel = 'DOC-' . str_pad((string)((int)$success['request_id']), 10, '0', STR_PAD_LEFT);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Submitted</title>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/CitiServe/frontend/dashboard/CSS/dashboard.css">
    <link rel="stylesheet" href="/CitiServe/frontend/document_request/css/request_submitted.css">
</head>
<body>
<?php render_resident_navbar($navCtx, 'document'); ?>
<div class="content-area">
    <div class="ticket-wrapper">
        <img src="/CitiServe/frontend/document_request/images/request-receipt.png" alt="" class="ticket-bg" />

        <div class="ticket-content">
            <div class="ticket-header">
                <img src="/CitiServe/frontend/document_request/images/request-icon-success.png" alt="Success" class="success-icon" />
                <div class="ticket-title">Request Submitted</div>
                <div class="ticket-subtitle">Your document request has been received.</div>
            </div>

            <div class="divider-dashed-wrap"></div>

            <div class="ref-box">
                <div class="ref-label">Request Reference Number</div>
                <div class="ref-number"><?= h($refLabel) ?></div>
                <div class="ref-hint">Save this number for tracking your request.</div>
            </div>

            <div class="detail-row"><span class="detail-label">Document Type</span><span class="detail-value"><?= h($success['service_name']) ?></span></div>
            <div class="detail-row"><span class="detail-label">Amount</span><span class="detail-value amount">₱<?= h($success['fee']) ?></span></div>
            <div class="detail-row"><span class="detail-label">Date</span><span class="detail-value"><?= h($dateLabel) ?></span></div>
            <div class="detail-row"><span class="detail-label">Time</span><span class="detail-value"><?= h($timeLabel) ?></span></div>
            <div class="detail-row"><span class="detail-label">Payment Method</span><span class="detail-value"><?= h($success['payment_method']) ?></span></div>
            <div class="detail-row"><span class="detail-label">Status</span><span class="detail-value"><img src="/CitiServe/frontend/document_request/images/request-reviewed.png" alt="Status" style="height:20px;object-fit:contain;"></span></div>

            <div class="divider-solid"></div>

            <div class="next-title">What Happens Next</div>
            <div class="next-item"><img src="/CitiServe/frontend/document_request/images/request-eye-icon.png" alt="" class="next-icon" /><div class="next-text">Barangay staff will review your uploaded documents and requirements.</div></div>
            <div class="next-item"><img src="/CitiServe/frontend/document_request/images/request-wallet.png" alt="" class="next-icon" /><div class="next-text">Your payment will be verified manually by the cashier.</div></div>
            <div class="next-item"><img src="/CitiServe/frontend/document_request/images/request-bell-icon.png" alt="" class="next-icon" /><div class="next-text">You will be notified when your document is ready for pickup.</div></div>
            <div class="next-item"><img src="/CitiServe/frontend/document_request/images/request-claim.png" alt="" class="next-icon" /><div class="next-text">Claim your document in person at the Barangay Hall with a valid ID.</div></div>

            <img src="/CitiServe/frontend/document_request/images/request-reminder.png" alt="Reminder" class="reminder-img" />
            <div class="divider-solid1"></div>

            <div class="btn-group">
                <a class="btn-img" href="/CitiServe/public/my_requests.php"><img src="/CitiServe/frontend/document_request/images/request-view.png" alt="View My Requests" /></a>
                <a class="btn-img" href="/CitiServe/public/dashboard.php"><img src="/CitiServe/frontend/document_request/images/request-back-to-dashboard.png" alt="Back to Dashboard" /></a>
            </div>
        </div>

        <div class="form-logo">
            <img src="/CitiServe/frontend/document_request/images/docu-logo.png" alt="CitiServe">
            <div class="form-logo-text">
                <span class="logo-pink">CitiServe</span>
                <span class="logo-gray"> © 2026. All rights reserved.</span>
            </div>
        </div>

        <img src="/CitiServe/frontend/document_request/images/request-faded-logo.png" class="faded-logo" alt="">
    </div>
</div>
<script src="/CitiServe/frontend/dashboard/dashboard.js"></script>
</body>
</html>
