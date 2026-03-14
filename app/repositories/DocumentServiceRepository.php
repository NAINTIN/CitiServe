<?php
require_once __DIR__ . '/../core/Database.php';

class DocumentServiceRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllActive(): array
    {
        $stmt = $this->db->query("
            SELECT id, name, description, price, processing_time_days
            FROM document_services
            WHERE is_active = 1
            ORDER BY name ASC
        ");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, name, description, price, processing_time_days, is_active
            FROM document_services
            WHERE id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}