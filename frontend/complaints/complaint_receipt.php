<?php
$user = [
  'first_name' => 'Juan',
  'full_name'  => 'Juan Dela Cruz',
  'avatar'     => '',
];

$categoryKey = $_GET['category'] ?? 'road_infrastructure';

$categoryMap = [
  'road_infrastructure' => [
    'title' => 'Road / Infrastructure',
    'value' => 'Road / Infrastructure',
  ],
  'garbage_sanitation' => [
    'title' => 'Garbage / Sanitation',
    'value' => 'Garbage / Sanitation',
  ],
  'noise_disturbance' => [
    'title' => 'Noise Disturbance',
    'value' => 'Noise Disturbance',
  ],
  'traffic_parking' => [
    'title' => 'Traffic / Parking',
    'value' => 'Traffic / Parking',
  ],
  'environmental_tree_animal' => [
    'title' => 'Environmental / Tree / Animal',
    'value' => 'Environmental / Tree / Animal',
  ],
  'water_electricity_utilities' => [
    'title' => 'Water / Electricity / Utilities',
    'value' => 'Water / Electricity / Utilities',
  ],
  'community_social_issues' => [
    'title' => 'Community / Social Issues',
    'value' => 'Community / Social Issues',
  ],
  'other_concerns' => [
    'title' => 'Other Concerns',
    'value' => 'Other Concerns',
  ],
  'anonymous_report' => [
    'title' => 'Anonymous Report',
    'value' => 'Anonymous Report',
  ],
];

$currentCategory = $categoryMap[$categoryKey] ?? $categoryMap['road_infrastructure'];

$notifications = [
  [
    'id'         => 1,
    'section'    => 'new',
    'category'   => 'document',
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
    'message'    => 'Your complaint has been submitted.',
    'time_label' => '6h',
    'read'       => false,
    'link'       => 'my_complaints.php',
    'main_icon'  => 'images/citiserve_notif.png',
    'badge_icon' => 'images/complaint_notif.png',
  ],
];

$unreadCount   = count(array_filter($notifications, fn($n) => !$n['read']));
$hasNotif      = $unreadCount > 0;
$notifSections = ['new' => [], 'today' => [], 'earlier' => []];

foreach ($notifications as $n) {
  $notifSections[$n['section']][] = $n;
}


/* =========================== Sample submitted Values =========================== */

$referenceNumber = 'CMP-0000000001';
$submissionType  = ($categoryKey === 'anonymous_report') ? 'Anonymous' : 'Identified';
$dateSubmitted   = 'April 2, 2026';
$timeSubmitted   = '9:20 AM';
$statusImage     = 'images/complaint_received.png';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Complaint Submitted – CitiServe</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/complaint_receipt.css">
</head>
<body>

<div class="design-strip left" aria-hidden="true"><img src="images/complaint_form_confirmation_design.png" alt=""></div>
<div class="design-strip right" aria-hidden="true"><img src="images/complaint_form_confirmation_design.png" alt=""></div>

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
                  <img class="notif-icon-main" src="<?= htmlspecialchars($n['main_icon']) ?>" alt="">
                  <?php if (!empty($n['badge_icon'])): ?>
                    <img class="notif-icon-badge" src="<?= htmlspecialchars($n['badge_icon']) ?>" alt="">
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
  <div class="submitted-page-card">
    <div class="content-area">
      <div class="ticket-wrapper">

        <img src="images/complaint-receipt.png" alt="" class="ticket-bg">

        <div class="ticket-content">
          <div class="ticket-header">
            <img src="images/complaint_icon_success.png" alt="Success" class="success-icon">
            <div class="ticket-title">Complaint Submitted</div>
            <div class="ticket-subtitle">Your complaint has been received by the barangay.</div>
          </div>

          <div class="divider-dashed-wrap"></div>

          <div class="ref-box">
            <div class="ref-label">Complaint Reference Number</div>
            <div class="ref-number"><?= htmlspecialchars($referenceNumber) ?></div>
            <div class="ref-hint">Save this number for tracking your complaint.</div>
          </div>

          <div class="detail-row">
            <span class="detail-label">Category</span>
            <span class="detail-value"><?= htmlspecialchars($currentCategory['value']) ?></span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Submission Type</span>
            <span class="detail-value"><?= htmlspecialchars($submissionType) ?></span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Date</span>
            <span class="detail-value"><?= htmlspecialchars($dateSubmitted) ?></span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Time</span>
            <span class="detail-value"><?= htmlspecialchars($timeSubmitted) ?></span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Status</span>
            <span class="detail-value">
              <img src="<?= htmlspecialchars($statusImage) ?>" alt="Received" class="status-img">
            </span>
          </div>

          <div class="divider-solid"></div>

          <div class="next-title">What Happens Next</div>

          <div class="next-item">
            <img src="images/complaint_eye_icon.png" alt="" class="next-icon">
            <div class="next-text">Barangay staff will review your complaint and classify its severity.</div>
          </div>

          <div class="next-item">
            <img src="images/complaint_wallet.png" alt="" class="next-icon">
            <div class="next-text">A barangay official will be assigned to assess and investigate the complaint.</div>
          </div>

          <div class="next-item">
            <img src="images/complaint_bell_icon.png" alt="" class="next-icon">
            <div class="next-text">Status updates will be posted in your complaint history.</div>
          </div>

          <div class="next-item">
            <img src="images/complaint_claim.png" alt="" class="next-icon">
            <div class="next-text">Once resolved, you will receive a notification with the resolution details.</div>
          </div>

          <div class="divider-solid1"></div>

          <div class="btn-group">
            <a href="my_complaints.php" class="btn-img">
              <img src="images/complaint_my_complaints.png" alt="View My Complaints">
            </a>

            <a href="dashboard.php" class="btn-img">
              <img src="images/complaint_back_dashboard.png" alt="Back to Dashboard">
            </a>
          </div>
        </div>

        <img src="images/complaint_faded_logo.png" class="faded-logo" alt="">
      </div>
    </div>

    <div class="submitted-footer">
      <img src="images/citiserve_solo_pink.png" alt="CitiServe" class="footer-logo">
      <div class="footer-text"><span>CitiServe</span> © 2026. All rights reserved.</div>
    </div>
  </div>
</div>

<script src="js/complaint_receipt.js"></script>
</body>
</html>