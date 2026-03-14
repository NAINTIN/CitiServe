<?php
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/repositories/ComplaintRepository.php';

$user = require_login();
$repo = new ComplaintRepository();
$rows = $repo->getByUserId((int)$user['id']);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Complaints</title>
</head>
<body>
    <h2>My Complaints</h2>
    <p>
        <a href="/CitiServe/public/complaint_create.php">Submit Complaint</a> |
        <a href="/CitiServe/public/index.php">Home</a> |
        <a href="/CitiServe/public/logout.php">Logout</a>
    </p>

    <?php if (empty($rows)): ?>
        <p>No complaints submitted yet.</p>
    <?php else: ?>
        <table border="1" cellpadding="6" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Category</th>
                <th>Title</th>
                <th>Description</th>
                <th>Location</th>
                <th>Anonymous</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= (int)$r['id'] ?></td>
                    <td><?= htmlspecialchars($r['category_name']) ?></td>
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <td><?= htmlspecialchars($r['description']) ?></td>
                    <td><?= htmlspecialchars($r['location'] ?? '') ?></td>
                    <td><?= ((int)$r['is_anonymous'] === 1) ? 'Yes' : 'No' ?></td>
                    <td><strong><?= htmlspecialchars($r['status']) ?></strong></td>
                    <td><?= htmlspecialchars($r['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>