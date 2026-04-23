<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/core/CitiServeData.php';

function bytes_from_ini($value)
{
    $value = trim((string)$value);
    if ($value === '') {
        return 0;
    }

    $unit = strtolower(substr($value, -1));
    $number = (float)$value;

    if ($unit === 'g') {
        return (int)($number * 1024 * 1024 * 1024);
    }
    if ($unit === 'm') {
        return (int)($number * 1024 * 1024);
    }
    if ($unit === 'k') {
        return (int)($number * 1024);
    }

    return (int)$number;
}

function complaint_normalize_category($name)
{
    $v = strtolower((string)$name);
    $v = str_replace(['&', '-', '_'], ' ', $v);
    $v = str_replace(['/', '(', ')'], ' ', $v);
    $v = preg_replace('/\s+/', ' ', $v);
    return trim((string)$v);
}

$user = require_resident();
$data = new CitiServeData();
$categories = $data->getActiveComplaintCategories();
$categoryById = [];
foreach ($categories as $cat) {
    $categoryById[(int)$cat['id']] = $cat;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    $contentLen = isset($_SERVER['CONTENT_LENGTH']) ? (int)$_SERVER['CONTENT_LENGTH'] : 0;
    $postMax = bytes_from_ini((string)ini_get('post_max_size'));
    if ($contentLen > 0 && $postMax > 0 && $contentLen > $postMax && empty($_POST) && empty($_FILES)) {
        $errors[] = 'Only files less than or equal to 10MB are allowed.';
    } else {
        csrf_verify_or_die();

        $categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
        $title = isset($_POST['title']) ? trim((string)$_POST['title']) : '';
        $description = isset($_POST['description']) ? trim((string)$_POST['description']) : '';
        $location = isset($_POST['location']) ? trim((string)$_POST['location']) : '';
        $isAnonymous = isset($_POST['is_anonymous']) && (string)$_POST['is_anonymous'] === '1';

        if ($categoryId <= 0 || !isset($categoryById[$categoryId])) {
            $errors[] = 'Please select a complaint category.';
        }

        if ($description === '') {
            $errors[] = 'Description is required.';
        }

        if ($location === '') {
            $errors[] = 'Location is required.';
        }

        if ($title === '' && isset($categoryById[$categoryId])) {
            $title = (string)$categoryById[$categoryId]['name'];
            if ($isAnonymous) {
                $title = 'Anonymous Report';
            }
        }

        if ($title === '') {
            $errors[] = 'Title is required.';
        }

        $evidenceRelativePath = null;
        $evidenceOriginalName = null;

        $uploadedEvidence = null;
        if (isset($_FILES['evidence']) && is_array($_FILES['evidence'])) {
            if (isset($_FILES['evidence']['name']) && is_array($_FILES['evidence']['name'])) {
                if (!empty($_FILES['evidence']['name'][0])) {
                    $uploadedEvidence = [
                        'name' => $_FILES['evidence']['name'][0],
                        'type' => $_FILES['evidence']['type'][0] ?? '',
                        'tmp_name' => $_FILES['evidence']['tmp_name'][0] ?? '',
                        'error' => $_FILES['evidence']['error'][0] ?? UPLOAD_ERR_NO_FILE,
                        'size' => $_FILES['evidence']['size'][0] ?? 0,
                    ];
                }
            } else {
                $uploadedEvidence = $_FILES['evidence'];
            }
        }

        if ($uploadedEvidence && (int)$uploadedEvidence['error'] !== UPLOAD_ERR_NO_FILE) {
            $err = (int)$uploadedEvidence['error'];

            if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
                $errors[] = 'Only files less than or equal to 10MB are allowed.';
            } elseif ($err !== UPLOAD_ERR_OK) {
                $errors[] = 'Evidence upload failed (code ' . $err . ').';
            } else {
                $size = isset($uploadedEvidence['size']) ? (int)$uploadedEvidence['size'] : 0;
                $tmp = isset($uploadedEvidence['tmp_name']) ? (string)$uploadedEvidence['tmp_name'] : '';

                if ($size > 10 * 1024 * 1024) {
                    $errors[] = 'Only files less than or equal to 10MB are allowed.';
                } elseif ($size <= 0 || $tmp === '' || !is_uploaded_file($tmp)) {
                    $errors[] = 'Uploaded file is invalid. Please reselect the file.';
                } else {
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mime = (string)$finfo->file($tmp);

                    $allowedMimeToExt = [
                        'image/jpeg' => 'jpg',
                        'image/png' => 'png',
                        'application/pdf' => 'pdf',
                        'video/mp4' => 'mp4',
                    ];

                    if (!isset($allowedMimeToExt[$mime])) {
                        $errors[] = 'Evidence must be jpg, jpeg, png, pdf, or mp4.';
                    } else {
                        $uploadDir = __DIR__ . '/uploads/complaint_evidence';
                        if (!is_dir($uploadDir)) {
                            $created = mkdir($uploadDir, 0775, true);
                            if (!$created && !is_dir($uploadDir)) {
                                $errors[] = 'Failed to create evidence upload directory.';
                            }
                        }

                        if (empty($errors) && !is_writable($uploadDir)) {
                            $errors[] = 'Evidence upload directory is not writable.';
                        }

                        if (empty($errors)) {
                            $ext = $allowedMimeToExt[$mime];
                            $safeName = bin2hex(random_bytes(16)) . '.' . $ext;
                            $dest = rtrim($uploadDir, '/\\') . DIRECTORY_SEPARATOR . $safeName;

                            if (move_uploaded_file($tmp, $dest)) {
                                $evidenceRelativePath = 'uploads/complaint_evidence/' . $safeName;
                                $evidenceOriginalName = isset($uploadedEvidence['name']) ? (string)$uploadedEvidence['name'] : 'evidence';
                            } else {
                                $errors[] = 'Evidence upload failed while saving file.';
                            }
                        }
                    }
                }
            }
        }

        if (empty($errors)) {
            $locationForDb = $location !== '' ? $location : null;

            $complaintId = $data->createComplaint([
                'user_id' => (int)$user['id'],
                'category_id' => $categoryId,
                'is_anonymous' => $isAnonymous,
                'title' => $title,
                'description' => $description,
                'location' => $locationForDb,
            ]);

            if ($complaintId > 0) {
                try {
                    $admins = $data->getUsersByRole('admin');
                    foreach ($admins as $admin) {
                        try {
                            $data->createNotification(
                                (int)$admin['id'],
                                'New Complaint Submitted',
                                'A resident submitted a new complaint.',
                                '/CitiServe/public/admin/complaints.php'
                            );
                        } catch (Throwable $ignoredNotificationError) {
                            error_log('COMPLAINT ADMIN NOTIFICATION ERROR: ' . $ignoredNotificationError->getMessage());
                        }
                    }
                } catch (Throwable $ignoredAdminLookupError) {
                    error_log('COMPLAINT ADMIN LOOKUP ERROR: ' . $ignoredAdminLookupError->getMessage());
                }
            }

            if ($complaintId > 0 && $evidenceRelativePath !== null) {
                $data->addComplaintEvidence($complaintId, $evidenceRelativePath, $evidenceOriginalName);
            }

            if ($complaintId > 0) {
                header('Location: /CitiServe/public/complaint_receipt.php?complaint_id=' . (int)$complaintId);
                exit;
            }

            $errors[] = 'Failed to submit complaint.';
        }
    }

    $redirectTarget = '/CitiServe/public/complaint_create.php';
    if (!empty($_POST['return_to'])) {
        $returnTo = (string)$_POST['return_to'];
        if (strpos($returnTo, '/CitiServe/public/complaint_form.php') === 0 || strpos($returnTo, '/CitiServe/public/anonymous_complaint_form.php') === 0) {
            $redirectTarget = $returnTo;
        }
    }

    $_SESSION['complaint_form_errors'] = $errors;
    header('Location: ' . $redirectTarget);
    exit;
}

