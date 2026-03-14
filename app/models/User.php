<?php

class User
{
    public ?int $id = null;
    public string $full_name;
    public string $email;
    public string $password_hash;
    public string $role = 'resident';
    public ?string $address = null;
    public ?string $contact_number = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public static function fromRow(array $row): User
    {
        $u = new User();
        $u->id = isset($row['id']) ? (int)$row['id'] : null;
        $u->full_name = $row['full_name'] ?? '';
        $u->email = $row['email'] ?? '';
        $u->password_hash = $row['password_hash'] ?? '';
        $u->role = $row['role'] ?? 'resident';
        $u->address = $row['address'] ?? null;
        $u->contact_number = $row['contact_number'] ?? null;
        $u->created_at = $row['created_at'] ?? null;
        $u->updated_at = $row['updated_at'] ?? null;
        return $u;
    }
}