<?php
session_start();
include("db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$teachers = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM teachers"))['total'];
$students = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM students"))['total'];
$subjects = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM subjects"))['total'];
$sessions = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM session_qr"))['total'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include("admin_layout.php"); ?>

<div class="content">

<h2 class="mb-4">Dashboard Overview</h2>

<div class="row g-4">

<div class="col-md-3">
<div class="card shadow p-4 text-center">
<h3><?php echo $teachers; ?></h3>
<p>Total Teachers</p>
</div>
</div>

<div class="col-md-3">
<div class="card shadow p-4 text-center">
<h3><?php echo $students; ?></h3>
<p>Total Students</p>
</div>
</div>

<div class="col-md-3">
<div class="card shadow p-4 text-center">
<h3><?php echo $subjects; ?></h3>
<p>Total Subjects</p>
</div>
</div>

<div class="col-md-3">
<div class="card shadow p-4 text-center">
<h3><?php echo $sessions; ?></h3>
<p>Total Sessions</p>
</div>
</div>

</div>

</div>

</body>
</html>