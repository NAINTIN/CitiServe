<?php
require_once __DIR__ . '/../../app/helpers/auth.php';
require_once __DIR__ . '/../../app/helpers/csrf.php';
require_once __DIR__ . '/../../app/core/CitiServeData.php';
require_once __DIR__ . '/../../app/helpers/profile_image.php';
require_once __DIR__ . '/../../app/helpers/profile_details.php';
require_once __DIR__ . '/../../app/helpers/admin_notifications.php';

$admin = require_admin();
$data = new CitiServeData();
$user = $data->findUserById((int)$admin['id']);
$profileImagePath = getUserProfileImagePublicPath((int)$admin['id']);
$notifData = build_admin_notifications($data, (int)$admin['id']);
$notifications = $notifData['notifications'];
$notifSections = $notifData['sections'];
$unreadCount = $notifData['unread_count'];
$hasNotif = $unreadCount > 0;

if (!$user) {
    session_start();
    session_unset();
    session_destroy();
    header('Location: /CitiServe/public/login.php');
    exit;
}

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$errors = [];
$success = '';
$activeTab = 'profile';

$fullName = (string)$user->full_name;
$address = (string)($user->address ?? '');
$contactNumber = (string)($user->contact_number ?? '');
$profileDetails = loadUserProfileDetails((int)$user->id);
$nameParts = splitFullNameParts($fullName);
$firstName = $profileDetails['first_name'] !== '' ? (string)$profileDetails['first_name'] : (string)$nameParts['first_name'];
$middleName = $profileDetails['middle_name'] !== '' ? (string)$profileDetails['middle_name'] : (string)$nameParts['middle_name'];
$lastName = $profileDetails['last_name'] !== '' ? (string)$profileDetails['last_name'] : (string)$nameParts['last_name'];
$suffix = $profileDetails['suffix'] !== '' ? (string)$profileDetails['suffix'] : (string)$nameParts['suffix'];
$dob = (string)$profileDetails['dob'];
$civilStatus = (string)$profileDetails['civil_status'];
$citizenship = (string)$profileDetails['citizenship'];
$gender = (string)$profileDetails['gender'];
$currentPassword = '';
$newPassword = '';
$confirmPassword = '';
$suffixes = ['Jr.', 'Sr.', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];
$civilOptions = ['Single', 'Married', 'Widowed', 'Separated', 'Annulled'];
$genderOptions = ['Male', 'Female', 'Other'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify_or_die();
    $action = isset($_POST['action']) ? trim((string)$_POST['action']) : '';

    if ($action === 'upload_avatar') {
        $activeTab = 'profile';
        $avatarFile = $_FILES['avatar'] ?? null;
        if (!$avatarFile) {
            $errors[] = 'Please choose a profile image.';
        } else {
            try {
                $profileImagePath = saveUserProfileImage($avatarFile, (int)$user->id);
                $success = 'Profile picture updated successfully.';
            } catch (Throwable $e) {
                $errors[] = $e->getMessage();
            }
        }
    } elseif ($action === 'save_profile') {
        $activeTab = 'profile';
        $firstName = trim(isset($_POST['first_name']) ? (string)$_POST['first_name'] : '');
        $middleName = trim(isset($_POST['middle_name']) ? (string)$_POST['middle_name'] : '');
        $lastName = trim(isset($_POST['last_name']) ? (string)$_POST['last_name'] : '');
        $suffix = trim(isset($_POST['suffix']) ? (string)$_POST['suffix'] : '');
        $address = trim(isset($_POST['address']) ? (string)$_POST['address'] : '');
        $contactNumber = trim(isset($_POST['contact_number']) ? (string)$_POST['contact_number'] : '');
        $dob = trim(isset($_POST['dob']) ? (string)$_POST['dob'] : '');
        $civilStatus = trim(isset($_POST['civil_status']) ? (string)$_POST['civil_status'] : '');
        $citizenship = trim(isset($_POST['citizenship']) ? (string)$_POST['citizenship'] : '');
        $gender = trim(isset($_POST['gender']) ? (string)$_POST['gender'] : '');

        $fullName = trim($firstName . ' ' . $middleName . ' ' . $lastName . ' ' . $suffix);

        if ($firstName === '' || $lastName === '') {
            $errors[] = 'First name and last name are required.';
        }
        if (!in_array($suffix, array_merge([''], $suffixes), true)) {
            $errors[] = 'Invalid suffix selected.';
        }
        if ($fullName !== '' && mb_strlen($fullName) > 100) {
            $errors[] = 'Full name must be 100 characters or less.';
        }

        if ($address !== '' && mb_strlen($address) > 1000) {
            $errors[] = 'Address is too long.';
        }

        if ($contactNumber !== '' && mb_strlen($contactNumber) > 20) {
            $errors[] = 'Contact number must be 20 characters or less.';
        }
        if ($dob !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob)) {
            $errors[] = 'Date of birth must use YYYY-MM-DD format.';
        }
        if ($civilStatus !== '' && !in_array($civilStatus, $civilOptions, true)) {
            $errors[] = 'Invalid civil status selected.';
        }
        if ($gender !== '' && !in_array($gender, $genderOptions, true)) {
            $errors[] = 'Invalid gender selected.';
        }

        if (empty($errors)) {
            $ok = $data->updateUserProfile(
                (int)$user->id,
                $fullName,
                $address !== '' ? $address : null,
                $contactNumber !== '' ? $contactNumber : null
            );

            if ($ok) {
                saveUserProfileDetails((int)$user->id, [
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'suffix' => $suffix,
                    'dob' => $dob,
                    'civil_status' => $civilStatus,
                    'citizenship' => $citizenship,
                    'gender' => $gender,
                ]);
                $success = 'Profile updated successfully.';
                $user = $data->findUserById((int)$admin['id']);
            } else {
                $errors[] = 'Failed to update profile.';
            }
        }
    } elseif ($action === 'change_password') {
        $activeTab = 'password';
        $currentPassword = isset($_POST['current_password']) ? (string)$_POST['current_password'] : '';
        $newPassword = isset($_POST['new_password']) ? (string)$_POST['new_password'] : '';
        $confirmPassword = isset($_POST['confirm_password']) ? (string)$_POST['confirm_password'] : '';

        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            $errors[] = 'All password fields are required.';
        }

        if (!password_verify($currentPassword, (string)$user->password_hash)) {
            $errors[] = 'Current password is incorrect.';
        }

        if (strlen($newPassword) < 8) {
            $errors[] = 'New password must be at least 8 characters.';
        }

        if ($newPassword !== $confirmPassword) {
            $errors[] = 'New password and confirmation do not match.';
        }

        if ($currentPassword === $newPassword) {
            $errors[] = 'New password must be different from current password.';
        }

        if (empty($errors)) {
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $ok = $data->updateUserPasswordHash((int)$user->id, $newHash);
            if ($ok) {
                $success = 'Password changed successfully.';
                $currentPassword = '';
                $newPassword = '';
                $confirmPassword = '';
            } else {
                $errors[] = 'Failed to change password.';
            }
        }
    }
}

