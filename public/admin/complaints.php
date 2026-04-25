<?php
// Don't show errors on the page (for security), but still log them
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Include all the files we need
require_once __DIR__ . '/../../app/helpers/auth.php';
require_once __DIR__ . '/../../app/helpers/csrf.php';
require_once __DIR__ . '/../../app/core/CitiServeData.php';

// Make sure the user is an admin or staff
$admin = require_admin();

$data = new CitiServeData();
$db = $data->getPdo();

// These are all the valid complaint statuses
$allowedStatuses = ['submitted', 'under_review', 'in_progress', 'resolved', 'rejected'];
$deletableStatuses = ['submitted', 'under_review', 'in_progress'];

// Variables for success and error messages
$message = '';
$error = '';

// Check if the form was submitted (admin updating a complaint status)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify the CSRF token
    csrf_verify_or_die();

    // Get the complaint ID and new status from the form
    $action = isset($_POST['action']) ? trim((string)$_POST['action']) : 'update_status';

    $complaintId = 0;
    if (isset($_POST['complaint_id'])) {
        $complaintId = (int)$_POST['complaint_id'];
    }

    if ($action === 'delete') {
        if ($complaintId <= 0) {
            $error = 'Invalid complaint delete request.';
        } else {
            $current = $data->findComplaintByIdWithOwner($complaintId);
            if (!$current) {
                $error = 'Complaint not found.';
            } elseif (!in_array((string)$current['status'], $deletableStatuses, true)) {
                $error = 'Complaint can no longer be deleted in its current status.';
            } else {
                $db->beginTransaction();
                try {
                    $data->deleteComplaintById($complaintId);
                    $db->commit();
                    $message = "Complaint #{$complaintId} deleted.";
                } catch (Throwable $e) {
                    if ($db->inTransaction()) {
                        $db->rollBack();
                    }
                    $error = 'Failed to delete complaint.';
                }
            }
        }
    } else {
        $newStatus = '';
        if (isset($_POST['new_status'])) {
            $newStatus = trim($_POST['new_status']);
        }

        $notes = '';
        if (isset($_POST['notes'])) {
            $notes = trim($_POST['notes']);
        }

        // Validate the input
        if ($complaintId <= 0 || !in_array($newStatus, $allowedStatuses, true)) {
            $error = 'Invalid complaint status update request.';
        } else {
            // Find the current complaint
            $current = $data->findComplaintById($complaintId);

            if (!$current) {
                $error = 'Complaint not found.';
            } elseif ($current['status'] === $newStatus) {
                $error = 'Status is already set to that value.';
            } else {
                // Use a transaction so both updates happen together (or neither)
                $db->beginTransaction();
                try {
                    // Update the complaint status
                    $data->updateComplaintStatus($complaintId, $newStatus);

                    // Save the status change in the history table
                    $sql = "
                        INSERT INTO status_history (entity_type, entity_id, old_status, new_status, changed_by, notes)
                        VALUES ('complaint', ?, ?, ?, ?, ?)
                    ";
                    $stmt = $db->prepare($sql);

                    // Set notes to null if empty
                    $notesForDb = null;
                    if ($notes !== '') {
                        $notesForDb = $notes;
                    }

                    $stmt->execute([
                        $complaintId,
                        $current['status'],
                        $newStatus,
                        (int)$admin['id'],
                        $notesForDb
                    ]);

                    if (!empty($current['user_id'])) {
                        $data->createNotification(
                            (int)$current['user_id'],
                            'Complaint Update',
                            "Your complaint #{$complaintId} status is now '{$newStatus}'.",
                            '/CitiServe/public/my_complaints.php'
                        );
                    }

                    // If everything worked, commit the transaction
                    $db->commit();
                    $message = "Complaint #{$complaintId} updated to '{$newStatus}'.";
                } catch (Throwable $e) {
                    // If something went wrong, undo everything
                    if ($db->inTransaction()) {
                        $db->rollBack();
                    }
                    $error = 'Failed to update complaint.';
                }
            }
        }
    }
}

// Get all complaints to display in the table
$rows = $data->getAllComplaints();

$receivedCount = 0;
$processingCount = 0;
$anonymousCount = 0;
foreach ($rows as $r) {
    $status = strtolower((string)$r['status']);
    if ($status === 'submitted') {
        $receivedCount++;
    }
    if ($status === 'under_review' || $status === 'in_progress') {
        $processingCount++;
    }
    if ((int)$r['is_anonymous'] === 1) {
        $anonymousCount++;
    }
}

