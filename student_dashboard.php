<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}
$name      = htmlspecialchars($_SESSION['student_name']);
$regno     = htmlspecialchars($_SESSION['student_regno']);
$firstName = htmlspecialchars(explode(' ', $_SESSION['student_name'])[0]);
$initial   = strtoupper(substr($_SESSION['student_name'], 0, 1));

$hour = (int)date('H');
$greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
$emoji    = $hour < 12 ? '☀️' : ($hour < 17 ? '🌤️' : '🌙');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard · QR Attendance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --teal:    #0fa97a; --teal-d: #0b8a63; --teal-l: #e8f7f2; --teal-ll:#f4fdf9;
            --bg:      #f4faf7; --white:  #ffffff;
            --ink:     #0d1f18; --ink-mid:#2c4a3c; --dim:#6b8c7d; --muted:#adc4ba;
            --border:  #d8ece4;
            --blue:    #3b82f6; --blue-l: #eff6ff;
            --gold:    #f59e0b; --gold-l: #fffbeb;
            --rose:    #e8445a; --rose-l: #fff5f6;
            --purple:  #8b5cf6; --purple-l:#f5f3ff;
            --r: 18px; --r-sm: 12px;
            --shadow: 0 2px 16px rgba(15,169,122,0.08);
            --shadow-md: 0 8px 32px rgba(15,169,122,0.12);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            color: var(--ink);
        }

        /* ── Topbar ── */
        .topbar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 0 36px;
            height: 62px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 100;
            box-shadow: 0 1px 12px rgba(15,169,122,0.06);
        }

        .tb-brand { display:flex; align-items:center; gap:10px; text-decoration:none; }
        .tb-icon  { width:34px; height:34px; border-radius:9px; background:var(--teal); display:flex; align-items:center; justify-content:center; font-size:16px; box-shadow:0 3px 12px rgba(15,169,122,0.3); }
        .tb-name  { font-size:16px; font-weight:700; color:var(--ink); }

        .tb-right { display:flex; align-items:center; gap:12px; }
        .tb-user  { display:flex; align-items:center; gap:9px; background:var(--teal-l); border:1px solid #c5e8d8; border-radius:10px; padding:6px 14px 6px 8px; font-size:13px; font-weight:600; color:var(--ink-mid); }
        .tb-avatar{ width:28px; height:28px; border-radius:7px; background:var(--teal); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:#fff; }

        .tb-logout { display:flex; align-items:center; gap:6px; padding:7px 14px; border:1.5px solid var(--border); border-radius:10px; background:transparent; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; font-weight:600; color:var(--dim); cursor:pointer; transition:all 0.2s; }
        .tb-logout:hover { background:var(--rose-l); border-color:#fcc; color:var(--rose); }

        /* ── Main ── */
        .main { max-width: 1000px; margin: 0 auto; padding: 44px 24px 80px; }

        /* ── Hero ── */
        .hero {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--r);
            padding: 32px 36px;
            margin-bottom: 28px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: var(--shadow);
            animation: fadeDown 0.5s ease both;
            position: relative; overflow: hidden;
        }

        .hero::after {
            content: '';
            position: absolute;
            right: -60px; top: -60px;
            width: 220px; height: 220px;
            border-radius: 50%;
            background: var(--teal-ll);
        }

        .hero-left { position: relative; z-index: 1; }
        .hero-eyebrow { font-size: 12px; font-weight: 600; letter-spacing: 0.07em; text-transform: uppercase; color: var(--teal); margin-bottom: 6px; }
        .hero h1 { font-size: 26px; font-weight: 800; color: var(--ink); margin-bottom: 5px; }
        .hero-sub { font-size: 14px; color: var(--dim); font-weight: 300; }

        .hero-right { position: relative; z-index: 1; text-align: right; }
        .hero-emoji { font-size: 52px; line-height: 1; display: block; }

        /* date/time bar */
        .pill-row { display:flex; gap:10px; flex-wrap:wrap; margin-top:16px; }
        .pill { display:flex; align-items:center; gap:7px; background:var(--teal-l); border:1px solid #c5e8d8; border-radius:8px; padding:7px 14px; font-size:13px; color:var(--ink-mid); font-weight:500; }

        /* ── Info cards row ── */
        .info-row { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:28px; animation:fadeUp 0.45s 0.06s ease both; }
        @media(max-width:640px){ .info-row{grid-template-columns:1fr 1fr;} }
        @media(max-width:420px){ .info-row{grid-template-columns:1fr;} }

        .info-card { background:var(--white); border:1px solid var(--border); border-radius:var(--r-sm); padding:18px 20px; box-shadow:var(--shadow); }
        .info-card .ic-lbl { font-size:10px; font-weight:600; letter-spacing:0.08em; text-transform:uppercase; color:var(--muted); margin-bottom:6px; }
        .info-card .ic-val { font-size:15px; font-weight:700; color:var(--ink); }
        .info-card .ic-val.teal { color:var(--teal); }

        /* ── Section label ── */
        .sec-lbl { font-size:10px; font-weight:600; letter-spacing:0.1em; text-transform:uppercase; color:var(--muted); margin-bottom:16px; }

        /* ── Action cards ── */
        .cards-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(210px,1fr)); gap:16px; }

        .acard {
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: var(--r);
            padding: 26px 22px 44px;
            text-decoration: none; color: var(--ink);
            cursor: pointer;
            position: relative; overflow: hidden;
            transition: transform 0.22s, box-shadow 0.22s, border-color 0.22s;
            animation: fadeUp 0.45s ease both;
        }

        .acard:nth-child(1){ animation-delay:0.10s; }
        .acard:nth-child(2){ animation-delay:0.16s; }
        .acard:nth-child(3){ animation-delay:0.22s; }
        .acard:nth-child(4){ animation-delay:0.28s; }

        .acard:hover { transform:translateY(-5px); }
        .acard:active{ transform:translateY(-1px); }

        /* left border accent */
        .acard::before { content:''; position:absolute; left:0; top:0; bottom:0; width:4px; border-radius:var(--r) 0 0 var(--r); }

        .ac-scan   ::before, .ac-scan::before   { background:var(--teal); }
        .ac-report ::before, .ac-report::before  { background:var(--blue); }
        .ac-profile::before, .ac-profile::before { background:var(--gold); }
        .ac-logout ::before, .ac-logout::before  { background:var(--rose); }

        .ac-scan:hover   { box-shadow:0 12px 36px rgba(15,169,122,0.14); border-color:#c5e8d8; }
        .ac-report:hover { box-shadow:0 12px 36px rgba(59,130,246,0.12); border-color:#bfdbfe; }
        .ac-profile:hover{ box-shadow:0 12px 36px rgba(245,158,11,0.12); border-color:#fde68a; }
        .ac-logout:hover { box-shadow:0 12px 36px rgba(232,68,90,0.10); border-color:#fcc; }

        /* icon badge */
        .acard-icon { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:20px; margin-bottom:18px; }
        .ac-scan    .acard-icon { background:var(--teal-l); }
        .ac-report  .acard-icon { background:var(--blue-l); }
        .ac-profile .acard-icon { background:var(--gold-l); }
        .ac-logout  .acard-icon { background:var(--rose-l); }

        .acard h5 { font-size:15px; font-weight:700; color:var(--ink); margin-bottom:5px; }
        .acard p  { font-size:13px; color:var(--dim); line-height:1.55; font-weight:300; }

        .acard-arrow { position:absolute; bottom:18px; right:18px; font-size:16px; color:var(--muted); transition:transform 0.2s,color 0.2s; }
        .acard:hover .acard-arrow { transform:translate(3px,-3px); color:var(--dim); }

        /* ── Modal ── */
        .overlay { display:none; position:fixed; inset:0; background:rgba(13,31,24,0.35); backdrop-filter:blur(6px); z-index:200; align-items:center; justify-content:center; }
        .overlay.show { display:flex; }
        .modal-box { background:var(--white); border:1px solid var(--border); border-radius:22px; padding:36px 32px; width:320px; text-align:center; box-shadow:0 24px 64px rgba(13,31,24,0.18); animation:popIn 0.25s cubic-bezier(0.22,1,0.36,1) both; }
        @keyframes popIn { from{opacity:0;transform:scale(0.9);}to{opacity:1;transform:scale(1);} }
        .modal-ico { width:54px; height:54px; border-radius:14px; background:var(--rose-l); border:1px solid #fcc; display:flex; align-items:center; justify-content:center; font-size:22px; margin:0 auto 16px; }
        .modal-box h5 { font-size:17px; font-weight:800; color:var(--ink); margin-bottom:6px; }
        .modal-box p  { font-size:13px; color:var(--dim); margin-bottom:24px; line-height:1.6; }
        .modal-btns   { display:flex; gap:10px; }
        .btn-stay     { flex:1; padding:11px; border-radius:10px; border:1.5px solid var(--border); background:transparent; font-family:'Plus Jakarta Sans',sans-serif; font-size:14px; font-weight:600; color:var(--dim); cursor:pointer; transition:all 0.2s; }
        .btn-stay:hover { background:var(--bg); color:var(--ink); }
        .btn-go       { flex:1; padding:11px; border-radius:10px; border:none; background:var(--rose); color:#fff; font-family:'Plus Jakarta Sans',sans-serif; font-size:14px; font-weight:700; cursor:pointer; transition:all 0.2s; box-shadow:0 4px 14px rgba(232,68,90,0.28); }
        .btn-go:hover { background:#d43350; transform:translateY(-1px); }

        /* ── Animations ── */
        @keyframes fadeDown { from{opacity:0;transform:translateY(-14px);}to{opacity:1;transform:translateY(0);} }
        @keyframes fadeUp   { from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);} }

        @media(max-width:600px){ .topbar{padding:0 16px;} .main{padding:28px 14px 60px;} .hero{padding:24px 20px;} .hero h1{font-size:20px;} .hero-emoji{font-size:36px;} }
    </style>
</head>
<body>

<!-- Topbar -->
<header class="topbar">
    <a href="#" class="tb-brand">
        <div class="tb-icon">📋</div>
        <span class="tb-name">QR Attendance</span>
    </a>
    <div class="tb-right">
        <div class="tb-user">
            <div class="tb-avatar"><?php echo $initial; ?></div>
            <?php echo $name; ?>
        </div>
        <button class="tb-logout" onclick="document.getElementById('logoutModal').classList.add('show')">
            🚪 Logout
        </button>
    </div>
</header>

<div class="main">

    <!-- Hero -->
    <div class="hero">
        <div class="hero-left">
            <div class="hero-eyebrow"><?php echo $greeting; ?></div>
            <h1>Hello, <?php echo $firstName; ?>! <?php echo $emoji; ?></h1>
            <p class="hero-sub">Here's your attendance hub for today.</p>
            <div class="pill-row">
                <div class="pill">📅 <span><?php echo date('D, d M Y'); ?></span></div>
                <div class="pill">🕐 <strong id="clock"><?php echo date('h:i A'); ?></strong></div>
            </div>
        </div>
        <div class="hero-right">
            <span class="hero-emoji">🎓</span>
        </div>
    </div>

    <!-- Info row -->
    <div class="info-row">
        <div class="info-card">
            <div class="ic-lbl">Register No.</div>
            <div class="ic-val teal"><?php echo $regno; ?></div>
        </div>
        <div class="info-card">
            <div class="ic-lbl">Student Name</div>
            <div class="ic-val"><?php echo $name; ?></div>
        </div>
        <div class="info-card">
            <div class="ic-lbl">Today's Date</div>
            <div class="ic-val"><?php echo date('d M Y'); ?></div>
        </div>
    </div>

    <!-- Quick actions -->
    <div class="sec-lbl">Quick Actions</div>
    <div class="cards-grid">

        <a href="scan_qr.php" class="acard ac-scan">
            <div class="acard-icon">📷</div>
            <h5>Scan QR Code</h5>
            <p>Check in to your current class session instantly</p>
            <div class="acard-arrow">↗</div>
        </a>

        <a href="student_attendance.php" class="acard ac-report">
            <div class="acard-icon">📊</div>
            <h5>My Attendance</h5>
            <p>View your subject-wise attendance records</p>
            <div class="acard-arrow">↗</div>
        </a>

        <a href="student_profile.php" class="acard ac-profile">
            <div class="acard-icon">👤</div>
            <h5>My Profile</h5>
            <p>Update your account info and password</p>
            <div class="acard-arrow">↗</div>
        </a>

        <div class="acard ac-logout" onclick="document.getElementById('logoutModal').classList.add('show')">
            <div class="acard-icon">🚪</div>
            <h5>Logout</h5>
            <p>End your session securely</p>
            <div class="acard-arrow">↗</div>
        </div>

    </div>
</div>

<!-- Logout Modal -->
<div class="overlay" id="logoutModal">
    <div class="modal-box">
        <div class="modal-ico">🚪</div>
        <h5>Leaving so soon?</h5>
        <p>You'll need to sign in again to access your dashboard.</p>
        <div class="modal-btns">
            <button class="btn-stay" onclick="document.getElementById('logoutModal').classList.remove('show')">Stay</button>
            <button class="btn-go"   onclick="window.location='logout.php'">Yes, Logout</button>
        </div>
    </div>
</div>

<script>
    document.getElementById('logoutModal').addEventListener('click', function(e){ if(e.target===this) this.classList.remove('show'); });
    function tick(){ const n=new Date(),h=n.getHours()%12||12,m=String(n.getMinutes()).padStart(2,'0'),a=n.getHours()>=12?'PM':'AM'; document.getElementById('clock').textContent=`${h}:${m} ${a}`; }
    setInterval(tick,1000);
</script>
</body>
</html>