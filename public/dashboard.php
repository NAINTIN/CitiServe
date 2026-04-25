<?php
require_once __DIR__ . '/../app/core/CitiServeData.php';

session_start();
if (empty($_SESSION['user_id'])) {
  header('Location: /CitiServe/public/login.php');
  exit;
}

$data = new CitiServeData();
$sessionUser = $data->findUserById((int)$_SESSION['user_id']);
if (!$sessionUser) {
  session_unset();
  session_destroy();
  header('Location: /CitiServe/public/login.php');
  exit;
}

$db = $data->getPdo();
$isAdminPortal = ($sessionUser->role === 'admin' || $sessionUser->role === 'staff');
$userId = (int)$sessionUser->id;

if ($isAdminPortal) {
  header('Location: /CitiServe/public/admin/dashboard.php');
  exit;
}

$user = [
  'first_name' => trim(explode(' ', (string)$sessionUser->full_name)[0]),
  'full_name'  => (string)$sessionUser->full_name,
  'barangay'   => 'Barangay Kalayaan, Angono, Rizal',
  'verified'   => ((int)$sessionUser->is_verified === 1),
  'avatar'     => '',
];

$requestCreateLink = $isAdminPortal ? '/CitiServe/public/admin/requests.php' : '/CitiServe/public/request_select.php';
$requestListLink = $isAdminPortal ? '/CitiServe/public/admin/requests.php' : '/CitiServe/public/my_requests.php';
$complaintCreateLink = $isAdminPortal ? '/CitiServe/public/admin/complaints.php' : '/CitiServe/public/complaint_create.php';
$complaintListLink = $isAdminPortal ? '/CitiServe/public/admin/complaints.php' : '/CitiServe/public/my_complaints.php';
$greetingPortalText = $isAdminPortal ? 'Admin/Staff Portal' : 'Resident Portal';
$profileRoleText = $isAdminPortal ? ucfirst((string)$sessionUser->role) : 'Resident';

$whereScope = $isAdminPortal ? '' : ' AND user_id = :user_id';
$params = $isAdminPortal ? [] : ['user_id' => $userId];

$stmt = $db->prepare("SELECT COUNT(*) FROM document_requests WHERE status IN ('ready', 'claimable')" . $whereScope);
$stmt->execute($params);
$readyToClaim = (int)$stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM document_requests WHERE status IN ('active', 'pending', 'received')" . $whereScope);
$stmt->execute($params);
$activeRequests = (int)$stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM complaints WHERE status IN ('active', 'open', 'submitted', 'under_review', 'in_progress')" . $whereScope);
$stmt->execute($params);
$activeComplaints = (int)$stmt->fetchColumn();

$stmt = $db->prepare('SELECT COUNT(*) FROM notifications WHERE is_read = 0 AND user_id = :user_id');
$stmt->execute(['user_id' => $userId]);
$unreadCount = (int)$stmt->fetchColumn();

$stats = [
  'ready_to_claim' => $readyToClaim,
  'active_requests' => $activeRequests,
  'active_complaints' => $activeComplaints,
];

$requestSql = 'SELECT dr.id, dr.created_at, ds.name AS service_name FROM document_requests dr INNER JOIN document_services ds ON ds.id = dr.document_service_id' . ($isAdminPortal ? '' : ' WHERE dr.user_id = :user_id') . ' ORDER BY dr.created_at DESC, dr.id DESC LIMIT 3';
$stmt = $db->prepare($requestSql);
$stmt->execute($params);
$recentRequestRows = $stmt->fetchAll();

$recentRequests = [];
foreach ($recentRequestRows as $r) {
  $createdAt = !empty($r['created_at']) ? strtotime((string)$r['created_at']) : time();
  $recentRequests[] = [
    'title' => (string)$r['service_name'],
    'date' => date('d F, Y', $createdAt),
    'time' => date('h:i A', $createdAt),
  ];
}

$complaintSql = 'SELECT c.id, c.created_at, cc.name AS category_name FROM complaints c INNER JOIN complaint_categories cc ON cc.id = c.category_id' . ($isAdminPortal ? '' : ' WHERE c.user_id = :user_id') . ' ORDER BY c.created_at DESC, c.id DESC LIMIT 3';
$stmt = $db->prepare($complaintSql);
$stmt->execute($params);
$recentComplaintRows = $stmt->fetchAll();

