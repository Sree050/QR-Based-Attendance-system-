<?php
date_default_timezone_set('Asia/Kolkata');

session_start();
include("db.php");

if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacher_login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

$qrData = "";
$subject_name = "";
$subject_code = "";
$expiry = "";
$session_id = "";
$hour = "";

/* Generate QR */
if (isset($_POST['generate_qr'])) {

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

    header("Location: generate_qr.php?session_id=$session_id");
    exit();
}

/* Extend Session */
if (isset($_POST['extend_session'])) {

    $session_id = $_POST['session_id'];

    mysqli_query($conn,
    "UPDATE session_qr
     SET expiry_time = DATE_ADD(expiry_time, INTERVAL 3 MINUTE)
     WHERE id='$session_id'"
    );

    header("Location: generate_qr.php?session_id=$session_id");
    exit();
}

/* Load Existing Session */
if (isset($_GET['session_id'])) {

    $session_id = $_GET['session_id'];

    $query = mysqli_query($conn,
    "SELECT sq.token, sq.expiry_time, sq.hour,
            s.subject_name, s.subject_code
     FROM session_qr sq
     JOIN subjects s ON sq.subject_id = s.id
     WHERE sq.id='$session_id'"
    );

    if ($row = mysqli_fetch_assoc($query)) {
        $qrData = $session_id . "|" . $row['token'];
        $expiry = $row['expiry_time'];
        $subject_name = $row['subject_name'];
        $subject_code = $row['subject_code'];
        $hour = $row['hour'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Generate QR</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>

<body class="p-5 bg-dark text-white">

<div class="container">

<div class="card p-4 text-dark">

<h4>Generate Attendance QR</h4>

<form method="POST">

<div class="mb-3">
<label>Select Subject</label>
<select name="subject_id" class="form-control" required>
<?php
$subjects = mysqli_query($conn,
"SELECT s.id, s.subject_name, s.subject_code
 FROM teacher_subjects ts
 JOIN subjects s ON ts.subject_id = s.id
 WHERE ts.teacher_id='$teacher_id'"
);
while ($row = mysqli_fetch_assoc($subjects)) {
echo "<option value='{$row['id']}'>
{$row['subject_name']} ({$row['subject_code']})
</option>";
}
?>
</select>
</div>

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

<?php if (!empty($qrData)): ?>

<div class="card mt-4 p-4 text-center text-dark">

<h5><?php echo $subject_name; ?> (<?php echo $subject_code; ?>)</h5>
<p>Hour: <?php echo $hour; ?></p>

<div id="qrcode" class="mt-3"></div>

<p class="mt-3">
Expires in:
<strong><span id="timer"></span></strong>
</p>

<form method="POST">
<input type="hidden" name="session_id" value="<?php echo $session_id; ?>">
<button name="extend_session" class="btn btn-warning">
Extend Session (+3 mins)
</button>
</form>

</div>

<script>
new QRCode(document.getElementById("qrcode"), {
text: "<?php echo $qrData; ?>",
width: 220,
height: 220
});

let expiryTime = new Date("<?php echo $expiry; ?>").getTime();

let countdown = setInterval(function() {
let now = new Date().getTime();
let timeLeft = Math.floor((expiryTime - now) / 1000);

if (timeLeft <= 0) {
document.getElementById("timer").innerText = "Expired";
clearInterval(countdown);
} else {
document.getElementById("timer").innerText = timeLeft + " seconds";
}
}, 1000);
</script>

<?php endif; ?>

</div>

</body>
</html>