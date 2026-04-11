<?php
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/core/CitiServeData.php';

// Make sure the user is logged in
$user = require_login();

$data = new CitiServeData();
$services = $data->getAllActiveDocumentServices();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Document Services</title>
</head>
<body>
    <h2>Available Document Services</h2>
    <p>Logged in as: <?= htmlspecialchars($user['full_name']) ?> (<?= htmlspecialchars($user['role']) ?>)</p>

    <p>
        <a href="/CitiServe/public/index.php">Home</a> |
        <a href="/CitiServe/public/my_requests.php">My Requests</a> |
        <a href="/CitiServe/public/logout.php">Logout</a>
    </p>

    <?php if (empty($services)): ?>
        <p style="color:red;">No active document services available.</p>
        <p>Ask admin to seed/insert rows in <code>document_services</code> with <code>is_active = 1</code>.</p>
    <?php else: ?>
        <table border="1" cellpadding="6" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Service</th>
                <th>Description</th>
                <th>Price</th>
                <th>Processing Days</th>
                <th>Action</th>
            </tr>
            <?php foreach ($services as $s): ?>
                <tr>
                    <td><?= (int)$s['id'] ?></td>
                    <td><?= htmlspecialchars($s['name']) ?></td>
                    <td><?php
                        // Show description or empty string if not set
                        if (isset($s['description'])) {
                            echo htmlspecialchars($s['description']);
                        } else {
                            echo '';
                        }
                    ?></td>
                    <td><?= htmlspecialchars($s['price']) ?></td>
                    <td><?= htmlspecialchars($s['processing_time_days']) ?></td>
                    <td>
                        <a href="/CitiServe/public/request_create.php?service_id=<?= (int)$s['id'] ?>">
                            Request
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
