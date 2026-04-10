<?php
/**
 * User Class
 * Handles user authentication, registration, and profile management
 */

require_once 'Database.php';

class User {
    private $db;
    private $conn;
    
    // User properties
    private $id;
    private $name;
    private $username;
    private $email;
    private $phone;
    private $userType;
    private $createdAt;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }
    
    /**
     * Register a new user
     * @param array $data User registration data
     * @return array Response with success status and message
     */
    public function register($data) {
        try {
            // Validate required fields
            if (empty($data['name']) || empty($data['username']) || 
                empty($data['email']) || empty($data['password']) || 
                empty($data['phone'])) {
                return [
                    'success' => false,
                    'message' => 'All fields are required'
                ];
            }
            
            // Sanitize input
            $name = $this->db->sanitize($data['name']);
            $username = $this->db->sanitize($data['username']);
            $email = $this->db->sanitize($data['email']);
            $phone = $this->db->sanitize($data['phone']);
            
            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return [
                    'success' => false,
                    'message' => 'Invalid email format'
                ];
            }
            
            // Check if username or email already exists
            if ($this->userExists($username, $email)) {
                return [
                    'success' => false,
                    'message' => 'Username or email already exists'
                ];
            }
            
            // Hash password
            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Insert user into database
            $stmt = $this->conn->prepare(
                "INSERT INTO users (name, username, email, phone, password, user_type, created_at) 
                 VALUES (?, ?, ?, ?, ?, 'user', NOW())"
            );
            
            $stmt->bind_param("sssss", $name, $username, $email, $phone, $passwordHash);
            
            if ($stmt->execute()) {
                $userId = $this->conn->insert_id;
                $stmt->close();
                
                return [
                    'success' => true,
                    'message' => 'Registration successful',
                    'user_id' => $userId
                ];
            } else {
                $stmt->close();
                throw new Exception($this->conn->error);
            }
            
        } catch (Exception $e) {
            error_log("User registration error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Registration failed. Please try again.'
            ];
        }
    }
    
    /**
     * Authenticate user login
     * @param string $username Username or email
     * @param string $password Password
     * @return array Response with success status and user data
     */
    public function login($username, $password) {
        try {
            // Sanitize input
            $username = $this->db->sanitize($username);
            
            // Query database for user
            $stmt = $this->conn->prepare(
                "SELECT id, name, username, email, phone, password, user_type 
                 FROM users 
                 WHERE username = ? OR email = ?"
            );
            
            $stmt->bind_param("ss", $username, $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $stmt->close();
                
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Set user properties
                    $this->id = $user['id'];
                    $this->name = $user['name'];
                    $this->username = $user['username'];
                    $this->email = $user['email'];
                    $this->phone = $user['phone'];
                    $this->userType = $user['user_type'];
                    
                    // Start session and store user data
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    
                    $_SESSION['user_id'] = $this->id;
                    $_SESSION['username'] = $this->username;
                    $_SESSION['user_type'] = $this->userType;
                    
                    // Return user data (without password)
                    return [
                        'success' => true,
                        'message' => 'Login successful',
                        'user' => [
                            'id' => $this->id,
                            'name' => $this->name,
                            'username' => $this->username,
                            'email' => $this->email,
                            'phone' => $this->phone,
                            'user_type' => $this->userType
                        ]
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Incorrect password'
                    ];
                }
            } else {
                $stmt->close();
                return [
                    'success' => false,
                    'message' => 'User not found'
                ];
            }
            
        } catch (Exception $e) {
            error_log("User login error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Login failed. Please try again.'
            ];
        }
    }
    
    /**
     * Logout user
     * @return array Response with success status
     */
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Unset all session variables
        session_unset();
        session_destroy();
        
        // Clear user properties
        $this->id = null;
        $this->name = null;
        $this->username = null;
        $this->email = null;
        $this->phone = null;
        $this->userType = null;
        
        return [
            'success' => true,
            'message' => 'Logged out successfully'
        ];
    }
    
    /**
     * Check if user exists
     * @param string $username Username to check
     * @param string $email Email to check
     * @return bool True if user exists, false otherwise
     */
    private function userExists($username, $email) {
        $stmt = $this->conn->prepare(
            "SELECT id FROM users WHERE username = ? OR email = ?"
        );
        
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        
        return $exists;
    }
    
    /**
     * Get user by ID
     * @param int $userId User ID
     * @return array|null User data or null if not found
     */
    public function getUserById($userId) {
        $userId = $this->db->sanitize($userId);
        
        $stmt = $this->conn->prepare(
            "SELECT id, name, username, email, phone, user_type, created_at 
             FROM users 
             WHERE id = ?"
        );
        
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $stmt->close();
            return $user;
        }
        
        $stmt->close();
        return null;
    }
    
    /**
     * Update user profile
     * @param int $userId User ID
     * @param array $data Updated data
     * @return array Response with success status
     */
    public function updateProfile($userId, $data) {
        try {
            $userId = $this->db->sanitize($userId);
            $updates = [];
            $params = [];
            $types = '';
            
            // Build dynamic update query
            if (isset($data['name'])) {
                $updates[] = "name = ?";
                $params[] = $this->db->sanitize($data['name']);
                $types .= 's';
            }
            
            if (isset($data['email'])) {
                $updates[] = "email = ?";
                $params[] = $this->db->sanitize($data['email']);
                $types .= 's';
            }
            
            if (isset($data['phone'])) {
                $updates[] = "phone = ?";
                $params[] = $this->db->sanitize($data['phone']);
                $types .= 's';
            }
            
            if (empty($updates)) {
                return [
                    'success' => false,
                    'message' => 'No fields to update'
                ];
            }
            
            $updates[] = "updated_at = NOW()";
            $params[] = $userId;
            $types .= 'i';
            
            $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            
            if ($stmt->execute()) {
                $stmt->close();
                return [
                    'success' => true,
                    'message' => 'Profile updated successfully'
                ];
            } else {
                $stmt->close();
                throw new Exception($this->conn->error);
            }
            
        } catch (Exception $e) {
            error_log("User update error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to update profile'
            ];
        }
    }
    
    /**
     * Change user password
     * @param int $userId User ID
     * @param string $currentPassword Current password
     * @param string $newPassword New password
     * @return array Response with success status
     */
    public function changePassword($userId, $currentPassword, $newPassword) {
        try {
            // Get current password hash
            $stmt = $this->conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                return [
                    'success' => false,
                    'message' => 'User not found'
                ];
            }
            
            $user = $result->fetch_assoc();
            $stmt->close();
            
            // Verify current password
            if (!password_verify($currentPassword, $user['password'])) {
                return [
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ];
            }
            
            // Hash new password
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Update password
            $stmt = $this->conn->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("si", $newPasswordHash, $userId);
            
            if ($stmt->execute()) {
                $stmt->close();
                return [
                    'success' => true,
                    'message' => 'Password changed successfully'
                ];
            } else {
                $stmt->close();
                throw new Exception($this->conn->error);
            }
            
        } catch (Exception $e) {
            error_log("Password change error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to change password'
            ];
        }
    }
    
    /**
     * Get all users (admin only)
     * @param string $userType Filter by user type
     * @return array List of users
     */
    public function getAllUsers($userType = null) {
        $sql = "SELECT id, name, username, email, phone, user_type, created_at 
                FROM users WHERE 1=1";
        
        if ($userType) {
            $userType = $this->db->sanitize($userType);
            $sql .= " AND user_type = '$userType'";
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $result = $this->conn->query($sql);
        $users = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }
        
        return $users;
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getPhone() { return $this->phone; }
    public function getUserType() { return $this->userType; }
}
?>

