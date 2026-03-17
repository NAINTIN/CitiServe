<?php
// Don't show errors on the page (for security), but still log them
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Include all the files we need
require_once __DIR__ . '/../../app/helpers/auth.php';
require_once __DIR__ . '/../../app/helpers/csrf.php';
require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/repositories/NotificationRepository.php';

define('RESIDENCY_PROOF_STORAGE_PREFIX', 'storage/residency_proofs/');
define('DEFAULT_IS_VERIFIED', 0);
define('DEFAULT_VERIFICATION_STATUS', 'not_submitted');
define('DEFAULT_RESIDENCY_PROOF_PATH', '');

function users_has_residency_verification_columns($db)
{
    $sql = "
        SELECT COUNT(*) AS cnt
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'users'
          AND COLUMN_NAME IN ('is_verified', 'residency_verification_status', 'residency_proof_path')
    ";
    $row = $db->query($sql)->fetch();
    return isset($row['cnt']) && (int)$row['cnt'] === 3;
}

// Make sure the user is an admin or staff
$admin = require_admin();

// Get the database connection
$db = Database::getInstance()->getConnection();

// These are the valid roles a user can have
$allowedRoles = ['resident', 'staff', 'admin'];
$notifRepo = new NotificationRepository();
$hasResidencyVerificationColumns = users_has_residency_verification_columns($db);

// Variables for success and error messages
$message = '';
$error = '';

