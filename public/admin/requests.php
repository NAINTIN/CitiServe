<?php
// Don't show errors on the page, but log them to a file
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../storage/php-error.log');
error_reporting(E_ALL);

// Include all the files we need
require_once __DIR__ . '/../../app/helpers/auth.php';
require_once __DIR__ . '/../../app/helpers/csrf.php';
require_once __DIR__ . '/../../app/core/CitiServeData.php';

// Make sure the user is an admin or staff
$admin = require_admin();

$data = new CitiServeData();
$db = $data->getPdo();

// These are all the valid request statuses
$allowedStatuses = ['received', 'pending', 'claimable', 'rejected', 'released'];

// Variables for success and error messages
$message = '';
$error = '';

// Check if the form was submitted (admin updating a request status)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify the CSRF token
    csrf_verify_or_die();

    // Get the request ID and new status from the form
    $requestId = 0;
    if (isset($_POST['request_id'])) {
        $requestId = (int)$_POST['request_id'];
    }

    $newStatus = '';
    if (isset($_POST['new_status'])) {
        $newStatus = trim($_POST['new_status']);
    }

    $notes = '';
    if (isset($_POST['notes'])) {
        $notes = trim($_POST['notes']);
    }

    // Validate the input
    if ($requestId <= 0 || !in_array($newStatus, $allowedStatuses, true)) {
        $error = 'Invalid request.';
    } else {
        // Find the current request
        $current = $data->findDocumentRequestById($requestId);

        if (!$current) {
            $error = 'Request not found.';
        } elseif ($current['status'] === $newStatus) {
            $error = 'Status is already set to that value.';
        } else {
            // Use a transaction so all updates happen together
            $db->beginTransaction();
            try {
                // 1) Update the request status
                $data->updateDocumentRequestStatus($requestId, $newStatus);

                // 2) Save the status change in the history table
                $sql = "
                    INSERT INTO status_history (entity_type, entity_id, old_status, new_status, changed_by, notes)
                    VALUES ('document_request', ?, ?, ?, ?, ?)
                ";
                $stmt = $db->prepare($sql);

                // Set notes to null if empty
                $notesForDb = null;
                if ($notes !== '') {
                    $notesForDb = $notes;
                }

                $stmt->execute([
                    $requestId,
                    $current['status'],
                    $newStatus,
                    (int)$admin['id'],
                    $notesForDb
                ]);

                // 3) Send a notification to the resident
                if (!empty($current['user_id'])) {
                    $data->createNotification(
                        (int)$current['user_id'],
                        'Document Request Update',
                        "Your request #{$requestId} status is now '{$newStatus}'.",
                        '/CitiServe/public/my_requests.php'
                    );
                }

                // If everything worked, commit the transaction
                $db->commit();
                $message = "Request #{$requestId} updated to '{$newStatus}'.";
            } catch (Throwable $e) {
                // If something went wrong, undo everything
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                // Log the error for debugging
                error_log('ADMIN REQUEST UPDATE ERROR: ' . $e->getMessage());
                error_log($e->getTraceAsString());
                $error = 'Failed to update request.';
            }
        }
    }
}

// Get all document requests to display in the table
$rows = $data->getAllDocumentRequestsWithUsers();
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
                        <?php
                        // Show the resident's name
                        if (isset($r['full_name'])) {
                            echo htmlspecialchars($r['full_name']);
                        } else {
                            echo 'N/A';
                        }
                        ?>
                        <br>
                        <small><?php
                            if (isset($r['email'])) {
                                echo htmlspecialchars($r['email']);
                            } else {
                                echo '';
                            }
                        ?></small>
                    </td>
                    <td><?php
                        if (isset($r['service_name'])) {
                            echo htmlspecialchars($r['service_name']);
                        } else {
                            echo '-';
                        }
                    ?></td>
                    <td><?php
                        if (isset($r['purpose'])) {
                            echo htmlspecialchars($r['purpose']);
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
                    <td><strong><?= htmlspecialchars($r['status']) ?></strong></td>
                    <td><?= htmlspecialchars($r['created_at']) ?></td>
                    <td>
                        <form method="post" style="margin:0;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="request_id" value="<?= (int)$r['id'] ?>">
                            <select name="new_status">
                                <?php foreach ($allowedStatuses as $st): ?>
                                    <?php
                                    $isSelected = '';
                                    if ($st === $r['status']) {
                                        $isSelected = 'selected';
                                    }
                                    ?>
                                    <option value="<?= htmlspecialchars($st) ?>" <?= $isSelected ?>>
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
