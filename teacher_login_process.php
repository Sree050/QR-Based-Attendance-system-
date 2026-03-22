<?php
session_start();
include("db.php");
$email = $_POST['email'];
$password = $_POST['password'];
$sql = "SELECT * FROM teachers WHERE email='$email'";
$result = mysqli_query($conn, $sql);
if(mysqli_num_rows($result) == 1){
    $row = mysqli_fetch_assoc($result);
    if(password_verify($password, $row['password'])){
        $_SESSION['teacher_id'] = $row['id'];
        $_SESSION['teacher_name'] = $row['name'];
        header("Location: teacher_dashboard.php");
        exit();
    } else {
        echo "<script>alert('Wrong Password'); window.location='teacher_login.php';</script>";
    }
} else {
    echo "<script>alert('User Not Found'); window.location='teacher_login.php';</script>";
}
$check = mysqli_query($conn, 
    "SELECT * FROM teacher_subjects WHERE teacher_id='{$row['id']}'");

if(mysqli_num_rows($check) == 0){
    header("Location: teacher_setup.php");
    exit();
}
?>