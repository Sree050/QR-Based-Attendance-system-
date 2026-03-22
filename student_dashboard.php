<?php 
session_start(); 
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}
?> 

<!DOCTYPE html> 
<html> 
<head> 
<title>Student Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"> 

<style>
body {
    background-color: #f4f6f9;
    font-family: 'Segoe UI', sans-serif;
}

.navbar-custom {
    background: #1e3c72;
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
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.card-scan { background: linear-gradient(45deg, #007bff, #00c6ff); }
.card-report { background: linear-gradient(45deg, #28a745, #00ff95); }
.card-logout { background: linear-gradient(45deg, #dc3545, #ff4e50); }

.dashboard-card i {
    font-size: 45px;
    margin-bottom: 15px;
}
</style> 
</head> 

<body> 

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom px-4">
    <a class="navbar-brand" href="#">QR Attendance System</a> 
    <div class="ms-auto text-white text-end">
        👨‍🎓 <?php echo $_SESSION['student_name']; ?><br>
        <small><?php echo $_SESSION['student_regno']; ?></small>
    </div>
</nav>

<div class="container py-5"> 
    <h3 class="text-center mb-5">Student Dashboard</h3> 

    <div class="row justify-content-center g-4">

        <!-- Scan QR -->
        <div class="col-lg-3 col-md-6"> 
            <div class="dashboard-card card-scan"
                 onclick="window.location='scan_qr.php'"> 
                <i class="bi bi-camera"></i> 
                <h5>Scan QR</h5> 
                <p>Mark your attendance</p>
            </div> 
        </div> 

        <!-- Attendance Report -->
        <div class="col-lg-3 col-md-6"> 
            <div class="dashboard-card card-report"
                 onclick="window.location='student_attendance.php'"> 
                <i class="bi bi-bar-chart"></i> 
                <h5>Attendance Report</h5> 
                <p>View your attendance</p> 
            </div>
        </div> 

        <!-- Logout -->
       <div class="col-lg-3 col-md-6">
    <div class="dashboard-card card-logout"
         data-bs-toggle="modal"
         data-bs-target="#logoutModal"> 
        <i class="bi bi-box-arrow-right"></i> 
        <h5>Logout</h5> 
        <p>Securely exit</p> 
    </div> 
</div>

    </div> 
</div> 

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Confirm Logout</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body text-center">
        <p>Do you want to logout?</p>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary"
                data-bs-dismiss="modal">
            No
        </button>
        <a href="logout_student.php" class="btn btn-danger">
            Yes, Logout
        </a>
      </div>

    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Confirm Logout</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body text-center">
        <p>Do you want to logout?</p>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary"
                data-bs-dismiss="modal">
            No
        </button>
        <a href="logout_student.php" class="btn btn-danger">
            Yes, Logout
        </a>
      </div>

    </div>
  </div>
</div>
</body> 
</html>