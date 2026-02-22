<?php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

$task_id = isset($_POST['task_id']) ? intval($_POST['task_id']) : 0;
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$priority = isset($_POST['priority']) ? $_POST['priority'] : 'medium';
$due_date = isset($_POST['due_date']) && !empty($_POST['due_date']) ? $_POST['due_date'] : null;
$status = isset($_POST['status']) ? $_POST['status'] : 'todo';
$tags = isset($_POST['tags']) ? $_POST['tags'] : [];

// Debug log
error_log("Edit task - ID: $task_id, Title: $title, Priority: $priority, Status: $status");

if ($task_id === 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid task ID']);
    exit;
}

if (empty($title)) {
    echo json_encode(['success' => false, 'error' => 'Title is required']);
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // Update task
    $stmt = $conn->prepare("UPDATE tasks SET title = ?, description = ?, priority = ?, due_date = ?, status = ? WHERE id = ?");
    
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param("sssssi", $title, $description, $priority, $due_date, $status, $task_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    // Delete existing tags
    $delete_stmt = $conn->prepare("DELETE FROM task_tags WHERE task_id = ?");
    $delete_stmt->bind_param("i", $task_id);
    $delete_stmt->execute();
    $delete_stmt->close();
    
    // Insert new tags
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
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    $conn->rollback();
    error_log("Error in edit_task.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>