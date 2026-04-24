<?php
// Don't show errors on the page (for security), but still log them
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Include all the files we need
require_once __DIR__ . '/../../app/helpers/auth.php';
require_once __DIR__ . '/../../app/helpers/csrf.php';
require_once __DIR__ . '/../../app/core/CitiServeData.php';

// Make sure the user is an admin or staff
$admin = require_admin();

$data = new CitiServeData();
$db = $data->getPdo();

// These are all the valid complaint statuses
$allowedStatuses = ['submitted', 'under_review', 'in_progress', 'resolved', 'rejected'];

// Variables for success and error messages
$message = '';
$error = '';

// Check if the form was submitted (admin updating a complaint status)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify the CSRF token
    csrf_verify_or_die();

    // Get the complaint ID and new status from the form
    $complaintId = 0;
    if (isset($_POST['complaint_id'])) {
        $complaintId = (int)$_POST['complaint_id'];
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
    if ($complaintId <= 0 || !in_array($newStatus, $allowedStatuses, true)) {
        $error = 'Invalid complaint status update request.';
    } else {
        // Find the current complaint
        $current = $data->findComplaintById($complaintId);

        if (!$current) {
            $error = 'Complaint not found.';
        } elseif ($current['status'] === $newStatus) {
            $error = 'Status is already set to that value.';
        } else {
            // Use a transaction so both updates happen together (or neither)
            $db->beginTransaction();
            try {
                // Update the complaint status
                $data->updateComplaintStatus($complaintId, $newStatus);

                // Save the status change in the history table
                $sql = "
                    INSERT INTO status_history (entity_type, entity_id, old_status, new_status, changed_by, notes)
                    VALUES ('complaint', ?, ?, ?, ?, ?)
                ";
                $stmt = $db->prepare($sql);

                // Set notes to null if empty
                $notesForDb = null;
                if ($notes !== '') {
                    $notesForDb = $notes;
                }

                $stmt->execute([
                    $complaintId,
                    $current['status'],
                    $newStatus,
                    (int)$admin['id'],
                    $notesForDb
                ]);

                if (!empty($current['user_id'])) {
                    $data->createNotification(
                        (int)$current['user_id'],
                        'Complaint Update',
                        "Your complaint #{$complaintId} status is now '{$newStatus}'.",
                        '/CitiServe/public/my_complaints.php'
                    );
                }

                // If everything worked, commit the transaction
                $db->commit();
                $message = "Complaint #{$complaintId} updated to '{$newStatus}'.";
            } catch (Throwable $e) {
                // If something went wrong, undo everything
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                $error = 'Failed to update complaint.';
            }
        }
    }
}

// Get all complaints to display in the table
$rows = $data->getAllComplaints();
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
                <?php
                    // Try to get the evidence files for this complaint
                    try {
                        $evidence = $data->getEvidenceByComplaintId((int)$r['id']);
                    } catch (Throwable $e) {
                        $evidence = [];
                    }
                ?>
                <tr>
                    <td><?= (int)$r['id'] ?></td>
                    <td>
                        <?php if ((int)$r['is_anonymous'] === 1): ?>
                            <em>Anonymous</em>
                        <?php else: ?>
                            <?php
                            // Show the user's name and email
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
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($r['category_name']) ?></td>
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <td><?= htmlspecialchars($r['description']) ?></td>
                    <td><?php
                        if (isset($r['location'])) {
                            echo htmlspecialchars($r['location']);
                        } else {
                            echo '';
                        }
                    ?></td>
                    <td><?php
                        if ((int)$r['is_anonymous'] === 1) {
                            echo 'Yes';
                        } else {
                            echo 'No';
                        }
                    ?></td>
                    <td><strong><?= htmlspecialchars($r['status']) ?></strong></td>
                    <td>
                        <?php if (empty($evidence)): ?>
                            -
                        <?php else: ?>
                            <?php foreach ($evidence as $ev): ?>
                                <a href="/CitiServe/public/<?= htmlspecialchars($ev['file_path']) ?>" target="_blank">
                                    <?php
                                    // Show the file name, or "evidence" if no name
                                    if ($ev['file_name']) {
                                        echo htmlspecialchars($ev['file_name']);
                                    } else {
                                        echo 'evidence';
                                    }
                                    ?>
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
