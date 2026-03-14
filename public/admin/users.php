<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../../app/helpers/auth.php';
require_once __DIR__ . '/../../app/helpers/csrf.php';
require_once __DIR__ . '/../../app/core/Database.php';

$admin = require_admin();
$db = Database::getInstance()->getConnection();

$allowedRoles = ['resident', 'staff', 'admin'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify_or_die();

    $userId = (int)($_POST['user_id'] ?? 0);
    $newRole = trim($_POST['new_role'] ?? '');

    if ($userId <= 0 || !in_array($newRole, $allowedRoles, true)) {
        $error = 'Invalid role update.';
    } else {
        if ($userId === (int)$admin['id'] && $newRole !== 'admin') {
            $error = 'You cannot remove your own admin role.';
        } else {
            try {
                $db->beginTransaction();

                $stmtOld = $db->prepare("SELECT id, role, full_name, email FROM users WHERE id = ? LIMIT 1");
                $stmtOld->execute([$userId]);
                $target = $stmtOld->fetch();

                if (!$target) {
                    throw new Exception('User not found.');
                }

                $oldRole = $target['role'];

                if ($oldRole === $newRole) {
                    $message = "No changes made. User already has role '{$newRole}'.";
                } else {
                    $stmt = $db->prepare("UPDATE users SET role = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$newRole, $userId]);

                    // Optional audit log (if table exists)
                    // $stmtAudit = $db->prepare("
                    //     INSERT INTO audit_logs (user_id, action, table_name, record_id, old_data, new_data)
                    //     VALUES (?, 'update_role', 'users', ?, ?, ?)
                    // ");
                    // $stmtAudit->execute([
                    //     (int)$admin['id'],
                    //     (string)$userId,
                    //     json_encode(['role' => $oldRole], JSON_UNESCAPED_UNICODE),
                    //     json_encode(['role' => $newRole], JSON_UNESCAPED_UNICODE),
                    // ]);

                    $message = "Updated role for {$target['full_name']} ({$target['email']}) from '{$oldRole}' to '{$newRole}'.";
                }

                $db->commit();
            } catch (Throwable $e) {
                if ($db->inTransaction()) $db->rollBack();
                $error = 'Failed to update role.';
            }
        }
    }
}

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
                                    <option value="<?= htmlspecialchars($role) ?>" <?= $role === $u['role'] ? 'selected' : '' ?>>
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