<?php
/**
 * Issue Reporting Endpoint
 * Handles submission and management of reports
 */

require_once 'classes/Report.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';
$report = new Report();

// Set header for JSON response
header('Content-Type: application/json');

switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'userId' => $_POST['user_id'] ?? '',
                'issueType' => $_POST['issue_type'] ?? '',
                'priority' => $_POST['priority'] ?? '',
                'route' => $_POST['route'] ?? '',
                'busNumber' => $_POST['bus_number'] ?? '',
                'date' => $_POST['date'] ?? '',
                'time' => $_POST['time'] ?? '',
                'location' => $_POST['location'] ?? '',
                'description' => $_POST['description'] ?? '',
                'contactMethod' => $_POST['contact_method'] ?? 'email',
                'anonymous' => isset($_POST['anonymous']) ? true : false
            ];
            
            $result = $report->create($data);
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
        break;
        
    case 'list':
        $userId = $_GET['user_id'] ?? null;
        if ($userId) {
            $status = $_GET['status'] ?? null;
            $reports = $report->getUserReports($userId, $status);
        } else {
            // Admin list or general (if auth allowed)
            $filters = [
                'status' => $_GET['status'] ?? null,
                'priority' => $_GET['priority'] ?? null,
                'issueType' => $_GET['issue_type'] ?? null,
                'route' => $_GET['route'] ?? null,
                'busNumber' => $_GET['bus_number'] ?? null
            ];
            $reports = $report->getAll($filters);
        }
        echo json_encode(['success' => true, 'reports' => $reports]);
        break;
        
    case 'details':
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Report ID required']);
            break;
        }
        $details = $report->getById($id);
        echo json_encode(['success' => true, 'report' => $details]);
        break;
        
    case 'resolve':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reportId = $_POST['report_id'] ?? '';
            $resolution = $_POST['resolution'] ?? null;
            
            if (empty($reportId)) {
                echo json_encode(['success' => false, 'message' => 'Report ID required']);
                break;
            }
            
            $result = $report->updateStatus($reportId, 'resolved', $resolution);
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Undefined action']);
        break;
}
?>
