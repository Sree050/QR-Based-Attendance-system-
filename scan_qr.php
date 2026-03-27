<?php
session_start();
include("db.php");

if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

$name    = htmlspecialchars($_SESSION['student_name']);
$initial = strtoupper(substr($_SESSION['student_name'], 0, 1));

$query = "
SELECT s.subject_name, s.subject_code, sq.id
FROM session_qr sq
JOIN subjects s ON sq.subject_id = s.id
WHERE sq.created_at >= NOW() - INTERVAL 5 MINUTE
ORDER BY sq.id DESC
LIMIT 1
";
$result  = mysqli_query($conn, $query);
$noSession = (mysqli_num_rows($result) == 0);
$session   = $noSession ? null : mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Scan QR · QR Attendance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
        :root {
            --teal:    #0fa97a; --teal-d: #0b8a63; --teal-l: #e8f7f2; --teal-ll:#f4fdf9;
            --bg:      #f4faf7; --white:  #ffffff;
            --ink:     #0d1f18; --ink-mid:#2c4a3c; --dim:#6b8c7d; --muted:#adc4ba;
            --border:  #d8ece4;
            --rose:    #e8445a; --rose-l: #fff5f6;
            --gold:    #f59e0b; --gold-l: #fffbeb;
            --r: 18px; --r-sm: 12px;
            --shadow: 0 2px 16px rgba(15,169,122,0.08);
            --shadow-md: 0 8px 32px rgba(15,169,122,0.13);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            color: var(--ink);
        }

        /* Topbar */
        .topbar { background:var(--white); border-bottom:1px solid var(--border); padding:0 36px; height:62px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:100; box-shadow:0 1px 12px rgba(15,169,122,0.06); }
        .tb-brand { display:flex; align-items:center; gap:10px; text-decoration:none; }
        .tb-icon  { width:34px; height:34px; border-radius:9px; background:var(--teal); display:flex; align-items:center; justify-content:center; font-size:16px; box-shadow:0 3px 12px rgba(15,169,122,0.3); }
        .tb-name  { font-size:16px; font-weight:700; color:var(--ink); }
        .tb-user  { display:flex; align-items:center; gap:9px; background:var(--teal-l); border:1px solid #c5e8d8; border-radius:10px; padding:6px 14px 6px 8px; font-size:13px; font-weight:600; color:var(--ink-mid); }
        .tb-avatar{ width:28px; height:28px; border-radius:7px; background:var(--teal); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:#fff; }

        /* Main */
        .main { max-width: 560px; margin: 0 auto; padding: 44px 20px 80px; }

        /* Back link */
        .back-link { display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:600; color:var(--dim); text-decoration:none; margin-bottom:28px; transition:color 0.2s; }
        .back-link:hover { color:var(--teal); }

        /* Session info card */
        .session-card {
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: var(--r);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            animation: fadeUp 0.45s ease both;
        }

        .session-stripe { height: 4px; background: linear-gradient(90deg, var(--teal), #34d399); }

        .session-head {
            padding: 24px 28px 20px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 14px;
        }

        .session-ico { width:44px; height:44px; border-radius:12px; background:var(--teal-l); display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }

        .session-head h2 { font-size:17px; font-weight:800; color:var(--ink); margin-bottom:3px; }
        .session-head p  { font-size:12px; color:var(--dim); font-weight:300; }

        .session-meta { padding: 18px 28px; display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
        .meta-chip { display:inline-flex; align-items:center; gap:6px; background:var(--teal-l); border:1px solid #c5e8d8; border-radius:20px; padding:5px 14px; font-size:12px; font-weight:600; color:var(--teal-d); }
        .meta-chip.live::before { content:''; display:inline-block; width:7px; height:7px; border-radius:50%; background:var(--teal); animation:pulse 1.5s ease infinite; }
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1);} 50%{opacity:0.5;transform:scale(1.3);} }

        /* Scanner area */
        .scanner-wrap { padding: 24px 28px 28px; }

        .scanner-label { font-size:12px; font-weight:600; letter-spacing:0.07em; text-transform:uppercase; color:var(--muted); margin-bottom:14px; }

        .scanner-box {
            background: var(--bg);
            border: 1.5px dashed #b2d8c8;
            border-radius: var(--r);
            overflow: hidden;
            position: relative;
        }

        /* Override html5-qrcode default styles */
        #reader { width: 100% !important; border: none !important; }
        #reader video { border-radius: 12px !important; }
        #reader img   { display: none !important; }
        #reader__scan_region { background: transparent !important; }
        #reader__dashboard   { background: var(--white) !important; padding: 12px 16px !important; border-top: 1px solid var(--border) !important; }
        #reader__dashboard button {
            background: var(--teal) !important; color: #fff !important;
            border: none !important; border-radius: 8px !important;
            padding: 8px 18px !important; font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-size: 13px !important; font-weight: 600 !important; cursor: pointer !important;
        }
        #reader__dashboard select {
            border: 1.5px solid var(--border) !important; border-radius: 8px !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-size: 13px !important; padding: 6px 10px !important;
            background: var(--bg) !important; color: var(--ink) !important;
        }
        #reader__status_span { font-family: 'Plus Jakarta Sans', sans-serif !important; font-size:13px !important; color:var(--dim) !important; }

        /* scan hint */
        .scan-hint { display:flex; align-items:center; gap:8px; margin-top:14px; background:var(--teal-l); border:1px solid #c5e8d8; border-radius:var(--r-sm); padding:11px 14px; font-size:13px; color:var(--ink-mid); font-weight:500; }

        /* No session state */
        .no-session {
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: var(--r);
            padding: 56px 32px;
            text-align: center;
            box-shadow: var(--shadow);
            animation: fadeUp 0.45s ease both;
        }
        .no-session-ico { font-size:52px; margin-bottom:16px; opacity:0.6; }
        .no-session h3  { font-size:20px; font-weight:800; color:var(--ink); margin-bottom:8px; }
        .no-session p   { font-size:14px; color:var(--dim); line-height:1.65; margin-bottom:24px; }
        .btn-back { display:inline-flex; align-items:center; gap:8px; padding:12px 24px; background:var(--teal); color:#fff; border:none; border-radius:var(--r-sm); font-family:'Plus Jakarta Sans',sans-serif; font-size:14px; font-weight:700; text-decoration:none; cursor:pointer; transition:all 0.2s; box-shadow:0 4px 16px rgba(15,169,122,0.25); }
        .btn-back:hover { background:var(--teal-d); transform:translateY(-1px); }

        /* Result overlay */
        .result-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(13,31,24,0.4);
            backdrop-filter: blur(8px);
            z-index: 200; align-items: center; justify-content: center;
        }
        .result-overlay.show { display:flex; }

        .result-box {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 40px 36px;
            width: 320px; text-align: center;
            box-shadow: 0 24px 64px rgba(13,31,24,0.2);
            animation: popIn 0.3s cubic-bezier(0.22,1,0.36,1) both;
        }

        @keyframes popIn { from{opacity:0;transform:scale(0.88);}to{opacity:1;transform:scale(1);} }

        .result-icon { width:64px; height:64px; border-radius:18px; display:flex; align-items:center; justify-content:center; font-size:28px; margin:0 auto 18px; }
        .result-icon.ok   { background:var(--teal-l); }
        .result-icon.fail { background:var(--rose-l); }

        .result-box h4 { font-size:18px; font-weight:800; color:var(--ink); margin-bottom:6px; }
        .result-box p  { font-size:14px; color:var(--dim); margin-bottom:24px; line-height:1.6; }

        .btn-result { width:100%; padding:13px; border:none; border-radius:var(--r-sm); font-family:'Plus Jakarta Sans',sans-serif; font-size:15px; font-weight:700; cursor:pointer; transition:all 0.2s; }
        .btn-result.ok   { background:var(--teal); color:#fff; box-shadow:0 4px 16px rgba(15,169,122,0.28); }
        .btn-result.ok:hover   { background:var(--teal-d); transform:translateY(-1px); }
        .btn-result.fail { background:var(--rose); color:#fff; box-shadow:0 4px 16px rgba(232,68,90,0.25); }
        .btn-result.fail:hover { background:#d43350; transform:translateY(-1px); }

        @keyframes fadeUp { from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);} }
        @media(max-width:600px){ .topbar{padding:0 16px;} .main{padding:32px 14px 60px;} .session-head,.scanner-wrap,.session-meta{padding-left:18px;padding-right:18px;} }
    </style>
</head>
<body>

<!-- Topbar -->
<header class="topbar">
    <a href="student_dashboard.php" class="tb-brand">
        <div class="tb-icon">📋</div>
        <span class="tb-name">QR Attendance</span>
    </a>
    <div class="tb-user">
        <div class="tb-avatar"><?php echo $initial; ?></div>
        <?php echo $name; ?>
    </div>
</header>

<div class="main">

    <a href="student_dashboard.php" class="back-link">← Back to Dashboard</a>

    <?php if ($noSession): ?>

    <!-- No active session -->
    <div class="no-session">
        <div class="no-session-ico">📭</div>
        <h3>No Active Session</h3>
        <p>There's no attendance session running right now.<br>Please wait for your teacher to start one, then refresh this page.</p>
        <a href="student_dashboard.php" class="btn-back">← Go to Dashboard</a>
    </div>

    <?php else: ?>

    <!-- Session card -->
    <div class="session-card">
        <div class="session-stripe"></div>

        <div class="session-head">
            <div class="session-ico">📚</div>
            <div>
                <h2><?php echo htmlspecialchars($session['subject_name']); ?></h2>
                <p>Subject Code: <strong><?php echo htmlspecialchars($session['subject_code']); ?></strong></p>
            </div>
        </div>

        <div class="session-meta">
            <span class="meta-chip live">Session Active</span>
            <span class="meta-chip">🕐 <?php echo date('h:i A'); ?></span>
            <span class="meta-chip">📅 <?php echo date('d M Y'); ?></span>
        </div>

        <div class="scanner-wrap">
            <div class="scanner-label">Point your camera at the QR code</div>
            <div class="scanner-box">
                <div id="reader"></div>
            </div>
            <div class="scan-hint">
                💡 Make sure the QR code is well-lit and fully inside the frame.
            </div>
        </div>
    </div>

    <?php endif; ?>

</div>

<!-- Result overlay -->
<div class="result-overlay" id="resultOverlay">
    <div class="result-box">
        <div class="result-icon" id="resultIcon"></div>
        <h4 id="resultTitle"></h4>
        <p  id="resultMsg"></p>
        <button class="btn-result" id="resultBtn" onclick="goHome()">Go to Dashboard</button>
    </div>
</div>

<script>
<?php if (!$noSession): ?>
function onScanSuccess(decodedText) {
    // stop scanner
    if (window._scanner) window._scanner.clear();

    fetch("mark_attendance.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "qr_data=" + encodeURIComponent(decodedText)
    })
    .then(r => r.text())
    .then(data => {
        const lower = data.toLowerCase();
        const ok    = lower.includes("success") || lower.includes("marked") || lower.includes("recorded");
        showResult(ok, data);
    })
    .catch(() => showResult(false, "Network error. Please try again."));
}

function showResult(ok, message) {
    document.getElementById('resultIcon').textContent  = ok ? '✅' : '❌';
    document.getElementById('resultIcon').className    = 'result-icon ' + (ok ? 'ok' : 'fail');
    document.getElementById('resultTitle').textContent = ok ? 'Attendance Marked!' : 'Check-in Failed';
    document.getElementById('resultMsg').textContent   = message;
    const btn = document.getElementById('resultBtn');
    btn.className = 'btn-result ' + (ok ? 'ok' : 'fail');
    btn.textContent = ok ? '✓ Done' : 'Try Again';
    if (!ok) btn.onclick = () => { document.getElementById('resultOverlay').classList.remove('show'); startScanner(); };
    else     btn.onclick = goHome;
    document.getElementById('resultOverlay').classList.add('show');
}

function goHome() { window.location.href = 'student_dashboard.php'; }

function startScanner() {
    window._scanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250, rememberLastUsedCamera: true });
    window._scanner.render(onScanSuccess);
}

startScanner();
<?php endif; ?>
</script>
</body>
</html>