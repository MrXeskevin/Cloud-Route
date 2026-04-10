<?php
/**
 * Admin Class
 * Handles admin authentication and system administration
 */

require_once 'Database.php';
require_once 'User.php';
require_once 'Bus.php';
require_once 'Booking.php';
require_once 'Report.php';

class Admin {
    private $db;
    private $conn;
    private $user;
    private $bus;
    private $booking;
    private $report;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
        $this->user = new User();
        $this->bus = new Bus();
        $this->booking = new Booking();
        $this->report = new Report();
    }
    
    /**
     * Admin login
     * @param string $username Username
     * @param string $password Password
     * @return array Response with success status
     */
    public function login($username, $password) {
        try {
            // Sanitize input
            $username = $this->db->sanitize($username);
            
            // Query database for admin user
            $stmt = $this->conn->prepare(
                "SELECT id, name, username, email, password 
                 FROM users 
                 WHERE username = ? AND user_type = 'admin'"
            );
            
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $admin = $result->fetch_assoc();
                $stmt->close();
                
                // Verify password
                if (password_verify($password, $admin['password'])) {
                    // Start session and store admin data
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['user_type'] = 'admin';
                    
                    return [
                        'success' => true,
                        'message' => 'Admin login successful',
                        'admin' => [
                            'id' => $admin['id'],
                            'name' => $admin['name'],
                            'username' => $admin['username'],
                            'email' => $admin['email']
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
                    'message' => 'Admin user not found'
                ];
            }
            
        } catch (Exception $e) {
            error_log("Admin login error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Login failed. Please try again.'
            ];
        }
    }
    
    /**
     * Logout admin
     * @return array Response with success status
     */
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Unset all session variables
        session_unset();
        session_destroy();
        
        return [
            'success' => true,
            'message' => 'Logged out successfully'
        ];
    }
    
    /**
     * Check if user is admin
     * @return bool True if admin, false otherwise
     */
    public function isAdmin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['admin_id']) && isset($_SESSION['user_type']) && 
               $_SESSION['user_type'] === 'admin';
    }
    
    /**
     * Add a new driver
     * @param array $data Driver data
     * @return array Response with success status
     */
    public function addDriver($data) {
        try {
            // Validate required fields
            if (empty($data['name']) || empty($data['license']) || empty($data['phone'])) {
                return [
                    'success' => false,
                    'message' => 'All required fields must be filled'
                ];
            }
            
            // Sanitize input
            $name = $this->db->sanitize($data['name']);
            $license = $this->db->sanitize($data['license']);
            $phone = $this->db->sanitize($data['phone']);
            $assignedBus = isset($data['bus']) ? $this->db->sanitize($data['bus']) : null;
            
            // Check if license already exists
            $stmt = $this->conn->prepare("SELECT id FROM drivers WHERE license_number = ?");
            $stmt->bind_param("s", $license);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $stmt->close();
                return [
                    'success' => false,
                    'message' => 'License number already exists'
                ];
            }
            $stmt->close();
            
            // Insert driver
            $stmt = $this->conn->prepare(
                "INSERT INTO drivers (name, license_number, phone, assigned_bus, status, created_at) 
                 VALUES (?, ?, ?, ?, 'active', NOW())"
            );
            
            $stmt->bind_param("ssss", $name, $license, $phone, $assignedBus);
            
            if ($stmt->execute()) {
                $driverId = $this->conn->insert_id;
                $stmt->close();
                
                return [
                    'success' => true,
                    'message' => 'Driver added successfully',
                    'driver_id' => $driverId
                ];
            } else {
                $stmt->close();
                throw new Exception($this->conn->error);
            }
            
        } catch (Exception $e) {
            error_log("Driver addition error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to add driver'
            ];
        }
    }
    
    /**
     * Update driver information
     * @param int $driverId Driver ID
     * @param array $data Updated data
     * @return array Response with success status
     */
    public function updateDriver($driverId, $data) {
        try {
            $driverId = $this->db->sanitize($driverId);
            $updates = [];
            $params = [];
            $types = '';
            
            // Build dynamic update query
            if (isset($data['name'])) {
                $updates[] = "name = ?";
                $params[] = $this->db->sanitize($data['name']);
                $types .= 's';
            }
            
            if (isset($data['phone'])) {
                $updates[] = "phone = ?";
                $params[] = $this->db->sanitize($data['phone']);
                $types .= 's';
            }
            
            if (isset($data['assignedBus'])) {
                $updates[] = "assigned_bus = ?";
                $params[] = $this->db->sanitize($data['assignedBus']);
                $types .= 's';
            }
            
            if (isset($data['status'])) {
                $updates[] = "status = ?";
                $params[] = $this->db->sanitize($data['status']);
                $types .= 's';
            }
            
            if (empty($updates)) {
                return [
                    'success' => false,
                    'message' => 'No fields to update'
                ];
            }
            
            $updates[] = "updated_at = NOW()";
            $params[] = $driverId;
            $types .= 'i';
            
            $sql = "UPDATE drivers SET " . implode(', ', $updates) . " WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            
            if ($stmt->execute()) {
                $stmt->close();
                return [
                    'success' => true,
                    'message' => 'Driver updated successfully'
                ];
            } else {
                $stmt->close();
                throw new Exception($this->conn->error);
            }
            
        } catch (Exception $e) {
            error_log("Driver update error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to update driver'
            ];
        }
    }
    
    /**
     * Delete driver (soft delete)
     * @param int $driverId Driver ID
     * @return array Response with success status
     */
    public function deleteDriver($driverId) {
        try {
            $driverId = $this->db->sanitize($driverId);
            
            $stmt = $this->conn->prepare(
                "UPDATE drivers SET status = 'inactive', updated_at = NOW() WHERE id = ?"
            );
            
            $stmt->bind_param("i", $driverId);
            
            if ($stmt->execute()) {
                $stmt->close();
                return [
                    'success' => true,
                    'message' => 'Driver deleted successfully'
                ];
            } else {
                $stmt->close();
                throw new Exception($this->conn->error);
            }
            
        } catch (Exception $e) {
            error_log("Driver deletion error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to delete driver'
            ];
        }
    }
    
    /**
     * Get all drivers
     * @param string $status Filter by status (optional)
     * @return array List of drivers
     */
    public function getAllDrivers($status = null) {
        $sql = "SELECT * FROM drivers WHERE 1=1";
        
        if ($status) {
            $status = $this->db->sanitize($status);
            $sql .= " AND status = '$status'";
        }
        
        $sql .= " ORDER BY name";
        
        $result = $this->conn->query($sql);
        $drivers = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $drivers[] = $row;
            }
        }
        
        return $drivers;
    }
    
    /**
     * Get system statistics
     * @return array System statistics
     */
    public function getStatistics() {
        $stats = [];
        
        // Get bus statistics
        $stats['buses'] = $this->bus->getStatistics();
        
        // Get booking statistics
        $stats['bookings'] = $this->booking->getStatistics();
        
        // Get report statistics
        $stats['reports'] = $this->report->getStatistics();
        
        // Get user statistics
        $result = $this->conn->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'user'");
        $stats['registered_users'] = $result->fetch_assoc()['count'];
        
        // Get driver statistics
        $result = $this->conn->query("SELECT COUNT(*) as count FROM drivers WHERE status = 'active'");
        $stats['active_drivers'] = $result->fetch_assoc()['count'];
        
        return $stats;
    }
    
    /**
     * Get dashboard analytics
     * @return array Dashboard analytics data
     */
    public function getDashboardAnalytics() {
        $analytics = [];
        
        // Bookings per day for the last 7 days
        $sql = "SELECT DATE(date) as booking_date, COUNT(*) as count 
                FROM bookings 
                WHERE date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
                AND status = 'confirmed' 
                GROUP BY DATE(date) 
                ORDER BY booking_date";
        
        $result = $this->conn->query($sql);
        $analytics['bookings_per_day'] = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $analytics['bookings_per_day'][] = $row;
            }
        }
        
        // Most popular routes
        $sql = "SELECT route, COUNT(*) as count 
                FROM bookings 
                WHERE status = 'confirmed' 
                GROUP BY route 
                ORDER BY count DESC 
                LIMIT 5";
        
        $result = $this->conn->query($sql);
        $analytics['popular_routes'] = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $analytics['popular_routes'][] = $row;
            }
        }
        
        // Bus utilization
        $sql = "SELECT b.bus_number, b.capacity, 
                COUNT(bk.id) as bookings,
                ROUND((COUNT(bk.id) / (b.capacity * 7)) * 100, 2) as utilization 
                FROM buses b 
                LEFT JOIN bookings bk ON b.bus_number = bk.bus_number 
                AND bk.date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
                AND bk.status = 'confirmed' 
                WHERE b.status = 'active' 
                GROUP BY b.bus_number, b.capacity";
        
        $result = $this->conn->query($sql);
        $analytics['bus_utilization'] = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $analytics['bus_utilization'][] = $row;
            }
        }
        
        return $analytics;
    }
    
    /**
     * Get system logs
     * @param int $limit Number of logs to retrieve
     * @return array System logs
     */
    public function getSystemLogs($limit = 50) {
        // This would connect to a logs table if implemented
        // For now, return recent activities from different tables
        
        $logs = [];
        
        // Recent bookings
        $sql = "SELECT 'booking' as type, created_at, 
                CONCAT('New booking: ', booking_id) as message 
                FROM bookings 
                ORDER BY created_at DESC 
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $limitParam = (int)($limit / 2);
        $stmt->bind_param("i", $limitParam);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $logs[] = $row;
            }
        }
        $stmt->close();
        
        // Recent reports
        $sql = "SELECT 'report' as type, created_at, 
                CONCAT('New report: ', report_id, ' - ', issue_type) as message 
                FROM reports 
                ORDER BY created_at DESC 
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limitParam);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $logs[] = $row;
            }
        }
        $stmt->close();
        
        // Sort by created_at
        usort($logs, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return array_slice($logs, 0, $limit);
    }
}
?>

