<?php
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
?>

<style>
body {
    margin: 0;
    font-family: 'Segoe UI';
}

.sidebar {
    width: 250px;
    height: 100vh;
    position: fixed;
    background: linear-gradient(180deg, #1e3c72, #2a5298);
    color: white;
    padding-top: 20px;
    transition: 0.3s;
}

.sidebar h4 {
    text-align: center;
    margin-bottom: 30px;
}

.sidebar a {
    display: block;
    padding: 12px 20px;
    color: white;
    text-decoration: none;
    transition: 0.3s;
}

.sidebar a:hover {
    background: rgba(255,255,255,0.15);
    padding-left: 25px;
}

.content {
    margin-left: 250px;
    padding: 30px;
    background: #f4f6f9;
    min-height: 100vh;
}
</style>

<div class="sidebar">
    <h4>Admin</h4>

    <a href="admin_dashboard.php">📊 Dashboard</a>
    <a href="manage_teacher.php">👨‍🏫 Teachers</a>
    <a href="manage_students.php">🎓 Students</a>
    <a href="manage_subjects.php">📚 Subjects</a>
    <a href="assign_subject.php">📝 Assign Subjects</a>
    <a href="global_attendance.php">📈 Reports</a>
    <a href="admin_logout.php">🚪 Logout</a>
</div>