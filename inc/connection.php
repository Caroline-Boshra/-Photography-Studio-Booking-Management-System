<?php 


$conn=mysqli_connect("localhost","root","","traffic_studio");


if ($conn->connect_error) {
    die(" connection failed: " . $conn->connect_error);
}