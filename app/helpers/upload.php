<?php
// This function saves an uploaded payment proof file to the server.
// It checks the file type and size, then saves it with a random filename.
// Returns the relative path to store in the database.
function savePaymentProof($file, $targetDirAbs, $targetDirDbPrefix = 'uploads/payment_proofs')
{
    // Step 1: Check if the upload was successful
    $uploadError = isset($file['error']) ? $file['error'] : UPLOAD_ERR_NO_FILE;
    if ($uploadError !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed.');
    }

    // Step 2: Check the file size (max 5 MB)
    $maxBytes = 5 * 1024 * 1024; // 5 MB in bytes
    $fileSize = isset($file['size']) ? $file['size'] : 0;
    if ($fileSize <= 0 || $fileSize > $maxBytes) {
        throw new RuntimeException('File too large. Max 5MB.');
    }

    // Step 3: Make sure the file is a real upload (not someone trying to trick us)
    $tempFilePath = isset($file['tmp_name']) ? $file['tmp_name'] : '';
    if (!is_uploaded_file($tempFilePath)) {
        throw new RuntimeException('Invalid upload source.');
    }

    // Step 4: Check the file type using MIME detection
    // This checks what the file actually is, not just the extension
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($tempFilePath);

    // Only allow these file types
    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'application/pdf' => 'pdf',
    ];

    if (!isset($allowedTypes[$mimeType])) {
        throw new RuntimeException('Invalid file type. Only JPG, PNG, PDF allowed.');
    }

    // Step 5: Get the safe file extension based on the MIME type
    $safeExtension = $allowedTypes[$mimeType];

    // Double-check: compare the original file extension with what MIME says
    $originalName = isset($file['name']) ? $file['name'] : 'file';
    $originalExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if ($originalExtension && $originalExtension !== $safeExtension) {
        // If they don't match, trust the MIME type (it's more reliable)
        $originalExtension = $safeExtension;
    }

    // Step 6: Create the upload directory if it doesn't exist
    if (!is_dir($targetDirAbs)) {
        $created = mkdir($targetDirAbs, 0775, true);
        if (!$created) {
            throw new RuntimeException('Failed to create upload directory.');
        }
    }

    // Step 7: Generate a random filename so files don't overwrite each other
    $randomName = bin2hex(random_bytes(16));
    $newFileName = $randomName . '.' . $safeExtension;
    $fullDestination = rtrim($targetDirAbs, '/\\') . DIRECTORY_SEPARATOR . $newFileName;

    // Step 8: Move the uploaded file from the temp folder to our uploads folder
    if (!move_uploaded_file($tempFilePath, $fullDestination)) {
        throw new RuntimeException('Failed to store uploaded file.');
    }

    // Step 9: Return the relative path (this is what we save in the database)
    $relativePath = rtrim($targetDirDbPrefix, '/\\') . '/' . $newFileName;
    return $relativePath;
}