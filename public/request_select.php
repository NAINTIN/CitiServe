<?php
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/core/CitiServeData.php';
require_once __DIR__ . '/../app/helpers/document_request.php';
require_once __DIR__ . '/../app/helpers/resident_navbar.php';

$user = require_resident();
$navCtx = build_resident_navbar_context((int)$user['id']);
$data = new CitiServeData();
$dbUser = $data->findUserById((int)$user['id']);
$isVerified = ($dbUser && (int)$dbUser->is_verified === 1);
$services = $data->getAllActiveDocumentServices();
$definitions = document_request_definitions();

$errors = [];
$preselectedServiceId = isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0;

if ($isVerified && $preselectedServiceId > 0) {
    $service = $data->findDocumentServiceById($preselectedServiceId);
    if ($service && (int)$service['is_active'] === 1 && isset($definitions[$service['name']])) {
        unset($_SESSION['document_request_draft']);
        header('Location: /CitiServe/public/request_form.php?service_id=' . $preselectedServiceId);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify_or_die();

    if (!$isVerified) {
        $errors[] = 'Your account must be verified before you can proceed with a document request.';
    } else {
        $serviceId = isset($_POST['service_id']) ? (int)$_POST['service_id'] : 0;
        if ($serviceId <= 0) {
            $errors[] = 'Please select a document type.';
        } else {
            $service = $data->findDocumentServiceById($serviceId);
            if (!$service || (int)$service['is_active'] !== 1) {
                $errors[] = 'Selected document service is not available.';
            } elseif (!isset($definitions[$service['name']])) {
                $errors[] = 'Selected document service is not yet supported in the new request flow.';
            } else {
                unset($_SESSION['document_request_draft']);
                header('Location: /CitiServe/public/request_form.php?service_id=' . $serviceId);
                exit;
            }
        }
    }
}

$cardVisuals = [
    'Barangay Business Clearance' => ['bg' => 'Barangay Business Clearance - Container (1).png', 'icon' => 'docu-bbc.png'],
    'Barangay Clearance' => ['bg' => 'Barangay Clearance - Container.png', 'icon' => 'docu-bc.png'],
    'Barangay ID' => ['bg' => 'Barangay ID - Container.png', 'icon' => 'docu-bid.png'],
    'Barangay Permit (Construction)' => ['bg' => 'Barangay Permit (Construction) - Container.png', 'icon' => 'docu-bp.png'],
    'Certificate of Indigency' => ['bg' => 'Certificate of Indigency - Container.png', 'icon' => 'docu-coi.png'],
    'Certificate of Residency' => ['bg' => 'Certificate of Residency - Container.png', 'icon' => 'docu-br.png'],
    'Solo Parent Certificate' => ['bg' => 'Solo Parent Certificate - Container.png', 'icon' => 'docu-spc.png'],
];

function h($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Document Request</title>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/CitiServe/frontend/dashboard/CSS/dashboard.css">
    <link rel="stylesheet" href="/CitiServe/frontend/document_request/css/document.css">
    <style>
        .error-box { color: #B91C1C; background: #FEF2F2; border: 1px solid #FECACA; border-radius: 10px; padding: 12px; margin-bottom: 14px; }
        .view-btn-wrap { display:block; width:100%; }
    </style>
</head>
<body>
<?php render_resident_navbar($navCtx, 'document'); ?>
<div class="content-area">
    <div class="form-breadcrumb" id="form-breadcrumb"></div>

    <div class="page-header">
        <h1>Barangay Document Requests</h1>
        <p>Select the document you need. Review requirements and fees before proceeding.</p>
    </div>
    <?php if (!$isVerified): ?>
        <div class="error-box">Your account is not yet verified. You can browse document details, but you must be verified before proceeding with a request.</div>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div class="error-box">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= h($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="reminders">
        <img src="/CitiServe/frontend/document_request/images/docu-yellowbg.png" alt="Reminders">
    </div>

    <div class="grid">
        <?php foreach ($services as $s): ?>
            <?php if (!isset($definitions[$s['name']])): continue; endif; ?>
            <?php
            $name = (string)$s['name'];
            $definition = $definitions[$name];
            $visual = $cardVisuals[$name] ?? ['bg' => 'Barangay Clearance - Container.png', 'icon' => 'docu-bc.png'];
            ?>
            <div class="card" style="background-image:url('/CitiServe/frontend/document_request/images/<?= h($visual['bg']) ?>')">
                <div class="card-content">
                    <div class="card-main">
                        <div class="card-top">
                            <img src="/CitiServe/frontend/document_request/images/<?= h($visual['icon']) ?>" class="card-icon" alt="">
                            <div>
                                <div class="card-title"><?= h($name) ?></div>
                                <div class="card-desc"><?= h((string)($s['description'] ?? '')) ?></div>
                            </div>
                        </div>

                        <div class="divider"></div>

                        <div class="card-meta">
                            <span><b>Fee:</b> <span class="price">₱<?= h((string)$s['price']) ?></span></span>
                            <span><b>Processing:</b> <span class="processing"><?= h((string)$s['processing_time_days']) ?> day(s)</span></span>
                            <span><b>Requirements:</b>
                                <ul>
                                    <?php foreach ((array)$definition['required_uploads'] as $upload): ?>
                                        <li><?= h((string)$upload['label']) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </span>
                        </div>
                    </div>

                    <?php if ($isVerified): ?>
                        <a class="view-btn-wrap" href="/CitiServe/public/request_form.php?service_id=<?= (int)$s['id'] ?>">
                            <img src="/CitiServe/frontend/document_request/images/docu-view-deets.png" class="view-btn" alt="Request">
                        </a>
                    <?php else: ?>
                        <span class="view-btn-wrap" style="opacity:.55; cursor:not-allowed;">
                            <img src="/CitiServe/frontend/document_request/images/docu-view-deets.png" class="view-btn" alt="Request">
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="footer">
            <img src="/CitiServe/frontend/document_request/images/docu-logo.png" class="footer-logo" alt="CitiServe">
            <div class="form-logo-text">
                <span class="logo-pink">CitiServe</span>
                <span class="logo-gray"> © 2026. All rights reserved.</span>
            </div>
        </div>
    </div>
</div>

<script>
const trail = [
  { label: "Document Requests", href: "/CitiServe/public/request_select.php" },
  { label: "Request Document", href: null }
];
(function renderBreadcrumb() {
  const el = document.getElementById("form-breadcrumb");
  el.innerHTML = trail.map((item, i) => {
    const isLast = i === trail.length - 1;
    const sep = i > 0 ? `<span class="form-sep">></span>` : "";
    if (isLast) return `${sep}<span class="form-active">${item.label}</span>`;
    return `${sep}<a href="${item.href}">${item.label}</a>`;
  }).join("");
})();
</script>
<script src="/CitiServe/frontend/dashboard/dashboard.js"></script>
</body>
</html>
