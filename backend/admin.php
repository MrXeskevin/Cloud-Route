<?php
/**
 * Admin Management Endpoint
 * Handles admin authentication, statistics, and system management
 */

require_once 'classes/Admin.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';
$admin = new Admin();

// Set header for JSON response
header('Content-Type: application/json');

switch ($action) {
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $result = $admin->login($username, $password);
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
        break;
        
    case 'stats':
        $stats = $admin->getStatistics();
        $analytics = $admin->getDashboardAnalytics();
        echo json_encode([
            'success' => true, 
            'stats' => $stats,
            'analytics' => $analytics
        ]);
        break;
        
    case 'add_bus':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Bus management usually delegated to Admin class
            // which in turn uses Bus class
            $data = $_POST;
            $result = $admin->addBus($data); // Assuming addBus exists or is handled via Bus class
            echo json_encode($result);
        }
        break;
        
    case 'add_driver':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $result = $admin->addDriver($data);
            echo json_encode($result);
        }
        break;
        
    case 'drivers_list':
        $status = $_GET['status'] ?? null;
        $drivers = $admin->getAllDrivers($status);
        echo json_encode(['success' => true, 'drivers' => $drivers]);
        break;
        
    case 'logout':
        $result = $admin->logout();
        echo json_encode($result);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Undefined action']);
        break;
}
?>