function h($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function complaint_status_image($status)
{
    $status = strtolower((string)$status);
    return match ($status) {
        'submitted' => '/CitiServe/frontend/my_resident/images/my_complaint_received.png',
        'under_review', 'in_progress' => '/CitiServe/frontend/my_resident/images/my_complaint_processing.png',
        'resolved' => '/CitiServe/frontend/my_resident/images/my_complaint_resolved.png',
        'rejected' => '/CitiServe/frontend/my_resident/images/my_complaint_rejected.png',
        default => '/CitiServe/frontend/my_resident/images/my_complaint_received.png',
    };
}

function complaint_type_image($isAnonymous)
{
    return ((int)$isAnonymous === 1)
        ? '/CitiServe/frontend/admin_dashboard/images/complaint_anonymous_type.png'
        : '/CitiServe/frontend/admin_dashboard/images/complaint_identified_type.png';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Complaints - CitiServe</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/CitiServe/frontend/admin_dashboard/CSS/admin_complaints.css">
</head>
<body>

<div class="design-strip left"><img src="/CitiServe/frontend/admin_dashboard/images/dashboard_design.png" alt=""></div>
<div class="design-strip right"><img src="/CitiServe/frontend/admin_dashboard/images/dashboard_design.png" alt=""></div>

<nav class="navbar">
  <a href="/CitiServe/public/admin/dashboard.php" class="navbar-logo"><img src="/CitiServe/frontend/admin_dashboard/images/logo_pink.png" alt="CitiServe"></a>
  <div class="navbar-nav admin-nav">
    <a href="/CitiServe/public/admin/dashboard.php" class="nav-item"><span class="nav-text">Dashboard</span></a>
    <a href="/CitiServe/public/admin/requests.php" class="nav-item"><span class="nav-text">Document Requests</span></a>
    <a href="/CitiServe/public/admin/complaints.php" class="nav-item active"><span class="nav-text">Complaints</span></a>
    <div class="nav-item has-dropdown">
      <span class="nav-text">User Management</span><span class="nav-chevron">⏷</span>
      <div class="nav-dropdown"><a href="/CitiServe/public/admin/users.php" class="nav-dropdown-item">Residents</a></div>
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
      <div class="profile-panel-top"><div class="profile-panel-fullname"><?= h($admin['full_name']) ?></div><div class="profile-panel-subtext"><?= h(ucfirst((string)$admin['role'])) ?> • Brgy. Kalayaan</div></div>
      <a href="/CitiServe/public/logout.php" class="profile-panel-item logout"><img src="/CitiServe/frontend/admin_dashboard/images/logout.png" class="profile-panel-icon2" alt=""><span>Logout</span></a>
    </div>
  </div>
</nav>

<div class="page-body">
  <div class="admin-doc-card">
    <div class="page-head">
      <h1 class="page-title">Complaint Management</h1>
      <p class="page-subtitle">Review and respond to all community complaints.</p>
    </div>
    <?php if ($message): ?><p style="color:#15803d;margin-top:10px;"><?= h($message) ?></p><?php endif; ?>
    <?php if ($error): ?><p style="color:#b91c1c;margin-top:10px;"><?= h($error) ?></p><?php endif; ?>

    <div class="admin-summary-row">
      <div class="summary-box summary-received"><div class="summary-number"><?= (int)$receivedCount ?></div><div class="summary-label">Received</div><img src="/CitiServe/frontend/admin_dashboard/images/complaint_faded_received.png" class="summary-icon1"></div>
      <div class="summary-box summary-processing"><div class="summary-number"><?= (int)$processingCount ?></div><div class="summary-label">Processing</div><img src="/CitiServe/frontend/admin_dashboard/images/complaint_faded_processing.png" class="summary-icon2"></div>
      <div class="summary-box summary-anonymous"><div class="summary-number"><?= (int)$anonymousCount ?></div><div class="summary-label">Anonymous</div><img src="/CitiServe/frontend/admin_dashboard/images/complaint_faded_anonymous.png" class="summary-icon3"></div>
    </div>

    <div class="toolbar">
      <div class="toolbar-left">
        <div class="search-wrap"><img src="/CitiServe/frontend/admin_dashboard/images/search_icon.png" alt="" class="search-icon"><input type="text" id="searchInput" placeholder="Search by resident, category, or complaint ID..."></div>
        <button type="button" class="clear-btn" id="clearBtn"><img src="/CitiServe/frontend/admin_dashboard/images/my_request_clear.png" alt="Clear"></button>
      </div>
      <div class="toolbar-right">
        <div class="custom-filter" id="statusFilter">
          <button type="button" class="filter-box" id="filterBox"><span id="selectedStatusText">All</span><span class="filter-arrow">▾</span></button>
          <div class="filter-dropdown" id="filterDropdown">
            <div class="filter-option active" data-value="all">All</div>
            <div class="filter-option" data-value="submitted">Received</div>
            <div class="filter-option" data-value="under_review">Under Review</div>
            <div class="filter-option" data-value="in_progress">In Progress</div>
          </div>
        </div>
      </div>
    </div>

    <div class="requests-table-card">
      <div class="requests-table-head">
        <div>Complaint ID</div><div>Reporter</div><div>Category</div><div>Date</div><div>Type</div><div>Status</div><div></div>
      </div>
      <?php if (empty($rows)): ?>
        <div class="requests-empty"><img src="/CitiServe/frontend/admin_dashboard/images/recent_complaint_main.png" alt="" class="empty-icon"><div class="empty-text">No complaints submitted yet</div></div>
      <?php else: ?>
        <div class="requests-table-body" id="requestsTableBody">
          <?php foreach ($rows as $r): ?>
            <?php
            $createdTs = !empty($r['created_at']) ? strtotime((string)$r['created_at']) : false;
            $dateLabel = $createdTs ? date('M j, Y', $createdTs) : (string)$r['created_at'];
            $dateTimeFull = $createdTs ? date('m/d/Y, g:i:s A', $createdTs) : (string)$r['created_at'];
            $complaintLabel = 'CMP-' . str_pad((string)((int)$r['id']), 10, '0', STR_PAD_LEFT);
            try {
                $evidence = $data->getEvidenceByComplaintId((int)$r['id']);
            } catch (Throwable $e) {
                $evidence = [];
            }
            $evidenceName = !empty($evidence) ? (string)($evidence[0]['file_name'] ?: 'evidence') : '';
            ?>
            <div class="request-row"
              data-complaint-id="<?= h($complaintLabel) ?>"
              data-reporter="<?= h((int)$r['is_anonymous'] === 1 ? 'Anonymous' : (string)($r['full_name'] ?? 'N/A')) ?>"
              data-category="<?= h((string)($r['category_name'] ?? '-')) ?>"
              data-date="<?= h($dateLabel) ?>"
              data-datetime-full="<?= h($dateTimeFull) ?>"
              data-type="<?= h((int)$r['is_anonymous'] === 1 ? 'Anonymous' : 'Identified') ?>"
              data-status="<?= h((string)$r['status']) ?>"
              data-contact="<?= h((string)($r['email'] ?? '-')) ?>"
              data-description="<?= h((string)($r['description'] ?? '')) ?>"
              data-location-text="<?= h((string)($r['location'] ?? '')) ?>"
              data-map-query="<?= h((string)($r['location'] ?? 'Barangay Kalayaan, Angono, Rizal')) ?>"
              data-evidence="<?= h($evidenceName) ?>"
              data-row-id="<?= (int)$r['id'] ?>">
              <div class="request-cell request-id"><?= h($complaintLabel) ?></div>
              <div class="request-cell resident-name <?= (int)$r['is_anonymous'] === 1 ? 'anonymous-reporter' : '' ?>"><?= h((int)$r['is_anonymous'] === 1 ? 'Anonymous' : (string)($r['full_name'] ?? 'N/A')) ?></div>
              <div class="request-cell document-name"><?= h((string)($r['category_name'] ?? '-')) ?></div>
              <div class="request-cell request-date"><?= h($dateLabel) ?></div>
              <div class="request-cell type-cell"><img src="<?= h(complaint_type_image((int)$r['is_anonymous'])) ?>" class="type-badge-img" alt=""></div>
              <div class="request-cell status-cell"><img src="<?= h(complaint_status_image((string)$r['status'])) ?>" class="status-badge-img" alt=""></div>
              <div class="request-cell manage-cell"><button type="button" class="manage-btn">Manage</button></div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="page-footer"><span><strong>CitiServe</strong> © 2026. All rights reserved.</span></div>
  </div>
</div>

<div class="manage-complaint-modal" id="manageComplaintModal">
  <div class="manage-complaint-box" id="manageComplaintBox">
    <button type="button" class="manage-complaint-close" id="manageComplaintClose">×</button>
    <div class="manage-complaint-title">Manage Complaint - <span id="manageComplaintId">-</span></div>
    <div class="manage-complaint-divider"></div>
    <div class="manage-complaint-scroll">
      <div class="complaint-top-grid">
        <div class="complaint-top-col">
          <div class="complaint-detail-row compact"><div class="complaint-detail-label">Complaint ID</div><div class="complaint-detail-value left" id="manageComplaintIdValue"></div></div>
          <div class="complaint-detail-row compact"><div class="complaint-detail-label">Reporter</div><div class="complaint-detail-value left" id="manageComplaintReporter"></div></div>
        </div>
        <div class="complaint-top-col">
          <div class="complaint-detail-row compact"><div class="complaint-detail-label">Category</div><div class="complaint-detail-value left" id="manageComplaintCategory"></div></div>
          <div class="complaint-detail-row compact"><div class="complaint-detail-label">Date Submitted</div><div class="complaint-detail-value left" id="manageComplaintDate"></div></div>
        </div>
      </div>
      <div class="complaint-section"><div class="complaint-detail-label">Description</div><div class="complaint-description-box" id="manageComplaintDescription"></div></div>
      <div class="complaint-section"><div class="complaint-detail-label">Location</div><div class="complaint-location-text" id="manageComplaintLocationText"></div><div class="complaint-map-wrap"><iframe id="manageComplaintMap" class="complaint-map-frame" src="" loading="lazy"></iframe></div></div>
      <div class="complaint-section complaint-evidence-section" id="manageComplaintEvidenceSection"><div class="complaint-detail-label">Evidence</div><div class="complaint-evidence-box"><div class="complaint-evidence-file" id="manageComplaintEvidence"></div></div></div>
      <div class="manage-field">
        <label>Update Status</label>
        <div class="manage-status-filter" id="manageComplaintStatusFilter">
          <button type="button" class="manage-status-box" id="manageComplaintStatusBox"><span id="manageComplaintSelectedStatus">submitted</span><span>▾</span></button>
          <div class="manage-status-dropdown" id="manageComplaintStatusDropdown">
            <?php foreach ($allowedStatuses as $st): ?>
              <div class="manage-status-option" data-value="<?= h($st) ?>"><?= h(ucfirst(str_replace('_', ' ', $st))) ?></div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <div class="manage-field"><label>Action Notes <span>(Optional)</span></label><textarea id="manageComplaintNotes" placeholder="Describe the action taken or reason for status change..."></textarea></div>
      <div class="manage-actions">
        <button type="button" class="manage-img-btn" id="manageComplaintCancelBtn"><img src="/CitiServe/frontend/admin_dashboard/images/close_manage_complaint.png" alt="Close"></button>
        <button type="button" class="manage-img-btn" id="manageComplaintUpdateBtn"><img src="/CitiServe/frontend/admin_dashboard/images/update_status_manage_complaint.png" alt="Update Status"></button>
      </div>
      <form method="post" id="manageComplaintUpdateForm" style="display:none;">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="update_status">
        <input type="hidden" name="complaint_id" id="manageComplaintIdInput" value="">
        <input type="hidden" name="new_status" id="manageComplaintStatusInput" value="">
        <input type="hidden" name="notes" id="manageComplaintNotesInput" value="">
      </form>
      <form method="post" style="margin-top:4px;">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="complaint_id" id="manageDeleteComplaintId" value="">
        <button type="submit" class="manage-btn" onclick="return confirm('Delete this complaint? This action cannot be undone.');">Delete Complaint</button>
      </form>
    </div>
  </div>
</div>

<script src="/CitiServe/frontend/admin_dashboard/JS/admin_complaints.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  let activeId = 0;
  document.querySelectorAll('.request-row .manage-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const row = btn.closest('.request-row');
      activeId = parseInt(row.getAttribute('data-row-id') || '0', 10);
      document.getElementById('manageComplaintIdInput').value = activeId;
      document.getElementById('manageDeleteComplaintId').value = activeId;
    });
  });
  document.getElementById('manageComplaintUpdateBtn').addEventListener('click', function () {
    if (!activeId) return;
    document.getElementById('manageComplaintIdInput').value = activeId;
    document.getElementById('manageComplaintStatusInput').value = (document.getElementById('manageComplaintSelectedStatus').textContent || '').trim().toLowerCase().replace(/ /g, '_');
    document.getElementById('manageComplaintNotesInput').value = document.getElementById('manageComplaintNotes').value || '';
    document.getElementById('manageComplaintUpdateForm').submit();
  });
});
</script>
</body>
</html>
