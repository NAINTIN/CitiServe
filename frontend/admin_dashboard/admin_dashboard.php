<?php
$user = [
  'first_name' => 'Jasmin',
  'full_name'  => 'Jasmin M.',
  'barangay'   => 'Barangay Kalayaan, Angono, Rizal',
  'avatar'     => '',
];

$stats = [
  'accounts_for_approval' => 0,
  'received_doc_requests' => 0,
  'received_complaints'   => 0,
  'registered_residents'  => 0,
];

$recentRequests = [
  [
    'name' => 'Caryll Armayan',
    'document' => 'Barangay Clearance',
    'date' => 'Apr 5, 2026',
    'time' => '10:00 AM',
    'status_img' => 'my_request_pending.png'
  ],
  [
    'name' => 'Miguel Santos',
    'document' => 'Certificate of Residency',
    'date' => 'Apr 4, 2026',
    'time' => '9:15 AM',
    'status_img' => 'my_request_received.png'
  ],
  [
    'name' => 'Angela Cruz',
    'document' => 'Barangay ID',
    'date' => 'Apr 3, 2026',
    'time' => '2:30 PM',
    'status_img' => 'my_request_claimable.png'
  ],
];

$recentComplaints = [
  [
    'name' => 'Nicole Arambulo',
    'category' => 'Road / Infrastructure',
    'date' => 'Apr 5, 2026',
    'time' => '8:30 AM',
    'status_img' => 'my_complaint_processing.png'
  ],
  [
    'name' => 'John Reyes',
    'category' => 'Garbage / Sanitation',
    'date' => 'Apr 4, 2026',
    'time' => '1:10 PM',
    'status_img' => 'my_complaint_received.png'
  ],
  [
    'name' => 'Maria Lopez',
    'category' => 'Noise Disturbance',
    'date' => 'Apr 3, 2026',
    'time' => '11:45 AM',
    'status_img' => 'my_complaint_resolved.png'
  ],
];

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

$testEmpty = false; // change to false kapag may laman

