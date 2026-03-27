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
$message    = "";
$msg_type   = "";

$query   = "SELECT * FROM students WHERE id = '$student_id'";
$result  = mysqli_query($conn, $query);
$student = mysqli_fetch_assoc($result);

if (isset($_POST['update_profile'])) {
    $full_name  = $_POST['full_name'];
    $department = $_POST['department'];
    $branch     = $_POST['branch'];
    $year       = $_POST['year'];

    $update = "UPDATE students SET full_name='$full_name', department='$department',
               branch='$branch', year='$year' WHERE id='$student_id'";
    mysqli_query($conn, $update);
    $_SESSION['student_name'] = $full_name;
    $student['full_name']  = $full_name;
    $student['department'] = $department;
    $student['branch']     = $branch;
    $student['year']       = $year;
    $message  = "Profile updated successfully!";
    $msg_type = "success";
}

if (isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new     = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if (!password_verify($current, $student['password'])) {
        $message = "Current password is incorrect!";
        $msg_type = "error";
    } elseif ($new !== $confirm) {
        $message = "New passwords do not match!";
        $msg_type = "error";
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE students SET password='$hashed' WHERE id='$student_id'");
        $message  = "Password changed successfully!";
        $msg_type = "success";
    }
}

$name    = htmlspecialchars($student['full_name']);
$initial = strtoupper(substr($student['full_name'], 0, 1));

