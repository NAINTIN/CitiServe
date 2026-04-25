<?php
require_once __DIR__ . '/../../app/helpers/auth.php';
require_once __DIR__ . '/../../app/helpers/csrf.php';
require_once __DIR__ . '/../../app/core/CitiServeData.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    csrf_verify_or_die();
    $admin = require_admin();
    $data = new CitiServeData();

    $action = isset($_POST['action']) ? trim((string)$_POST['action']) : '';
    $userId = (int)$admin['id'];

    if ($action === 'mark_all') {
        $data->markAllNotificationsAsRead($userId);
        echo json_encode(['ok' => true]);
        exit;
    }

    if ($action === 'mark_one') {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Invalid notification id']);
            exit;
        }
        $data->markNotificationAsRead($id, $userId);
        echo json_encode(['ok' => true]);
        exit;
    }

    if ($action === 'mark_many') {
        $idsRaw = isset($_POST['ids']) ? (string)$_POST['ids'] : '';
        $parts = array_filter(array_map('trim', explode(',', $idsRaw)));
        foreach ($parts as $part) {
            $id = (int)$part;
            if ($id > 0) {
                $data->markNotificationAsRead($id, $userId);
            }
        }
        echo json_encode(['ok' => true]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid action']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Failed to update notification status']);
}

