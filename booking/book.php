<?php
require_once '../inc/connection.php';
require_once '../inc/function.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    msg("Method Not Allowed", 405);
}

$name=htmlspecialchars(trim($_POST['name'] ?? ''));
$email=htmlspecialchars(trim($_POST['email'] ?? ''));
$phone=htmlspecialchars(trim($_POST['phone'] ?? ''));
$date=htmlspecialchars(trim($_POST['session_date'] ?? ''));
$time=htmlspecialchars(trim($_POST['session_time'] ?? ''));
$message=htmlspecialchars(trim($_POST['message'] ?? ''));

if (empty($name)) {
    msg("Name is required", 400);
}


if (empty($email)) {
    msg("Email is required", 400);
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    msg("This email is not valid", 400);
}

if (empty($phone)) {
    msg("phone is required", 400);
} elseif (! is_string($phone)) {
    msg("Invalid phone number ", 400);
}elseif (strlen($phone)<11) {
    msg("the number must be 11 chars", 400);
}

if (empty($date)) {
    msg("Session date is required", 400);
}

if (empty($time)) {
    msg("Session time is required", 400);
}


$query = "INSERT INTO bookings (client_name, email, phone, session_date, session_time, message) VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($query);
$stmt->bind_param("ssssss", $name, $email, $phone, $date, $time, $message);

if ($stmt->execute()) {
    $booking_id = $stmt->insert_id;

    $get = $conn->prepare("SELECT * FROM bookings WHERE id = ?");
    $get->bind_param("i", $booking_id);
    $get->execute();
    $result = $get->get_result();
    $booking = $result->fetch_object();
    $get->close();

    msg("Booking submitted successfully", 200, $booking);
} else {
    msg("Failed to submit booking", 500);
}