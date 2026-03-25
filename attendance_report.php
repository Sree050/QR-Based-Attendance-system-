<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("db.php");

if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacher_login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

$report_generated = false;
$total_students = 0;
$present = 0;
$absent = 0;
$percentage = 0;
$absentees = [];
$subject_name = "";
$subject_code = "";
$selected_date = "";
$session_found = false;

$subjects_query = "
SELECT s.id, s.subject_name, s.subject_code
FROM subjects s
JOIN teacher_subjects ts ON s.id = ts.subject_id
WHERE ts.teacher_id = '$teacher_id'
";
$subjects_result = mysqli_query($conn, $subjects_query);

if (isset($_POST['view_report'])) {

    $subject_id = $_POST['subject_id'];
    $selected_date = $_POST['date'];

    $sub_result = mysqli_query($conn, "SELECT subject_name, subject_code, semester FROM subjects WHERE id = '$subject_id'");
    $subject = mysqli_fetch_assoc($sub_result);
    $subject_name = $subject['subject_name'];
    $subject_code = $subject['subject_code'];
    $semester = $subject['semester'];

    $session_result = mysqli_query($conn, "
        SELECT id FROM session_qr
        WHERE teacher_id = '$teacher_id' AND subject_id = '$subject_id'
        AND DATE(created_at) = '$selected_date'
        ORDER BY id DESC LIMIT 1
    ");

    if ($session = mysqli_fetch_assoc($session_result)) {
        $session_found = true;
        $session_id = $session['id'];

        $present = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM attendance WHERE session_id = '$session_id'"))['total'];
        $total_students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM students WHERE year = '$semester'"))['total'];
        $absent = $total_students - $present;
        if ($total_students > 0) $percentage = round(($present / $total_students) * 100, 2);

        $abs_result = mysqli_query($conn, "
            SELECT full_name, register_no FROM students
            WHERE year = '$semester'
            AND id NOT IN (SELECT student_id FROM attendance WHERE session_id = '$session_id')
        ");
        while ($row = mysqli_fetch_assoc($abs_result)) $absentees[] = $row;
        $report_generated = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Attendance Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            padding: 2.5rem 1rem;
            color: #fff;
        }

        .page-wrap { max-width: 820px; margin: 0 auto; display: flex; flex-direction: column; gap: 1.25rem; }

        /* Page header */
        .page-header { text-align: center; margin-bottom: 0.5rem; }
        .page-badge {
            display: inline-flex; align-items: center; gap: 0.4rem;
            background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);
            color: rgba(255,255,255,0.7); font-size: 0.72rem; font-weight: 600;
            letter-spacing: 0.08em; text-transform: uppercase;
            padding: 0.32rem 0.9rem; border-radius: 999px; margin-bottom: 0.65rem;
        }
        .page-badge i { color: #818cf8; }
        .page-title { font-size: 1.75rem; font-weight: 800; color: #fff; }
        .page-sub { font-size: 0.85rem; color: rgba(255,255,255,0.45); margin-top: 0.25rem; }

        /* Glass card */
        .glass-card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 20px;
            padding: 1.75rem;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.35);
        }

        /* Form */
        .form-row { display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end; }
        .form-col { flex: 1; min-width: 180px; }

        .field-label {
            font-size: 0.72rem; font-weight: 600; text-transform: uppercase;
            letter-spacing: 0.08em; color: rgba(255,255,255,0.5);
            display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.45rem;
        }
        .field-label i { color: #818cf8; }

        .styled-input, .styled-select {
            width: 100%; appearance: none; -webkit-appearance: none;
            background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.15);
            border-radius: 11px; color: #fff; font-family: 'Inter', sans-serif;
            font-size: 0.88rem; padding: 0.72rem 1rem; outline: none;
            transition: border-color 0.25s, box-shadow 0.25s;
        }
        .styled-input:focus, .styled-select:focus {
            border-color: #818cf8;
            box-shadow: 0 0 0 3px rgba(129,140,248,0.18);
        }
        .styled-select {
            padding-right: 2.5rem;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='%23818cf8' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 0.9rem center;
            cursor: pointer;
        }
        .styled-select option { background: #302b63; }
        input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); opacity: 0.5; cursor: pointer; }

        .btn-report {
            width: 100%; padding: 0.75rem; border: none; border-radius: 11px;
            font-family: 'Inter', sans-serif; font-size: 0.9rem; font-weight: 600;
            cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff; box-shadow: 0 4px 18px rgba(99,102,241,0.35);
            transition: opacity 0.2s, transform 0.15s;
        }
        .btn-report:hover { opacity: 0.9; transform: translateY(-1px); }

        /* Report header */
        .report-subject { font-size: 1.2rem; font-weight: 700; color: #fff; }
        .report-meta {
            display: flex; gap: 0.6rem; flex-wrap: wrap; margin-top: 0.5rem;
        }
        .pill {
            display: inline-flex; align-items: center; gap: 0.3rem;
            font-size: 0.72rem; font-weight: 600; padding: 0.28rem 0.7rem;
            border-radius: 999px;
        }
        .pill-code { background: rgba(129,140,248,0.18); color: #a5b4fc; border: 1px solid rgba(129,140,248,0.3); }
        .pill-date { background: rgba(52,211,153,0.12); color: #6ee7b7; border: 1px solid rgba(52,211,153,0.25); }
        .pill-day  { background: rgba(251,191,36,0.12); color: #fde68a; border: 1px solid rgba(251,191,36,0.25); }

        .divider { border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 1.25rem 0; }

        /* Stat cards */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.85rem; }
        @media(max-width:600px) { .stats-grid { grid-template-columns: repeat(2,1fr); } }

        .stat-card {
            background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
            border-radius: 14px; padding: 1rem; text-align: center;
        }
        .stat-icon { font-size: 1.3rem; margin-bottom: 0.3rem; }
        .stat-value { font-size: 1.7rem; font-weight: 800; line-height: 1; }
        .stat-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.07em; color: rgba(255,255,255,0.45); margin-top: 0.25rem; }
        .stat-total  .stat-value { color: #e2e8f0; }
        .stat-present .stat-value { color: #34d399; }
        .stat-absent  .stat-value { color: #f87171; }
        .stat-pct    .stat-value { color: #818cf8; }

        /* Attendance bar */
        .att-bar-wrap { margin: 1.25rem 0 0.25rem; }
        .att-bar-labels { display: flex; justify-content: space-between; font-size: 0.72rem; color: rgba(255,255,255,0.45); margin-bottom: 0.4rem; }
        .att-bar-track { height: 8px; background: rgba(255,255,255,0.08); border-radius: 999px; overflow: hidden; }
        .att-bar-fill { height: 100%; border-radius: 999px; transition: width 1s ease; }

        /* Absentees */
        .section-title {
            font-size: 0.72rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.1em; color: rgba(255,255,255,0.45); margin-bottom: 0.85rem;
            display: flex; align-items: center; gap: 0.4rem;
        }
        .section-title i { color: #f87171; }

        .absentee-list { display: flex; flex-direction: column; gap: 0.5rem; }
        .absentee-row {
            display: flex; align-items: center; justify-content: space-between;
            background: rgba(248,113,113,0.07); border: 1px solid rgba(248,113,113,0.15);
            border-radius: 10px; padding: 0.65rem 0.9rem;
            font-size: 0.85rem;
        }
        .absentee-name { font-weight: 600; color: #fca5a5; }
        .absentee-reg { font-size: 0.75rem; color: rgba(255,255,255,0.4); }

        .no-absent {
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            background: rgba(52,211,153,0.08); border: 1px solid rgba(52,211,153,0.2);
            border-radius: 12px; padding: 1rem;
            font-size: 0.9rem; font-weight: 600; color: #34d399;
        }

        /* Alert */
        .alert-glass {
            display: flex; align-items: center; gap: 0.75rem;
            background: rgba(248,113,113,0.1); border: 1px solid rgba(248,113,113,0.25);
            border-radius: 14px; padding: 1rem 1.25rem;
            color: #fca5a5; font-size: 0.88rem; font-weight: 500;
        }
        .alert-glass i { font-size: 1.2rem; flex-shrink: 0; }
    </style>
</head>
<body>

<div class="page-wrap">

    <!-- Header -->
    <div class="page-header">
        <div class="page-badge"><i class="bi bi-bar-chart-line"></i> Reports</div>
        <div class="page-title">Attendance Report</div>
        <div class="page-sub">Select a subject and date to view session attendance</div>
    </div>

    <!-- Filter card -->
    <div class="glass-card">
        <form method="POST">
            <div class="form-row">

                <div class="form-col">
                    <div class="field-label"><i class="bi bi-calendar3"></i> Date</div>
                    <input type="date" name="date" class="styled-input"
                        value="<?php echo htmlspecialchars($selected_date); ?>" required>
                </div>

                <div class="form-col">
                    <div class="field-label"><i class="bi bi-book"></i> Subject</div>
                    <select name="subject_id" class="styled-select" required>
                        <option value="">Choose Subject</option>
                        <?php while($row = mysqli_fetch_assoc($subjects_result)): ?>
                            <option value="<?php echo $row['id']; ?>"
                                <?php if(isset($_POST['subject_id']) && $_POST['subject_id'] == $row['id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($row['subject_name']); ?>
                                (<?php echo htmlspecialchars($row['subject_code']); ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-col" style="flex:0 0 auto; min-width:150px;">
                    <div class="field-label" style="opacity:0">.</div>
                    <button name="view_report" class="btn-report">
                        <i class="bi bi-search"></i> View Report
                    </button>
                </div>

            </div>
        </form>
    </div>

    <?php if ($report_generated): ?>

        <!-- Report card -->
        <div class="glass-card">

            <!-- Subject & meta -->
            <div class="report-subject"><?php echo htmlspecialchars($subject_name); ?></div>
            <div class="report-meta">
                <span class="pill pill-code"><i class="bi bi-hash"></i><?php echo htmlspecialchars($subject_code); ?></span>
                <span class="pill pill-date"><i class="bi bi-calendar3"></i><?php echo htmlspecialchars($selected_date); ?></span>
                <span class="pill pill-day"><i class="bi bi-sun"></i><?php echo date('l', strtotime($selected_date)); ?></span>
            </div>

            <hr class="divider">

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card stat-total">
                    <div class="stat-icon">👥</div>
                    <div class="stat-value"><?php echo $total_students; ?></div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-card stat-present">
                    <div class="stat-icon">✅</div>
                    <div class="stat-value"><?php echo $present; ?></div>
                    <div class="stat-label">Present</div>
                </div>
                <div class="stat-card stat-absent">
                    <div class="stat-icon">❌</div>
                    <div class="stat-value"><?php echo $absent; ?></div>
                    <div class="stat-label">Absent</div>
                </div>
                <div class="stat-card stat-pct">
                    <div class="stat-icon">📊</div>
                    <div class="stat-value"><?php echo $percentage; ?>%</div>
                    <div class="stat-label">Rate</div>
                </div>
            </div>

            <!-- Attendance bar -->
            <div class="att-bar-wrap">
                <div class="att-bar-labels">
                    <span>Attendance</span>
                    <span><?php echo $percentage; ?>%</span>
                </div>
                <div class="att-bar-track">
                    <?php
                        $barColor = $percentage >= 75
                            ? 'linear-gradient(90deg,#34d399,#6ee7b7)'
                            : ($percentage >= 50
                                ? 'linear-gradient(90deg,#f59e0b,#fbbf24)'
                                : 'linear-gradient(90deg,#ef4444,#f87171)');
                    ?>
                    <div class="att-bar-fill" style="width:<?php echo $percentage; ?>%; background:<?php echo $barColor; ?>"></div>
                </div>
            </div>

            <hr class="divider">

            <!-- Absentees -->
            <div class="section-title">
                <i class="bi bi-person-x-fill"></i> Absentees
                <?php if(!empty($absentees)): ?>
                    <span class="pill pill-code" style="margin-left:0.3rem;"><?php echo count($absentees); ?></span>
                <?php endif; ?>
            </div>

            <?php if (!empty($absentees)): ?>
                <div class="absentee-list">
                    <?php foreach($absentees as $i => $student): ?>
                        <div class="absentee-row">
                            <div>
                                <span style="font-size:0.72rem;color:rgba(255,255,255,0.3);margin-right:0.5rem;"><?php echo $i+1; ?>.</span>
                                <span class="absentee-name"><?php echo htmlspecialchars($student['full_name']); ?></span>
                            </div>
                            <span class="absentee-reg"><?php echo htmlspecialchars($student['register_no']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-absent"><i class="bi bi-patch-check-fill"></i> Full attendance — no absentees!</div>
            <?php endif; ?>

        </div>

    <?php elseif(isset($_POST['view_report']) && !$session_found): ?>

        <div class="alert-glass">
            <i class="bi bi-exclamation-circle-fill"></i>
            No attendance session found for the selected date and subject. Please check your selection.
        </div>

    <?php endif; ?>

</div>

</body>
</html>