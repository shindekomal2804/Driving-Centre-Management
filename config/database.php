<?php
/**
 * Database Configuration File
 * Contains connection parameters for MySQL database
 */

// Database constants
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'driving_school');
define('DB_PORT', 3306);

// Create connection using MySQLi
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Database Connection Failed: " . $conn->connect_error);
    }
    
    // Set character set to UTF-8
    if (!$conn->set_charset("utf8mb4")) {
        throw new Exception("Error loading character set utf8mb4: " . $conn->error);
    }
    
} catch (Exception $e) {
    // Log error and display message
    error_log($e->getMessage());
    die("Database connection error. Please try again later.");
}

// Global function to get database connection
function getConnection() {
    global $conn;
    return $conn;
}

// Function to prepare and execute queries securely
function executeQuery($query, $types = "", $params = array()) {
    $conn = getConnection();
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        error_log("Query preparation failed: " . $conn->error);
        return false;
    }
    
    // Bind parameters if provided
    if (!empty($types) && !empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    // Execute query
    if (!$stmt->execute()) {
        error_log("Query execution failed: " . $stmt->error);
        return false;
    }
    
    return $stmt;
}

// Function to get single row result
function getRow($query, $types = "", $params = array()) {
    $stmt = executeQuery($query, $types, $params);
    if (!$stmt) return null;
    
    $result = $stmt->get_result();
    return $result ? $result->fetch_assoc() : null;
}

// Function to get multiple rows result
function getRows($query, $types = "", $params = array()) {
    $stmt = executeQuery($query, $types, $params);
    if (!$stmt) return array();
    
    $result = $stmt->get_result();
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : array();
}

// Function to get last inserted ID
function getLastId() {
    global $conn;
    return $conn->insert_id;
}

// Function to get affected rows
function getAffectedRows() {
    global $conn;
    return $conn->affected_rows;
}
?>
