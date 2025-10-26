<?php
// /classes/User.php

class User {
    
    private $db; // Stores the PDO connection object

    /**
     * @param PDO $pdo A PDO database connection object.
     */
    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    /**
     * Finds a user by their ID.
     * @param int $id The user's ID.
     * @return mixed Associative array of user data or false if not found.
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM Users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Finds a user by their email address.
     * @param string $email The user's email.
     * @return mixed Associative array of user data or false if not found.
     */
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Gets all users (with pagination in a real app).
     * @return array An array of all users.
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT id, first_name, last_name, email, role, status FROM Users ORDER BY last_name");
        return $stmt->fetchAll();
    }

    /**
     * Creates a new user.
     * @param array $data Associative array of user data.
     * @return string The ID of the new user.
     */
    public function create($data) {
        $sql = "INSERT INTO Users (first_name, last_name, email, password, phone, role, status, created_at) 
                VALUES (:first_name, :last_name, :email, :password, :phone, :role, :status, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':first_name' => $data['first_name'],
            ':last_name'  => $data['last_name'],
            ':email'      => $data['email'],
            ':password'   => $data['password'], // Password should be hashed *before* calling this
            ':phone'      => $data['phone'] ?? null,
            ':role'       => $data['role'],
            ':status'     => $data['status'] ?? 'pending'
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Updates a user's profile information.
     * @param int $id The user's ID.
     * @param array $data Associative array of data to update.
     * @return bool True on success, false on failure.
     */
    public function update($id, $data) {
        $sql = "UPDATE Users SET 
                first_name = :first_name, 
                last_name = :last_name, 
                phone = :phone, 
                role = :role, 
                status = :status,
                bio = :bio
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':first_name' => $data['first_name'],
            ':last_name'  => $data['last_name'],
            ':phone'      => $data['phone'] ?? null,
            ':role'       => $data['role'],
            ':status'     => $data['status'],
            ':bio'        => $data['bio'] ?? null,
            ':id'         => $id
        ]);
    }

    /**
     * Deletes a user.
     * @param int $id The ID of the user to delete.
     * @return bool True on success, false on failure.
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM Users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Changes a user's password.
     * @param int $id The user's ID.
     * @param string $hashed_password The *new*, already-hashed password.
     * @return bool True on success, false on failure.
     */
    public function changePassword($id, $hashed_password) {
        $stmt = $this->db->prepare("UPDATE Users SET password = ? WHERE id = ?");
        return $stmt->execute([$hashed_password, $id]);
    }
}
?>