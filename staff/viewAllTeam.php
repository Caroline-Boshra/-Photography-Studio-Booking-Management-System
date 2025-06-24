<?php
require_once '../inc/connection.php';
require_once '../inc/header.php';
require_once '../inc/function.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    msg("Method Not Allowed", 405);
}

$query = "SELECT id, name, email, role, file_path FROM team_members ORDER BY id DESC";
$result = $conn->query($query);

$rows = $result->fetch_all(MYSQLI_ASSOC);

$members = [];
foreach ($rows as $row) {
    $members[] = (object) $row;
}

msg("Team members fetched successfully", 200, $members);