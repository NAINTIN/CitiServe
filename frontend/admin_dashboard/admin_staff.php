<?php
$user = [
  'first_name' => 'Jasmin',
  'full_name'  => 'Jasmin M.',
  'avatar'     => '',
];

$staffs = [
  [
    'staff_id' => 'STF-0000000001',
    'name' => 'Roberto Dela Cruz',
    'email' => 'roberto.dc@kalayaan.gov.ph',
    'contact' => '09618254091',
    'role' => 'Barangay Captain',
    'last_login' => 'Apr 5, 2026',
    'joined' => 'Jan 15, 2026',
    'status' => 'Active',
    'birthdate' => '08/12/1978',
    'gender' => 'Male',
    'civil_status' => 'Married',
    'citizenship' => 'Filipino',
    'address' => '45 Mabini St., Brgy. Kalayaan, Angono, Rizal',
  ],
  [
    'staff_id' => 'STF-0000000002',
    'name' => 'Ana Reyes',
    'email' => 'ana.reyes@kalayaan.gov.ph',
    'contact' => '091718256189',
    'role' => 'Kagawad',
    'last_login' => 'Mar 14, 2026',
    'joined' => 'Feb 10, 2026',
    'status' => 'Active',
    'birthdate' => '05/18/1987',
    'gender' => 'Female',
    'civil_status' => 'Single',
    'citizenship' => 'Filipino',
    'address' => '78 Rizal Ave., Brgy. Kalayaan, Angono, Rizal',
  ],
  [
    'staff_id' => 'STF-0000000003',
    'name' => 'Mario Santos',
    'email' => 'mario.santos@kalayaan.gov.ph',
    'contact' => '09331234567',
    'role' => 'Barangay Secretary',
    'last_login' => 'Mar 15, 2026',
    'joined' => 'Apr 1, 2026',
    'status' => 'Active',
    'birthdate' => '11/15/1983',
    'gender' => 'Male',
    'civil_status' => 'Single',
    'citizenship' => 'Filipino',
    'address' => '123 Rizal St., Brgy. Kalayaan, Angono, Rizal',
  ],
  [
    'staff_id' => 'STF-0000000004',
    'name' => 'Lorna Cruz',
    'email' => 'lorna.cruz@kalayaan.gov.ph',
    'contact' => '09718817256',
    'role' => 'Barangay Treasurer',
    'last_login' => 'Feb 4, 2026',
    'joined' => 'Jan 28, 2026',
    'status' => 'Inactive',
    'birthdate' => '09/21/1980',
    'gender' => 'Female',
    'civil_status' => 'Married',
    'citizenship' => 'Filipino',
    'address' => 'Sitio Uno, Brgy. Kalayaan, Angono, Rizal',
  ],
  [
    'staff_id' => 'STF-0000000005',
    'name' => 'Ramon Lim',
    'email' => 'ramon.lim@kalayaan.gov.ph',
    'contact' => '09199855241',
    'role' => 'Administrative Staff',
    'last_login' => 'Jan 2, 2026',
    'joined' => 'Jan 5, 2026',
    'status' => 'Active',
    'birthdate' => '12/05/1992',
    'gender' => 'Male',
    'civil_status' => 'Single',
    'citizenship' => 'Filipino',
    'address' => 'Blk 5 Lot 12, Brgy. Kalayaan, Angono, Rizal',
  ],
];

$totalStaff = count($staffs);
$activeCount = count(array_filter($staffs, fn($s) => strtolower($s['status']) === 'active'));
$inactiveCount = count(array_filter($staffs, fn($s) => strtolower($s['status']) === 'inactive'));

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
  <title>Staff Management - CitiServe</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="CSS/admin_staff.css">
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

    <div class="nav-item has-dropdown active">
      <span class="nav-text">User Management</span>
      <span class="nav-chevron">⏷</span>
      <div class="nav-dropdown">
        <a href="admin_residents.php" class="nav-dropdown-item">Residents</a>
        <a href="admin_staff.php" class="nav-dropdown-item">Staff</a>
      </div>
    </div>

    <a href="admin_account_verification.php" class="nav-item"><span class="nav-text">Account Verification</span></a>
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
      <a href="admin_profile.php" class="profile-panel-item"><img src="images/my_profile.png" class="profile-panel-icon1" alt=""><span>My Profile</span></a>
      <a href="login.php" class="profile-panel-item logout"><img src="images/logout.png" class="profile-panel-icon2" alt=""><span>Logout</span></a>
    </div>
  </div>
</nav>

