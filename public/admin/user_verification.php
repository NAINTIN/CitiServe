<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../storage/php-error.log');
error_reporting(E_ALL);

require_once __DIR__ . '/../../app/helpers/auth.php';
require_once __DIR__ . '/../../app/helpers/csrf.php';
require_once __DIR__ . '/../../app/core/CitiServeData.php';
require_once __DIR__ . '/../../app/helpers/admin_notifications.php';

$admin = require_admin();
$data = new CitiServeData();
$db = $data->getPdo();

$adminNotif = build_admin_notifications($data, (int)$admin['id']);
$notifSections = $adminNotif['sections'];
$hasNotif = $adminNotif['has_notif'];
$notifications = $adminNotif['notifications'];
$unreadCount = (int)$adminNotif['unread_count'];

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

// Get all users and filter for unverified residents
$allUsers = $data->getAllUsersWithVerification();
$users = [];
$pendingCount = 0;
$approvedCount = 0;
$rejectedCount = 0;

foreach ($allUsers as $u) {
    if ((string)$u['role'] === 'resident') {
        if ((int)$u['is_verified'] === 0) {
            $users[] = $u;
            $pendingCount++;
        } elseif ((int)$u['is_verified'] === 1) {
            $approvedCount++;
        } else {
            $rejectedCount++;
        }
    }
}

