<?php
require_once '../inc/connection.php';
require_once '../inc/header.php';
require_once '../inc/function.php';
require_once '../inc/config.php';
require_once '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    msg("Method Not Allowed", 405);
}

$email    = trim(htmlspecialchars($_POST['email'] ?? ''));
$password = trim(htmlspecialchars($_POST['password'] ?? ''));

if (empty($email)) {
    msg("Email is required", 400);
}elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    msg("Invalid email format", 400);
}
if (empty($password)) {
    msg("Password is required", 400);
}


$query = "SELECT * FROM admins WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$GLOBALS['stmt'] = $stmt;
$GLOBALS['conn'] = $conn;
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    msg("Invalid email or password", 401);
}

$admin = $result->fetch_assoc();


if (!password_verify($password, $admin['password'])) {
    msg("Invalid email or password", 401);
}


$payload = [
    "admin_id" => $admin['id'],
    "email"    => $admin['email'],
    "name"     => $admin['name'],
    "iat"      => time(),                           
    "exp"      => time() + (7 * 24 * 60 * 60)       
];

$jwt = JWT::encode($payload, $secret_key, 'HS256');


msg("Login successful", 200, ["token" => $jwt]);