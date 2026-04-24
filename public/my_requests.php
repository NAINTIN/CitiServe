<?php
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/core/CitiServeData.php';

$user = require_login();
$data = new CitiServeData();
$requests = $data->getDocumentRequestsByUserId((int)$user['id']);

$notifications = [];
$notifSections = ['new' => [], 'today' => [], 'earlier' => []];
$hasNotif = false;

function h($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function request_status_image($status)
{
    $status = strtolower((string)$status);
    return match ($status) {
        'received' => '/CitiServe/frontend/my_resident/images/my_request_received.png',
        'pending' => '/CitiServe/frontend/my_resident/images/my_request_pending.png',
        'claimable' => '/CitiServe/frontend/my_resident/images/my_request_claimable.png',
        'rejected' => '/CitiServe/frontend/my_resident/images/my_request_rejected.png',
        'released' => '/CitiServe/frontend/my_resident/images/my_request_claimable.png',
        default => '/CitiServe/frontend/my_resident/images/my_request_received.png',
    };
}

$firstName = trim(explode(' ', (string)($user['full_name'] ?? 'Resident'))[0]);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Requests – CitiServe</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/CitiServe/frontend/my_resident/CSS/my_requests.css">
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

    <div class="nav-item has-dropdown active" id="navDocReq">
      <span class="nav-text">Document Requests</span>
      <span class="nav-chevron">⏷</span>
      <div class="nav-dropdown">
        <a href="/CitiServe/public/request_select.php" class="nav-dropdown-item">Request Document</a>
        <a href="/CitiServe/public/my_requests.php" class="nav-dropdown-item">My Requests</a>
      </div>
    </div>

    <div class="nav-item has-dropdown" id="navComplaint">
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
  <div class="requests-page-card">
    <img src="/CitiServe/frontend/my_resident/images/flower_design.png" alt="" class="flower-design top-flower">
    <img src="/CitiServe/frontend/my_resident/images/flower_design.png" alt="" class="flower-design bottom-flower">

    <div class="breadcrumb">
      <a href="/CitiServe/public/request_select.php">Document Requests</a>
      <span>></span>
      <span class="current">My Requests</span>
    </div>

    <div class="page-head">
      <h1 class="page-title">My Document Requests</h1>
      <p class="page-subtitle">Track the status of your document requests.</p>
    </div>

    <div class="toolbar">
      <div class="toolbar-left">
        <div class="search-wrap">
          <img src="/CitiServe/frontend/my_resident/images/search_icon.png" alt="" class="search-icon">
          <input type="text" id="searchInput" placeholder="Search by document type or request ID...">
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
            <div class="filter-option" data-value="pending">Pending</div>
            <div class="filter-option" data-value="claimable">Claimable</div>
            <div class="filter-option" data-value="rejected">Rejected</div>
            <div class="filter-option" data-value="released">Released</div>
          </div>
        </div>

        <a href="/CitiServe/public/request_select.php" class="new-request-btn">
          <img src="/CitiServe/frontend/my_resident/images/my_request_new_request.png" alt="New Request">
        </a>
      </div>
    </div>

    <div class="requests-table-card">
      <div class="requests-table-head">
        <div>Request ID</div>
        <div>Document</div>
        <div>Date</div>
        <div>Fee</div>
        <div>Payment</div>
        <div>Status</div>
        <div></div>
      </div>

      <?php if (empty($requests)): ?>
        <div class="requests-empty">
          <img src="/CitiServe/frontend/my_resident/images/recent_request_main.png" alt="" class="empty-icon">
          <div class="empty-text">No document requests yet</div>
        </div>
      <?php else: ?>
        <div class="requests-table-body" id="requestsTableBody">
          <?php foreach ($requests as $r): ?>
            <?php
              $status = strtolower((string)($r['status'] ?? 'received'));
              $createdTs = strtotime((string)($r['created_at'] ?? ''));
              $dateLabel = $createdTs ? date('M j, Y', $createdTs) : (string)($r['created_at'] ?? '-');
              $dateTimeFull = $createdTs ? date('m/d/Y, g:i:s A', $createdTs) : (string)($r['created_at'] ?? '-');
              $requestLabel = 'DOC-' . str_pad((string)((int)$r['id']), 10, '0', STR_PAD_LEFT);
            ?>
            <div class="request-row"
                data-request-id="<?= h($requestLabel) ?>"
                data-document="<?= h((string)($r['service_name'] ?? '-')) ?>"
                data-date="<?= h($dateLabel) ?>"
                data-fee="₱<?= h((string)($r['fee'] ?? '0.00')) ?>"
                data-payment="<?= h((string)($r['payment_method'] ?? '-')) ?>"
                data-status="<?= h($status) ?>"
                data-status-image="<?= h(request_status_image($status)) ?>"
                data-datetime-full="<?= h($dateTimeFull) ?>"
                data-reference-number="<?= h((string)($r['payment_reference'] ?? '-')) ?>">

              <div class="request-cell request-id"><?= h($requestLabel) ?></div>
              <div class="request-cell document-name"><?= h((string)($r['service_name'] ?? '-')) ?></div>
              <div class="request-cell request-date"><?= h($dateLabel) ?></div>
              <div class="request-cell fee">₱<?= h((string)($r['fee'] ?? '0.00')) ?></div>
              <div class="request-cell payment"><?= h((string)($r['payment_method'] ?? '-')) ?></div>
              <div class="request-cell">
                <img src="<?= h(request_status_image($status)) ?>" alt="<?= h($status) ?>" class="status-badge-img">
              </div>
              <div class="request-cell details-cell">
                <button type="button" class="details-btn">Details</button>
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

<div class="request-details-modal" id="requestDetailsModal">
  <div class="request-details-box">
    <button type="button" class="request-details-close" id="requestDetailsClose" aria-label="Close">×</button>

    <div class="request-details-title">Request Details</div>
    <div class="request-details-divider"></div>

    <div class="request-details-body">
      <img src="/CitiServe/frontend/my_resident/images/complaint_faded_logo.png" alt="" class="request-details-faded-logo">

      <div class="request-detail-row"><div class="request-detail-label">Request ID</div><div class="request-detail-value" id="modalRequestId">-</div></div>
      <div class="request-detail-row"><div class="request-detail-label">Document Type</div><div class="request-detail-value" id="modalDocumentType">-</div></div>
      <div class="request-detail-row"><div class="request-detail-label">Date Submitted</div><div class="request-detail-value" id="modalDateSubmitted">-</div></div>
      <div class="request-detail-row"><div class="request-detail-label">Fee</div><div class="request-detail-value" id="modalFee">-</div></div>
      <div class="request-detail-row"><div class="request-detail-label">Payment Method</div><div class="request-detail-value" id="modalPaymentMethod">-</div></div>
      <div class="request-detail-row"><div class="request-detail-label">Reference Number</div><div class="request-detail-value" id="modalReferenceNumber">-</div></div>
    </div>

    <div class="request-details-divider bottom"></div>

    <div class="request-detail-status-row">
      <div class="request-detail-label">Status</div>
      <div class="request-detail-status-value">
        <img src="/CitiServe/frontend/my_resident/images/my_request_received.png" alt="Status" id="modalStatusImage" class="request-detail-status-img">
      </div>
    </div>

    <div class="request-detail-warning" id="requestDetailWarning">
      <img src="/CitiServe/frontend/my_resident/images/rejected_request_warning.png" alt="Rejected Warning" class="request-warning-full-img">
    </div>
  </div>
</div>

<script src="/CitiServe/frontend/my_resident/JS/my_requests.js"></script>
</body>
</html>
