<?php
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/core/CitiServeData.php';

$user = require_login();
$data = new CitiServeData();
$requests = $data->getDocumentRequestsByUserId((int)$user['id']);

$statusLabels = [
    'pending' => 'pending',
    'received' => 'received',
    'claimable' => 'claimable',
    'rejected' => 'rejected',
    'released' => 'released',
];
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Document Requests</title>
</head>
<body>
    <h2>My Document Requests</h2>
    <p>
        <a href="/CitiServe/public/request_select.php">New Request</a> |
        <a href="/CitiServe/public/services.php">Services</a> |
        <a href="/CitiServe/public/index.php">Home</a> |
        <a href="/CitiServe/public/logout.php">Logout</a>
    </p>

    <?php if (empty($requests)): ?>
        <p>No requests yet.</p>
    <?php else: ?>
        <table border="1" cellpadding="6" cellspacing="0">
            <tr>
                <th>Request ID</th>
                <th>Document Type</th>
                <th>Date Submitted</th>
                <th>Fee</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>Payment Ref</th>
                <th>Payment Proof</th>
            </tr>
            <?php foreach ($requests as $r): ?>
                <?php $status = isset($statusLabels[$r['status']]) ? $statusLabels[$r['status']] : $r['status']; ?>
                <tr>
                    <td>#<?= (int)$r['id'] ?></td>
                    <td><?= htmlspecialchars((string)$r['service_name']) ?></td>
                    <td><?= htmlspecialchars((string)$r['created_at']) ?></td>
                    <td>₱<?= htmlspecialchars((string)$r['fee']) ?></td>
                    <td><?= htmlspecialchars((string)($r['payment_method'] ?? '-')) ?></td>
                    <td><strong><?= htmlspecialchars((string)$status) ?></strong></td>
                    <td><?= htmlspecialchars((string)($r['payment_reference'] ?? '-')) ?></td>
                    <td>
                        <?php if (!empty($r['payment_proof_path'])): ?>
                            <a href="/CitiServe/public/<?= htmlspecialchars((string)$r['payment_proof_path']) ?>" target="_blank">View</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
