<?php
include("db.php");
$message = "";
if (isset($_POST['signup'])) {
    $full_name   = $_POST['full_name'];
    $register_no = $_POST['register_no'];
    $department  = $_POST['department'];
    $branch      = $_POST['branch'];
    $year        = $_POST['year'];
    $password    = $_POST['password'];
    $confirm     = $_POST['confirm_password'];
    if ($password !== $confirm) {
        $message = "Passwords do not match!";
    } else {
        $check = "SELECT id FROM students WHERE register_no='$register_no'";
        if (mysqli_num_rows(mysqli_query($conn, $check)) > 0) {
            $message = "Register number already exists!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $insert = "INSERT INTO students (full_name,register_no,department,branch,year,password) VALUES ('$full_name','$register_no','$department','$branch','$year','$hashed')";
            $message = mysqli_query($conn, $insert) ? "success" : "Something went wrong. Try again.";
        }
    }
}
$v = fn($k) => isset($_POST[$k]) ? htmlspecialchars($_POST[$k]) : '';
$errStep = 1;
if ($message && $message !== 'success') {
    if (str_contains(strtolower($message),'password')) $errStep = 3;
    elseif (str_contains(strtolower($message),'register')) $errStep = 1;
    else $errStep = 2;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Sign Up · QR Attendance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --teal:   #0fa97a; --teal-d: #0b8a63; --teal-l: #e8f7f2;
            --bg:     #f4faf7; --white:  #ffffff;
            --ink:    #0d1f18; --ink-mid:#2c4a3c; --dim:#6b8c7d; --muted:#adc4ba;
            --border: #d8ece4; --rose:   #e8445a; --gold: #f5b731;
            --r: 16px; --r-sm: 10px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); min-height: 100vh; display: flex; color: var(--ink); }

        /* Left */
        .left { width: 42%; background: var(--teal); display: flex; flex-direction: column; justify-content: space-between; padding: 44px 48px; position: relative; overflow: hidden; }
        .left::before { content:''; position:absolute; width:420px; height:420px; border-radius:50%; background:rgba(255,255,255,0.07); bottom:-140px; left:-110px; }
        .left::after  { content:''; position:absolute; width:260px; height:260px; border-radius:50%; background:rgba(255,255,255,0.05); top:50px; right:-70px; }
        .brand { display:flex; align-items:center; gap:10px; position:relative; z-index:1; }
        .brand-icon { width:36px; height:36px; border-radius:9px; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; font-size:17px; }
        .brand-name { font-size:16px; font-weight:700; color:#fff; }
        .left-mid { position:relative; z-index:1; }
        .badge { display:inline-block; background:rgba(255,255,255,0.18); color:rgba(255,255,255,0.92); font-size:11px; font-weight:600; letter-spacing:0.08em; text-transform:uppercase; padding:5px 14px; border-radius:20px; margin-bottom:22px; }
        .left-mid h2 { font-size:28px; font-weight:800; color:#fff; line-height:1.25; margin-bottom:14px; }
        .left-mid p  { font-size:13px; color:rgba(255,255,255,0.68); line-height:1.75; max-width:260px; }
        .steps-visual { margin-top:32px; display:flex; flex-direction:column; gap:14px; }
        .sv { display:flex; align-items:center; gap:12px; }
        .sv-num { width:28px; height:28px; border-radius:50%; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; color:#fff; flex-shrink:0; }
        .sv-num.done { background:rgba(255,255,255,0.9); color:var(--teal); }
        .sv-text { font-size:13px; color:rgba(255,255,255,0.82); }
        .left-foot { font-size:12px; color:rgba(255,255,255,0.38); position:relative; z-index:1; }

        /* Right */
        .right { flex:1; background:var(--white); display:flex; flex-direction:column; align-items:center; justify-content:center; padding:40px 36px; overflow-y:auto; }
        .form-wrap { width:100%; max-width:400px; animation:fadeUp 0.45s ease both; }

        /* Progress bar */
        .progress-bar { display:flex; align-items:center; gap:0; margin-bottom:32px; }
        .pb-step { display:flex; flex-direction:column; align-items:center; gap:5px; flex:1; }
        .pb-dot { width:30px; height:30px; border-radius:50%; border:2px solid var(--border); background:var(--bg); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; color:var(--muted); transition:all 0.3s; position:relative; z-index:1; }
        .pb-step.active .pb-dot { border-color:var(--teal); background:var(--teal); color:#fff; box-shadow:0 0 0 4px rgba(15,169,122,0.15); }
        .pb-step.done   .pb-dot { border-color:var(--teal); background:var(--teal-l); color:var(--teal); }
        .pb-lbl { font-size:10px; font-weight:600; color:var(--muted); white-space:nowrap; }
        .pb-step.active .pb-lbl { color:var(--teal); }
        .pb-step.done   .pb-lbl { color:var(--teal); }
        .pb-line { flex:1; height:2px; background:var(--border); margin-bottom:16px; transition:background 0.3s; }
        .pb-line.done { background:var(--teal); }

        /* Header */
        .step-head { margin-bottom:24px; }
        .step-head h1 { font-size:22px; font-weight:800; color:var(--ink); margin-bottom:4px; }
        .step-head p  { font-size:13px; color:var(--dim); font-weight:300; }

        /* Toast */
        .toast { display:flex; align-items:center; gap:9px; background:#fff5f6; border:1px solid #fcc; border-left:3px solid var(--rose); color:#b91c1c; padding:11px 14px; border-radius:var(--r-sm); font-size:13px; font-weight:500; margin-bottom:18px; animation:shake 0.4s ease; }
        .toast-ok { background:#f0faf6; border:1px solid #b2dfc8; border-left:3px solid var(--teal); color:var(--teal-d); }

        /* Form */
        .panel { display:none; }
        .panel.active { display:block; animation:fadeUp 0.3s ease both; }
        .form-group { margin-bottom:14px; }
        .form-group label { display:block; font-size:12px; font-weight:600; color:var(--ink-mid); margin-bottom:6px; }
        .input-wrap { position:relative; }
        .input-icon { position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:14px; color:var(--muted); pointer-events:none; }
        .form-group input,
        .form-group select { width:100%; padding:11px 12px 11px 36px; background:var(--bg); border:1.5px solid var(--border); border-radius:var(--r-sm); font-family:'Plus Jakarta Sans',sans-serif; font-size:14px; color:var(--ink); outline:none; transition:all 0.2s; appearance:none; }
        .form-group input:focus,
        .form-group select:focus { border-color:var(--teal); background:#fff; box-shadow:0 0 0 3px rgba(15,169,122,0.12); }
        .form-group input::placeholder { color:var(--muted); }
        .form-group select option { background:#fff; }
        .sel-wrap::after { content:'▾'; position:absolute; right:12px; top:50%; transform:translateY(-50%); color:var(--muted); pointer-events:none; font-size:13px; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
        @media(max-width:480px){ .form-row{grid-template-columns:1fr;} }
        .toggle-pw { position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; font-size:13px; color:var(--muted); }

        /* strength */
        .s-bar  { height:3px; border-radius:2px; background:var(--border); margin-top:6px; overflow:hidden; }
        .s-fill { height:100%; border-radius:2px; width:0; transition:width 0.3s,background 0.3s; }
        .s-lbl  { font-size:10px; margin-top:3px; color:var(--muted); }

        .sep { border:none; border-top:1px solid var(--border); margin:16px 0; }

        /* Buttons */
        .btn-row { display:flex; gap:10px; margin-top:8px; }
        .btn { flex:1; padding:12px; border-radius:var(--r-sm); font-family:'Plus Jakarta Sans',sans-serif; font-size:14px; font-weight:600; cursor:pointer; transition:all 0.2s; border:none; }
        .btn-outline { background:transparent; border:1.5px solid var(--border); color:var(--dim); }
        .btn-outline:hover { background:var(--bg); color:var(--ink); }
        .btn-teal { background:var(--teal); color:#fff; box-shadow:0 4px 16px rgba(15,169,122,0.25); }
        .btn-teal:hover { background:var(--teal-d); transform:translateY(-1px); box-shadow:0 8px 22px rgba(15,169,122,0.35); }
        .btn:active { transform:translateY(0); }
        .btn:disabled { opacity:0.4; cursor:default; }

        .foot-link { text-align:center; margin-top:20px; font-size:13px; color:var(--dim); }
        .foot-link a { color:var(--teal); font-weight:600; text-decoration:none; }
        .foot-link a:hover { text-decoration:underline; }

        @media(max-width:768px){ .left{display:none;} body{background:#fff;} }
        @keyframes fadeUp { from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);} }
        @keyframes shake { 0%,100%{transform:translateX(0);}20%{transform:translateX(-5px);}40%{transform:translateX(5px);}60%{transform:translateX(-3px);}80%{transform:translateX(3px);} }
    </style>
</head>
<body>

<div class="left">
    <div class="brand"><div class="brand-icon">📋</div><span class="brand-name">QR Attendance</span></div>
    <div class="left-mid">
        <span class="badge">Student Registration</span>
        <h2>Join in 3 simple steps</h2>
        <p>Create your account to start checking in and tracking your attendance automatically.</p>
        <div class="steps-visual">
            <div class="sv"><div class="sv-num" id="svn1">1</div><div class="sv-text">Personal Info</div></div>
            <div class="sv"><div class="sv-num" id="svn2">2</div><div class="sv-text">Academic Details</div></div>
            <div class="sv"><div class="sv-num" id="svn3">3</div><div class="sv-text">Set Password</div></div>
        </div>
    </div>
    <div class="left-foot">© QR Attendance System</div>
</div>

<div class="right">
<div class="form-wrap">

    <!-- Progress bar -->
    <div class="progress-bar">
        <div class="pb-step active" id="pbs1"><div class="pb-dot">1</div><div class="pb-lbl">Personal</div></div>
        <div class="pb-line" id="pbl1"></div>
        <div class="pb-step" id="pbs2"><div class="pb-dot">2</div><div class="pb-lbl">Academic</div></div>
        <div class="pb-line" id="pbl2"></div>
        <div class="pb-step" id="pbs3"><div class="pb-dot">3</div><div class="pb-lbl">Security</div></div>
    </div>

    <?php if ($message === 'success'): ?>
    <div class="toast toast-ok">✅ &nbsp;Account created! Redirecting to login…</div>
    <script>setTimeout(()=>window.location.href='student_login.php',2000);</script>
    <?php elseif ($message): ?>
    <div class="toast">⚠️ &nbsp;<?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if ($message !== 'success'): ?>
    <form method="POST" id="sf">

        <!-- Panel 1 -->
        <div class="panel active" id="p1">
            <div class="step-head"><h1>Personal Info</h1><p>Tell us your name and register number</p></div>
            <div class="form-group">
                <label>Full Name</label>
                <div class="input-wrap"><span class="input-icon">👤</span><input type="text" name="full_name" value="<?php echo $v('full_name'); ?>" placeholder="e.g. Anantha Kumar" required></div>
            </div>
            <div class="form-group">
                <label>Register Number</label>
                <div class="input-wrap"><span class="input-icon">🪪</span><input type="text" name="register_no" value="<?php echo $v('register_no'); ?>" placeholder="e.g. 2023CS001" required></div>
            </div>
            <div class="btn-row"><button type="button" class="btn btn-teal" onclick="go(2)">Next →</button></div>
        </div>

        <!-- Panel 2 -->
        <div class="panel" id="p2">
            <div class="step-head"><h1>Academic Details</h1><p>Your department, branch and year</p></div>
            <div class="form-group">
                <label>Department</label>
                <div class="input-wrap"><span class="input-icon">🏛️</span><input type="text" name="department" value="<?php echo $v('department'); ?>" placeholder="e.g. Computer Science" required></div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Branch</label>
                    <div class="input-wrap"><span class="input-icon">🌿</span><input type="text" name="branch" value="<?php echo $v('branch'); ?>" placeholder="e.g. CSE-A" required></div>
                </div>
                <div class="form-group">
                    <label>Year</label>
                    <div class="input-wrap sel-wrap"><span class="input-icon">📅</span>
                        <select name="year" required>
                            <option value="">Select</option>
                            <?php for($y=1;$y<=5;$y++): ?>
                            <option value="<?php echo $y; ?>" <?php echo $v('year')==$y?'selected':''; ?>>Year <?php echo $y; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="btn-row">
                <button type="button" class="btn btn-outline" onclick="go(1)">← Back</button>
                <button type="button" class="btn btn-teal"    onclick="go(3)">Next →</button>
            </div>
        </div>

        <!-- Panel 3 -->
        <div class="panel" id="p3">
            <div class="step-head"><h1>Set Password</h1><p>Choose a strong password for your account</p></div>
            <div class="form-group">
                <label>Create Password</label>
                <div class="input-wrap"><span class="input-icon">🔒</span>
                    <input type="password" name="password" id="pw1" placeholder="Min. 8 characters" required oninput="strength(this.value)">
                    <button type="button" class="toggle-pw" onclick="tp('pw1','t1')" id="t1">👁</button>
                </div>
                <div class="s-bar"><div class="s-fill" id="sf2"></div></div>
                <div class="s-lbl"  id="sl"></div>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <div class="input-wrap"><span class="input-icon">🔐</span>
                    <input type="password" name="confirm_password" id="pw2" placeholder="Repeat password" required>
                    <button type="button" class="toggle-pw" onclick="tp('pw2','t2')" id="t2">👁</button>
                </div>
            </div>
            <div class="btn-row">
                <button type="button" class="btn btn-outline" onclick="go(2)">← Back</button>
                <button type="submit" name="signup" class="btn btn-teal">✓ Create Account</button>
            </div>
        </div>

    </form>
    <div class="foot-link">Already have an account? <a href="student_login.php">Sign In</a></div>
    <?php endif; ?>

</div>
</div>

<script>
let cur = <?php echo ($message && $message!=='success') ? $errStep : 1; ?>;
const total = 3;

function go(n) {
    if (n > cur) {
        const inputs = document.querySelectorAll('#p'+cur+' input[required], #p'+cur+' select[required]');
        for (const i of inputs) { if (!i.value.trim()) { i.focus(); i.style.borderColor='var(--rose)'; setTimeout(()=>i.style.borderColor='',1500); return; } }
    }
    document.getElementById('p'+cur).classList.remove('active');
    setStep(cur, n > cur ? 'done' : '');
    cur = n;
    document.getElementById('p'+cur).classList.add('active');
    setStep(cur, 'active');
    // lines
    for (let i=1;i<=2;i++) document.getElementById('pbl'+i).classList.toggle('done', i<cur);
    // left visual
    for (let i=1;i<=3;i++) document.getElementById('svn'+i).classList.toggle('done', i<cur);
}

function setStep(n, cls) {
    const el = document.getElementById('pbs'+n);
    el.classList.remove('active','done');
    if (cls) el.classList.add(cls);
}

function tp(fid, bid) {
    const f=document.getElementById(fid),b=document.getElementById(bid),h=f.type==='password';
    f.type=h?'text':'password'; b.textContent=h?'🙈':'👁';
}

function strength(v) {
    const fill=document.getElementById('sf2'), lbl=document.getElementById('sl');
    let s=0;
    if(v.length>=8)s++;if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[^A-Za-z0-9]/.test(v))s++;
    const l=[{w:'0%',bg:'transparent',t:''},{w:'25%',bg:'#e8445a',t:'Weak'},{w:'50%',bg:'#f5b731',t:'Fair'},{w:'75%',bg:'#f5b731',t:'Good'},{w:'100%',bg:'#0fa97a',t:'Strong'}][s];
    fill.style.width=l.w; fill.style.background=l.bg; lbl.textContent=l.t; lbl.style.color=l.bg;
}

document.addEventListener('DOMContentLoaded',()=>{ if(cur!==1) go(cur); });
</script>
</body>
</html>