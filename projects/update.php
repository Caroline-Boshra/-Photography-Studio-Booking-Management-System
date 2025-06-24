<?php
require_once '../inc/connection.php';
require_once '../inc/header.php';
require_once '../inc/function.php';
require_once '../middleware/verifyToken.php';

$admin_id = $GLOBALS['auth_admin']['id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    msg("Method Not Allowed", 405);
}

$id = intval($_POST['id'] ?? 0);
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
$old = $result->fetch_assoc();
$stmt->close();


$title      = isset($_POST['title']) ? htmlspecialchars(trim($_POST['title'])) : $old['title'];
$media_type = isset($_POST['media_type']) ? htmlspecialchars(trim($_POST['media_type'])) : $old['media_type'];
$file_path  = $old['file_path'];


if (isset($_POST['media_type']) && !in_array($media_type, ['image', 'video'])) {
    msg("Media type must be 'image' or 'video'", 400);
}


if (isset($_FILES['file'])) {
    $file       = $_FILES['file'];
    $fileName   = $file['name'];
    $tmpName    = $file['tmp_name'];
    $fileSizeMB = $file['size'] / (1024 * 1024);
    $error      = $file['error'];
    $ext        = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $newName    = uniqid() . '.' . $ext;

    if ($error !== UPLOAD_ERR_OK) {
        msg("Upload error", 400);
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'mov', 'avi'];
    if (!in_array($ext, $allowedExtensions)) {
        msg("Invalid file extension", 400);
    }

    if ($fileSizeMB > 10) {
        msg("File is too large. Max 10MB allowed", 400);
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $tmpName);
    finfo_close($finfo);

    $allowedMimeTypes = [
        'image/jpeg', 'image/png', 'image/webp',
        'video/mp4', 'video/quicktime', 'video/x-msvideo'
    ];
    if (!in_array($mimeType, $allowedMimeTypes)) {
        msg("Invalid file type", 400);
    }

    $uploadPath = "../uploads/ourProjects/" . $newName;
    $file_path  = "uploads/ourProjects/" . $newName;

    if (!move_uploaded_file($tmpName, $uploadPath)) {
        msg("Failed to upload file", 500);
    }

    
    $oldFile = "../" . $old['file_path'];
    if (file_exists($oldFile)) {
        unlink($oldFile);
    }
}


$stmt = $conn->prepare("UPDATE projects SET title = ?, media_type = ?, file_path = ? WHERE id = ?");
$stmt->bind_param("sssi", $title, $media_type, $file_path, $id);

if ($stmt->execute()) {
    
    $get = $conn->prepare("SELECT * FROM projects WHERE id = ?");
    $get->bind_param("i", $id);
    $get->execute();
    $res = $get->get_result();
    $project = $res->fetch_object();
    $get->close();

    msg("Project updated successfully", 200, $project);
} else {
    msg("Failed to update project", 500);
}