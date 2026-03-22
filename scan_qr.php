<?php
session_start();
include("db.php");

if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

$query = "
SELECT s.subject_name, s.subject_code, sq.id 
FROM session_qr sq
JOIN subjects s ON sq.subject_id = s.id
WHERE sq.created_at >= NOW() - INTERVAL 5 MINUTE
ORDER BY sq.id DESC
LIMIT 1
";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    die("No active attendance session.");
}

$session = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
<title>Scan QR</title>

<script src="https://unpkg.com/html5-qrcode"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #1e3c72, #2a5298);
    color: white;
    font-family: 'Segoe UI', sans-serif;
}
.card {
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    border-radius: 15px;
}
</style>
</head>

<body class="p-4">

<div class="container">

    <div class="card p-4 text-center shadow">

        <h4>Active Session</h4>
        <h5>
            <?php echo $session['subject_name']; ?>
            (<?php echo $session['subject_code']; ?>)
        </h5>

        <div id="reader" style="width:300px; margin:auto;" class="mt-3"></div>

    </div>

</div>

<script>
function onScanSuccess(decodedText) {

    fetch("mark_attendance.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "qr_data=" + encodeURIComponent(decodedText)
    })
    .then(response => response.text())
    .then(data => {

        alert(data);

        window.location.href = "student_dashboard.php";

    });

}

new Html5QrcodeScanner("reader", {
    fps: 10,
    qrbox: 250
}).render(onScanSuccess);
</script>

</body>
</html>