$active_tab = 'overview';
if ($message) {
    if (isset($_POST['update_profile']))  $active_tab = 'edit';
    if (isset($_POST['change_password'])) $active_tab = 'security';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Profile · QR Attendance</title>
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
            --purple:  #8b5cf6; --purple-l:#f5f3ff;
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
        .main { max-width: 760px; margin: 0 auto; padding: 44px 24px 80px; }

        /* Back */
        .back-link { display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:600; color:var(--dim); text-decoration:none; margin-bottom:28px; transition:color 0.2s; }
        .back-link:hover { color:var(--teal); }

        /* Hero */
        .hero {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--r);
            padding: 28px 32px;
            display: flex; align-items: center; gap: 20px;
            margin-bottom: 24px;
            box-shadow: var(--shadow);
            animation: fadeDown 0.5s ease both;
            position: relative; overflow: hidden;
        }
        .hero::after { content:''; position:absolute; right:-50px; top:-50px; width:180px; height:180px; border-radius:50%; background:var(--teal-ll); }

        .hero-avatar {
            width: 64px; height: 64px; border-radius: 18px;
            background: linear-gradient(135deg, var(--teal), #34d399);
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; font-weight: 800; color: #fff;
            flex-shrink: 0;
            box-shadow: 0 6px 20px rgba(15,169,122,0.32);
            position: relative; z-index: 1;
        }
        .hero-text { position: relative; z-index: 1; }
        .hero-text .eyebrow { font-size:11px; font-weight:600; letter-spacing:0.09em; text-transform:uppercase; color:var(--teal); margin-bottom:4px; }
        .hero-text h1 { font-size:22px; font-weight:800; color:var(--ink); margin-bottom:3px; }
        .hero-text p  { font-size:13px; color:var(--dim); font-weight:300; }

        /* Toast */
        .toast { display:flex; align-items:center; gap:9px; padding:11px 16px; border-radius:var(--r-sm); font-size:13px; font-weight:500; margin-bottom:20px; animation:shake 0.4s ease; }
        .toast-dot { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
        .toast.success { background:#f0faf6; border:1px solid #b2dfc8; border-left:3px solid var(--teal); color:var(--teal-d); animation:fadeDown 0.3s ease both; }
        .toast.success .toast-dot { background:var(--teal); }
        .toast.error   { background:var(--rose-l); border:1px solid #fcc; border-left:3px solid var(--rose); color:#b91c1c; }
        .toast.error   .toast-dot { background:var(--rose); }

        /* Tabs */
        .tabs { display:flex; gap:4px; background:var(--white); border:1px solid var(--border); border-radius:14px; padding:4px; margin-bottom:20px; box-shadow:var(--shadow); animation:fadeDown 0.5s 0.06s ease both; }
        .tab-btn { flex:1; padding:9px 12px; border:none; background:transparent; border-radius:10px; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; font-weight:600; color:var(--dim); cursor:pointer; transition:all 0.2s; }
        .tab-btn.active { background:var(--teal); color:#fff; box-shadow:0 3px 12px rgba(15,169,122,0.25); }
        .tab-btn:not(.active):hover { background:var(--teal-l); color:var(--teal-d); }

        /* Tab panels */
        .tab-panel { display:none; }
        .tab-panel.active { display:block; }

        /* Card */
        .card { background:var(--white); border:1.5px solid var(--border); border-radius:var(--r); overflow:hidden; box-shadow:var(--shadow-md); animation:fadeUp 0.45s 0.1s ease both; }
        .card-stripe { height:4px; }
        .card-header { display:flex; align-items:center; gap:14px; padding:20px 26px 16px; border-bottom:1px solid var(--border); }
        .card-icon { width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0; }
        .card-header h2 { font-size:16px; font-weight:800; color:var(--ink); }
        .card-header p  { font-size:12px; color:var(--dim); margin-top:1px; }
        .card-body { padding:24px 26px; }

        /* Info grid */
        .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
        @media(max-width:520px){ .info-grid{grid-template-columns:1fr;} }
        .info-cell { background:var(--bg); border:1px solid var(--border); border-radius:var(--r-sm); padding:14px 16px; }
        .info-cell .lbl { font-size:10px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:var(--muted); margin-bottom:5px; }
        .info-cell .val { font-size:14px; font-weight:600; color:var(--ink); }
        .info-cell .val.teal { color:var(--teal-d); }

        /* Form */
        .form-group { margin-bottom:16px; }
        .form-group label { display:block; font-size:12px; font-weight:700; color:var(--ink-mid); margin-bottom:6px; letter-spacing:0.01em; }
        .input-wrap { position:relative; }
        .input-icon { position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:14px; color:var(--muted); pointer-events:none; }
        .form-group input,
        .form-group select { width:100%; padding:11px 12px 11px 36px; background:var(--bg); border:1.5px solid var(--border); border-radius:var(--r-sm); font-family:'Plus Jakarta Sans',sans-serif; font-size:14px; color:var(--ink); outline:none; transition:all 0.2s; appearance:none; }
        .form-group input:focus,
        .form-group select:focus { border-color:var(--teal); background:#fff; box-shadow:0 0 0 3px rgba(15,169,122,0.12); }
        .form-group input::placeholder { color:var(--muted); }
        .form-group select option { background:#fff; }
        .sel-arrow::after { content:'▾'; position:absolute; right:12px; top:50%; transform:translateY(-50%); color:var(--muted); pointer-events:none; font-size:13px; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
        @media(max-width:520px){ .form-row{grid-template-columns:1fr;} }
        .toggle-pw { position:absolute; right:11px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; font-size:14px; color:var(--muted); transition:color 0.2s; }
        .toggle-pw:hover { color:var(--dim); }
        .sep { border:none; border-top:1px solid var(--border); margin:18px 0; }

        /* Strength */
        .s-bar  { height:3px; border-radius:2px; background:var(--border); margin-top:6px; overflow:hidden; }
        .s-fill { height:100%; border-radius:2px; width:0; transition:width 0.3s,background 0.3s; }
        .s-lbl  { font-size:10px; margin-top:3px; color:var(--muted); }

        /* Buttons */
        .btn { display:inline-flex; align-items:center; gap:7px; padding:11px 22px; border:none; border-radius:var(--r-sm); font-family:'Plus Jakarta Sans',sans-serif; font-size:14px; font-weight:700; cursor:pointer; transition:all 0.2s; }
        .btn-teal { background:var(--teal); color:#fff; box-shadow:0 4px 14px rgba(15,169,122,0.25); }
        .btn-teal:hover { background:var(--teal-d); transform:translateY(-1px); box-shadow:0 8px 20px rgba(15,169,122,0.32); }
        .btn-gold { background:var(--gold); color:#fff; box-shadow:0 4px 14px rgba(245,158,11,0.22); }
        .btn-gold:hover { background:#d97706; transform:translateY(-1px); box-shadow:0 8px 20px rgba(245,158,11,0.32); }
        .btn:active { transform:translateY(0); }

        /* Animations */
        @keyframes fadeDown { from{opacity:0;transform:translateY(-14px);}to{opacity:1;transform:translateY(0);} }
        @keyframes fadeUp   { from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);} }
        @keyframes shake    { 0%,100%{transform:translateX(0);}20%{transform:translateX(-5px);}40%{transform:translateX(5px);}60%{transform:translateX(-3px);}80%{transform:translateX(3px);} }

        @media(max-width:600px){ .topbar{padding:0 16px;} .main{padding:28px 14px 60px;} }
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

    <!-- Hero -->
    <div class="hero">
        <div class="hero-avatar"><?php echo $initial; ?></div>
        <div class="hero-text">
            <div class="eyebrow">My Profile</div>
            <h1><?php echo $name; ?></h1>
            <p><?php echo htmlspecialchars($student['register_no']); ?> &middot; Year <?php echo $student['year']; ?> &middot; <?php echo htmlspecialchars($student['department']); ?></p>
        </div>
    </div>

    <!-- Toast -->
    <?php if ($message): ?>
    <div class="toast <?php echo $msg_type; ?>">
        <div class="toast-dot"></div>
        <?php echo htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>

    <!-- Tabs -->
    <div class="tabs">
        <button class="tab-btn <?php echo $active_tab==='overview' ?'active':''; ?>" onclick="switchTab('overview',this)">Overview</button>
        <button class="tab-btn <?php echo $active_tab==='edit'     ?'active':''; ?>" onclick="switchTab('edit',this)">Edit Profile</button>
        <button class="tab-btn <?php echo $active_tab==='security' ?'active':''; ?>" onclick="switchTab('security',this)">Security</button>
    </div>

    <!-- OVERVIEW -->
    <div id="tab-overview" class="tab-panel <?php echo $active_tab==='overview'?'active':''; ?>">
        <div class="card">
            <div class="card-stripe" style="background:linear-gradient(90deg,var(--teal),#34d399);"></div>
            <div class="card-header">
                <div class="card-icon" style="background:var(--teal-l);">👤</div>
                <div><h2>Account Information</h2><p>Your personal and academic details</p></div>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-cell">
                        <div class="lbl">Full Name</div>
                        <div class="val"><?php echo $name; ?></div>
                    </div>
                    <div class="info-cell">
                        <div class="lbl">Register Number</div>
                        <div class="val teal"><?php echo htmlspecialchars($student['register_no']); ?></div>
                    </div>
                    <div class="info-cell">
                        <div class="lbl">Department</div>
                        <div class="val"><?php echo htmlspecialchars($student['department']); ?></div>
                    </div>
                    <div class="info-cell">
                        <div class="lbl">Branch</div>
                        <div class="val"><?php echo htmlspecialchars($student['branch']); ?></div>
                    </div>
                    <div class="info-cell">
                        <div class="lbl">Year</div>
                        <div class="val">Year <?php echo htmlspecialchars($student['year']); ?></div>
                    </div>
                    <div class="info-cell">
                        <div class="lbl">Joined</div>
                        <div class="val"><?php echo date('d M Y', strtotime($student['created_at'])); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- EDIT -->
    <div id="tab-edit" class="tab-panel <?php echo $active_tab==='edit'?'active':''; ?>">
        <div class="card">
            <div class="card-stripe" style="background:linear-gradient(90deg,var(--blue),var(--teal));"></div>
            <div class="card-header">
                <div class="card-icon" style="background:var(--blue-l);">✏️</div>
                <div><h2>Edit Profile</h2><p>Update your academic information</p></div>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label>Full Name</label>
                        <div class="input-wrap">
                            <span class="input-icon">👤</span>
                            <input type="text" name="full_name" value="<?php echo $name; ?>" placeholder="Your full name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Department</label>
                        <div class="input-wrap">
                            <span class="input-icon">🏛️</span>
                            <input type="text" name="department" value="<?php echo htmlspecialchars($student['department']); ?>" placeholder="e.g. Computer Science" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Branch</label>
                            <div class="input-wrap">
                                <span class="input-icon">🌿</span>
                                <input type="text" name="branch" value="<?php echo htmlspecialchars($student['branch']); ?>" placeholder="e.g. CSE-A" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Year</label>
                            <div class="input-wrap sel-arrow">
                                <span class="input-icon">📅</span>
                                <select name="year" required>
                                    <?php for($y=1;$y<=5;$y++): ?>
                                    <option value="<?php echo $y; ?>" <?php echo $student['year']==$y?'selected':''; ?>>Year <?php echo $y; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-teal">✓ &nbsp;Save Changes</button>
                </form>
            </div>
        </div>
    </div>

    <!-- SECURITY -->
    <div id="tab-security" class="tab-panel <?php echo $active_tab==='security'?'active':''; ?>">
        <div class="card">
            <div class="card-stripe" style="background:linear-gradient(90deg,var(--gold),var(--rose));"></div>
            <div class="card-header">
                <div class="card-icon" style="background:var(--gold-l);">🔒</div>
                <div><h2>Change Password</h2><p>Keep your account secure</p></div>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label>Current Password</label>
                        <div class="input-wrap">
                            <span class="input-icon">🔑</span>
                            <input type="password" name="current_password" placeholder="Enter current password" required>
                        </div>
                    </div>
                    <hr class="sep">
                    <div class="form-row">
                        <div class="form-group">
                            <label>New Password</label>
                            <div class="input-wrap">
                                <span class="input-icon">🔒</span>
                                <input type="password" name="new_password" id="pw1" placeholder="Min. 8 characters" required oninput="checkStr(this.value)">
                                <button type="button" class="toggle-pw" onclick="tp('pw1','t1')" id="t1">👁</button>
                            </div>
                            <div class="s-bar"><div class="s-fill" id="sfill"></div></div>
                            <div class="s-lbl" id="slbl"></div>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <div class="input-wrap">
                                <span class="input-icon">🔐</span>
                                <input type="password" name="confirm_password" id="pw2" placeholder="Repeat new password" required>
                                <button type="button" class="toggle-pw" onclick="tp('pw2','t2')" id="t2">👁</button>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-gold">🔑 &nbsp;Update Password</button>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
function switchTab(name, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
}
function tp(fid, bid) {
    const f=document.getElementById(fid),b=document.getElementById(bid),h=f.type==='password';
    f.type=h?'text':'password'; b.textContent=h?'🙈':'👁';
}
function checkStr(v) {
    const fill=document.getElementById('sfill'), lbl=document.getElementById('slbl');
    let s=0;
    if(v.length>=8)s++;if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[^A-Za-z0-9]/.test(v))s++;
    const l=[{w:'0%',bg:'transparent',t:''},{w:'25%',bg:'#e8445a',t:'Weak'},{w:'50%',bg:'#f59e0b',t:'Fair'},{w:'75%',bg:'#f59e0b',t:'Good'},{w:'100%',bg:'#0fa97a',t:'Strong'}][s];
    fill.style.width=l.w; fill.style.background=l.bg; lbl.textContent=l.t; lbl.style.color=l.bg;
}
</script>
</body>
</html>