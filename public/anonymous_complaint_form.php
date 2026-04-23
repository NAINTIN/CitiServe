<?php
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/core/CitiServeData.php';

function anonymous_form_normalize_category($name)
{
  $v = strtolower((string)$name);
  $v = str_replace(['&', '-', '_'], ' ', $v);
  $v = str_replace(['/', '(', ')'], ' ', $v);
  $v = preg_replace('/\s+/', ' ', $v);
  return trim((string)$v);
}

$userInfo = require_resident();
$data = new CitiServeData();
$categories = $data->getActiveComplaintCategories();
$categoryById = [];
foreach ($categories as $cat) {
  $categoryById[(int)$cat['id']] = $cat;
}

$currentCategoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
if ($currentCategoryId <= 0 || !isset($categoryById[$currentCategoryId])) {
  foreach ($categories as $cat) {
    if (anonymous_form_normalize_category((string)$cat['name']) === 'other') {
      $currentCategoryId = (int)$cat['id'];
      break;
    }
  }
  if ($currentCategoryId <= 0 && !empty($categories)) {
    $currentCategoryId = (int)$categories[0]['id'];
  }
}

$categoryKey = 'anonymous_report';
$currentCategory = [
  'title' => 'Anonymous Report',
  'value' => 'Anonymous Report',
  'icon' => '/CitiServe/frontend/complaints/images/pink_anonymous.png',
];

$user = [
  'first_name' => trim(explode(' ', (string)$userInfo['full_name'])[0]),
  'full_name' => (string)$userInfo['full_name'],
  'avatar' => '',
];

$notificationsRaw = $data->getNotificationsByUser((int)$userInfo['id']);
$notifications = array_slice($notificationsRaw, 0, 10);
$notifSections = ['new' => [], 'today' => [], 'earlier' => []];
$nowTs = time();
foreach ($notifications as $n) {
  $combined = strtolower((string)$n['title'] . ' ' . (string)$n['message']);
  $cat = 'announcement';
  if (strpos($combined, 'complaint') !== false) {
    $cat = 'complaint';
  } elseif (strpos($combined, 'document') !== false || strpos($combined, 'request') !== false) {
    $cat = 'document';
  }

  $createdTs = !empty($n['created_at']) ? strtotime((string)$n['created_at']) : $nowTs;
  if ($createdTs === false) {
    $createdTs = $nowTs;
  }
  $age = max(0, $nowTs - $createdTs);
  $timeLabel = $age < 3600 ? max(1, (int)floor($age / 60)) . 'm' : ($age < 86400 ? (int)floor($age / 3600) . 'h' : (int)floor($age / 86400) . 'd');
  $section = $age < 3600 ? 'new' : ($age < 86400 ? 'today' : 'earlier');

  $notifSections[$section][] = [
    'id' => (int)$n['id'],
    'category' => $cat,
    'message' => (string)$n['message'],
    'time_label' => $timeLabel,
    'read' => ((int)$n['is_read'] === 1),
    'link' => '/CitiServe/public/notifications.php?open=' . (int)$n['id'],
    'main_icon' => '/CitiServe/frontend/complaints/images/citiserve_notif.png',
    'badge_icon' => $cat === 'complaint' ? '/CitiServe/frontend/complaints/images/complaint_notif.png' : '/CitiServe/frontend/complaints/images/document_notif.png',
  ];
}

$unreadCount = 0;
foreach ($notifications as $n) {
  if ((int)$n['is_read'] === 0) {
    $unreadCount++;
  }
}
$hasNotif = $unreadCount > 0;

