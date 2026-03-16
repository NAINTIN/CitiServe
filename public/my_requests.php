<?php
// Include the auth helper and DocumentRequestRepository
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/repositories/DocumentRequestRepository.php';

// Make sure the user is logged in
$user = require_login();

// Get all document requests for this user
$requestRepo = new DocumentRequestRepository();
$requests = $requestRepo->getByUserId((int)$user['id']);
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
        <a href="/CitiServe/public/services.php">Services</a> |
        <a href="/CitiServe/public/index.php">Home</a> |
        <a href="/CitiServe/public/logout.php">Logout</a>
    </p>

    <?php if (empty($requests)): ?>
        <p>No requests yet.</p>
    <?php else: ?>
        <table border="1" cellpadding="6" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Service</th>
                <th>Status</th>
                <th>Purpose</th>
                <th>Payment Ref</th>
                <th>Proof</th>
                <th>Created</th>
            </tr>
            <?php foreach ($requests as $r): ?>
                <tr>
                    <td><?= (int)$r['id'] ?></td>
                    <td><?= htmlspecialchars($r['service_name']) ?></td>
                    <td><?= htmlspecialchars($r['status']) ?></td>
                    <td><?php
                        if (isset($r['purpose'])) {
                            echo htmlspecialchars($r['purpose']);
                        } else {
                            echo '';
                        }
                    ?></td>
                    <td><?php
                        if (isset($r['payment_reference'])) {
                            echo htmlspecialchars($r['payment_reference']);
                        } else {
                            echo '';
                        }
                    ?></td>
                    <td>
                        <?php if (!empty($r['payment_proof_path'])): ?>
                            <a href="/CitiServe/public/<?= htmlspecialchars($r['payment_proof_path']) ?>" target="_blank">View</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($r['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>