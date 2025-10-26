<?php
// /classes/Authentication.php

require_once __DIR__ . '/User.php';

class Authentication {
    
    private $db;
    private $user_class;

    public function __construct(PDO $pdo) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = $pdo;
        $this->user_class = new User($pdo);
    }

    /**
     * Attempts to register a new user.
     * @param array $data User data (first_name, last_name, email, password).
     * @return array ['success' => bool, 'message' => string]
     */
    public function register($data) {
        if ($this->user_class->findByEmail($data['email'])) {
            return ['success' => false, 'message' => 'An account with this email already exists.'];
        }
        
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $data['role'] = 'student'; // Self-registration is for students
        $data['status'] = 'active'; // Or 'pending' if email verification is added
        
        $user_id = $this->user_class->create($data);
        
        if ($user_id) {
            // TODO: Send verification email here
            return ['success' => true, 'message' => 'Registration successful.'];
        } else {
            return ['success' => false, 'message' => 'Registration failed.'];
        }
    }

    /**
     * Attempts to log a user in.
     * @param string $email
     * @param string $password
     * @return bool True on success, false on failure.
     */
    public function login($email, $password) {
        $user = $this->user_class->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] !== 'active') {
                return false; // Account is pending or suspended
            }
            
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['first_name'] = $user['first_name'];
            
            return true;
        }
        return false;
    }

    /**
     * Logs the user out.
     */
    public function logout() {
        $_SESSION = array();
        session_destroy();
    }

    /**
     * Checks if a user is currently logged in.
     * @return bool
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Gets the logged-in user's data from the session.
     * @return array|null
     */
    public function getSessionUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        return [
            'id' => $_SESSION['user_id'],
            'role' => $_SESSION['role'],
            'email' => $_SESSION['email'],
            'first_name' => $_SESSION['first_name']
        ];
    }
    
    /**
     * Checks if the current user has one of the required roles.
     * @param array $roles e.g., ['admin', 'teacher']
     * @return bool
     */
    public function checkRole($roles = []) {
        if (!$this->isLoggedIn() || empty($roles)) {
            return false;
        }
        return in_array($_SESSION['role'], $roles);
    }
}
?>