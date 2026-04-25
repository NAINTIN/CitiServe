<?php
// Don't show errors on the page (for security), but still log them
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Include all the files we need
require_once __DIR__ . '/../../app/helpers/auth.php';
require_once __DIR__ . '/../../app/helpers/csrf.php';
require_once __DIR__ . '/../../app/core/CitiServeData.php';
require_once __DIR__ . '/../../app/helpers/admin_notifications.php';

// Make sure the user is an admin
$admin = require_admin();

$data = new CitiServeData();
$db = $data->getPdo();

// Helper function to format staff ID
function formatStaffId($role, $id) {
    $prefix = ($role === 'staff' || $role === 'admin') ? 'STF' : 'RES';
    return $prefix . '-' . str_pad((int)$id, 7, '0', STR_PAD_LEFT);
}

function h($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function getInitials($name) {
    $parts = explode(' ', trim($name));
    $first = strtoupper(substr($parts[0] ?? '', 0, 1));
    $last = strtoupper(substr(end($parts) ?: '', 0, 1));
    return $first . $last;
}

// Variables for success and error messages
$message = '';
$error = '';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify_or_die();

    $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $action = isset($_POST['action']) ? trim($_POST['action']) : '';

    if ($userId <= 0) {
        $error = 'Invalid request.';
    } else {
        try {
            $db->beginTransaction();

            $stmt = $db->prepare("SELECT id, role, full_name, email, is_verified FROM users WHERE id = ? LIMIT 1");
            $stmt->execute([$userId]);
            $target = $stmt->fetch();

            if (!$target) {
                throw new Exception('User not found.');
            }

            if ($action === 'toggle_status') {
                // Toggle is_verified (1 = Active, 0 = Inactive)
                $newStatus = ((int)$target['is_verified'] === 1) ? 0 : 1;
                $db->prepare("UPDATE users SET is_verified = ?, updated_at = NOW() WHERE id = ?")->execute([$newStatus, $userId]);
                $message = "Updated status for {$target['full_name']}.";
            } elseif ($action === 'remove') {
                // Soft-remove: set role to 'resident' (don't hard delete)
                if ($userId === (int)$admin['id']) {
                    $error = 'You cannot remove your own account.';
                } else {
                    $db->prepare("UPDATE users SET role = 'resident', updated_at = NOW() WHERE id = ?")->execute([$userId]);
                    $message = "Removed {$target['full_name']} from staff.";
                }
            } elseif ($action === 'add') {
                // Add new staff member
                $firstName = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
                $lastName = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
                $email = isset($_POST['email']) ? trim($_POST['email']) : '';
                $contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
                $staffRole = isset($_POST['staff_role']) ? trim($_POST['staff_role']) : 'Administrative Staff';

                if ($firstName === '' || $lastName === '' || $email === '') {
                    $error = 'First name, last name, and email are required.';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Invalid email address.';
                } else {
                    // Check if email already exists
                    $check = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
                    $check->execute([$email]);
                    if ($check->fetch()) {
                        $error = 'Email already registered.';
                    } else {
                        // Generate a random password
                        $password = bin2hex(random_bytes(8));
                        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                        $fullName = $firstName . ' ' . $lastName;
                        $stmt = $db->prepare("
                            INSERT INTO users (full_name, email, password_hash, role, is_verified, created_at, updated_at)
                            VALUES (?, ?, ?, 'staff', 1, NOW(), NOW())
                        ");
                        $stmt->execute([$fullName, $email, $passwordHash]);
                        $newId = (int)$db->lastInsertId();

                        // Create notification for the new staff member
                        $data->createNotification(
                            $newId,
                            'Staff Account Created',
                            "Your staff account has been created. Your temporary password is: $password. Please change it after logging in.",
                            '/CitiServe/public/admin/profile.php'
                        );

                        $message = "Added new staff: $fullName. Temporary password: $password";
                    }
                }
            }

            $db->commit();
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $error = 'Operation failed: ' . $e->getMessage();
        }
    }
}

// Build notification data
$adminNotif = build_admin_notifications($data, (int)$admin['id']);
$notifSections = $adminNotif['sections'];
$hasNotif = $adminNotif['has_notif'];
$notifications = $adminNotif['notifications'];
$unreadCount = (int)$adminNotif['unread_count'];

// Fetch staff members (role = 'admin' or 'staff')
$staffMembers = $db->query("
    SELECT id, full_name, email, role, created_at, is_verified, contact_number
    FROM users
    WHERE role IN ('admin', 'staff')
    ORDER BY created_at DESC
")->fetchAll();

// Compute summary counts
$totalStaff = 0;
$activeCount = 0;
$inactiveCount = 0;
foreach ($staffMembers as $s) {
    $totalStaff++;
    if ((int)$s['is_verified'] === 1) {
        $activeCount++;
    } else {
        $inactiveCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management - CitiServe</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/CitiServe/frontend/admin_dashboard/CSS/admin_dashboard.css">
    <link rel="stylesheet" href="/CitiServe/frontend/admin_dashboard/CSS/admin_staff.css">
</head>
<body>

<div class="design-strip left"><img src="/CitiServe/frontend/admin_dashboard/images/dashboard_design.png" alt=""></div>
<div class="design-strip right"><img src="/CitiServe/frontend/admin_dashboard/images/dashboard_design.png" alt=""></div>

<nav class="navbar">
  <a href="/CitiServe/public/admin/dashboard.php" class="navbar-logo">
    <img src="/CitiServe/frontend/admin_dashboard/images/logo_pink.png" alt="CitiServe">
  </a>
  <div class="navbar-nav admin-nav">
    <a href="/CitiServe/public/admin/dashboard.php" class="nav-item">
      <span class="nav-text">Dashboard</span>
    </a>
    <a href="/CitiServe/public/admin/requests.php" class="nav-item">
      <span class="nav-text">Document Requests</span>
    </a>
    <a href="/CitiServe/public/admin/complaints.php" class="nav-item">
      <span class="nav-text">Complaints</span>
    </a>
    <div class="nav-item has-dropdown active">
      <span class="nav-text">User Management</span>
      <span class="nav-chevron">⏷</span>
      <div class="nav-dropdown">
        <a href="/CitiServe/public/admin/users.php" class="nav-dropdown-item">Residents</a>
        <a href="/CitiServe/public/admin/staff.php" class="nav-dropdown-item">Staff</a>
      </div>
    </div>
    <a href="/CitiServe/public/admin/user_verification.php" class="nav-item">
      <span class="nav-text">Account Verification</span>
    </a>
  </div>
  <div class="navbar-right admin-navbar-right">
    <button class="notif-btn" id="notifBtn"
      data-has-notif="<?= $hasNotif ? '1' : '0' ?>"
      data-img-on="/CitiServe/frontend/admin_dashboard/images/with_notif.png"
      data-img-off="/CitiServe/frontend/admin_dashboard/images/no_notif.png"
      data-img-active="/CitiServe/frontend/admin_dashboard/images/select_notif.png"
      data-read-url="/CitiServe/public/admin/notifications_read.php"
      data-csrf-token="<?= h(csrf_token()) ?>"
      title="Notifications">
      <img id="notifIcon"
        src="<?= $hasNotif ? '/CitiServe/frontend/admin_dashboard/images/with_notif.png' : '/CitiServe/frontend/admin_dashboard/images/no_notif.png' ?>"
        alt="Notifications">
      <span class="notif-count-badge" id="notifCount" <?= $unreadCount > 0 ? '' : 'style="display:none;"' ?>>
        <?= $unreadCount > 99 ? '99+' : (int)$unreadCount ?>
      </span>
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
            <div class="notif-section-label" data-section="<?= h($key) ?>"><?= h($label) ?></div>
            <?php foreach ($notifSections[$key] as $n): ?>
              <div class="notif-item <?= $n['read'] ? '' : 'unread' ?>"
                data-id="<?= (int)$n['id'] ?>"
                data-category="<?= h($n['category']) ?>"
                data-link="<?= h($n['link']) ?>">
                <div class="notif-icon-wrap">
                  <img class="notif-icon-main" src="<?= h($n['main_icon']) ?>" alt="">
                  <?php if (!empty($n['badge_icon'])): ?>
                    <img class="notif-icon-badge" src="<?= h($n['badge_icon']) ?>" alt="">
                  <?php endif; ?>
                </div>
                <div class="notif-text">
                  <div class="notif-msg"><?= h($n['message']) ?></div>
                  <div class="notif-time"><?= h($n['time_label']) ?></div>
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
        <button class="notif-see-prev" id="notifSeePrev"><p>Go to requests and complaints</p></button>
      </div>
    </div>
    <div class="profile-pill" id="profilePill">
      <div class="profile-avatar">
        <img src="<?= !empty($admin['avatar']) ? h($admin['avatar']) : '/CitiServe/frontend/admin_dashboard/images/admin_dummy_icon.png' ?>" alt="Admin">
      </div>
      <span class="profile-name"><?= h(explode(' ', (string)$admin['full_name'])[0]) ?></span>
      <span class="profile-chevron"><img src="/CitiServe/frontend/admin_dashboard/images/profile_dropdown.png" alt=""></span>
    </div>
    <div class="profile-panel" id="profilePanel">
      <div class="profile-panel-top">
        <div class="profile-panel-fullname"><?= h($admin['full_name']) ?></div>
        <div class="profile-panel-subtext"><?= h(ucfirst((string)$admin['role'])) ?> • Brgy. Kalayaan</div>
      </div>
      <a href="/CitiServe/public/admin/profile.php" class="profile-panel-item">
        <img src="/CitiServe/frontend/admin_dashboard/images/my_profile.png" class="profile-panel-icon1" alt="">
        <span>My Profile</span>
      </a>
      <a href="/CitiServe/public/logout.php" class="profile-panel-item logout">
        <img src="/CitiServe/frontend/admin_dashboard/images/logout.png" class="profile-panel-icon2" alt="">
        <span>Logout</span>
      </a>
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

      <img src="/CitiServe/frontend/admin_dashboard/images/success_creation_notif.png" alt="Success" class="staff-success-notif" id="staffSuccessNotif">

      <button type="button" class="add-staff-btn" id="addStaffBtn">
        <img src="/CitiServe/frontend/admin_dashboard/images/add_new_staff.png" alt="Add New Staff">
      </button>
    </div>

    <?php if ($message): ?>
      <p style="color:#15803d;margin-top:10px;"><?= h($message) ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
      <p style="color:#b91c1c;margin-top:10px;"><?= h($error) ?></p>
    <?php endif; ?>

    <div class="admin-summary-row">
      <div class="summary-box summary-total">
        <div class="summary-number"><?= $totalStaff ?></div>
        <div class="summary-label">Total Staff</div>
        <img src="/CitiServe/frontend/admin_dashboard/images/stafftotal_faded_icon.png" class="summary-icon1">
      </div>

      <div class="summary-box summary-active">
        <div class="summary-number"><?= $activeCount ?></div>
        <div class="summary-label">Active</div>
        <img src="/CitiServe/frontend/admin_dashboard/images/admin_active_resident.png" class="summary-icon2">
      </div>

      <div class="summary-box summary-inactive">
        <div class="summary-number"><?= $inactiveCount ?></div>
        <div class="summary-label">Inactive</div>
        <img src="/CitiServe/frontend/admin_dashboard/images/admin_inactive.png" class="summary-icon3">
      </div>
    </div>

    <div class="toolbar">
      <div class="toolbar-left">
        <div class="search-wrap">
          <img src="/CitiServe/frontend/admin_dashboard/images/search_icon.png" alt="" class="search-icon">
          <input type="text" id="searchInput" placeholder="Search by name, email, or staff ID...">
        </div>
        <button type="button" class="clear-btn" id="clearBtn"><img src="/CitiServe/frontend/admin_dashboard/images/my_request_clear.png" alt="Clear"></button>
      </div>
      <div class="toolbar-right">
        <div class="custom-filter" id="filterBox">
          <button type="button" class="filter-box" id="filterBtn"><span id="selectedFilterText">All</span><span class="filter-arrow">▾</span></button>
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

      <?php if (empty($staffMembers)): ?>
        <div class="requests-empty">
          <img src="/CitiServe/frontend/admin_dashboard/images/recent_resident_main.png" alt="" class="empty-icon">
          <div class="empty-text">No staff members found</div>
        </div>
      <?php else: ?>
        <div class="requests-table-body" id="requestsTableBody">
          <?php foreach ($staffMembers as $s): ?>
            <?php
              $createdTs = !empty($s['created_at']) ? strtotime((string)$s['created_at']) : false;
              $joined = $createdTs ? date('M j, Y', $createdTs) : '-';
              $status = ((int)$s['is_verified'] === 1) ? 'Active' : 'Inactive';
              $staffId = formatStaffId($s['role'], (int)$s['id']);
            ?>
            <div class="request-row"
              data-staff-id="<?= h($staffId) ?>"
              data-name="<?= h($s['full_name']) ?>"
              data-email="<?= h($s['email']) ?>"
              data-contact="<?= h($s['contact_number'] ?? '-') ?>"
              data-role="<?= h($s['role']) ?>"
              data-joined="<?= h($joined) ?>"
              data-status="<?= h($status) ?>"
              data-initials="<?= h(getInitials($s['full_name'])) ?>">

              <div class="request-cell request-id"><?= h($staffId) ?></div>

              <div class="request-cell resident-name-cell">
                <div class="resident-initials"><?= h(getInitials($s['full_name'])) ?></div>
                <div>
                  <div class="resident-fullname"><?= h($s['full_name']) ?></div>
                  <div class="resident-email"><?= h($s['email']) ?></div>
                </div>
              </div>

              <div class="request-cell"><?= h($s['contact_number'] ?? '-') ?></div>

              <div class="request-cell document-name"><?= h($s['role']) ?></div>

              <div class="request-cell request-date"><?= h($joined) ?></div>

              <div class="request-cell">
                <img
                  src="/CitiServe/frontend/admin_dashboard/images/<?= ((int)$s['is_verified'] === 1) ? 'resident_active_staff.png' : 'resident_inactive_staff.png' ?>"
                  alt="<?= h($status) ?>"
                  class="resident-status-img">
              </div>

              <div class="request-cell staff-action-cell">
                <button type="button" class="remove-staff-btn" data-action="remove">Remove</button>
                <span class="staff-action-separator">|</span>
                <button type="button" class="manage-btn view-profile-btn">View Profile</button>
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

<!-- Staff Profile Modal -->
<div class="resident-profile-modal" id="staffProfileModal">
  <div class="resident-profile-box staff-profile-box">
    <button type="button" class="resident-profile-close-x" id="staffProfileCloseX">×</button>

    <div class="resident-profile-title">Staff Profile</div>
    <div class="resident-profile-divider"></div>

    <div class="resident-profile-header">
      <div class="resident-profile-avatar" id="profileInitials">?</div>
      <div class="resident-profile-head-text">
        <div class="resident-profile-name" id="profileName">Name</div>
        <div class="resident-profile-id">
          <span id="profileId">STF-0000001</span> · <span id="profileRole">Role</span>
        </div>
        <img src="/CitiServe/frontend/admin_dashboard/images/resident_active_staff.png" class="staff-profile-status-img" id="profileStatusImg">
      </div>
    </div>

    <div class="resident-profile-divider"></div>

    <div class="resident-profile-grid">
      <div>
        <label>Email</label>
        <p id="profileEmail">-</p>
      </div>
      <div>
        <label>Contact Number</label>
        <p id="profileContact">-</p>
      </div>
      <div>
        <label>Date Joined</label>
        <p id="profileJoined">-</p>
      </div>
      <div>
        <label>Status</label>
        <p id="profileStatus">-</p>
      </div>
    </div>

    <div class="resident-profile-actions staff-profile-actions">
      <button type="button" class="resident-profile-img-btn" id="toggleStaffStatusBtn">
        <img src="/CitiServe/frontend/admin_dashboard/images/resident_mark_inactive.png" alt="Mark as Inactive" id="toggleStaffStatusImg">
      </button>
      <button type="button" class="resident-profile-img-btn" id="staffProfileCloseBtn">
        <img src="/CitiServe/frontend/admin_dashboard/images/resident_close_profile.png" alt="Close">
      </button>
    </div>
  </div>
</div>

<!-- Add New Staff Modal -->
<div class="add-staff-modal" id="addStaffModal">
  <div class="add-staff-box">
    <button type="button" class="add-staff-close" id="addStaffCloseX">×</button>

    <div class="add-staff-title">Add New Staff Account</div>
    <div class="resident-profile-divider"></div>

    <form method="post" id="addStaffForm">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="add">

      <div class="add-staff-row">
        <div class="add-staff-field">
          <label>First Name <span>*</span></label>
          <input type="text" name="first_name" placeholder="e.g. Juan" required>
        </div>
        <div class="add-staff-field">
          <label>Last Name <span>*</span></label>
          <input type="text" name="last_name" placeholder="e.g. Dela Cruz" required>
        </div>
      </div>

      <div class="add-staff-field">
        <label>Email Address <span>*</span></label>
        <input type="email" name="email" placeholder="e.g. juan@email.com" required>
      </div>

      <div class="add-staff-field">
        <label>Contact Number <span>*</span></label>
        <input type="text" name="contact" placeholder="09XX-XXX-XXXX">
      </div>

      <div class="add-staff-field">
        <label>Role</label>
        <div class="add-staff-select" id="addStaffRoleBox">
          <button type="button" class="add-staff-select-btn" type="button">
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
          <input type="hidden" name="staff_role" id="addStaffRoleInput" value="Barangay Captain">
        </div>
      </div>

      <div class="add-staff-actions">
        <button type="button" class="add-staff-img-btn" id="addStaffCancel">
          <img src="/CitiServe/frontend/admin_dashboard/images/add_new_staff_cancel.png" alt="Cancel">
        </button>
        <button type="submit" class="add-staff-img-btn add-staff-submit" id="addStaffSubmit">
          <img src="/CitiServe/frontend/admin_dashboard/images/add_new_staff_proceed.png" alt="Add New Staff">
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Remove Staff Modal -->
<div class="remove-staff-modal" id="removeStaffModal">
  <div class="remove-staff-box">
    <div class="remove-content">
      <div class="remove-icon-box">
        <img src="/CitiServe/frontend/admin_dashboard/images/remove_staff_icon.png" alt="">
      </div>
      <div class="remove-texts">
        <div class="remove-title">Remove Staff Account</div>
        <div class="remove-subtitle">
          This will remove the staff account from the system.
        </div>
      </div>
    </div>

    <div class="remove-actions">
      <form method="post" id="removeStaffForm">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="remove">
        <input type="hidden" name="user_id" id="removeStaffId" value="">
        <button type="submit" class="remove-img-btn" id="confirmRemoveStaff">
          <img src="/CitiServe/frontend/admin_dashboard/images/remove_staff_button_proceeds.png" alt="Remove">
        </button>
      </form>
      <button class="remove-img-btn" id="cancelRemoveStaff">
        <img src="/CitiServe/frontend/admin_dashboard/images/remove_staff_button_cancel.png" alt="Cancel">
      </button>
    </div>
  </div>
</div>

<script src="/CitiServe/frontend/admin_dashboard/JS/admin_dashboard.js"></script>
<script src="/CitiServe/public/admin/JS/admin_staff.js"></script>
</body>
</html>
