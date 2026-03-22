<?php
date_default_timezone_set('Asia/Kolkata');

session_start();
include("db.php");

if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacher_login.php");
    exit();
}

if (!isset($_GET['session_id'])) {
    header("Location: teacher_setup.php");
    exit();
}

$session_id = $_GET['session_id'];

$query = mysqli_query($conn,
"SELECT sq.token, sq.expiry_time, sq.hour,
        s.subject_name, s.subject_code
 FROM session_qr sq
 JOIN subjects s ON sq.subject_id = s.id
 WHERE sq.id='$session_id'"
);

$row = mysqli_fetch_assoc($query);

$qrData = $session_id . "|" . $row['token'];
$expiry = $row['expiry_time'];
$subject_name = $row['subject_name'];
$subject_code = $row['subject_code'];
$hour = $row['hour'];

/* Extend Session */
if (isset($_POST['extend_session'])) {

    mysqli_query($conn,
    "UPDATE session_qr
     SET expiry_time = DATE_ADD(expiry_time, INTERVAL 3 MINUTE)
     WHERE id='$session_id'"
    );

    header("Location: show_qr.php?session_id=$session_id");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Attendance QR</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>

<body class="p-5 bg-dark text-white">

<div class="container">

<div class="card p-4 text-center text-dark">

<h5><?php echo $subject_name; ?> (<?php echo $subject_code; ?>)</h5>
<p>Hour: <?php echo $hour; ?></p>

<div id="qrcode" class="mt-3"></div>

<p class="mt-3">
Expires in:
<strong><span id="timer"></span></strong>
</p>

<form method="POST">
<button name="extend_session" class="btn btn-warning">
Extend Session (+3 mins)
</button>
</form>

<a href="teacher_setup.php" class="btn btn-secondary mt-3">
Back
</a>

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

</div>

</body>
</html>