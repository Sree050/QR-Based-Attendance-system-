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
$message = "";
$msg_type = "";
$query = "SELECT * FROM teachers WHERE id = '$teacher_id'";
$result = mysqli_query($conn, $query);
$teacher = mysqli_fetch_assoc($result);

if (isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $check_email = "SELECT id FROM teachers WHERE email = '$email' AND id != '$teacher_id'";
    $check_result = mysqli_query($conn, $check_email);
    if (mysqli_num_rows($check_result) > 0) {
        $message = "Email already exists!";
        $msg_type = "error";
    } else {
        $update = "UPDATE teachers SET name='$name', email='$email' WHERE id='$teacher_id'";
        mysqli_query($conn, $update);
        $_SESSION['teacher_name'] = $name;
        $message = "Profile updated successfully!";
        $msg_type = "success";
    }
}
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    if (!password_verify($current_password, $teacher['password'])) {
        $message = "Current password is incorrect!";
        $msg_type = "error";
    } elseif ($new_password !== $confirm_password) {
        $message = "New passwords do not match!";
        $msg_type = "error";
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $update_pass = "UPDATE teachers SET password='$hashed' WHERE id='$teacher_id'";
        mysqli_query($conn, $update_pass);
        $message = "Password changed successfully!";
        $msg_type = "success";
    }
}
$subject_query = "
SELECT subjects.subject_name, subjects.subject_code, subjects.semester
FROM subjects
JOIN teacher_subjects ON subjects.id = teacher_subjects.subject_id
WHERE teacher_subjects.teacher_id = '$teacher_id'
ORDER BY subjects.semester ASC
";
$subject_result = mysqli_query($conn, $subject_query);
$subjects = [];
while ($row = mysqli_fetch_assoc($subject_result)) {
    $subjects[] = $row;
}

