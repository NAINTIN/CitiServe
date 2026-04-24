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

/* ========================= REQUEST DATA =========================
   Leave this empty for now, like you asked.
   Later, dito mo ilalagay yung real requests from DB.
================================================================ */
$requests = [
  [
    'request_id'        => 'DOC-0000000008',
    'document'          => 'Barangay Business Clearance',
    'date'              => 'Apr 5, 2026',
    'fee'               => '₱150.00',
    'payment'           => 'GCash',
    'status'            => 'Received',
    'datetime_full'     => '04/05/2026, 9:20:04 AM',
    'reference_number'  => '9301872639188',
  ],
  [
    'request_id'        => 'DOC-0000000007',
    'document'          => 'Solo Parent Certificate',
    'date'              => 'Apr 4, 2026',
    'fee'               => '₱50.00',
    'payment'           => 'GCash',
    'status'            => 'Received',
    'datetime_full'     => '04/04/2026, 8:14:16 AM',
    'reference_number'  => '8301872639187',
  ],
  [
    'request_id'        => 'DOC-0000000006',
    'document'          => 'Barangay Business Clearance',
    'date'              => 'Apr 3, 2026',
    'fee'               => '₱150.00',
    'payment'           => 'GCash',
    'status'            => 'Received',
    'datetime_full'     => '04/03/2026, 11:32:27 AM',
    'reference_number'  => '7301872639186',
  ],
  [
    'request_id'        => 'DOC-0000000005',
    'document'          => 'Barangay Clearance',
    'date'              => 'Apr 1, 2026',
    'fee'               => '₱50.00',
    'payment'           => 'GCash',
    'status'            => 'Received',
    'datetime_full'     => '04/01/2026, 6:26:04 PM',
    'reference_number'  => '3031872639187',
  ],
  [
    'request_id'        => 'DOC-0000000004',
    'document'          => 'Barangay Clearance',
    'date'              => 'Mar 20, 2026',
    'fee'               => '₱50.00',
    'payment'           => 'GCash',
    'status'            => 'Claimable',
    'datetime_full'     => '03/20/2026, 2:15:22 PM',
    'reference_number'  => '4031872639184',
  ],
  [
    'request_id'        => 'DOC-0000000003',
    'document'          => 'Certificate of Residency',
    'date'              => 'Mar 22, 2026',
    'fee'               => '₱30.00',
    'payment'           => 'Maya',
    'status'            => 'Pending',
    'datetime_full'     => '03/22/2026, 10:05:48 AM',
    'reference_number'  => '5031872639183',
  ],
  [
    'request_id'        => 'DOC-0000000002',
    'document'          => 'Barangay ID',
    'date'              => 'Mar 25, 2026',
    'fee'               => '₱75.00',
    'payment'           => 'InstaPay',
    'status'            => 'Received',
    'datetime_full'     => '03/25/2026, 1:09:36 PM',
    'reference_number'  => '6031872639182',
  ],
  [
    'request_id'        => 'DOC-0000000001',
    'document'          => 'Certificate of Indigency',
    'date'              => 'Mar 15, 2026',
    'fee'               => '₱20.00',
    'payment'           => 'GCash',
    'status'            => 'Rejected',
    'datetime_full'     => '03/15/2026, 9:11:03 AM',
    'reference_number'  => '7031872639181',
  ],
];

/*
Example row format for later:
$requests = [
  [
    'request_id' => 'DOC-0000000008',
    'document'   => 'Barangay Business Clearance',
    'date'       => 'Apr 5, 2026',
    'fee'        => '₱150.00',
    'payment'    => 'GCash',
    'status'     => 'Received'
  ],
];
*/

