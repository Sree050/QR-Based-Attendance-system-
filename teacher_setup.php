<?php
date_default_timezone_set('Asia/Kolkata');

session_start();
include("db.php");

if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacher_login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

if (isset($_POST['generate_qr'])) {

    $semester = $_POST['semester'];
    $subject_id = $_POST['subject_id'];
    $hour = $_POST['hour'];

    $token = bin2hex(random_bytes(5));

    mysqli_query($conn,
        "INSERT INTO session_qr
        (teacher_id, subject_id, token, expiry_time, created_at, hour)
        VALUES
        ('$teacher_id', '$subject_id', '$token',
         DATE_ADD(NOW(), INTERVAL 2 MINUTE), NOW(), '$hour')"
    );

    $session_id = mysqli_insert_id($conn);

    header("Location: show_qr.php?session_id=$session_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Generate QR — Attendance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .card-wrapper {
            width: 100%;
            max-width: 480px;
        }

        /* Header badge */
        .page-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 0.35rem 0.85rem;
            border-radius: 999px;
            margin-bottom: 1rem;
        }

        .page-title {
            color: #fff;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.3rem;
        }

        .page-subtitle {
            color: rgba(255,255,255,0.55);
            font-size: 0.88rem;
            margin-bottom: 1.75rem;
        }

        /* Card */
        .main-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 20px;
            padding: 2rem;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.4);
        }

        /* Step indicator */
        .steps {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.75rem;
        }

        .step-dot {
            height: 4px;
            flex: 1;
            border-radius: 999px;
            background: rgba(255,255,255,0.15);
            transition: background 0.3s;
        }

        .step-dot.active {
            background: #818cf8;
        }

        /* Field groups */
        .field-group {
            margin-bottom: 1.25rem;
        }

        .field-label {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: rgba(255,255,255,0.6);
            margin-bottom: 0.5rem;
        }

        .field-label i {
            font-size: 0.9rem;
            color: #818cf8;
        }

        .styled-select {
            width: 100%;
            appearance: none;
            -webkit-appearance: none;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 12px;
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 0.92rem;
            padding: 0.75rem 2.75rem 0.75rem 1rem;
            outline: none;
            transition: border-color 0.25s, background 0.25s, box-shadow 0.25s;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23818cf8' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            cursor: pointer;
        }

        .styled-select:focus {
            border-color: #818cf8;
            background-color: rgba(129,140,248,0.1);
            box-shadow: 0 0 0 3px rgba(129,140,248,0.18);
        }

        .styled-select option {
            background: #302b63;
            color: #fff;
        }

        .styled-select:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        /* Loading spinner inside subject dropdown */
        .select-wrapper {
            position: relative;
        }

        .select-spinner {
            display: none;
            position: absolute;
            right: 2.6rem;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,0.2);
            border-top-color: #818cf8;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        .select-spinner.show { display: block; }

        @keyframes spin { to { transform: translateY(-50%) rotate(360deg); } }

        /* Divider */
        .divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.1);
            margin: 1.5rem 0;
        }

        /* Info row */
        .info-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
            color: rgba(255,255,255,0.4);
            margin-bottom: 1.25rem;
        }

        .info-row i { color: #fbbf24; font-size: 0.95rem; }

        /* Submit button */
        .btn-generate {
            width: 100%;
            padding: 0.85rem;
            border: none;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.03em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            transition: opacity 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 4px 20px rgba(99,102,241,0.4);
        }

        .btn-generate:hover:not(:disabled) {
            opacity: 0.92;
            transform: translateY(-1px);
            box-shadow: 0 8px 28px rgba(99,102,241,0.5);
        }

        .btn-generate:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn-generate:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            box-shadow: none;
        }

        /* Step dots update on select */
        select:valid ~ * { color: #818cf8; }
    </style>
</head>

<body>

<div class="card-wrapper">

    <div class="text-center">
        <span class="page-badge"><i class="bi bi-qr-code"></i> Attendance System</span>
        <div class="page-title">Generate QR Code</div>
        <div class="page-subtitle">Fill in the session details to create a timed QR for student attendance.</div>
    </div>

    <div class="main-card">

        <!-- Step indicator -->
        <div class="steps" id="stepDots">
            <div class="step-dot" id="dot1"></div>
            <div class="step-dot" id="dot2"></div>
            <div class="step-dot" id="dot3"></div>
        </div>

        <form method="POST" id="qrForm">

            <!-- Semester -->
            <div class="field-group">
                <div class="field-label"><i class="bi bi-layers"></i> Semester</div>
                <div class="select-wrapper">
                    <select name="semester" id="semester" class="styled-select" required>
                        <option value="">Choose a semester…</option>
                        <?php for($i=1;$i<=8;$i++): ?>
                            <option value="<?php echo $i; ?>">Semester <?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <!-- Subject -->
            <div class="field-group">
                <div class="field-label"><i class="bi bi-book"></i> Subject</div>
                <div class="select-wrapper">
                    <select name="subject_id" id="subjectDropdown" class="styled-select" required disabled>
                        <option value="">Select semester first…</option>
                    </select>
                    <div class="select-spinner" id="subjectSpinner"></div>
                </div>
            </div>

            <!-- Hour -->
            <div class="field-group">
                <div class="field-label"><i class="bi bi-clock"></i> Hour</div>
                <div class="select-wrapper">
                    <select name="hour" id="hourSelect" class="styled-select" required disabled>
                        <option value="">Choose an hour…</option>
                        <?php for($i=1;$i<=7;$i++): ?>
                            <option value="<?php echo $i; ?>">Hour <?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <hr class="divider">

            <div class="info-row">
                <i class="bi bi-stopwatch"></i>
                QR code expires automatically after <strong>&nbsp;2 minutes</strong>.
            </div>

            <button type="submit" name="generate_qr" class="btn-generate" id="submitBtn" disabled>
                <i class="bi bi-qr-code-scan"></i> Generate QR Code
            </button>

        </form>

    </div>

</div>

<script>
    const semesterEl   = document.getElementById('semester');
    const subjectEl    = document.getElementById('subjectDropdown');
    const hourEl       = document.getElementById('hourSelect');
    const submitBtn    = document.getElementById('submitBtn');
    const spinner      = document.getElementById('subjectSpinner');
    const dots         = [document.getElementById('dot1'), document.getElementById('dot2'), document.getElementById('dot3')];

    function updateDots() {
        const s = semesterEl.value;
        const sub = subjectEl.value;
        const h = hourEl.value;
        dots[0].classList.toggle('active', !!s);
        dots[1].classList.toggle('active', !!(s && sub));
        dots[2].classList.toggle('active', !!(s && sub && h));
    }

    function checkSubmit() {
        const ready = semesterEl.value && subjectEl.value && hourEl.value;
        submitBtn.disabled = !ready;
        updateDots();
    }

    semesterEl.addEventListener('change', function () {
        const semester = this.value;

        // Reset dependents
        subjectEl.innerHTML = '<option value="">Loading subjects…</option>';
        subjectEl.disabled = true;
        hourEl.disabled = true;
        hourEl.value = '';
        submitBtn.disabled = true;
        spinner.classList.add('show');
        updateDots();

        if (!semester) {
            subjectEl.innerHTML = '<option value="">Select semester first…</option>';
            spinner.classList.remove('show');
            return;
        }

        fetch("load_teacher_subjects.php?semester=" + semester)
            .then(r => r.text())
            .then(data => {
                subjectEl.innerHTML = data;
                subjectEl.disabled = false;
                spinner.classList.remove('show');
                updateDots();
            })
            .catch(() => {
                subjectEl.innerHTML = '<option value="">Failed to load — retry</option>';
                spinner.classList.remove('show');
            });
    });

    subjectEl.addEventListener('change', function () {
        hourEl.disabled = !this.value;
        if (!this.value) hourEl.value = '';
        checkSubmit();
    });

    hourEl.addEventListener('change', checkSubmit);
</script>

</body>
</html>