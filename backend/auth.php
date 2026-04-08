<?php
/**
 * Authentication Endpoint
 * Handles login, signup, and logout actions
 */

require_once 'classes/User.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';
$user = new User();

// Set header for JSON response
header('Content-Type: application/json');

switch ($action) {
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = isset($_POST['username']) ? $_POST['username'] : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            
            $result = $user->login($username, $password);
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
        break;
        
    case 'signup':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'username' => $_POST['username'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'password' => $_POST['password'] ?? ''
            ];
            
            $result = $user->signup($data);
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
        break;
        
    case 'logout':
        $result = $user->logout();
        echo json_encode($result);
        break;
        
    case 'check':
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => true, 
                'authenticated' => true,
                'user' => [
                    'id' => $_SESSION['user_id'],
                    'username' => $_SESSION['username'],
                    'name' => $_SESSION['name'],
                    'user_type' => $_SESSION['user_type']
                ]
            ]);
        } else {
            echo json_encode(['success' => true, 'authenticated' => false]);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Undefined action']);
        break;
}
?>
