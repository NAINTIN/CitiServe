<?php
require_once __DIR__ . '/../app/helpers/auth.php';

require_resident();

$serviceId = isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0;
$target = '/CitiServe/public/request_select.php';
if ($serviceId > 0) {
    $target .= '?service_id=' . $serviceId;
}

header('Location: ' . $target);
exit;
