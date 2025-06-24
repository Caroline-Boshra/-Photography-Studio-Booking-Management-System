<?php
require_once '../inc/connection.php';
require_once '../inc/header.php';
require_once '../inc/function.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    msg("Method Not Allowed", 405);
}

$query = "SELECT * FROM projects ORDER BY uploaded_at DESC";
$result = $conn->query($query);

$rows = $result->fetch_all(MYSQLI_ASSOC);

$projects = [];
foreach ($rows as $row) {
    $projects[] = (object) $row;
}

msg("Projects fetched successfully", 200, $projects);