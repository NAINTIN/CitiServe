<?php
$user = [
  'first_name' => 'Juan',
  'full_name'  => 'Juan Dela Cruz',
  'avatar'     => '',
];

$complaintCategories = [
  [
    'title' => 'Road / Infrastructure',
    'desc'  => 'Damaged roads, potholes, broken drainage, collapsed structures establishments',
    'icon'  => 'images/pink_road.png',
    'subtle'=> 'images/subtle_road.png',
    'link'  => 'complaint_form.php?category=road_infrastructure'
  ],
  [
    'title' => 'Garbage / Sanitation',
    'desc'  => 'Uncollected garbage, illegal dumping, poor sanitation',
    'icon'  => 'images/pink_garbage.png',
    'subtle'=> 'images/subtle_garbage.png',
    'link'  => 'complaint_form.php?category=garbage_sanitation'
  ],
  [
    'title' => 'Noise Disturbance',
    'desc'  => 'Excessive noise from neighbors, parties, or establishments',
    'icon'  => 'images/pink_noise.png',
    'subtle'=> 'images/subtle_noise.png',
    'link'  => 'complaint_form.php?category=noise_disturbance'
  ],
  [
    'title' => 'Traffic / Parking',
    'desc'  => 'Traffic obstruction, illegal parking, road blockages',
    'icon'  => 'images/pink_traffic.png',
    'subtle'=> 'images/subtle_traffic.png',
    'link'  => 'complaint_form.php?category=traffic_parking'
  ],
  [
    'title' => 'Environmental / Tree / Animal',
    'desc'  => 'Fallen trees, stray animals, environmental violations',
    'icon'  => 'images/pink_environmental.png',
    'subtle'=> 'images/subtle_environmental.png',
    'link'  => 'complaint_form.php?category=environmental_tree_animal'
  ],
  [
    'title' => 'Water / Electricity / Utilities',
    'desc'  => 'Water supply issues, power outages, utility disruptions',
    'icon'  => 'images/pink_water.png',
    'subtle'=> 'images/subtle_water.png',
    'link'  => 'complaint_form.php?category=water_electricity_utilities'
  ],
  [
    'title' => 'Community / Social Issues',
    'desc'  => 'Community disputes, social concerns, barangay matters',
    'icon'  => 'images/pink_community.png',
    'subtle'=> 'images/subtle_community.png',
    'link'  => 'complaint_form.php?category=community_social_issues'
  ],
  [
    'title' => 'Other Concerns',
    'desc'  => 'Issues not covered by other categories',
    'icon'  => 'images/pink_otherconcerns.png',
    'subtle'=> 'images/subtle_otherconcerns.png',
    'link'  => 'complaint_form.php?category=other_concerns'
  ],
  [
    'title' => 'Anonymous Report',
    'desc'  => 'Submit a complaint without revealing your identity',
    'icon'  => 'images/pink_anonymous.png',
    'subtle'=> 'images/subtle_anonymous.png',
    'link'  => 'anonymous_complaint_form.php?category=anonymous_report'
  ],
];


$notifications = [
  [
    'id'         => 1,
    'section'    => 'new',
    'category'   => 'document',
    'type'       => 'submitted',
    'message'    => 'Your <strong>Barangay Clearance</strong> request has been submitted.',
    'time_label' => '1m',
    'read'       => false,
    'link'       => 'my_requests.php',
    'main_icon'  => 'images/citiserve_notif.png',
    'badge_icon' => 'images/document_notif.png',
  ],
  [
    'id'         => 2,
    'section'    => 'today',
    'category'   => 'announcement',
    'type'       => 'fb_announcement',
    'message'    => 'New announcement from Barangay Kalayaan. <strong>Click to view.</strong>',
    'time_label' => '3h',
    'read'       => false,
    'link'       => 'https://www.facebook.com/share/p/18HBDUBBBX/',
    'main_icon'  => 'images/kalayaan_notif.png',
    'badge_icon' => 'images/important_notif.png',
  ],
  [
    'id'         => 3,
    'section'    => 'today',
    'category'   => 'complaint',
    'type'       => 'submitted',
    'message'    => 'Your complaint has been submitted.',
    'time_label' => '6h',
    'read'       => false,
    'link'       => 'my_complaints.php',
    'main_icon'  => 'images/citiserve_notif.png',
    'badge_icon' => 'images/complaint_notif.png',
  ],
  [
    'id'         => 4,
    'section'    => 'earlier',
    'category'   => 'account',
    'type'       => 'fully_verified',
    'message'    => 'Your account has been successfully verified. <strong>You may now access all services.</strong>',
    'time_label' => '1d',
    'read'       => false,
    'link'       => 'profile.php',
    'main_icon'  => 'images/citiserve_notif.png',
    'badge_icon' => 'images/verified_notif.png',
  ],
];

