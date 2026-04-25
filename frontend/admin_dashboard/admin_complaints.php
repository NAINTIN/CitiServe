<?php
$user = [
  'first_name' => 'Jasmin',
  'full_name'  => 'Jasmin M.',
  'avatar'     => '',
];

$complaints = [
  [
    'complaint_id'   => 'CMP-0000000006',
    'reporter'       => 'Samantha Nicole Arambulo',
    'category'       => 'Garbage / Sanitation',
    'date'           => 'Apr 5, 2026',
    'datetime_full'  => '04/05/2026, 6:00:00 PM',
    'type'           => 'Identified',
    'status'         => 'Received',
    'contact'        => '09171234567',
    'description'    => 'Improper garbage disposal has been observed near the community area.',
    'location_text'  => 'Near Barangay Kalayaan Covered Court',
    'map_query'      => 'Barangay Kalayaan Covered Court Angono Rizal',
    'evidence'       => 'garbage_area_photo.jpg',
  ],
  [
    'complaint_id'   => 'CMP-0000000005',
    'reporter'       => 'Anonymous',
    'category'       => 'Anonymous Report',
    'date'           => 'Apr 4, 2026',
    'datetime_full'  => '04/04/2026, 6:00:00 PM',
    'type'           => 'Anonymous',
    'status'         => 'Received',
    'contact'        => 'Anonymous',
    'description'    => 'A concern was reported anonymously and needs barangay review.',
    'location_text'  => 'Within Barangay Kalayaan area',
    'map_query'      => 'Barangay Kalayaan Angono Rizal',
    'evidence'       => '',
  ],
  [
    'complaint_id'   => 'CMP-0000000004',
    'reporter'       => 'Ana Cruz',
    'category'       => 'Noise Disturbance',
    'date'           => 'Apr 3, 2026',
    'datetime_full'  => '04/03/2026, 6:00:00 PM',
    'type'           => 'Identified',
    'status'         => 'Received',
    'contact'        => '09181234567',
    'description'    => 'Loud noise has been reported during late evening hours.',
    'location_text'  => 'Near Kalayaan Street, Angono, Rizal',
    'map_query'      => 'Kalayaan Street Angono Rizal',
    'evidence'       => 'noise_report.jpg',
  ],
  [
    'complaint_id'   => 'CMP-0000000003',
    'reporter'       => 'Pedro Lim',
    'category'       => 'Road / Infrastructure',
    'date'           => 'Apr 1, 2026',
    'datetime_full'  => '04/01/2025, 6:00:00 PM',
    'type'           => 'Identified',
    'status'         => 'Processing',
    'contact'        => '09171234567',
    'description'    => 'Large pothole on the main road causing accidents near the elementary school.',
    'location_text'  => 'Near Brgy. Kalayaan Elementary School, Main Road',
    'map_query'      => 'Barangay Kalayaan Elementary School Angono Rizal',
    'evidence'       => 'pothole_photo.jpg',
  ],
  [
    'complaint_id'   => 'CMP-0000000002',
    'reporter'       => 'Carlos Dela Cruz',
    'category'       => 'Community / Social Issues',
    'date'           => 'Mar 20, 2026',
    'datetime_full'  => '03/20/2026, 6:00:00 PM',
    'type'           => 'Identified',
    'status'         => 'Received',
    'contact'        => '09201234567',
    'description'    => 'A community concern was reported and requires attention from barangay staff.',
    'location_text'  => 'Barangay Kalayaan, Angono, Rizal',
    'map_query'      => 'Barangay Kalayaan Angono Rizal',
    'evidence'       => '',
  ],
  [
    'complaint_id'   => 'CMP-0000000001',
    'reporter'       => 'Harry Styles',
    'category'       => 'Garbage / Sanitation',
    'date'           => 'Mar 22, 2026',
    'datetime_full'  => '03/22/2026, 6:00:00 PM',
    'type'           => 'Identified',
    'status'         => 'Processing',
    'contact'        => '09211234567',
    'description'    => 'Uncollected garbage was reported in the area and may affect nearby residents.',
    'location_text'  => 'Near Barangay Kalayaan Hall',
    'map_query'      => 'Barangay Kalayaan Hall Angono Rizal',
    'evidence'       => 'uncollected_garbage.jpg',
  ],
];

$receivedCount = count(array_filter($complaints, fn($c) => strtolower($c['status']) === 'received'));
$processingCount = count(array_filter($complaints, fn($c) => strtolower($c['status']) === 'processing'));
$anonymousCount = count(array_filter($complaints, fn($c) => strtolower($c['type']) === 'anonymous'));

