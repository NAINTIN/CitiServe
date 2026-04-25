<?php
// ==============================
// MOCK SESSION — replace with real session/DB later
// ==============================
session_start();


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
// Mock user data — replace with DB query later
$user = [
  'full_name'  => 'Juan Dela Cruz',
  'first_name'    => 'Juan',
  'middle_name'   => 'N/A',
  'last_name'     => 'Dela Cruz',
  'suffix'        => '',
  'contact'       => '096748791829',
  'email'         => 'juand@gmail.com',
  'dob'           => '2000-03-19',
  'civil_status'  => 'Married',
  'citizenship'   => 'Filipino',
  'gender'        => 'Male',
  'address'       => '123 Rizal St., Brgy. Kalayaan, Angono, Rizal',
  'member_since'  => '04-10-2026',
  'account_type'  => 'Fully Verified',
  'status'        => 'Active',
  'avatar'        => 'images/profile-my-profile.png',
  'role_label'    => 'Resident &middot; Brgy. Kalayaan',
];

// Verification status — replace with DB value later
// Options: "verified", "pending", "rejected"
$verificationStatus = 'rejected';

// Handle Profile Save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_profile') {
  // TODO: validate & save to DB
  $user['first_name']   = htmlspecialchars($_POST['first_name'] ?? $user['first_name']);
  $user['middle_name']  = htmlspecialchars($_POST['middle_name'] ?? $user['middle_name']);
  $user['last_name']    = htmlspecialchars($_POST['last_name'] ?? $user['last_name']);
  $user['suffix']       = htmlspecialchars($_POST['suffix'] ?? $user['suffix']);
  $user['contact']      = htmlspecialchars($_POST['contact'] ?? $user['contact']);
  $user['email']        = htmlspecialchars($_POST['email'] ?? $user['email']);
  $user['dob']          = htmlspecialchars($_POST['dob'] ?? $user['dob']);
  $user['civil_status'] = htmlspecialchars($_POST['civil_status'] ?? $user['civil_status']);
  $user['citizenship']  = htmlspecialchars($_POST['citizenship'] ?? $user['citizenship']);
  $user['gender']       = htmlspecialchars($_POST['gender'] ?? $user['gender']);
  $user['address']      = htmlspecialchars($_POST['address'] ?? $user['address']);
  $profileSaved = true;
}

// Handle Password Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
  // TODO: validate old password & update in DB
  $passwordSaved = true;
}

// Handle Document Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_doc') {
  if (!empty($_FILES['doc_file']['name'])) {
    // TODO: move_uploaded_file() to your uploads folder & save path to DB
    $uploadedFileName = htmlspecialchars($_FILES['doc_file']['name']);
    $docUploaded = true;
  }
}

// Suffix options
$suffixes = ['Jr.','Sr.','II','III','IV','V','VI','VII','VIII','IX','X'];

// Civil status options
$civilOptions = ['Single','Married','Widowed','Separated','Annulled'];

// Gender options
$genderOptions = ['Male','Female'];

