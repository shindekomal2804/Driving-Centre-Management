<?php
/**
 * Save Settings Handler (Admin Only)
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';

// Check if admin is logged in
if (!AuthManager::isAdminLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Admin access required']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

if (empty($input) || !is_array($input)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No settings provided']);
    exit();
}

$errors = [];
$updated_count = 0;

// Process each setting
foreach ($input as $key => $value) {
    // Skip if key is empty or contains invalid characters
    if (empty($key) || !preg_match('/^[a-zA-Z0-9_-]+$/', $key)) {
        $errors[] = "Invalid setting key: $key";
        continue;
    }

    // Check if setting exists
    $existing = getRow("SELECT id FROM site_settings WHERE setting_key = ?", "s", [$key]);

    if ($existing) {
        // Update existing setting
        $query = "UPDATE site_settings SET setting_value = ? WHERE setting_key = ?";
        $stmt = getConnection()->prepare($query);
        $stmt->bind_param("ss", $value, $key);
    } else {
        // Insert new setting
        $query = "INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)";
        $stmt = getConnection()->prepare($query);
        $stmt->bind_param("ss", $key, $value);
    }

    if ($stmt->execute()) {
        $updated_count++;
    } else {
        error_log("Failed to save setting $key: " . $stmt->error);
        $errors[] = "Failed to save setting: $key";
    }
}

if (count($errors) > 0) {
    http_response_code(207); // Multi-status
    echo json_encode([
        'success' => false,
        'message' => "Updated $updated_count settings with " . count($errors) . " errors",
        'errors' => $errors
    ]);
} else {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => "All $updated_count settings saved successfully"
    ]);
}
?>
