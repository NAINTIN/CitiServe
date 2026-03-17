<?php
// Admin-only protected residency proof viewer
require_once __DIR__ . '/../../app/helpers/auth.php';
require_once __DIR__ . '/../../app/core/Database.php';

define('RESIDENCY_PROOF_STORAGE_PREFIX', 'storage/residency_proofs/');
define('RESIDENCY_PROOF_FILENAME_HEX_LENGTH', 32);

require_admin();

$userId = 0;
if (isset($_GET['user_id'])) {
    $userId = (int)$_GET['user_id'];
}

if ($userId <= 0) {
    http_response_code(400);
    echo 'Invalid user.';
    exit;
}

$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("
    SELECT residency_proof_path
    FROM users
    WHERE id = ? AND role = 'resident'
    LIMIT 1
");
$stmt->execute([$userId]);
$row = $stmt->fetch();

if (!$row || empty($row['residency_proof_path'])) {
    http_response_code(404);
    echo 'Proof not found.';
    exit;
}

$relativePath = (string)$row['residency_proof_path'];
if (strpos($relativePath, RESIDENCY_PROOF_STORAGE_PREFIX) !== 0) {
    http_response_code(400);
    echo 'Invalid proof path.';
    exit;
}

$normalizedRelativePath = ltrim($relativePath, '/');
if (strpos($normalizedRelativePath, '..') !== false || strpos($normalizedRelativePath, '//') !== false) {
    http_response_code(400);
    echo 'Invalid proof path.';
    exit;
}

$pathPattern = '/^storage\/residency_proofs\/[a-f0-9]{' . RESIDENCY_PROOF_FILENAME_HEX_LENGTH . '}\.(jpg|png)$/';
if (!preg_match($pathPattern, $relativePath)) {
    http_response_code(400);
    echo 'Invalid proof file.';
    exit;
}

$baseDir = realpath(__DIR__ . '/../../storage/residency_proofs');
if ($baseDir === false) {
    http_response_code(404);
    echo 'Proof storage is unavailable.';
    exit;
}

$candidatePath = __DIR__ . '/../../' . $normalizedRelativePath;
$filePath = realpath($candidatePath);
if ($filePath === false || !is_file($filePath)) {
    http_response_code(404);
    echo 'Proof file not found.';
    exit;
}

$normalizedFilePath = str_replace('\\', '/', $filePath);
$normalizedBaseDir = rtrim(str_replace('\\', '/', $baseDir), '/');
if (strpos($normalizedFilePath, $normalizedBaseDir . '/') !== 0) {
    http_response_code(404);
    echo 'Proof file not found.';
    exit;
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = (string)$finfo->file($filePath);
if ($mime !== 'image/jpeg' && $mime !== 'image/png') {
    http_response_code(415);
    echo 'Unsupported proof type.';
    exit;
}

header('Content-Type: ' . $mime);
header('Content-Length: ' . (string)filesize($filePath));
header('X-Content-Type-Options: nosniff');
readfile($filePath);
