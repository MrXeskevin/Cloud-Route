<?php
/**
 * Bus Management Endpoint
 * Handles bus coordinates, list, and availability
 */

require_once 'classes/Bus.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';
$bus = new Bus();

// Set header for JSON response
header('Content-Type: application/json');

switch ($action) {
    case 'locations':
        $locations = $bus->getLocations();
        echo json_encode(['success' => true, 'locations' => $locations]);
        break;
        
    case 'list':
        $status = $_GET['status'] ?? null;
        $buses = $bus->getAll($status);
        echo json_encode(['success' => true, 'buses' => $buses]);
        break;
        
    case 'availability':
        $route = $_GET['route'] ?? '';
        $date = $_GET['date'] ?? '';
        $time = $_GET['time'] ?? '';
        
        if (empty($route) || empty($date) || empty($time)) {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            break;
        }
        
        $available = $bus->getAvailable($route, $date, $time);
        echo json_encode(['success' => true, 'buses' => $available]);
        break;
        
    case 'details':
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Bus ID required']);
            break;
        }
        $details = $bus->getById($id);
        echo json_encode(['success' => true, 'bus' => $details]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Undefined action']);
        break;
}
?>
