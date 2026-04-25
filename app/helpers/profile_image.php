<?php

function getUserProfileImagePublicPath(int $userId): ?string
{
    $baseDir = __DIR__ . '/../../public/uploads/profile_images';
    $basePublic = '/CitiServe/public/uploads/profile_images';
    $candidates = ['jpg', 'jpeg', 'png'];

    foreach ($candidates as $ext) {
        $abs = $baseDir . '/user_' . $userId . '.' . $ext;
        if (is_file($abs)) {
            return $basePublic . '/user_' . $userId . '.' . $ext . '?v=' . filemtime($abs);
        }
    }

    return null;
}

function saveUserProfileImage(array $file, int $userId): string
{
    $uploadErr = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($uploadErr === UPLOAD_ERR_NO_FILE) {
        throw new RuntimeException('Please choose a profile image.');
    }
    if ($uploadErr === UPLOAD_ERR_INI_SIZE || $uploadErr === UPLOAD_ERR_FORM_SIZE) {
        throw new RuntimeException('Profile image exceeds the maximum file size (5MB).');
    }
    if ($uploadErr !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Profile image upload failed.');
    }

    $tmp = (string)($file['tmp_name'] ?? '');
    if ($tmp === '' || !is_uploaded_file($tmp)) {
        throw new RuntimeException('Uploaded profile image is invalid.');
    }

    $size = (int)($file['size'] ?? 0);
    if ($size <= 0 || $size > 5 * 1024 * 1024) {
        throw new RuntimeException('Profile image must be up to 5MB.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = (string)$finfo->file($tmp);
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    ];
    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Profile image must be JPG or PNG.');
    }

    $ext = $allowed[$mime];
    $uploadDir = __DIR__ . '/../../public/uploads/profile_images';
    if (!is_dir($uploadDir)) {
        $created = mkdir($uploadDir, 0775, true);
        if (!$created && !is_dir($uploadDir)) {
            throw new RuntimeException('Unable to create profile image directory.');
        }
    }

    foreach (['jpg', 'jpeg', 'png'] as $oldExt) {
        $oldFile = $uploadDir . '/user_' . $userId . '.' . $oldExt;
        if (is_file($oldFile)) {
            @unlink($oldFile);
        }
    }

    $dest = $uploadDir . '/user_' . $userId . '.' . $ext;
    if (!move_uploaded_file($tmp, $dest)) {
        throw new RuntimeException('Failed to save uploaded profile image.');
    }

    return '/CitiServe/public/uploads/profile_images/user_' . $userId . '.' . $ext . '?v=' . time();
}

