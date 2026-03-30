<?php
session_start();
include("db.php");
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
$teachers = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM teachers"))['total'];
$students = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM students"))['total'];
$subjects = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM subjects"))['total'];
$sessions = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM session_qr"))['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
  :root {
    --page-bg:   #0e1015;
    --card-bg:   #16191f;
    --card-bg2:  #1c2028;
    --surface:   #1f232b;
    --gold:      #d4a017;
    --gold-lt:   #f0c040;
    --gold-dim:  rgba(212,160,23,0.15);
    --gold-glow: rgba(212,160,23,0.35);
    --text:      rgba(228, 172, 20, 1);
    --text-muted:#7a7060;
    --border:    rgba(212,160,23,0.1);
  }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    background: var(--page-bg);
    font-family: 'DM Sans', sans-serif;
    color: var(--text);
  }

  /* ── Content area ── */
  .content {
    padding: 2.5rem 2.2rem 4rem;
    max-width: 1280px;
  }

  /* ── Page header ── */
  .dash-header {
    display: flex; align-items: flex-end;
    justify-content: space-between; flex-wrap: wrap;
    gap: 1rem; margin-bottom: .75rem;
  }
  .dash-header .label {
    font-size: .67rem; font-weight: 700; letter-spacing: .2em;
    text-transform: uppercase; color: var(--gold); margin-bottom: .3rem;
  }
  .dash-header h2 {
    font-family: 'Syne', sans-serif;
    font-size: clamp(1.7rem, 3.5vw, 2.6rem);
    font-weight: 800;
    color: var(--text);
    line-height: 1.1;
    opacity: .15;
  }
  .dash-header .timestamp {
    font-size: .76rem; font-weight: 500;
    color: rgba(240,192,64,.85);
    background: var(--card-bg);
    border: 1px solid var(--border);
    padding: .48rem 1.1rem; border-radius: 100px;
    display: flex; align-items: center; gap: .4rem;
    white-space: nowrap;
  }

  /* ── Divider ── */
  .section-divider {
    height: 1px;
    background: linear-gradient(90deg, var(--gold) 0%, transparent 60%);
    opacity: .3; margin-bottom: 2rem;
  }

  /* ── Stat cards ── */
  .stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 1.1rem; margin-bottom: 2.5rem;
  }

  .stat-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 1.1rem;
    padding: 1.5rem 1.4rem 1.3rem;
    position: relative; overflow: hidden;
    transition: transform .28s ease, box-shadow .28s ease, border-color .28s ease;
    cursor: default;
  }
  .stat-card:hover {
    transform: translateY(-5px);
    border-color: rgba(212,160,23,0.3);
    box-shadow: 0 20px 50px rgba(0,0,0,.5), 0 0 30px rgba(212,160,23,.08);
  }

  /* Top gold accent stripe */
  .stat-card::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0;
    height: 2px;
    background: linear-gradient(90deg, var(--gold), var(--gold-lt), transparent);
    border-radius: 1.1rem 1.1rem 0 0;
    opacity: .7;
  }

  /* Inner radial glow on hover */
  .stat-card::after {
    content: '';
    position: absolute; inset: 0;
    background: radial-gradient(ellipse at 0% 0%, rgba(212,160,23,.09), transparent 65%);
    opacity: 0; border-radius: inherit;
    transition: opacity .28s ease;
  }
  .stat-card:hover::after { opacity: 1; }

  .stat-card .icon-wrap {
    width: 46px; height: 46px; border-radius: .8rem;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.25rem;
    background: var(--gold-dim);
    color: var(--gold-lt);
    margin-bottom: 1.1rem;
    position: relative; z-index: 1;
    border: 1px solid rgba(212,160,23,.2);
  }

  .stat-card .stat-number {
    font-family: 'Syne', sans-serif;
    font-size: 2.8rem; font-weight: 800; line-height: 1;
    color: var(--gold-lt);
    position: relative; z-index: 1;
  }

  .stat-card .stat-label {
    font-size: .71rem; font-weight: 600;
    color: var(--text-muted); text-transform: uppercase;
    letter-spacing: .14em; margin-top: .5rem;
    position: relative; z-index: 1;
  }

  .stat-card .stat-badge {
    position: absolute; top: 1.1rem; right: 1.1rem;
    font-size: .65rem; font-weight: 600;
    color: var(--gold);
    background: var(--gold-dim);
    border: 1px solid rgba(212,160,23,.2);
    padding: .2rem .65rem; border-radius: 100px;
    z-index: 1; display: flex; align-items: center; gap: .2rem;
  }

  /* ── Section heading ── */
  .section-title {
    font-family: 'Syne', sans-serif; font-size: .82rem; font-weight: 700;
    letter-spacing: .12em; margin-bottom: 1rem;
    color: rgba(212,160,23,.45); text-transform: uppercase;
    display: flex; align-items: center; gap: .6rem;
  }
  .section-title::after {
    content: ''; flex: 1; height: 1px;
    background: var(--border);
  }

  /* ── Quick-action buttons ── */
  .action-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(155px, 1fr));
    gap: 1rem;
  }

  .action-btn {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 1rem;
    padding: 1.4rem 1rem;
    display: flex; flex-direction: column;
    align-items: center; gap: .55rem;
    text-decoration: none;
    color: var(--text-muted);
    font-size: .71rem; font-weight: 600;
    letter-spacing: .1em; text-transform: uppercase;
    transition: all .25s ease;
  }
  .action-btn i {
    font-size: 1.55rem;
    color: rgba(212,160,23,.5);
    transition: color .25s ease;
  }
  .action-btn:hover {
    border-color: rgba(212,160,23,.4);
    background: rgba(212,160,23,.07);
    color: var(--gold-lt);
    transform: translateY(-3px);
    box-shadow: 0 14px 35px rgba(0,0,0,.4), 0 0 20px rgba(212,160,23,.08);
  }
  .action-btn:hover i { color: var(--gold-lt); }

  /* ── Entrance animations ── */
  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(22px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  .stat-card  { opacity: 0; animation: fadeUp .45s ease forwards; }
  .stat-card:nth-child(1) { animation-delay: .05s; }
  .stat-card:nth-child(2) { animation-delay: .12s; }
  .stat-card:nth-child(3) { animation-delay: .19s; }
  .stat-card:nth-child(4) { animation-delay: .26s; }

  .action-btn { opacity: 0; animation: fadeUp .45s ease forwards; }
  .action-btn:nth-child(1) { animation-delay: .33s; }
  .action-btn:nth-child(2) { animation-delay: .39s; }
  .action-btn:nth-child(3) { animation-delay: .45s; }
  .action-btn:nth-child(4) { animation-delay: .51s; }

  @media (max-width: 576px) {
    .content { padding: 1.5rem 1rem 3rem; }
    .dash-header { flex-direction: column; align-items: flex-start; }
  }
</style>
</head>
<body>

<?php include("admin_layout.php"); ?>

<div class="content">

  <!-- Header -->
  <div class="dash-header">
    <div>
      <div class="label">Admin Panel</div>
      <h2>Dashboard Overview</h2>
    </div>
    <div class="timestamp">
      <i class="bi bi-clock"></i>
      <?php echo date("D, d M Y · h:i A"); ?>
    </div>
  </div>

  <div class="section-divider"></div>

  <!-- Stat Cards -->
  <div class="stat-grid">

    <div class="stat-card">
      <span class="stat-badge"><i class="bi bi-arrow-up-short"></i> Active</span>
      <div class="icon-wrap"><i class="bi bi-person-badge-fill"></i></div>
      <div class="stat-number"><?php echo $teachers; ?></div>
      <div class="stat-label">Total Teachers</div>
    </div>

    <div class="stat-card">
      <span class="stat-badge"><i class="bi bi-mortarboard-fill"></i> Enrolled</span>
      <div class="icon-wrap"><i class="bi bi-people-fill"></i></div>
      <div class="stat-number"><?php echo $students; ?></div>
      <div class="stat-label">Total Students</div>
    </div>

    <div class="stat-card">
      <span class="stat-badge"><i class="bi bi-book-fill"></i> Listed</span>
      <div class="icon-wrap"><i class="bi bi-journal-bookmark-fill"></i></div>
      <div class="stat-number"><?php echo $subjects; ?></div>
      <div class="stat-label">Total Subjects</div>
    </div>

    <div class="stat-card">
      <span class="stat-badge"><i class="bi bi-qr-code"></i> Generated</span>
      <div class="icon-wrap"><i class="bi bi-qr-code-scan"></i></div>
      <div class="stat-number"><?php echo $sessions; ?></div>
      <div class="stat-label">Total Sessions</div>
    </div>

  </div>

  <!-- Quick Actions -->
  <div class="section-title">Quick Actions</div>
  <div class="action-grid">
    <a href="add_teacher.php"  class="action-btn"><i class="bi bi-person-plus-fill"></i>Add Teacher</a>
    <a href="add_student.php"  class="action-btn"><i class="bi bi-person-check-fill"></i>Add Student</a>
    <a href="add_subject.php"  class="action-btn"><i class="bi bi-journal-plus"></i>Add Subject</a>
    <a href="sessions.php"     class="action-btn"><i class="bi bi-qr-code"></i>View Sessions</a>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>