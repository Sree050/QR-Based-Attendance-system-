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
<html lang="en">
<head>
    <title>Attendance QR — <?php echo htmlspecialchars($subject_code); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            color: #fff;
        }

        .wrapper {
            width: 100%;
            max-width: 440px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        /* Top meta badge */
        .meta-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            color: rgba(255,255,255,0.7);
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            padding: 0.35rem 1rem;
            border-radius: 999px;
        }

        .meta-badge i { color: #818cf8; }

        /* Main card */
        .qr-card {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 24px;
            padding: 2rem 1.75rem;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 30px 60px rgba(0,0,0,0.45);
            text-align: center;
        }

        /* Subject header */
        .subject-name {
            font-size: 1.35rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
            margin-bottom: 0.25rem;
        }

        .subject-meta {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-top: 0.4rem;
            flex-wrap: wrap;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.3rem 0.75rem;
            border-radius: 999px;
        }

        .pill-code {
            background: rgba(129,140,248,0.18);
            color: #a5b4fc;
            border: 1px solid rgba(129,140,248,0.3);
        }

        .pill-hour {
            background: rgba(52,211,153,0.15);
            color: #6ee7b7;
            border: 1px solid rgba(52,211,153,0.25);
        }

        /* QR frame */
        .qr-frame {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            border-radius: 18px;
            padding: 16px;
            margin: 1.5rem 0 1rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3), 0 0 0 1px rgba(255,255,255,0.1);
            position: relative;
        }

        .qr-frame.expired::after {
            content: 'EXPIRED';
            position: absolute;
            inset: 0;
            border-radius: 18px;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            font-weight: 800;
            color: #f87171;
            letter-spacing: 0.12em;
        }

        /* Timer section */
        .timer-section {
            margin-bottom: 1.25rem;
        }

        .timer-label {
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.45);
            margin-bottom: 0.4rem;
        }

        .timer-display {
            font-size: 2.75rem;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
            letter-spacing: -0.02em;
            line-height: 1;
            color: #fff;
            transition: color 0.4s;
        }

        .timer-display.warning { color: #fbbf24; }
        .timer-display.danger  { color: #f87171; }

        /* Progress ring */
        .progress-ring-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            margin-top: 0.4rem;
        }

        .progress-bar-track {
            flex: 1;
            height: 5px;
            background: rgba(255,255,255,0.1);
            border-radius: 999px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #6366f1, #a78bfa);
            transition: width 1s linear, background 0.4s;
        }

        .progress-bar-fill.warning { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
        .progress-bar-fill.danger  { background: linear-gradient(90deg, #ef4444, #f87171); }

        .progress-pct {
            font-size: 0.75rem;
            font-weight: 600;
            color: rgba(255,255,255,0.4);
            min-width: 2.5rem;
            text-align: right;
        }

        /* Divider */
        .divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.1);
            margin: 1.25rem 0;
        }

        /* Buttons */
        .btn-extend {
            width: 100%;
            padding: 0.8rem;
            border: none;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
            color: #1c1c1c;
            box-shadow: 0 4px 18px rgba(251,191,36,0.3);
            transition: opacity 0.2s, transform 0.15s;
            margin-bottom: 0.65rem;
        }

        .btn-extend:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-extend:active { transform: translateY(0); }

        .btn-back {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.06);
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            transition: background 0.2s, transform 0.15s;
        }

        .btn-back:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
            transform: translateY(-1px);
        }

        /* Scan hint */
        .scan-hint {
            font-size: 0.78rem;
            color: rgba(255,255,255,0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            margin-top: -0.25rem;
            margin-bottom: 0.25rem;
        }

        /* Pulse on QR when active */
        @keyframes pulse-ring {
            0%   { box-shadow: 0 8px 32px rgba(0,0,0,0.3), 0 0 0 0 rgba(129,140,248,0.4); }
            70%  { box-shadow: 0 8px 32px rgba(0,0,0,0.3), 0 0 0 12px rgba(129,140,248,0); }
            100% { box-shadow: 0 8px 32px rgba(0,0,0,0.3), 0 0 0 0 rgba(129,140,248,0); }
        }

        .qr-frame.live { animation: pulse-ring 2.2s ease-out infinite; }
    </style>
</head>
<body>

<div class="wrapper">

    <div class="meta-badge">
        <i class="bi bi-broadcast"></i> Live Session
    </div>

    <div class="qr-card">

        <!-- Subject info -->
        <div class="subject-name"><?php echo htmlspecialchars($subject_name); ?></div>
        <div class="subject-meta">
            <span class="pill pill-code">
                <i class="bi bi-hash"></i><?php echo htmlspecialchars($subject_code); ?>
            </span>
            <span class="pill pill-hour">
                <i class="bi bi-clock"></i>Hour <?php echo htmlspecialchars($hour); ?>
            </span>
        </div>

        <!-- QR Code -->
        <div class="qr-frame live" id="qrFrame">
            <div id="qrcode"></div>
        </div>

        <div class="scan-hint">
            <i class="bi bi-phone"></i> Students scan with their phones to mark attendance
        </div>

        <!-- Timer -->
        <div class="timer-section">
            <div class="timer-label">Time Remaining</div>
            <div class="timer-display" id="timerDisplay">--</div>
            <div class="progress-ring-wrap">
                <div class="progress-bar-track">
                    <div class="progress-bar-fill" id="progressFill" style="width:100%"></div>
                </div>
                <div class="progress-pct" id="progressPct">100%</div>
            </div>
        </div>

        <hr class="divider">

        <!-- Actions -->
        <form method="POST">
            <button type="submit" name="extend_session" class="btn-extend">
                <i class="bi bi-plus-circle"></i> Extend Session (+3 mins)
            </button>
        </form>

        <a href="teacher_setup.php" class="btn-back">
            <i class="bi bi-arrow-left"></i> Back to Setup
        </a>

    </div>

</div>

<script>
    // Generate QR
    new QRCode(document.getElementById("qrcode"), {
        text: "<?php echo addslashes($qrData); ?>",
        width: 200,
        height: 200,
        colorDark: "#111827",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });

    const expiryTime   = new Date("<?php echo $expiry; ?>").getTime();
    const totalDuration = 2 * 60 * 1000; // baseline 2 min for % calc (resets on extend)
    const startTime    = Date.now();
    const sessionTotal = expiryTime - startTime;

    const timerEl   = document.getElementById('timerDisplay');
    const fillEl    = document.getElementById('progressFill');
    const pctEl     = document.getElementById('progressPct');
    const qrFrame   = document.getElementById('qrFrame');

    function pad(n) { return String(n).padStart(2, '0'); }

    const tick = setInterval(function () {
        const now      = Date.now();
        const timeLeft = Math.floor((expiryTime - now) / 1000);

        if (timeLeft <= 0) {
            timerEl.textContent = '00:00';
            timerEl.className   = 'timer-display danger';
            fillEl.style.width  = '0%';
            fillEl.className    = 'progress-bar-fill danger';
            pctEl.textContent   = '0%';
            qrFrame.classList.remove('live');
            qrFrame.classList.add('expired');
            clearInterval(tick);
            return;
        }

        const mins = Math.floor(timeLeft / 60);
        const secs = timeLeft % 60;
        timerEl.textContent = pad(mins) + ':' + pad(secs);

        const pct = Math.max(0, Math.min(100, (timeLeft / (sessionTotal / 1000)) * 100));
        fillEl.style.width = pct + '%';
        pctEl.textContent  = Math.round(pct) + '%';

        if (timeLeft <= 30) {
            timerEl.className = 'timer-display danger';
            fillEl.className  = 'progress-bar-fill danger';
        } else if (timeLeft <= 60) {
            timerEl.className = 'timer-display warning';
            fillEl.className  = 'progress-bar-fill warning';
        } else {
            timerEl.className = 'timer-display';
            fillEl.className  = 'progress-bar-fill';
        }
    }, 1000);
</script>

</body>
</html>