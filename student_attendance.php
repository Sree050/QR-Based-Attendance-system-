<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("db.php");

if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$name       = htmlspecialchars($_SESSION['student_name']);
$initial    = strtoupper(substr($_SESSION['student_name'], 0, 1));

// Fetch all subjects that have sessions
$query = "
SELECT s.id, s.subject_name, s.subject_code
FROM subjects s
JOIN session_qr sq ON s.id = sq.subject_id
GROUP BY s.id
";
$result = mysqli_query($conn, $query);

$rows         = [];
$total_all    = 0;
$present_all  = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $sid = $row['id'];

    $tc = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT COUNT(*) as t FROM session_qr WHERE subject_id='$sid'"))['t'];

    $pr = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT COUNT(*) as t FROM attendance a
         JOIN session_qr sq ON a.session_id = sq.id
         WHERE a.student_id='$student_id' AND sq.subject_id='$sid'"))['t'];

    $pct = $tc > 0 ? round(($pr / $tc) * 100, 1) : 0;

    $rows[] = [
        'name'    => $row['subject_name'],
        'code'    => $row['subject_code'],
        'total'   => $tc,
        'present' => $pr,
        'pct'     => $pct,
    ];

    $total_all   += $tc;
    $present_all += $pr;
}

$overall_pct = $total_all > 0 ? round(($present_all / $total_all) * 100, 1) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Attendance · QR Attendance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --teal:    #0fa97a; --teal-d: #0b8a63; --teal-l: #e8f7f2; --teal-ll:#f4fdf9;
            --bg:      #f4faf7; --white:  #ffffff;
            --ink:     #0d1f18; --ink-mid:#2c4a3c; --dim:#6b8c7d; --muted:#adc4ba;
            --border:  #d8ece4;
            --rose:    #e8445a; --rose-l: #fff5f6;
            --gold:    #f59e0b; --gold-l: #fffbeb;
            --blue:    #3b82f6; --blue-l: #eff6ff;
            --r: 18px; --r-sm: 12px;
            --shadow:    0 2px 16px rgba(15,169,122,0.08);
            --shadow-md: 0 8px 32px rgba(15,169,122,0.12);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); min-height: 100vh; color: var(--ink); }

        /* Topbar */
        .topbar { background:var(--white); border-bottom:1px solid var(--border); padding:0 36px; height:62px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:100; box-shadow:0 1px 12px rgba(15,169,122,0.06); }
        .tb-brand { display:flex; align-items:center; gap:10px; text-decoration:none; }
        .tb-icon  { width:34px; height:34px; border-radius:9px; background:var(--teal); display:flex; align-items:center; justify-content:center; font-size:16px; box-shadow:0 3px 12px rgba(15,169,122,0.3); }
        .tb-name  { font-size:16px; font-weight:700; color:var(--ink); }
        .tb-user  { display:flex; align-items:center; gap:9px; background:var(--teal-l); border:1px solid #c5e8d8; border-radius:10px; padding:6px 14px 6px 8px; font-size:13px; font-weight:600; color:var(--ink-mid); }
        .tb-avatar{ width:28px; height:28px; border-radius:7px; background:var(--teal); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:#fff; }

        /* Main */
        .main { max-width: 900px; margin: 0 auto; padding: 44px 24px 80px; }

        /* Back link */
        .back-link { display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:600; color:var(--dim); text-decoration:none; margin-bottom:28px; transition:color 0.2s; }
        .back-link:hover { color:var(--teal); }

        /* Page heading */
        .page-head { margin-bottom:28px; animation:fadeDown 0.5s ease both; }
        .page-head .eyebrow { font-size:11px; font-weight:600; letter-spacing:0.09em; text-transform:uppercase; color:var(--teal); margin-bottom:6px; }
        .page-head h1 { font-size:26px; font-weight:800; color:var(--ink); margin-bottom:4px; }
        .page-head p  { font-size:14px; color:var(--dim); font-weight:300; }

        /* Summary cards */
        .summary-row { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:28px; animation:fadeUp 0.45s 0.06s ease both; }
        @media(max-width:600px){ .summary-row{grid-template-columns:1fr 1fr;} }
        @media(max-width:380px){ .summary-row{grid-template-columns:1fr;} }

        .s-card { background:var(--white); border:1px solid var(--border); border-radius:var(--r-sm); padding:18px 20px; box-shadow:var(--shadow); position:relative; overflow:hidden; }
        .s-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; }
        .s-card.sc-total   ::before, .s-card.sc-total::before   { background:var(--blue); }
        .s-card.sc-present ::before, .s-card.sc-present::before { background:var(--teal); }
        .s-card.sc-overall ::before, .s-card.sc-overall::before { background:<?php echo $overall_pct >= 75 ? 'var(--teal)' : 'var(--rose)'; ?>; }
        .s-card .lbl { font-size:10px; font-weight:600; letter-spacing:0.08em; text-transform:uppercase; color:var(--muted); margin-bottom:8px; }
        .s-card .val { font-size:26px; font-weight:800; color:var(--ink); line-height:1; }
        .s-card .val.teal  { color:var(--teal); }
        .s-card .val.rose  { color:var(--rose); }
        .s-card .sub { font-size:11px; color:var(--muted); margin-top:4px; }

        /* Overall ring */
        .ring-wrap { display:flex; align-items:center; gap:14px; }
        .ring { position:relative; width:52px; height:52px; flex-shrink:0; }
        .ring svg { transform:rotate(-90deg); }
        .ring-bg   { fill:none; stroke:var(--border); stroke-width:5; }
        .ring-fill { fill:none; stroke-width:5; stroke-linecap:round; transition:stroke-dashoffset 1s ease; }
        .ring-num  { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:800; }

        /* Table card */
        .table-card { background:var(--white); border:1px solid var(--border); border-radius:var(--r); overflow:hidden; box-shadow:var(--shadow-md); animation:fadeUp 0.45s 0.1s ease both; }
        .table-head { padding:20px 24px 16px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; }
        .table-head h2 { font-size:16px; font-weight:800; color:var(--ink); }
        .table-head p  { font-size:12px; color:var(--dim); margin-top:2px; }

        .filter-chips { display:flex; gap:6px; }
        .chip { padding:5px 14px; border-radius:20px; border:1.5px solid var(--border); background:transparent; font-family:'Plus Jakarta Sans',sans-serif; font-size:12px; font-weight:600; color:var(--dim); cursor:pointer; transition:all 0.2s; }
        .chip.active       { background:var(--teal); border-color:var(--teal); color:#fff; }
        .chip.chip-warn    { }
        .chip.chip-warn.active { background:var(--rose); border-color:var(--rose); color:#fff; }

        /* Table */
        .att-table { width:100%; border-collapse:collapse; }
        .att-table th { text-align:left; font-size:10px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:var(--muted); padding:12px 20px; border-bottom:1px solid var(--border); background:var(--bg); white-space:nowrap; }
        .att-table td { padding:14px 20px; border-bottom:1px solid var(--border); font-size:14px; color:var(--ink); vertical-align:middle; }
        .att-table tr:last-child td { border-bottom:none; }
        .att-table tr { transition:background 0.15s; }
        .att-table tbody tr:hover td { background:var(--teal-ll); }
        .att-table tr.row-warn td   { background:#fff9f9; }
        .att-table tr.row-warn:hover td { background:#fff5f5; }

        .code-chip { font-family:'Courier New',monospace; font-size:12px; font-weight:600; background:var(--bg); border:1px solid var(--border); border-radius:6px; padding:3px 10px; color:var(--ink-mid); }

        /* Progress bar in table */
        .pct-wrap { display:flex; align-items:center; gap:10px; }
        .pct-bar  { flex:1; height:6px; border-radius:3px; background:var(--border); overflow:hidden; min-width:60px; }
        .pct-fill { height:100%; border-radius:3px; transition:width 0.8s ease; }
        .pct-fill.good { background:var(--teal); }
        .pct-fill.warn { background:var(--rose); }
        .pct-num  { font-size:13px; font-weight:700; white-space:nowrap; min-width:40px; text-align:right; }
        .pct-num.good { color:var(--teal-d); }
        .pct-num.warn { color:var(--rose); }

        /* Status badge */
        .badge { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:20px; font-size:11px; font-weight:700; }
        .badge-ok   { background:var(--teal-l); color:var(--teal-d); }
        .badge-warn { background:var(--rose-l); color:var(--rose); }
        .badge-dot  { width:6px; height:6px; border-radius:50%; flex-shrink:0; }
        .badge-ok   .badge-dot { background:var(--teal); }
        .badge-warn .badge-dot { background:var(--rose); }

        /* Empty state */
        .empty { text-align:center; padding:56px 20px; }
        .empty-ico { font-size:48px; margin-bottom:14px; opacity:0.5; }
        .empty p   { font-size:15px; color:var(--dim); }

        /* Hidden row */
        .att-table tr.hidden-row { display:none; }

        /* Animations */
        @keyframes fadeDown { from{opacity:0;transform:translateY(-14px);}to{opacity:1;transform:translateY(0);} }
        @keyframes fadeUp   { from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);} }

        @media(max-width:600px){
            .topbar{padding:0 16px;} .main{padding:28px 14px 60px;}
            .att-table th:nth-child(3),.att-table td:nth-child(3),
            .att-table th:nth-child(4),.att-table td:nth-child(4){ display:none; }
        }
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

    <!-- Page heading -->
    <div class="page-head">
        <div class="eyebrow">Attendance Report</div>
        <h1>My Attendance</h1>
        <p>Subject-wise breakdown of your attendance across all classes</p>
    </div>

    <!-- Summary row -->
    <div class="summary-row">
        <div class="s-card sc-total">
            <div class="lbl">Total Classes</div>
            <div class="val blue"><?php echo $total_all; ?></div>
            <div class="sub">across <?php echo count($rows); ?> subjects</div>
        </div>
        <div class="s-card sc-present">
            <div class="lbl">Classes Attended</div>
            <div class="val teal"><?php echo $present_all; ?></div>
            <div class="sub"><?php echo $total_all - $present_all; ?> missed</div>
        </div>
        <div class="s-card sc-overall">
            <div class="lbl">Overall Percentage</div>
            <div class="ring-wrap">
                <div class="ring">
                    <svg viewBox="0 0 52 52" width="52" height="52">
                        <circle class="ring-bg"   cx="26" cy="26" r="22"/>
                        <circle class="ring-fill" cx="26" cy="26" r="22"
                            stroke="<?php echo $overall_pct >= 75 ? 'var(--teal)' : 'var(--rose)'; ?>"
                            stroke-dasharray="<?php echo round(2 * 3.14159 * 22, 1); ?>"
                            stroke-dashoffset="<?php echo round((1 - $overall_pct/100) * 2 * 3.14159 * 22, 1); ?>"
                            id="ring-fill"/>
                    </svg>
                    <div class="ring-num" style="color:<?php echo $overall_pct >= 75 ? 'var(--teal-d)' : 'var(--rose)'; ?>">
                        <?php echo $overall_pct; ?>
                    </div>
                </div>
                <div>
                    <div class="val <?php echo $overall_pct >= 75 ? 'teal' : 'rose'; ?>" style="font-size:22px;">
                        <?php echo $overall_pct; ?>%
                    </div>
                    <div class="sub"><?php echo $overall_pct >= 75 ? 'Good standing' : 'Needs attention'; ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table card -->
    <div class="table-card">
        <div class="table-head">
            <div>
                <h2>Subject-wise Breakdown</h2>
                <p><?php echo count($rows); ?> subjects enrolled</p>
            </div>
            <div class="filter-chips">
                <button class="chip active" onclick="filterRows('all',this)">All</button>
                <button class="chip chip-warn" onclick="filterRows('warn',this)">Below 75%</button>
            </div>
        </div>

        <?php if (empty($rows)): ?>
        <div class="empty">
            <div class="empty-ico">📭</div>
            <p>No attendance data available yet.</p>
        </div>
        <?php else: ?>
        <div style="overflow-x:auto;">
        <table class="att-table" id="att-table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Code</th>
                    <th>Total</th>
                    <th>Present</th>
                    <th>Attendance</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $r):
                $good = $r['pct'] >= 75;
            ?>
            <tr class="<?php echo $good ? '' : 'row-warn'; ?>" data-warn="<?php echo $good ? '0' : '1'; ?>">
                <td style="font-weight:600;"><?php echo htmlspecialchars($r['name']); ?></td>
                <td><span class="code-chip"><?php echo htmlspecialchars($r['code']); ?></span></td>
                <td style="color:var(--dim);"><?php echo $r['total']; ?></td>
                <td style="font-weight:600;"><?php echo $r['present']; ?></td>
                <td style="min-width:160px;">
                    <div class="pct-wrap">
                        <div class="pct-bar">
                            <div class="pct-fill <?php echo $good?'good':'warn'; ?>"
                                 style="width:<?php echo $r['pct']; ?>%;"></div>
                        </div>
                        <span class="pct-num <?php echo $good?'good':'warn'; ?>"><?php echo $r['pct']; ?>%</span>
                    </div>
                </td>
                <td>
                    <?php if ($good): ?>
                    <span class="badge badge-ok"><span class="badge-dot"></span>Good</span>
                    <?php else: ?>
                    <span class="badge badge-warn"><span class="badge-dot"></span>Below 75%</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>

</div>

<script>
function filterRows(type, btn) {
    document.querySelectorAll('.filter-chips .chip').forEach(c => c.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('#att-table tbody tr').forEach(row => {
        if (type === 'all') { row.classList.remove('hidden-row'); }
        else { row.classList.toggle('hidden-row', row.dataset.warn !== '1'); }
    });
}
</script>
</body>
</html>