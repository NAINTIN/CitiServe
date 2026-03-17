<?php

// This class represents a User in our system.
// Each property matches a column in the "users" database table.
class User
{
    // User's ID number (null if not saved to database yet)
    public $id = null;

    // User's full name
    public $full_name = '';

    // User's email address
    public $email = '';

    // User's hashed password (never store plain text passwords!)
    public $password_hash = '';

    // User's role: 'resident', 'staff', or 'admin'
    public $role = 'resident';

    // User's home address (optional)
    public $address = null;

    // User's contact number (optional)
    public $contact_number = null;

    // Whether the resident account is verified to submit complaints
    public $is_verified = 0;

    // Verification status for residency proof workflow
    public $residency_verification_status = 'not_submitted';

    // Uploaded residency proof file path (optional)
    public $residency_proof_path = null;

    // When the account was created
    public $created_at = null;

    // When the account was last updated
    public $updated_at = null;

    // This static method creates a User object from a database row (associative array).
    // It's like a helper that converts database data into a User object.
    public static function fromRow($row)
    {
        $user = new User();

        // Set the id (convert to integer, or null if not set)
        if (isset($row['id'])) {
            $user->id = (int)$row['id'];
        } else {
            $user->id = null;
        }

        // Set each property from the row, using defaults if the value is missing
        $user->full_name = isset($row['full_name']) ? $row['full_name'] : '';
        $user->email = isset($row['email']) ? $row['email'] : '';
        $user->password_hash = isset($row['password_hash']) ? $row['password_hash'] : '';
        $user->role = isset($row['role']) ? $row['role'] : 'resident';
        $user->address = isset($row['address']) ? $row['address'] : null;
        $user->contact_number = isset($row['contact_number']) ? $row['contact_number'] : null;
        $user->is_verified = isset($row['is_verified']) ? (int)$row['is_verified'] : 0;
        $user->residency_verification_status = isset($row['residency_verification_status']) ? $row['residency_verification_status'] : 'not_submitted';
        $user->residency_proof_path = isset($row['residency_proof_path']) ? $row['residency_proof_path'] : null;
        $user->created_at = isset($row['created_at']) ? $row['created_at'] : null;
        $user->updated_at = isset($row['updated_at']) ? $row['updated_at'] : null;

        return $user;
    }
}