$complaintTitleMap = [
  'road/infrastructure' => 'Road / Infrastructure',
  'garbage/sanitation' => 'Garbage / Sanitation',
  'noise disturbance' => 'Noise Disturbance',
  'traffic/parking' => 'Traffic / Parking',
  'environmental/tree/animal concerns' => 'Environmental / Tree / Animal',
  'water/electricity/utilities' => 'Water / Electricity / Utilities',
  'community/social issues' => 'Community / Social Issues',
  'other' => 'Other Concerns',
];

$recentComplaints = [];
foreach ($recentComplaintRows as $c) {
  $createdAt = !empty($c['created_at']) ? strtotime((string)$c['created_at']) : time();
  $rawName = strtolower((string)$c['category_name']);
  $recentComplaints[] = [
    'title' => isset($complaintTitleMap[$rawName]) ? $complaintTitleMap[$rawName] : (string)$c['category_name'],
    'date' => date('d F, Y', $createdAt),
    'time' => date('h:i A', $createdAt),
  ];
}

$announcement = ['id' => 1, 'img' => '/CitiServe/frontend/dashboard/images/announcement_stack.png'];

$upcomingRequest = [];
if (!empty($recentRequests)) {
  $upcomingRequest = [
    'title' => $recentRequests[0]['title'],
    'date' => $recentRequests[0]['date'],
    'time' => $recentRequests[0]['time'],
    'type' => 'Document',
  ];
}

$requestIcons = [
  'Barangay Business Clearance'    => '/CitiServe/frontend/dashboard/images/business_clearance.png',
  'Barangay Clearance'             => '/CitiServe/frontend/dashboard/images/barangay_clearance.png',
  'Barangay ID'                    => '/CitiServe/frontend/dashboard/images/barangay_id.png',
  'Barangay Permit (Construction)' => '/CitiServe/frontend/dashboard/images/barangay_permit.png',
  'Certificate of Indigency'       => '/CitiServe/frontend/dashboard/images/cert_indigency.png',
  'Certificate of Residency'       => '/CitiServe/frontend/dashboard/images/cert_residency.png',
  'Solo Parent Certificate'        => '/CitiServe/frontend/dashboard/images/cert_soloparent.png',
];

$upcomingRequestIcon = '/CitiServe/frontend/dashboard/images/barangay_clearance.png';
if (!empty($upcomingRequest['title']) && isset($requestIcons[$upcomingRequest['title']])) {
  $upcomingRequestIcon = $requestIcons[$upcomingRequest['title']];
}

$complaintIcons = [
  'Road / Infrastructure'               => '/CitiServe/frontend/dashboard/images/road.png',
  'Garbage / Sanitation'                => '/CitiServe/frontend/dashboard/images/garbage.png',
  'Noise Disturbance'                   => '/CitiServe/frontend/dashboard/images/noise.png',
  'Traffic / Parking'                   => '/CitiServe/frontend/dashboard/images/traffic.png',
  'Environmental / Tree / Animal'       => '/CitiServe/frontend/dashboard/images/environmental.png',
  'Water / Electricity / Utilities'     => '/CitiServe/frontend/dashboard/images/water.png',
  'Community / Social Issues'           => '/CitiServe/frontend/dashboard/images/community.png',
  'Other Concerns'                      => '/CitiServe/frontend/dashboard/images/other_concerns.png',
  'Anonymous Report'                    => '/CitiServe/frontend/dashboard/images/anonymous.png',
];

date_default_timezone_set('Asia/Manila');
$hour = (int) date('G');
if ($hour >= 5 && $hour < 12) {
  $greeting = 'Good morning!';
} elseif ($hour >= 12 && $hour < 17) {
  $greeting = 'Good afternoon!';
} else {
  $greeting = 'Good evening!';
}

$stmt = $db->prepare('SELECT id, title, message, link, is_read, created_at FROM notifications WHERE user_id = :user_id AND is_read = 0 ORDER BY created_at DESC, id DESC LIMIT 10');
$stmt->execute(['user_id' => $userId]);
$notificationRows = $stmt->fetchAll();

