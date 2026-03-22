<?php
session_start();
if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacher_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Teacher Dashboard</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #1e3c72, #2a5298);
    font-family: 'Segoe UI';
}

.dashboard-card {
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    color: white;
    transition: 0.3s ease;
    cursor: pointer;
}

.dashboard-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
}

.card-qr { background: linear-gradient(45deg, #007bff, #00c6ff); }
.card-report { background: linear-gradient(45deg, #28a745, #00ff95); }
.card-profile { background: linear-gradient(45deg, #ff8c00, #ffb347); }
.card-logout { background: linear-gradient(45deg, #dc3545, #ff4e50); }

.dashboard-card i {
    font-size: 45px;
    margin-bottom: 15px;
}
</style>
</head>

<body>

<nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand">QR Attendance System</span>
    <span class="text-white">
        👨‍🏫 <?php echo $_SESSION['teacher_name']; ?>
    </span>
</nav>

<div class="container py-5">

<h3 class="text-center text-white mb-5">Teacher Dashboard</h3>

<div class="row justify-content-center g-4">

<div class="col-lg-3 col-md-6">
<div class="dashboard-card card-qr"
onclick="window.location='teacher_setup.php'">
<i class="bi bi-qr-code"></i>
<h5>Generate QR</h5>
<p>Create attendance session</p>
</div>
</div>

<div class="col-lg-3 col-md-6">
<div class="dashboard-card card-report"
onclick="window.location='attendance_report.php'">
<i class="bi bi-bar-chart"></i>
<h5>Attendance Report</h5>
<p>View attendance details</p>
</div>
</div>

<div class="col-lg-3 col-md-6">
<div class="dashboard-card card-profile"
onclick="window.location='teacher_profile.php'">
<i class="bi bi-person-circle"></i>
<h5>Profile</h5>
<p>Manage account</p>
</div>
</div>

<div class="col-lg-3 col-md-6">
<div class="dashboard-card card-logout"
onclick="confirmLogout()">
<i class="bi bi-box-arrow-right"></i>
<h5>Logout</h5>
<p>Secure exit</p>
</div>
</div>

</div>

</div>

<script>
function confirmLogout() {
    if (confirm("Do you want to logout?")) {
        window.location = "logout.php";
    }
}
</script>

</body>
</html>