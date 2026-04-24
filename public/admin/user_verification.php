<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../storage/php-error.log');
error_reporting(E_ALL);

require_once __DIR__ . '/../../app/helpers/auth.php';
require_once __DIR__ . '/../../app/helpers/csrf.php';
require_once __DIR__ . '/../../app/core/CitiServeData.php';

$admin = require_admin();
$data = new CitiServeData();
$db = $data->getPdo();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify_or_die();

    $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $action = isset($_POST['action']) ? trim((string)$_POST['action']) : '';

    if ($userId <= 0 || !in_array($action, ['verify', 'reject'], true)) {
        $error = 'Invalid verification request.';
    } else {
        try {
            $db->beginTransaction();

            $target = $data->findUserById($userId);
            if (!$target) {
                throw new Exception('User not found.');
            }

            if ((string)$target->role !== 'resident') {
                throw new Exception('Only resident accounts can be verified.');
            }

            if ($action === 'verify') {
                $data->updateUserVerificationStatus($userId, 1);
                $data->createNotification(
                    $userId,
                    'Account Verification Approved',
                    'Your account is now fully verified. You can now request documents and submit complaints.',
                    '/CitiServe/public/dashboard.php'
                );
                $message = 'User verification approved.';
            } else {
                $data->updateUserVerificationStatus($userId, 0);
                $data->createNotification(
                    $userId,
                    'Account Verification Rejected',
                    'Your proof of ID was rejected. Please re-upload a clear valid ID for admin review.',
                    '/CitiServe/public/profile_edit.php#proof-of-id'
                );
                $message = 'User verification rejected.';
            }

            $db->commit();
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $error = 'Failed to update verification status.';
        }
    }
}

$users = $data->getAllUsersWithVerification();

function h($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin - User Verification</title>
</head>
<body>
    <h2>Admin / Staff - User Verification</h2>
    <p>
        Logged in as <?= h($admin['full_name']) ?> (<?= h($admin['role']) ?>)
        | <a href="/CitiServe/public/admin/users.php">Manage Roles</a>
        | <a href="/CitiServe/public/index.php">Home</a>
        | <a href="/CitiServe/public/logout.php">Logout</a>
    </p>

    <?php if ($message): ?><p style="color: green;"><?= h($message) ?></p><?php endif; ?>
    <?php if ($error): ?><p style="color: red;"><?= h($error) ?></p><?php endif; ?>

    <?php if (empty($users)): ?>
        <p>No users found.</p>
    <?php else: ?>
        <table border="1" cellpadding="6" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Registered</th>
                <th>Status</th>
                <th>Proof of ID</th>
                <th>Action</th>
            </tr>
            <?php foreach ($users as $u): ?>
                <?php
                $isResident = ((string)$u['role'] === 'resident');
                $isVerified = ((int)$u['is_verified'] === 1);
                $proofPath = isset($u['proof_of_id']) ? (string)$u['proof_of_id'] : '';
                $isImage = preg_match('/\.(jpg|jpeg|png)$/i', $proofPath) === 1;
                ?>
                <tr>
                    <td><?= (int)$u['id'] ?></td>
                    <td><?= h($u['full_name']) ?></td>
                    <td><?= h($u['email']) ?></td>
                    <td><?= h($u['role']) ?></td>
                    <td><?= h($u['created_at']) ?></td>
                    <td>
                        <?php if ($isVerified): ?>
                            <span style="color:green; font-weight:700;">Verified</span>
                        <?php else: ?>
                            <span style="color:#d97706; font-weight:700;">Unverified</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($proofPath !== ''): ?>
                            <?php if ($isImage): ?>
                                <a href="/CitiServe/public/<?= h($proofPath) ?>" target="_blank">
                                    <img src="/CitiServe/public/<?= h($proofPath) ?>" alt="Proof of ID" style="max-width:120px; max-height:80px; border:1px solid #ddd;">
                                </a><br>
                            <?php endif; ?>
                            <a href="/CitiServe/public/<?= h($proofPath) ?>" target="_blank">View File</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($isResident && !$isVerified): ?>
                            <form method="post" style="margin:0 0 6px 0;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                                <button type="submit" name="action" value="verify">Verify</button>
                                <button type="submit" name="action" value="reject">Reject</button>
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
