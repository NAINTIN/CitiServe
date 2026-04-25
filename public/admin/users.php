<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../../app/helpers/auth.php';
require_once __DIR__ . '/../../app/helpers/csrf.php';
require_once __DIR__ . '/../../app/core/CitiServeData.php';
require_once __DIR__ . '/../../app/helpers/admin_notifications.php';

$admin = require_admin();
$data = new CitiServeData();
$db = $data->getPdo();

$allowedRoles = ['resident', 'staff', 'admin'];
$message = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify_or_die();
    $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $newRole = isset($_POST['new_role']) ? trim($_POST['new_role']) : '';
    if ($userId > 0 && in_array($newRole, $allowedRoles, true)) {
        if ($userId === (int)$admin['id'] && $newRole !== 'admin') {
            $error = 'You cannot remove your own admin role.';
        } else {
            try {
                $db->beginTransaction();
                $stmt = $db->prepare("UPDATE users SET role = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$newRole, $userId]);
                $db->commit();
                $message = "Role updated successfully.";
            } catch (Throwable $e) {
                if ($db->inTransaction()) $db->rollBack();
                $error = 'Failed to update role.';
            }
        }
    }
}

$adminNotif = build_admin_notifications($data, (int)$admin['id']);
$notifSections = $adminNotif['sections'];
$hasNotif = $adminNotif['has_notif'];
$unreadCount = (int)$adminNotif['unread_count'];

