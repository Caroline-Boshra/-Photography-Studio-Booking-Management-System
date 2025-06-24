<?php
require_once '../inc/connection.php';
require_once '../inc/header.php';
require_once '../inc/function.php';
require_once '../middleware/verifyToken.php'; 

$admin_id = $GLOBALS['auth_admin']['id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    msg("Method Not Allowed", 405);
}

$name  = htmlspecialchars(trim($_POST['name'] ?? ''));
$email = htmlspecialchars(trim($_POST['email'] ?? ''));
$role  = htmlspecialchars(trim($_POST['role'] ?? ''));

if (empty($name))   msg("Name is required", 400);
if (empty($email))  msg("Email is required", 400);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) msg("Invalid email format", 400);
if (empty($role))   msg("Role is required", 400);

if (!isset($_FILES['image'])) {
    msg("Image is required", 400);
}

$image       = $_FILES['image'];
$imageName   = $image['name'];
$tmpName     = $image['tmp_name'];
$imageSizeMB = $image['size'] / (1024 * 1024);
$error       = $image['error'];
$ext         = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
$newName     = uniqid() . '.' . $ext;

if ($error !== UPLOAD_ERR_OK) {
    msg("Upload error", 400);
}

$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
if (!in_array($ext, $allowedExtensions)) {
    msg("Invalid image extension", 400);
}

if ($imageSizeMB > 5) {
    msg("Image is too large. Max 5MB allowed", 400);
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $tmpName);
finfo_close($finfo);

$allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
if (!in_array($mimeType, $allowedMimeTypes)) {
    msg("Invalid image type", 400);
}

$uploadPath = "../uploads/ourTeam/" . $newName;
$image_path = "uploads/ourTeam/" . $newName;

if (!move_uploaded_file($tmpName, $uploadPath)) {
    msg("Failed to upload image", 500);
}

$stmt = $conn->prepare("INSERT INTO team_members (name, email, role, image) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $role, $image_path);

if ($stmt->execute()) {
    $team_member = (object)[
        "id"    => $stmt->insert_id,
        "name"  => $name,
        "email" => $email,
        "image" => $image_path
    ];

    msg("Team member added successfully", 200, $team_member);
} else {
    unlink($uploadPath);
    msg("Database error", 500);
}