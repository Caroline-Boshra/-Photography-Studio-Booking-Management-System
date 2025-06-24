<?php 
require_once '../inc/connection.php';
require_once '../inc/header.php';
require_once '../inc/function.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    msg("Method Not Allowed", 405);
}

$name     = trim(htmlspecialchars($_POST['name'] ?? ''));
$email    = trim(htmlspecialchars($_POST['email'] ?? ''));
$password = trim(htmlspecialchars($_POST['password'] ?? ''));

if (empty($name)) {
    msg("Name is required", 400);
}
if (empty($email)) {
    msg("Email is required", 400);
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    msg("Invalid email format", 400);
}
if (empty($password)) {
    msg("Please enter your password", 400);
} elseif (strlen($password) < 8) {
    msg("Password must be at least 8 characters", 400);
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$Query = "SELECT * FROM admins WHERE email = ?";
$stmt = $conn->prepare($Query);
$stmt->bind_param("s", $email);
$GLOBALS['stmt'] = $stmt;
$GLOBALS['conn'] = $conn;
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows > 0) {
    msg("Email already exists", 400);
}

$insertQuery = "INSERT INTO admins (name, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($insertQuery);
$stmt->bind_param("sss", $name, $email, $hashedPassword);
$GLOBALS['stmt'] = $stmt;

if ($stmt->execute()) {
    msg("Admin registered successfully", 200);
} else {
    msg("Something went wrong", 500);
}