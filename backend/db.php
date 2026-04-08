<?php
/**
 * Database Connection Helper (Legacy support)
 * Routes connection to the Database Singleton class
 */

require_once 'classes/Database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // For legacy scripts checking $conn
    if (!$conn) {
        die("Connection failed");
    }
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>