$notifications = [];
$nowTs = time();
foreach ($notificationRows as $n) {
  $combined = strtolower((string)$n['title'] . ' ' . (string)$n['message']);
  $category = 'announcement';
  if (strpos($combined, 'complaint') !== false) {
    $category = 'complaint';
  } elseif (strpos($combined, 'document') !== false || strpos($combined, 'request') !== false) {
    $category = 'document';
  }

  $createdTs = !empty($n['created_at']) ? strtotime((string)$n['created_at']) : $nowTs;
  if ($createdTs === false) {
    $createdTs = $nowTs;
  }

  $age = max(0, $nowTs - $createdTs);
  $timeLabel = $age < 3600 ? max(1, (int)floor($age / 60)) . 'm' : ($age < 86400 ? (int)floor($age / 3600) . 'h' : (int)floor($age / 86400) . 'd');
  $section = $age < 3600 ? 'new' : ($age < 86400 ? 'today' : 'earlier');

  $notifications[] = [
    'id' => (int)$n['id'],
    'section' => $section,
    'category' => $category,
    'type' => 'generated',
    'message' => (string)$n['message'],
    'time_label' => $timeLabel,
    'read' => ((int)$n['is_read'] === 1),
    'link' => '/CitiServe/public/notifications.php?open=' . (int)$n['id'],
    'main_icon'  => '/CitiServe/frontend/dashboard/images/citiserve_notif.png',
    'badge_icon' => $category === 'complaint'
      ? '/CitiServe/frontend/dashboard/images/complaint_notif.png'
      : '/CitiServe/frontend/dashboard/images/document_notif.png',
  ];
}

