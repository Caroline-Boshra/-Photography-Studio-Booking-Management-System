<?php
require_once '../vendor/autoload.php';
require_once '../inc/function.php';
require_once '../inc/config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    msg("Authorization header not found", 401);
}


$authHeader = $headers['Authorization'];
if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    msg("Invalid Authorization header format", 401);
}

$token = $matches[1];

try {
  
    $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));

   
    $GLOBALS['auth_admin'] = [
        "id" => $decoded->admin_id,
        "email" => $decoded->email,
        "name" => $decoded->name
    ];

} catch (Exception $e) {
    msg("Invalid or expired token", 401);
}