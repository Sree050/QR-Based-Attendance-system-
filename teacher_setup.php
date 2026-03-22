<?php
date_default_timezone_set('Asia/Kolkata');

session_start();
include("db.php");

if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacher_login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

if (isset($_POST['generate_qr'])) {

    $semester = $_POST['semester'];
    $subject_id = $_POST['subject_id'];
    $hour = $_POST['hour'];

    $token = bin2hex(random_bytes(5));

    mysqli_query($conn,
        "INSERT INTO session_qr
        (teacher_id, subject_id, token, expiry_time, created_at, hour)
        VALUES
        ('$teacher_id', '$subject_id', '$token',
         DATE_ADD(NOW(), INTERVAL 2 MINUTE), NOW(), '$hour')"
    );

    $session_id = mysqli_insert_id($conn);

    header("Location: show_qr.php?session_id=$session_id");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Generate QR</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-5 bg-light">

<div class="container">

<div class="card p-4 shadow">

<h4>Generate Attendance QR</h4>

<form method="POST">

<!-- Semester Selection -->
<div class="mb-3">
<label>Select Semester</label>
<select name="semester" id="semester" class="form-control" required>
<option value="">Choose Semester</option>
<?php for($i=1;$i<=8;$i++): ?>
<option value="<?php echo $i; ?>">Semester <?php echo $i; ?></option>
<?php endfor; ?>
</select>
</div>

<!-- Subject Selection -->
<div class="mb-3">
<label>Select Subject</label>
<select name="subject_id" id="subjectDropdown" class="form-control" required>
<option value="">Select Semester First</option>
</select>
</div>

<!-- Hour Selection -->
<div class="mb-3">
<label>Select Hour</label>
<select name="hour" class="form-control" required>
<option value="">Choose Hour</option>
<?php for($i=1;$i<=7;$i++): ?>
<option value="<?php echo $i; ?>">Hour <?php echo $i; ?></option>
<?php endfor; ?>
</select>
</div>

<button name="generate_qr" class="btn btn-primary w-100">
Generate QR
</button>

</form>

</div>

</div>

<script>
document.getElementById("semester").addEventListener("change", function() {

    let semester = this.value;

    fetch("load_teacher_subjects.php?semester=" + semester)
    .then(response => response.text())
    .then(data => {
        document.getElementById("subjectDropdown").innerHTML = data;
    });
});
</script>

</body>
</html>