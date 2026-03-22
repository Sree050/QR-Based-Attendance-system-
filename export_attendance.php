<?php
session_start();
include("db.php");

if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacher_login.php");
    exit();
}

$subject_id = $_POST['subject_id'];
$date = $_POST['date'];
$teacher_id = $_SESSION['teacher_id'];

/* Get session */
$session_query = "
SELECT sq.id, sq.hour, s.subject_name, s.subject_code
FROM session_qr sq
JOIN subjects s ON sq.subject_id = s.id
WHERE sq.teacher_id='$teacher_id'
AND sq.subject_id='$subject_id'
AND DATE(sq.created_at)='$date'
";

$session_result = mysqli_query($conn, $session_query);
$session = mysqli_fetch_assoc($session_result);

$session_id = $session['id'];
$hour = $session['hour'];
$subject_name = $session['subject_name'];
$subject_code = $session['subject_code'];

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Attendance_Report.xls");

echo "Subject Code\tSubject Name\tDate\tDay\tHour\tRegister No\tStudent Name\tStatus\n";

$day = date('l', strtotime($date));

/* Get all students of that semester */
$semester_query = "SELECT semester FROM subjects WHERE id='$subject_id'";
$semester_result = mysqli_query($conn,$semester_query);
$semester = mysqli_fetch_assoc($semester_result)['semester'];

$students_query = "SELECT * FROM students WHERE year='$semester'";
$students = mysqli_query($conn,$students_query);

while ($student = mysqli_fetch_assoc($students)) {

    $student_id = $student['id'];
    $register_no = $student['register_no'];
    $name = $student['name'];

    $check_query = "
    SELECT * FROM attendance
    WHERE session_id='$session_id'
    AND student_id='$student_id'
    ";

    $check = mysqli_query($conn,$check_query);

    $status = (mysqli_num_rows($check) > 0) ? "Present" : "Absent";

    echo "$subject_code\t$subject_name\t$date\t$day\t$hour\t$register_no\t$name\t$status\n";
}
?>