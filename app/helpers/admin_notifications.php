<?php

function build_admin_notifications(CitiServeData $data, int $adminUserId): array
{
    $notifications = array_slice($data->getNotificationsByUser($adminUserId), 0, 20);
    $sections = ['new' => [], 'today' => [], 'earlier' => []];
    $nowTs = time();
    $unreadCount = 0;

    foreach ($notifications as $n) {
        $title = isset($n['title']) ? (string)$n['title'] : '';
        $message = isset($n['message']) ? (string)$n['message'] : '';
        $messageLower = strtolower($title . ' ' . $message);

        $category = 'announcement';
        if (strpos($messageLower, 'complaint') !== false) {
            $category = 'complaint';
        } elseif (strpos($messageLower, 'document') !== false || strpos($messageLower, 'request') !== false) {
            $category = 'document';
        }

        $createdAt = isset($n['created_at']) ? (string)$n['created_at'] : '';
        $createdTs = $createdAt !== '' ? strtotime($createdAt) : false;
        if ($createdTs === false) {
            $createdTs = $nowTs;
        }

        $ageSeconds = max(0, $nowTs - $createdTs);
        if ($ageSeconds < 3600) {
            $timeLabel = max(1, (int)floor($ageSeconds / 60)) . 'm';
        } elseif ($ageSeconds < 86400) {
            $timeLabel = (int)floor($ageSeconds / 3600) . 'h';
        } else {
            $timeLabel = (int)floor($ageSeconds / 86400) . 'd';
        }

        $section = 'earlier';
        if ($ageSeconds < 3600) {
            $section = 'new';
        } elseif ($ageSeconds < 86400) {
            $section = 'today';
        }

        $isRead = ((int)($n['is_read'] ?? 0) === 1);
        if (!$isRead) {
            $unreadCount++;
        }

        $sections[$section][] = [
            'id' => (int)($n['id'] ?? 0),
            'category' => $category,
            'message' => $message !== '' ? $message : $title,
            'time_label' => $timeLabel,
            'read' => $isRead,
            'link' => !empty($n['link']) ? (string)$n['link'] : '/CitiServe/public/admin/dashboard.php',
            'main_icon' => '/CitiServe/frontend/admin_dashboard/images/citiserve_notif.png',
            'badge_icon' => $category === 'complaint'
                ? '/CitiServe/frontend/admin_dashboard/images/complaint_notif.png'
                : '/CitiServe/frontend/admin_dashboard/images/document_notif.png',
        ];
    }

    return [
        'notifications' => $notifications,
        'sections' => $sections,
        'has_notif' => $unreadCount > 0,
        'unread_count' => $unreadCount,
    ];
}