<div class="page-body">
  <div class="admin-doc-card">

    <div class="page-head staff-page-head">
      <div>
        <h1 class="page-title">Staff Management</h1>
        <p class="page-subtitle">Manage barangay staff accounts and access.</p>
      </div>

      <img src="images/success_creation_notif.png" alt="Success" class="staff-success-notif" id="staffSuccessNotif">

      <button type="button" class="add-staff-btn">
        <img src="images/add_new_staff.png" alt="Add New Staff">
      </button>
    </div>

    <div class="admin-summary-row">
      <div class="summary-box summary-total">
        <div class="summary-number"><?= $totalStaff ?></div>
        <div class="summary-label">Total Staff</div>
        <img src="images/stafftotal_faded_icon.png" class="summary-icon1">
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
          <input type="text" id="searchInput" placeholder="Search by name, email, or role...">
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
        <div>Staff ID</div>
        <div>Name</div>
        <div>Contact</div>
        <div>Role</div>
        <div>Last Login</div>
        <div>Status</div>
        <div></div>
      </div>

      <div class="requests-table-body" id="requestsTableBody">
        <?php foreach ($staffs as $staff): ?>
          <div class="request-row"
            data-staff-id="<?= htmlspecialchars($staff['staff_id']) ?>"
            data-name="<?= htmlspecialchars($staff['name']) ?>"
            data-email="<?= htmlspecialchars($staff['email']) ?>"
            data-contact="<?= htmlspecialchars($staff['contact']) ?>"
            data-role="<?= htmlspecialchars($staff['role']) ?>"
            data-last-login="<?= htmlspecialchars($staff['last_login']) ?>"
            data-joined="<?= htmlspecialchars($staff['joined']) ?>"
            data-status="<?= htmlspecialchars($staff['status']) ?>"
            data-initials="<?= htmlspecialchars(getInitials($staff['name'])) ?>"
            data-birthdate="<?= htmlspecialchars($staff['birthdate']) ?>"
            data-gender="<?= htmlspecialchars($staff['gender']) ?>"
            data-civil="<?= htmlspecialchars($staff['civil_status']) ?>"
            data-citizenship="<?= htmlspecialchars($staff['citizenship']) ?>"
            data-address="<?= htmlspecialchars($staff['address']) ?>">

            <div class="request-cell request-id"><?= htmlspecialchars($staff['staff_id']) ?></div>

            <div class="request-cell resident-name-cell">
              <div class="resident-initials"><?= htmlspecialchars(getInitials($staff['name'])) ?></div>
              <div>
                <div class="resident-fullname"><?= htmlspecialchars($staff['name']) ?></div>
                <div class="resident-email"><?= htmlspecialchars($staff['email']) ?></div>
              </div>
            </div>

            <div class="request-cell request-date"><?= htmlspecialchars($staff['contact']) ?></div>
            <div class="request-cell document-name"><?= htmlspecialchars($staff['role']) ?></div>
            <div class="request-cell request-date"><?= htmlspecialchars($staff['last_login']) ?></div>

            <div class="request-cell">
              <img 
                src="<?= strtolower($staff['status']) === 'active' 
                  ? 'images/resident_active_staff.png' 
                  : 'images/resident_inactive_staff.png' ?>" 
                alt="<?= htmlspecialchars($staff['status']) ?>" 
                class="resident-status-img">
            </div>

            <div class="request-cell staff-action-cell">
              <button type="button" class="remove-staff-btn">Remove</button>
              <span class="staff-action-separator">|</span>
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