$users = $db->query("
    SELECT u.*, 
    (SELECT COUNT(*) FROM document_requests dr WHERE dr.user_id = u.id) AS request_count,
    (SELECT COUNT(*) FROM complaints c WHERE c.user_id = u.id) AS complaint_count
    FROM users u ORDER BY u.created_at DESC
")->fetchAll();

$totalUsers = count($users);
$residentCount = 0; $staffCount = 0; $adminCount = 0;
foreach ($users as $u) {
    if ($u['role'] === 'resident') $residentCount++;
    elseif ($u['role'] === 'staff') $staffCount++;
    elseif ($u['role'] === 'admin') $adminCount++;
}


function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function getInitials($name) {
    $parts = explode(' ', trim($name));
    return strtoupper(substr($parts[0] ?? '', 0, 1) . substr(end($parts) ?: '', 0, 1));
}
function getTypeImage($isVerified) {
    return ((int)$isVerified === 1) ? 'resident_full_admin.png' : 'resident_basic_admin.png';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management - CitiServe</title>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/CitiServe/frontend/admin_dashboard/CSS/admin_dashboard.css">
    <link rel="stylesheet" href="/CitiServe/frontend/admin_dashboard/CSS/admin_residents.css">
</head>
<body>

<div class="design-strip left"><img src="/CitiServe/frontend/admin_dashboard/images/dashboard_design.png" alt=""></div>
<div class="design-strip right"><img src="/CitiServe/frontend/admin_dashboard/images/dashboard_design.png" alt=""></div>

<nav class="navbar">
  <a href="/CitiServe/public/admin/dashboard.php" class="navbar-logo">
    <img src="/CitiServe/frontend/admin_dashboard/images/logo_pink.png" alt="CitiServe">
  </a>
  <div class="navbar-nav admin-nav">
    <a href="/CitiServe/public/admin/dashboard.php" class="nav-item"><span class="nav-text">Dashboard</span></a>
    <a href="/CitiServe/public/admin/requests.php" class="nav-item"><span class="nav-text">Document Requests</span></a>
    <a href="/CitiServe/public/admin/complaints.php" class="nav-item"><span class="nav-text">Complaints</span></a>
    <div class="nav-item has-dropdown active">
      <span class="nav-text">User Management</span>
      <span class="nav-chevron">⏷</span>
      <div class="nav-dropdown">
        <a href="/CitiServe/public/admin/users.php" class="nav-dropdown-item">Residents</a>
        <a href="/CitiServe/public/admin/staff.php" class="nav-dropdown-item">Staff</a>
      </div>
    </div>
    <a href="/CitiServe/public/admin/user_verification.php" class="nav-item"><span class="nav-text">Account Verification</span></a>
  </div>

  <div class="navbar-right admin-navbar-right">
    <button class="notif-btn" id="notifBtn">
      <img id="notifIcon" src="<?= $hasNotif ? '/CitiServe/frontend/admin_dashboard/images/with_notif.png' : '/CitiServe/frontend/admin_dashboard/images/no_notif.png' ?>" alt="">
      <?php if($unreadCount > 0): ?><span class="notif-count-badge"><?= $unreadCount ?></span><?php endif; ?>
    </button>
    
    <div class="profile-pill" id="profilePill">
      <div class="profile-avatar">
        <img src="<?= !empty($admin['avatar']) ? h($admin['avatar']) : '/CitiServe/frontend/admin_dashboard/images/admin_dummy_icon.png' ?>" alt="">
      </div>
      <span class="profile-name"><?= h(explode(' ', $admin['full_name'])[0]) ?></span>
      <span class="profile-chevron"><img src="/CitiServe/frontend/admin_dashboard/images/profile_dropdown.png" alt=""></span>
    </div>
  </div>

  <div class="profile-panel" id="profilePanel">
      <div class="profile-panel-top">
        <div class="profile-panel-fullname"><?= h($admin['full_name']) ?></div>
        <div class="profile-panel-subtext">Admin • Brgy. Kalayaan</div>
      </div>

      <a href="/CitiServe/public/admin/profile.php" class="profile-panel-item">
        <img src="/CitiServe/frontend/admin_dashboard/images/my_profile.png" class="profile-panel-icon1" alt="">
        <span>My Profile</span>
      </a>

      <a href="/CitiServe/public/logout.php" class="profile-panel-item logout">
        <img src="/CitiServe/frontend/admin_dashboard/images/logout.png" class="profile-panel-icon2" alt="">
        <span>Logout</span>
      </a>
    </div>
  </div>
</nav>

<div class="page-body">
  <div class="admin-doc-card">
    <div class="page-head">
      <h1 class="page-title">User Management</h1>
      <p class="page-subtitle">View, manage, and track all user information.</p>
    </div>

    <div class="admin-summary-row">
      <div class="summary-box summary-total">
        <div class="summary-number"><?= $totalUsers ?></div>
        <div class="summary-label">Total Users</div>
        <img src="/CitiServe/frontend/admin_dashboard/images/admin_total_resident.png" class="summary-icon1">
      </div>
      <div class="summary-box summary-residents">
        <div class="summary-number"><?= $residentCount ?></div>
        <div class="summary-label">Residents</div>
        <img src="/CitiServe/frontend/admin_dashboard/images/admin_active_resident.png" class="summary-icon2">
      </div>
      <div class="summary-box summary-staff">
        <div class="summary-number"><?= $staffCount ?></div>
        <div class="summary-label">Staff</div>
        <img src="/CitiServe/frontend/admin_dashboard/images/stafftotal_faded_icon.png" class="summary-icon3">
      </div>
      <div class="summary-box summary-admins">
        <div class="summary-number"><?= $adminCount ?></div>
        <div class="summary-label">Admins</div>
        <img src="/CitiServe/frontend/admin_dashboard/images/admin_dummy_icon.png" class="summary-icon4">
      </div>
    </div>

    <div class="toolbar">
      <div class="toolbar-left">
        <div class="search-wrap">
          <img src="/CitiServe/frontend/admin_dashboard/images/search_icon.png" alt="" class="search-icon">
          <input type="text" id="searchInput" placeholder="Search...">
        </div>
        <button class="clear-btn"><img src="/CitiServe/frontend/admin_dashboard/images/my_request_clear.png" alt=""></button>
      </div>
    </div>

    <div class="requests-table-card">
      <div class="requests-table-head">
        <div>User ID</div>
        <div>Name & Email</div>
        <div>Role</div>
        <div>Contact</div>
        <div>Req.</div>
        <div>Comp.</div>
        <div>Joined</div>
        <div>Type</div>
        <div>Action</div> </div>

      <div class="requests-table-body">
        <?php foreach ($users as $u): ?>
          <div class="request-row">
            <div class="request-cell request-id"><?= (int)$u['id'] ?></div>
            <div class="request-cell resident-name-cell">
              <div class="resident-initials"><?= h(getInitials($u['full_name'])) ?></div>
              <div>
                <div class="resident-fullname"><?= h($u['full_name']) ?></div>
                <div class="resident-email"><?= h($u['email']) ?></div>
              </div>
            </div>
            <div class="request-cell"><?= h($u['role']) ?></div>
            <div class="request-cell"><?= h($u['contact_number'] ?? '-') ?></div>
            <div class="request-cell count-cell"><?= (int)$u['request_count'] ?></div>
            <div class="request-cell count-cell"><?= (int)$u['complaint_count'] ?></div>
            <div class="request-cell"><?= date('M j, Y', strtotime($u['created_at'])) ?></div>
            <div class="request-cell type-cell">
              <img src="/CitiServe/frontend/admin_dashboard/images/<?= h(getTypeImage($u['is_verified'])) ?>" class="type-badge-img">
            </div>
            <div class="request-cell manage-cell">
              <form method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                <select name="new_role" class="role-select-box">
                  <?php foreach ($allowedRoles as $r): ?>
                    <option value="<?= $r ?>" <?= $r === $u['role'] ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
                  <?php endforeach; ?>
                </select>
                <button type="submit" class="manage-btn">Update</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="page-footer"><span><strong>CitiServe</strong> © 2026. All rights reserved.</span></div>
  </div>
</div>
<script src="/CitiServe/frontend/admin_dashboard/JS/admin_residents.js"></script>
</body>
</html>