<?php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check database connection
if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Get POST data (handle both JSON and form-data)
$input = file_get_contents('php://input');
$data = [];

if (!empty($input)) {
    // Try to parse as JSON
    $jsonData = json_decode($input, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $data = $jsonData;
    }
}

// If not JSON, try form data
if (empty($data)) {
    $data['name'] = $_POST['name'] ?? '';
    $data['color'] = $_POST['color'] ?? '#6366f1';
}

// Validate input
if (!isset($data['name']) || empty(trim($data['name']))) {
    echo json_encode(['success' => false, 'error' => 'Tag name is required']);
    exit;
}

$name = trim($data['name']);
$color = isset($data['color']) ? $data['color'] : '#6366f1';

// Validate color format
if (!preg_match('/^#[a-f0-9]{6}$/i', $color)) {
    $color = '#6366f1';
}

// Check if tag already exists
$checkStmt = $conn->prepare("SELECT id FROM tags WHERE name = ?");
$checkStmt->bind_param("s", $name);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    echo json_encode(['success' => false, 'error' => 'Tag already exists']);
    $checkStmt->close();
    exit;
}
$checkStmt->close();

// Insert new tag
$stmt = $conn->prepare("INSERT INTO tags (name, color) VALUES (?, ?)");
$stmt->bind_param("ss", $name, $color);

if ($stmt->execute()) {
    $tagId = $stmt->insert_id;
    echo json_encode([
        'success' => true, 
        'tag_id' => $tagId,
        'name' => $name,
        'color' => $color
    ]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$stmt->close();

?>