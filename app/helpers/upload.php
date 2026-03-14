<?php
function savePaymentProof(array $file, string $targetDirAbs, string $targetDirDbPrefix = 'uploads/payment_proofs'): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed.');
    }

    // 5 MB limit
    $maxBytes = 5 * 1024 * 1024;
    if (($file['size'] ?? 0) <= 0 || $file['size'] > $maxBytes) {
        throw new RuntimeException('File too large. Max 5MB.');
    }

    $tmp = $file['tmp_name'] ?? '';
    if (!is_uploaded_file($tmp)) {
        throw new RuntimeException('Invalid upload source.');
    }

    // MIME allowlist
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($tmp);
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'application/pdf' => 'pdf',
    ];
    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Invalid file type. Only JPG, PNG, PDF allowed.');
    }

    // Optional extension cross-check
    $origName = $file['name'] ?? 'file';
    $extFromName = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
    $safeExt = $allowed[$mime];
    if ($extFromName && $extFromName !== $safeExt) {
        // force trusted extension from MIME
        $extFromName = $safeExt;
    }

    if (!is_dir($targetDirAbs) && !mkdir($targetDirAbs, 0775, true)) {
        throw new RuntimeException('Failed to create upload directory.');
    }

    // Random filename
    $newBase = bin2hex(random_bytes(16));
    $newName = $newBase . '.' . $safeExt;
    $destAbs = rtrim($targetDirAbs, '/\\') . DIRECTORY_SEPARATOR . $newName;

    if (!move_uploaded_file($tmp, $destAbs)) {
        throw new RuntimeException('Failed to store uploaded file.');
    }

    // DB path (relative to /public)
    return rtrim($targetDirDbPrefix, '/\\') . '/' . $newName;
}