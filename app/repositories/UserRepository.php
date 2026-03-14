<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../core/Database.php';

class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ? User::fromRow($row) : null;
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? User::fromRow($row) : null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('
            INSERT INTO users (full_name, email, password_hash, role, address, contact_number)
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $data['full_name'],
            $data['email'],
            $data['password_hash'],
            $data['role'] ?? 'resident',
            $data['address'] ?? null,
            $data['contact_number'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function updateProfile(int $id, string $fullName, ?string $address, ?string $contactNumber): bool
    {
        $stmt = $this->db->prepare('
            UPDATE users
            SET full_name = ?, address = ?, contact_number = ?, updated_at = NOW()
            WHERE id = ?
        ');
        return $stmt->execute([$fullName, $address, $contactNumber, $id]);
    }

    public function updatePasswordHash(int $id, string $newHash): bool
    {
        $stmt = $this->db->prepare('
            UPDATE users
            SET password_hash = ?, updated_at = NOW()
            WHERE id = ?
        ');
        return $stmt->execute([$newHash, $id]);
    }
}