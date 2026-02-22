<?php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check database connection
if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Get POST data
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$priority = isset($_POST['priority']) ? $_POST['priority'] : 'medium';
$due_date = isset($_POST['due_date']) && !empty($_POST['due_date']) ? $_POST['due_date'] : null;
$status = isset($_POST['status']) ? $_POST['status'] : 'todo';
$tags = isset($_POST['tags']) ? $_POST['tags'] : [];

// Debug log
error_log("Add task - Title: $title, Priority: $priority, Status: $status");

if (empty($title)) {
    echo json_encode(['success' => false, 'error' => 'Title is required']);
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // Insert task
    $stmt = $conn->prepare("INSERT INTO tasks (title, description, priority, due_date, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param("sssss", $title, $description, $priority, $due_date, $status);
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $task_id = $conn->insert_id;
    error_log("Task created with ID: $task_id");
    
    // Handle tags
    if (!empty($tags)) {
        $tag_stmt = $conn->prepare("INSERT INTO task_tags (task_id, tag_id) VALUES (?, ?)");
        
        if (!$tag_stmt) {
            throw new Exception('Tag prepare failed: ' . $conn->error);
        }
        
        foreach ($tags as $tag_id) {
            if (is_numeric($tag_id)) {
                $tag_stmt->bind_param("ii", $task_id, $tag_id);
                if (!$tag_stmt->execute()) {
                    throw new Exception('Tag execute failed: ' . $tag_stmt->error);
                }
            }
        }
        $tag_stmt->close();
    }
    
    $conn->commit();
    echo json_encode(['success' => true, 'task_id' => $task_id]);
    
} catch (Exception $e) {
    $conn->rollback();
    error_log("Error in add_tasks.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

if (isset($stmt)) $stmt->close();
$conn->close();
?>