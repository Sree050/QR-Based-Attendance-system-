<?php
include("db.php");

$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Check if email already exists
$check = "SELECT * FROM teachers WHERE email='$email'";
$result = mysqli_query($conn, $check);

if(mysqli_num_rows($result) > 0){
    echo "<script>alert('Email already exists'); window.location='teacher_signup.php';</script>";
    exit();
}

$sql = "INSERT INTO teachers (name, email, password)
        VALUES ('$name', '$email', '$password')";

if (mysqli_query($conn, $sql)) {
    echo "<script>alert('Account Created Successfully'); window.location='teacher_login.php';</script>";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>