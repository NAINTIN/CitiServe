<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../../app/helpers/auth.php';
require_once __DIR__ . '/../../app/helpers/csrf.php';
require_once __DIR__ . '/../../app/repositories/ComplaintRepository.php';
require_once __DIR__ . '/../../app/core/Database.php';

$admin = require_admin();
$repo = new ComplaintRepository();
$db = Database::getInstance()->getConnection();

$allowedStatuses = ['submitted', 'under_review', 'in_progress', 'resolved', 'rejected'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify_or_die();

    $complaintId = (int)($_POST['complaint_id'] ?? 0);
    $newStatus = trim($_POST['new_status'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if ($complaintId <= 0 || !in_array($newStatus, $allowedStatuses, true)) {
        $error = 'Invalid complaint status update request.';
    } else {
        $current = $repo->findById($complaintId);

        if (!$current) {
            $error = 'Complaint not found.';
        } elseif ($current['status'] === $newStatus) {
            $error = 'Status is already set to that value.';
        } else {
            $db->beginTransaction();
            try {
                $repo->updateStatus($complaintId, $newStatus);

                $stmt = $db->prepare("
                    INSERT INTO status_history (entity_type, entity_id, old_status, new_status, changed_by, notes)
                    VALUES ('complaint', ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $complaintId,
                    $current['status'],
                    $newStatus,
                    (int)$admin['id'],
                    $notes !== '' ? $notes : null
                ]);

                $db->commit();
                $message = "Complaint #{$complaintId} updated to '{$newStatus}'.";
            } catch (Throwable $e) {
                if ($db->inTransaction()) $db->rollBack();
                $error = 'Failed to update complaint.';
            }
        }
    }
}

$rows = $repo->getAll();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin - Complaint Management</title>
</head>
<body>
    <h2>Admin / Staff - Complaint Management</h2>
    <p>
        Logged in as <?= htmlspecialchars($admin['full_name']) ?> (<?= htmlspecialchars($admin['role']) ?>)
        | <a href="/CitiServe/public/index.php">Home</a>
        | <a href="/CitiServe/public/logout.php">Logout</a>
    </p>

    <?php if ($message): ?><p style="color: green;"><?= htmlspecialchars($message) ?></p><?php endif; ?>
    <?php if ($error): ?><p style="color: red;"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <?php if (empty($rows)): ?>
        <p>No complaints found.</p>
    <?php else: ?>
        <table border="1" cellpadding="6" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Complainant</th>
                <th>Category</th>
                <th>Title</th>
                <th>Description</th>
                <th>Location</th>
                <th>Anonymous</th>
                <th>Status</th>
                <th>Evidence</th>
                <th>Created</th>
                <th>Update</th>
            </tr>
            <?php foreach ($rows as $r): ?>
                <?php $evidence = $repo->getEvidenceByComplaintId((int)$r['id']); ?>
                <tr>
                    <td><?= (int)$r['id'] ?></td>
                    <td>
                        <?php if ((int)$r['is_anonymous'] === 1): ?>
                            <em>Anonymous</em>
                        <?php else: ?>
                            <?= htmlspecialchars($r['full_name'] ?? 'N/A') ?><br>
                            <small><?= htmlspecialchars($r['email'] ?? '') ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($r['category_name']) ?></td>
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <td><?= htmlspecialchars($r['description']) ?></td>
                    <td><?= htmlspecialchars($r['location'] ?? '') ?></td>
                    <td><?= ((int)$r['is_anonymous'] === 1) ? 'Yes' : 'No' ?></td>
                    <td><strong><?= htmlspecialchars($r['status']) ?></strong></td>
                    <td>
                        <?php if (empty($evidence)): ?>
                            -
                        <?php else: ?>
                            <?php foreach ($evidence as $ev): ?>
                                <a href="/CitiServe/public/<?= htmlspecialchars($ev['file_path']) ?>" target="_blank">
                                    <?= htmlspecialchars($ev['file_name'] ?: 'evidence') ?>
                                </a><br>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($r['created_at']) ?></td>
                    <td>
                        <form method="post" style="margin:0;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="complaint_id" value="<?= (int)$r['id'] ?>">
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