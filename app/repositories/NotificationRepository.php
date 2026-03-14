<?php

require_once __DIR__ . '/../core/Database.php';

class NotificationRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create(int $userId, string $title, string $message, ?string $link = null): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO notifications (user_id, title, message, link, is_read, created_at)
            VALUES (?, ?, ?, ?, 0, NOW())
        ");
        $stmt->execute([$userId, $title, $message, $link]);

        return (int)$this->db->lastInsertId();
    }

    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT id, user_id, title, message, link, is_read, created_at
            FROM notifications
            WHERE user_id = ?
            ORDER BY created_at DESC, id DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function markAsRead(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE notifications
            SET is_read = 1
            WHERE id = ? AND user_id = ?
        ");
        return $stmt->execute([$id, $userId]);
    }

    public function markAllAsRead(int $userId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE notifications
            SET is_read = 1
            WHERE user_id = ? AND is_read = 0
        ");
        return $stmt->execute([$userId]);
    }

    public function unreadCount(int $userId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS cnt
            FROM notifications
            WHERE user_id = ? AND is_read = 0
        ");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return (int)($row['cnt'] ?? 0);
    }
}