<?php
require_once '../inc/connection.php';
require_once '../inc/header.php';
require_once '../inc/function.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    msg("Method Not Allowed", 405);
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    msg("Invalid project ID", 400);
}

$stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    msg("Project not found", 404);
}

$project = (object) $result->fetch_assoc();
msg("Project fetched successfully", 200, $project);