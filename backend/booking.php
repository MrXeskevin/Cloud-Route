<?php
/**
 * Booking Management Endpoint
 * Handles seat reservations, cancellations, and status
 */

require_once 'classes/Booking.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';
$booking = new Booking();

// Set header for JSON response
header('Content-Type: application/json');

switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'userId' => $_POST['user_id'] ?? '',
                'bus' => $_POST['bus'] ?? '',
                'route' => $_POST['route'] ?? '',
                'date' => $_POST['date'] ?? '',
                'time' => $_POST['time'] ?? '',
                'pickup' => $_POST['pickup'] ?? '',
                'seat' => $_POST['seat'] ?? ''
            ];
            
            $result = $booking->create($data);
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
        break;
        
    case 'cancel':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingId = $_POST['booking_id'] ?? '';
            $userId = $_POST['user_id'] ?? null;
            
            if (empty($bookingId)) {
                echo json_encode(['success' => false, 'message' => 'Booking ID required']);
                break;
            }
            
            $result = $booking->cancel($bookingId, $userId);
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
        break;
        
    case 'list':
        $userId = $_GET['user_id'] ?? '';
        if (empty($userId)) {
            echo json_encode(['success' => false, 'message' => 'User ID required']);
            break;
        }
        $status = $_GET['status'] ?? null;
        $bookings = $booking->getUserBookings($userId, $status);
        echo json_encode(['success' => true, 'bookings' => $bookings]);
        break;
        
    case 'booked_seats':
        $bus = $_GET['bus'] ?? '';
        $date = $_GET['date'] ?? '';
        $time = $_GET['time'] ?? '';
        
        if (empty($bus) || empty($date) || empty($time)) {
            echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
            break;
        }
        
        $seats = $booking->getBookedSeats($bus, $date, $time);
        echo json_encode(['success' => true, 'seats' => $seats]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Undefined action']);
        break;
}
?>
