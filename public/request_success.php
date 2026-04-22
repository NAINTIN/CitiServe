<?php
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/core/CitiServeData.php';

$user = require_resident();
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
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Request Submitted</title>
</head>
<body>
    <h2>Request Submitted Successfully</h2>
    <p>Your document request was submitted.</p>

    <table border="1" cellpadding="6" cellspacing="0">
        <tr><th>Request ID</th><td>#<?= (int)$success['request_id'] ?></td></tr>
        <tr><th>Document Type</th><td><?= h($success['service_name']) ?></td></tr>
        <tr><th>Amount</th><td>₱<?= h($success['fee']) ?></td></tr>
        <tr><th>Date/Time</th><td><?= h($success['submitted_at']) ?></td></tr>
        <tr><th>Payment Method</th><td><?= h($success['payment_method']) ?></td></tr>
        <tr><th>Status</th><td><?= h($success['status']) ?></td></tr>
    </table>

    <p>
        <a href="/CitiServe/public/dashboard.php">Go to Dashboard</a> |
        <a href="/CitiServe/public/my_requests.php">Go to My Requests</a> |
        <a href="/CitiServe/public/request_select.php">Create Another Request</a>
    </p>
</body>
</html>
