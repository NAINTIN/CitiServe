<?php
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/core/CitiServeData.php';
require_once __DIR__ . '/../app/helpers/document_request.php';

$user = require_login();
$data = new CitiServeData();
$services = $data->getAllActiveDocumentServices();
$definitions = document_request_definitions();

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
    <title>Document Services</title>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/CitiServe/frontend/document_request/css/document.css">
    <style>
        .top-links { margin-bottom: 12px; font-size: 13px; color: #6B7280; }
        .top-links a { color: #6B7280; text-decoration: none; margin-right: 12px; }
        .top-links a:hover { color: #E8265E; }
        .status-note { margin-bottom: 10px; color: #6B7280; font-size: 13px; }
        .empty-box { color: #B91C1C; background: #FEF2F2; border: 1px solid #FECACA; border-radius: 10px; padding: 12px; margin: 10px 0 15px; }
    </style>
</head>
<body>
<div class="content-area">
    <div class="top-links">
        <a href="/CitiServe/public/index.php">Home</a>
        <a href="/CitiServe/public/my_requests.php">My Requests</a>
        <a href="/CitiServe/public/logout.php">Logout</a>
    </div>

    <div class="status-note">Logged in as: <?= h($user['full_name']) ?> (<?= h($user['role']) ?>)</div>
    <div class="form-breadcrumb" id="form-breadcrumb"></div>

    <div class="page-header">
        <h1>Available Document Services</h1>
        <p>Select and review active services offered by the barangay.</p>
    </div>

    <?php if (empty($services)): ?>
        <div class="empty-box">
            <p>No active document services available.</p>
            <p>Ask admin to add active rows in <code>document_services</code>.</p>
        </div>
    <?php endif; ?>

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

                    <?php if (($user['role'] ?? '') === 'resident'): ?>
                        <a href="/CitiServe/public/request_select.php?service_id=<?= (int)$s['id'] ?>">
                            <img src="/CitiServe/frontend/document_request/images/docu-view-deets.png" class="view-btn" alt="Request">
                        </a>
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
  { label: "Services", href: null }
];
(function renderBreadcrumb() {
  const el = document.getElementById("form-breadcrumb");
  el.innerHTML = trail.map((item, i) => {
    const sep = i > 0 ? `<span class="form-sep">></span>` : "";
    return item.href
      ? `${sep}<a href="${item.href}">${item.label}</a>`
      : `${sep}<span class="form-active">${item.label}</span>`;
  }).join("");
})();
</script>
</body>
</html>
