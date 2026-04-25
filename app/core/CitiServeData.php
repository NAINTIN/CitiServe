<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../models/User.php';

class CitiServeData
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function findUserByEmail($email)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ? User::fromRow($row) : null;
    }

    public function findUserById($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? User::fromRow($row) : null;
    }

    public function getUsersByRole($role)
    {
        $stmt = $this->db->prepare('SELECT id, full_name, email, role FROM users WHERE role = ? ORDER BY id ASC');
        $stmt->execute([$role]);
        return $stmt->fetchAll();
    }

    public function createUser($data)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (full_name, email, password_hash, role, address, contact_number, is_verified, proof_of_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['full_name'],
            $data['email'],
            $data['password_hash'],
            isset($data['role']) ? $data['role'] : 'resident',
            isset($data['address']) ? $data['address'] : null,
            isset($data['contact_number']) ? $data['contact_number'] : null,
            isset($data['is_verified']) ? (int)$data['is_verified'] : 1,
            isset($data['proof_of_id']) ? $data['proof_of_id'] : null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function updateUserProfile($id, $fullName, $address, $contactNumber)
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET full_name = ?, address = ?, contact_number = ?, updated_at = NOW() WHERE id = ?'
        );
        return $stmt->execute([$fullName, $address, $contactNumber, $id]);
    }

    public function updateUserProofOfId($id, $proofPath)
    {
        $stmt = $this->db->prepare('UPDATE users SET proof_of_id = ?, is_verified = 0, updated_at = NOW() WHERE id = ?');
        return $stmt->execute([$proofPath, $id]);
    }

    public function updateUserVerificationStatus($id, $isVerified)
    {
        $stmt = $this->db->prepare('UPDATE users SET is_verified = ?, updated_at = NOW() WHERE id = ?');
        return $stmt->execute([(int)$isVerified, (int)$id]);
    }

    public function getAllUsersWithVerification()
    {
        $sql = 'SELECT id, full_name, email, role, address, created_at, is_verified, proof_of_id FROM users ORDER BY created_at DESC, id DESC';
        return $this->db->query($sql)->fetchAll();
    }

    public function updateUserPasswordHash($id, $newHash)
    {
        $stmt = $this->db->prepare('UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?');
        return $stmt->execute([$newHash, $id]);
    }

    public function getAllActiveDocumentServices()
    {
        $sql = 'SELECT id, name, description, price, processing_time_days FROM document_services WHERE is_active = 1 ORDER BY name ASC';
        return $this->db->query($sql)->fetchAll();
    }

    public function findDocumentServiceById($id)
    {
        $stmt = $this->db->prepare('SELECT id, name, description, price, processing_time_days, is_active FROM document_services WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function createDocumentRequest($data)
    {
        $sql = "INSERT INTO document_requests (user_id, document_service_id, purpose, status, payment_method, payment_reference, payment_proof_path, form_data_json) VALUES (?, ?, ?, 'received', ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['user_id'],
            $data['document_service_id'],
            isset($data['purpose']) ? $data['purpose'] : null,
            isset($data['payment_method']) ? $data['payment_method'] : null,
            isset($data['payment_reference']) ? $data['payment_reference'] : null,
            isset($data['payment_proof_path']) ? $data['payment_proof_path'] : null,
            isset($data['form_data_json']) ? $data['form_data_json'] : null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function addDocumentRequestFile($requestId, $fileType, $filePath, $originalName = null)
    {
        $stmt = $this->db->prepare('INSERT INTO document_request_files (document_request_id, file_type, file_path, original_name) VALUES (?, ?, ?, ?)');
        return $stmt->execute([(int)$requestId, (string)$fileType, (string)$filePath, $originalName]);
    }

    public function getDocumentRequestFilesByRequestId($requestId)
    {
        $stmt = $this->db->prepare('SELECT id, document_request_id, file_type, file_path, original_name, uploaded_at FROM document_request_files WHERE document_request_id = ? ORDER BY id ASC');
        $stmt->execute([(int)$requestId]);
        return $stmt->fetchAll();
    }

    public function getDocumentRequestsByUserId($userId)
    {
        $sql = "
            SELECT
                dr.id,
                dr.status,
                dr.purpose,
                dr.payment_method,
                dr.payment_reference,
                dr.payment_proof_path,
                dr.created_at,
                ds.name AS service_name,
                ds.price AS fee,
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

    public function getAllDocumentRequestsWithUsers()
    {
        $sql = "
            SELECT
                dr.id,
                dr.user_id,
                dr.status,
                dr.purpose,
                dr.payment_method,
                dr.payment_reference,
                dr.payment_proof_path,
                dr.form_data_json,
                dr.created_at,
                dr.updated_at,
                ds.name AS service_name,
                ds.price AS fee,
                u.full_name,
                u.email
            FROM document_requests dr
            INNER JOIN document_services ds ON ds.id = dr.document_service_id
            INNER JOIN users u ON u.id = dr.user_id
            ORDER BY dr.created_at DESC
        ";
        return $this->db->query($sql)->fetchAll();
    }

    public function findDocumentRequestById($id)
    {
        $stmt = $this->db->prepare('SELECT id, user_id, status, document_service_id, payment_method, payment_reference, payment_proof_path, form_data_json, created_at FROM document_requests WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function deleteDocumentRequestById($id)
    {
        $requestId = (int)$id;
        $this->db->prepare('DELETE FROM document_request_files WHERE document_request_id = ?')->execute([$requestId]);
        $this->db->prepare("DELETE FROM status_history WHERE entity_type = 'document_request' AND entity_id = ?")->execute([$requestId]);
        $stmt = $this->db->prepare('DELETE FROM document_requests WHERE id = ?');
        return $stmt->execute([$requestId]);
    }

    public function getDocumentRequestByIdWithService($id)
    {
        $stmt = $this->db->prepare("
            SELECT
                dr.id,
                dr.user_id,
                dr.document_service_id,
                dr.status,
                dr.purpose,
                dr.payment_method,
                dr.payment_reference,
                dr.payment_proof_path,
                dr.form_data_json,
                dr.created_at,
                dr.updated_at,
                ds.name AS service_name,
                ds.price AS fee
            FROM document_requests dr
            INNER JOIN document_services ds ON ds.id = dr.document_service_id
            WHERE dr.id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function updateDocumentRequestStatus($requestId, $newStatus)
    {
        $stmt = $this->db->prepare('UPDATE document_requests SET status = ?, updated_at = NOW() WHERE id = ?');
        return $stmt->execute([$newStatus, $requestId]);
    }

    public function getActiveComplaintCategories()
    {
        $sql = 'SELECT id, name, description FROM complaint_categories WHERE is_active = 1 ORDER BY name ASC';
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createComplaint($data)
    {
        $sql = "INSERT INTO complaints (user_id, category_id, is_anonymous, title, description, location, status) VALUES (?, ?, ?, ?, ?, ?, 'submitted')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            (int)$data['user_id'],
            (int)$data['category_id'],
            !empty($data['is_anonymous']) ? 1 : 0,
            (string)$data['title'],
            (string)$data['description'],
            isset($data['location']) ? $data['location'] : null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function addComplaintEvidence($complaintId, $filePath, $fileName = null)
    {
        $stmt = $this->db->prepare('INSERT INTO complaint_evidence (complaint_id, file_path, file_name) VALUES (?, ?, ?)');
        return $stmt->execute([$complaintId, $filePath, $fileName]);
    }

    public function getComplaintsByUserId($userId)
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

    public function getAllComplaints()
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
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findComplaintById($id)
    {
        $stmt = $this->db->prepare('SELECT id, status FROM complaints WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findComplaintByIdWithOwner($id)
    {
        $stmt = $this->db->prepare('SELECT id, user_id, status FROM complaints WHERE id = ? LIMIT 1');
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function deleteComplaintById($id)
    {
        $complaintId = (int)$id;
        $this->db->prepare('DELETE FROM complaint_evidence WHERE complaint_id = ?')->execute([$complaintId]);
        $this->db->prepare("DELETE FROM status_history WHERE entity_type = 'complaint' AND entity_id = ?")->execute([$complaintId]);
        $stmt = $this->db->prepare('DELETE FROM complaints WHERE id = ?');
        return $stmt->execute([$complaintId]);
    }

    public function updateComplaintStatus($complaintId, $newStatus)
    {
        $stmt = $this->db->prepare('UPDATE complaints SET status = ?, updated_at = NOW() WHERE id = ?');
        return $stmt->execute([$newStatus, $complaintId]);
    }

    public function getEvidenceByComplaintId($complaintId)
    {
        $stmt = $this->db->prepare('SELECT id, file_path, file_name, uploaded_at FROM complaint_evidence WHERE complaint_id = ? ORDER BY uploaded_at DESC');
        $stmt->execute([$complaintId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createNotification($userId, $title, $message, $link = null)
    {
        $sql = 'INSERT INTO notifications (user_id, title, message, link, is_read, created_at) VALUES (?, ?, ?, ?, 0, NOW())';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $title, $message, $link]);
        return (int)$this->db->lastInsertId();
    }

    public function getNotificationsByUser($userId)
    {
        $sql = 'SELECT id, user_id, title, message, link, is_read, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC, id DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function markNotificationAsRead($id, $userId)
    {
        $stmt = $this->db->prepare('UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?');
        return $stmt->execute([$id, $userId]);
    }

    public function markAllNotificationsAsRead($userId)
    {
        $stmt = $this->db->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0');
        return $stmt->execute([$userId]);
    }

    public function unreadNotificationsCount($userId)
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) AS cnt FROM notifications WHERE user_id = ? AND is_read = 0');
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return isset($row['cnt']) ? (int)$row['cnt'] : 0;
    }

    public function getPdo()
    {
        return $this->db;
    }
}
