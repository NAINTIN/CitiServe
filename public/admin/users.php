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

// These are the valid roles a user can have
$allowedRoles = ['resident', 'staff', 'admin'];

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

// Get all users to display in the table
$users = $db->query("
    SELECT id, full_name, email, role, created_at
    FROM users
    ORDER BY created_at DESC
")->fetchAll();
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

    <?php if (empty($users)): ?>
        <p>No users found.</p>
    <?php else: ?>
        <table border="1" cellpadding="6" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Current Role</th>
                <th>Created At</th>
                <th>Change Role</th>
            </tr>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= (int)$u['id'] ?></td>
                    <td><?= htmlspecialchars($u['full_name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><strong><?= htmlspecialchars($u['role']) ?></strong></td>
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
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