$unreadCount   = count(array_filter($notifications, fn($n) => !$n['read']));
$hasNotif      = $unreadCount > 0;
$notifSections = ['new' => [], 'today' => [], 'earlier' => []];
foreach ($notifications as $n) {
  $notifSections[$n['section']][] = $n;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CitiServe - Submit Complaint</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/complaint.css">
</head>
<body>

<div class="design-strip left" aria-hidden="true"><img src="images/complaint_design.png" alt=""></div>
<div class="design-strip right" aria-hidden="true"><img src="images/complaint_design.png" alt=""></div>

<nav class="navbar">
  <a href="dashboard.php" class="navbar-logo">
    <img src="images/logo_pink.png" alt="CitiServe">
  </a>

  <div class="navbar-nav">
    <a href="dashboard.php" class="nav-item">
      <span class="nav-text">Dashboard</span>
    </a>

    <div class="nav-item has-dropdown" id="navDocReq">
      <span class="nav-text">Document Requests</span>
      <span class="nav-chevron">⏷</span>
      <div class="nav-dropdown">
        <a href="request_document.php" class="nav-dropdown-item">Request Document</a>
        <a href="my_requests.php" class="nav-dropdown-item">My Requests</a>
      </div>
    </div>

    <div class="nav-item has-dropdown active" id="navComplaint">
      <span class="nav-text">Complaint Management</span>
      <span class="nav-chevron">⏷</span>
      <div class="nav-dropdown">
        <a href="complaints.php" class="nav-dropdown-item">Submit Complaint</a>
        <a href="my_complaints.php" class="nav-dropdown-item">My Complaints</a>
      </div>
    </div>
  </div>

  <div class="navbar-right">
    <button class="notif-btn" id="notifBtn"
      data-has-notif="<?= $hasNotif ? '1' : '0' ?>"
      data-img-on="images/with_notif.png"
      data-img-off="images/no_notif.png"
      data-img-active="images/select_notif.png"
      title="Notifications">
      <img id="notifIcon"
        src="<?= $hasNotif ? 'images/with_notif.png' : 'images/no_notif.png' ?>"
        alt="Notifications">
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
                  <div class="notif-msg"><?= $n['message'] ?></div>
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

    <div class="profile-pill" id="profilePill">
      <div class="profile-avatar">
        <img src="<?= !empty($user['avatar']) ? htmlspecialchars($user['avatar']) : 'images/profile_icon.png' ?>" alt="Profile">
      </div>
      <span class="profile-name"><?= htmlspecialchars($user['first_name']) ?> D.</span>
      <span class="profile-chevron"><img src="images/profile_dropdown.png" alt=""></span>
    </div>

    <div class="profile-panel" id="profilePanel">
      <div class="profile-panel-top">
        <div class="profile-panel-fullname"><?= htmlspecialchars($user['full_name']) ?></div>
        <div class="profile-panel-subtext">Resident • Brgy. Kalayaan</div>
      </div>

      <a href="profile.php" class="profile-panel-item">
        <img src="images/my_profile.png" alt="My Profile" class="profile-panel-icon1">
        <span>My Profile</span>
      </a>

      <a href="login.php" class="profile-panel-item logout">
        <img src="images/logout.png" alt="Logout" class="profile-panel-icon2">
        <span>Logout</span>
      </a>
    </div>
  </div>
</nav>

<div class="page-body">
  <div class="complaints-card">
    <div class="breadcrumb">
      <a href="complaint.php">Complaint Management</a>
      <span>></span>
      <span class="current">Submit Complaint</span>
    </div>

    <h1 class="page-title">Submit a Community Complaint</h1>
    <p class="page-subtitle">Select the category that best describes your concern.</p>

    <div class="reminders-wrap">
      <img src="images/complaint_reminders.png" alt="Important Reminders" class="reminders-img">
    </div>

    <div class="complaint-grid">
      <?php foreach ($complaintCategories as $item): ?>
            <a href="<?= $item['title'] === 'Anonymous Report' ? '#' : htmlspecialchars($item['link']) ?>"
            class="complaint-card-item <?= $item['title'] === 'Other Concerns' ? 'other-concerns-card' : '' ?>"
            <?= $item['title'] === 'Anonymous Report' ? 'id="anonymousBtn"' : '' ?>>
            <div class="complaint-card-subtle">
            <img src="<?= htmlspecialchars($item['subtle']) ?>" alt="">
          </div>

          <div class="complaint-card-main">
            <div class="complaint-icon-wrap">
              <img src="<?= htmlspecialchars($item['icon']) ?>" alt="" class="complaint-icon">
            </div>

            <div class="complaint-details">
              <div class="complaint-title"><?= htmlspecialchars($item['title']) ?></div>
              <div class="complaint-desc"><?= htmlspecialchars($item['desc']) ?></div>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>

    <div class="complaints-footer">
      <img src="images/citiserve_solo_pink.png" alt="CitiServe" class="footer-logo">
      <div class="footer-text"><span>CitiServe</span> © 2026. All rights reserved.</div>
    </div>
  </div>
</div>

<!-- ===================== ANONYMOUS MODAL ===================== -->
<div class="anon-modal" id="anonModal">
  <div class="anon-modal-box">

    <img src="images/anonymous_panel.png" alt="Anonymous Warning" class="anon-img">

    <div class="anon-actions">
    <div class="anon-actions">
      <img src="images/anonymous_back.png" id="anonBack" class="anon-btn" alt="Back">
      <img src="images/anonymous_continue.png" id="anonContinue" class="anon-btn" alt="Continue">
    </div>
    </div>
  </div>
</div>

<script src="JS/complaint.js"></script>
</body>
</html>