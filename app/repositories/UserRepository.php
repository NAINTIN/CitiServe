<?php
// Include the User model and Database connection
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../core/Database.php';

// This class handles all database operations for users.
// It can find users, create new ones, and update profiles.
class UserRepository
{
    // This stores our database connection
    private $db;

    // When we create a new UserRepository, it connects to the database
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getByRole($role)
    {
        $sql = 'SELECT id, full_name, email, role FROM users WHERE role = ? ORDER BY id ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$role]);
        return $stmt->fetchAll();
    }

    // Find a user by their email address
    // Returns a User object if found, or null if not found
    public function findByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email = ? LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $row = $stmt->fetch();

        // If we found a row, convert it to a User object
        if ($row) {
            return User::fromRow($row);
        }
        return null;
    }

    // Find a user by their ID number
    // Returns a User object if found, or null if not found
    public function findById($id)
    {
        $sql = 'SELECT * FROM users WHERE id = ? LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        // If we found a row, convert it to a User object
        if ($row) {
            return User::fromRow($row);
        }
        return null;
    }

    // Create a new user in the database
    // Returns the new user's ID number
    public function create($data)
    {
        $sql = '
            INSERT INTO users (full_name, email, password_hash, role, address, contact_number)
            VALUES (?, ?, ?, ?, ?, ?)
        ';
        $stmt = $this->db->prepare($sql);

        // Get the values from the data array, with defaults for optional fields
        $fullName = $data['full_name'];
        $email = $data['email'];
        $passwordHash = $data['password_hash'];
        $role = isset($data['role']) ? $data['role'] : 'resident';
        $address = isset($data['address']) ? $data['address'] : null;
        $contactNumber = isset($data['contact_number']) ? $data['contact_number'] : null;

        $stmt->execute([$fullName, $email, $passwordHash, $role, $address, $contactNumber]);

        // Return the ID of the newly created user
        return (int)$this->db->lastInsertId();
    }

    // Update a user's profile (name, address, contact number)
    // Returns true if the update was successful
    public function updateProfile($id, $fullName, $address, $contactNumber)
    {
        $sql = '
            UPDATE users
            SET full_name = ?, address = ?, contact_number = ?, updated_at = NOW()
            WHERE id = ?
        ';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$fullName, $address, $contactNumber, $id]);
    }

    // Update a user's password hash
    // Returns true if the update was successful
    public function updatePasswordHash($id, $newHash)
    {
        $sql = '
            UPDATE users
            SET password_hash = ?, updated_at = NOW()
            WHERE id = ?
        ';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$newHash, $id]);
    }
}