<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../storage/php-error.log');
error_reporting(E_ALL);

require_once __DIR__ . '/../../app/helpers/auth.php';
require_once __DIR__ . '/../../app/helpers/csrf.php';
require_once __DIR__ . '/../../app/core/CitiServeData.php';
require_once __DIR__ . '/../../app/helpers/document_request.php';

$admin = require_admin();
$data = new CitiServeData();
$db = $data->getPdo();

$allowedStatuses = ['received', 'pending', 'claimable', 'rejected', 'released'];
$deletableStatuses = ['received', 'pending', 'claimable'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify_or_die();

    $action = isset($_POST['action']) ? trim((string)$_POST['action']) : 'update_status';
    $requestId = isset($_POST['request_id']) ? (int)$_POST['request_id'] : 0;

    if ($action === 'delete') {
        if ($requestId <= 0) {
            $error = 'Invalid delete request.';
        } else {
            $current = $data->findDocumentRequestById($requestId);
            if (!$current) {
                $error = 'Request not found.';
            } elseif (!in_array((string)$current['status'], $deletableStatuses, true)) {
                $error = 'Request can no longer be deleted in its current status.';
            } else {
                $db->beginTransaction();
                try {
                    $data->deleteDocumentRequestById($requestId);
                    $db->commit();
                    $message = "Request #{$requestId} deleted.";
                } catch (Throwable $e) {
                    if ($db->inTransaction()) {
                        $db->rollBack();
                    }
                    error_log('ADMIN REQUEST DELETE ERROR: ' . $e->getMessage());
                    $error = 'Failed to delete request.';
                }
            }
        }
    } else {
        $newStatus = isset($_POST['new_status']) ? trim((string)$_POST['new_status']) : '';
        $notes = isset($_POST['notes']) ? trim((string)$_POST['notes']) : '';

        if ($requestId <= 0 || !in_array($newStatus, $allowedStatuses, true)) {
            $error = 'Invalid request.';
        } else {
            $current = $data->findDocumentRequestById($requestId);

            if (!$current) {
                $error = 'Request not found.';
            } elseif ($current['status'] === $newStatus) {
                $error = 'Status is already set to that value.';
            } else {
                $db->beginTransaction();
                try {
                    $data->updateDocumentRequestStatus($requestId, $newStatus);

                    $stmt = $db->prepare(
                        "INSERT INTO status_history (entity_type, entity_id, old_status, new_status, changed_by, notes)
                         VALUES ('document_request', ?, ?, ?, ?, ?)"
                    );
                    $stmt->execute([
                        $requestId,
                        $current['status'],
                        $newStatus,
                        (int)$admin['id'],
                        $notes !== '' ? $notes : null,
                    ]);

                    if (!empty($current['user_id'])) {
                        $data->createNotification(
                            (int)$current['user_id'],
                            'Document Request Update',
                            "Your request #{$requestId} status is now '{$newStatus}'.",
                            '/CitiServe/public/my_requests.php'
                        );
                    }

                    $db->commit();
                    $message = "Request #{$requestId} updated to '{$newStatus}'.";
                } catch (Throwable $e) {
                    if ($db->inTransaction()) {
                        $db->rollBack();
                    }
                    error_log('ADMIN REQUEST UPDATE ERROR: ' . $e->getMessage());
                    $error = 'Failed to update request.';
                }
            }
        }
    }
}

$rows = $data->getAllDocumentRequestsWithUsers();

$receivedCount = 0;
$pendingCount = 0;
$claimableCount = 0;
foreach ($rows as $r) {
    $st = strtolower((string)$r['status']);
    if ($st === 'received') {
        $receivedCount++;
    } elseif ($st === 'pending') {
        $pendingCount++;
    } elseif ($st === 'claimable') {
        $claimableCount++;
    }
}

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Document Requests - CitiServe</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/CitiServe/frontend/admin_dashboard/CSS/admin_document_request.css">
</head>
<body>