// Check if the form was submitted (admin changing a user's role)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify the CSRF token
    csrf_verify_or_die();

    // Get the user ID and new role from the form
    $userId = 0;
    if (isset($_POST['user_id'])) {
        $userId = (int)$_POST['user_id'];
    }

    if (isset($_POST['verification_action'])) {
        $action = trim((string)$_POST['verification_action']);

        if (!$hasResidencyVerificationColumns) {
            $error = 'Residency verification columns are missing in your database. Please import the latest schema.';
        } elseif ($userId <= 0 || ($action !== 'accept' && $action !== 'reject')) {
            $error = 'Invalid verification action.';
        } else {
            try {
                $db->beginTransaction();

                $stmtUser = $db->prepare("
                    SELECT id, full_name, role, is_verified, residency_verification_status
                    FROM users
                    WHERE id = ?
                    LIMIT 1
                ");
                $stmtUser->execute([$userId]);
                $target = $stmtUser->fetch();

                if (!$target || $target['role'] !== 'resident') {
                    throw new Exception('Resident not found.');
                }

                if ($target['residency_verification_status'] !== 'pending') {
                    throw new Exception('Only pending submissions can be reviewed.');
                }

                if ($action === 'accept') {
                    $stmtUpdate = $db->prepare("
                        UPDATE users
                        SET is_verified = 1, residency_verification_status = 'approved', updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmtUpdate->execute([$userId]);

                    $notifRepo->create(
                        $userId,
                        'Residency proof approved',
                        'Your proof of residency was approved. You can now submit complaints.',
                        '/CitiServe/public/complaint_create.php'
                    );

                    $message = "Approved residency proof for {$target['full_name']}.";
                } else {
                    $stmtUpdate = $db->prepare("
                        UPDATE users
                        SET is_verified = 0, residency_verification_status = 'rejected', updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmtUpdate->execute([$userId]);

                    $notifRepo->create(
                        $userId,
                        'Residency proof rejected',
                        'Your proof of residency was rejected. Please submit a clearer/valid proof.',
                        '/CitiServe/public/residency_verification.php'
                    );

                    $message = "Rejected residency proof for {$target['full_name']}.";
                }

                $db->commit();
            } catch (Throwable $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                $error = 'Failed to review residency proof.';
            }
        }
    } else {
        $newRole = '';
        if (isset($_POST['new_role'])) {
            $newRole = trim($_POST['new_role']);
        }

        // Validate the input
        if ($userId <= 0 || !in_array($newRole, $allowedRoles, true)) {
            $error = 'Invalid role update.';
        } else {
            // Don't let admins remove their own admin role
            if ($userId === (int)$admin['id'] && $newRole !== 'admin') {
                $error = 'You cannot remove your own admin role.';
            } else {
                try {
                    // Start a transaction
                    $db->beginTransaction();

                    // Look up the user we want to change
                    $stmtOld = $db->prepare("SELECT id, role, full_name, email FROM users WHERE id = ? LIMIT 1");
                    $stmtOld->execute([$userId]);
                    $target = $stmtOld->fetch();

                    if (!$target) {
                        throw new Exception('User not found.');
                    }

                    $oldRole = $target['role'];

                    // Check if the role is actually changing
                    if ($oldRole === $newRole) {
                        $message = "No changes made. User already has role '{$newRole}'.";
                    } else {
                        // Update the user's role
                        $stmt = $db->prepare("UPDATE users SET role = ?, updated_at = NOW() WHERE id = ?");
                        $stmt->execute([$newRole, $userId]);

                        $message = "Updated role for {$target['full_name']} ({$target['email']}) from '{$oldRole}' to '{$newRole}'.";
                    }

                    // Commit the transaction
                    $db->commit();
                } catch (Throwable $e) {
                    // If something went wrong, undo everything
                    if ($db->inTransaction()) {
                        $db->rollBack();
                    }
                    $error = 'Failed to update role.';
                }
            }
        }
    }
}

// Get all users to display in the table
if ($hasResidencyVerificationColumns) {
    $users = $db->query("
        SELECT id, full_name, email, role, is_verified, residency_verification_status, residency_proof_path, created_at
        FROM users
        ORDER BY created_at DESC
    ")->fetchAll();
} else {
    $stmtUsers = $db->prepare("
        SELECT id, full_name, email, role, ? AS is_verified, ? AS residency_verification_status, ? AS residency_proof_path, created_at
        FROM users
        ORDER BY created_at DESC
    ");
    $stmtUsers->execute([DEFAULT_IS_VERIFIED, DEFAULT_VERIFICATION_STATUS, DEFAULT_RESIDENCY_PROOF_PATH]);
    $users = $stmtUsers->fetchAll();
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin - Manage User Roles</title>
</head>
<body>
    <h2>Admin - Manage User Roles</h2>
    <p>
        Logged in as <?= htmlspecialchars($admin['full_name']) ?> (<?= htmlspecialchars($admin['role']) ?>)
        | <a href="/CitiServe/public/index.php">Home</a>
        | <a href="/CitiServe/public/logout.php">Logout</a>
    </p>

    <?php if ($message): ?>
        <p style="color: green;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if (!$hasResidencyVerificationColumns): ?>
        <p style="color: #b36b00;">
            <strong>Warning:</strong> Residency verification fields are not available in the current database schema. Role management still works.
        </p>
    <?php endif; ?>

    <?php if (empty($users)): ?>
        <p>No users found.</p>
    <?php else: ?>
        <table border="1" cellpadding="6" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Current Role</th>
                <th>Verification</th>
                <th>Proof</th>
                <th>Created At</th>
                <th>Change Role</th>
                <th>Review Proof</th>
            </tr>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= (int)$u['id'] ?></td>
                    <td><?= htmlspecialchars($u['full_name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><strong><?= htmlspecialchars($u['role']) ?></strong></td>
                    <td>
                        <?php if ($u['role'] === 'resident'): ?>
                            <?= ((int)$u['is_verified'] === 1) ? 'Verified' : 'Unverified' ?>
                            (<?= htmlspecialchars($u['residency_verification_status']) ?>)
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $proofPath = isset($u['residency_proof_path']) ? (string)$u['residency_proof_path'] : '';
                        if ($u['role'] === 'resident' && $proofPath !== '' && strpos($proofPath, RESIDENCY_PROOF_STORAGE_PREFIX) === 0):
                            $proofUrl = '/CitiServe/public/admin/view_residency_proof.php?user_id=' . (int)$u['id'];
                        ?>
                            <a href="<?= htmlspecialchars($proofUrl) ?>" target="_blank">View proof</a><br>
                            <img src="<?= htmlspecialchars($proofUrl) ?>" alt="Residency proof for <?= htmlspecialchars($u['full_name']) ?>" style="max-width:120px;max-height:120px;">
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($u['created_at']) ?></td>
                    <td>
                        <form method="post" style="margin:0;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                            <select name="new_role">
                                <?php foreach ($allowedRoles as $role): ?>
                                    <?php
                                    $isSelected = '';
                                    if ($role === $u['role']) {
                                        $isSelected = 'selected';
                                    }
                                    ?>
                                    <option value="<?= htmlspecialchars($role) ?>" <?= $isSelected ?>>
                                        <?= htmlspecialchars($role) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit">Update</button>
                        </form>
                    </td>
                    <td>
                        <?php if ($u['role'] === 'resident' && $u['residency_verification_status'] === 'pending'): ?>
                            <form method="post" style="margin:0 0 6px 0;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                                <input type="hidden" name="verification_action" value="accept">
                                <button type="submit">Accept</button>
                            </form>
                            <form method="post" style="margin:0;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                                <input type="hidden" name="verification_action" value="reject">
                                <button type="submit">Reject</button>
                            </form>
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
