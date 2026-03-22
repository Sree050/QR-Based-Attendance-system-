<?php
$host = "localhost";
$user = "root";
$password = "sree50";  // Important
$database = "qr_attendance";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
