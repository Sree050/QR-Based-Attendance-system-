<?php
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$current = basename($_SERVER['PHP_SELF']);
?>

<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: 'DM Sans', sans-serif;
  background: #f0c040;
  color: #1a1f2e;
}

/* ══════════════════════════════
   SIDEBAR
══════════════════════════════ */
.sidebar {
  width: 240px;
  height: 100vh;
  position: fixed;
  top: 0; left: 0;
  background: #111318;
  border-right: 1px solid rgba(212, 160, 23, 0.12);
  display: flex;
  flex-direction: column;
  z-index: 100;
  overflow: hidden;
}

/* Gold ambient glow top-left */
.sidebar::before {
  content: '';
  position: absolute;
  top: -80px; left: -60px;
  width: 260px; height: 260px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(212,160,23,0.13) 0%, transparent 70%);
  pointer-events: none;
}
/* Subtle bottom glow */
.sidebar::after {
  content: '';
  position: absolute;
  bottom: -60px; right: -60px;
  width: 200px; height: 200px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(212,160,23,0.07) 0%, transparent 70%);
  pointer-events: none;
}

/* ── Brand ── */
.sidebar-brand {
  padding: 1.5rem 1.2rem 1.1rem;
  display: flex;
  align-items: center;
  gap: .75rem;
  border-bottom: 1px solid rgba(212,160,23,0.12);
  margin-bottom: .5rem;
  position: relative; z-index: 1;
}