<div class="design-strip left"><img src="/CitiServe/frontend/admin_dashboard/images/dashboard_design.png" alt=""></div>
<div class="design-strip right"><img src="/CitiServe/frontend/admin_dashboard/images/dashboard_design.png" alt=""></div>

<nav class="navbar">
  <a href="/CitiServe/public/admin/dashboard.php" class="navbar-logo">
    <img src="/CitiServe/frontend/admin_dashboard/images/logo_pink.png" alt="CitiServe">
  </a>
  <div class="navbar-nav admin-nav">
    <a href="/CitiServe/public/admin/dashboard.php" class="nav-item"><span class="nav-text">Dashboard</span></a>
    <a href="/CitiServe/public/admin/requests.php" class="nav-item active"><span class="nav-text">Document Requests</span></a>
    <a href="/CitiServe/public/admin/complaints.php" class="nav-item"><span class="nav-text">Complaints</span></a>
    <div class="nav-item has-dropdown">
      <span class="nav-text">User Management</span><span class="nav-chevron">⏷</span>
      <div class="nav-dropdown">
        <a href="/CitiServe/public/admin/users.php" class="nav-dropdown-item">Residents</a>
      </div>
    </div>
    <a href="/CitiServe/public/admin/user_verification.php" class="nav-item"><span class="nav-text">Account Verification</span></a>
  </div>
  <div class="navbar-right admin-navbar-right">
    <div class="profile-pill" id="profilePill">
      <div class="profile-avatar"><img src="/CitiServe/frontend/admin_dashboard/images/admin_dummy_icon.png" alt="Admin"></div>
      <span class="profile-name"><?= h(explode(' ', (string)$admin['full_name'])[0]) ?></span>
      <span class="profile-chevron"><img src="/CitiServe/frontend/admin_dashboard/images/profile_dropdown.png" alt=""></span>
    </div>
    <div class="profile-panel" id="profilePanel">
      <div class="profile-panel-top">
        <div class="profile-panel-fullname"><?= h($admin['full_name']) ?></div>
        <div class="profile-panel-subtext"><?= h(ucfirst((string)$admin['role'])) ?> • Brgy. Kalayaan</div>
      </div>
      <a href="/CitiServe/public/logout.php" class="profile-panel-item logout">
        <img src="/CitiServe/frontend/admin_dashboard/images/logout.png" class="profile-panel-icon2" alt=""><span>Logout</span>
      </a>
    </div>
  </div>
</nav>

