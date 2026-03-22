<?php
session_start();
include("db.php");

if (!isset($_SESSION['teacher_id'])) {
    exit();
}

$teacher_id = $_SESSION['teacher_id'];
$semester = $_GET['semester'];

$query = "
SELECT s.id, s.subject_name, s.subject_code
FROM teacher_subjects ts
JOIN subjects s ON ts.subject_id = s.id
WHERE ts.teacher_id = '$teacher_id'
AND s.semester = '$semester'
";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {

    echo "<option value=''>Choose Subject</option>";

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value='{$row['id']}'>
        {$row['subject_name']} ({$row['subject_code']})
        </option>";
    }

} else {
    echo "<option value=''>No subjects assigned</option>";
}
?>