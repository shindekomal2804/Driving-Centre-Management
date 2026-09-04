<?php
/**
 * Authentication & Session Management
 * Handles user login, logout, and session validation
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';

class AuthManager {
    
    private $conn;
    
    public function __construct() {
        $this->conn = getConnection();
    }
    
    /**
     * Register a new user
     */
    public function registerUser($name, $email, $phone, $password) {
        // Validate input
        if (empty($name) || empty($email) || empty($phone) || empty($password)) {
            return array('success' => false, 'message' => 'All fields are required');
        }
        
        // Check if email already exists
        $query = "SELECT id FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return array('success' => false, 'message' => 'Email already registered');
        }
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT, array('cost' => PASSWORD_COST));
        
        // Insert user
        $query = "INSERT INTO users (name, email, phone, password, role, status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return array('success' => false, 'message' => 'Database error');
        }
        
        $role = ROLE_USER;
        $status = STATUS_ACTIVE;
        
        $stmt->bind_param("ssssss", $name, $email, $phone, $hashed_password, $role, $status);
        
        if ($stmt->execute()) {
            return array('success' => true, 'message' => 'Registration successful', 'user_id' => $this->conn->insert_id);
        } else {
            error_log("Execute failed: " . $stmt->error);
            return array('success' => false, 'message' => 'Registration failed');
        }
    }
    
    /**
     * Login user
     */
    public function loginUser($email, $password) {
        // Validate input
        if (empty($email) || empty($password)) {
            return array('success' => false, 'message' => 'Email and password required');
        }
        
        // Get user from database
        $query = "SELECT id, name, email, password, role, status FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return array('success' => false, 'message' => 'Invalid email or password');
        }
        
        $user = $result->fetch_assoc();
        
        // Check status
        if ($user['status'] !== STATUS_ACTIVE && $user['status'] !== 'active') {
            return array('success' => false, 'message' => 'Account is inactive or blocked');
        }
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            return array('success' => false, 'message' => 'Invalid email or password');
        }
        
        // Start session
        if (!isset($_SESSION)) {
            session_start();
        }
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['login_time'] = time();
        
        return array('success' => true, 'message' => 'Login successful');
    }
    
    /**
     * Login admin
     */
    public function loginAdmin($username, $password) {
        // Validate input
        if (empty($username) || empty($password)) {
            return array('success' => false, 'message' => 'Username and password required');
        }

        $username = trim($username);
        $password = trim($password);

        $isDefaultAdmin = (
            defined('DEFAULT_ADMIN_USERNAME') && defined('DEFAULT_ADMIN_PASSWORD') &&
            strcasecmp($username, DEFAULT_ADMIN_USERNAME) === 0 &&
            $password === DEFAULT_ADMIN_PASSWORD
        );

        // Get admin from database
        $query = "SELECT id, username, full_name, email, password, role, status FROM admin WHERE LOWER(username) = LOWER(?) OR LOWER(email) = LOWER(?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();

            // Check status
            if ($admin['status'] !== 'active') {
                return array('success' => false, 'message' => 'Admin account is inactive');
            }

            // Verify password or default fallback
            if (!password_verify($password, $admin['password']) && !$isDefaultAdmin) {
                return array('success' => false, 'message' => 'Invalid username or password');
            }

            // Start session
            if (!isset($_SESSION)) {
                session_start();
            }

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_role'] = $admin['role'];
            $_SESSION['admin_login_time'] = time();

            // Update last login
            $query = "UPDATE admin SET last_login = NOW(), ip_address = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unknown';
            $stmt->bind_param("si", $ip, $admin['id']);
            $stmt->execute();

            return array('success' => true, 'message' => 'Admin login successful');
        }

        // Fallback hardcoded admin for initial setup if no admin user exists in database
        if ($isDefaultAdmin) {
            if (!isset($_SESSION)) {
                session_start();
            }

            $_SESSION['admin_id'] = 0;
            $_SESSION['admin_username'] = DEFAULT_ADMIN_USERNAME;
            $_SESSION['admin_name'] = 'Super Admin';
            $_SESSION['admin_email'] = 'admin@driveeasy.com';
            $_SESSION['admin_role'] = 'super_admin';
            $_SESSION['admin_login_time'] = time();

            return array('success' => true, 'message' => 'Admin login successful');
        }

        return array('success' => false, 'message' => 'Invalid username or password');
    }
    
    /**
     * Check if user is logged in
     */
    public static function isUserLoggedIn() {
        if (!isset($_SESSION)) {
            session_start();
        }
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Check if admin is logged in
     */
    public static function isAdminLoggedIn() {
        if (!isset($_SESSION)) {
            session_start();
        }
        return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
    }
    
    /**
     * Get current user session
     */
    public static function getCurrentUser() {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        if (self::isUserLoggedIn()) {
            return array(
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'],
                'email' => $_SESSION['user_email'],
                'role' => $_SESSION['user_role']
            );
        }
        return null;
    }
    
    /**
     * Get current admin session
     */
    public static function getCurrentAdmin() {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        if (self::isAdminLoggedIn()) {
            return array(
                'id' => $_SESSION['admin_id'],
                'username' => $_SESSION['admin_username'],
                'name' => $_SESSION['admin_name'],
                'email' => $_SESSION['admin_email'],
                'role' => $_SESSION['admin_role']
            );
        }
        return null;
    }
    
    /**
     * Logout user
     */
    public static function logoutUser() {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        session_unset();
        session_destroy();
        return true;
    }
    
    /**
     * Check session timeout
     */
    public static function checkSessionTimeout() {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > SESSION_TIMEOUT)) {
            self::logoutUser();
            return false;
        }
        
        // Update session time
        if (isset($_SESSION['login_time'])) {
            $_SESSION['login_time'] = time();
        }
        
        return true;
    }
}

// Start session for every page
if (session_status() === PHP_SESSION_NONE) {
    // Set session timeout before session starts
    ini_set('session.gc_maxlifetime', SESSION_TIMEOUT);
    ini_set('session.cookie_lifetime', SESSION_TIMEOUT);
    ini_set('session.use_strict_mode', 1);
    session_start();
}
?>