$profileFirstName = trim(explode(' ', (string)$user['full_name'])[0]);

$notificationsRaw = $data->getNotificationsByUser((int)$user['id']);
$notifications = array_slice($notificationsRaw, 0, 10);

$notifSections = ['new' => [], 'today' => [], 'earlier' => []];
$nowTs = time();

foreach ($notifications as $n) {
    $messageLower = strtolower(((string)$n['title']) . ' ' . ((string)$n['message']));
    $category = 'announcement';
    if (strpos($messageLower, 'complaint') !== false) {
        $category = 'complaint';
    } elseif (strpos($messageLower, 'document') !== false || strpos($messageLower, 'request') !== false) {
        $category = 'document';
    }

    $createdAt = isset($n['created_at']) ? (string)$n['created_at'] : '';
    $createdTs = $createdAt !== '' ? strtotime($createdAt) : false;
    if ($createdTs === false) {
        $createdTs = $nowTs;
    }

    $ageSeconds = max(0, $nowTs - $createdTs);
    if ($ageSeconds < 3600) {
        $timeLabel = max(1, (int)floor($ageSeconds / 60)) . 'm';
    } elseif ($ageSeconds < 86400) {
        $timeLabel = (int)floor($ageSeconds / 3600) . 'h';
    } else {
        $timeLabel = (int)floor($ageSeconds / 86400) . 'd';
    }

    $section = 'earlier';
    if ($ageSeconds < 3600) {
        $section = 'new';
    } elseif ($ageSeconds < 86400) {
        $section = 'today';
    }

    $notifSections[$section][] = [
        'id' => (int)$n['id'],
        'category' => $category,
        'message' => htmlspecialchars((string)$n['message']),
        'time_label' => $timeLabel,
        'read' => ((int)$n['is_read'] === 1),
        'link' => '/CitiServe/public/notifications.php?open=' . (int)$n['id'],
        'main_icon' => '/CitiServe/frontend/complaints/images/citiserve_notif.png',
        'badge_icon' => $category === 'complaint'
            ? '/CitiServe/frontend/complaints/images/complaint_notif.png'
            : '/CitiServe/frontend/complaints/images/document_notif.png',
    ];
}