$active_tab = 'overview';
if ($message) {
    if (isset($_POST['update_profile'])) $active_tab = 'edit';
    if (isset($_POST['change_password'])) $active_tab = 'security';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Profile · QR Attendance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:       #0d1128;
            --bg2:      #131831;
            --bg3:      #1a2044;
            --panel:    #1c2240;
            --panel2:   #222849;
            --border:   rgba(255,255,255,0.07);
            --border2:  rgba(255,255,255,0.12);
            --text:     #e8ecf7;
            --dim:      #7b85a8;
            --muted:    #4a5278;
            --blue:     #4f8ef7;
            --cyan:     #30d5c8;
            --gold:     #f5c542;
            --rose:     #f05f74;
            --green:    #34d399;
            --purple:   #a78bfa;
            --r:        18px;
            --r-sm:     12px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            background-image:
                radial-gradient(ellipse 60% 50% at 90% 0%, rgba(79,142,247,0.12) 0%, transparent 55%),
                radial-gradient(ellipse 50% 40% at 5% 100%, rgba(167,139,250,0.10) 0%, transparent 55%);
        }

        /* Navbar */
        .navbar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 32px; height: 62px;
            background: rgba(13,17,40,0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            position: sticky; top: 0; z-index: 100;
        }
        .nav-brand {
            display: flex; align-items: center; gap: 10px;
            font-family: 'Syne', sans-serif; font-size: 17px; font-weight: 700;
            color: var(--text); text-decoration: none;
        }
        .nav-brand-icon {
            width: 34px; height: 34px; border-radius: 9px;
            background: linear-gradient(135deg, var(--blue), var(--purple));
            display: flex; align-items: center; justify-content: center; font-size: 16px;
        }
        .nav-user {
            display: flex; align-items: center; gap: 10px;
            background: var(--panel); border: 1px solid var(--border2);
            border-radius: 10px; padding: 6px 14px 6px 8px;
            font-size: 14px; font-weight: 500;
        }
        .nav-avatar {
            width: 28px; height: 28px; border-radius: 7px;
            background: linear-gradient(135deg, var(--gold), var(--rose));
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif; font-size: 12px; font-weight: 700; color: var(--bg);
        }

        /* Page */
        .page { max-width: 900px; margin: 0 auto; padding: 40px 20px 80px; }

        /* Hero */
        .hero {
            display: flex; align-items: center; gap: 20px;
            margin-bottom: 36px; animation: fadeDown 0.5s ease both;
        }
        .hero-avatar {
            width: 64px; height: 64px; border-radius: 16px;
            background: linear-gradient(135deg, var(--blue), var(--purple));
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif; font-size: 26px; font-weight: 800; color: #fff;
            flex-shrink: 0; box-shadow: 0 8px 28px rgba(79,142,247,0.35);
        }
        .hero-text .eyebrow {
            font-size: 11px; font-weight: 600; letter-spacing: 0.1em;
            text-transform: uppercase; color: var(--dim); margin-bottom: 4px;
        }
        .hero-text h1 {
            font-family: 'Syne', sans-serif; font-size: 24px; font-weight: 700;
            color: var(--text); line-height: 1.1;
        }
        .hero-text p { font-size: 13px; color: var(--dim); margin-top: 3px; font-weight: 300; }

        /* Toast */
        .toast {
            display: flex; align-items: center; gap: 10px;
            padding: 13px 18px; border-radius: var(--r-sm);
            font-size: 14px; font-weight: 500;
            margin-bottom: 24px; animation: fadeDown 0.35s ease both;
        }
        .toast-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .toast.success { background: rgba(52,211,153,0.10); border: 1px solid rgba(52,211,153,0.25); color: var(--green); }
        .toast.success .toast-dot { background: var(--green); }
        .toast.error   { background: rgba(240,95,116,0.10);  border: 1px solid rgba(240,95,116,0.25);  color: var(--rose); }
        .toast.error   .toast-dot { background: var(--rose); }

        /* Tabs */
        .tabs {
            display: flex; gap: 4px;
            background: var(--panel); border: 1px solid var(--border);
            border-radius: 14px; padding: 4px;
            margin-bottom: 24px; animation: fadeDown 0.5s 0.08s ease both;
        }
        .tab-btn {
            flex: 1; padding: 9px 14px; border: none; background: transparent;
            border-radius: 10px; font-family: 'DM Sans', sans-serif;
            font-size: 13px; font-weight: 500; color: var(--dim); cursor: pointer; transition: all 0.2s;
        }
        .tab-btn.active { background: var(--bg3); color: var(--text); box-shadow: 0 2px 10px rgba(0,0,0,0.3); }
        .tab-btn:not(.active):hover { background: rgba(255,255,255,0.04); color: var(--text); }

        /* Card */
        .card { background: var(--panel); border: 1px solid var(--border); border-radius: var(--r); overflow: hidden; animation: fadeUp 0.45s 0.12s ease both; }
        .card-stripe { height: 4px; }
        .card-header { display: flex; align-items: center; gap: 14px; padding: 22px 26px 18px; border-bottom: 1px solid var(--border); }
        .card-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 17px; flex-shrink: 0; }
        .card-header h2 { font-family: 'Syne', sans-serif; font-size: 16px; font-weight: 700; color: var(--text); }
        .card-header p  { font-size: 12px; color: var(--dim); margin-top: 1px; }
        .card-body { padding: 26px; }

        /* Tab panels */
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }

        /* Info grid */
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        @media(max-width:560px){ .info-grid { grid-template-columns: 1fr; } }
        .info-cell { background: var(--bg3); border: 1px solid var(--border); border-radius: var(--r-sm); padding: 15px 18px; }
        .info-cell .lbl { font-size: 10px; font-weight: 600; letter-spacing: 0.09em; text-transform: uppercase; color: var(--muted); margin-bottom: 5px; }
        .info-cell .val { font-size: 14px; font-weight: 500; color: var(--text); }

        /* Form */
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 12px; font-weight: 600; color: var(--dim); margin-bottom: 7px; letter-spacing: 0.02em; }
        .form-group input { width: 100%; padding: 11px 15px; background: var(--bg3); border: 1.5px solid var(--border); border-radius: var(--r-sm); font-family: 'DM Sans', sans-serif; font-size: 14px; color: var(--text); transition: border-color 0.2s, box-shadow 0.2s; outline: none; }
        .form-group input:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(79,142,247,0.15); }
        .form-group input::placeholder { color: var(--muted); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        @media(max-width:560px){ .form-row { grid-template-columns: 1fr; } }
        .sep { border: none; border-top: 1px solid var(--border); margin: 20px 0; }

        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 11px 22px; border: none; border-radius: var(--r-sm); font-family: 'DM Sans', sans-serif; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        .btn-blue  { background: var(--blue); color: #fff; }
        .btn-blue:hover  { background: #6aa3f9; box-shadow: 0 6px 22px rgba(79,142,247,0.35); transform: translateY(-1px); }
        .btn-gold  { background: var(--gold); color: var(--bg); }
        .btn-gold:hover  { background: #f7d060; box-shadow: 0 6px 22px rgba(245,197,66,0.35); transform: translateY(-1px); }
        .btn:active { transform: translateY(0); }

        /* Password strength */
        .strength-bar  { height: 3px; border-radius: 2px; background: var(--border); margin-top: 7px; overflow: hidden; }
        .strength-fill { height: 100%; border-radius: 2px; width: 0; transition: width 0.3s, background 0.3s; }
        .strength-label{ font-size: 11px; margin-top: 4px; color: var(--muted); }

        /* Subject Carousel */
        .subject-carousel { padding: 26px; }
        .subject-card { background: var(--bg3); border: 1px solid var(--border); border-radius: var(--r); padding: 28px 26px; display: none; animation: fadeUp 0.3s ease both; }
        .subject-card.active { display: block; }
        .sem-tag { display: inline-flex; align-items: center; gap: 6px; background: rgba(79,142,247,0.12); border: 1px solid rgba(79,142,247,0.25); color: var(--blue); font-size: 11px; font-weight: 600; letter-spacing: 0.07em; text-transform: uppercase; padding: 4px 12px; border-radius: 20px; margin-bottom: 16px; }
        .subject-card h3 { font-family: 'Syne', sans-serif; font-size: 20px; font-weight: 700; color: var(--text); margin-bottom: 10px; }
        .code-chip { font-size: 13px; color: var(--dim); font-family: 'Courier New', monospace; background: var(--panel2); border: 1px solid var(--border); display: inline-block; padding: 3px 12px; border-radius: 6px; }
        .carousel-nav { display: flex; align-items: center; gap: 12px; margin-top: 22px; }
        .carousel-arrow { width: 34px; height: 34px; border-radius: 9px; border: 1px solid var(--border); background: var(--panel2); color: var(--dim); font-size: 16px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; flex-shrink: 0; }
        .carousel-arrow:hover:not(:disabled) { background: var(--bg3); color: var(--text); border-color: var(--border2); }
        .carousel-arrow:disabled { opacity: 0.3; cursor: default; }
        .carousel-dots { display: flex; gap: 6px; flex: 1; flex-wrap: wrap; }
        .dot { width: 7px; height: 7px; border-radius: 50%; background: var(--border2); cursor: pointer; transition: all 0.2s; }
        .dot.active { background: var(--blue); width: 20px; border-radius: 4px; }
        .carousel-counter { font-size: 12px; color: var(--muted); flex-shrink: 0; }
        .empty-state { text-align: center; padding: 48px 20px; color: var(--dim); }
        .empty-state .icon { font-size: 38px; margin-bottom: 12px; opacity: 0.5; }

        /* Animations */
        @keyframes fadeDown { from { opacity: 0; transform: translateY(-14px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeUp   { from { opacity: 0; transform: translateY(16px);  } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<nav class="navbar">
    <a class="nav-brand" href="teacher_dashboard.php">
        <div class="nav-brand-icon">📋</div>
        QR Attendance
    </a>
    <div class="nav-user">
        <div class="nav-avatar"><?php echo strtoupper(substr($teacher['name'], 0, 1)); ?></div>
        <?php echo htmlspecialchars($teacher['name']); ?>
    </div>
</nav>

<div class="page">

    <div class="hero">
        <div class="hero-avatar"><?php echo strtoupper(substr($teacher['name'], 0, 1)); ?></div>
        <div class="hero-text">
            <div class="eyebrow">My Profile</div>
            <h1><?php echo htmlspecialchars($teacher['name']); ?></h1>
            <p>Teacher &middot; ID #<?php echo $teacher['id']; ?> &middot; Joined <?php echo date('M Y', strtotime($teacher['created_at'])); ?></p>
        </div>
    </div>

    <?php if ($message): ?>
    <div class="toast <?php echo $msg_type; ?>">
        <div class="toast-dot"></div>
        <?php echo htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>

    <div class="tabs">
        <button class="tab-btn <?php echo $active_tab=='overview'?'active':''; ?>" onclick="switchTab('overview',this)">Overview</button>
        <button class="tab-btn <?php echo $active_tab=='edit'?'active':''; ?>"     onclick="switchTab('edit',this)">Edit Profile</button>
        <button class="tab-btn <?php echo $active_tab=='security'?'active':''; ?>" onclick="switchTab('security',this)">Security</button>
        <button class="tab-btn" onclick="switchTab('subjects',this)">Subjects
            <?php if(count($subjects)>0): ?>
            <span style="background:var(--blue);color:#fff;border-radius:8px;padding:1px 7px;font-size:11px;margin-left:4px;"><?php echo count($subjects); ?></span>
            <?php endif; ?>
        </button>
    </div>

    <!-- OVERVIEW -->
    <div id="tab-overview" class="tab-panel <?php echo $active_tab=='overview'?'active':''; ?>">
        <div class="card">
            <div class="card-stripe" style="background:linear-gradient(90deg,var(--blue),var(--cyan));"></div>
            <div class="card-header">
                <div class="card-icon" style="background:rgba(79,142,247,0.12);">👤</div>
                <div><h2>Account Information</h2><p>Your personal details and account status</p></div>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-cell"><div class="lbl">Teacher ID</div><div class="val">#<?php echo htmlspecialchars($teacher['id']); ?></div></div>
                    <div class="info-cell"><div class="lbl">Member Since</div><div class="val"><?php echo date('d M Y', strtotime($teacher['created_at'])); ?></div></div>
                    <div class="info-cell"><div class="lbl">Full Name</div><div class="val"><?php echo htmlspecialchars($teacher['name']); ?></div></div>
                    <div class="info-cell"><div class="lbl">Email Address</div><div class="val"><?php echo htmlspecialchars($teacher['email']); ?></div></div>
                </div>
            </div>
        </div>
    </div>

    <!-- EDIT -->
    <div id="tab-edit" class="tab-panel <?php echo $active_tab=='edit'?'active':''; ?>">
        <div class="card">
            <div class="card-stripe" style="background:linear-gradient(90deg,var(--cyan),var(--green));"></div>
            <div class="card-header">
                <div class="card-icon" style="background:rgba(48,213,200,0.12);">✏️</div>
                <div><h2>Edit Profile</h2><p>Update your name and email address</p></div>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($teacher['name']); ?>" placeholder="Your name" required>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($teacher['email']); ?>" placeholder="you@example.com" required>
                        </div>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-blue">✓ &nbsp;Save Changes</button>
                </form>
            </div>
        </div>
    </div>

    <!-- SECURITY -->
    <div id="tab-security" class="tab-panel <?php echo $active_tab=='security'?'active':''; ?>">
        <div class="card">
            <div class="card-stripe" style="background:linear-gradient(90deg,var(--gold),var(--rose));"></div>
            <div class="card-header">
                <div class="card-icon" style="background:rgba(245,197,66,0.12);">🔒</div>
                <div><h2>Change Password</h2><p>Keep your account secure with a strong password</p></div>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" placeholder="Enter current password" required>
                    </div>
                    <hr class="sep">
                    <div class="form-row">
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" id="new_password" placeholder="Min. 8 characters" required oninput="checkStrength(this.value)">
                            <div class="strength-bar"><div class="strength-fill" id="sfill"></div></div>
                            <div class="strength-label" id="slabel"></div>
                        </div>
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" placeholder="Repeat new password" required>
                        </div>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-gold">🔑 &nbsp;Update Password</button>
                </form>
            </div>
        </div>
    </div>

    <!-- SUBJECTS -->
    <div id="tab-subjects" class="tab-panel">
        <div class="card">
            <div class="card-stripe" style="background:linear-gradient(90deg,var(--purple),var(--blue));"></div>
            <div class="card-header">
                <div class="card-icon" style="background:rgba(167,139,250,0.12);">📚</div>
                <div><h2>Assigned Subjects</h2><p><?php echo count($subjects); ?> subject<?php echo count($subjects)!=1?'s':''; ?> assigned</p></div>
            </div>
            <?php if (count($subjects) > 0): ?>
            <div class="subject-carousel">
                <?php foreach ($subjects as $i => $s): ?>
                <div class="subject-card <?php echo $i===0?'active':''; ?>" id="sc-<?php echo $i; ?>">
                    <div class="sem-tag">📅 Semester <?php echo htmlspecialchars($s['semester']); ?></div>
                    <h3><?php echo htmlspecialchars($s['subject_name']); ?></h3>
                    <span class="code-chip"><?php echo htmlspecialchars($s['subject_code']); ?></span>
                </div>
                <?php endforeach; ?>

                <div class="carousel-nav">
                    <button class="carousel-arrow" id="prev-btn" onclick="carouselNav(-1)" disabled>&#8592;</button>
                    <div class="carousel-dots" id="cdots">
                        <?php foreach ($subjects as $i => $s): ?>
                        <div class="dot <?php echo $i===0?'active':''; ?>" onclick="carouselGo(<?php echo $i; ?>)"></div>
                        <?php endforeach; ?>
                    </div>
                    <div class="carousel-counter" id="ccounter">1 / <?php echo count($subjects); ?></div>
                    <button class="carousel-arrow" id="next-btn" onclick="carouselNav(1)" <?php echo count($subjects)<=1?'disabled':''; ?>>&#8594;</button>
                </div>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <div class="icon">📭</div>
                <p>No subjects assigned yet.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<script>
    const TOTAL = <?php echo count($subjects); ?>;
    let current = 0;

    function switchTab(name, btn) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + name).classList.add('active');
        btn.classList.add('active');
    }

    function carouselGo(idx) {
        document.getElementById('sc-' + current).classList.remove('active');
        document.querySelectorAll('.dot')[current].classList.remove('active');
        current = idx;
        document.getElementById('sc-' + current).classList.add('active');
        document.querySelectorAll('.dot')[current].classList.add('active');
        document.getElementById('ccounter').textContent = (current + 1) + ' / ' + TOTAL;
        document.getElementById('prev-btn').disabled = current === 0;
        document.getElementById('next-btn').disabled = current === TOTAL - 1;
    }

    function carouselNav(dir) {
        const next = current + dir;
        if (next >= 0 && next < TOTAL) carouselGo(next);
    }

    function checkStrength(val) {
        const fill = document.getElementById('sfill');
        const label = document.getElementById('slabel');
        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        const lvl = [
            {w:'0%',  bg:'transparent',t:''},
            {w:'25%', bg:'#f05f74',    t:'Weak'},
            {w:'50%', bg:'#f5a623',    t:'Fair'},
            {w:'75%', bg:'#f5c542',    t:'Good'},
            {w:'100%',bg:'#34d399',    t:'Strong'},
        ][score];
        fill.style.width = lvl.w;
        fill.style.background = lvl.bg;
        label.textContent = lvl.t;
        label.style.color = lvl.bg;
    }
</script>
</body>
</html>