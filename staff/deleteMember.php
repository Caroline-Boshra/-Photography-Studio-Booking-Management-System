<?php
require_once '../inc/connection.php';
require_once '../inc/header.php';
require_once '../inc/function.php';
require_once '../middleware/verifyToken.php';

$admin_id = $GLOBALS['auth_admin']['id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    msg("Method Not Allowed", 405);
}

$id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
if (!$id) {
    msg("Valid Member ID is required", 400);
}

$stmt = $conn->prepare("SELECT file_path FROM team_members WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    msg("Member not found", 404);
}

$row = $result->fetch_assoc();
$file_path = '../' . $row['file_path'];

$deleteStmt = $conn->prepare("DELETE FROM team_members WHERE id = ?");
$deleteStmt->bind_param("i", $id);

if ($deleteStmt->execute()) {
    if (!empty($row['file_path']) && file_exists($file_path)) {
        unlink($file_path);
    }

    msg("Member deleted successfully", 200);
} else {
    msg("Failed to delete Member", 500);
}