<!------------------------- STAFF PROFILE MODAL ------------------------->
<div class="resident-profile-modal" id="staffProfileModal">
  <div class="resident-profile-box staff-profile-box">
    <button type="button" class="resident-profile-close-x" id="staffProfileCloseX">×</button>

    <div class="resident-profile-title">Staff Profile</div>
    <div class="resident-profile-divider"></div>

    <div class="resident-profile-header">
      <div class="resident-profile-avatar" id="profileInitials">MS</div>

      <div class="resident-profile-head-text">
        <div class="resident-profile-name" id="profileName">Mario Santos</div>
        <div class="resident-profile-id">
          <span id="profileId">STF-0000000003</span> · <span id="profileRole">Barangay Secretary</span>
        </div>
        <img src="images/resident_active_staff.png" class="staff-profile-status-img" id="profileStatusImg">
      </div>
    </div>

    <div class="resident-profile-divider"></div>

    <div class="resident-profile-grid">
      <div>
        <label>Email</label>
        <p id="profileEmail"></p>
      </div>
      <div>
        <label>Contact Number</label>
        <p id="profileContact"></p>
      </div>
      <div>
        <label>Date of Birth</label>
        <p id="profileBirthdate"></p>
      </div>
      <div>
        <label>Gender</label>
        <p id="profileGender"></p>
      </div>
      <div>
        <label>Civil Status</label>
        <p id="profileCivil"></p>
      </div>
      <div>
        <label>Citizenship</label>
        <p id="profileCitizenship"></p>
      </div>
      <div class="profile-address">
        <label>Address</label>
        <p id="profileAddress"></p>
      </div>
      <div>
        <label>Date Joined</label>
        <p id="profileJoined"></p>
      </div>
      <div>
        <label>Last Login</label>
        <p id="profileLastLogin"></p>
      </div>
    </div>

    <div class="resident-profile-actions staff-profile-actions">
      <button type="button" class="resident-profile-img-btn" id="toggleStaffStatusBtn">
        <img src="images/resident_mark_inactive.png" alt="Mark as Inactive" id="toggleStaffStatusImg">
      </button>

      <button type="button" class="resident-profile-img-btn" id="staffProfileCloseBtn">
        <img src="images/resident_close_profile.png" alt="Close">
      </button>
    </div>
  </div>
</div>

<!------------------------- ADD STAFF  MODAL ------------------------->
<div class="add-staff-modal" id="addStaffModal">
  <div class="add-staff-box">
    <button type="button" class="add-staff-close" id="addStaffCloseX">×</button>

    <div class="add-staff-title">Add New Staff Account</div>
    <div class="resident-profile-divider"></div>

    <form class="add-staff-form">
      <div class="add-staff-field">
        <label>Staff ID</label>
        <input type="text" value="STF-0000000011" disabled>
        <small>Auto-generated. Cannot be edited.</small>
      </div>

      <div class="add-staff-row">
        <div class="add-staff-field">
          <label>First Name <span>*</span></label>
          <input type="text" placeholder="e.g. Juan">
        </div>

        <div class="add-staff-field">
          <label>Last Name <span>*</span></label>
          <input type="text" placeholder="e.g. Dela Cruz">
        </div>
      </div>

      <div class="add-staff-field">
        <label>Email Address <span>*</span></label>
        <input type="email" placeholder="e.g juand@email.com">
      </div>

      <div class="add-staff-field">
        <label>Contact Number <span>*</span></label>
        <input type="text" placeholder="09XX-XXX-XXXX">
      </div>

      <div class="add-staff-field">
        <label>Role</label>

        <div class="add-staff-select" id="addStaffRoleBox">
          <button type="button" class="add-staff-select-btn">
            <span id="addStaffSelectedRole">Barangay Captain</span>
            <span>▾</span>
          </button>

          <div class="add-staff-dropdown" id="addStaffRoleDropdown">
            <div class="add-staff-option" data-value="Barangay Captain">Barangay Captain</div>
            <div class="add-staff-option" data-value="Kagawad">Kagawad</div>
            <div class="add-staff-option" data-value="Barangay Secretary">Barangay Secretary</div>
            <div class="add-staff-option" data-value="Barangay Treasurer">Barangay Treasurer</div>
            <div class="add-staff-option" data-value="Administrative Staff">Administrative Staff</div>
          </div>
        </div>
      </div>

      <div class="add-staff-actions">
        <button type="button" class="add-staff-img-btn" id="addStaffCancel">
          <img src="images/add_new_staff_cancel.png" alt="Cancel">
        </button>

        <button type="button" class="add-staff-img-btn add-staff-submit" id="addStaffSubmit">
          <img src="images/add_new_staff_proceed.png" alt="Add New Staff">
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ================= REMOVE STAFF MODAL ================= -->
<div class="remove-staff-modal" id="removeStaffModal">
  <div class="remove-staff-box">

    <div class="remove-staff-content">
      <div class="remove-icon-box">
        <img src="images/remove_staff_icon.png" alt="">
      </div>

      <div class="remove-texts">
        <div class="remove-title">Remove Staff Account</div>
        <div class="remove-subtitle">
          This will remove the staff account from the system.
        </div>
      </div>
    </div>

    <div class="remove-actions">
      <button class="remove-img-btn" id="confirmRemoveStaff">
        <img src="images/remove_staff_button_procees.png" alt="Remove">
      </button>

      <button class="remove-img-btn" id="cancelRemoveStaff">
        <img src="images/remove_staff_button_cancel.png" alt="Cancel">
      </button>
    </div>

  </div>
</div>


<script src="JS/admin_staff.js"></script>
</body>
</html>