function getComplaintStatusImage($status) {
  return match (strtolower($status)) {
    'received'   => 'images/my_complaint_received.png',
    'processing' => 'images/my_complaint_processing.png',
    'resolved'   => 'images/my_complaint_resolved.png',
    'rejected'   => 'images/my_complaint_rejected.png',
    default      => 'images/my_complaint_received.png',
  };
}

function getComplaintTypeImage($type) {
  return match (strtolower($type)) {
    'anonymous'  => 'images/complaint_anonymous_type.png',
    'identified' => 'images/complaint_identified_type.png',
    default      => 'images/complaint_identified_type.png',
  };
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Document Requests - CitiServe</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="CSS/admin_complaints.css">
</head>

<body>

<div class="design-strip left"><img src="images/dashboard_design.png" alt=""></div>
<div class="design-strip right"><img src="images/dashboard_design.png" alt=""></div>

<nav class="navbar">
  <a href="admin_dashboard.php" class="navbar-logo">
    <img src="images/logo_pink.png" alt="CitiServe">
  </a>

  <div class="navbar-nav admin-nav">
    <a href="admin_dashboard.php" class="nav-item">
      <span class="nav-text">Dashboard</span>
    </a>

    <a href="admin_document_request.php" class="nav-item">
      <span class="nav-text">Document Requests</span>
    </a>

    <a href="admin_complaints.php" class="nav-item active">
      <span class="nav-text">Complaints</span>
    </a>

    <div class="nav-item has-dropdown">
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
        <img src="<?= !empty($user['avatar']) ? htmlspecialchars($user['avatar']) : 'images/admin_dummy_icon.png' ?>" alt="Admin">
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
        <img src="images/my_profile.png" class="profile-panel-icon1" alt="">
        <span>My Profile</span>
      </a>

      <a href="login.php" class="profile-panel-item logout">
        <img src="images/logout.png" class="profile-panel-icon2" alt="">
        <span>Logout</span>
      </a>
    </div>
  </div>
</nav>

<div class="page-body">
  <div class="admin-doc-card">

    <div class="page-head">
      <h1 class="page-title">Complaint Management</h1>
      <p class="page-subtitle">Review and respond to all community complaints.</p>
    </div>

    <div class="admin-summary-row">
        <div class="summary-box summary-received">
            <div class="summary-number"><?= $receivedCount ?></div>
            <div class="summary-label">Received</div>
            <img src="images/complaint_faded_received.png" class="summary-icon1">
        </div>

        <div class="summary-box summary-processing">
            <div class="summary-number"><?= $processingCount ?></div>
            <div class="summary-label">Processing</div>
            <img src="images/complaint_faded_processing.png" class="summary-icon2">
        </div>

        <div class="summary-box summary-anonymous">
            <div class="summary-number"><?= $anonymousCount ?></div>
            <div class="summary-label">Anonymous</div>
            <img src="images/complaint_faded_anonymous.png" class="summary-icon3">
        </div>
    </div>

    <div class="toolbar">
      <div class="toolbar-left">
        <div class="search-wrap">
          <img src="images/search_icon.png" alt="" class="search-icon">
          <input type="text" id="searchInput" placeholder="Search by resident, category, or complaint ID...">
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
          </div>
        </div>
      </div>
    </div>

<div class="requests-table-card">
  <div class="requests-table-head">
    <div>Complaint ID</div>
    <div>Reporter</div>
    <div>Category</div>
    <div>Date</div>
    <div>Type</div>
    <div>Status</div>
    <div></div>
  </div>

  <?php if (empty($complaints)): ?>
    <div class="requests-empty">
      <img src="images/recent_complaint_main.png" alt="" class="empty-icon">
      <div class="empty-text">No complaints submitted yet</div>
    </div>
  <?php else: ?>
    <div class="requests-table-body" id="requestsTableBody">
      <?php foreach ($complaints as $complaint): ?>
        <div class="request-row"
            data-complaint-id="<?= htmlspecialchars($complaint['complaint_id']) ?>"
            data-reporter="<?= htmlspecialchars($complaint['reporter']) ?>"
            data-category="<?= htmlspecialchars($complaint['category']) ?>"
            data-date="<?= htmlspecialchars($complaint['date']) ?>"
            data-datetime-full="<?= htmlspecialchars($complaint['datetime_full']) ?>"
            data-type="<?= htmlspecialchars($complaint['type']) ?>"
            data-status="<?= htmlspecialchars($complaint['status']) ?>"
            data-contact="<?= htmlspecialchars($complaint['contact']) ?>"
            data-description="<?= htmlspecialchars($complaint['description']) ?>"
            data-location-text="<?= htmlspecialchars($complaint['location_text']) ?>"
            data-map-query="<?= htmlspecialchars($complaint['map_query']) ?>"
            data-evidence="<?= htmlspecialchars($complaint['evidence']) ?>"
            data-status-image="<?= htmlspecialchars(getComplaintStatusImage($complaint['status'])) ?>"
        >

          <div class="request-cell request-id"><?= htmlspecialchars($complaint['complaint_id']) ?></div>

          <div class="request-cell resident-name <?= strtolower($complaint['type']) === 'anonymous' ? 'anonymous-reporter' : '' ?>">
            <?= htmlspecialchars($complaint['reporter']) ?>
          </div>

          <div class="request-cell document-name"><?= htmlspecialchars($complaint['category']) ?></div>
          <div class="request-cell request-date"><?= htmlspecialchars($complaint['date']) ?></div>

          <div class="request-cell type-cell">
            <img src="<?= htmlspecialchars(getComplaintTypeImage($complaint['type'])) ?>" class="type-badge-img" alt="<?= htmlspecialchars($complaint['type']) ?>">
          </div>

          <div class="request-cell status-cell">
            <img src="<?= htmlspecialchars(getComplaintStatusImage($complaint['status'])) ?>" class="status-badge-img" alt="<?= htmlspecialchars($complaint['status']) ?>">
          </div>

          <div class="request-cell manage-cell">
            <button type="button" class="manage-btn">Manage</button>
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




<div class="manage-complaint-modal" id="manageComplaintModal">
  <div class="manage-complaint-box" id="manageComplaintBox">
    <button type="button" class="manage-complaint-close" id="manageComplaintClose">×</button>

    <div class="manage-complaint-title">
      Manage Complaint - <span id="manageComplaintId">CMP-0000000003</span>
    </div>

    <div class="manage-complaint-divider"></div>

    <div class="manage-complaint-scroll">
      <div class="complaint-top-grid">
        <div class="complaint-top-col">
          <div class="complaint-detail-row compact">
            <div class="complaint-detail-label">Complaint ID</div>
            <div class="complaint-detail-value left" id="manageComplaintIdValue"></div>
          </div>

          <div class="complaint-detail-row compact">
            <div class="complaint-detail-label">Reporter</div>
            <div class="complaint-detail-value left" id="manageComplaintReporter"></div>
          </div>
        </div>

        <div class="complaint-top-col">
          <div class="complaint-detail-row compact">
            <div class="complaint-detail-label">Category</div>
            <div class="complaint-detail-value left" id="manageComplaintCategory"></div>
          </div>

          <div class="complaint-detail-row compact">
            <div class="complaint-detail-label">Date Submitted</div>
            <div class="complaint-detail-value left" id="manageComplaintDate"></div>
          </div>
        </div>
      </div>

      <div class="complaint-section">
        <div class="complaint-detail-label">Description</div>
        <div class="complaint-description-box" id="manageComplaintDescription"></div>
      </div>

      <div class="complaint-section">
        <div class="complaint-detail-label">Location</div>
        <div class="complaint-location-text" id="manageComplaintLocationText"></div>
        <div class="complaint-map-wrap">
          <iframe id="manageComplaintMap" class="complaint-map-frame" src="" loading="lazy"></iframe>
        </div>
      </div>

      <div class="complaint-section complaint-evidence-section" id="manageComplaintEvidenceSection">
        <div class="complaint-detail-label">Evidence</div>
        <div class="complaint-evidence-box">
          <div class="complaint-evidence-file" id="manageComplaintEvidence"></div>
          <button type="button" class="complaint-evidence-view">View</button>
        </div>
      </div>

      <div class="manage-field">
        <label>Update Status</label>
        <div class="manage-status-filter" id="manageComplaintStatusFilter">
          <button type="button" class="manage-status-box" id="manageComplaintStatusBox">
            <span id="manageComplaintSelectedStatus">Received</span>
            <span>▾</span>
          </button>

          <div class="manage-status-dropdown" id="manageComplaintStatusDropdown">
            <div class="manage-status-option active" data-value="Received">Received</div>
            <div class="manage-status-option" data-value="Processing">Processing</div>
            <div class="manage-status-option" data-value="Rejected">Rejected</div>
            <div class="manage-status-option" data-value="Resolved">Resolved</div>
          </div>
        </div>
      </div>

      <div class="manage-field">
        <label>Action Notes <span>(Optional)</span></label>
        <textarea id="manageComplaintNotes" placeholder="Describe the action taken or reason for status change..."></textarea>
      </div>

      <div class="manage-actions">
        <button type="button" class="manage-img-btn" id="manageComplaintCancelBtn">
          <img src="images/close_manage_complaint.png" alt="Close">
        </button>

        <button type="button" class="manage-img-btn" id="manageComplaintUpdateBtn">
          <img src="images/update_status_manage_complaint.png" alt="Update Status">
        </button>
      </div>
    </div>
  </div>
</div>

<script src="JS/admin_complaints.js"></script>
</body>
</html>