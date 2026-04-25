<?php
$user = [
  'first_name' => 'Jasmin',
  'full_name'  => 'Jasmin M.',
  'avatar'     => '',
];

$residents = [
  [
    'resident_id' => 'RES-0000000001',
    'name' => 'Maria Santos',
    'email' => 'maria.santos@email.com',
    'contact' => '09171234567',
    'requests' => 3,
    'complaints' => 1,
    'joined' => 'Apr 5, 2026',
    'type' => 'Fully Verified',
    'status' => 'Active',

    'birthdate' => '02/14/1995',
    'gender' => 'Female',
    'civil_status' => 'Single',
    'citizenship' => 'Filipino',
    'address' => '45 Mabini St., Brgy. Kalayaan, Angono, Rizal',
  ],
  [
    'resident_id' => 'RES-0000000002',
    'name' => 'Jose Reyes',
    'email' => 'jose.reyes@email.com',
    'contact' => '09221234567',
    'requests' => 1,
    'complaints' => 2,
    'joined' => 'Apr 1, 2026',
    'type' => 'Fully Verified',
    'status' => 'Active',

    'birthdate' => '06/10/1990',
    'gender' => 'Male',
    'civil_status' => 'Married',
    'citizenship' => 'Filipino',
    'address' => '78 Rizal Ave., Brgy. Kalayaan, Angono, Rizal',
  ],
  [
    'resident_id' => 'RES-0000000003',
    'name' => 'Ana Cruz',
    'email' => 'ana.cruz@email.com',
    'contact' => '09331234567',
    'requests' => 1,
    'complaints' => 2,
    'joined' => 'Mar 15, 2026',
    'type' => 'Fully Verified',
    'status' => 'Active',

    'birthdate' => '03/11/1988',
    'gender' => 'Female',
    'civil_status' => 'Single',
    'citizenship' => 'Filipino',
    'address' => '123 Rizal St., Brgy. Kalayaan, Angono, Rizal',
  ],
  [
    'resident_id' => 'RES-0000000004',
    'name' => 'Pedro Lim',
    'email' => 'pedro.lim@email.com',
    'contact' => '09441234567',
    'requests' => 3,
    'complaints' => 1,
    'joined' => 'Mar 14, 2026',
    'type' => 'Basic',
    'status' => 'Active',

    'birthdate' => '09/25/1985',
    'gender' => 'Male',
    'civil_status' => 'Married',
    'citizenship' => 'Filipino',
    'address' => 'Sitio Uno, Brgy. Kalayaan, Angono, Rizal',
  ],
  [
    'resident_id' => 'RES-0000000005',
    'name' => 'Rosa Bautista',
    'email' => 'rosa.bautista@email.com',
    'contact' => '09661234567',
    'requests' => 2,
    'complaints' => 3,
    'joined' => 'Feb 6, 2026',
    'type' => 'Fully Verified',
    'status' => 'Inactive',

    'birthdate' => '12/05/1992',
    'gender' => 'Female',
    'civil_status' => 'Widowed',
    'citizenship' => 'Filipino',
    'address' => 'Blk 5 Lot 12, Brgy. Kalayaan, Angono, Rizal',
  ],
];

$totalResidents = count($residents);
$activeCount = count(array_filter($residents, fn($r) => strtolower($r['status']) === 'active'));
$inactiveCount = count(array_filter($residents, fn($r) => strtolower($r['status']) === 'inactive'));

function getInitials($name) {
  $parts = explode(' ', trim($name));
  $first = strtoupper(substr($parts[0] ?? '', 0, 1));
  $last = strtoupper(substr(end($parts) ?: '', 0, 1));
  return $first . $last;
}

function getTypeImage($type) {
  return strtolower($type) === 'basic'
    ? 'images/resident_basic_admin.png'
    : 'images/resident_full_admin.png';
}

