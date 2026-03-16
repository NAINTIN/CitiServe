<?php
// Include the Database connection
require_once __DIR__ . '/../core/Database.php';

// This class handles database operations for document services.
// Document services are the types of documents residents can request (like certificates, IDs, etc).
class DocumentServiceRepository
{
    // This stores our database connection
    private $db;

    // When we create a new DocumentServiceRepository, it connects to the database
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Get all active (available) document services
    public function getAllActive()
    {
        $sql = "
            SELECT id, name, description, price, processing_time_days
            FROM document_services
            WHERE is_active = 1
            ORDER BY name ASC
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    // Find a single document service by its ID
    // Returns the service as an array, or null if not found
    public function findById($id)
    {
        $sql = "
            SELECT id, name, description, price, processing_time_days, is_active
            FROM document_services
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
}