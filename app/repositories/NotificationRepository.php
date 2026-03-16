<?php
// Include the Database connection
require_once __DIR__ . '/../core/Database.php';

// This class handles all database operations for notifications.
// Notifications are messages sent to users (like status updates).
class NotificationRepository
{
    // This stores our database connection
    private $db;

    // When we create a new NotificationRepository, it connects to the database
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Create a new notification for a user
    // Returns the new notification's ID number
    public function create($userId, $title, $message, $link = null)
    {
        $sql = "
            INSERT INTO notifications (user_id, title, message, link, is_read, created_at)
            VALUES (?, ?, ?, ?, 0, NOW())
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $title, $message, $link]);

        return (int)$this->db->lastInsertId();
    }

    // Get all notifications for a specific user (newest first)
    public function getByUser($userId)
    {
        $sql = "
            SELECT id, user_id, title, message, link, is_read, created_at
            FROM notifications
            WHERE user_id = ?
            ORDER BY created_at DESC, id DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // Mark a single notification as read
    public function markAsRead($id, $userId)
    {
        $sql = "
            UPDATE notifications
            SET is_read = 1
            WHERE id = ? AND user_id = ?
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id, $userId]);
    }

    // Mark ALL unread notifications as read for a user
    public function markAllAsRead($userId)
    {
        $sql = "
            UPDATE notifications
            SET is_read = 1
            WHERE user_id = ? AND is_read = 0
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    }

    // Count how many unread notifications a user has
    public function unreadCount($userId)
    {
        $sql = "
            SELECT COUNT(*) AS cnt
            FROM notifications
            WHERE user_id = ? AND is_read = 0
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $row = $stmt->fetch();

        // Return the count as an integer
        if (isset($row['cnt'])) {
            return (int)$row['cnt'];
        }
        return 0;
    }
}