$sessionErrors = [];
if (isset($_SESSION['complaint_form_errors']) && is_array($_SESSION['complaint_form_errors'])) {
  $sessionErrors = $_SESSION['complaint_form_errors'];
}
unset($_SESSION['complaint_form_errors']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CitiServe - Anonymous Complaint Form</title>
  <base href="/CitiServe/frontend/complaints/">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/CitiServe/frontend/complaints/css/anonymous_complaint_form.css">
</head>
<body>

<div class="design-strip left" aria-hidden="true"><img src="/CitiServe/frontend/complaints/images/complaint_form_design.png" alt=""></div>
<div class="design-strip right" aria-hidden="true"><img src="/CitiServe/frontend/complaints/images/complaint_form_design.png" alt=""></div>

<nav class="navbar">
  <a href="/CitiServe/public/dashboard.php" class="navbar-logo">
    <img src="/CitiServe/frontend/complaints/images/logo_pink.png" alt="CitiServe">
  </a>

  <div class="navbar-nav">
    <a href="/CitiServe/public/dashboard.php" class="nav-item">
      <span class="nav-text">Dashboard</span>
    </a>

    <div class="nav-item has-dropdown" id="navDocReq">
      <span class="nav-text">Document Requests</span>
      <span class="nav-chevron">⏷</span>
      <div class="nav-dropdown">
        <a href="/CitiServe/public/request_select.php" class="nav-dropdown-item">Request Document</a>
        <a href="/CitiServe/public/my_requests.php" class="nav-dropdown-item">My Requests</a>
      </div>
    </div>

    <div class="nav-item has-dropdown active" id="navComplaint">
      <span class="nav-text">Complaint Management</span>
      <span class="nav-chevron">⏷</span>
      <div class="nav-dropdown">
        <a href="/CitiServe/public/complaint_create.php" class="nav-dropdown-item">Submit Complaint</a>
        <a href="/CitiServe/public/my_complaints.php" class="nav-dropdown-item">My Complaints</a>
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
      <span class="profile-chevron"><img src="/CitiServe/frontend/complaints/images/profile_dropdown.png" alt=""></span>
    </div>

    <div class="profile-panel" id="profilePanel">
      <div class="profile-panel-top">
        <div class="profile-panel-fullname"><?= htmlspecialchars($user['full_name']) ?></div>
        <div class="profile-panel-subtext">Resident • Brgy. Kalayaan</div>
      </div>

      <a href="/CitiServe/public/profile.php" class="profile-panel-item">
        <img src="/CitiServe/frontend/complaints/images/my_profile.png" alt="My Profile" class="profile-panel-icon1">
        <span>My Profile</span>
      </a>

      <a href="/CitiServe/public/logout.php" class="profile-panel-item logout">
        <img src="/CitiServe/frontend/complaints/images/logout.png" alt="Logout" class="profile-panel-icon2">
        <span>Logout</span>
      </a>
    </div>
  </div>
</nav>

<div class="page-body">
  <div class="form-page-card">

    <?php if (!empty($sessionErrors)): ?>
      <div style="margin: 16px 24px; color: #b42318; font-weight: 600;">
        <?php foreach ($sessionErrors as $err): ?>
          <div><?= htmlspecialchars((string)$err) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="breadcrumb">
      <a href="/CitiServe/public/complaint_create.php" class="leave-form-link">Complaint Management</a>
      <span>></span>
      <a href="/CitiServe/public/complaint_create.php" class="leave-form-link">Submit Complaint</a>
      <span>></span>
      <span class="current">Form</span>
    </div>

    <div class="form-head">
      <h1 class="form-page-title"><?= htmlspecialchars($currentCategory['title']) ?> – Complaint Form</h1>
      <p class="form-page-subtitle">Fill in all required fields accurately. Be as specific as possible.</p>
    </div>

    <div class="progress-wrap">
    <div class="progress-step active">
        <div class="progress-icon-wrap active">
        <img src="/CitiServe/frontend/complaints/images/complaint_fillout.png" alt="Fill Out Form" class="progress-step-icon">
        </div>
        <div class="progress-text">
        <span class="progress-title">Fill Out Form</span>
        <span class="progress-sub">Enter your details</span>
        </div>
    </div>

    <div class="progress-line-img">
        <img src="/CitiServe/frontend/complaints/images/complaint_progress_bar.png" alt="">
    </div>

    <div class="progress-step">
        <div class="progress-icon-wrap">
        <img src="/CitiServe/frontend/complaints/images/complaint_confirmation.png" alt="Confirmation" class="progress-step-icon">
        </div>
        <div class="progress-text inactive-text">
        <span class="progress-title">Confirmation</span>
        <span class="progress-sub">Review and confirm</span>
        </div>
    </div>
    </div>

    <div class="form-layout">
      <div class="form-main">
        <form action="/CitiServe/public/complaint_create.php" method="POST" enctype="multipart/form-data" id="complaintForm" novalidate>

          <?= csrf_field() ?>

          <input type="hidden" name="category_key" value="<?= htmlspecialchars($categoryKey) ?>">
          <input type="hidden" name="category_label" value="<?= htmlspecialchars($currentCategory['value']) ?>">
          <input type="hidden" name="category_id" value="<?= (int)$currentCategoryId ?>">
          <input type="hidden" name="title" value="Anonymous Report">
          <input type="hidden" name="is_anonymous" value="1">
          <input type="hidden" name="return_to" value="/CitiServe/public/anonymous_complaint_form.php?category_id=<?= (int)$currentCategoryId ?>">

        <div class="form-card reporter-card anonymous-reporter-card">
        <div class="card-header pink-head">
            <img src="/CitiServe/frontend/complaints/images/complaint_reporter.png" alt="" class="card-header-icon">
            <span>Reporter Information</span>
        </div>

        <div class="card-body reporter-body anonymous-reporter-body">
            <div class="anonymous-grid-1">
            <div class="field">
                <label>First Name</label>
                <input type="text" name="first_name" value="Anonymous" readonly class="anonymous-readonly">
            </div>
            </div>

            <div class="grid-2 anonymous-grid-2">
                <div class="field">
                    <label>Contact Number</label>
                    <input type="text" name="contact_number" value="Anonymous" readonly class="anonymous-readonly">
                </div>

                <div class="field">
                    <label>Email Address</label>
                    <input type="text" name="email" value="Anonymous" readonly class="anonymous-readonly">
                </div>
            </div>
        </div>
        </div>

          <div class="form-card details-card">
            <div class="card-header light-head">
              <img src="/CitiServe/frontend/complaints/images/complaint_details.png" alt="" class="card-header-icon">
              <span>Complaint Details</span>
            </div>

            <div class="card-body details-body">
              <div class="field">
                <label>Complaint Category</label>
                <input type="text" value="<?= htmlspecialchars($currentCategory['value']) ?>" readonly>
              </div>

              <div class="field">
                <label>Complaint Description <span>*</span></label>
                <textarea name="description" rows="6" maxlength="10000" placeholder="Describe the issue in detail. Include when it started, how it affects the community, and any other relevant information." required></textarea>
                <div class="char-counter"><span id="charCount">0</span>/10000</div>
              </div>

            <div class="field">
            <label>Complaint Location <span>*</span></label>
            <div class="location-row">
                <input
                type="text"
                name="location"
                id="complaintLocation"
                placeholder="Enter address or describe the location"
                required
                >
                <button type="button" class="use-location-btn" id="useMyLocation">
                <img src="/CitiServe/frontend/complaints/images/complaint_location.png" alt="" class="use-location-icon">
                <span>Use My Location</span>
                </button>
            </div>

            <div class="location-map-box" id="locationMapBox">
                <div class="map-placeholder" id="mapPlaceholder">
                <img src="/CitiServe/frontend/complaints/images/complaint_map.png" alt="" class="map-placeholder-icon">
                <span>Click “Use My Location” to pin on map<br>or type the address in the field above</span>
                </div>

                <iframe
                id="googleMapFrame"
                class="google-map-frame"
                src=""
                loading="lazy"
                allowfullscreen
                referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
            </div>

            <div class="field date-field">
                <label>Date of Incident <span>*</span></label>
                <input type="date" name="incident_date" required>
            </div>
            </div>
        </div>

          <div class="form-card evidence-card">
            <div class="card-header light-head">
                <img src="/CitiServe/frontend/complaints/images/complaint_evidence.png" alt="" class="card-header-icon">
                <span>Upload Evidence <small>(Optional)</small></span>
            </div>

            <div class="card-body evidence-body">
                <label for="evidenceUpload" class="upload-box" id="uploadBox">
                <input
                    type="file"
                    id="evidenceUpload"
                    name="evidence"
                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.mp4"
                >

                <div class="upload-default" id="uploadDefault">
                    <img src="/CitiServe/frontend/complaints/images/complaint_image.png" alt="" class="upload-icon">
                    <div class="upload-title">Click to upload photo or video evidence</div>
                    <div class="upload-sub">JPG, PNG, PDF, MP4 – Max 10MB</div>
                </div>

                <div id="fileList" class="file-list inside-upload"></div>
                </label>

                <div class="upload-note">
                Evidence helps barangay staff assess the complaint faster. Photos of the issue, location, or related documents are accepted.
                </div>
            </div>
            </div>

        <div class="form-actions">
            <a href="/CitiServe/public/complaint_create.php" class="form-action-img-link leave-form-link">
            <img src="/CitiServe/frontend/complaints/images/complaint_form_back.png" alt="Back" class="form-action-img back-action-img">
            </a>

            <button type="submit" class="submit-img-btn">
                <img src="/CitiServe/frontend/complaints/images/complaint_submit_complaint.png" alt="Submit Complaint" class="form-action-img submit-action-img">
            </button>
            </div>
        </form>
      </div>

        <aside class="form-side">
            <div class="side-card">
                <div class="side-head">Category</div>
                <div class="side-category">Anonymous Report</div>
            </div>

            <div class="anonymous-panel-img-wrap">
                <img src="/CitiServe/frontend/complaints/images/anonymous_complaint_panel.png" alt="Anonymous Complaint Panel" class="anonymous-panel-img">
            </div>

            <div class="submission-type-img-wrap">
                <img src="/CitiServe/frontend/complaints/images/complaint_submission_type.png" alt="Submission Type Notice" class="submission-type-img">
            </div>
        </aside>
    </div>

    <div class="complaints-footer">
      <img src="/CitiServe/frontend/complaints/images/citiserve_solo_pink.png" alt="CitiServe" class="footer-logo">
      <div class="footer-text"><span>CitiServe</span> © 2026. All rights reserved.</div>
    </div>
  </div>
</div>


  <!-- SUBMIT CONFIRM MODAL -->
  <div class="custom-modal-overlay" id="submitModal">
    <div class="custom-modal">
      <div class="custom-modal-top">
        <div class="custom-modal-icon-wrap">
          <img src="/CitiServe/frontend/complaints/images/submit_form_check.png" alt="Submit" class="custom-modal-icon">
        </div>

        <div class="custom-modal-content">
          <h3>Submit Form?</h3>
          <p>Once submitted, your responses can no longer be modified. Please review your entries before proceeding.</p>
        </div>
      </div>

      <div class="custom-modal-actions">
        <button type="button" class="modal-btn modal-btn-outline" id="cancelSubmitBtn">Cancel</button>
        <button type="button" class="modal-btn modal-btn-fill" id="confirmSubmitBtn">Submit</button>
      </div>
    </div>
  </div>

  <!-- DISCARD CHANGES MODAL -->
  <div class="custom-modal-overlay" id="discardModal">
    <div class="custom-modal">
      <div class="custom-modal-top">
        <div class="custom-modal-icon-wrap">
          <img src="/CitiServe/frontend/complaints/images/discard_changes_form.png" alt="Discard" class="custom-modal-icon">
        </div>

        <div class="custom-modal-content">
          <h3>Discard Changes?</h3>
          <p>You have unsaved changes. If you leave this page, your progress will be lost.</p>
        </div>
      </div>

      <div class="custom-modal-actions">
        <button type="button" class="modal-btn modal-btn-outline" id="confirmDiscardBtn">Discard Changes</button>
        <button type="button" class="modal-btn modal-btn-fill" id="stayOnPageBtn">Stay on Page</button>
      </div>
    </div>
  </div>

<script src="/CitiServe/frontend/complaints/JS/anonymous_complaint_form.js"></script>
</body>
</html>
