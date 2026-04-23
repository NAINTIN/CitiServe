<?php
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/core/CitiServeData.php';

$user = require_login();
$data = new CitiServeData();
$db = $data->getPdo();

if (isset($_GET['open'])) {
    $openId = (int)$_GET['open'];
    if ($openId > 0) {
        $data->markNotificationAsRead($openId, (int)$user['id']);

        $stmt = $db->prepare('SELECT link FROM notifications WHERE id = ? AND user_id = ? LIMIT 1');
        $stmt->execute([$openId, (int)$user['id']]);
        $target = $stmt->fetchColumn();

        if (!empty($target)) {
            header('Location: ' . (string)$target);
            exit;
        }
    }

    header('Location: /CitiServe/public/notifications.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify_or_die();

    if (isset($_POST['mark_all_read'])) {
        $data->markAllNotificationsAsRead((int)$user['id']);
        header('Location: /CitiServe/public/notifications.php');
        exit;
    }

    if (isset($_POST['mark_read_id'])) {
        $id = (int)$_POST['mark_read_id'];
        if ($id > 0) {
            $data->markNotificationAsRead($id, (int)$user['id']);
        }
        header('Location: /CitiServe/public/notifications.php');
        exit;
    }
}

$rows = $data->getNotificationsByUser((int)$user['id']);
$roleText = ($user['role'] === 'admin' || $user['role'] === 'staff') ? ucfirst((string)$user['role']) : 'Resident';
$firstName = trim(explode(' ', (string)$user['full_name'])[0]);

function h($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notifications - CitiServe</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/CitiServe/frontend/dashboard/CSS/dashboard.css">
    <style>
        .notif-page-wrap { max-width: 980px; margin: 40px auto; }
        .notif-page-card { background: #fff; border: 1px solid #f6d4df; border-radius: 28px; padding: 28px; box-shadow: 0 8px 30px rgba(122, 37, 63, 0.08); }
        .notif-page-head { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 18px; }
        .notif-page-title { margin: 0; font-size: 30px; font-weight: 800; color: #7f1941; }
        .notif-page-sub { margin: 6px 0 0; color: #8c6b77; font-size: 14px; }
        .notif-actions { display: flex; gap: 10px; }
        .notif-btn-main { border: 1px solid #f0b8ca; background: #fff3f8; color: #7f1941; border-radius: 12px; font-weight: 700; padding: 10px 14px; cursor: pointer; }
        .notif-list-full { display: grid; gap: 12px; margin-top: 16px; }
        .notif-row { border: 1px solid #f5d7e2; border-radius: 16px; padding: 14px 16px; display: flex; justify-content: space-between; gap: 18px; background: #fff; }
        .notif-row.unread { background: #fff7fb; }
        .notif-row-left { min-width: 0; }
        .notif-row-title { font-size: 15px; font-weight: 800; color: #7f1941; margin-bottom: 4px; }
        .notif-row-msg { font-size: 14px; color: #4f3340; line-height: 1.45; }
        .notif-row-time { font-size: 12px; color: #94737f; margin-top: 6px; }
        .notif-row-right { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
        .notif-open-link { text-decoration: none; border: 1px solid #f0b8ca; color: #7f1941; border-radius: 10px; padding: 8px 11px; font-size: 13px; font-weight: 700; }
        .notif-mark-form { margin: 0; }
        .notif-mark-btn { border: 0; background: #7f1941; color: #fff; border-radius: 10px; padding: 8px 11px; font-size: 12px; font-weight: 700; cursor: pointer; }
        .notif-read-pill { background: #f3e9ee; color: #7a5a66; border-radius: 999px; padding: 6px 10px; font-size: 12px; font-weight: 700; }
        .notif-empty { padding: 30px 10px; text-align: center; color: #8a6975; border: 1px dashed #efc8d7; border-radius: 14px; }
        .notif-top-links { margin-bottom: 14px; display: flex; gap: 12px; align-items: center; }
        .notif-top-links a { color: #7f1941; text-decoration: none; font-weight: 700; }
    </style>
</head>
<body>
<div class="design-strip left" aria-hidden="true"><img src="/CitiServe/frontend/dashboard/images/dashboard_design.png" alt=""></div>
<div class="design-strip right" aria-hidden="true"><img src="/CitiServe/frontend/dashboard/images/dashboard_design.png" alt=""></div>

<nav class="navbar">
  <a href="/CitiServe/public/dashboard.php" class="navbar-logo">
    <img src="/CitiServe/frontend/dashboard/images/logo_pink.png" alt="CitiServe">
  </a>
  <div class="navbar-right">
    <div class="profile-pill">
      <div class="profile-avatar"><img src="/CitiServe/frontend/dashboard/images/profile_icon.png" alt="Profile"></div>
      <span class="profile-name"><?= h($firstName) ?></span>
    </div>
  </div>
</nav>

<div class="notif-page-wrap">
    <div class="notif-page-card">
        <div class="notif-top-links">
            <a href="/CitiServe/public/dashboard.php">Dashboard</a>
            <span>•</span>
            <a href="/CitiServe/public/logout.php">Logout</a>
        </div>

        <div class="notif-page-head">
            <div>
                <h1 class="notif-page-title">Notifications</h1>
                <p class="notif-page-sub"><?= h($roleText) ?> • Brgy. Kalayaan</p>
            </div>
            <div class="notif-actions">
                <form method="post" style="margin:0;">
                    <?= csrf_field() ?>
                    <button type="submit" name="mark_all_read" value="1" class="notif-btn-main">Mark all as read</button>
                </form>
            </div>
        </div>

        <?php if (empty($rows)): ?>
            <div class="notif-empty">No notifications yet.</div>
        <?php else: ?>
            <div class="notif-list-full">
                <?php foreach ($rows as $n): ?>
                    <div class="notif-row <?= (int)$n['is_read'] === 0 ? 'unread' : '' ?>">
                        <div class="notif-row-left">
                            <div class="notif-row-title"><?= h($n['title']) ?></div>
                            <div class="notif-row-msg"><?= h($n['message']) ?></div>
                            <div class="notif-row-time"><?= h($n['created_at']) ?></div>
                        </div>

                        <div class="notif-row-right">
                            <a class="notif-open-link" href="/CitiServe/public/notifications.php?open=<?= (int)$n['id'] ?>">Open</a>
                            <?php if ((int)$n['is_read'] === 0): ?>
                                <form method="post" class="notif-mark-form">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="mark_read_id" value="<?= (int)$n['id'] ?>">
                                    <button type="submit" class="notif-mark-btn">Mark read</button>
                                </form>
                            <?php else: ?>
                                <span class="notif-read-pill">Read</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
