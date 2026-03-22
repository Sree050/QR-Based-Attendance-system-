<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("db.php");

if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
?>

<!DOCTYPE html>
<html>
<head>
<title>My Attendance</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #1e3c72, #2a5298);
    font-family: 'Segoe UI', sans-serif;
}
.card {
    background: rgba(255,255,255,0.95);
    border-radius: 15px;
}
</style>
</head>

<body class="p-5">

<div class="container">

<div class="card p-4 shadow">

<h4 class="mb-4 text-center">My Attendance Report</h4>

<table class="table table-bordered text-center">
<thead class="table-dark">
<tr>
    <th>Subject</th>
    <th>Code</th>
    <th>Total Classes</th>
    <th>Present</th>
    <th>Percentage</th>
    <th>Status</th>
</tr>
</thead>
<tbody>

<?php

$query = "
SELECT s.id, s.subject_name, s.subject_code
FROM subjects s
JOIN session_qr sq ON s.id = sq.subject_id
GROUP BY s.id
";

$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {

    $subject_id = $row['id'];

    // Total sessions conducted
    $total_query = "
    SELECT COUNT(*) as total
    FROM session_qr
    WHERE subject_id = '$subject_id'
    ";
    $total_result = mysqli_query($conn, $total_query);
    $total_classes = mysqli_fetch_assoc($total_result)['total'];

    // Present count
    $present_query = "
    SELECT COUNT(*) as total
    FROM attendance a
    JOIN session_qr sq ON a.session_id = sq.id
    WHERE a.student_id = '$student_id'
    AND sq.subject_id = '$subject_id'
    ";
    $present_result = mysqli_query($conn, $present_query);
    $present = mysqli_fetch_assoc($present_result)['total'];

    $percentage = 0;
    if ($total_classes > 0) {
        $percentage = round(($present / $total_classes) * 100, 2);
    }

    $status = $percentage < 75
        ? "<span class='badge bg-danger'>Below 75%</span>"
        : "<span class='badge bg-success'>Good</span>";

    echo "<tr>
            <td>{$row['subject_name']}</td>
            <td>{$row['subject_code']}</td>
            <td>$total_classes</td>
            <td>$present</td>
            <td>$percentage%</td>
            <td>$status</td>
          </tr>";
}

?>

</tbody>
</table>

<a href="student_dashboard.php" class="btn btn-dark mt-3">
Back to Dashboard
</a>

</div>
</div>

</body>
</html>