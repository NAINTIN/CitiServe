<?php
$user = [
  'first_name' => 'Jasmin',
  'full_name'  => 'Jasmin M.',
  'avatar'     => '',
];

$requests = [
  [
    'request_id' => 'DOC-0000000006',
    'resident'   => 'Samantha Nicole Arambulo',
    'document'   => 'Barangay Business Clearance',
    'date'       => 'Apr 5, 2026',
    'datetime_full' => '04/05/2026, 6:00:00 PM',
    'fee'        => '₱150.00',
    'payment'    => 'GCash',
    'reference_number' => '3014823639257',
    'status'     => 'Received',
    'requirements' => [
      'Valid ID (Front).jpg',
      'Business Permit.jpg',
      'Payment Proof Screenshot.png',
    ],
  ],
  [
    'request_id' => 'DOC-0000000005',
    'resident'   => 'Jasmin Caryll Armayan',
    'document'   => 'Solo Parent Certificate',
    'date'       => 'Apr 4, 2026',
    'datetime_full' => '04/04/2026, 6:00:00 PM',
    'fee'        => '₱50.00',
    'payment'    => 'GCash',
    'reference_number' => '3014823639258',
    'status'     => 'Received',
    'requirements' => [
      'Valid ID (Front).jpg',
      'Solo Parent ID.jpg',
    ],
  ],
  [
    'request_id' => 'DOC-0000000004',
    'resident'   => 'Ana Cruz',
    'document'   => 'Barangay Business Clearance',
    'date'       => 'Apr 3, 2026',
    'datetime_full' => '04/03/2026, 6:00:00 PM',
    'fee'        => '₱150.00',
    'payment'    => 'GCash',
    'reference_number' => '3014823639259',
    'status'     => 'Received',
    'requirements' => [
      'Valid ID (Front).jpg',
    ],
  ],
  [
    'request_id' => 'DOC-0000000003',
    'resident'   => 'Pedro Lim',
    'document'   => 'Barangay Clearance',
    'date'       => 'Apr 1, 2026',
    'datetime_full' => '04/01/2026, 6:00:00 PM',
    'fee'        => '₱50.00',
    'payment'    => 'GCash',
    'reference_number' => '3014823639260',
    'status'     => 'Received',
    'requirements' => [
      'Valid ID (Front).jpg',
      'Payment Proof Screenshot.png',
    ],
  ],
  [
    'request_id' => 'DOC-0000000002',
    'resident'   => 'Carlos Dela Cruz',
    'document'   => 'Barangay Clearance',
    'date'       => 'Mar 20, 2026',
    'datetime_full' => '03/20/2026, 6:00:00 PM',
    'fee'        => '₱50.00',
    'payment'    => 'GCash',
    'reference_number' => '3014823639261',
    'status'     => 'Claimable',
    'requirements' => [
      'Valid ID (Front).jpg',
    ],
  ],
  [
    'request_id' => 'DOC-0000000001',
    'resident'   => 'Liza Mendoza',
    'document'   => 'Certificate of Residency',
    'date'       => 'Mar 22, 2026',
    'datetime_full' => '03/22/2026, 6:00:00 PM',
    'fee'        => '₱30.00',
    'payment'    => 'Maya',
    'reference_number' => '3014823639262',
    'status'     => 'Pending',
    'requirements' => [
      'Valid ID (Front).jpg',
      'Barangay ID.jpg',
    ],
  ],
];

$receivedCount = count(array_filter($requests, fn($r) => strtolower($r['status']) === 'received'));
$pendingCount = count(array_filter($requests, fn($r) => strtolower($r['status']) === 'pending'));
$claimableCount = count(array_filter($requests, fn($r) => strtolower($r['status']) === 'claimable'));

