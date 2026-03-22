<?php
session_start();
include("db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$report_generated = false;
$selected_date = "";

if (isset($_POST['view_report'])) {

    $selected_date = $_POST['date'];

    $query = "
    SELECT 
        s.subject_name,
        s.subject_code,
        s.semester,
        t.name AS teacher_name,
        sq.id AS session_id
    FROM session_qr sq
    JOIN subjects s ON sq.subject_id = s.id
    JOIN teachers t ON sq.teacher_id = t.id
    WHERE DATE(sq.created_at) = '$selected_date'
    ";

    $result = mysqli_query($conn, $query);
    $report_generated = true;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Global Attendance Report</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<?php include("admin_layout.php"); ?>

<div class="content">

<h2 class="mb-4">Global Attendance Report</h2>

<div class="card p-4 shadow mb-4">

<form method="POST" class="row g-3">

<div class="col-md-4">
<label>Select Date</label>
<input type="date" name="date" class="form-control" required>
</div>

<div class="col-md-3 align-self-end">
<button name="view_report" class="btn btn-primary">
View Report
</button>
</div>

</form>

</div>

<?php if ($report_generated): ?>

<div class="card p-4 shadow">

<h5>Date: <?php echo $selected_date; ?></h5>

<table class="table table-bordered table-striped mt-3">
<thead class="table-dark">
<tr>
<th>Subject</th>
<th>Code</th>
<th>Teacher</th>
<th>Total Students</th>
<th>Present</th>
<th>Absent</th>
<th>Attendance %</th>
</tr>
</thead>

<tbody>

<?php
while ($row = mysqli_fetch_assoc($result)) {

    $session_id = $row['session_id'];
    $semester = $row['semester'];

    // Total Students (based on year)
    $total_query = "
    SELECT COUNT(*) as total
    FROM students
    WHERE year = '$semester'
    ";
    $total_students = mysqli_fetch_assoc(mysqli_query($conn,$total_query))['total'];

    // Present Count
    $present_query = "
    SELECT COUNT(*) as total
    FROM attendance
    WHERE session_id = '$session_id'
    ";
    $present = mysqli_fetch_assoc(mysqli_query($conn,$present_query))['total'];

    $absent = $total_students - $present;

    $percentage = 0;
    if ($total_students > 0) {
        $percentage = round(($present / $total_students) * 100, 2);
    }

    echo "<tr>
        <td>{$row['subject_name']}</td>
        <td>{$row['subject_code']}</td>
        <td>{$row['teacher_name']}</td>
        <td>$total_students</td>
        <td class='text-success fw-bold'>$present</td>
        <td class='text-danger fw-bold'>$absent</td>
        <td>$percentage%</td>
    </tr>";
}
?>

</tbody>
</table>

</div>

<?php endif; ?>

</div>

</body>
</html>