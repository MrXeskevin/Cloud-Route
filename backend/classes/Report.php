<?php
/**
 * Report Class
 * Handles issue reporting and resolution
 */

require_once 'Database.php';

class Report {
    private $db;
    private $conn;
    
    // Report properties
    private $id;
    private $reportId;
    private $userId;
    private $issueType;
    private $priority;
    private $route;
    private $busNumber;
    private $date;
    private $time;
    private $location;
    private $description;
    private $status;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }
    
    /**
     * Create a new report
     * @param array $data Report data
     * @return array Response with success status
     */
    public function create($data) {
        try {
            // Validate required fields
            if (empty($data['userId']) || empty($data['issueType']) || 
                empty($data['priority']) || empty($data['route']) || 
                empty($data['date']) || empty($data['time']) || 
                empty($data['location']) || empty($data['description'])) {
                return [
                    'success' => false,
                    'message' => 'All required fields must be filled'
                ];
            }
            
            // Sanitize input
            $userId = (int)$data['userId'];
            $issueType = $this->db->sanitize($data['issueType']);
            $priority = $this->db->sanitize($data['priority']);
            $route = $this->db->sanitize($data['route']);
            $date = $this->db->sanitize($data['date']);
            $time = $this->db->sanitize($data['time']);
            $location = $this->db->sanitize($data['location']);
            $description = $this->db->sanitize($data['description']);
            $busNumber = isset($data['busNumber']) ? $this->db->sanitize($data['busNumber']) : null;
            $contactMethod = isset($data['contactMethod']) ? $this->db->sanitize($data['contactMethod']) : 'email';
            $anonymous = isset($data['anonymous']) ? 1 : 0;
            
            // Generate report ID
            $reportId = $this->db->generateId('RPT');
            
            // Insert report
            $stmt = $this->conn->prepare(
                "INSERT INTO reports (report_id, user_id, issue_type, priority, route, 
                bus_number, date, time, location, description, contact_method, anonymous, 
                status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())"
            );
            
            $stmt->bind_param("sisssssssssi", $reportId, $userId, $issueType, $priority, 
                              $route, $busNumber, $date, $time, $location, $description, 
                              $contactMethod, $anonymous);
            
            if ($stmt->execute()) {
                $id = $this->conn->insert_id;
                $stmt->close();
                
                return [
                    'success' => true,
                    'message' => 'Report submitted successfully',
                    'report' => [
                        'id' => $reportId,
                        'issue_type' => $issueType,
                        'priority' => $priority,
                        'status' => 'pending'
                    ]
                ];
            } else {
                $stmt->close();
                throw new Exception($this->conn->error);
            }
            
        } catch (Exception $e) {
            error_log("Report creation error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to submit report. Please try again.'
            ];
        }
    }
    
    /**
     * Update report status
     * @param string $reportId Report ID
     * @param string $status New status
     * @param string $resolution Resolution text (optional)
     * @return array Response with success status
     */
    public function updateStatus($reportId, $status, $resolution = null) {
        try {
            $reportId = $this->db->sanitize($reportId);
            $status = $this->db->sanitize($status);
            
            if ($status === 'resolved' && $resolution) {
                $resolution = $this->db->sanitize($resolution);
                $stmt = $this->conn->prepare(
                    "UPDATE reports SET status = ?, resolution = ?, resolved_at = NOW(), updated_at = NOW() 
                     WHERE report_id = ?"
                );
                $stmt->bind_param("sss", $status, $resolution, $reportId);
            } else {
                $stmt = $this->conn->prepare(
                    "UPDATE reports SET status = ?, updated_at = NOW() WHERE report_id = ?"
                );
                $stmt->bind_param("ss", $status, $reportId);
            }
            
            if ($stmt->execute()) {
                $affected = $this->conn->affected_rows;
                $stmt->close();
                
                if ($affected > 0) {
                    return [
                        'success' => true,
                        'message' => 'Report status updated successfully'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Report not found'
                    ];
                }
            } else {
                $stmt->close();
                throw new Exception($this->conn->error);
            }
            
        } catch (Exception $e) {
            error_log("Report update error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to update report status'
            ];
        }
    }
    
    /**
     * Get user's reports
     * @param int $userId User ID
     * @param string $status Filter by status (optional)
     * @return array List of reports
     */
    public function getUserReports($userId, $status = null) {
        $userId = $this->db->sanitize($userId);
        
        $sql = "SELECT * FROM reports WHERE user_id = '$userId'";
        
        if ($status) {
            $status = $this->db->sanitize($status);
            $sql .= " AND status = '$status'";
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $result = $this->conn->query($sql);
        $reports = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $reports[] = $row;
            }
        }
        
        return $reports;
    }
    
    /**
     * Get all reports (admin)
     * @param array $filters Optional filters
     * @return array List of reports
     */
    public function getAll($filters = []) {
        $sql = "SELECT r.*, u.name as user_name, u.username, u.email, u.phone 
                FROM reports r 
                LEFT JOIN users u ON r.user_id = u.id 
                WHERE 1=1";
        
        // Apply filters
        if (isset($filters['status'])) {
            $status = $this->db->sanitize($filters['status']);
            $sql .= " AND r.status = '$status'";
        }
        
        if (isset($filters['priority'])) {
            $priority = $this->db->sanitize($filters['priority']);
            $sql .= " AND r.priority = '$priority'";
        }
        
        if (isset($filters['issueType'])) {
            $issueType = $this->db->sanitize($filters['issueType']);
            $sql .= " AND r.issue_type = '$issueType'";
        }
        
        if (isset($filters['route'])) {
            $route = $this->db->sanitize($filters['route']);
            $sql .= " AND r.route = '$route'";
        }
        
        if (isset($filters['busNumber'])) {
            $busNumber = $this->db->sanitize($filters['busNumber']);
            $sql .= " AND r.bus_number = '$busNumber'";
        }
        
        $sql .= " ORDER BY 
                  CASE r.priority 
                    WHEN 'urgent' THEN 1 
                    WHEN 'high' THEN 2 
                    WHEN 'medium' THEN 3 
                    WHEN 'low' THEN 4 
                  END,
                  r.created_at DESC";
        
        $result = $this->conn->query($sql);
        $reports = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Hide user info if anonymous
                if ($row['anonymous'] == 1) {
                    $row['user_name'] = 'Anonymous';
                    $row['username'] = 'Anonymous';
                    $row['email'] = 'Anonymous';
                    $row['phone'] = 'Anonymous';
                }
                $reports[] = $row;
            }
        }
        
        return $reports;
    }
    
    /**
     * Get report by ID
     * @param string $reportId Report ID
     * @return array|null Report data or null if not found
     */
    public function getById($reportId) {
        $reportId = $this->db->sanitize($reportId);
        
        $stmt = $this->conn->prepare(
            "SELECT r.*, u.name as user_name, u.username, u.email, u.phone 
             FROM reports r 
             LEFT JOIN users u ON r.user_id = u.id 
             WHERE r.report_id = ?"
        );
        
        $stmt->bind_param("s", $reportId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $report = $result->fetch_assoc();
            $stmt->close();
            
            // Hide user info if anonymous
            if ($report['anonymous'] == 1) {
                $report['user_name'] = 'Anonymous';
                $report['username'] = 'Anonymous';
                $report['email'] = 'Anonymous';
                $report['phone'] = 'Anonymous';
            }
            
            return $report;
        }
        
        $stmt->close();
        return null;
    }
    
    /**
     * Delete report (soft delete - update status)
     * @param string $reportId Report ID
     * @return array Response with success status
     */
    public function delete($reportId) {
        try {
            $reportId = $this->db->sanitize($reportId);
            
            $stmt = $this->conn->prepare(
                "UPDATE reports SET status = 'closed', updated_at = NOW() WHERE report_id = ?"
            );
            
            $stmt->bind_param("s", $reportId);
            
            if ($stmt->execute()) {
                $stmt->close();
                return [
                    'success' => true,
                    'message' => 'Report deleted successfully'
                ];
            } else {
                $stmt->close();
                throw new Exception($this->conn->error);
            }
            
        } catch (Exception $e) {
            error_log("Report deletion error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to delete report'
            ];
        }
    }
    
    /**
     * Get report statistics
     * @return array Report statistics
     */
    public function getStatistics() {
        $stats = [];
        
        // Total reports
        $result = $this->conn->query("SELECT COUNT(*) as count FROM reports");
        $stats['total'] = $result->fetch_assoc()['count'];
        
        // Pending reports
        $result = $this->conn->query("SELECT COUNT(*) as count FROM reports WHERE status = 'pending'");
        $stats['pending'] = $result->fetch_assoc()['count'];
        
        // In progress reports
        $result = $this->conn->query("SELECT COUNT(*) as count FROM reports WHERE status = 'in_progress'");
        $stats['in_progress'] = $result->fetch_assoc()['count'];
        
        // Resolved reports
        $result = $this->conn->query("SELECT COUNT(*) as count FROM reports WHERE status = 'resolved'");
        $stats['resolved'] = $result->fetch_assoc()['count'];
        
        // Urgent reports
        $result = $this->conn->query("SELECT COUNT(*) as count FROM reports WHERE priority = 'urgent' AND status != 'resolved'");
        $stats['urgent'] = $result->fetch_assoc()['count'];
        
        // Reports by type
        $result = $this->conn->query(
            "SELECT issue_type, COUNT(*) as count 
             FROM reports 
             GROUP BY issue_type 
             ORDER BY count DESC"
        );
        
        $stats['by_type'] = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $stats['by_type'][$row['issue_type']] = $row['count'];
            }
        }
        
        return $stats;
    }
    
    /**
     * Get reports grouped by priority
     * @return array Reports grouped by priority
     */
    public function getByPriority() {
        $sql = "SELECT priority, COUNT(*) as count 
                FROM reports 
                WHERE status IN ('pending', 'in_progress') 
                GROUP BY priority 
                ORDER BY 
                  CASE priority 
                    WHEN 'urgent' THEN 1 
                    WHEN 'high' THEN 2 
                    WHEN 'medium' THEN 3 
                    WHEN 'low' THEN 4 
                  END";
        
        $result = $this->conn->query($sql);
        $priorities = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $priorities[] = $row;
            }
        }
        
        return $priorities;
    }
}
?>

