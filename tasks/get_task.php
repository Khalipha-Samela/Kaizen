<?php
session_start();
require_once "../config/db.php";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check database connection
if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Get task ID from query string
if (!isset($_GET['task_id']) || !is_numeric($_GET['task_id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid task ID']);
    exit;
}

$task_id = intval($_GET['task_id']);

// Get task details with tags
$stmt = $conn->prepare("
    SELECT t.*, 
           GROUP_CONCAT(tt.tag_id) as tag_ids
    FROM tasks t
    LEFT JOIN task_tags tt ON t.id = tt.task_id
    WHERE t.id = ?
    GROUP BY t.id
");

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Database prepare error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $task_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Task not found']);
    $stmt->close();
    $conn->close();
    exit;
}

$task = $result->fetch_assoc();

// Format response
$response = [
    'success' => true,
    'task' => [
        'id' => $task['id'],
        'title' => $task['title'],
        'description' => $task['description'],
        'priority' => $task['priority'],
        'due_date' => $task['due_date'],
        'status' => $task['status'],
        'tags' => $task['tag_ids'] ? explode(',', $task['tag_ids']) : []
    ]
];

echo json_encode($response);
$stmt->close();
$conn->close();
?>