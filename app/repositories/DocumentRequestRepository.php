<?php
// Include the Database connection
require_once __DIR__ . '/../core/Database.php';

// This class handles all database operations for document requests.
class DocumentRequestRepository
{
    // This stores our database connection
    private $db;

    // When we create a new DocumentRequestRepository, it connects to the database
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Create a new document request in the database
    // Returns the new request's ID number
    public function create($data)
    {
        $sql = "
            INSERT INTO document_requests
            (user_id, document_service_id, purpose, status, payment_reference, payment_proof_path)
            VALUES (?, ?, ?, 'received', ?, ?)
        ";
        $stmt = $this->db->prepare($sql);

        // Get values from the data array, with null as default for optional fields
        $userId = $data['user_id'];
        $serviceId = $data['document_service_id'];
        $purpose = isset($data['purpose']) ? $data['purpose'] : null;
        $paymentRef = isset($data['payment_reference']) ? $data['payment_reference'] : null;
        $proofPath = isset($data['payment_proof_path']) ? $data['payment_proof_path'] : null;

        $stmt->execute([$userId, $serviceId, $purpose, $paymentRef, $proofPath]);

        return (int)$this->db->lastInsertId();
    }

    // Get all document requests submitted by a specific user
    public function getByUserId($userId)
    {
        $sql = "
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
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // Get ALL document requests with user info (for admin/staff)
    public function getAllWithUsers()
    {
        $sql = "
            SELECT
                dr.id,
                dr.user_id,
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
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    // Find a single document request by its ID
    // Returns the request as an array, or null if not found
    public function findById($id)
    {
        $sql = "
            SELECT id, user_id, status
            FROM document_requests
            WHERE id = ?
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if ($row) {
            return $row;
        }
        return null;
    }

    // Update the status of a document request
    public function updateStatus($requestId, $newStatus)
    {
        $sql = "
            UPDATE document_requests
            SET status = ?, updated_at = NOW()
            WHERE id = ?
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$newStatus, $requestId]);
    }
}