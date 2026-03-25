<?php
session_start();
if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacher_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Teacher Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Inter', 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, #0f1740 0%, #1e1050 100%);
        min-height: 100vh;
        color: white;
    }

    /* ── Ambient orbs ── */
    .orb {
        position: fixed;
        border-radius: 50%;
        filter: blur(90px);
        opacity: 0.18;
        pointer-events: none;
        z-index: 0;
    }
    .orb-1 { width: 500px; height: 500px; background: #3b82f6; top: -160px; left: -160px; }
    .orb-2 { width: 400px; height: 400px; background: #7c3aed; bottom: -100px; right: -100px; }
    .orb-3 { width: 260px; height: 260px; background: #0ea5e9; top: 40%; right: 10%; }

    /* ── Navbar ── */
    .top-nav {
        position: relative;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 32px;
        height: 68px;
        background: rgba(255,255,255,0.05);
        backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }

    .nav-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        font-size: 1.05rem;
        letter-spacing: -0.2px;
    }

    .nav-brand .brand-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #6366f1, #3b82f6);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        box-shadow: 0 4px 12px rgba(99,102,241,0.4);
    }

    .nav-user {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.88rem;
        color: rgba(255,255,255,0.7);
    }

    .nav-avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #6366f1, #3b82f6);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
    }

    /* ── Main ── */
    .main-wrap {
        position: relative;
        z-index: 1;
        max-width: 1100px;
        margin: 0 auto;
        padding: 48px 24px 60px;
    }

    /* ── Header section ── */
    .page-header {
        margin-bottom: 44px;
    }

    .greeting {
        font-size: 0.82rem;
        font-weight: 500;
        color: rgba(255,255,255,0.45);
        letter-spacing: 1.2px;
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .page-header h2 {
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin-bottom: 6px;
    }

    .page-header p {
        font-size: 0.88rem;
        color: rgba(255,255,255,0.45);
    }

    /* ── Stat pills ── */
    .stat-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 24px;
    }

    .stat-pill {
        display: flex;
        align-items: center;
        gap: 8px;
        background: rgba(255,255,255,0.07);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 12px;
        padding: 10px 18px;
        font-size: 0.84rem;
        color: rgba(255,255,255,0.7);
    }

    .stat-pill .val {
        font-weight: 700;
        font-size: 1rem;
        color: white;
    }

    /* ── Section label ── */
    .section-label {
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 1.4px;
        text-transform: uppercase;
        color: rgba(255,255,255,0.35);
        margin-bottom: 18px;
    }

    /* ── Dashboard cards ── */
    .cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
    }

    .dash-card {
        border-radius: 22px;
        padding: 32px 28px;
        cursor: pointer;
        border: 1px solid rgba(255,255,255,0.1);
        background: rgba(255,255,255,0.06);
        backdrop-filter: blur(12px);
        transition: transform 0.22s ease, box-shadow 0.22s ease, background 0.22s ease;
        position: relative;
        overflow: hidden;
        text-decoration: none;
        color: white;
        display: block;
    }

    .dash-card::before {
        content: '';
        position: absolute;
        inset: 0;
        opacity: 0;
        transition: opacity 0.22s ease;
        border-radius: inherit;
    }

    .dash-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 48px rgba(0,0,0,0.35);
        color: white;
    }

    .dash-card:hover::before { opacity: 1; }

    /* Card accent lines */
    .dash-card .accent-line {
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        border-radius: 22px 22px 0 0;
    }

    /* Individual card colors */
    .card-qr .accent-line     { background: linear-gradient(90deg, #3b82f6, #06b6d4); }
    .card-qr::before          { background: linear-gradient(135deg, rgba(59,130,246,0.12), rgba(6,182,212,0.08)); }
    .card-qr:hover            { box-shadow: 0 20px 48px rgba(59,130,246,0.25); }

    .card-report .accent-line { background: linear-gradient(90deg, #10b981, #34d399); }
    .card-report::before      { background: linear-gradient(135deg, rgba(16,185,129,0.12), rgba(52,211,153,0.08)); }
    .card-report:hover        { box-shadow: 0 20px 48px rgba(16,185,129,0.25); }

    .card-profile .accent-line{ background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .card-profile::before     { background: linear-gradient(135deg, rgba(245,158,11,0.12), rgba(251,191,36,0.08)); }
    .card-profile:hover       { box-shadow: 0 20px 48px rgba(245,158,11,0.25); }

    .card-logout .accent-line { background: linear-gradient(90deg, #ef4444, #f87171); }
    .card-logout::before      { background: linear-gradient(135deg, rgba(239,68,68,0.12), rgba(248,113,113,0.08)); }
    .card-logout:hover        { box-shadow: 0 20px 48px rgba(239,68,68,0.2); }

    /* Card icon badge */
    .card-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 20px;
        position: relative;
        z-index: 1;
    }

    .card-qr     .card-icon { background: rgba(59,130,246,0.2);  color: #93c5fd; }
    .card-report .card-icon { background: rgba(16,185,129,0.2);  color: #6ee7b7; }
    .card-profile.card-icon { background: rgba(245,158,11,0.2);  color: #fcd34d; }
    .card-profile .card-icon{ background: rgba(245,158,11,0.2);  color: #fcd34d; }
    .card-logout  .card-icon{ background: rgba(239,68,68,0.2);   color: #fca5a5; }

    .dash-card h5 {
        font-size: 1.05rem;
        font-weight: 700;
        margin-bottom: 6px;
        position: relative;
        z-index: 1;
    }

    .dash-card p {
        font-size: 0.82rem;
        color: rgba(255,255,255,0.5);
        margin: 0;
        line-height: 1.5;
        position: relative;
        z-index: 1;
    }

    .card-arrow {
        position: absolute;
        bottom: 24px;
        right: 24px;
        font-size: 1rem;
        color: rgba(255,255,255,0.25);
        transition: transform 0.2s, color 0.2s;
        z-index: 1;
    }

    .dash-card:hover .card-arrow {
        transform: translate(3px, -3px);
        color: rgba(255,255,255,0.7);
    }

    /* ── Logout modal ── */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(6px);
        z-index: 100;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.show { display: flex; }

    .modal-box {
        background: #1a1a3e;
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 22px;
        padding: 40px 36px;
        width: 360px;
        text-align: center;
        box-shadow: 0 32px 64px rgba(0,0,0,0.5);
        animation: popIn 0.25s cubic-bezier(0.22,1,0.36,1);
    }

    @keyframes popIn {
        from { opacity: 0; transform: scale(0.92); }
        to   { opacity: 1; transform: scale(1); }
    }

    .modal-icon {
        width: 60px;
        height: 60px;
        border-radius: 18px;
        background: rgba(239,68,68,0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        margin: 0 auto 20px;
    }

    .modal-box h5 {
        font-size: 1.15rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .modal-box p {
        font-size: 0.85rem;
        color: rgba(255,255,255,0.5);
        margin-bottom: 28px;
    }

    .modal-actions { display: flex; gap: 12px; }

    .btn-cancel {
        flex: 1;
        padding: 12px;
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.15);
        background: rgba(255,255,255,0.07);
        color: white;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-cancel:hover { background: rgba(255,255,255,0.12); }

    .btn-confirm-logout {
        flex: 1;
        padding: 12px;
        border-radius: 12px;
        border: none;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.2s, transform 0.2s;
        box-shadow: 0 4px 16px rgba(239,68,68,0.4);
    }
    .btn-confirm-logout:hover { opacity: 0.9; transform: translateY(-1px); }

    @media (max-width: 600px) {
        .top-nav { padding: 0 16px; }
        .main-wrap { padding: 32px 16px 48px; }
        .page-header h2 { font-size: 1.5rem; }
        .cards-grid { grid-template-columns: 1fr 1fr; gap: 14px; }
        .dash-card { padding: 24px 18px; }
    }

    @media (max-width: 400px) {
        .cards-grid { grid-template-columns: 1fr; }
    }
</style>
</head>

<body>

<!-- Ambient orbs -->
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>
<div class="orb orb-3"></div>

<!-- Navbar -->
<nav class="top-nav">
    <div class="nav-brand">
        <div class="brand-icon">📡</div>
        QR Attendance
    </div>
    <div class="nav-user">
        <div class="nav-avatar">👨‍🏫</div>
        <span><?php echo htmlspecialchars($_SESSION['teacher_name']); ?></span>
    </div>
</nav>

<!-- Main content -->
<div class="main-wrap">

    <!-- Header -->
    <div class="page-header">
        <div class="greeting">Good day</div>
        <h2>Welcome back, <?php echo htmlspecialchars(explode(' ', $_SESSION['teacher_name'])[0]); ?> 👋</h2>
        <p>Here's your teaching hub — manage sessions, reports, and your profile.</p>

        <div class="stat-row">
            <div class="stat-pill"><i class="bi bi-calendar-check"></i> Today: <span class="val ms-1">Mon, <?php echo date('d M Y'); ?></span></div>
            <div class="stat-pill"><i class="bi bi-clock"></i> <span class="val me-1"><?php echo date('h:i A'); ?></span></div>
        </div>
    </div>

    <!-- Cards -->
    <div class="section-label">Quick Actions</div>

    <div class="cards-grid">

        <!-- Generate QR -->
        <a href="teacher_setup.php" class="dash-card card-qr">
            <div class="accent-line"></div>
            <div class="card-icon"><i class="bi bi-qr-code"></i></div>
            <h5>Generate QR</h5>
            <p>Start a new attendance session instantly</p>
            <div class="card-arrow"><i class="bi bi-arrow-up-right"></i></div>
        </a>

        <!-- Attendance Report -->
        <a href="attendance_report.php" class="dash-card card-report">
            <div class="accent-line"></div>
            <div class="card-icon"><i class="bi bi-bar-chart-line"></i></div>
            <h5>Attendance Report</h5>
            <p>View and export detailed attendance records</p>
            <div class="card-arrow"><i class="bi bi-arrow-up-right"></i></div>
        </a>

        <!-- Profile -->
        <a href="teacher_profile.php" class="dash-card card-profile">
            <div class="accent-line"></div>
            <div class="card-icon"><i class="bi bi-person-circle"></i></div>
            <h5>My Profile</h5>
            <p>Update your account info and preferences</p>
            <div class="card-arrow"><i class="bi bi-arrow-up-right"></i></div>
        </a>

        <!-- Logout -->
        <div class="dash-card card-logout" onclick="showLogout()">
            <div class="accent-line"></div>
            <div class="card-icon"><i class="bi bi-box-arrow-right"></i></div>
            <h5>Logout</h5>
            <p>End your session securely</p>
            <div class="card-arrow"><i class="bi bi-arrow-up-right"></i></div>
        </div>

    </div>
</div>

<!-- Logout modal -->
<div class="modal-overlay" id="logoutModal">
    <div class="modal-box">
        <div class="modal-icon">🚪</div>
        <h5>Leaving so soon?</h5>
        <p>You'll need to sign in again to access your dashboard.</p>
        <div class="modal-actions">
            <button class="btn-cancel" onclick="hideLogout()">Stay</button>
            <button class="btn-confirm-logout" onclick="window.location='logout.php'">Yes, Logout</button>
        </div>
    </div>
</div>

<script>
    function showLogout() {
        document.getElementById('logoutModal').classList.add('show');
    }
    function hideLogout() {
        document.getElementById('logoutModal').classList.remove('show');
    }
    document.getElementById('logoutModal').addEventListener('click', function(e) {
        if (e.target === this) hideLogout();
    });
</script>

</body>
</html>