$unreadCount = 0;
foreach ($notifications as $n) {
    if ((int)$n['is_read'] === 0) {
        $unreadCount++;
    }
}
$hasNotif = $unreadCount > 0;

$categoryCardMap = [
    'road infrastructure' => ['title' => 'Road / Infrastructure', 'icon' => 'pink_road.png', 'subtle' => 'subtle_road.png', 'desc' => 'Damaged roads, potholes, broken drainage, collapsed structures establishments'],
    'garbage sanitation' => ['title' => 'Garbage / Sanitation', 'icon' => 'pink_garbage.png', 'subtle' => 'subtle_garbage.png', 'desc' => 'Uncollected garbage, illegal dumping, poor sanitation'],
    'noise disturbance' => ['title' => 'Noise Disturbance', 'icon' => 'pink_noise.png', 'subtle' => 'subtle_noise.png', 'desc' => 'Excessive noise from neighbors, parties, or establishments'],
    'traffic parking' => ['title' => 'Traffic / Parking', 'icon' => 'pink_traffic.png', 'subtle' => 'subtle_traffic.png', 'desc' => 'Traffic obstruction, illegal parking, road blockages'],
    'environmental tree animal concerns' => ['title' => 'Environmental / Tree / Animal', 'icon' => 'pink_environmental.png', 'subtle' => 'subtle_environmental.png', 'desc' => 'Fallen trees, stray animals, environmental violations'],
    'water electricity utilities' => ['title' => 'Water / Electricity / Utilities', 'icon' => 'pink_water.png', 'subtle' => 'subtle_water.png', 'desc' => 'Water supply issues, power outages, utility disruptions'],
    'community social issues' => ['title' => 'Community / Social Issues', 'icon' => 'pink_community.png', 'subtle' => 'subtle_community.png', 'desc' => 'Community disputes, social concerns, barangay matters'],
    'other' => ['title' => 'Other Concerns', 'icon' => 'pink_otherconcerns.png', 'subtle' => 'subtle_otherconcerns.png', 'desc' => 'Issues not covered by other categories'],
];

$complaintCategories = [];
$anonymousCategoryId = 0;
foreach ($categories as $cat) {
    $normalized = complaint_normalize_category((string)$cat['name']);
    $mapped = isset($categoryCardMap[$normalized]) ? $categoryCardMap[$normalized] : $categoryCardMap['other'];

    $card = [
        'id' => (int)$cat['id'],
        'title' => $mapped['title'],
        'desc' => $mapped['desc'],
        'icon' => '/CitiServe/frontend/complaints/images/' . $mapped['icon'],
        'subtle' => '/CitiServe/frontend/complaints/images/' . $mapped['subtle'],
        'link' => '/CitiServe/public/complaint_form.php?category_id=' . (int)$cat['id'],
    ];

    $complaintCategories[] = $card;
    if ($normalized === 'other' && $anonymousCategoryId === 0) {
        $anonymousCategoryId = (int)$cat['id'];
    }
}
if ($anonymousCategoryId === 0 && !empty($complaintCategories)) {
    $anonymousCategoryId = (int)$complaintCategories[0]['id'];
}

