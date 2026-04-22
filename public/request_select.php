<?php
require_once __DIR__ . '/../app/helpers/auth.php';
require_once __DIR__ . '/../app/helpers/csrf.php';
require_once __DIR__ . '/../app/core/CitiServeData.php';
require_once __DIR__ . '/../app/helpers/document_request.php';

$user = require_resident();
$data = new CitiServeData();
$services = $data->getAllActiveDocumentServices();
$definitions = document_request_definitions();

$errors = [];
$preselectedServiceId = isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0;

if ($preselectedServiceId > 0) {
    $service = $data->findDocumentServiceById($preselectedServiceId);
    if ($service && (int)$service['is_active'] === 1 && isset($definitions[$service['name']])) {
        unset($_SESSION['document_request_draft']);
        header('Location: /CitiServe/public/request_form.php?service_id=' . $preselectedServiceId);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify_or_die();

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
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Select Document Request</title>
</head>
<body>
    <h2>Select Document Request</h2>
    <p>
        <a href="/CitiServe/public/dashboard.php">Dashboard</a> |
        <a href="/CitiServe/public/my_requests.php">My Requests</a> |
        <a href="/CitiServe/public/logout.php">Logout</a>
    </p>

    <?php if ($errors): ?>
        <div style="color:red;">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post">
        <?= csrf_field() ?>
        <label for="service_id">Document Type / Service</label><br>
        <select id="service_id" name="service_id" required>
            <option value="">-- Select Document Type --</option>
            <?php foreach ($services as $s): ?>
                <?php if (!isset($definitions[$s['name']])): continue; endif; ?>
                <option value="<?= (int)$s['id'] ?>">
                    <?= htmlspecialchars($s['name']) ?> - ₱<?= htmlspecialchars((string)$s['price']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <button type="submit">Continue</button>
    </form>
</body>
</html>
