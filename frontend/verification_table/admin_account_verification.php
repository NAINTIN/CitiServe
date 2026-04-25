<?php
$user = [
  'first_name' => 'Jasmin',
  'full_name'  => 'Jasmin M.',
  'avatar'     => '',
];

$accounts = [
  [
    'resident_id' => 'RES-0000000013',
    'name' => 'Ramon Villanueva',
    'email' => 'ramon.v@email.com',
    'address' => '123 Rizal St. Barangay Kalayaan, Angono, Rizal',
    'date' => 'Apr 5, 2026',
    'status' => 'Pending',

    'document' => 'Valid ID (Front).jpg',
    'initials' => 'RV',
  ],
  [
    'resident_id' => 'RES-0000000014',
    'name' => 'Cristina Ramos',
    'email' => 'cristina.r@email.com',
    'address' => '456 Bonifacio St. Barangay Kalayaan, Angono, Rizal',
    'date' => 'Mar 14, 2026',
    'status' => 'Pending',

    'document' => 'Valid ID (Front).jpg',
    'initials' => 'CR',
  ],
  [
    'resident_id' => 'RES-0000000015',
    'name' => 'Eduardo Flores',
    'email' => 'eduardo.f@email.com',
    'address' => '789 Aguinaldo St. Barangay Kalayaan, Angono, Rizal',
    'date' => 'Mar 15, 2026',
    'status' => 'Pending',

    'document' => 'Valid ID (Front).jpg',
    'initials' => 'EF',
  ],
  [
    'resident_id' => 'RES-0000000016',
    'name' => 'Maricel Torres',
    'email' => 'maricel.t@email.com',
    'address' => '012 Jacinto St. Barangay Kalayaan, Angono, Rizal',
    'date' => 'Feb 4, 2026',
    'status' => 'Pending',

    'document' => 'Valid ID (Front).jpg',
    'initials' => 'MT',
  ],
  [
    'resident_id' => 'RES-0000000017',
    'name' => 'Jerico Colinares',
    'email' => 'jerico.c@gmail.com',
    'address' => '345 Miko Clark Cruz St. Barangay Kalayaan, Angono, Rizal',
    'date' => 'Jan 2, 2026',
    'status' => 'Pending',

    'document' => 'Valid ID (Front).jpg',
    'initials' => 'JC',
  ],
];

$pendingCount = 5;
$approvedCount = 6;
$rejectedCount = 1;

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

  <link rel="stylesheet" href="./admin_account_verification.css">
</head>

<body>
<div class="design-strip left"><img src="images/dashboard_design.png" alt=""></div>
<div class="design-strip right"><img src="images/dashboard_design.png" alt=""></div>

<nav class="navbar">
  <a href="admin_dashboard.php" class="navbar-logo">
    <img src="images/logo_pink.png" alt="CitiServe">
  </a>

  <div class="navbar-nav admin-nav">
    <a href="admin_dashboard.php" class="nav-item"><span class="nav-text">Dashboard</span></a>
    <a href="admin_document_request.php" class="nav-item"><span class="nav-text">Document Requests</span></a>
    <a href="admin_complaints.php" class="nav-item"><span class="nav-text">Complaints</span></a>

    <div class="nav-item has-dropdown">
      <span class="nav-text">User Management</span>
      <span class="nav-chevron">⏷</span>
      <div class="nav-dropdown">
        <a href="admin_residents.php" class="nav-dropdown-item">Residents</a>
        <a href="admin_staff.php" class="nav-dropdown-item">Staff</a>
      </div>
    </div>

    <a href="admin_account_verification.php" class="nav-item active">
      <span class="nav-text">Account Verification</span>
    </a>

    <a href="admin_reports.php" class="nav-item"><span class="nav-text">Reports</span></a>
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
      <h1 class="page-title">Account Verification</h1>
      <p class="page-subtitle">Review and approve resident account registrations.</p>
    </div>

    <div class="admin-summary-row">
      <div class="summary-box summary-pending-verification">
        <div class="summary-number"><?= $pendingCount ?></div>
        <div class="summary-label">Pending</div>
        <img src="images/admin_verification_pending.png" class="summary-icon1">
      </div>

      <div class="summary-box summary-approved-verification">
        <div class="summary-number"><?= $approvedCount ?></div>
        <div class="summary-label">Approved</div>
        <img src="images/admin_verification_approved.png" class="summary-icon2">
      </div>

      <div class="summary-box summary-rejected-verification">
        <div class="summary-number"><?= $rejectedCount ?></div>
        <div class="summary-label">Rejected</div>
        <img src="images/admin_verification_rejected.png" class="summary-icon3">
      </div>
    </div>

    <div class="pending-title-row">
      <img src="images/pending_acc_icon.png" alt="">
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
        <?php foreach ($accounts as $account): ?>
          <div class="verification-row"
            data-resident-id="<?= htmlspecialchars($account['resident_id']) ?>"
            data-name="<?= htmlspecialchars($account['name']) ?>"
            data-email="<?= htmlspecialchars($account['email']) ?>"
            data-address="<?= htmlspecialchars($account['address']) ?>"
            data-date="<?= htmlspecialchars($account['date']) ?>"
            data-initials="<?= htmlspecialchars(getInitials($account['name'])) ?>"
            data-document="<?= htmlspecialchars($account['document']) ?>"
           >

            <div class="verification-cell request-id"><?= htmlspecialchars($account['resident_id']) ?></div>

            <div class="verification-cell resident-name-cell">
              <div class="resident-initials"><?= htmlspecialchars(getInitials($account['name'])) ?></div>
              <div>
                <div class="resident-fullname"><?= htmlspecialchars($account['name']) ?></div>
                <div class="resident-email"><?= htmlspecialchars($account['email']) ?></div>
              </div>
            </div>

            <div class="verification-cell address-cell"><?= htmlspecialchars($account['address']) ?></div>
            <div class="verification-cell request-date"><?= htmlspecialchars($account['date']) ?></div>

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
      Account Review – <span id="reviewResidentId">RES-0000000003</span>
    </div>

    <div class="account-review-divider"></div>

    <div class="account-review-header">
      <div class="account-review-avatar" id="reviewInitials">EF</div>

      <div>
        <div class="account-review-name" id="reviewName">Eduardo Flores</div>
        <div class="account-review-id" id="reviewSmallId">RES-0000000003</div>
        <img src="images/resident_active_staff.png" class="account-review-status-img" alt="Active">
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
        <button type="button">View</button>
      </div>
    </div>

    <div class="account-review-actions">
      <button type="button" class="account-review-img-btn" id="rejectAccountBtn">
        <img src="images/admin_reject_account.png" alt="Reject">
      </button>

      <button type="button" class="account-review-img-btn" id="approveAccountBtn">
        <img src="images/admin_approve_account.png" alt="Approve Account">
      </button>
    </div>
  </div>
</div>

<script src="JS/admin_account_verification.js"></script>
</body>
</html>
