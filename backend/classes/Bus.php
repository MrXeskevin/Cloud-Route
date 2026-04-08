<?php
/**
 * Bus Class
 * Handles bus management, routes, locations, and availability
 */

require_once 'Database.php';

class Bus {
    private $db;
    private $conn;
    
    // Bus properties
    private $id;
    private $busNumber;
    private $capacity;
    private $routeId;
    private $routeCode;
    private $driverId;
    private $status;
    private $currentLat;
    private $currentLng;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }
    
    /**
     * Create a new bus
     * @param array $data Bus data
     * @return array Response with success status
     */
    public function create($data) {
        try {
            // Validate required fields
            if (empty($data['busNumber']) || empty($data['capacity'])) {
                return [
                    'success' => false,
                    'message' => 'Bus number and capacity are required'
                ];
            }
            
            // Sanitize input
            $busNumber = $this->db->sanitize($data['busNumber']);
            $capacity = (int)$data['capacity'];
            $routeCode = isset($data['route']) ? $this->db->sanitize($data['route']) : null;
            $status = isset($data['status']) ? $this->db->sanitize($data['status']) : 'active';
            
            // Check if bus number already exists
            if ($this->busExists($busNumber)) {
                return [
                    'success' => false,
                    'message' => 'Bus number already exists'
                ];
            }
            
            // Insert bus into database
            $stmt = $this->conn->prepare(
                "INSERT INTO buses (bus_number, capacity, route_code, status, created_at) 
                 VALUES (?, ?, ?, ?, NOW())"
            );
            
            $stmt->bind_param("siss", $busNumber, $capacity, $routeCode, $status);
            
            if ($stmt->execute()) {
                $busId = $this->conn->insert_id;
                $stmt->close();
                
                return [
                    'success' => true,
                    'message' => 'Bus added successfully',
                    'bus_id' => $busId
                ];
            } else {
                $stmt->close();
                throw new Exception($this->conn->error);
            }
            
        } catch (Exception $e) {
            error_log("Bus creation error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to add bus'
            ];
        }
    }
    
    /**
     * Update bus information
     * @param int $busId Bus ID
     * @param array $data Updated data
     * @return array Response with success status
     */
    public function update($busId, $data) {
        try {
            $busId = $this->db->sanitize($busId);
            $updates = [];
            $params = [];
            $types = '';
            
            // Build dynamic update query
            if (isset($data['capacity'])) {
                $updates[] = "capacity = ?";
                $params[] = (int)$data['capacity'];
                $types .= 'i';
            }
            
            if (isset($data['route'])) {
                $updates[] = "route_code = ?";
                $params[] = $this->db->sanitize($data['route']);
                $types .= 's';
            }
            
            if (isset($data['status'])) {
                $updates[] = "status = ?";
                $params[] = $this->db->sanitize($data['status']);
                $types .= 's';
            }
            
            if (isset($data['driverId'])) {
                $updates[] = "driver_id = ?";
                $params[] = (int)$data['driverId'];
                $types .= 'i';
            }
            
            if (empty($updates)) {
                return [
                    'success' => false,
                    'message' => 'No fields to update'
                ];
            }
            
            $updates[] = "updated_at = NOW()";
            $params[] = $busId;
            $types .= 'i';
            
            $sql = "UPDATE buses SET " . implode(', ', $updates) . " WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            
            if ($stmt->execute()) {
                $stmt->close();
                return [
                    'success' => true,
                    'message' => 'Bus updated successfully'
                ];
            } else {
                $stmt->close();
                throw new Exception($this->conn->error);
            }
            
        } catch (Exception $e) {
            error_log("Bus update error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to update bus'
            ];
        }
    }
    
    /**
     * Delete bus (soft delete)
     * @param int $busId Bus ID
     * @return array Response with success status
     */
    public function delete($busId) {
        try {
            $busId = $this->db->sanitize($busId);
            
            $stmt = $this->conn->prepare(
                "UPDATE buses SET status = 'deleted', updated_at = NOW() WHERE id = ?"
            );
            
            $stmt->bind_param("i", $busId);
            
            if ($stmt->execute()) {
                $stmt->close();
                return [
                    'success' => true,
                    'message' => 'Bus deleted successfully'
                ];
            } else {
                $stmt->close();
                throw new Exception($this->conn->error);
            }
            
        } catch (Exception $e) {
            error_log("Bus deletion error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to delete bus'
            ];
        }
    }
    
    /**
     * Get all buses
     * @param string $status Filter by status (optional)
     * @return array List of buses
     */
    public function getAll($status = null) {
        $sql = "SELECT b.*, r.route_name, r.origin, r.destination, d.name as driver_name 
                FROM buses b 
                LEFT JOIN routes r ON b.route_id = r.id 
                LEFT JOIN drivers d ON b.driver_id = d.id 
                WHERE b.status != 'deleted'";
        
        if ($status) {
            $status = $this->db->sanitize($status);
            $sql .= " AND b.status = '$status'";
        }
        
        $sql .= " ORDER BY b.bus_number";
        
        $result = $this->conn->query($sql);
        $buses = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $buses[] = $row;
            }
        }
        
        return $buses;
    }
    
    /**
     * Get bus by ID
     * @param int $busId Bus ID
     * @return array|null Bus data or null if not found
     */
    public function getById($busId) {
        $busId = $this->db->sanitize($busId);
        
        $stmt = $this->conn->prepare(
            "SELECT b.*, r.route_name, r.origin, r.destination, d.name as driver_name 
             FROM buses b 
             LEFT JOIN routes r ON b.route_id = r.id 
             LEFT JOIN drivers d ON b.driver_id = d.id 
             WHERE b.id = ?"
        );
        
        $stmt->bind_param("i", $busId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $bus = $result->fetch_assoc();
            $stmt->close();
            return $bus;
        }
        
        $stmt->close();
        return null;
    }
    
    /**
     * Get bus by number
     * @param string $busNumber Bus number
     * @return array|null Bus data or null if not found
     */
    public function getByNumber($busNumber) {
        $busNumber = $this->db->sanitize($busNumber);
        
        $stmt = $this->conn->prepare(
            "SELECT * FROM buses WHERE bus_number = ?"
        );
        
        $stmt->bind_param("s", $busNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $bus = $result->fetch_assoc();
            $stmt->close();
            return $bus;
        }
        
        $stmt->close();
        return null;
    }
    
    /**
     * Check if bus exists
     * @param string $busNumber Bus number
     * @return bool True if exists, false otherwise
     */
    private function busExists($busNumber) {
        $stmt = $this->conn->prepare(
            "SELECT id FROM buses WHERE bus_number = ?"
        );
        
        $stmt->bind_param("s", $busNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        
        return $exists;
    }
    
    /**
     * Get bus locations (for map display)
     * @return array List of bus locations
     */
    public function getLocations() {
        $sql = "SELECT b.id, b.bus_number, b.route_code, r.route_name, 
                b.current_lat, b.current_lng, b.last_update 
                FROM buses b 
                LEFT JOIN routes r ON b.route_code = r.route_code 
                WHERE b.status = 'active'";
        
        $result = $this->conn->query($sql);
        $locations = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Simulate location if no GPS data (for development/testing)
                if (empty($row['current_lat']) || empty($row['current_lng'])) {
                    // Mbarara University coordinates with random offset
                    $row['current_lat'] = -0.6019 + (rand(-100, 100) / 10000);
                    $row['current_lng'] = 30.6574 + (rand(-100, 100) / 10000);
                }
                $locations[] = $row;
            }
        }
        
        return $locations;
    }
    
    /**
     * Update bus location (for GPS tracking)
     * @param int $busId Bus ID
     * @param float $latitude Latitude
     * @param float $longitude Longitude
     * @return array Response with success status
     */
    public function updateLocation($busId, $latitude, $longitude) {
        try {
            $busId = $this->db->sanitize($busId);
            $latitude = (float)$latitude;
            $longitude = (float)$longitude;
            
            $stmt = $this->conn->prepare(
                "UPDATE buses SET current_lat = ?, current_lng = ?, last_update = NOW() WHERE id = ?"
            );
            
            $stmt->bind_param("ddi", $latitude, $longitude, $busId);
            
            if ($stmt->execute()) {
                $stmt->close();
                return [
                    'success' => true,
                    'message' => 'Location updated successfully'
                ];
            } else {
                $stmt->close();
                throw new Exception($this->conn->error);
            }
            
        } catch (Exception $e) {
            error_log("Bus location update error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to update location'
            ];
        }
    }
    
    /**
     * Get available buses for a route
     * @param string $routeCode Route code
     * @param string $date Date
     * @param string $time Time
     * @return array List of available buses
     */
    public function getAvailable($routeCode, $date, $time) {
        $routeCode = $this->db->sanitize($routeCode);
        $date = $this->db->sanitize($date);
        $time = $this->db->sanitize($time);
        
        $sql = "SELECT b.id, b.bus_number, b.capacity, 
                (SELECT COUNT(*) FROM bookings bk 
                 WHERE bk.bus_number = b.bus_number 
                 AND bk.date = '$date' 
                 AND bk.time = '$time' 
                 AND bk.status = 'confirmed') as booked_seats 
                FROM buses b 
                WHERE b.route_code = '$routeCode' 
                AND b.status = 'active'";
        
        $result = $this->conn->query($sql);
        $buses = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['available_seats'] = $row['capacity'] - $row['booked_seats'];
                $buses[] = $row;
            }
        }
        
        return $buses;
    }
    
    /**
     * Get bus statistics
     * @return array Bus statistics
     */
    public function getStatistics() {
        $stats = [];
        
        // Total buses
        $result = $this->conn->query("SELECT COUNT(*) as count FROM buses WHERE status != 'deleted'");
        $stats['total'] = $result->fetch_assoc()['count'];
        
        // Active buses
        $result = $this->conn->query("SELECT COUNT(*) as count FROM buses WHERE status = 'active'");
        $stats['active'] = $result->fetch_assoc()['count'];
        
        // Buses in maintenance
        $result = $this->conn->query("SELECT COUNT(*) as count FROM buses WHERE status = 'maintenance'");
        $stats['maintenance'] = $result->fetch_assoc()['count'];
        
        // Inactive buses
        $result = $this->conn->query("SELECT COUNT(*) as count FROM buses WHERE status = 'inactive'");
        $stats['inactive'] = $result->fetch_assoc()['count'];
        
        return $stats;
    }
}
?>

