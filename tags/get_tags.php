<?php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Cache-Control: max-age=300'); // Cache for 5 minutes

// Add pagination for large tag sets
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$result = $conn->query("SELECT * FROM tags ORDER BY name LIMIT $offset, $limit");

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

$result = $conn->query("SELECT * FROM tags ORDER BY name");

if (!$result) {
    echo json_encode(['success' => false, 'error' => $conn->error]);
    exit;
}

$tags = [];
while ($row = $result->fetch_assoc()) {
    $tags[] = $row;
}

echo json_encode(['success' => true, 'tags' => $tags]);
$conn->close();
?>