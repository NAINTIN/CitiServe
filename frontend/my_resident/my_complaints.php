<?php
$user = [
  'first_name' => 'Juan',
  'full_name'  => 'Juan Dela Cruz',
  'avatar'     => '',
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

$complaints = [
  [
    'complaint_id'      => 'CMP-0000000008',
    'category'          => 'Garbage / Sanitation',
    'date'              => 'Apr 5, 2026',
    'type'              => 'Identified',
    'status'            => 'Received',
    'submitted_by'      => 'Juan Dela Cruz',
    'contact'           => '09171234567',
    'description'       => 'Uncollected garbage has been piling up near the roadside for several days and is causing foul odor in the area.',
    'location_text'     => 'Purok 2, Barangay Kalayaan, Angono, Rizal',
    'map_query'         => 'Purok 2, Barangay Kalayaan, Angono, Rizal',
    'evidence'          => 'garbage_report.jpg',
    'datetime_full'     => '04/05/2026, 9:20:04 AM',
  ],
  [
    'complaint_id'      => 'CMP-0000000007',
    'category'          => 'Anonymous Report',
    'date'              => 'Apr 5, 2026',
    'type'              => 'Anonymous',
    'status'            => 'Received',
    'submitted_by'      => 'Anonymous',
    'contact'           => 'Not provided',
    'description'       => 'There is repeated disturbance late at night coming from the covered court area.',
    'location_text'     => 'Covered Court, Barangay Kalayaan',
    'map_query'         => 'Covered Court, Barangay Kalayaan, Angono, Rizal',
    'evidence'          => '',
    'datetime_full'     => '04/05/2026, 8:14:16 AM',
  ],
  [
    'complaint_id'      => 'CMP-0000000006',
    'category'          => 'Road / Infrastructure',
    'date'              => 'Apr 5, 2026',
    'type'              => 'Identified',
    'status'            => 'Received',
    'submitted_by'      => 'Maria Santos',
    'contact'           => '09171234567',
    'description'       => 'There is a large pothole on the main road near the elementary school. It has been causing accidents for local residents.',
    'location_text'     => 'Near Brgy. Kalayaan Elementary School, Main Road',
    'map_query'         => 'Barangay Kalayaan Elementary School, Angono, Rizal',
    'evidence'          => 'pothole_photo.jpg',
    'datetime_full'     => '04/05/2025, 6:00:00 PM',
  ],
  [
    'complaint_id'      => 'CMP-0000000005',
    'category'          => 'Road / Infrastructure',
    'date'              => 'Apr 5, 2026',
    'type'              => 'Identified',
    'status'            => 'Processing',
    'submitted_by'      => 'Maria Santos',
    'contact'           => '09171234567',
    'description'       => 'Road surface is damaged and currently under assessment by barangay personnel.',
    'location_text'     => 'Main Road, Barangay Kalayaan',
    'map_query'         => 'Main Road, Barangay Kalayaan, Angono, Rizal',
    'evidence'          => '',
    'datetime_full'     => '04/05/2025, 6:00:00 PM',
  ],
  [
    'complaint_id'      => 'CMP-0000000004',
    'category'          => 'Anonymous Report',
    'date'              => 'Mar 18, 2026',
    'type'              => 'Identified',
    'status'            => 'Received',
    'submitted_by'      => 'Anonymous',
    'contact'           => 'Not provided',
    'description'       => 'Suspicious activity has been observed near the alley during late evenings.',
    'location_text'     => 'Alley beside the public market',
    'map_query'         => 'Public Market, Angono, Rizal',
    'evidence'          => '',
    'datetime_full'     => '03/18/2026, 5:42:10 PM',
  ],
  [
    'complaint_id'      => 'CMP-0000000003',
    'category'          => 'Garbage / Sanitation',
    'date'              => 'Mar 15, 2026',
    'type'              => 'Identified',
    'status'            => 'Resolved',
    'submitted_by'      => 'Pedro Reyes',
    'contact'           => '09179876543',
    'description'       => 'Garbage collection issue was reported and has already been resolved.',
    'location_text'     => 'Near Barangay Hall',
    'map_query'         => 'Barangay Hall, Angono, Rizal',
    'evidence'          => 'trash_area.png',
    'datetime_full'     => '03/15/2026, 1:22:08 PM',
  ],
  [
    'complaint_id'      => 'CMP-0000000002',
    'category'          => 'Noise Disturbance',
    'date'              => 'Mar 10, 2026',
    'type'              => 'Identified',
    'status'            => 'Rejected',
    'submitted_by'      => 'Ana Cruz',
    'contact'           => '09174561234',
    'description'       => 'Complaint was filed regarding loud music during midnight.',
    'location_text'     => 'Block 3, Riverside Homes',
    'map_query'         => 'Riverside Homes, Angono, Rizal',
    'evidence'          => '',
    'datetime_full'     => '03/10/2026, 10:10:55 PM',
  ],
  [
    'complaint_id'      => 'CMP-0000000001',
    'category'          => 'Community / Social Issues',
    'date'              => 'Mar 8, 2026',
    'type'              => 'Identified',
    'status'            => 'Processing',
    'submitted_by'      => 'Liza Gomez',
    'contact'           => '09175550001',
    'description'       => 'Community dispute involving repeated verbal confrontation between neighbors.',
    'location_text'     => 'Purok 5, Barangay Kalayaan',
    'map_query'         => 'Purok 5, Barangay Kalayaan, Angono, Rizal',
    'evidence'          => '',
    'datetime_full'     => '03/08/2026, 3:08:31 PM',
  ],
];

function getComplaintTypeImage($type) {
  return match (strtolower($type)) {
    'identified' => 'images/my_complaint_identified.png',
    'anonymous'  => 'images/my_complaint_anonymous.png',
    default      => 'images/my_complaint_identified.png',
  };
}

function getComplaintStatusImage($status) {
  return match (strtolower($status)) {
    'received'   => 'images/my_complaint_received.png',
    'processing' => 'images/my_complaint_processing.png',
    'resolved'   => 'images/my_complaint_resolved.png',
    'rejected'   => 'images/my_complaint_rejected.png',
    default      => 'images/my_complaint_received.png',
  };
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Complaints – CitiServe</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/my_complaints.css">
</head>
<body>

<div class="design-strip left" aria-hidden="true"><img src="images/dashboard_design.png" alt=""></div>
<div class="design-strip right" aria-hidden="true"><img src="images/dashboard_design.png" alt=""></div>

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
        <a href="document_request.php" class="nav-dropdown-item">Request Document</a>
        <a href="my_requests.php" class="nav-dropdown-item">My Requests</a>
      </div>
    </div>

    <div class="nav-item has-dropdown active" id="navComplaint">
      <span class="nav-text">Complaint Management</span>
      <span class="nav-chevron">⏷</span>
      <div class="nav-dropdown">
        <a href="complaint.php" class="nav-dropdown-item">Submit Complaint</a>
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
  <div class="complaints-page-card">
    <img src="images/flower_design.png" alt="" class="flower-design top-flower">
    <img src="images/flower_design.png" alt="" class="flower-design bottom-flower">

    <div class="breadcrumb">
      <a href="complaint.php">Complaint Management</a>
      <span>></span>
      <span class="current">My Complaints</span>
    </div>

    <div class="page-head">
      <h1 class="page-title">My Complaints</h1>
      <p class="page-subtitle">Track the status of your submitted complaints.</p>
    </div>

    <div class="toolbar">
      <div class="toolbar-left">
        <div class="search-wrap">
          <img src="images/search_icon.png" alt="" class="search-icon">
          <input type="text" id="searchInput" placeholder="Search by category or request ID...">
        </div>

        <button type="button" class="clear-btn" id="clearBtn">
          <img src="images/my_request_clear.png" alt="Clear">
        </button>
      </div>

      <div class="toolbar-right">
        <div class="custom-filter" id="statusFilter">
          <button type="button" class="filter-box" id="filterBox">
            <span id="selectedStatusText">All</span>
            <span class="filter-arrow">▾</span>
          </button>

          <div class="filter-dropdown" id="filterDropdown">
            <div class="filter-option active" data-value="all">All</div>
            <div class="filter-option" data-value="received">Received</div>
            <div class="filter-option" data-value="processing">Processing</div>
            <div class="filter-option" data-value="resolved">Resolved</div>
            <div class="filter-option" data-value="rejected">Rejected</div>
          </div>
        </div>

        <a href="complaint.php" class="new-complaint-btn">
          <img src="images/my_complaint_new_complaint.png" alt="New Complaint">
        </a>
      </div>
    </div>

    <div class="complaints-table-card">
      <div class="complaints-table-head">
        <div>Request ID</div>
        <div>Category</div>
        <div>Date</div>
        <div>Type</div>
        <div>Status</div>
        <div></div>
      </div>

      <?php if (empty($complaints)): ?>
        <div class="complaints-empty">
          <img src="images/my_complaint_empty_icon.png" alt="" class="empty-icon">
          <div class="empty-text">No complaints submitted yet</div>
        </div>
      <?php else: ?>
        <div class="complaints-table-body" id="complaintsTableBody">
          <?php foreach ($complaints as $complaint): ?>
            <div class="complaint-row"
                data-request-id="<?= htmlspecialchars($complaint['complaint_id']) ?>"
                data-category="<?= htmlspecialchars($complaint['category']) ?>"
                data-date="<?= htmlspecialchars($complaint['date']) ?>"
                data-type="<?= htmlspecialchars($complaint['type']) ?>"
                data-status="<?= htmlspecialchars($complaint['status']) ?>"
                data-status-image="<?= htmlspecialchars(getComplaintStatusImage($complaint['status'])) ?>"
                data-submitted-by="<?= htmlspecialchars($complaint['submitted_by']) ?>"
                data-contact="<?= htmlspecialchars($complaint['contact']) ?>"
                data-description="<?= htmlspecialchars($complaint['description']) ?>"
                data-location-text="<?= htmlspecialchars($complaint['location_text']) ?>"
                data-map-query="<?= htmlspecialchars($complaint['map_query']) ?>"
                data-evidence="<?= htmlspecialchars($complaint['evidence']) ?>"
                data-datetime-full="<?= htmlspecialchars($complaint['datetime_full']) ?>">

              <div class="complaint-cell complaint-id"><?= htmlspecialchars($complaint['complaint_id']) ?></div>
              <div class="complaint-cell complaint-category"><?= htmlspecialchars($complaint['category']) ?></div>
              <div class="complaint-cell complaint-date"><?= htmlspecialchars($complaint['date']) ?></div>
              <div class="complaint-cell complaint-type">
                <img src="<?= htmlspecialchars(getComplaintTypeImage($complaint['type'])) ?>" alt="<?= htmlspecialchars($complaint['type']) ?>" class="complaint-type-img">
              </div>
              <div class="complaint-cell complaint-status">
                <img src="<?= htmlspecialchars(getComplaintStatusImage($complaint['status'])) ?>" alt="<?= htmlspecialchars($complaint['status']) ?>" class="complaint-status-img">
              </div>
              <div class="complaint-cell details-cell">
                <button type="button" class="details-btn">Details</button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="page-footer">
      <span><strong>CitiServe</strong> © 2026. All rights reserved.</span>
    </div>
  </div>
</div>


<!-- ===================== COMPLAINT DETAILS MODAL ===================== -->
<div class="complaint-details-modal" id="complaintDetailsModal">
  <div class="complaint-details-box" id="complaintDetailsBox">
    <button type="button" class="complaint-details-close" id="complaintDetailsClose" aria-label="Close">×</button>

    <div class="complaint-details-title">Complaint Details</div>
    <div class="complaint-details-divider"></div>

    <div class="complaint-details-body">
      <div class="complaint-top-grid">
        <div class="complaint-top-col">
          <div class="complaint-detail-row compact">
            <div class="complaint-detail-label">Complaint ID</div>
            <div class="complaint-detail-value left" id="modalComplaintId">CMP-0000000001</div>
          </div>

          <div class="complaint-detail-row compact">
            <div class="complaint-detail-label">Category</div>
            <div class="complaint-detail-value left" id="modalComplaintCategory">Road / Infrastructure</div>
          </div>

          <div class="complaint-detail-row compact">
            <div class="complaint-detail-label">Submitted By</div>
            <div class="complaint-detail-value left" id="modalComplaintSubmittedBy">Maria Santos</div>
          </div>
        </div>

        <div class="complaint-top-col">
          <div class="complaint-detail-row compact">
            <div class="complaint-detail-label">Status</div>
            <div class="complaint-detail-status-wrap">
              <img src="images/my_complaint_processing.png" alt="Status" id="modalComplaintStatusImage" class="complaint-detail-status-img">
            </div>
          </div>

          <div class="complaint-detail-row compact">
            <div class="complaint-detail-label">Date Submitted</div>
            <div class="complaint-detail-value left" id="modalComplaintDateSubmitted">04/05/2025, 6:00:00 PM</div>
          </div>

          <div class="complaint-detail-row compact">
            <div class="complaint-detail-label">Contact</div>
            <div class="complaint-detail-value left" id="modalComplaintContact">09171234567</div>
          </div>
        </div>
      </div>

      <div class="complaint-section">
        <div class="complaint-detail-label">Description</div>
        <div class="complaint-description-box" id="modalComplaintDescription">
          There is a large pothole on the main road near the elementary school. It has been causing accidents for local residents.
        </div>
      </div>

      <div class="complaint-section">
        <div class="complaint-detail-label">Location</div>
        <div class="complaint-location-text" id="modalComplaintLocationText">
          Near Brgy. Kalayaan Elementary School, Main Road
        </div>
        <div class="complaint-map-wrap">
          <iframe
            id="modalComplaintMap"
            class="complaint-map-frame"
            src=""
            loading="lazy"
            allowfullscreen
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>
      </div>

      <div class="complaint-section complaint-evidence-section" id="complaintEvidenceSection">
        <div class="complaint-detail-label">Evidence</div>
        <div class="complaint-evidence-box">
          <div class="complaint-evidence-file" id="modalComplaintEvidence">pothole_photo.jpg</div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="js/my_complaints.js"></script>
</body>
</html>