if ($testEmpty) {
  $announcement = [
    'id' => null,
    'img' => '',
    'fb_link' => '',
  ];
} else {
  $announcement = [
    'id' => 1,
    'img' => 'images/announcement_stack.png',
    'fb_link' => 'https://www.facebook.com/share/p/18HBDUBBBX/',
  ];
}


  date_default_timezone_set('Asia/Manila');
  $hour = (int) date('G');
    if ($hour >= 5 && $hour < 12) {
      $greeting = 'Good morning!';
    } elseif ($hour >= 12 && $hour < 18) {
      $greeting = 'Good afternoon!';
    } else {
      $greeting = 'Good evening!';
    }

    $hasAnnouncement = !empty($announcement['img']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>CitiServe - Admin Dashboard</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="CSS/admin_dashboard.css">
</head>
<body>

<div class="design-strip left" aria-hidden="true"><img src="images/dashboard_design.png" alt=""></div>
<div class="design-strip right" aria-hidden="true"><img src="images/dashboard_design.png" alt=""></div>

<nav class="navbar">
  <a href="admin_dashboard.php" class="navbar-logo">
    <img src="images/logo_pink.png" alt="CitiServe">
  </a>

  <div class="navbar-nav admin-nav">
    <a href="admin_dashboard.php" class="nav-item active">
      <span class="nav-text">Dashboard</span>
    </a>

    <a href="admin_document_request.php" class="nav-item">
      <span class="nav-text">Document Requests</span>
    </a>

    <a href="admin_complaints.php" class="nav-item">
      <span class="nav-text">Complaints</span>
    </a>

    <div class="nav-item has-dropdown" id="navUserManagement">
      <span class="nav-text">User Management</span>
      <span class="nav-chevron">⏷</span>
      <div class="nav-dropdown">
        <a href="admin_residents.php" class="nav-dropdown-item">Residents</a>
        <a href="admin_staff.php" class="nav-dropdown-item">Staff</a>
      </div>
    </div>

    <a href="admin_account_verification.php" class="nav-item">
      <span class="nav-text">Account Verification</span>
    </a>

    <a href="admin_reports.php" class="nav-item">
      <span class="nav-text">Reports</span>
    </a>
  </div>

  <div class="navbar-right admin-navbar-right">
    <div class="profile-pill" id="profilePill">
      <div class="profile-avatar">
        <img src="<?= !empty($user['avatar']) ? htmlspecialchars($user['avatar']) : 'images/admin_dummy_icon.png' ?>" alt="Admin Profile">
      </div>
      <span class="profile-name"><?= htmlspecialchars($user['first_name']) ?> M.</span>
      <span class="profile-chevron"><img src="images/profile_dropdown.png" alt=""></span>
    </div>

    <div class="profile-panel" id="profilePanel">
      <div class="profile-panel-top">
        <div class="profile-panel-fullname"><?= htmlspecialchars($user['full_name']) ?></div>
        <div class="profile-panel-subtext">Admin • Brgy. Kalayaan</div>
      </div>

      <a href="admin_profile.php" class="profile-panel-item">
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
  <div class="dashboard-card">
    <div class="dashboard-grid">

<div class="left-col">
  <div class="greeting-section">
    <div class="greeting-top">
      <?= htmlspecialchars($greeting) ?>
      <img src="images/welcome_emoji.png" alt="👋" class="greeting-emoji">
    </div>

    <div class="greeting-name">Welcome, Jasmine Nicole</div>
    <div class="greeting-sub"><?= htmlspecialchars($user['barangay']) ?> — Staff Overview</div>

    <div class="quick-actions-label">QUICK ACTIONS</div>
  </div>

  <div class="action-btns admin-action-btns">
    <a href="admin_document_request.php" class="action-btn1">
      <img src="images/manage_request.png" alt="Manage Requests">
    </a>

    <a href="admin_complaints.php" class="action-btn2">
      <img src="images/manage_document.png" alt="Manage Complaints">
    </a>
  </div>

    <div class="admin-announcement-card <?= $hasAnnouncement ? 'has-announcement' : 'is-empty' ?>">
    <div class="admin-ann-header">
        <div class="admin-ann-title">
        <img src="images/announcement_board_icon.png" alt="" class="admin-ann-icon">
        <span>Announcement Board</span>
        </div>

        <?php if (!$hasAnnouncement): ?>
        <button type="button" class="announcement-img-btn add-announcement-btn">
            <img src="images/announcement_add_active.png" alt="Add Announcement">
        </button>
        <?php else: ?>
        <button type="button" class="announcement-img-btn add-announcement-btn inactive" disabled>
            <img src="images/announcement_add_inactive.png" alt="Add Announcement">
        </button>
        <?php endif; ?>
    </div>

    <?php if ($hasAnnouncement): ?>
        <div class="admin-ann-preview">
        <img src="<?= htmlspecialchars($announcement['img']) ?>" class="ann-main" alt="Announcement">

        <a href="<?= htmlspecialchars($announcement['fb_link']) ?>" class="ann-hover-link" target="_blank">
            View on Facebook
        </a>
        </div>
    <?php else: ?>
        <div class="admin-ann-empty">
        <img src="images/announcement_empty_icon.png" alt="" class="admin-ann-empty-icon">
        <p>No announcement posted. Click "Add Announcement" to post one.</p>
        <span>Only one announcement can be active at a time.</span>
        </div>
    <?php endif; ?>

    <div class="admin-ann-footer">
        <span class="ann-posted">Posted: <?= $hasAnnouncement ? '04-18-2026' : 'N/A' ?></span>

        <div class="ann-actions">
        <button type="button" class="announcement-action-img-btn <?= !$hasAnnouncement ? 'disabled' : '' ?>" <?= !$hasAnnouncement ? 'disabled' : '' ?>>
            <img src="images/announcement_edit.png" alt="Edit">
        </button>

        <button type="button" class="announcement-action-img-btn <?= !$hasAnnouncement ? 'disabled' : '' ?>" <?= !$hasAnnouncement ? 'disabled' : '' ?>>
            <img src="images/announcement_remove.png" alt="Remove">
        </button>
        </div>
    </div>
    </div>
</div>   

      <div class="right-col">
        <div class="stat-row admin-stat-row">
          <div class="stat-card stat-approval">
            <a href="admin_account_verification.php" class="stat-arrow">
              <img src="images/dashboard_arrow1.png" alt="Go">
            </a>
            <div class="stat-label">Accounts<br>for Approval</div>
            <div class="stat-value"><?= (int)$stats['accounts_for_approval'] ?></div>
          </div>

          <div class="stat-card stat-docs">
            <a href="admin_document_request.php" class="stat-arrow">
              <img src="images/dashboard_arrow1.png" alt="Go">
            </a>
            <div class="stat-label">Received<br>Doc Requests</div>
            <div class="stat-value"><?= (int)$stats['received_doc_requests'] ?></div>
          </div>

          <div class="stat-card stat-complaints">
            <a href="admin_complaints.php" class="stat-arrow">
              <img src="images/dashboard_arrow1.png" alt="Go">
            </a>
            <div class="stat-label">Received<br>Complaints</div>
            <div class="stat-value"><?= (int)$stats['received_complaints'] ?></div>
          </div>

          <div class="stat-card stat-residents">
            <a href="admin_residents.php" class="stat-arrow">
              <img src="images/dashboard_arrow.png" alt="Go">
            </a>
            <div class="stat-label">Registered<br>Residents</div>
            <div class="stat-value"><?= (int)$stats['registered_residents'] ?></div>
          </div>
        </div>

        <div class="admin-panels-col">
          <div class="recent-panel admin-recent-panel">
            <div class="panel-header1">
              <div class="panel-title">
                <img src="images/recent_request.png" class="panel-icon" alt="">
                Recent Document Requests
              </div>
              <a href="admin_document_request.php" class="panel-viewall1">
                <img src="images/viewall_pink.png" alt="View all">
              </a>
            </div>

            <div class="panel-body">
            <?php if (!empty($recentRequests)): ?>

                <?php foreach (array_slice($recentRequests, 0, 3) as $req): ?>
                <div class="recent-entry">
                    <div class="recent-entry-left">

                    <div class="recent-entry-icon-wrap">
                        <img src="<?= $requestIcons[$req['document']] ?? 'images/default.png' ?>" class="recent-entry-icon">
                    </div>

                    <div class="recent-entry-details">
                        <div class="recent-entry-title recent-entry-title-request">
                        <?= htmlspecialchars($req['name']) ?> - <?= htmlspecialchars($req['document']) ?>
                        </div>

                        <div class="recent-entry-meta">
                        <?= htmlspecialchars($req['date']) ?>
                        <span class="recent-entry-separator">|</span>
                        <?= htmlspecialchars($req['time']) ?>
                        </div>
                    </div>

                    </div>

                    <!-- STATUS BADGE -->
                    <img src="images/<?= $req['status_img'] ?>" class="status-badge-img">
                </div>
                <?php endforeach; ?>

            <?php else: ?>
                <img src="images/recent_request_main.png" class="panel-empty-main" alt="">
                <span class="panel-empty-text">No document requests yet</span>
            <?php endif; ?>
            </div>
          </div>

          <div class="recent-panel complaints admin-recent-panel">
            <div class="panel-header2">
              <div class="panel-title">
                <img src="images/recent_complaint.png" class="panel-icon" alt="">
                Recent Complaints
              </div>
              <a href="admin_complaints.php" class="panel-viewall2">
                <img src="images/viewall_yellow.png" alt="View all">
              </a>
            </div>

            <div class="panel-body">
            <?php if (!empty($recentComplaints)): ?>

                <?php foreach (array_slice($recentComplaints, 0, 3) as $cmp): ?>
                <div class="recent-entry">
                    <div class="recent-entry-left">

                    <div class="recent-entry-icon-wrap">
                        <img src="<?= $complaintIcons[$cmp['category']] ?? 'images/default.png' ?>" class="recent-entry-icon">
                    </div>

                    <div class="recent-entry-details">
                        <div class="recent-entry-title recent-entry-title-complaint">
                        <?= htmlspecialchars($cmp['name']) ?> - <?= htmlspecialchars($cmp['category']) ?>
                        </div>

                        <div class="recent-entry-meta">
                        <?= htmlspecialchars($cmp['date']) ?>
                        <span class="recent-entry-separator">|</span>
                        <?= htmlspecialchars($cmp['time']) ?>
                        </div>
                    </div>

                    </div>

                    <!-- STATUS BADGE -->
                    <img src="images/<?= $cmp['status_img'] ?>" class="status-badge-img">
                </div>
                <?php endforeach; ?>

            <?php else: ?>
                <img src="images/recent_complaint_main.png" class="panel-empty-main" alt="">
                <span class="panel-empty-text">No complaints submitted yet</span>
            <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

    <div class="modal-overlay" id="announcementModal">
    <div class="announcement-modal">

        <!-- HEADER -->
        <div class="announcement-modal-header">
        <div class="announcement-modal-title">
            <img src="images/announcement_board_icon.png">
            <span>Announcement Board</span>
        </div>

        <button class="announcement-add-btn">
            <img src="images/announcement_add_active.png">
        </button>
        </div>

        <!-- BODY (EMPTY STATE) -->
        <div class="announcement-modal-body">
        <img src="images/announcement_empty_icon.png" class="announcement-empty-icon">

        <div class="announcement-empty-text">
            No announcement posted. Click "Add Announcement" to post one.
        </div>

        <div class="announcement-empty-sub">
            Only one announcement can be active at a time.
        </div>
        </div>

        <!-- FOOTER -->
        <div class="announcement-modal-footer">
        <span class="announcement-posted">Posted: N/A</span>

        <div class="announcement-actions">
            <button class="announcement-action disabled">
            <img src="images/announcement_edit.png">
            </button>

            <button class="announcement-action disabled">
            <img src="images/announcement_remove.png">
            </button>
        </div>
        </div>
    </div>
    </div>

    <!-- ADD ANNOUNCEMENT MODAL -->
    <div class="modal-overlay" id="addAnnouncementModal">
        <div class="add-announcement-modal">

            <div class="add-ann-header">
            <img src="images/add_announcement_icon.png" alt="" class="add-ann-icon">
            <span>Add Announcement</span>
            </div>

            <div class="add-ann-body">
            <label class="add-ann-label">
                Upload Image <span>(from your device)</span> <b>*</b>
            </label>

            <label class="upload-ann-box" for="announcementImage">
                <input type="file" id="announcementImage" accept="image/*" hidden>
                <img src="images/add_announcement_icon_pic.png" alt="" class="upload-cloud">
                <p>Click to upload image from your device</p>
            </label>

            <label class="add-ann-label fb-label">
                Facebook Post Link <b>*</b>
            </label>

            <input type="url" class="fb-link-input" id="fbPostLink" placeholder="https://www.facebook.com/...">

            <div class="add-ann-actions">
                <button type="button" class="add-ann-img-btn" id="cancelAddAnnouncement">
                <img src="images/cancel_add_announcement.png" alt="Cancel">
                </button>

                <button type="button" class="add-ann-img-btn" id="postAnnouncement">
                <img src="images/post_announcement.png" alt="Post Announcement">
                </button>
            </div>
            </div>
        </div>
    </div>

    <!-- EDIT ANNOUNCEMENT MODAL -->
    <div class="modal-overlay" id="editAnnouncementModal">
    <div class="add-announcement-modal">

        <div class="add-ann-header">
        <img src="images/add_announcement_icon.png" alt="" class="add-ann-icon">
        <span>Edit Announcement</span>
        </div>

        <div class="add-ann-body">
        <label class="add-ann-label">
            Upload Image <span>(from your device)</span> <b>*</b>
        </label>

        <label class="upload-ann-box edit-upload-ann-box has-image" for="editAnnouncementImage">
            <input type="file" id="editAnnouncementImage" accept="image/*" hidden>

            <img src="<?= htmlspecialchars($announcement['img']) ?>" class="upload-ann-preview">

            <div class="change-image-label">Click to change image</div>
        </label>

        <label class="add-ann-label fb-label">
            Facebook Post Link <b>*</b>
        </label>

        <input 
            type="url" 
            class="fb-link-input" 
            id="editFbPostLink"
            value="<?= htmlspecialchars($announcement['fb_link']) ?>"
        >

        <div class="add-ann-actions">
            <button type="button" class="add-ann-img-btn" id="cancelEditAnnouncement">
            <img src="images/cancel_add_announcement.png" alt="Cancel">
            </button>

            <button type="button" class="add-ann-img-btn" id="saveAnnouncementChanges">
            <img src="images/save_announcement_changes.png" alt="Save Changes">
            </button>
        </div>
        </div>

    </div>
    </div>

    <script>
        const hasAnnouncement = <?= $hasAnnouncement ? 'true' : 'false' ?>;
    </script>

<script src="JS/admin_dashboard.js"></script>
</body>
</html>