<div class="page-body">
  <div class="admin-doc-card">
    <div class="page-head">
      <h1 class="page-title">Document Request Management</h1>
      <p class="page-subtitle">Review and process all document requests from residents.</p>
    </div>
    <?php if ($message): ?><p style="color:#15803d;margin-top:10px;"><?= h($message) ?></p><?php endif; ?>
    <?php if ($error): ?><p style="color:#b91c1c;margin-top:10px;"><?= h($error) ?></p><?php endif; ?>

    <div class="admin-summary-row">
      <div class="summary-box summary-received"><div class="summary-number"><?= (int)$receivedCount ?></div><div class="summary-label">Received</div><img src="/CitiServe/frontend/admin_dashboard/images/received_faded_icon.png" class="summary-icon1"></div>
      <div class="summary-box summary-pending"><div class="summary-number"><?= (int)$pendingCount ?></div><div class="summary-label">Pending</div><img src="/CitiServe/frontend/admin_dashboard/images/pending_faded_icon.png" class="summary-icon2"></div>
      <div class="summary-box summary-claimable"><div class="summary-number"><?= (int)$claimableCount ?></div><div class="summary-label">Claimable</div><img src="/CitiServe/frontend/admin_dashboard/images/claimable_faded.png" class="summary-icon3"></div>
    </div>

    <div class="toolbar">
      <div class="toolbar-left">
        <div class="search-wrap">
          <img src="/CitiServe/frontend/admin_dashboard/images/search_icon.png" alt="" class="search-icon">
          <input type="text" id="searchInput" placeholder="Search by resident, document type, or request ID...">
        </div>
        <button type="button" class="clear-btn" id="clearBtn"><img src="/CitiServe/frontend/admin_dashboard/images/my_request_clear.png" alt="Clear"></button>
      </div>
      <div class="toolbar-right">
        <div class="custom-filter" id="statusFilter">
          <button type="button" class="filter-box" id="filterBox"><span id="selectedStatusText">All</span><span class="filter-arrow">▾</span></button>
          <div class="filter-dropdown" id="filterDropdown">
            <div class="filter-option active" data-value="all">All</div>
            <div class="filter-option" data-value="pending">Pending</div>
            <div class="filter-option" data-value="claimable">Claimable</div>
          </div>
        </div>
      </div>
    </div>

    <div class="requests-table-card">
      <div class="requests-table-head">
        <div>Request ID</div><div>Resident</div><div>Document</div><div>Date</div><div>Fee</div><div>Payment</div><div>Status</div><div></div>
      </div>
      <?php if (empty($rows)): ?>
        <div class="requests-empty"><img src="/CitiServe/frontend/admin_dashboard/images/recent_request_main.png" alt="" class="empty-icon"><div class="empty-text">No document requests yet</div></div>
      <?php else: ?>
        <div class="requests-table-body" id="requestsTableBody">
          <?php foreach ($rows as $r): ?>
            <?php
            $createdTs = !empty($r['created_at']) ? strtotime((string)$r['created_at']) : false;
            $dateLabel = $createdTs ? date('M j, Y', $createdTs) : (string)$r['created_at'];
            $dateTimeFull = $createdTs ? date('m/d/Y, g:i:s A', $createdTs) : (string)$r['created_at'];
            $requestLabel = 'DOC-' . str_pad((string)((int)$r['id']), 10, '0', STR_PAD_LEFT);
            $files = $data->getDocumentRequestFilesByRequestId((int)$r['id']);
            $requirements = [];
            foreach ($files as $f) {
                $requirements[] = (string)($f['original_name'] ?: document_request_readable_field_name((string)$f['file_type']));
            }
            ?>
            <div class="request-row"
              data-request-id="<?= h($requestLabel) ?>"
              data-resident="<?= h((string)($r['full_name'] ?? 'N/A')) ?>"
              data-document="<?= h((string)($r['service_name'] ?? '-')) ?>"
              data-status="<?= h(strtolower((string)$r['status'])) ?>"
              data-date="<?= h($dateLabel) ?>"
              data-datetime-full="<?= h($dateTimeFull) ?>"
              data-fee="₱<?= h((string)($r['fee'] ?? '0.00')) ?>"
              data-payment="<?= h((string)($r['payment_method'] ?? '-')) ?>"
              data-reference-number="<?= h((string)($r['payment_reference'] ?? '-')) ?>"
              data-requirements='<?= h(json_encode($requirements)) ?>'
              data-row-id="<?= (int)$r['id'] ?>">
              <div class="request-cell request-id"><?= h($requestLabel) ?></div>
              <div class="request-cell resident-name"><?= h((string)($r['full_name'] ?? 'N/A')) ?></div>
              <div class="request-cell document-name"><?= h((string)($r['service_name'] ?? '-')) ?></div>
              <div class="request-cell request-date"><?= h($dateLabel) ?></div>
              <div class="request-cell fee">₱<?= h((string)($r['fee'] ?? '0.00')) ?></div>
              <div class="request-cell payment"><?= h((string)($r['payment_method'] ?? '-')) ?></div>
              <div class="request-cell status-cell"><img src="<?= h(request_status_image((string)$r['status'])) ?>" alt="<?= h((string)$r['status']) ?>" class="status-badge-img"></div>
              <div class="request-cell manage-cell"><button type="button" class="manage-btn">Manage</button></div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="page-footer"><span><strong>CitiServe</strong> © 2026. All rights reserved.</span></div>
  </div>
</div>

