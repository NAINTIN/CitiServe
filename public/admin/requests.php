<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../storage/php-error.log');
error_reporting(E_ALL);

require_once __DIR__ . '/../../app/helpers/auth.php';
require_once __DIR__ . '/../../app/helpers/csrf.php';
require_once __DIR__ . '/../../app/core/CitiServeData.php';
require_once __DIR__ . '/../../app/helpers/document_request.php';

$admin = require_admin();
$data = new CitiServeData();
$db = $data->getPdo();

$allowedStatuses = ['claimable', 'rejected'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify_or_die();

    $requestId = isset($_POST['request_id']) ? (int)$_POST['request_id'] : 0;
    $newStatus = isset($_POST['new_status']) ? trim((string)$_POST['new_status']) : '';
    $notes = isset($_POST['notes']) ? trim((string)$_POST['notes']) : '';

    if ($requestId <= 0 || !in_array($newStatus, $allowedStatuses, true)) {
        $error = 'Invalid request.';
    } else {
        $current = $data->findDocumentRequestById($requestId);

        if (!$current) {
            $error = 'Request not found.';
        } elseif ($current['status'] === $newStatus) {
            $error = 'Status is already set to that value.';
        } else {
            $db->beginTransaction();
            try {
                $data->updateDocumentRequestStatus($requestId, $newStatus);

                $stmt = $db->prepare(
                    "INSERT INTO status_history (entity_type, entity_id, old_status, new_status, changed_by, notes)
                     VALUES ('document_request', ?, ?, ?, ?, ?)"
                );
                $stmt->execute([
                    $requestId,
                    $current['status'],
                    $newStatus,
                    (int)$admin['id'],
                    $notes !== '' ? $notes : null,
                ]);

                if (!empty($current['user_id'])) {
                    $data->createNotification(
                        (int)$current['user_id'],
                        'Document Request Update',
                        "Your request #{$requestId} status is now '{$newStatus}'.",
                        '/CitiServe/public/my_requests.php'
                    );
                }

                $db->commit();
                $message = "Request #{$requestId} updated to '{$newStatus}'.";
            } catch (Throwable $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                error_log('ADMIN REQUEST UPDATE ERROR: ' . $e->getMessage());
                $error = 'Failed to update request.';
            }
        }
    }
}

$rows = $data->getAllDocumentRequestsWithUsers();

function h($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
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
        Logged in as <?= h($admin['full_name']) ?> (<?= h($admin['role']) ?>)
        | <a href="/CitiServe/public/index.php">Home</a>
        | <a href="/CitiServe/public/logout.php">Logout</a>
    </p>

    <?php if ($message): ?><p style="color: green;"><?= h($message) ?></p><?php endif; ?>
    <?php if ($error): ?><p style="color: red;"><?= h($error) ?></p><?php endif; ?>

    <?php if (empty($rows)): ?>
        <p>No requests found.</p>
    <?php else: ?>
        <table border="1" cellpadding="6" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Resident</th>
                <th>Document</th>
                <th>Details</th>
                <th>Files</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Created</th>
                <th>Update</th>
            </tr>
            <?php foreach ($rows as $r): ?>
                <?php
                $details = [];
                if (!empty($r['form_data_json'])) {
                    $decoded = json_decode((string)$r['form_data_json'], true);
                    if (is_array($decoded)) {
                        $details = $decoded;
                    }
                }
                $files = $data->getDocumentRequestFilesByRequestId((int)$r['id']);
                ?>
                <tr>
                    <td>#<?= (int)$r['id'] ?></td>
                    <td>
                        <?= h($r['full_name'] ?? 'N/A') ?><br>
                        <small><?= h($r['email'] ?? '') ?></small>
                    </td>
                    <td><?= h($r['service_name'] ?? '-') ?><br><small>₱<?= h($r['fee'] ?? '0.00') ?></small></td>
                    <td>
                        <?php if ($details): ?>
                            <ul style="margin:0; padding-left:16px;">
                                <?php foreach ($details as $k => $v): ?>
                                    <?php if ($v === ''): continue; endif; ?>
                                    <li><strong><?= h(document_request_readable_field_name((string)$k)) ?>:</strong> <?= nl2br(h((string)$v)) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <?= h($r['purpose'] ?? '-') ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($files)): ?>
                            <ul style="margin:0; padding-left:16px;">
                                <?php foreach ($files as $f): ?>
                                    <li>
                                        <?= h(document_request_readable_field_name((string)$f['file_type'])) ?>:
                                        <a href="/CitiServe/public/<?= h((string)$f['file_path']) ?>" target="_blank">View</a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td>
                        Method: <?= h($r['payment_method'] ?? '-') ?><br>
                        Ref: <?= h($r['payment_reference'] ?? '-') ?><br>
                        Proof:
                        <?php if (!empty($r['payment_proof_path'])): ?>
                            <a href="/CitiServe/public/<?= h((string)$r['payment_proof_path']) ?>" target="_blank">View</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><strong><?= h($r['status']) ?></strong></td>
                    <td><?= h($r['created_at']) ?></td>
                    <td>
                        <form method="post" style="margin:0;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="request_id" value="<?= (int)$r['id'] ?>">
                            <select name="new_status" required>
                                <option value="">-- Set Status --</option>
                                <?php foreach ($allowedStatuses as $st): ?>
                                    <option value="<?= h($st) ?>"><?= h($st) ?></option>
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
