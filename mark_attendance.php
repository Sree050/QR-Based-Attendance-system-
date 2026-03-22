<?php
session_start();
include("db.php");

if (!isset($_SESSION['student_id'])) {
    exit("Unauthorized Access");
}

$student_id = $_SESSION['student_id'];

if (!isset($_POST['qr_data'])) {
    exit("Invalid QR Data");
}

$qr = $_POST['qr_data'];

/* Validate QR Format */
if (strpos($qr, "|") === false) {
    exit("Invalid QR Format");
}

list($session_id, $token) = explode("|", $qr);

/* Validate Active Session PROPERLY */
$query = "
SELECT * FROM session_qr
WHERE id='$session_id'
AND token='$token'
AND expiry_time > NOW()
";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    exit("QR Expired or Invalid");
}

/* Check Duplicate Attendance */
$check = "
SELECT id FROM attendance
WHERE session_id='$session_id'
AND student_id='$student_id'
";

$check_result = mysqli_query($conn, $check);

if (mysqli_num_rows($check_result) > 0) {
    exit("Attendance Already Marked");
}

/* Insert Attendance */
$insert = "
INSERT INTO attendance (session_id, student_id, marked_at)
VALUES ('$session_id', '$student_id', NOW())
";

if (mysqli_query($conn, $insert)) {
    echo "Attendance Updated Successfully!";
} else {
    echo "Error Marking Attendance";
}
?>