<div class="manage-request-modal" id="manageRequestModal">
  <div class="manage-request-box">
    <button type="button" class="manage-request-close" id="manageRequestClose">×</button>
    <div class="manage-request-title">Manage Request - <span id="manageRequestId">-</span></div>
    <div class="manage-request-divider"></div>
    <div class="manage-request-body">
      <div class="manage-top-grid">
        <div class="manage-top-col">
          <div class="manage-detail-row"><div class="manage-detail-label">Resident</div><div class="manage-detail-value" id="manageResident">-</div></div>
          <div class="manage-detail-row"><div class="manage-detail-label">Fee</div><div class="manage-detail-value" id="manageFee">-</div></div>
          <div class="manage-detail-row"><div class="manage-detail-label">Reference No.</div><div class="manage-detail-value" id="manageReference">-</div></div>
        </div>
        <div class="manage-top-col">
          <div class="manage-detail-row"><div class="manage-detail-label">Document</div><div class="manage-detail-value" id="manageDocument">-</div></div>
          <div class="manage-detail-row"><div class="manage-detail-label">Payment Method</div><div class="manage-detail-value" id="managePayment">-</div></div>
          <div class="manage-detail-row"><div class="manage-detail-label">Date Submitted</div><div class="manage-detail-value" id="manageDate">-</div></div>
        </div>
      </div>
      <div class="manage-requirements-box" id="manageRequirementsBox"></div>
      <div class="manage-field">
        <label>Update Status</label>
        <div class="manage-status-filter" id="manageStatusFilter">
          <button type="button" class="manage-status-box" id="manageStatusBox"><span id="manageSelectedStatus">received</span><span id="manageArrow">▾</span></button>
          <div class="manage-status-dropdown" id="manageStatusDropdown">
            <?php foreach ($allowedStatuses as $st): ?>
              <div class="manage-status-option" data-value="<?= h($st) ?>"><?= h(ucfirst($st)) ?></div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <div class="manage-field"><label>Staff Notes <span>(Optional)</span></label><textarea id="manageStaffNotes" placeholder="Add internal notes or reason for status change..."></textarea></div>
      <div class="manage-actions">
        <button type="button" class="manage-img-btn" id="manageCancelBtn"><img src="/CitiServe/frontend/admin_dashboard/images/docu_request_cancel.png" alt="Close"></button>
        <button type="button" class="manage-img-btn" id="manageUpdateBtn"><img src="/CitiServe/frontend/admin_dashboard/images/docu_request_update.png" alt="Update Status"></button>
      </div>
      <form method="post" id="manageUpdateForm" style="display:none;">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="update_status">
        <input type="hidden" name="request_id" id="manageRequestIdInput" value="">
        <input type="hidden" name="new_status" id="manageNewStatusInput" value="">
        <input type="hidden" name="notes" id="manageNotesInput" value="">
      </form>
      <form method="post" id="manageDeleteForm" style="margin-top:4px;">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="request_id" id="manageDeleteRequestId" value="">
        <button type="submit" class="manage-btn" onclick="return confirm('Delete this request? This action cannot be undone.');">Delete Request</button>
      </form>
    </div>
  </div>
</div>

<script src="/CitiServe/frontend/admin_dashboard/JS/admin_document_request.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  let activeRowId = 0;
  document.querySelectorAll('.request-row .manage-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const row = btn.closest('.request-row');
      activeRowId = parseInt(row.getAttribute('data-row-id') || '0', 10);
      document.getElementById('manageRequestIdInput').value = activeRowId;
      document.getElementById('manageDeleteRequestId').value = activeRowId;
    });
  });
  document.getElementById('manageUpdateBtn').addEventListener('click', function () {
    if (!activeRowId) return;
    document.getElementById('manageRequestIdInput').value = activeRowId;
    document.getElementById('manageNewStatusInput').value = (document.getElementById('manageSelectedStatus').textContent || '').trim().toLowerCase();
    document.getElementById('manageNotesInput').value = document.getElementById('manageStaffNotes').value || '';
    document.getElementById('manageUpdateForm').submit();
  });
});
</script>
</body>
</html>