.brand-icon {
  width: 38px; height: 38px;
  border-radius: .65rem;
  background: linear-gradient(135deg, #d4a017, #f0c040);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.1rem; color: #1a1200;
  flex-shrink: 0;
  box-shadow: 0 4px 16px rgba(212,160,23,0.45);
}

.brand-name {
  font-family: 'Syne', sans-serif;
  font-size: .95rem; font-weight: 800;
  color: #fff; line-height: 1.1;
}
.brand-sub {
  font-size: .6rem; font-weight: 500;
  color: rgba(212,160,23,0.55);
  letter-spacing: .14em; text-transform: uppercase;
  margin-top: 2px;
}

/* ── Admin strip ── */
.admin-strip {
  display: flex; align-items: center; gap: .6rem;
  padding: .65rem .9rem;
  margin: 0 .7rem .5rem;
  border-radius: .65rem;
  background: rgba(212,160,23,0.07);
  border: 1px solid rgba(212,160,23,0.14);
  position: relative; z-index: 1;
}
.admin-avatar {
  width: 30px; height: 30px; border-radius: 50%;
  background: linear-gradient(135deg, #d4a017, #f0c040);
  display: flex; align-items: center; justify-content: center;
  font-size: .72rem; font-weight: 800; color: #1a1200;
  flex-shrink: 0;
}
.admin-name {
  font-size: .75rem; font-weight: 600;
  color: rgba(255,255,255,.8);
}
.admin-role {
  font-size: .58rem; color: rgba(212,160,23,0.55);
  letter-spacing: .1em; text-transform: uppercase;
}

/* ── Nav label ── */
.nav-section-label {
  font-size: .58rem; font-weight: 700;
  letter-spacing: .18em; text-transform: uppercase;
  color: rgba(212,160,23,0.35);
  padding: .8rem 1.35rem .3rem;
  position: relative; z-index: 1;
}

/* ── Nav links ── */
.sidebar-nav {
  flex: 1;
  padding: 0 .65rem;
  display: flex; flex-direction: column;
  gap: .12rem;
  overflow-y: auto; scrollbar-width: none;
  position: relative; z-index: 1;
}
.sidebar-nav::-webkit-scrollbar { display: none; }

.nav-link {
  display: flex; align-items: center; gap: .7rem;
  padding: .62rem .8rem;
  border-radius: .6rem;
  color: rgba(255,255,255,.45);
  text-decoration: none;
  font-size: .82rem; font-weight: 500;
  transition: all .22s ease;
  position: relative; overflow: hidden;
  white-space: nowrap;
}

.nav-icon {
  width: 30px; height: 30px; border-radius: .45rem;
  display: flex; align-items: center; justify-content: center;
  font-size: .9rem;
  background: rgba(255,255,255,.05);
  color: rgba(255,255,255,.4);
  flex-shrink: 0;
  transition: all .22s ease;
}

.nav-dot {
  display: none;
  width: 5px; height: 5px; border-radius: 50%;
  background: #d4a017;
  margin-left: auto; flex-shrink: 0;
}

.nav-link:hover {
  color: #f0c040;
  background: rgba(212,160,23,0.08);
  transform: translateX(3px);
}
.nav-link:hover .nav-icon {
  background: rgba(212,160,23,0.14);
  color: #f0c040;
}

/* Active */
.nav-link.active {
  color: #f0c040;
  background: rgba(212,160,23,0.12);
}
.nav-link.active .nav-icon {
  background: linear-gradient(135deg, #d4a017, #f0c040);
  color: #1a1200;
  box-shadow: 0 4px 14px rgba(212,160,23,0.4);
}
.nav-link.active::before {
  content: '';
  position: absolute; left: 0; top: 18%; bottom: 18%;
  width: 3px;
  background: linear-gradient(180deg, #d4a017, #f0c040);
  border-radius: 0 3px 3px 0;
}
.nav-link.active .nav-dot { display: block; }

/* ── Logout footer ── */
.sidebar-footer {
  padding: .65rem;
  border-top: 1px solid rgba(212,160,23,0.1);
  position: relative; z-index: 1;
}

.logout-btn {
  display: flex; align-items: center; gap: .7rem;
  padding: .62rem .8rem;
  border-radius: .6rem;
  color: rgba(255,255,255,.38);
  text-decoration: none;
  font-size: .82rem; font-weight: 500;
  transition: all .22s ease; width: 100%;
}
.logout-btn .nav-icon {
  background: rgba(255,80,80,.07);
  color: rgba(255,120,120,.5);
}
.logout-btn:hover {
  color: #ff8080;
  background: rgba(255,80,80,.09);
}
.logout-btn:hover .nav-icon {
  background: rgba(255,80,80,.16);
  color: #ff8080;
}

/* ══════════════════════════════
   CONTENT AREA
══════════════════════════════ */
.content {
  margin-left: 240px;
  background: #ec9016ff;
  min-height: 100vh;
}

/* ── Entrance animations ── */
@keyframes slideIn {
  from { opacity: 0; transform: translateX(-12px); }
  to   { opacity: 1; transform: translateX(0); }
}
.nav-link, .logout-btn {
  opacity: 0;
  animation: slideIn .35s ease forwards;
}
.nav-link:nth-child(1) { animation-delay: .05s; }
.nav-link:nth-child(2) { animation-delay: .10s; }
.nav-link:nth-child(3) { animation-delay: .15s; }
.nav-link:nth-child(4) { animation-delay: .20s; }
.nav-link:nth-child(5) { animation-delay: .25s; }
.nav-link:nth-child(6) { animation-delay: .30s; }
.logout-btn            { animation-delay: .35s; }
</style>

<div class="sidebar">

  <!-- Brand -->
  <div class="sidebar-brand">
    <div class="brand-icon"><i class="bi bi-qr-code-scan"></i></div>
    <div>
      <div class="brand-name">QR Attend</div>
      <div class="brand-sub">Admin Portal</div>
    </div>
  </div>

  <!-- Admin strip -->
  <div class="admin-strip">
    <div class="admin-avatar">A</div>
    <div>
      <div class="admin-name">Administrator</div>
      <div class="admin-role">Super Admin</div>
    </div>
  </div>

  <div class="nav-section-label">Main Menu</div>

  <nav class="sidebar-nav">
    <a href="admin_dashboard.php"   class="nav-link <?= $current=='admin_dashboard.php'   ? 'active':'' ?>">
      <span class="nav-icon"><i class="bi bi-speedometer2"></i></span>
      Dashboard <span class="nav-dot"></span>
    </a>
    <a href="manage_teacher.php"    class="nav-link <?= $current=='manage_teacher.php'    ? 'active':'' ?>">
      <span class="nav-icon"><i class="bi bi-person-badge"></i></span>
      Teachers <span class="nav-dot"></span>
    </a>
    <a href="manage_students.php"   class="nav-link <?= $current=='manage_students.php'   ? 'active':'' ?>">
      <span class="nav-icon"><i class="bi bi-people"></i></span>
      Students <span class="nav-dot"></span>
    </a>
    <a href="manage_subjects.php"   class="nav-link <?= $current=='manage_subjects.php'   ? 'active':'' ?>">
      <span class="nav-icon"><i class="bi bi-journal-bookmark"></i></span>
      Subjects <span class="nav-dot"></span>
    </a>
    <a href="assign_subject.php"    class="nav-link <?= $current=='assign_subject.php'    ? 'active':'' ?>">
      <span class="nav-icon"><i class="bi bi-shuffle"></i></span>
      Assign Subjects <span class="nav-dot"></span>
    </a>
    <a href="global_attendance.php" class="nav-link <?= $current=='global_attendance.php' ? 'active':'' ?>">
      <span class="nav-icon"><i class="bi bi-bar-chart-line"></i></span>
      Reports <span class="nav-dot"></span>
    </a>
  </nav>

  <div class="sidebar-footer">
    <a href="admin_logout.php" class="logout-btn">
      <span class="nav-icon"><i class="bi bi-box-arrow-right"></i></span>
      Logout
    </a>
  </div>

</div>