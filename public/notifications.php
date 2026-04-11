<?php
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/core/CitiServeData.php';

// Make sure the user is logged in
$user = require_login();

$data = new CitiServeData();

// Check if the form was submitted (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify the CSRF token
    csrf_verify_or_die();

    // Check if the user clicked "Mark all as read"
    if (isset($_POST['mark_all_read'])) {
        $data->markAllNotificationsAsRead((int)$user['id']);
        header('Location: /CitiServe/public/notifications.php');
        exit;
    }

    // Check if the user clicked "Mark read" on a single notification
    if (isset($_POST['mark_read_id'])) {
        $id = (int)$_POST['mark_read_id'];
        if ($id > 0) {
            $data->markNotificationAsRead($id, (int)$user['id']);
        }
        header('Location: /CitiServe/public/notifications.php');
        exit;
    }
}

// Get all notifications for this user
$rows = $data->getNotificationsByUser((int)$user['id']);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Notifications</title>
</head>
<body>
    <h2>My Notifications</h2>
    <p>
        <a href="/CitiServe/public/index.php">Home</a> |
        <a href="/CitiServe/public/logout.php">Logout</a>
    </p>

    <form method="post" style="margin-bottom: 12px;">
        <?= csrf_field() ?>
        <button type="submit" name="mark_all_read" value="1">Mark all as read</button>
    </form>

    <?php if (empty($rows)): ?>
        <p>No notifications yet.</p>
    <?php else: ?>
        <table border="1" cellpadding="6" cellspacing="0">
            <tr>
                <th>Status</th>
                <th>Title</th>
                <th>Message</th>
                <th>When</th>
                <th>Action</th>
            </tr>
            <?php foreach ($rows as $n): ?>
                <tr>
                    <td>
                        <?php
                        // Show "Read" or "Unread" based on the is_read value
                        if ((int)$n['is_read'] === 1) {
                            echo 'Read';
                        } else {
                            echo 'Unread';
                        }
                        ?>
                    </td>
                    <td><?= htmlspecialchars($n['title']) ?></td>
                    <td>
                        <?= htmlspecialchars($n['message']) ?>
                        <?php if (!empty($n['link'])): ?>
                            <br><a href="<?= htmlspecialchars($n['link']) ?>">Open</a>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($n['created_at']) ?></td>
                    <td>
                        <?php if ((int)$n['is_read'] === 0): ?>
                            <form method="post" style="margin:0;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="mark_read_id" value="<?= (int)$n['id'] ?>">
                                <button type="submit">Mark read</button>
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
