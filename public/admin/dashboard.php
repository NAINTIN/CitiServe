<?php
require_once __DIR__ . '/../../app/helpers/auth.php';
require_once __DIR__ . '/../../app/helpers/csrf.php';
require_once __DIR__ . '/../../app/core/CitiServeData.php';
require_once __DIR__ . '/../../app/helpers/admin_notifications.php';

$admin = require_admin();
$data = new CitiServeData();
$db = $data->getPdo();

function h($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function request_status_image_name($status)
{
    $status = strtolower((string)$status);
    return match ($status) {
        'pending' => 'my_request_pending.png',
        'claimable', 'released' => 'my_request_claimable.png',
        'rejected' => 'my_request_rejected.png',
        default => 'my_request_received.png',
    };
}

function complaint_status_image_name($status)
{
    $status = strtolower((string)$status);
    return match ($status) {
        'under_review', 'in_progress' => 'my_complaint_processing.png',
        'resolved' => 'my_complaint_resolved.png',
        'rejected' => 'my_complaint_rejected.png',
        default => 'my_complaint_received.png',
    };
}

$stats = [
    'accounts_for_approval' => 0,
    'received_doc_requests' => 0,
    'received_complaints' => 0,
    'registered_residents' => 0,
];

$recentRequests = [];
$recentComplaints = [];

try {
    $stmt = $db->query("SELECT COUNT(*) FROM users WHERE role = 'resident' AND is_verified = 0");
    $stats['accounts_for_approval'] = (int)$stmt->fetchColumn();

    $stmt = $db->query("SELECT COUNT(*) FROM document_requests WHERE status IN ('received', 'pending', 'claimable')");
    $stats['received_doc_requests'] = (int)$stmt->fetchColumn();

    $stmt = $db->query("SELECT COUNT(*) FROM complaints WHERE status IN ('submitted', 'under_review', 'in_progress')");
    $stats['received_complaints'] = (int)$stmt->fetchColumn();

    $stmt = $db->query("SELECT COUNT(*) FROM users WHERE role = 'resident'");
    $stats['registered_residents'] = (int)$stmt->fetchColumn();

    $stmt = $db->query("
        SELECT dr.id, dr.status, dr.created_at, ds.name AS document_name, u.full_name
        FROM document_requests dr
        INNER JOIN users u ON u.id = dr.user_id
        INNER JOIN document_services ds ON ds.id = dr.document_service_id
        ORDER BY dr.created_at DESC, dr.id DESC
        LIMIT 3
    ");
    $recentRequestRows = $stmt->fetchAll();
    foreach ($recentRequestRows as $r) {
        $created = !empty($r['created_at']) ? strtotime((string)$r['created_at']) : false;
        $recentRequests[] = [
            'name' => (string)($r['full_name'] ?? 'Resident'),
            'document' => (string)($r['document_name'] ?? 'Document'),
            'date' => $created ? date('M j, Y', $created) : '-',
            'time' => $created ? date('g:i A', $created) : '-',
            'status_img' => request_status_image_name((string)$r['status']),
        ];
    }

    $stmt = $db->query("
        SELECT c.id, c.status, c.is_anonymous, c.created_at, cc.name AS category_name, u.full_name
        FROM complaints c
        INNER JOIN complaint_categories cc ON cc.id = c.category_id
        LEFT JOIN users u ON u.id = c.user_id
        ORDER BY c.created_at DESC, c.id DESC
        LIMIT 3
    ");
    $recentComplaintRows = $stmt->fetchAll();
    foreach ($recentComplaintRows as $c) {
        $created = !empty($c['created_at']) ? strtotime((string)$c['created_at']) : false;
        $recentComplaints[] = [
            'name' => ((int)$c['is_anonymous'] === 1) ? 'Anonymous' : (string)($c['full_name'] ?? 'Resident'),
            'category' => (string)($c['category_name'] ?? 'Other Concerns'),
            'date' => $created ? date('M j, Y', $created) : '-',
            'time' => $created ? date('g:i A', $created) : '-',
            'status_img' => complaint_status_image_name((string)$c['status']),
        ];
    }
} catch (Throwable $e) {
    // keep fallback values
}

$user = [
    'first_name' => trim(explode(' ', (string)$admin['full_name'])[0]),
    'full_name' => (string)$admin['full_name'],
    'barangay' => 'Barangay Kalayaan, Angono, Rizal',
    'avatar' => '',
];

$adminNotif = build_admin_notifications($data, (int)$admin['id']);
$notifSections = $adminNotif['sections'];
$hasNotif = $adminNotif['has_notif'];
$notifications = $adminNotif['notifications'];
$unreadCount = (int)$adminNotif['unread_count'];

$requestIcons = [
    'Barangay Business Clearance'    => 'images/business_clearance.png',
    'Barangay Clearance'             => 'images/barangay_clearance.png',
    'Barangay ID'                    => 'images/barangay_id.png',
    'Barangay Permit (Construction)' => 'images/barangay_permit.png',
    'Certificate of Indigency'       => 'images/cert_indigency.png',
    'Certificate of Residency'       => 'images/cert_residency.png',
    'Solo Parent Certificate'        => 'images/cert_soloparent.png',
];

$complaintIcons = [
    'Road / Infrastructure'           => 'images/road.png',
    'Garbage / Sanitation'            => 'images/garbage.png',
    'Noise Disturbance'               => 'images/noise.png',
    'Traffic / Parking'               => 'images/traffic.png',
    'Environmental / Tree / Animal'   => 'images/environmental.png',
    'Water / Electricity / Utilities' => 'images/water.png',
    'Community / Social Issues'       => 'images/community.png',
    'Other Concerns'                  => 'images/other_concerns.png',
    'Anonymous Report'                => 'images/anonymous.png',
];

date_default_timezone_set('Asia/Manila');
$hour = (int) date('G');
if ($hour >= 5 && $hour < 12) {
    $greeting = 'Good morning!';
} elseif ($hour >= 12 && $hour < 18) {
    $greeting = 'Good afternoon!';
} else {
    $greeting = 'Good evening!';
}

$hasAnnouncement = false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CitiServe</title>
    <base href="/CitiServe/frontend/admin_dashboard/">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/admin_dashboard.css">
</head>
<body>

<div class="design-strip left" aria-hidden="true"><img src="images/dashboard_design.png" alt=""></div>
<div class="design-strip right" aria-hidden="true"><img src="images/dashboard_design.png" alt=""></div>

<nav class="navbar">
  <a href="/CitiServe/public/admin/dashboard.php" class="navbar-logo">
    <img src="images/logo_pink.png" alt="CitiServe">
  </a>
  <div class="navbar-nav admin-nav">
    <a href="/CitiServe/public/admin/dashboard.php" class="nav-item active"><span class="nav-text">Dashboard</span></a>
    <a href="/CitiServe/public/admin/requests.php" class="nav-item"><span class="nav-text">Document Requests</span></a>
    <a href="/CitiServe/public/admin/complaints.php" class="nav-item"><span class="nav-text">Complaints</span></a>
    <div class="nav-item has-dropdown" id="navUserManagement">
      <span class="nav-text">User Management</span>
      <span class="nav-chevron">⏷</span>
      <div class="nav-dropdown">
        <a href="/CitiServe/public/admin/users.php" class="nav-dropdown-item">Residents</a>
      </div>
    </div>
    <a href="/CitiServe/public/admin/user_verification.php" class="nav-item"><span class="nav-text">Account Verification</span></a>
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
        <img src="<?= !empty($user['avatar']) ? h($user['avatar']) : 'images/admin_dummy_icon.png' ?>" alt="Admin Profile">
      </div>
      <span class="profile-name"><?= h($user['first_name']) ?></span>
      <span class="profile-chevron"><img src="images/profile_dropdown.png" alt=""></span>
    </div>
    <div class="profile-panel" id="profilePanel">
      <div class="profile-panel-top">
        <div class="profile-panel-fullname"><?= h($user['full_name']) ?></div>
        <div class="profile-panel-subtext"><?= h(ucfirst((string)$admin['role'])) ?> • Brgy. Kalayaan</div>
      </div>
      <a href="/CitiServe/public/admin/profile.php" class="profile-panel-item">
        <img src="/CitiServe/frontend/admin_dashboard/images/my_profile.png" alt="My Profile" class="profile-panel-icon1">
        <span>My Profile</span>
      </a>
      <a href="/CitiServe/public/logout.php" class="profile-panel-item logout">
        <img src="images/logout.png" alt="Logout" class="profile-panel-icon2">
        <span>Logout</span>
      </a>
    </div>
  </div>
</nav>

<div class="page-body">
  <div class="dashboard-card">
    <div class="dashboard-grid">
      <div class="left-col">
        <div class="greeting-section">
          <div class="greeting-top"><?= h($greeting) ?><img src="images/welcome_emoji.png" alt="👋" class="greeting-emoji"></div>
          <div class="greeting-name">Welcome, <?= h($user['full_name']) ?></div>
          <div class="greeting-sub"><?= h($user['barangay']) ?> — Admin Overview</div>
          <div class="quick-actions-label">QUICK ACTIONS</div>
        </div>
        <div class="action-btns admin-action-btns">
          <a href="/CitiServe/public/admin/requests.php" class="action-btn1"><img src="images/manage_request.png" alt="Manage Requests"></a>
          <a href="/CitiServe/public/admin/complaints.php" class="action-btn2"><img src="images/manage_document.png" alt="Manage Complaints"></a>
        </div>
        <div class="admin-announcement-card <?= $hasAnnouncement ? 'has-announcement' : 'is-empty' ?>">
          <div class="admin-ann-header">
            <div class="admin-ann-title"><img src="images/announcement_board_icon.png" alt="" class="admin-ann-icon"><span>Announcement Board</span></div>
          </div>
          <div class="admin-ann-empty">
            <img src="images/announcement_empty_icon.png" alt="" class="admin-ann-empty-icon">
            <p>No announcement posted.</p>
            <span>Only one announcement can be active at a time.</span>
          </div>
        </div>
      </div>

      <div class="right-col">
        <div class="stat-row admin-stat-row">
          <div class="stat-card stat-approval">
            <a href="/CitiServe/public/admin/user_verification.php" class="stat-arrow"><img src="images/dashboard_arrow1.png" alt="Go"></a>
            <div class="stat-label">Accounts<br>for Approval</div>
            <div class="stat-value"><?= (int)$stats['accounts_for_approval'] ?></div>
          </div>
          <div class="stat-card stat-docs">
            <a href="/CitiServe/public/admin/requests.php" class="stat-arrow"><img src="images/dashboard_arrow1.png" alt="Go"></a>
            <div class="stat-label">Received<br>Doc Requests</div>
            <div class="stat-value"><?= (int)$stats['received_doc_requests'] ?></div>
          </div>
          <div class="stat-card stat-complaints">
            <a href="/CitiServe/public/admin/complaints.php" class="stat-arrow"><img src="images/dashboard_arrow1.png" alt="Go"></a>
            <div class="stat-label">Received<br>Complaints</div>
            <div class="stat-value"><?= (int)$stats['received_complaints'] ?></div>
          </div>
          <div class="stat-card stat-residents">
            <a href="/CitiServe/public/admin/users.php" class="stat-arrow"><img src="images/dashboard_arrow.png" alt="Go"></a>
            <div class="stat-label">Registered<br>Residents</div>
            <div class="stat-value"><?= (int)$stats['registered_residents'] ?></div>
          </div>
        </div>

        <div class="admin-panels-col">
          <div class="recent-panel admin-recent-panel">
            <div class="panel-header1">
              <div class="panel-title"><img src="images/recent_request.png" class="panel-icon" alt="">Recent Document Requests</div>
              <a href="/CitiServe/public/admin/requests.php" class="panel-viewall1"><img src="images/viewall_pink.png" alt="View all"></a>
            </div>
            <div class="panel-body">
              <?php if (!empty($recentRequests)): ?>
                <?php foreach ($recentRequests as $req): ?>
                  <div class="recent-entry">
                    <div class="recent-entry-left">
                      <div class="recent-entry-icon-wrap"><img src="<?= h($requestIcons[$req['document']] ?? 'images/default.png') ?>" class="recent-entry-icon"></div>
                      <div class="recent-entry-details">
                        <div class="recent-entry-title recent-entry-title-request"><?= h($req['name']) ?> - <?= h($req['document']) ?></div>
                        <div class="recent-entry-meta"><?= h($req['date']) ?><span class="recent-entry-separator">|</span><?= h($req['time']) ?></div>
                      </div>
                    </div>
                    <img src="images/<?= h($req['status_img']) ?>" class="status-badge-img">
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <img src="images/recent_request_main.png" class="panel-empty-main" alt=""><span class="panel-empty-text">No document requests yet</span>
              <?php endif; ?>
            </div>
          </div>

          <div class="recent-panel complaints admin-recent-panel">
            <div class="panel-header2">
              <div class="panel-title"><img src="images/recent_complaint.png" class="panel-icon" alt="">Recent Complaints</div>
              <a href="/CitiServe/public/admin/complaints.php" class="panel-viewall2"><img src="images/viewall_yellow.png" alt="View all"></a>
            </div>
            <div class="panel-body">
              <?php if (!empty($recentComplaints)): ?>
                <?php foreach ($recentComplaints as $cmp): ?>
                  <div class="recent-entry">
                    <div class="recent-entry-left">
                      <div class="recent-entry-icon-wrap"><img src="<?= h($complaintIcons[$cmp['category']] ?? 'images/default.png') ?>" class="recent-entry-icon"></div>
                      <div class="recent-entry-details">
                        <div class="recent-entry-title recent-entry-title-complaint"><?= h($cmp['name']) ?> - <?= h($cmp['category']) ?></div>
                        <div class="recent-entry-meta"><?= h($cmp['date']) ?><span class="recent-entry-separator">|</span><?= h($cmp['time']) ?></div>
                      </div>
                    </div>
                    <img src="images/<?= h($cmp['status_img']) ?>" class="status-badge-img">
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <img src="images/recent_complaint_main.png" class="panel-empty-main" alt=""><span class="panel-empty-text">No complaints submitted yet</span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="JS/admin_dashboard.js"></script>
</body>
</html>