$assetBase = '/CitiServe/frontend/profile%20and%20verification/profile';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Staff Profile - CitiServe</title>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/CitiServe/frontend/admin_dashboard/CSS/admin_dashboard.css">
    <link rel="stylesheet" href="<?= $assetBase ?>/css/staff_profile.css">
    <style>
        .form-group select {
            width: 100%;
            height: 35px;
            padding: 0 14px;
            border: 1.5px solid #E5E7EB;
            border-radius: 3px;
            font-size: 13.6px;
            color: #111;
            background: #fff;
            outline: none;
        }
    </style>
</head>
<body>
<div class="design-strip left" aria-hidden="true"><img src="<?= $assetBase ?>/images/complaint_design.png" alt=""></div>
<div class="design-strip right" aria-hidden="true"><img src="<?= $assetBase ?>/images/complaint_design.png" alt=""></div>
<nav class="navbar">
  <a href="/CitiServe/public/admin/dashboard.php" class="navbar-logo"><img src="/CitiServe/frontend/admin_dashboard/images/logo_pink.png" alt="CitiServe"></a>
  <div class="navbar-nav admin-nav">
    <a href="/CitiServe/public/admin/dashboard.php" class="nav-item"><span class="nav-text">Dashboard</span></a>
    <a href="/CitiServe/public/admin/requests.php" class="nav-item"><span class="nav-text">Document Requests</span></a>
    <a href="/CitiServe/public/admin/complaints.php" class="nav-item"><span class="nav-text">Complaints</span></a>
    <div class="nav-item has-dropdown" id="navUserManagement">
      <span class="nav-text">User Management</span><span class="nav-chevron">⏷</span>
      <div class="nav-dropdown"><a href="/CitiServe/public/admin/users.php" class="nav-dropdown-item">Residents</a></div>
    </div>
    <a href="/CitiServe/public/admin/user_verification.php" class="nav-item"><span class="nav-text">Account Verification</span></a>
  </div>
  <div class="navbar-right admin-navbar-right">
    <button class="notif-btn" id="notifBtn"
      data-has-notif="<?= $hasNotif ? '1' : '0' ?>"
      data-img-on="/CitiServe/frontend/admin_dashboard/images/with_notif.png"
      data-img-off="/CitiServe/frontend/admin_dashboard/images/no_notif.png"
      data-img-active="/CitiServe/frontend/admin_dashboard/images/select_notif.png"
      data-read-url="/CitiServe/public/admin/notifications_read.php"
      data-csrf-token="<?= e(csrf_token()) ?>"
      title="Notifications">
      <img id="notifIcon" src="<?= $hasNotif ? '/CitiServe/frontend/admin_dashboard/images/with_notif.png' : '/CitiServe/frontend/admin_dashboard/images/no_notif.png' ?>" alt="Notifications">
      <span class="notif-count-badge" id="notifCount" <?= $unreadCount > 0 ? '' : 'style="display:none;"' ?>><?= $unreadCount > 99 ? '99+' : (int)$unreadCount ?></span>
    </button>
    <div class="notif-panel" id="notifPanel" aria-label="Notifications">
      <div class="notif-panel-header"><span class="notif-panel-title">Notifications</span><button class="notif-panel-more" title="More options">···</button></div>
      <div class="notif-tabs"><button class="notif-tab active" data-filter="all">All</button><button class="notif-tab" data-filter="document">Document</button><button class="notif-tab" data-filter="complaint">Complaint</button></div>
      <div class="notif-list" id="notifList">
        <?php foreach (['new' => 'New', 'today' => 'Today', 'earlier' => 'Earlier'] as $key => $label): ?>
          <?php if (!empty($notifSections[$key])): ?>
            <div class="notif-section-label" data-section="<?= e($key) ?>"><?= e($label) ?></div>
            <?php foreach ($notifSections[$key] as $n): ?>
              <div class="notif-item <?= $n['read'] ? '' : 'unread' ?>" data-id="<?= (int)$n['id'] ?>" data-category="<?= e($n['category']) ?>" data-link="<?= e($n['link']) ?>">
                <div class="notif-icon-wrap"><img class="notif-icon-main" src="<?= e($n['main_icon']) ?>" alt=""><?php if (!empty($n['badge_icon'])): ?><img class="notif-icon-badge" src="<?= e($n['badge_icon']) ?>" alt=""><?php endif; ?></div>
                <div class="notif-text"><div class="notif-msg"><?= e($n['message']) ?></div><div class="notif-time"><?= e($n['time_label']) ?></div></div>
                <div class="notif-dot"></div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        <?php endforeach; ?>
        <?php if (empty($notifications)): ?><div class="notif-empty">No notifications yet.</div><?php endif; ?>
      </div>
      <div class="notif-footer"><button class="notif-see-prev" id="notifSeePrev"><p>Go to requests and complaints</p></button></div>
    </div>
    <div class="profile-pill" id="profilePill">
      <div class="profile-avatar"><img src="<?= e($profileImagePath ?? '/CitiServe/frontend/admin_dashboard/images/admin_dummy_icon.png') ?>" alt="Admin Profile"></div>
      <span class="profile-name"><?= e(trim(explode(' ', (string)$user->full_name)[0])) ?></span>
      <span class="profile-chevron"><img src="/CitiServe/frontend/admin_dashboard/images/profile_dropdown.png" alt=""></span>
    </div>
    <div class="profile-panel" id="profilePanel">
      <div class="profile-panel-top"><div class="profile-panel-fullname"><?= e((string)$user->full_name) ?></div><div class="profile-panel-subtext"><?= e(ucfirst((string)$admin['role'])) ?> • Brgy. Kalayaan</div></div>
      <a href="/CitiServe/public/admin/profile.php" class="profile-panel-item"><img src="/CitiServe/frontend/admin_dashboard/images/my_profile.png" alt="My Profile" class="profile-panel-icon1"><span>My Profile</span></a>
      <a href="/CitiServe/public/logout.php" class="profile-panel-item logout"><img src="/CitiServe/frontend/admin_dashboard/images/logout.png" alt="Logout" class="profile-panel-icon2"><span>Logout</span></a>
    </div>
  </div>
