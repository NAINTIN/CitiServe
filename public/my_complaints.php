<?php
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/core/CitiServeData.php';

$user = require_login();
$data = new CitiServeData();
$deletableStatuses = ['submitted', 'under_review', 'in_progress'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify_or_die();
    $action = isset($_POST['action']) ? trim((string)$_POST['action']) : '';
    $complaintId = isset($_POST['complaint_id']) ? (int)$_POST['complaint_id'] : 0;

    if ($action === 'delete_complaint' && $complaintId > 0) {
        $current = $data->findComplaintByIdWithOwner($complaintId);
        if (!$current || (int)$current['user_id'] !== (int)$user['id']) {
            $error = 'Complaint not found.';
        } elseif (!in_array(strtolower((string)$current['status']), $deletableStatuses, true)) {
            $error = 'This complaint can no longer be deleted.';
        } else {
            try {
                $data->deleteComplaintById($complaintId);
                $message = 'Complaint deleted successfully.';
            } catch (Throwable $e) {
                $error = 'Failed to delete complaint.';
            }
        }
    }
}

$rows = $data->getComplaintsByUserId((int)$user['id']);

$notifications = [];
$notifSections = ['new' => [], 'today' => [], 'earlier' => []];
$hasNotif = false;

function h($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function complaint_type_image($isAnonymous)
{
    return ((int)$isAnonymous === 1)
        ? '/CitiServe/frontend/my_resident/images/my_complaint_anonymous.png'
        : '/CitiServe/frontend/my_resident/images/my_complaint_identified.png';
}

function complaint_status_key($status)
{
    $status = strtolower((string)$status);
    return match ($status) {
        'submitted' => 'received',
        'under_review', 'in_progress' => 'processing',
        'resolved' => 'resolved',
        'rejected' => 'rejected',
        default => 'received',
    };
}

function complaint_status_image($status)
{
    $key = complaint_status_key($status);
    return match ($key) {
        'received' => '/CitiServe/frontend/my_resident/images/my_complaint_received.png',
        'processing' => '/CitiServe/frontend/my_resident/images/my_complaint_processing.png',
        'resolved' => '/CitiServe/frontend/my_resident/images/my_complaint_resolved.png',
        'rejected' => '/CitiServe/frontend/my_resident/images/my_complaint_rejected.png',
        default => '/CitiServe/frontend/my_resident/images/my_complaint_received.png',
    };
}

$firstName = trim(explode(' ', (string)($user['full_name'] ?? 'Resident'))[0]);
$contactNumber = isset($user['contact_number']) ? (string)$user['contact_number'] : 'Not provided';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Complaints – CitiServe</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/CitiServe/frontend/my_resident/CSS/my_complaints.css">
</head>
<body>

<div class="design-strip left" aria-hidden="true"><img src="/CitiServe/frontend/my_resident/images/dashboard_design.png" alt=""></div>
<div class="design-strip right" aria-hidden="true"><img src="/CitiServe/frontend/my_resident/images/dashboard_design.png" alt=""></div>

<nav class="navbar">
  <a href="/CitiServe/public/dashboard.php" class="navbar-logo">
    <img src="/CitiServe/frontend/my_resident/images/logo_pink.png" alt="CitiServe">
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
      data-img-on="/CitiServe/frontend/my_resident/images/with_notif.png"
      data-img-off="/CitiServe/frontend/my_resident/images/no_notif.png"
      data-img-active="/CitiServe/frontend/my_resident/images/select_notif.png"
      title="Notifications">
      <img id="notifIcon"
        src="<?= $hasNotif ? '/CitiServe/frontend/my_resident/images/with_notif.png' : '/CitiServe/frontend/my_resident/images/no_notif.png' ?>"
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
        <div class="notif-empty">No notifications yet.</div>
      </div>
      <div class="notif-footer">
        <button class="notif-see-prev" id="notifSeePrev"><p>See previous notifications</p></button>
      </div>
    </div>

    <div class="profile-pill" id="profilePill">
      <div class="profile-avatar">
        <img src="/CitiServe/frontend/my_resident/images/profile_icon.png" alt="Profile">
      </div>
      <span class="profile-name"><?= h($firstName) ?></span>
      <span class="profile-chevron"><img src="/CitiServe/frontend/my_resident/images/profile_dropdown.png" alt=""></span>
    </div>

    <div class="profile-panel" id="profilePanel">
      <div class="profile-panel-top">
        <div class="profile-panel-fullname"><?= h((string)$user['full_name']) ?></div>
        <div class="profile-panel-subtext">Resident • Brgy. Kalayaan</div>
      </div>
      <a href="/CitiServe/public/profile.php" class="profile-panel-item">
        <img src="/CitiServe/frontend/my_resident/images/my_profile.png" alt="My Profile" class="profile-panel-icon1">
        <span>My Profile</span>
      </a>
      <a href="/CitiServe/public/logout.php" class="profile-panel-item logout">
        <img src="/CitiServe/frontend/my_resident/images/logout.png" alt="Logout" class="profile-panel-icon2">
        <span>Logout</span>
      </a>
    </div>
  </div>
</nav>

<div class="page-body">
  <div class="complaints-page-card">
    <?php if ($message): ?>
      <div style="margin: 12px 0; color: #198754; font-weight: 600;"><?= h($message) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div style="margin: 12px 0; color: #c1121f; font-weight: 600;"><?= h($error) ?></div>
    <?php endif; ?>

    <img src="/CitiServe/frontend/my_resident/images/flower_design.png" alt="" class="flower-design top-flower">
    <img src="/CitiServe/frontend/my_resident/images/flower_design.png" alt="" class="flower-design bottom-flower">

    <div class="breadcrumb">
      <a href="/CitiServe/public/complaint_create.php">Complaint Management</a>
      <span>></span>
      <span class="current">My Complaints</span>
    </div>

    <div class="page-head">
      <h1 class="page-title">My Complaints</h1>
      <p class="page-subtitle">Track the status of your submitted complaints.</p>
    </div>

    <div class="toolbar">
      <div class="toolbar-left">
        <div class="search-wrap">
          <img src="/CitiServe/frontend/my_resident/images/search_icon.png" alt="" class="search-icon">
          <input type="text" id="searchInput" placeholder="Search by category or request ID...">
        </div>

        <button type="button" class="clear-btn" id="clearBtn">
          <img src="/CitiServe/frontend/my_resident/images/my_request_clear.png" alt="Clear">
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
            <div class="filter-option" data-value="received">Received</div>
            <div class="filter-option" data-value="processing">Processing</div>
            <div class="filter-option" data-value="resolved">Resolved</div>
            <div class="filter-option" data-value="rejected">Rejected</div>
          </div>
        </div>

        <a href="/CitiServe/public/complaint_create.php" class="new-complaint-btn">
          <img src="/CitiServe/frontend/my_resident/images/my_complaint_new_complaint.png" alt="New Complaint">
        </a>
      </div>
    </div>

    <div class="complaints-table-card">
      <div class="complaints-table-head">
        <div>Request ID</div>
        <div>Category</div>
        <div>Date</div>
        <div>Type</div>
        <div>Status</div>
        <div></div>
      </div>

      <?php if (empty($rows)): ?>
        <div class="complaints-empty">
          <img src="/CitiServe/frontend/my_resident/images/my_complaint_empty_icon.png" alt="" class="empty-icon">
          <div class="empty-text">No complaints submitted yet</div>
        </div>
      <?php else: ?>
        <div class="complaints-table-body" id="complaintsTableBody">
          <?php foreach ($rows as $r): ?>
            <?php
              $createdTs = strtotime((string)($r['created_at'] ?? ''));
              $dateLabel = $createdTs ? date('M j, Y', $createdTs) : (string)($r['created_at'] ?? '-');
              $dateTimeFull = $createdTs ? date('m/d/Y, g:i:s A', $createdTs) : (string)($r['created_at'] ?? '-');
              $complaintLabel = 'CMP-' . str_pad((string)((int)$r['id']), 10, '0', STR_PAD_LEFT);
              $statusKey = complaint_status_key((string)($r['status'] ?? 'submitted'));
              $submittedBy = ((int)($r['is_anonymous'] ?? 0) === 1) ? 'Anonymous' : (string)($user['full_name'] ?? 'Resident');
            ?>
            <div class="complaint-row"
                data-request-id="<?= h($complaintLabel) ?>"
                data-category="<?= h((string)($r['category_name'] ?? '-')) ?>"
                data-date="<?= h($dateLabel) ?>"
                data-type="<?= ((int)($r['is_anonymous'] ?? 0) === 1) ? 'Anonymous' : 'Identified' ?>"
                data-status="<?= h($statusKey) ?>"
                data-status-image="<?= h(complaint_status_image((string)($r['status'] ?? 'submitted'))) ?>"
                data-submitted-by="<?= h($submittedBy) ?>"
                data-contact="<?= h($contactNumber) ?>"
                data-description="<?= h((string)($r['description'] ?? '')) ?>"
                data-location-text="<?= h((string)($r['location'] ?? '')) ?>"
                data-map-query="<?= h((string)($r['location'] ?? 'Barangay Kalayaan, Angono, Rizal')) ?>"
                data-evidence=""
                data-datetime-full="<?= h($dateTimeFull) ?>">

              <div class="complaint-cell complaint-id"><?= h($complaintLabel) ?></div>
              <div class="complaint-cell complaint-category"><?= h((string)($r['category_name'] ?? '-')) ?></div>
              <div class="complaint-cell complaint-date"><?= h($dateLabel) ?></div>
              <div class="complaint-cell complaint-type">
                <img src="<?= h(complaint_type_image((int)($r['is_anonymous'] ?? 0))) ?>" alt="Type" class="complaint-type-img">
              </div>
              <div class="complaint-cell complaint-status">
                <img src="<?= h(complaint_status_image((string)($r['status'] ?? 'submitted'))) ?>" alt="Status" class="complaint-status-img">
              </div>
              <div class="complaint-cell details-cell">
                <button type="button" class="details-btn">Details</button>
                <?php if (in_array(strtolower((string)($r['status'] ?? '')), $deletableStatuses, true)): ?>
                  <form method="post" style="margin-top:6px;" onsubmit="return confirm('Delete this complaint? This action cannot be undone.');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete_complaint">
                    <input type="hidden" name="complaint_id" value="<?= (int)$r['id'] ?>">
                    <button type="submit" class="details-btn">Delete</button>
                  </form>
                <?php endif; ?>
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

<div class="complaint-details-modal" id="complaintDetailsModal">
  <div class="complaint-details-box" id="complaintDetailsBox">
    <button type="button" class="complaint-details-close" id="complaintDetailsClose" aria-label="Close">×</button>

    <div class="complaint-details-title">Complaint Details</div>
    <div class="complaint-details-divider"></div>

    <div class="complaint-details-body">
      <div class="complaint-top-grid">
        <div class="complaint-top-col">
          <div class="complaint-detail-row compact"><div class="complaint-detail-label">Complaint ID</div><div class="complaint-detail-value left" id="modalComplaintId">-</div></div>
          <div class="complaint-detail-row compact"><div class="complaint-detail-label">Category</div><div class="complaint-detail-value left" id="modalComplaintCategory">-</div></div>
          <div class="complaint-detail-row compact"><div class="complaint-detail-label">Submitted By</div><div class="complaint-detail-value left" id="modalComplaintSubmittedBy">-</div></div>
        </div>

        <div class="complaint-top-col">
          <div class="complaint-detail-row compact">
            <div class="complaint-detail-label">Status</div>
            <div class="complaint-detail-status-wrap"><img src="/CitiServe/frontend/my_resident/images/my_complaint_processing.png" alt="Status" id="modalComplaintStatusImage" class="complaint-detail-status-img"></div>
          </div>
          <div class="complaint-detail-row compact"><div class="complaint-detail-label">Date Submitted</div><div class="complaint-detail-value left" id="modalComplaintDateSubmitted">-</div></div>
          <div class="complaint-detail-row compact"><div class="complaint-detail-label">Contact</div><div class="complaint-detail-value left" id="modalComplaintContact">-</div></div>
        </div>
      </div>

      <div class="complaint-section">
        <div class="complaint-detail-label">Description</div>
        <div class="complaint-description-box" id="modalComplaintDescription">-</div>
      </div>

      <div class="complaint-section">
        <div class="complaint-detail-label">Location</div>
        <div class="complaint-location-text" id="modalComplaintLocationText">-</div>
        <div class="complaint-map-wrap">
          <iframe id="modalComplaintMap" class="complaint-map-frame" src="" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>

      <div class="complaint-section complaint-evidence-section" id="complaintEvidenceSection">
        <div class="complaint-detail-label">Evidence</div>
        <div class="complaint-evidence-box"><div class="complaint-evidence-file" id="modalComplaintEvidence"></div></div>
      </div>
    </div>
  </div>
</div>

<script src="/CitiServe/frontend/my_resident/JS/my_complaints.js"></script>
</body>
</html>
