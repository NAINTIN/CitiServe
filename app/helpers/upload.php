<?php

function saveUploadedFileWithRules($file, $targetDirAbs, $targetDirDbPrefix, $allowedTypes)
{
    $uploadError = isset($file['error']) ? $file['error'] : UPLOAD_ERR_NO_FILE;
    if ($uploadError !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed.');
    }

    $maxBytes = 5 * 1024 * 1024;
    $fileSize = isset($file['size']) ? (int)$file['size'] : 0;
    if ($fileSize <= 0 || $fileSize > $maxBytes) {
        throw new RuntimeException('File too large. Max 5MB.');
    }

    $tempFilePath = isset($file['tmp_name']) ? $file['tmp_name'] : '';
    if (!is_uploaded_file($tempFilePath)) {
        throw new RuntimeException('Invalid upload source.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($tempFilePath);

    if (!isset($allowedTypes[$mimeType])) {
        throw new RuntimeException('Invalid file type.');
    }

    $safeExtension = $allowedTypes[$mimeType];

    if (!is_dir($targetDirAbs)) {
        $created = mkdir($targetDirAbs, 0775, true);
        if (!$created && !is_dir($targetDirAbs)) {
            throw new RuntimeException('Failed to create upload directory.');
        }
    }

    $newFileName = bin2hex(random_bytes(16)) . '.' . $safeExtension;
    $fullDestination = rtrim($targetDirAbs, '/\\') . DIRECTORY_SEPARATOR . $newFileName;

    if (!move_uploaded_file($tempFilePath, $fullDestination)) {
        throw new RuntimeException('Failed to store uploaded file.');
    }

    return rtrim($targetDirDbPrefix, '/\\') . '/' . $newFileName;
}

// Backward-compatible existing helper.
function savePaymentProof($file, $targetDirAbs, $targetDirDbPrefix = 'uploads/payment_proofs')
{
    return saveUploadedFileWithRules(
        $file,
        $targetDirAbs,
        $targetDirDbPrefix,
        [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/pdf' => 'pdf',
        ]
    );
}

function saveDocumentRequestFile($file, $targetDirAbs, $targetDirDbPrefix = 'uploads/request_files')
{
    return saveUploadedFileWithRules(
        $file,
        $targetDirAbs,
        $targetDirDbPrefix,
        [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/pdf' => 'pdf',
        ]
    );
}

function savePaymentProofScreenshot($file, $targetDirAbs, $targetDirDbPrefix = 'uploads/payment_proofs')
{
    return saveUploadedFileWithRules(
        $file,
        $targetDirAbs,
        $targetDirDbPrefix,
        [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
        ]
    );
}
