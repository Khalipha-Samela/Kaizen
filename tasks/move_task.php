<?php
require_once "../config/db.php";

header('Content-Type: application/json');

if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Check both GET and POST
$id = null;
$status = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['task_id']) && is_numeric($_POST['task_id']) ? (int)$_POST['task_id'] : null;
    $status = isset($_POST['status']) ? $_POST['status'] : null;
} else {
    $id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;
    $status = isset($_GET['status']) ? $_GET['status'] : null;
}

// Validate status
$validStatuses = ['todo', 'progress', 'done'];
if (!in_array($status, $validStatuses)) {
    $status = 'todo';
}

if ($id) {
    $stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
}
?>