$complaintCategories[] = [
    'id' => $anonymousCategoryId,
    'title' => 'Anonymous Report',
    'desc' => 'Submit a complaint without revealing your identity',
    'icon' => '/CitiServe/frontend/complaints/images/pink_anonymous.png',
    'subtle' => '/CitiServe/frontend/complaints/images/subtle_anonymous.png',
    'link' => '/CitiServe/public/anonymous_complaint_form.php?category_id=' . (int)$anonymousCategoryId,
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CitiServe - Submit Complaint</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/CitiServe/frontend/complaints/css/complaint.css">
</head>
<body>

<div class="design-strip left" aria-hidden="true"><img src="/CitiServe/frontend/complaints/images/complaint_design.png" alt=""></div>
<div class="design-strip right" aria-hidden="true"><img src="/CitiServe/frontend/complaints/images/complaint_design.png" alt=""></div>

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
      data-img-on="/CitiServe/frontend/complaints/images/with_notif.png"
      data-img-off="/CitiServe/frontend/complaints/images/no_notif.png"
      data-img-active="/CitiServe/frontend/complaints/images/select_notif.png"
      title="Notifications">
      <img id="notifIcon"
        src="<?= $hasNotif ? '/CitiServe/frontend/complaints/images/with_notif.png' : '/CitiServe/frontend/complaints/images/no_notif.png' ?>"
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
                  <img class="notif-icon-main"
                      src="<?= htmlspecialchars($n['main_icon']) ?>"
                      alt="">
                  <?php if (!empty($n['badge_icon'])): ?>
                    <img class="notif-icon-badge"
                        src="<?= htmlspecialchars($n['badge_icon']) ?>"
                        alt="">
                  <?php endif; ?>
                </div>

                <div class="notif-text">
                  <div class="notif-msg"><?= htmlspecialchars((string)$n['message']) ?></div>
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
        <img src="/CitiServe/frontend/complaints/images/profile_icon.png" alt="Profile">
      </div>
      <span class="profile-name"><?= htmlspecialchars($profileFirstName) ?></span>
      <span class="profile-chevron"><img src="/CitiServe/frontend/complaints/images/profile_dropdown.png" alt=""></span>
    </div>

    <div class="profile-panel" id="profilePanel">
      <div class="profile-panel-top">
        <div class="profile-panel-fullname"><?= htmlspecialchars((string)$user['full_name']) ?></div>
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
  <div class="complaints-card">
    <div class="breadcrumb">
      <a href="/CitiServe/public/complaint_create.php">Complaint Management</a>
      <span>></span>
      <span class="current">Submit Complaint</span>
    </div>

    <h1 class="page-title">Submit a Community Complaint</h1>
    <p class="page-subtitle">Select the category that best describes your concern.</p>

    <div class="reminders-wrap">
      <img src="/CitiServe/frontend/complaints/images/complaint_reminders.png" alt="Important Reminders" class="reminders-img">
    </div>

    <div class="complaint-grid">
      <?php foreach ($complaintCategories as $item): ?>
            <a href="<?= $item['title'] === 'Anonymous Report' ? '#' : htmlspecialchars($item['link']) ?>"
            class="complaint-card-item <?= $item['title'] === 'Other Concerns' ? 'other-concerns-card' : '' ?>"
            <?= $item['title'] === 'Anonymous Report' ? 'id="anonymousBtn" data-anonymous-link="' . htmlspecialchars($item['link']) . '"' : '' ?>>
            <div class="complaint-card-subtle">
            <img src="<?= htmlspecialchars($item['subtle']) ?>" alt="">
          </div>

          <div class="complaint-card-main">
            <div class="complaint-icon-wrap">
              <img src="<?= htmlspecialchars($item['icon']) ?>" alt="" class="complaint-icon">
            </div>

            <div class="complaint-details">
              <div class="complaint-title"><?= htmlspecialchars($item['title']) ?></div>
              <div class="complaint-desc"><?= htmlspecialchars($item['desc']) ?></div>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>

    <div class="complaints-footer">
      <img src="/CitiServe/frontend/complaints/images/citiserve_solo_pink.png" alt="CitiServe" class="footer-logo">
      <div class="footer-text"><span>CitiServe</span> © 2026. All rights reserved.</div>
    </div>
  </div>
</div>

<div class="anon-modal" id="anonModal">
  <div class="anon-modal-box">

    <img src="/CitiServe/frontend/complaints/images/anonymous_panel.png" alt="Anonymous Warning" class="anon-img">

    <div class="anon-actions">
    <div class="anon-actions">
      <img src="/CitiServe/frontend/complaints/images/anonymous_back.png" id="anonBack" class="anon-btn" alt="Back">
      <img src="/CitiServe/frontend/complaints/images/anonymous_continue.png" id="anonContinue" class="anon-btn" alt="Continue">
    </div>
    </div>
  </div>
</div>

<script src="/CitiServe/frontend/complaints/JS/complaint.js"></script>
</body>
</html>
