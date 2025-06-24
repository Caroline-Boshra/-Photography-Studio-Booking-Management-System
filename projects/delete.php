<?php
require_once '../inc/connection.php';
require_once '../inc/header.php';
require_once '../inc/function.php';
require_once '../middleware/verifyToken.php';

$admin_id = $GLOBALS['auth_admin']['id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    msg("Method Not Allowed", 405);
}

$id = intval($_POST['id'] ?? 0);

if (!$id) {
    msg("Project ID is required", 400);
}


$stmt = $conn->prepare("SELECT file_path FROM projects WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    msg("Project not found", 404);
}

$row = $result->fetch_assoc();
$file_path = '../' . $row['file_path']; 


$deleteStmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
$deleteStmt->bind_param("i", $id);

if ($deleteStmt->execute()) {

    if (file_exists($file_path)) {
        unlink($file_path);
    }

    msg("Project deleted successfully", 200);
} else {
    msg("Failed to delete project", 500);
}