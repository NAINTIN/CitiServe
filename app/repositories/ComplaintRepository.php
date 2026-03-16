<?php
// Include the Database connection
require_once __DIR__ . '/../core/Database.php';

// This class handles all database operations for complaints.
// It can create complaints, get complaint lists, update statuses, etc.
class ComplaintRepository
{
    // This stores our database connection
    private $db;

    // When we create a new ComplaintRepository, it connects to the database
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Get all active complaint categories (for the dropdown in the form)
    public function getActiveCategories()
    {
        $sql = "
            SELECT id, name, description
            FROM complaint_categories
            WHERE is_active = 1
            ORDER BY name ASC
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Create a new complaint in the database
    // Returns the new complaint's ID number
    public function create($data)
    {
        $sql = "
            INSERT INTO complaints
            (user_id, category_id, is_anonymous, title, description, location, status)
            VALUES (?, ?, ?, ?, ?, ?, 'submitted')
        ";
        $stmt = $this->db->prepare($sql);

        // Figure out the is_anonymous value (1 or 0)
        $isAnonymous = 0;
        if (!empty($data['is_anonymous'])) {
            $isAnonymous = 1;
        }

        // Get the location (can be null)
        $location = isset($data['location']) ? $data['location'] : null;

        $stmt->execute([
            (int)$data['user_id'],
            (int)$data['category_id'],
            $isAnonymous,
            (string)$data['title'],
            (string)$data['description'],
            $location,
        ]);

        return (int)$this->db->lastInsertId();
    }

    // Add evidence (like a photo or document) to a complaint
    public function addEvidence($complaintId, $filePath, $fileName = null)
    {
        $sql = "
            INSERT INTO complaint_evidence (complaint_id, file_path, file_name)
            VALUES (?, ?, ?)
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$complaintId, $filePath, $fileName]);
    }

    // Get all complaints submitted by a specific user
    public function getByUserId($userId)
    {
        $sql = "
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
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get ALL complaints (for admin/staff to see)
    public function getAll()
    {
        $sql = "
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
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Find a single complaint by its ID
    // Returns the complaint as an array, or null if not found
    public function findById($id)
    {
        $sql = "
            SELECT id, status
            FROM complaints
            WHERE id = ?
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $row;
        }
        return null;
    }

    // Update the status of a complaint (e.g. from 'submitted' to 'under_review')
    public function updateStatus($complaintId, $newStatus)
    {
        $sql = "
            UPDATE complaints
            SET status = ?, updated_at = NOW()
            WHERE id = ?
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$newStatus, $complaintId]);
    }

    // Get all evidence files attached to a specific complaint
    public function getEvidenceByComplaintId($complaintId)
    {
        $sql = "
            SELECT id, file_path, file_name, uploaded_at
            FROM complaint_evidence
            WHERE complaint_id = ?
            ORDER BY uploaded_at DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$complaintId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}