function getStatusClass($status) {
  return strtolower($status) === 'active' ? 'status-active' : 'status-inactive';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resident Management - CitiServe</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="CSS/admin_residents.css">
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

    <a href="admin_complaints.php" class="nav-item">
      <span class="nav-text">Complaints</span>
    </a>

    <div class="nav-item has-dropdown active">
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
      <h1 class="page-title">Resident Management</h1>
      <p class="page-subtitle">View, manage, and track all resident information</p>
    </div>

    <div class="admin-summary-row">
      <div class="summary-box summary-total">
        <div class="summary-number"><?= $totalResidents ?></div>
        <div class="summary-label">Total Residents</div>
        <img src="images/admin_total_resident.png" class="summary-icon1">
      </div>

      <div class="summary-box summary-active">
        <div class="summary-number"><?= $activeCount ?></div>
        <div class="summary-label">Active</div>
        <img src="images/admin_active_resident.png" class="summary-icon2">
      </div>

      <div class="summary-box summary-inactive">
        <div class="summary-number"><?= $inactiveCount ?></div>
        <div class="summary-label">Inactive</div>
        <img src="images/admin_inactive.png" class="summary-icon3">
      </div>
    </div>

    <div class="toolbar">
      <div class="toolbar-left">
        <div class="search-wrap">
          <img src="images/search_icon.png" alt="" class="search-icon">
          <input type="text" id="searchInput" placeholder="Search by name, email, or resident ID...">
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
            <div class="filter-option" data-value="az">Asc. A → Z</div>
            <div class="filter-option" data-value="za">Dsc. Z → A</div>
            <div class="filter-option" data-value="active">Active</div>
            <div class="filter-option" data-value="inactive">Inactive</div>
          </div>
        </div>
      </div>
    </div>

    <div class="requests-table-card">
      <div class="requests-table-head">
        <div>Resident ID</div>
        <div>Name</div>
        <div>Contact</div>
        <div>Requests</div>
        <div>Complaints</div>
        <div>Joined</div>
        <div>Type</div>
        <div>Status</div>
        <div></div>
      </div>

      <div class="requests-table-body" id="requestsTableBody">
        <?php foreach ($residents as $resident): ?>
          <div class="request-row"
            data-resident-id="<?= htmlspecialchars($resident['resident_id']) ?>"
            data-name="<?= htmlspecialchars($resident['name']) ?>"
            data-email="<?= htmlspecialchars($resident['email']) ?>"
            data-status="<?= htmlspecialchars($resident['status']) ?>"

            data-contact="<?= htmlspecialchars($resident['contact']) ?>"
            data-requests="<?= htmlspecialchars($resident['requests']) ?>"
            data-complaints="<?= htmlspecialchars($resident['complaints']) ?>"
            data-joined="<?= htmlspecialchars($resident['joined']) ?>"
            data-type="<?= htmlspecialchars($resident['type']) ?>"
            data-initials="<?= htmlspecialchars(getInitials($resident['name'])) ?>"

            data-birthdate="<?= htmlspecialchars($resident['birthdate']) ?>"
            data-gender="<?= htmlspecialchars($resident['gender']) ?>"
            data-civil="<?= htmlspecialchars($resident['civil_status']) ?>"
            data-citizenship="<?= htmlspecialchars($resident['citizenship']) ?>"
            data-address="<?= htmlspecialchars($resident['address']) ?>"
            >

            <div class="request-cell request-id"><?= htmlspecialchars($resident['resident_id']) ?></div>

            <div class="request-cell resident-name-cell">
              <div class="resident-initials"><?= htmlspecialchars(getInitials($resident['name'])) ?></div>
              <div>
                <div class="resident-fullname"><?= htmlspecialchars($resident['name']) ?></div>
                <div class="resident-email"><?= htmlspecialchars($resident['email']) ?></div>
              </div>
            </div>

            <div class="request-cell request-date"><?= htmlspecialchars($resident['contact']) ?></div>
            <div class="request-cell count-cell"><?= htmlspecialchars($resident['requests']) ?></div>
            <div class="request-cell count-cell"><?= htmlspecialchars($resident['complaints']) ?></div>
            <div class="request-cell request-date"><?= htmlspecialchars($resident['joined']) ?></div>

            <div class="request-cell type-cell">
              <img src="<?= htmlspecialchars(getTypeImage($resident['type'])) ?>" class="type-badge-img" alt="<?= htmlspecialchars($resident['type']) ?>">
            </div>

            <div class="request-cell">
            <img 
            src="<?= strtolower($resident['status']) === 'active' 
                ? 'images/resident_active_staff.png' 
                : 'images/resident_inactive_staff.png' ?>" 
            alt="<?= htmlspecialchars($resident['status']) ?>" 
            class="resident-status-img">
            </div>

            <div class="request-cell manage-cell">
              <button type="button" class="manage-btn">View Profile</button>
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


<div class="resident-profile-modal" id="residentProfileModal">
  <div class="resident-profile-box">
    <button type="button" class="resident-profile-close-x" id="residentProfileCloseX">×</button>

    <div class="resident-profile-title">Resident Profile</div>
    <div class="resident-profile-divider"></div>

    <div class="resident-profile-header">
      <div class="resident-profile-avatar" id="profileInitials">AC</div>

      <div class="resident-profile-head-text">
        <div class="resident-profile-name" id="profileName">Ana Cruz</div>
        <div class="resident-profile-id" id="profileId">RES-0000000003</div>
        <img src="images/resident_full_admin.png" class="resident-profile-type" id="profileTypeImg">
      </div>
    </div>

    <div class="resident-profile-divider"></div>

    <div class="resident-profile-grid">
      <div>
        <label>Email</label>
        <p id="profileEmail">ana.cruz@email.com</p>
      </div>
      <div>
        <label>Contact Number</label>
        <p id="profileContact">09331234567</p>
      </div>
      <div>
        <label>Date of Birth</label>
        <p id="profileBirthdate">03/11/1988</p>
      </div>
      <div>
        <label>Gender</label>
        <p id="profileGender">Female</p>
      </div>
      <div>
        <label>Civil Status</label>
        <p id="profileCivil">Single</p>
      </div>
      <div>
        <label>Citizenship</label>
        <p id="profileCitizenship">Filipino</p>
      </div>
      <div class="profile-address">
        <label>Address</label>
        <p id="profileAddress">123 Rizal St., Brgy. Kalayaan, Angono, Rizal</p>
      </div>
      <div>
        <label>Date Joined</label>
        <p id="profileJoined">04/01/2025</p>
      </div>
    </div>

    <div class="resident-profile-stats">
      <div class="profile-stat profile-stat-request">
        <strong id="profileRequests">1</strong>
        <span>Total Requests</span>
      </div>

      <div class="profile-stat profile-stat-complaint">
        <strong id="profileComplaints">2</strong>
        <span>Total Complaints</span>
      </div>

      <div class="profile-stat profile-stat-status" id="profileStatusBox">
        <strong id="profileStatus">Active</strong>
        <span>Account Status</span>
      </div>
    </div>

    <div class="resident-profile-actions">
      <button type="button" class="resident-profile-img-btn" id="toggleResidentStatusBtn">
        <img src="images/resident_mark_inactive.png" alt="Mark as Inactive" id="toggleResidentStatusImg">
      </button>

      <button type="button" class="resident-profile-img-btn" id="residentProfileCloseBtn">
        <img src="images/resident_close_profile.png" alt="Close">
      </button>
    </div>
  </div>
</div>

<script src="JS/admin_residents.js"></script>
</body>
</html>