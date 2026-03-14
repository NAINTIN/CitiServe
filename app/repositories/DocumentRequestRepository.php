<?php
require_once __DIR__ . '/../core/Database.php';

class DocumentRequestRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO document_requests
            (user_id, document_service_id, purpose, status, payment_reference, payment_proof_path)
            VALUES (?, ?, ?, 'received', ?, ?)
        ");
        $stmt->execute([
            $data['user_id'],
            $data['document_service_id'],
            $data['purpose'] ?? null,
            $data['payment_reference'] ?? null,
            $data['payment_proof_path'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getByUserId(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                dr.id,
                dr.status,
                dr.purpose,
                dr.payment_reference,
                dr.payment_proof_path,
                dr.created_at,
                ds.name AS service_name,
                ds.price,
                ds.processing_time_days
            FROM document_requests dr
            INNER JOIN document_services ds ON ds.id = dr.document_service_id
            WHERE dr.user_id = ?
            ORDER BY dr.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getAllWithUsers(): array
    {
        $stmt = $this->db->query("
            SELECT
                dr.id,
                dr.status,
                dr.purpose,
                dr.payment_reference,
                dr.payment_proof_path,
                dr.created_at,
                dr.updated_at,
                ds.name AS service_name,
                u.full_name,
                u.email
            FROM document_requests dr
            INNER JOIN document_services ds ON ds.id = dr.document_service_id
            INNER JOIN users u ON u.id = dr.user_id
            ORDER BY dr.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, status
            FROM document_requests
            WHERE id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function updateStatus(int $requestId, string $newStatus): bool
    {
        $stmt = $this->db->prepare("
            UPDATE document_requests
            SET status = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$newStatus, $requestId]);
    }
}