</nav>
<div class="content-area">
    <img src="<?= $assetBase ?>/images/profile-bg-top.png" style="position:absolute; top:20px; right:37%; width:275px; pointer-events:none; z-index:0;" alt="" onerror="this.style.display='none'">
    <img src="<?= $assetBase ?>/images/profile-bg-mid.png" style="position:absolute; top:0; right:30px; width:275px; pointer-events:none; z-index:0;" alt="" onerror="this.style.display='none'">
    <div class="form-breadcrumb">
        <a href="/CitiServe/public/admin/dashboard.php">Dashboard</a>
        <span class="form-sep">></span>
        <span class="form-active">Staff Profile</span>
    </div>

    <h1 class="form-title">Staff Profile</h1>
    <p class="form-subtitle">Manage your staff account information and password.</p>

    <?php if ($success !== ''): ?>
        <p style="color:#15803d;font-weight:600;margin-bottom:12px;"><?= e($success) ?></p>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div style="color:#be123c;background:#fff1f2;border:1px solid #fecdd3;padding:10px 14px;border-radius:8px;margin-bottom:12px;">
            <ul style="margin-left:18px;">
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="profile-wrapper">
        <aside class="profile-side">
            <div class="avatar-card">
                <div class="avatar-section">
                    <div class="avatar-wrap">
                        <img class="avatar-img" src="<?= e($profileImagePath ?? ($assetBase . '/images/profile-staff-profile.png')) ?>" alt="Profile">
                        <form method="post" enctype="multipart/form-data" id="avatarForm" style="display:none;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="upload_avatar">
                            <input type="file" id="avatarInput" name="avatar" accept=".jpg,.jpeg,.png,image/jpeg,image/png" onchange="document.getElementById('avatarForm').submit()">
                        </form>
                        <button class="avatar-edit-btn" type="button" onclick="document.getElementById('avatarInput').click()">
                            <img src="<?= $assetBase ?>/images/profile-edit.png" alt="Edit">
                        </button>
                    </div>
                    <div class="user-name"><?= e($user->full_name) ?></div>
                    <div class="user-role"><?= e(ucfirst((string)$user->role)) ?> - CitiServe</div>
                </div>
                <div class="avatar-divider"></div>
                <div class="meta-table">
                    <div class="meta-row"><span class="meta-label">Staff ID</span><span class="meta-val"><?= (int)$user->id ?></span></div>
                    <div class="meta-row"><span class="meta-label">Joined</span><span class="meta-val"><?= e((string)($user->created_at ?? '-')) ?></span></div>
                    <div class="meta-row"><span class="meta-label">Status</span><span class="badge-active">Active</span></div>
                </div>
            </div>

            <div class="sidenav-card">
                <a href="#profile" class="sidenav-item<?= $activeTab === 'profile' ? ' active' : '' ?>">Profile Information</a>
                <a href="#password" class="sidenav-item<?= $activeTab === 'password' ? ' active' : '' ?>">Change Password</a>
            </div>
        </aside>

        <div class="profile-main">
            <div id="section-profile" class="profile-section<?= $activeTab === 'profile' ? ' active' : '' ?>">
                <div class="form-card">
                    <div class="form-card-bar">
                        Profile Information
                        <small style="font-weight:400;font-size:11px;">Update your staff account details.</small>
                    </div>
                    <form method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="save_profile">
                        <div class="form-card-body">
                            <div class="form-row-4">
                                <div class="form-group">
                                    <label>First Name <span class="req">*</span></label>
                                    <input type="text" name="first_name" maxlength="50" value="<?= e($firstName) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Middle Name <span class="opt">(Optional)</span></label>
                                    <input type="text" name="middle_name" maxlength="50" value="<?= e($middleName) ?>">
                                </div>
                                <div class="form-group">
                                    <label>Last Name <span class="req">*</span></label>
                                    <input type="text" name="last_name" maxlength="50" value="<?= e($lastName) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Suffix <span class="opt">(Optional)</span></label>
                                    <select name="suffix">
                                        <option value="">Select suffix</option>
                                        <?php foreach ($suffixes as $s): ?>
                                            <option value="<?= e($s) ?>" <?= $suffix === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row-2">
                                <div class="form-group">
                                    <label>Contact Number</label>
                                    <input type="text" name="contact_number" maxlength="20" value="<?= e($contactNumber) ?>">
                                </div>
                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input type="email" value="<?= e((string)$user->email) ?>" disabled style="background:#f9fafb;color:#4b5563;">
                                </div>
                            </div>
                            <div class="form-row-4">
                                <div class="form-group">
                                    <label>Date of Birth</label>
                                    <input type="date" name="dob" value="<?= e($dob) ?>">
                                </div>
                                <div class="form-group">
                                    <label>Civil Status</label>
                                    <select name="civil_status">
                                        <option value="">Select status</option>
                                        <?php foreach ($civilOptions as $o): ?>
                                            <option value="<?= e($o) ?>" <?= $civilStatus === $o ? 'selected' : '' ?>><?= e($o) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Citizenship</label>
                                    <input type="text" name="citizenship" maxlength="50" value="<?= e($citizenship) ?>">
                                </div>
                                <div class="form-group">
                                    <label>Gender</label>
                                    <select name="gender">
                                        <option value="">Select gender</option>
                                        <?php foreach ($genderOptions as $o): ?>
                                            <option value="<?= e($o) ?>" <?= $gender === $o ? 'selected' : '' ?>><?= e($o) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Complete Address</label>
                                <input type="text" name="address" maxlength="1000" value="<?= e($address) ?>">
                            </div>
                            <div class="form-group">
                                <label>Role</label>
                                <input type="text" value="<?= e(ucfirst((string)$user->role)) ?>" disabled style="background:#fff;color:#111;cursor:default;">
                            </div>
                        </div>
                        <div class="form-btn-row">
                            <button class="btn-save" type="submit">
                                <img src="<?= $assetBase ?>/images/profile-save-changes.png" alt="Save Changes">
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="section-password" class="profile-section<?= $activeTab === 'password' ? ' active' : '' ?>">
                <div class="form-card">
                    <div class="form-card-bar">
                        Change Password
                        <small style="font-weight:400;font-size:11px;">Use a strong password with at least 8 characters.</small>
                    </div>
                    <form method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="change_password">
                        <div class="form-card-body">
                            <div class="form-group">
                                <label>Current Password <span class="req">*</span></label>
                                <div class="pw-wrap">
                                    <input type="password" id="currentPw" name="current_password" value="<?= e($currentPassword) ?>" required>
                                    <button class="pw-eye" type="button" onclick="togglePw('currentPw', this)" tabindex="-1">
                                        <img src="<?= $assetBase ?>/images/eye.png" alt="Show">
                                    </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>New Password <span class="req">*</span></label>
                                <div class="pw-wrap">
                                    <input type="password" id="newPw" name="new_password" value="<?= e($newPassword) ?>" required>
                                    <button class="pw-eye" type="button" onclick="togglePw('newPw', this)" tabindex="-1">
                                        <img src="<?= $assetBase ?>/images/eye.png" alt="Show">
                                    </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Confirm New Password <span class="req">*</span></label>
                                <div class="pw-wrap">
                                    <input type="password" id="confirmPw" name="confirm_password" value="<?= e($confirmPassword) ?>" required>
                                    <button class="pw-eye" type="button" onclick="togglePw('confirmPw', this)" tabindex="-1">
                                        <img src="<?= $assetBase ?>/images/eye.png" alt="Show">
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-btn-row">
                            <button class="btn-save" type="submit">
                                <img src="<?= $assetBase ?>/images/profile-change-pass-btn.png" alt="Change Password">
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const sectionMap = { '#profile': 'section-profile', '#password': 'section-password' };
function showSection(hash) {
    document.querySelectorAll('.profile-section').forEach((s) => s.classList.remove('active'));
    document.querySelectorAll('.sidenav-item').forEach((l) => l.classList.remove('active'));
    const id = sectionMap[hash];
    const section = id ? document.getElementById(id) : null;
    if (section) {
        section.classList.add('active');
    }
    const link = document.querySelector('.sidenav-item[href="' + hash + '"]');
    if (link) {
        link.classList.add('active');
    }
}
document.querySelectorAll('.sidenav-item').forEach((link) => {
    link.addEventListener('click', function (event) {
        event.preventDefault();
        const hash = this.getAttribute('href');
        showSection(hash);
        if (window.history && window.history.replaceState) {
            window.history.replaceState(null, '', hash);
        }
    });
});
function togglePw(inputId, button) {
    const input = document.getElementById(inputId);
    const img = button.querySelector('img');
    if (!input || !img) {
        return;
    }
    if (input.type === 'password') {
        input.type = 'text';
        img.src = '<?= $assetBase ?>/images/eyeclosed.png';
    } else {
        input.type = 'password';
        img.src = '<?= $assetBase ?>/images/eye.png';
    }
}
</script>
<script src="/CitiServe/frontend/admin_dashboard/JS/admin_dashboard.js"></script>
</body>
</html>