function getStatusImage($status) {
  return match (strtolower($status)) {
    'received'  => 'images/my_request_received.png',
    'claimable' => 'images/my_request_claimable.png',
    'pending'   => 'images/my_request_pending.png',
    'rejected'  => 'images/my_request_rejected.png',
    'released'  => 'images/my_request_claimable.png',
    default     => 'images/my_request_received.png',
  };
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Requests – CitiServe</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/my_requests.css">
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

    <div class="nav-item has-dropdown active" id="navDocReq">
      <span class="nav-text">Document Requests</span>
      <span class="nav-chevron">⏷</span>
      <div class="nav-dropdown">
        <a href="document_request.php" class="nav-dropdown-item">Request Document</a>
        <a href="my_requests.php" class="nav-dropdown-item">My Requests</a>
      </div>
    </div>

    <div class="nav-item has-dropdown" id="navComplaint">
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
  <div class="requests-page-card">
    <img src="images/flower_design.png" alt="" class="flower-design top-flower">
    <img src="images/flower_design.png" alt="" class="flower-design bottom-flower">

    <div class="breadcrumb">
      <a href="document_request.php">Document Requests</a>
      <span>></span>
      <span class="current">My Requests</span>
    </div>

    <div class="page-head">
      <h1 class="page-title">My Document Requests</h1>
      <p class="page-subtitle">Track the status of your document requests.</p>
    </div>

    <div class="toolbar">
      <div class="toolbar-left">
        <div class="search-wrap">
          <img src="images/search_icon.png" alt="" class="search-icon">
          <input type="text" id="searchInput" placeholder="Search by document type or request ID...">
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
            <div class="filter-option" data-value="pending">Pending</div>
            <div class="filter-option" data-value="claimable">Claimable</div>
            <div class="filter-option" data-value="rejected">Rejected</div>
            <div class="filter-option" data-value="released">Released</div>
          </div>
        </div>

        <a href="document_request.php" class="new-request-btn">
          <img src="images/my_request_new_request.png" alt="New Request">
        </a>
      </div>
    </div>

    <div class="requests-table-card">
      <div class="requests-table-head">
        <div>Request ID</div>
        <div>Document</div>
        <div>Date</div>
        <div>Fee</div>
        <div>Payment</div>
        <div>Status</div>
        <div></div>
      </div>

      <?php if (empty($requests)): ?>
        <div class="requests-empty">
          <img src="images/recent_request_main.png" alt="" class="empty-icon">
          <div class="empty-text">No document requests yet</div>
        </div>
      <?php else: ?>
        <div class="requests-table-body" id="requestsTableBody">
          <?php foreach ($requests as $request): ?>
            <div class="request-row"
                data-request-id="<?= htmlspecialchars($request['request_id']) ?>"
                data-document="<?= htmlspecialchars($request['document']) ?>"
                data-date="<?= htmlspecialchars($request['date']) ?>"
                data-fee="<?= htmlspecialchars($request['fee']) ?>"
                data-payment="<?= htmlspecialchars($request['payment']) ?>"
                data-status="<?= htmlspecialchars($request['status']) ?>"
                data-status-image="<?= htmlspecialchars(getStatusImage($request['status'])) ?>"
                data-datetime-full="<?= htmlspecialchars($request['datetime_full']) ?>"
                data-reference-number="<?= htmlspecialchars($request['reference_number']) ?>">

              <div class="request-cell request-id"><?= htmlspecialchars($request['request_id']) ?></div>
              <div class="request-cell document-name"><?= htmlspecialchars($request['document']) ?></div>
              <div class="request-cell request-date"><?= htmlspecialchars($request['date']) ?></div>
              <div class="request-cell fee"><?= htmlspecialchars($request['fee']) ?></div>
              <div class="request-cell payment"><?= htmlspecialchars($request['payment']) ?></div>
              <div class="request-cell">
                <img src="<?= htmlspecialchars(getStatusImage($request['status'])) ?>" alt="<?= htmlspecialchars($request['status']) ?>" class="status-badge-img">
              </div>
              <div class="request-cell details-cell">
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


<!-- ===================== REQUEST DETAILS MODAL ===================== -->
<div class="request-details-modal" id="requestDetailsModal">
  <div class="request-details-box">
    <button type="button" class="request-details-close" id="requestDetailsClose" aria-label="Close">×</button>

    <div class="request-details-title">Request Details</div>
    <div class="request-details-divider"></div>

    <div class="request-details-body">
      <img src="images/complaint_faded_logo.png" alt="" class="request-details-faded-logo">

      <div class="request-detail-row">
        <div class="request-detail-label">Request ID</div>
        <div class="request-detail-value" id="modalRequestId">DOC-0000000005</div>
      </div>

      <div class="request-detail-row">
        <div class="request-detail-label">Document Type</div>
        <div class="request-detail-value" id="modalDocumentType">Barangay Clearance</div>
      </div>

      <div class="request-detail-row">
        <div class="request-detail-label">Date Submitted</div>
        <div class="request-detail-value" id="modalDateSubmitted">04/01/2026, 6:26:04 PM</div>
      </div>

      <div class="request-detail-row">
        <div class="request-detail-label">Fee</div>
        <div class="request-detail-value" id="modalFee">₱50.00</div>
      </div>

      <div class="request-detail-row">
        <div class="request-detail-label">Payment Method</div>
        <div class="request-detail-value" id="modalPaymentMethod">GCash</div>
      </div>

      <div class="request-detail-row">
        <div class="request-detail-label">Reference Number</div>
        <div class="request-detail-value" id="modalReferenceNumber">3031872639187</div>
      </div>
    </div>

    <div class="request-details-divider bottom"></div>

    <div class="request-detail-status-row">
      <div class="request-detail-label">Status</div>
      <div class="request-detail-status-value">
        <img src="images/my_request_received.png" alt="Status" id="modalStatusImage" class="request-detail-status-img">
      </div>
    </div>

    <div class="request-detail-warning" id="requestDetailWarning">
      <img src="images/rejected_request_warning.png" alt="Rejected Warning" class="request-warning-full-img">
    </div>
  </div>
</div>

<script src="js/my_requests.js"></script>
</body>
</html>