// Full name helper
$fullName = trim($user['first_name'] . ' ' . $user['last_name']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile – CitiServe</title>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/my_profile.css">
</head>
<body>

<div class="design-strip left" aria-hidden="true"><img src="images/complaint_design.png" alt=""></div>
<div class="design-strip right" aria-hidden="true"><img src="images/complaint_design.png" alt=""></div>
<nav class="navbar">
  <a href="dashboard.php" class="navbar-logo">
    <img src="images/logo_pink .png" alt="CitiServe">
  </a>

  <div class="navbar-nav">
    <a href="dashboard.php" class="nav-item">
      <span class="nav-text">Dashboard</span>
    </a>

    <div class="nav-item has-dropdown active" id="navDocReq">
      <span class="nav-text">Document Requests</span>
      <span class="nav-chevron">⏷</span>
      <div class="nav-dropdown">
        <a href="document.php" class="nav-dropdown-item">Request Document</a>
        <a href="my_requests.php" class="nav-dropdown-item">My Requests</a>
      </div>
    </div>

    <div class="nav-item has-dropdown" id="navComplaint">
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
<div class="content-area">

  <img src="images/profile-bg-top.png" style="position:absolute; top:20px; right:37%; width:275px; pointer-events:none; z-index:0;" onerror="this.style.display='none'">
  <img src="images/profile-bg-mid.png" style="position:absolute; top:0%; right:30px; width:275px; pointer-events:none; z-index:0;" onerror="this.style.display='none'">

  <!-- BREADCRUMB -->
  <div class="form-breadcrumb">
    <a href="dashboard.php">Dashboard</a>
    <span class="form-sep">></span>
    <span class="form-active">My Profile</span>
  </div>

  <h1 class="form-title">My Profile</h1>
  <p class="form-subtitle">Manage your account information and password.</p>

  <div class="profile-wrapper">

    <!-- SIDEBAR -->
    <aside class="profile-side">
      <div class="avatar-card">
        <div class="avatar-section">
          <div class="avatar-wrap">
            <img class="avatar-img" src="<?= htmlspecialchars($user['avatar']) ?>" alt="<?= htmlspecialchars($fullName) ?>"/>
            <form method="POST" enctype="multipart/form-data" id="avatarForm" style="display:none;">
              <input type="hidden" name="action" value="upload_avatar">
              <input type="file" id="avatarInput" name="avatar" accept="image/*" onchange="document.getElementById('avatarForm').submit()">
            </form>
            <button class="avatar-edit-btn" onclick="document.getElementById('avatarInput').click()">
              <img src="images/profile-edit.png" alt="Edit" onerror="this.style.display='none'">
            </button>
          </div>
          <div class="user-name" id="userName"><?= htmlspecialchars($fullName) ?></div>
          <div class="user-role" id="userRole"><?= $user['role_label'] ?></div>
        </div>
        <div class="avatar-divider"></div>
        <div class="meta-table">
          <div class="meta-row">
            <span class="meta-label">Member since</span>
            <span class="meta-val"><?= htmlspecialchars($user['member_since']) ?></span>
          </div>
          <div class="meta-row">
            <span class="meta-label">Account Type</span>
            <span class="meta-val" id="accountTypeVal"><?= htmlspecialchars($user['account_type']) ?></span>
          </div>
          <div class="meta-row">
            <span class="meta-label">Status</span>
            <span class="badge-active"><?= htmlspecialchars($user['status']) ?></span>
          </div>
        </div>
      </div>

      <div class="sidenav-card">
        <a href="#profile" class="sidenav-item active">
          <img src="images/profile-pf-icon.png"
               data-src="images/change-pass-black-icon.png"
               data-pink="images/profile-pf-icon.png" alt="">
          Profile Information
        </a>
        <a href="#verification" class="sidenav-item">
          <img src="images/profile-change-password.png"
               data-src="images/profile-change-password.png"
               data-pink="images/profile-acc-very-pink.png" alt="">
          Account Verification
        </a>
        <a href="#password" class="sidenav-item">
          <img src="images/profile-account-verification.png"
               data-src="images/profile-account-verification.png"
               data-pink="images/profile-icon-pink.png" alt="">
          Change Password
        </a>
      </div>
    </aside>

    <!-- MAIN -->
    <div class="profile-main">

      <!-- ── SECTION: Profile Information ── -->
      <div id="section-profile" class="profile-section active">
        <div class="form-card">
          <div class="form-card-bar">
            Profile Information
            <small style="font-weight:400; font-size:11px;">Update your personal details.</small>
          </div>
          <form method="POST">
            <input type="hidden" name="action" value="save_profile">
            <div class="form-card-body">
              <div class="form-row-4">
                <div class="form-group">
                  <label>First Name <span class="req">*</span></label>
                  <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>">
                </div>
                <div class="form-group">
                  <label>Middle Name <span class="opt">(Optional)</span></label>
                  <input type="text" name="middle_name" value="<?= htmlspecialchars($user['middle_name']) ?>">
                </div>
                <div class="form-group">
                  <label>Last Name <span class="req">*</span></label>
                  <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>">
                </div>
                <div class="form-group">
                  <label>Suffix <span class="opt">(Optional)</span></label>
                  <div class="custom-select <?= !empty($user['suffix']) ? 'filled' : '' ?>" id="suffixDropdown">
                    <input type="hidden" name="suffix" id="suffixVal" value="<?= htmlspecialchars($user['suffix']) ?>">
                    <div class="custom-select-selected" onclick="toggleDropdown('suffixDropdown')">
                      <span class="custom-select-text"><?= !empty($user['suffix']) ? htmlspecialchars($user['suffix']) : 'Select suffix' ?></span>
                      <span class="custom-select-arrow">▾</span>
                    </div>
                    <div class="custom-select-options">
                      <?php foreach ($suffixes as $s): ?>
                        <div class="custom-select-option <?= $user['suffix'] === $s ? 'selected' : '' ?>"
                             onclick="selectOption('suffixDropdown','<?= $s ?>','suffixVal')"><?= $s ?></div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-row-2">
                <div class="form-group">
                  <label>Contact Number <span class="req">*</span></label>
                  <input type="text" name="contact" value="<?= htmlspecialchars($user['contact']) ?>">
                </div>
                <div class="form-group">
                  <label>Email Address</label>
                  <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">
                </div>
              </div>

              <div class="form-row-4">
                <div class="form-group">
                  <label>Date of Birth <span class="req">*</span></label>
                  <input type="date" name="dob" value="<?= htmlspecialchars($user['dob']) ?>">
                </div>
                <div class="form-group">
                  <label>Civil Status <span class="req">*</span></label>
                  <div class="custom-select filled" id="civilDropdown">
                    <input type="hidden" name="civil_status" id="civilVal" value="<?= htmlspecialchars($user['civil_status']) ?>">
                    <div class="custom-select-selected" onclick="toggleDropdown('civilDropdown')">
                      <span class="custom-select-text"><?= htmlspecialchars($user['civil_status']) ?></span>
                      <span class="custom-select-arrow">▾</span>
                    </div>
                    <div class="custom-select-options">
                      <?php foreach ($civilOptions as $c): ?>
                        <div class="custom-select-option <?= $user['civil_status'] === $c ? 'selected' : '' ?>"
                             onclick="selectOption('civilDropdown','<?= $c ?>','civilVal')"><?= $c ?></div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label>Citizenship <span class="req">*</span></label>
                  <input type="text" name="citizenship" value="<?= htmlspecialchars($user['citizenship']) ?>">
                </div>
                <div class="form-group">
                  <label>Gender <span class="req">*</span></label>
                  <div class="custom-select filled" id="genderDropdown">
                    <input type="hidden" name="gender" id="genderVal" value="<?= htmlspecialchars($user['gender']) ?>">
                    <div class="custom-select-selected" onclick="toggleDropdown('genderDropdown')">
                      <span class="custom-select-text"><?= htmlspecialchars($user['gender']) ?></span>
                      <span class="custom-select-arrow">▾</span>
                    </div>
                    <div class="custom-select-options">
                      <?php foreach ($genderOptions as $g): ?>
                        <div class="custom-select-option <?= $user['gender'] === $g ? 'selected' : '' ?>"
                             onclick="selectOption('genderDropdown','<?= $g ?>','genderVal')"><?= $g ?></div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label>Complete Address <span class="req">*</span></label>
                <input type="text" name="address" value="<?= htmlspecialchars($user['address']) ?>">
              </div>
            </div>

            <div class="form-btn-row">
              <?php if (!empty($profileSaved)): ?>
                <div class="success-banner">
                  <img src="images/profile-fully-verify-green.png" alt="">
                </div>
              <?php endif; ?>
              <button class="form-btn" type="submit">
                <img src="images/profile-save-changes.png" alt="Save Changes">
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- ── SECTION: Account Verification ── -->
      <div id="section-verification" class="profile-section">
        <div class="form-card">
          <div class="form-card-bar">
            Account Verification
            <small style="font-weight:400; font-size:11px;">Your identity verification status and details.</small>
          </div>
          <div class="form-card-body verif-body">
            <div class="verif-left">
              <div class="verif-box">
                <div class="verif-box-title">Verification Details</div>
                <div class="verif-box-content">
                  <div class="verif-details-row">
                    <div>
                      <p class="label">Verification Status</p>
                      <?php
                        $statusImgMap = [
                          'verified' => 'images/profile-fully-verified.png',
                          'pending'  => 'images/profile-pending.png',
                          'rejected' => 'images/profile-final-rehected.png',
                        ];
                      ?>
                      <img src="<?= $statusImgMap[$verificationStatus] ?>" id="statusImg">
                    </div>
                    <div>
                      <p class="label">Date Submitted</p>
                      <p class="value">04-10-2026</p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="verif-box">
                <div class="verif-box-title">Submitted Document</div>
                <div class="verif-box-content">
                  <form method="POST" enctype="multipart/form-data" id="docForm">
                    <input type="hidden" name="action" value="upload_doc">
                    <?php
                      $uploadDisabled = in_array($verificationStatus, ['verified', 'pending']);
                      $uploadStyle = $uploadDisabled
                        ? 'pointer-events:none; opacity:0.5; cursor:not-allowed;'
                        : 'cursor:pointer;';
                    ?>
                    <div class="upload-area" id="uploadArea"
                         style="<?= $uploadStyle ?>"
                         <?= !$uploadDisabled ? 'onclick="document.getElementById(\'docInput\').click()"' : '' ?>>
                      <div class="upload-placeholder" id="uploadPlaceholder" <?= !empty($docUploaded) ? 'style="display:none"' : '' ?>>
                        <img src="images/click-to-upload.png" alt="" style="width:30px; height:28px; margin-bottom:2px;">
                        <p>Click to upload file</p>
                      </div>
                      <div class="upload-result" id="uploadedFile" style="<?= !empty($docUploaded) ? 'display:flex' : 'display:none' ?>">
                        <img src="https://api.iconify.design/lucide/circle-check.svg?color=%2316a34a" alt="">
                        <span id="uploadedFileName"><?= !empty($uploadedFileName) ? htmlspecialchars($uploadedFileName) : '' ?></span>
                      </div>
                    </div>
                    <input type="file" id="docInput" name="doc_file" accept=".jpg,.jpeg,.png,.pdf"
                           style="display:none;" onchange="handleDocUpload(event)">
                  </form>
                  <p class="upload-hint">Upload front side of any valid government-issued ID (JPG, PNG, PDF · max 5MB)</p>
                </div>
              </div>
            </div>

            <div class="verif-right">
              <?php
                $rightTopMap = [
                  'verified' => 'images/profile-your-account-is-verified.png',
                  'pending'  => 'images/profile-pending1.png',
                  'rejected' => 'images/profile-rejected1.png',
                ];
                $rightBottomMap = [
                  'verified' => 'images/profile-what-you-can-do-now.png',
                  'pending'  => 'images/profile-pending2.png',
                  'rejected' => 'images/profile-rejected2.png',
                ];
                $submitBtnMap = [
                  'verified' => 'images/profile-cant-submit-agin.png',
                  'pending'  => 'images/profile-cant-submit-agin.png',
                  'rejected' => 'images/profile-submit-na-naman.png',
                ];
              ?>
              <div class="verif-images">
                <img src="<?= $rightTopMap[$verificationStatus] ?>" id="rightTopImg">
                <img src="<?= $rightBottomMap[$verificationStatus] ?>" id="rightBottomImg">
              </div>
              <div class="submit-again-row">
                <?php if ($verificationStatus === 'rejected'): ?>
                  <img src="<?= $submitBtnMap[$verificationStatus] ?>" id="submitBtn"
                       onclick="document.getElementById('docForm').submit()" style="cursor:pointer;">
                <?php else: ?>
                  <img src="<?= $submitBtnMap[$verificationStatus] ?>" id="submitBtn">
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ── SECTION: Change Password ── -->
      <div id="section-password" class="profile-section">
        <div class="form-card">
          <div class="form-card-bar">
            Change Password
            <small style="font-weight:400; font-size:11px;">Use a strong password of at least 8 characters.</small>
          </div>
          <form method="POST">
            <input type="hidden" name="action" value="change_password">
            <div class="form-card-body">
              <div class="form-group">
                <label>Current Password <span class="req">*</span></label>
                <div class="pw-wrap">
                  <input type="password" name="current_pw" id="currentPw" placeholder="Enter current password">
                  <button class="pw-eye" type="button" onclick="togglePw('currentPw', this)" tabindex="-1">
                    <img src="images/eye.png" alt="Show">
                  </button>
                </div>
              </div>
              <div class="form-group">
                <label>New Password <span class="req">*</span></label>
                <div class="pw-wrap">
                  <input type="password" name="new_pw" id="newPw" placeholder="Enter new password">
                  <button class="pw-eye" type="button" onclick="togglePw('newPw', this)" tabindex="-1">
                    <img src="images/eye.png" alt="Show">
                  </button>
                </div>
              </div>
              <div class="form-group">
                <label>Confirm New Password <span class="req">*</span></label>
                <div class="pw-wrap">
                  <input type="password" name="confirm_pw" id="confirmPw" placeholder="Confirm new password">
                  <button class="pw-eye" type="button" onclick="togglePw('confirmPw', this)" tabindex="-1">
                    <img src="images/eye.png" alt="Show">
                  </button>
                </div>
              </div>
              <div class="pw-requirements">
                <p class="pw-req-title">Password Requirements:</p>
                <ul>
                  <li>At least 8 characters long</li>
                  <li>Mix of letters and numbers recommended</li>
                  <li>Avoid using personal information</li>
                </ul>
              </div>
            </div>
            <div class="form-btn-row">
              <?php if (!empty($passwordSaved)): ?>
                <div class="success-banner">
                  <img src="images/profile-success-pass.png" alt="Success">
                </div>
              <?php endif; ?>
              <button class="form-btn" type="submit">
                <img src="images/profile-change-pass-btn.png" alt="Change Password">
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>

  <div class="form-logo">
    <div class="form-logo-text">
      <span class="logo-pink">CitiServe</span>
      <span class="logo-gray"> © 2026. All rights reserved.</span>
    </div>
  </div>
</div>
<script src="js/my_profile.js" defer></script>
</body>
</html>