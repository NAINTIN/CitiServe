<?php
require_once __DIR__ . '/../core/Database.php';

class ComplaintRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getActiveCategories(): array
    {
        $stmt = $this->db->query("
            SELECT id, name, description
            FROM complaint_categories
            WHERE is_active = 1
            ORDER BY name ASC
        ");
        return $stmt->fetchAll();
    }

    public function create(array $data): int
{
    $stmt = $this->db->prepare("
        INSERT INTO complaints
        (user_id, category_id, is_anonymous, title, description, location, status)
        VALUES (?, ?, ?, ?, ?, ?, 'submitted')
    ");
    $stmt->execute([
        $data['user_id'],
        $data['category_id'],
        $data['is_anonymous'] ? 1 : 0,
        $data['title'],
        $data['description'],
        $data['location'] ?? null,
    ]);

    return (int)$this->db->lastInsertId();
}

    public function addEvidence(int $complaintId, string $filePath, ?string $fileName = null): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO complaint_evidence (complaint_id, file_path, file_name)
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$complaintId, $filePath, $fileName]);
    }

    public function getByUserId(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                c.id,
                c.title,
                c.description,
                c.location,
                c.status,
                c.is_anonymous,
                c.created_at,
                cc.name AS category_name
            FROM complaints c
            INNER JOIN complaint_categories cc ON cc.id = c.category_id
            WHERE c.user_id = ?
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT
                c.id,
                c.title,
                c.description,
                c.location,
                c.status,
                c.is_anonymous,
                c.created_at,
                c.updated_at,
                cc.name AS category_name,
                u.full_name,
                u.email
            FROM complaints c
            INNER JOIN complaint_categories cc ON cc.id = c.category_id
            LEFT JOIN users u ON u.id = c.user_id
            ORDER BY c.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, status
            FROM complaints
            WHERE id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function updateStatus(int $complaintId, string $newStatus): bool
    {
        $stmt = $this->db->prepare("
            UPDATE complaints
            SET status = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$newStatus, $complaintId]);
    }

    public function getEvidenceByComplaintId(int $complaintId): array
    {
        $stmt = $this->db->prepare("
            SELECT id, file_path, file_name, uploaded_at
            FROM complaint_evidence
            WHERE complaint_id = ?
            ORDER BY uploaded_at DESC
        ");
        $stmt->execute([$complaintId]);
        return $stmt->fetchAll();
    }
}