$hasNotif = $unreadCount > 0;
$notifSections = ['new' => [], 'today' => [], 'earlier' => []];
foreach ($notifications as $n) {
  $notifSections[$n['section']][] = $n;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>CitiServe - Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/CitiServe/frontend/dashboard/CSS/dashboard.css">
</head>
<body>

<div class="design-strip left"  aria-hidden="true"><img src="/CitiServe/frontend/dashboard/images/dashboard_design.png" alt=""></div>
<div class="design-strip right" aria-hidden="true"><img src="/CitiServe/frontend/dashboard/images/dashboard_design.png" alt=""></div>

<!-- ═══ NAVBAR ════════════════════════════════════════════════════ -->
<nav class="navbar">

  <a href="/CitiServe/public/dashboard.php" class="navbar-logo">
    <img src="/CitiServe/frontend/dashboard/images/logo_pink.png" alt="CitiServe">
  </a>

  <div class="navbar-nav">
    <a href="/CitiServe/public/dashboard.php" class="nav-item active">
      <span class="nav-text">Dashboard</span>
    </a>

    <div class="nav-item has-dropdown" id="navDocReq">
      <span class="nav-text">Document Requests</span>
      <span class="nav-chevron">⏷</span>
      <div class="nav-dropdown">
        <a href="<?= htmlspecialchars($requestCreateLink) ?>" class="nav-dropdown-item">Request Document</a>
        <a href="<?= htmlspecialchars($requestListLink) ?>"      class="nav-dropdown-item">My Requests</a>
      </div>
    </div>

    <div class="nav-item has-dropdown" id="navComplaint">
      <span class="nav-text">Complaint Management</span>
      <span class="nav-chevron">⏷</span>
      <div class="nav-dropdown">
        <a href="<?= htmlspecialchars($complaintCreateLink) ?>" class="nav-dropdown-item">Submit Complaint</a>
        <a href="<?= htmlspecialchars($complaintListLink) ?>"    class="nav-dropdown-item">My Complaints</a>
      </div>
    </div>
  </div>

  <!-- ══ NAVBAR RIGHT ══════════════════════════════════════════════ -->
  <div class="navbar-right">

    <!-- Bell button -->
    <button class="notif-btn" id="notifBtn"
      data-has-notif="<?= $hasNotif ? '1' : '0' ?>"
      data-img-on="/CitiServe/frontend/dashboard/images/with_notif.png"
      data-img-off="/CitiServe/frontend/dashboard/images/no_notif.png"
      data-img-active="/CitiServe/frontend/dashboard/images/select_notif.png"
      title="Notifications">
      <img id="notifIcon"
        src="<?= $hasNotif ? '/CitiServe/frontend/dashboard/images/with_notif.png' : '/CitiServe/frontend/dashboard/images/no_notif.png' ?>"
        alt="Notifications">
    </button>

    <!-- Notification Panel -->
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
            <div class="notif-section-label" data-section="<?= $key ?>"><?= $label ?></div>

            <?php foreach ($notifSections[$key] as $n): ?>
              <div class="notif-item <?= $n['read'] ? '' : 'unread' ?>"
                  data-id="<?= (int)$n['id'] ?>"
                  data-category="<?= htmlspecialchars($n['category']) ?>"
                  data-link="<?= htmlspecialchars($n['link']) ?>">

                <div class="notif-icon-wrap">
                  <img class="notif-icon-main"
                      src="<?= htmlspecialchars($n['main_icon']) ?>"
                      alt="">
                  <?php if (!empty($n['badge_icon'])): ?>
                    <img class="notif-icon-badge"
                        src="<?= htmlspecialchars($n['badge_icon']) ?>"
                        alt="">
                  <?php endif; ?>
                </div>

                <div class="notif-text">
                  <div class="notif-msg"><?= htmlspecialchars((string)$n['message']) ?></div>
                  <div class="notif-time"><?= htmlspecialchars($n['time_label']) ?></div>
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
        <button class="notif-see-prev" id="notifSeePrev"><p>See previous notifications</p></button>
      </div>
    </div>


<!-- ═══ PROFILE ════════════════════════════════════════════════════ -->
    <div class="profile-pill" id="profilePill">
      <div class="profile-avatar">
        <img src="<?= !empty($user['avatar']) ? htmlspecialchars($user['avatar']) : '/CitiServe/frontend/dashboard/images/profile_icon.png' ?>" alt="Profile">
      </div>
      <span class="profile-name"><?= htmlspecialchars($user['first_name']) ?></span>
      <span class="profile-chevron"><img src="/CitiServe/frontend/dashboard/images/profile_dropdown.png" alt=""></span>
    </div>

    <div class="profile-panel" id="profilePanel">
      <div class="profile-panel-top">
        <div class="profile-panel-fullname"><?= htmlspecialchars($user['full_name']) ?></div>
        <div class="profile-panel-subtext"><?= htmlspecialchars($profileRoleText) ?> • Brgy. Kalayaan</div>
      </div>

      <a href="/CitiServe/public/profile.php" class="profile-panel-item">
        <img src="/CitiServe/frontend/dashboard/images/my_profile.png" alt="My Profile" class="profile-panel-icon1">
        <span>My Profile</span>
      </a>

      <a href="/CitiServe/public/logout.php" class="profile-panel-item logout">
        <img src="/CitiServe/frontend/dashboard/images/logout.png" alt="Logout" class="profile-panel-icon2">
        <span>Logout</span>
      </a>
    </div>

  </div>
</nav>


<!-- ═══ PAGE BODY ═════════════════════════════════════════════════ -->
<div class="page-body">
  <div class="dashboard-card">
    <div class="dashboard-grid">

      <!-- ══ LEFT COLUMN ══════════════════════════════════════════ -->
      <div class="left-col">

        <div class="greeting-section">
          <div class="greeting-top">
            <?= htmlspecialchars($greeting) ?>
            <img src="/CitiServe/frontend/dashboard/images/welcome_emoji.png" alt="👋" class="greeting-emoji">
          </div>
          <div class="greeting-name">Welcome, <?= htmlspecialchars($user['full_name']) ?></div>
          <div class="greeting-sub"><?= htmlspecialchars($user['barangay']) ?> — <?= htmlspecialchars($greetingPortalText) ?></div>
          <?php if ($user['verified']): ?>
            <span class="badge-verified" style="display:inline-flex;align-items:center;gap:8px;padding:6px 12px;border-radius:999px;background:#dcfce7;color:#166534;font-size:12px;font-weight:700;">Fully Verified</span>
          <?php else: ?>
            <span class="badge-verified" style="display:inline-flex;align-items:center;gap:8px;padding:6px 12px;border-radius:999px;background:#ffedd5;color:#c2410c;font-size:12px;font-weight:700;">Not Verified</span>
            <div style="margin-top:8px;font-size:12px;color:#7a7f94;max-width:460px;">Your account is still under review. Please wait for admin approval or re-upload your ID if it gets rejected.</div>
          <?php endif; ?>
        </div>

        <div class="action-btns">
          <a href="<?= htmlspecialchars($requestCreateLink) ?>" class="action-btn1">
            <img src="/CitiServe/frontend/dashboard/images/main_request_document.png" alt="Request Document">
          </a>
          <a href="<?= htmlspecialchars($complaintCreateLink) ?>" class="action-btn2">
            <img src="/CitiServe/frontend/dashboard/images/main_submit_complaint.png" alt="Submit Complaint">
          </a>
        </div>

        <div class="announcement-card">
          <img src="<?= htmlspecialchars($announcement['img']) ?>" class="ann-main" alt="Announcement">
          <a href="https://www.facebook.com/share/p/18HBDUBBBX/?id=<?= (int)$announcement['id'] ?>" class="ann-footer-link" target="_blank">
            View Full Details 🡒
          </a>
        </div>
      </div>


      <!-- ══ RIGHT COLUMN ══════════════════════════════════════════ -->
      <div class="right-col">

        <div class="stat-row">
          <div class="stat-card featured">
            <a href="<?= htmlspecialchars($requestListLink) ?>" class="stat-arrow">
              <img src="/CitiServe/frontend/dashboard/images/dashboard_arrow1.png" alt="Go">
            </a>
            <div class="stat-label">Ready<br>to Claim</div>
            <div class="stat-value"><?= (int)$stats['ready_to_claim'] ?></div>
          </div>

          <div class="stat-card">
            <a href="<?= htmlspecialchars($requestListLink) ?>" class="stat-arrow">
              <img src="/CitiServe/frontend/dashboard/images/dashboard_arrow.png" alt="Go">
            </a>
            <div class="stat-label">Active<br>Requests</div>
            <div class="stat-value"><?= (int)$stats['active_requests'] ?></div>
          </div>

          <div class="stat-card">
            <a href="<?= htmlspecialchars($complaintListLink) ?>" class="stat-arrow">
              <img src="/CitiServe/frontend/dashboard/images/dashboard_arrow.png" alt="Go">
            </a>
            <div class="stat-label">Active<br>Complaints</div>
            <div class="stat-value"><?= (int)$stats['active_complaints'] ?></div>
          </div>

          <!-- Notification count now uses $unreadCount -->
          <div class="stat-card">
            <a href="/CitiServe/public/notifications.php" class="stat-arrow">
              <img src="/CitiServe/frontend/dashboard/images/dashboard_arrow.png" alt="Go">
            </a>
            <div class="stat-label">New Notification</div>
            <div class="stat-value"><?= $unreadCount ?></div>
          </div>
        </div>

        <!-- ══ BOTTOM RIGHT ══════════════════════════════════════ -->
        <div class="bottom-row">
          <div class="panels-col">

            <div class="recent-panel">
              <div class="panel-header1">
                <div class="panel-title">
                  <img src="/CitiServe/frontend/dashboard/images/recent_request.png" class="panel-icon" alt="">
                  Recent Requests
                </div>
                <a href="<?= htmlspecialchars($requestListLink) ?>" class="panel-viewall1">
                  <img src="/CitiServe/frontend/dashboard/images/viewall_pink.png" alt="View all">
                </a>
              </div>
              <div class="panel-body">
                <?php if (empty($recentRequests)): ?>
                  <img src="/CitiServe/frontend/dashboard/images/recent_request_main.png" class="panel-empty-main" alt="">
                  <span class="panel-empty-text">No document requests yet</span>
                <?php else: ?>
                  <?php foreach ($recentRequests as $r): ?>
                    <?php
                      $requestIcon = $requestIcons[$r['title']] ?? '/CitiServe/frontend/dashboard/images/barangay_clearance.png';
                    ?>
                    <div class="recent-entry recent-entry-request">
                      <div class="recent-entry-left">
                        <div class="recent-entry-icon-wrap recent-entry-icon-wrap-request">
                          <img src="<?= htmlspecialchars($requestIcon) ?>" alt="" class="recent-entry-icon">
                        </div>

                        <div class="recent-entry-details">
                          <div class="recent-entry-title recent-entry-title-request">
                            <?= htmlspecialchars($r['title']) ?>
                          </div>
                          <div class="recent-entry-meta">
                            <?= htmlspecialchars($r['date']) ?>
                            <span class="recent-entry-separator">|</span>
                            <?= htmlspecialchars($r['time']) ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>

            <div class="recent-panel complaints">
              <div class="panel-header2">
                <div class="panel-title">
                  <img src="/CitiServe/frontend/dashboard/images/recent_complaint.png" class="panel-icon" alt="">
                  Recent Complaints
                </div>
                <a href="<?= htmlspecialchars($complaintListLink) ?>" class="panel-viewall2">
                  <img src="/CitiServe/frontend/dashboard/images/viewall_yellow.png" alt="View all">
                </a>
              </div>
              <div class="panel-body">
                <?php if (empty($recentComplaints)): ?>
                  <img src="/CitiServe/frontend/dashboard/images/recent_complaint_main.png" class="panel-empty-main" alt="">
                  <span class="panel-empty-text">No complaints submitted yet</span>
                <?php else: ?>
                  <?php foreach ($recentComplaints as $c): ?>
                    <?php
                      $complaintIcon = $complaintIcons[$c['title']] ?? '/CitiServe/frontend/dashboard/images/other_concerns.png';
                    ?>
                    <div class="recent-entry recent-entry-complaint">
                      <div class="recent-entry-left">
                        <div class="recent-entry-icon-wrap recent-entry-icon-wrap-complaint">
                          <img src="<?= htmlspecialchars($complaintIcon) ?>" alt="" class="recent-entry-icon">
                        </div>

                        <div class="recent-entry-details">
                          <div class="recent-entry-title recent-entry-title-complaint">
                            <?= htmlspecialchars($c['title']) ?>
                          </div>
                          <div class="recent-entry-meta">
                            <?= htmlspecialchars($c['date']) ?>
                            <span class="recent-entry-separator">|</span>
                            <?= htmlspecialchars($c['time']) ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>

          </div>

          <!-- ══ CALENDAR ════════════════════════════════════════ -->
          <div class="calendar-wrap">
            <div class="cal-top">
              <div class="cal-header">
                <span class="cal-month" id="calMonthLabel"></span>
                <div class="cal-nav">
                  <button class="cal-nav-btn" id="calPrev">&#8249;</button>
                  <button class="cal-nav-btn" id="calNext">&#8250;</button>
                </div>
              </div>
              <div class="cal-divider"></div>
              <div class="cal-grid">
                <div class="cal-days-header">
                  <span>SUN</span><span>MON</span><span>TUE</span>
                  <span>WED</span><span>THU</span><span>FRI</span><span>SAT</span>
                </div>
                <div class="cal-days-grid" id="calDaysGrid"></div>
              </div>
            </div>

            <div class="cal-upcoming">
              <div class="cal-upcoming-label">Upcoming</div>
              <div class="cal-upcoming-item <?= !empty($upcomingRequest) ? 'has-request' : 'is-empty' ?>">
                <?php if (!empty($upcomingRequest)): ?>
                  <div class="upcoming-request-card">
                    
                    <div class="upcoming-request-left">
                      <div class="upcoming-request-icon-wrap">
                        <img src="<?= htmlspecialchars($upcomingRequestIcon) ?>" alt="" class="upcoming-request-icon">
                      </div>

                      <div class="upcoming-request-details">
                        <div class="upcoming-request-title">
                          <?= htmlspecialchars($upcomingRequest['title']) ?>
                        </div>

                        <div class="upcoming-request-meta">
                          <?= htmlspecialchars($upcomingRequest['date']) ?>
                          <span class="upcoming-request-separator">|</span>
                          <?= htmlspecialchars($upcomingRequest['time']) ?>
                        </div>
                      </div>
                    </div>

                    <div class="upcoming-request-badge">
                      <?= htmlspecialchars($upcomingRequest['type']) ?>
                    </div>

                  </div>
                <?php else: ?>
                  <span class="upcoming-empty-text">No pending document requests</span>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="/CitiServe/frontend/dashboard/dashboard.js"></script>
</body>
</html>