function h($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function getInitials($name) {
    $parts = explode(' ', trim($name));
    $first = strtoupper(substr($parts[0] ?? '', 0, 1));
    $last = strtoupper(substr(end($parts) ?: '', 0, 1));
    return $first . $last;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Verification - CitiServe</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/CitiServe/frontend/admin_dashboard/CSS/admin_dashboard.css">
    <link rel="stylesheet" href="/CitiServe/public/admin/CSS/admin_account_verification.css">
</head>

<body>
<div class="design-strip left" aria-hidden="true"><img src="/CitiServe/frontend/admin_dashboard/images/dashboard_design.png" alt=""></div>
<div class="design-strip right" aria-hidden="true"><img src="/CitiServe/frontend/admin_dashboard/images/dashboard_design.png" alt=""></div>

<nav class="navbar">
  <a href="/CitiServe/public/admin/dashboard.php" class="navbar-logo">
    <img src="/CitiServe/frontend/admin_dashboard/images/logo_pink.png" alt="CitiServe">
  </a>
  <div class="navbar-nav admin-nav">
    <a href="/CitiServe/public/admin/dashboard.php" class="nav-item"><span class="nav-text">Dashboard</span></a>
    <a href="/CitiServe/public/admin/requests.php" class="nav-item"><span class="nav-text">Document Requests</span></a>
    <a href="/CitiServe/public/admin/complaints.php" class="nav-item"><span class="nav-text">Complaints</span></a>
    <div class="nav-item has-dropdown" id="navUserManagement">
      <span class="nav-text">User Management</span>
      <span class="nav-chevron">⏷</span>
      <div class="nav-dropdown">
        <a href="/CitiServe/public/admin/users.php" class="nav-dropdown-item">Residents</a>
      </div>
    </div>
    <a href="/CitiServe/public/admin/user_verification.php" class="nav-item active"><span class="nav-text">Account Verification</span></a>
  </div>
  <div class="navbar-right admin-navbar-right">
    <button class="notif-btn" id="notifBtn"
      data-has-notif="<?= $hasNotif ? '1' : '0' ?>"
      data-img-on="/CitiServe/frontend/admin_dashboard/images/with_notif.png"
      data-img-off="/CitiServe/frontend/admin_dashboard/images/no_notif.png"
      data-img-active="/CitiServe/frontend/admin_dashboard/images/select_notif.png"
      data-read-url="/CitiServe/public/admin/notifications_read.php"
      data-csrf-token="<?= h(csrf_token()) ?>"
      title="Notifications">
      <img id="notifIcon"
        src="<?= $hasNotif ? '/CitiServe/frontend/admin_dashboard/images/with_notif.png' : '/CitiServe/frontend/admin_dashboard/images/no_notif.png' ?>"
        alt="Notifications">
      <span class="notif-count-badge" id="notifCount" <?= $unreadCount > 0 ? '' : 'style="display:none;"' ?>>
        <?= $unreadCount > 99 ? '99+' : (int)$unreadCount ?>
      </span>
    </button>
    <div class="notif-panel" id="notifPanel" aria-label="Notifications">
      <div class="notif-panel-header">
        <span class="notif-panel-title">Notifications</span>
        <button class="notif-panel-more" title="More options">···</button>
      </div>
      <div class="notif-tabs">
        <button class="notif-tab active" data-filter="all">All</button>
        <button class="notif-tab" data-filter="document">Document</button>
        <button class="notif-tab" data-filter="complaint">Complaint</button>
      </div>
      <div class="notif-list" id="notifList">
        <?php foreach (['new' => 'New', 'today' => 'Today', 'earlier' => 'Earlier'] as $key => $label): ?>
          <?php if (!empty($notifSections[$key])): ?>
            <div class="notif-section-label" data-section="<?= h($key) ?>"><?= h($label) ?></div>
            <?php foreach ($notifSections[$key] as $n): ?>
              <div class="notif-item <?= $n['read'] ? '' : 'unread' ?>"
                data-id="<?= (int)$n['id'] ?>"
                data-category="<?= h($n['category']) ?>"
                data-link="<?= h($n['link']) ?>">
                <div class="notif-icon-wrap">
                  <img class="notif-icon-main" src="<?= h($n['main_icon']) ?>" alt="">
                  <?php if (!empty($n['badge_icon'])): ?>
                    <img class="notif-icon-badge" src="<?= h($n['badge_icon']) ?>" alt="">
                  <?php endif; ?>
                </div>
                <div class="notif-text">
                  <div class="notif-msg"><?= h($n['message']) ?></div>
                  <div class="notif-time"><?= h($n['time_label']) ?></div>
                </div>
                <div class="notif-dot"></div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        <?php endforeach; ?>
        <?php if (empty($notifications)): ?>
          <div class="notif-empty">No notifications yet.</div>
        <?php endif; ?>
      </div>
      <div class="notif-footer">
        <button class="notif-see-prev" id="notifSeePrev"><p>Go to requests and complaints</p></button>
      </div>
    </div>
    <div class="profile-pill" id="profilePill">
      <div class="profile-avatar">
        <img src="<?= !empty($admin['avatar']) ? h($admin['avatar']) : '/CitiServe/frontend/admin_dashboard/images/admin_dummy_icon.png' ?>" alt="Admin">
      </div>
      <span class="profile-name"><?= h(explode(' ', (string)$admin['full_name'])[0]) ?></span>
      <span class="profile-chevron"><img src="/CitiServe/frontend/admin_dashboard/images/profile_dropdown.png" alt=""></span>
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
      <h1 class="page-title">Account Verification</h1>
      <p class="page-subtitle">Review and approve resident account registrations.</p>
    </div>
    <?php if ($message): ?><p style="color:#15803d;margin-top:10px;"><?= h($message) ?></p><?php endif; ?>
    <?php if ($error): ?><p style="color:#b91c1c;margin-top:10px;"><?= h($error) ?></p><?php endif; ?>

    <div class="admin-summary-row">
      <div class="summary-box summary-pending-verification">
        <div class="summary-number"><?= $pendingCount ?></div>
        <div class="summary-label">Pending</div>
        <img src="/CitiServe/frontend/verification_table/images/admin_verification_pending.png" class="summary-icon1">
      </div>

      <div class="summary-box summary-approved-verification">
        <div class="summary-number"><?= $approvedCount ?></div>
        <div class="summary-label">Approved</div>
        <img src="/CitiServe/frontend/verification_table/images/admin_verification_approved.png" class="summary-icon2">
      </div>

      <div class="summary-box summary-rejected-verification">
        <div class="summary-number"><?= $rejectedCount ?></div>
        <div class="summary-label">Rejected</div>
        <img src="/CitiServe/frontend/verification_table/images/admin_verification_rejected.png" class="summary-icon3">
      </div>
    </div>

    <div class="pending-title-row">
      <img src="/CitiServe/frontend/verification_table/images/pending_acc_icon.png" alt="">
      <span>Pending Accounts</span>
    </div>

    <div class="verification-table-card">
      <div class="verification-table-head">
        <div>Resident ID</div>
        <div>Name</div>
        <div>Address</div>
        <div>Date</div>
        <div></div>
      </div>

      <div class="verification-table-body" id="verificationTableBody">
        <?php foreach ($users as $u): ?>
          <?php
          // Format date
          $date = !empty($u['created_at']) ? date('M j, Y', strtotime($u['created_at'])) : 'Unknown';
          // Get proof of ID filename
          $proofOfId = !empty($u['proof_of_id']) ? basename($u['proof_of_id']) : 'No document';
          ?>
          <div class="verification-row"
            data-resident-id="<?= h($u['id']) ?>"
            data-name="<?= h($u['full_name']) ?>"
            data-email="<?= h($u['email']) ?>"
            data-address="<?= h($u['address'] ?? 'No address provided') ?>"
            data-date="<?= h($date) ?>"
            data-initials="<?= h(getInitials($u['full_name'])) ?>"
            data-document="<?= h($proofOfId) ?>">


            <div class="verification-cell request-id"><?= h($u['id']) ?></div>

            <div class="verification-cell resident-name-cell">
              <div class="resident-initials"><?= h(getInitials($u['full_name'])) ?></div>
              <div>
                <div class="resident-fullname"><?= h($u['full_name']) ?></div>
                <div class="resident-email"><?= h($u['email']) ?></div>
              </div>
            </div>

            <div class="verification-cell address-cell"><?= h($u['address'] ?? 'No address provided') ?></div>
            <div class="verification-cell request-date"><?= h($date) ?></div>

            <div class="verification-cell review-cell">
              <button type="button" class="review-btn">Review</button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="page-footer">
      <span><strong>CitiServe</strong> © 2026. All rights reserved.</span>
    </div>
  </div>
</div>

<div class="account-review-modal" id="accountReviewModal">
  <div class="account-review-box">
    <button type="button" class="account-review-close" id="accountReviewClose">×</button>

    <div class="account-review-title">
      Account Review – <span id="reviewResidentId">RES-0000000001</span>
    </div>

    <div class="account-review-divider"></div>

    <div class="account-review-header">
      <div class="account-review-avatar" id="reviewInitials">EF</div>

      <div>
        <div class="account-review-name" id="reviewName">Eduardo Flores</div>
        <div class="account-review-id" id="reviewSmallId">RES-0000000001</div>
        <img src="/CitiServe/frontend/admin_dashboard/images/resident_active_staff.png" class="account-review-status-img" alt="Active">
      </div>
    </div>

    <div class="account-review-divider"></div>

    <div class="account-review-grid">
      <div>
        <label>Email</label>
        <p id="reviewEmail">eduardo.f@email.com</p>
      </div>

      <div>
        <label>Date Submitted</label>
        <p id="reviewDate">04/01/2025</p>
      </div>

      <div class="review-address">
        <label>Address</label>
        <p id="reviewAddress">123 Rizal St., Brgy. Kalayaan, Angono, Rizal</p>
      </div>
    </div>

    <div class="review-document-box">
      <div class="review-document-title">Uploaded Document</div>

      <div class="review-document-item">
        <span id="reviewDocumentName">Valid ID (Front).jpg</span>
        <a href="#" id="reviewDocumentLink" target="_blank" class="review-doc-link">View</a>
      </div>
    </div>

    <div class="account-review-actions">
      <form method="POST" id="verifyForm" style="margin:0;">
        <?= csrf_field() ?>
        <input type="hidden" name="user_id" id="verifyUserId" value="">
        <input type="hidden" name="action" id="verifyAction" value="">
      </form>
      <button type="button" class="account-review-img-btn" id="rejectAccountBtn">
        <img src="/CitiServe/frontend/verification_table/images/admin_reject_account.png" alt="Reject">
      </button>

      <button type="button" class="account-review-img-btn" id="approveAccountBtn">
        <img src="/CitiServe/frontend/verification_table/images/admin_approve_account.png" alt="Approve Account">
      </button>
    </div>
  </div>
</div>

<script src="/CitiServe/frontend/admin_dashboard/JS/admin_dashboard.js"></script>
<script src="/CitiServe/public/admin/JS/admin_account_verification.js"></script>
</body>
</html>