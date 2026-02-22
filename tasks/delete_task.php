<?php
require_once "../config/db.php";

header('Content-Type: application/json');

if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Check both GET and POST for task ID
$id = null;
if (isset($_POST['task_id']) && is_numeric($_POST['task_id'])) {
    $id = (int)$_POST['task_id'];
} elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
} elseif (isset($_GET['task_id']) && is_numeric($_GET['task_id'])) {
    $id = (int)$_GET['task_id'];
}

if ($id) {
    // First delete task_tags (foreign key will handle this with ON DELETE CASCADE if set)
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid ID']);
}

?>