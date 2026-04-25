<?php

function profile_details_storage_dir(): string
{
    return __DIR__ . '/../../storage/profile_details';
}

function loadUserProfileDetails(int $userId): array
{
    $defaults = [
        'first_name' => '',
        'middle_name' => '',
        'last_name' => '',
        'suffix' => '',
        'dob' => '',
        'civil_status' => '',
        'citizenship' => '',
        'gender' => '',
    ];

    $file = profile_details_storage_dir() . '/user_' . $userId . '.json';
    if (!is_file($file)) {
        return $defaults;
    }

    $raw = @file_get_contents($file);
    if ($raw === false || $raw === '') {
        return $defaults;
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return $defaults;
    }

    return array_merge($defaults, $decoded);
}

function saveUserProfileDetails(int $userId, array $details): void
{
    $dir = profile_details_storage_dir();
    if (!is_dir($dir)) {
        $created = mkdir($dir, 0775, true);
        if (!$created && !is_dir($dir)) {
            throw new RuntimeException('Unable to create profile details directory.');
        }
    }

    $payload = [
        'first_name' => (string)($details['first_name'] ?? ''),
        'middle_name' => (string)($details['middle_name'] ?? ''),
        'last_name' => (string)($details['last_name'] ?? ''),
        'suffix' => (string)($details['suffix'] ?? ''),
        'dob' => (string)($details['dob'] ?? ''),
        'civil_status' => (string)($details['civil_status'] ?? ''),
        'citizenship' => (string)($details['citizenship'] ?? ''),
        'gender' => (string)($details['gender'] ?? ''),
    ];

    $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        throw new RuntimeException('Failed to encode profile details.');
    }

    $file = $dir . '/user_' . $userId . '.json';
    $ok = @file_put_contents($file, $json, LOCK_EX);
    if ($ok === false) {
        throw new RuntimeException('Failed to save profile details.');
    }
}

function splitFullNameParts(string $fullName): array
{
    $suffixes = ['Jr.', 'Sr.', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];
    $parts = preg_split('/\s+/', trim($fullName)) ?: [];
    if (empty($parts)) {
        return ['first_name' => '', 'middle_name' => '', 'last_name' => '', 'suffix' => ''];
    }

    $suffix = '';
    $last = end($parts);
    if (in_array($last, $suffixes, true)) {
        $suffix = array_pop($parts) ?: '';
    }

    if (count($parts) === 1) {
        return ['first_name' => $parts[0], 'middle_name' => '', 'last_name' => '', 'suffix' => $suffix];
    }

    $first = array_shift($parts) ?: '';
    $lastName = array_pop($parts) ?: '';
    $middle = implode(' ', $parts);

    return [
        'first_name' => $first,
        'middle_name' => $middle,
        'last_name' => $lastName,
        'suffix' => $suffix,
    ];
}

