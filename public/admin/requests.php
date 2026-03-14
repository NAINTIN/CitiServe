<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../../app/helpers/auth.php';
require_once __DIR__ . '/../../app/helpers/csrf.php';
require_once __DIR__ . '/../../app/repositories/DocumentRequestRepository.php';
require_once __DIR__ . '/../../app/core/Database.php';

$admin = require_admin();
$requestRepo = new DocumentRequestRepository();
$db = Database::getInstance()->getConnection();

$allowedStatuses = ['received', 'pending', 'claimable', 'rejected', 'released'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify_or_die();

    $requestId = (int)($_POST['request_id'] ?? 0);
    $newStatus = trim($_POST['new_status'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if ($requestId <= 0 || !in_array($newStatus, $allowedStatuses, true)) {
        $error = 'Invalid request.';
    } else {
        $current = $requestRepo->findById($requestId);

        if (!$current) {
            $error = 'Request not found.';
        } elseif ($current['status'] === $newStatus) {
            $error = 'Status is already set to that value.';
        } else {
            $db->beginTransaction();
            try {
                $requestRepo->updateStatus($requestId, $newStatus);

                $stmt = $db->prepare("
                    INSERT INTO status_history (entity_type, entity_id, old_status, new_status, changed_by, notes)
                    VALUES ('document_request', ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $requestId,
                    $current['status'],
                    $newStatus,
                    (int)$admin['id'],
                    $notes !== '' ? $notes : null
                ]);

                $db->commit();
                $message = "Request #{$requestId} updated to '{$newStatus}'.";
            } catch (Throwable $e) {
                if ($db->inTransaction()) $db->rollBack();
                $error = 'Failed to update request.';
            }
        }
    }
}

$rows = $requestRepo->getAllWithUserAndService();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin - Document Requests</title>
</head>
<body>
    <h2>Admin / Staff - Document Requests</h2>
    <p>
        Logged in as <?= htmlspecialchars($admin['full_name']) ?> (<?= htmlspecialchars($admin['role']) ?>)
        | <a href="/CitiServe/public/index.php">Home</a>
        | <a href="/CitiServe/public/logout.php">Logout</a>
    </p>

    <?php if ($message): ?><p style="color: green;"><?= htmlspecialchars($message) ?></p><?php endif; ?>
    <?php if ($error): ?><p style="color: red;"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <?php if (empty($rows)): ?>
        <p>No requests found.</p>
    <?php else: ?>
        <table border="1" cellpadding="6" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Resident</th>
                <th>Service</th>
                <th>Notes</th>
                <th>Payment Proof</th>
                <th>Status</th>
                <th>Created</th>
                <th>Update</th>
            </tr>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= (int)$r['id'] ?></td>
                    <td>
                        <?= htmlspecialchars($r['full_name'] ?? 'N/A') ?><br>
                        <small><?= htmlspecialchars($r['email'] ?? '') ?></small>
                    </td>
                    <td><?= htmlspecialchars($r['service_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($r['notes'] ?? '') ?></td>
                    <td>
                        <?php if (!empty($r['payment_proof_path'])): ?>
                            <a href="/CitiServe/public/<?= htmlspecialchars($r['payment_proof_path']) ?>" target="_blank">View</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><strong><?= htmlspecialchars($r['status']) ?></strong></td>
                    <td><?= htmlspecialchars($r['created_at']) ?></td>
                    <td>
                        <form method="post" style="margin:0;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="request_id" value="<?= (int)$r['id'] ?>">
                            <select name="new_status">
                                <?php foreach ($allowedStatuses as $st): ?>
                                    <option value="<?= htmlspecialchars($st) ?>" <?= $st === $r['status'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($st) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <br>
                            <input type="text" name="notes" placeholder="notes (optional)">
                            <br>
                            <button type="submit">Save</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>