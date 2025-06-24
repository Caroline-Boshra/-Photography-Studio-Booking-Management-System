<?php

require_once 'connection.php';

$createAdmins = "CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255)
)";

$createBookings = "CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    session_date DATE,
    session_time TIME,
    message TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    admin_id INT,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL
)";

$createContact_messages = "CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100),
    email VARCHAR(100),
    message TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)";

$createTeam_members = "CREATE TABLE IF NOT EXISTS team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    role VARCHAR(255),
    image VARCHAR(255)
)";

$createProjects = "CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100),
    media_type ENUM('image', 'video') NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    admin_id INT,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL
)";


$conn->query($createAdmins);
$conn->query($createBookings);
$conn->query($createContact_messages); 
$conn->query($createTeam_members);
$conn->query($createProjects);

echo "Tables added successfully";

?>