function getStatusImage($status) {
  return match (strtolower($status)) {
    'received'  => 'images/my_request_received.png',
    'pending'   => 'images/my_request_pending.png',
    'claimable' => 'images/my_request_claimable.png',
    'rejected'  => 'images/my_request_rejected.png',
    default     => 'images/my_request_received.png',
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

  <link rel="stylesheet" href="CSS/admin_document_request.css">
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

    <a href="admin_document_request.php" class="nav-item active">
      <span class="nav-text">Document Requests</span>
    </a>

    <a href="admin_complaints.php" class="nav-item">
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
      <h1 class="page-title">Document Request Management</h1>
      <p class="page-subtitle">Review and process all document requests from residents.</p>
    </div>

    <div class="admin-summary-row">

    <div class="summary-box summary-received">
        <div class="summary-number"><?= $receivedCount ?></div>
        <div class="summary-label">Received</div>
        <img src="images/received_faded_icon.png" class="summary-icon1">
    </div>

    <div class="summary-box summary-pending">
        <div class="summary-number"><?= $pendingCount ?></div>
        <div class="summary-label">Pending</div>
        <img src="images/pending_faded_icon.png" class="summary-icon2">
    </div>

    <div class="summary-box summary-claimable">
        <div class="summary-number"><?= $claimableCount ?></div>
        <div class="summary-label">Claimable</div>
        <img src="images/claimable_faded.png" class="summary-icon3">
    </div>

    </div>

    <div class="toolbar">
      <div class="toolbar-left">
        <div class="search-wrap">
          <img src="images/search_icon.png" alt="" class="search-icon">
          <input type="text" id="searchInput" placeholder="Search by resident, document type, or request ID...">
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
            <div class="filter-option" data-value="pending">Pending</div>
            <div class="filter-option" data-value="claimable">Claimable</div>
          </div>
        </div>
      </div>
    </div>

    <div class="requests-table-card">
      <div class="requests-table-head">
        <div>Request ID</div>
        <div>Resident</div>
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
                data-resident="<?= htmlspecialchars($request['resident']) ?>"
                data-document="<?= htmlspecialchars($request['document']) ?>"
                data-status="<?= htmlspecialchars($request['status']) ?>"
                data-date="<?= htmlspecialchars($request['date']) ?>"
                data-datetime-full="<?= htmlspecialchars($request['datetime_full']) ?>"
                data-fee="<?= htmlspecialchars($request['fee']) ?>"
                data-payment="<?= htmlspecialchars($request['payment']) ?>"
                data-reference-number="<?= htmlspecialchars($request['reference_number']) ?>"
                data-requirements='<?= htmlspecialchars(json_encode($request["requirements"]), ENT_QUOTES, "UTF-8") ?>'
            >
              

              <div class="request-cell request-id"><?= htmlspecialchars($request['request_id']) ?></div>
              <div class="request-cell resident-name"><?= htmlspecialchars($request['resident']) ?></div>
              <div class="request-cell document-name"><?= htmlspecialchars($request['document']) ?></div>
              <div class="request-cell request-date"><?= htmlspecialchars($request['date']) ?></div>
              <div class="request-cell fee"><?= htmlspecialchars($request['fee']) ?></div>
              <div class="request-cell payment"><?= htmlspecialchars($request['payment']) ?></div>
              <div class="request-cell status-cell">
                <img src="<?= htmlspecialchars(getStatusImage($request['status'])) ?>" alt="<?= htmlspecialchars($request['status']) ?>" class="status-badge-img">
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


<!-- ===================== MANAGE REQUEST MODAL ===================== -->
<div class="manage-request-modal" id="manageRequestModal">
  <div class="manage-request-box">
    <button type="button" class="manage-request-close" id="manageRequestClose">×</button>

    <div class="manage-request-title">
      Manage Request - <span id="manageRequestId">DOC-0000000003</span>
    </div>

    <div class="manage-request-divider"></div>

    <div class="manage-request-body">
      <div class="manage-top-grid">
        <div class="manage-top-col">
          <div class="manage-detail-row">
            <div class="manage-detail-label">Resident</div>
            <div class="manage-detail-value" id="manageResident">Pedro Lim</div>
          </div>

          <div class="manage-detail-row">
            <div class="manage-detail-label">Fee</div>
            <div class="manage-detail-value" id="manageFee">₱50.00</div>
          </div>

          <div class="manage-detail-row">
            <div class="manage-detail-label">Reference No.</div>
            <div class="manage-detail-value" id="manageReference">3014823639257</div>
          </div>
        </div>

        <div class="manage-top-col">
          <div class="manage-detail-row">
            <div class="manage-detail-label">Document</div>
            <div class="manage-detail-value" id="manageDocument">Barangay Clearance</div>
          </div>

          <div class="manage-detail-row">
            <div class="manage-detail-label">Payment Method</div>
            <div class="manage-detail-value" id="managePayment">GCash</div>
          </div>

          <div class="manage-detail-row">
            <div class="manage-detail-label">Date Submitted</div>
            <div class="manage-detail-value" id="manageDate">04/01/2026, 6:00:00 PM</div>
          </div>
        </div>
      </div>

      <div class="manage-requirements-box" id="manageRequirementsBox">
        <div class="manage-req-title">Uploaded Requirement/s</div>

        <div class="manage-req-item">
          <span>Valid ID (Front).jpg</span>
          <button type="button">View</button>
        </div>

        <div class="manage-req-item">
          <span>Payment Proof Screenshot.png</span>
          <button type="button">View</button>
        </div>
      </div>

      <div class="manage-field">
        <label>Update Status</label>

        <div class="manage-status-filter" id="manageStatusFilter">
          <button type="button" class="manage-status-box" id="manageStatusBox">
            <span id="manageSelectedStatus">Received</span>
            <span id="manageArrow">▾</span>
          </button>

          <div class="manage-status-dropdown" id="manageStatusDropdown">
            <div class="manage-status-option active" data-value="Received">Received</div>
            <div class="manage-status-option" data-value="Pending">Pending</div>
            <div class="manage-status-option" data-value="Claimable">Claimable</div>
            <div class="manage-status-option" data-value="Released">Released</div>
            <div class="manage-status-option" data-value="Rejected">Rejected</div>
          </div>
        </div>
      </div>

      <div class="manage-field">
        <label>Staff Notes <span>(Optional)</span></label>
        <textarea id="manageStaffNotes" placeholder="Add internal notes or reason for status change..."></textarea>
      </div>

      <div class="manage-actions">
        <button type="button" class="manage-img-btn" id="manageCancelBtn">
          <img src="images/docu_request_cancel.png" alt="Close">
        </button>

        <button type="button" class="manage-img-btn" id="manageUpdateBtn">
          <img src="images/docu_request_update.png" alt="Update Status">
        </button>
      </div>
    </div>
  </div>
</div>

<script src="JS/admin_document_request.js"></script>
</body>
</html>