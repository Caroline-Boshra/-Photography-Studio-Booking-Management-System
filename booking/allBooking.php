<?php
require_once '../inc/connection.php';
require_once '../inc/function.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    msg("Method Not Allowed", 405);
}

$query = "SELECT * FROM bookings ORDER BY created_at DESC";
$result = $conn->query($query);

$rows = $result->fetch_all(MYSQLI_ASSOC);

$bookings = [];
foreach ($rows as $row) {
    $bookings[] = (object) $row;
}

msg("bookings fetched successfully", 200, $bookings);