<?php
function e($val) { return htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8'); }

$staff = [
    'staff_id'    => 'STF-0000000003',
    'first_name'  => 'Jasmine',
    'middle_name' => 'Moblanco',
    'last_name'   => 'Armayan',
    'suffix'      => '',
    'contact'     => '096748791829',
    'email'       => 'jasminearmayan@gmail.com',
    'dob'         => '2000-03-19',
    'civil_status'=> 'Married',
    'citizenship' => 'Filipino',
    'gender'      => 'Female',
    'address'     => '123 Rizal St., Brgy. Kalayaan, Angono, Rizal',
    'role'        => 'Barangay Secretary',
    'avatar'      => '',
    'status'      => 'Active',
    'created_at'  => '2026-04-01',
];

$full_name   = e($staff['first_name']) . ' ' . e($staff['last_name']);
$avatar_src  = 'images/profile-staff-profile.png';
$joined_date = '04/01/2026';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Staff Profile – CitiServe</title>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/staff_profile.css">
</head>
<body>

<div class="content-area">

  <img src="images/profile-bg-top.png" style="position:absolute; top:20px; right:37%; width:275px; pointer-events:none; z-index:0;" onerror="this.style.display='none'">
  <img src="images/profile-bg-mid.png" style="position:absolute; top:0%; right:30px; width:275px; pointer-events:none; z-index:0;" onerror="this.style.display='none'">

  <!-- Breadcrumb -->
  <div class="form-breadcrumb">
    <a href="dashboard.php">Dashboard</a>
    <span class="form-sep">></span>
    <span class="form-active">Staff Profile</span>
  </div>

  <h1 class="form-title">Staff Profile</h1>
  <p class="form-subtitle">Manage your staff account information and password.</p>

  <div class="profile-wrapper">

    <!-- ── SIDEBAR ── -->
    <aside class="profile-side">
      <div class="avatar-card">
        <div class="avatar-section">
          <div class="avatar-wrap">
            <img class="avatar-img" id="avatarPreview"
              src="<?= $avatar_src ?>"
              alt="<?= $full_name ?>"/>
            <input type="file" id="avatarInput" accept="image/*" style="display:none;" onchange="previewAvatar(event)">
            <button class="avatar-edit-btn" onclick="document.getElementById('avatarInput').click()">
              <img src="images/profile-edit.png" alt="Edit" onerror="this.outerHTML='<span style=\'font-size:16px;cursor:pointer\'>✏️</span>'">
            </button>
          </div>
          <div class="user-name" id="userName"><?= $full_name ?></div>
          <div class="user-role" id="userRole"><?= e($staff['role']) ?></div>
        </div>
        <div class="avatar-divider"></div>
        <div class="meta-table">
          <div class="meta-row">
            <span class="meta-label">Staff ID</span>
            <span class="meta-val"><?= e($staff['staff_id']) ?></span>
          </div>
          <div class="meta-row">
            <span class="meta-label">Joined</span>
            <span class="meta-val"><?= $joined_date ?></span>
          </div>
          <div class="meta-row">
            <span class="meta-label">Status</span>
            <span class="badge-active"><?= e($staff['status']) ?></span>
          </div>
        </div>
      </div>

      <div class="sidenav-card">
        <a href="#profile" class="sidenav-item active">
          <img src="images/profile-pf-icon.png"
               data-src="images/change-pass-black-icon.png"
               data-pink="images/profile-pf-icon.png" alt="" onerror="this.style.display='none'">
          Profile Information
        </a>
        <a href="#password" class="sidenav-item">
          <img src="images/profile-account-verification.png"
               data-src="images/profile-account-verification.png"
               data-pink="images/profile-icon-pink.png" alt="" onerror="this.style.display='none'">
          Change Password
        </a>
      </div>
    </aside>

    <!-- ── MAIN ── -->
    <div class="profile-main">

      <!-- ── SECTION: Profile Information ── -->
      <div id="section-profile" class="profile-section active">
        <div class="form-card">
          <div class="form-card-bar">
            Profile Information
            <small style="font-weight:400; font-size:11px;">Update your staff account details.</small>
          </div>
          <div class="form-card-body">

            <div class="form-row-4">
              <div class="form-group">
                <label>First Name <span class="req">*</span></label>
                <input type="text" id="firstNameInput" name="first_name"
                       value="<?= e($staff['first_name']) ?>" placeholder="First name">
              </div>
              <div class="form-group">
                <label>Middle Name <span class="opt">(Optional)</span></label>
                <input type="text" id="middleNameInput" name="middle_name"
                       value="<?= e($staff['middle_name']) ?>" placeholder="Middle name">
              </div>
              <div class="form-group">
                <label>Last Name <span class="req">*</span></label>
                <input type="text" id="lastNameInput" name="last_name"
                       value="<?= e($staff['last_name']) ?>" placeholder="Last name">
              </div>
              <div class="form-group">
                <label>Suffix <span class="opt">(Optional)</span></label>
                <input type="hidden" name="suffix" id="suffix_val" value="<?= e($staff['suffix']) ?>">
                <div class="custom-select <?= !empty($staff['suffix']) ? 'filled' : '' ?>" id="suffixDropdown">
                  <div class="custom-select-selected" onclick="toggleDropdown('suffixDropdown')">
                    <span class="custom-select-text"><?= !empty($staff['suffix']) ? e($staff['suffix']) : 'Select suffix' ?></span>
                    <span class="custom-select-arrow">▾</span>
                  </div>
                  <div class="custom-select-options">
                    <?php foreach (['Jr.','Sr.','II','III','IV','V','VI','VII','VIII','IX','X'] as $s): ?>
                    <div class="custom-select-option <?= $staff['suffix'] === $s ? 'selected' : '' ?>"
                         onclick="selectOption('suffixDropdown','<?= $s ?>')"><?= $s ?></div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-row-2">
              <div class="form-group">
                <label>Contact Number <span class="req">*</span></label>
                <input type="text" id="contactInput" name="contact"
                       value="<?= e($staff['contact']) ?>" placeholder="Contact number">
              </div>
              <div class="form-group">
                <label>Email Address <span class="req">*</span></label>
                <input type="email" id="emailInput" name="email"
                       value="<?= e($staff['email']) ?>" placeholder="Email address">
              </div>
            </div>

            <div class="form-row-4">
              <div class="form-group">
                <label>Date of Birth <span class="req">*</span></label>
                <input type="date" id="dobInput" name="dob"
                       value="<?= e($staff['dob']) ?>">
              </div>
              <div class="form-group">
                <label>Civil Status <span class="req">*</span></label>
                <input type="hidden" name="civil_status" id="civilStatus_val" value="<?= e($staff['civil_status']) ?>">
                <div class="custom-select filled" id="civilDropdown">
                  <div class="custom-select-selected" onclick="toggleDropdown('civilDropdown')">
                    <span class="custom-select-text"><?= e($staff['civil_status']) ?></span>
                    <span class="custom-select-arrow">▾</span>
                  </div>
                  <div class="custom-select-options">
                    <?php foreach (['Single','Married','Widowed','Separated','Annulled'] as $cs): ?>
                    <div class="custom-select-option <?= $staff['civil_status'] === $cs ? 'selected' : '' ?>"
                         onclick="selectOption('civilDropdown','<?= $cs ?>')"><?= $cs ?></div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label>Citizenship <span class="req">*</span></label>
                <input type="text" id="citizenshipInput" name="citizenship"
                       value="<?= e($staff['citizenship']) ?>" placeholder="Citizenship">
              </div>
              <div class="form-group">
                <label>Gender <span class="req">*</span></label>
                <input type="hidden" name="gender" id="gender_val" value="<?= e($staff['gender']) ?>">
                <div class="custom-select filled" id="genderDropdown">
                  <div class="custom-select-selected" onclick="toggleDropdown('genderDropdown')">
                    <span class="custom-select-text"><?= e($staff['gender']) ?></span>
                    <span class="custom-select-arrow">▾</span>
                  </div>
                  <div class="custom-select-options">
                    <?php foreach (['Male','Female','Other'] as $g): ?>
                    <div class="custom-select-option <?= $staff['gender'] === $g ? 'selected' : '' ?>"
                         onclick="selectOption('genderDropdown','<?= $g ?>')"><?= $g ?></div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-row-2">
              <div class="form-group">
                <label>Complete Address <span class="req">*</span></label>
                <input type="text" id="addressInput" name="address"
                       value="<?= e($staff['address']) ?>" placeholder="Complete address">
              </div>
              <div class="form-group">
                <label>Role</label>
                <input type="text" value="<?= e($staff['role']) ?>" disabled
                       style="background:#fff; color:#111; cursor:default;">
              </div>
            </div>

          </div>
          <div class="form-btn-row">
            <div class="success-banner" id="successBanner" style="display:none;">
              <img src="images/profile-fully-verify-green.png" alt="Saved!">
            </div>
            <div class="error-msg" id="profileError" style="display:none;"></div>
            <button class="btn-save" type="button" onclick="handleSave()">
              <img src="images/profile-save-changes.png" alt="Save Changes">
            </button>
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
          <div class="form-card-body">

            <div class="form-group">
              <label>Current Password <span class="req">*</span></label>
              <div class="pw-wrap">
                <input type="password" id="currentPw" placeholder="Enter current password">
                <button class="pw-eye" onclick="togglePw('currentPw', this)" tabindex="-1">
                  <img src="images/eye.png" alt="Show" onerror="this.outerHTML='<span>👁</span>'">
                </button>
              </div>
            </div>

            <div class="form-group">
              <label>New Password <span class="req">*</span></label>
              <div class="pw-wrap">
                <input type="password" id="newPw" placeholder="Enter new password">
                <button class="pw-eye" onclick="togglePw('newPw', this)" tabindex="-1">
                  <img src="images/eye.png" alt="Show" onerror="this.outerHTML='<span>👁</span>'">
                </button>
              </div>
            </div>

            <div class="form-group">
              <label>Confirm New Password <span class="req">*</span></label>
              <div class="pw-wrap">
                <input type="password" id="confirmPw" placeholder="Confirm new password">
                <button class="pw-eye" onclick="togglePw('confirmPw', this)" tabindex="-1">
                  <img src="images/eye.png" alt="Show" onerror="this.outerHTML='<span>👁</span>'">
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
            <div class="success-banner" id="pwSuccessBanner" style="display:none;">
              <img src="images/profile-success-pass.png" alt="Password Changed!">
            </div>
            <div class="error-msg" id="pwError" style="display:none;"></div>
            <button class="btn-save" type="button" onclick="handlePwSave()">
              <img src="images/profile-change-pass-btn.png" alt="Change Password">
            </button>
          </div>
        </div>
      </div>

    </div><!-- /.profile-main -->
  </div><!-- /.profile-wrapper -->

  <div class="form-logo">
    <img src="images/docu-logo.png" onerror="this.style.display='none'">
    <div class="form-logo-text">
      <span class="logo-pink">CitiServe</span>
      <span class="logo-gray"> © 2026. All rights reserved.</span>
    </div>
  </div>

</div><!-- /.content-area -->
<script src="js/staff_profile.js" defer></script>
</body>
</html>