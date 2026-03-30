<?php
session_start();
include("db.php");

$message  = "";
$msg_type = "";

if (isset($_POST['signup'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $message  = "Passwords do not match!";
        $msg_type = "error";
    } else {
        $check = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username'");
        if (mysqli_num_rows($check) > 0) {
            $message  = "Username already exists!";
            $msg_type = "error";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($conn, "INSERT INTO admin (username, password) VALUES ('$username', '$hashed')");
            $message  = "success";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Sign Up · QR Attendance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg:      #0a0a0f;
            --bg2:     #111118;
            --panel:   #16161f;
            --panel2:  #1e1e2a;
            --border:  rgba(255,255,255,0.07);
            --border2: rgba(255,255,255,0.12);
            --text:    #f0eff8;
            --dim:     #7a7a9a;
            --muted:   #3f3f5a;
            --gold:    #d4a843;
            --gold-l:  #f0c96a;
            --gold-dim:rgba(212,168,67,0.12);
            --rose:    #e05068;
            --green:   #34d399;
            --r:       16px;
            --r-sm:    10px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text);
            padding: 28px 16px;
            background-image:
                radial-gradient(ellipse 60% 50% at 50% 0%,   rgba(212,168,67,0.08) 0%, transparent 60%),
                radial-gradient(ellipse 40% 40% at 80% 80%,  rgba(212,168,67,0.04) 0%, transparent 55%);
        }

        .wrap {
            width: 100%; max-width: 420px;
            display: flex; flex-direction: column; align-items: center;
        }

        /* Seal */
        .seal {
            width: 60px; height: 60px; border-radius: 50%;
            background: var(--panel2);
            border: 1px solid rgba(212,168,67,0.3);
            display: flex; align-items: center; justify-content: center;
            font-size: 24px;
            box-shadow: 0 0 0 6px rgba(212,168,67,0.06), 0 8px 24px rgba(0,0,0,0.4);
            margin-bottom: -30px; position: relative; z-index: 2;
            animation: fadeDown 0.5s ease both;
        }

        /* Card */
        .card {
            width: 100%;
            background: var(--panel);
            border: 1px solid var(--border2);
            border-radius: var(--r);
            overflow: hidden;
            box-shadow: 0 32px 80px rgba(0,0,0,0.6), 0 0 0 1px rgba(212,168,67,0.06);
            animation: fadeUp 0.5s 0.05s ease both;
        }

        .card-top {
            height: 3px;
            background: linear-gradient(90deg, #8b6914, var(--gold), #d4a843, #8b6914);
            background-size: 200% 100%;
            animation: shimmer 3s linear infinite;
        }
        @keyframes shimmer { 0%{background-position:200% 0;} 100%{background-position:-200% 0;} }

        .card-body { padding: 44px 36px 36px; }

        /* Header */
        .card-head { text-align: center; margin-bottom: 28px; }
        .admin-label {
            display: inline-block;
            background: var(--gold-dim);
            border: 1px solid rgba(212,168,67,0.25);
            color: var(--gold);
            font-size: 10px; font-weight: 700;
            letter-spacing: 0.12em; text-transform: uppercase;
            padding: 4px 14px; border-radius: 20px;
            margin-bottom: 14px;
        }
        .card-head h1 {
            font-family: 'Instrument Serif', serif;
            font-size: 26px; font-weight: 400;
            color: var(--text); letter-spacing: -0.2px; margin-bottom: 5px;
        }
        .card-head p { font-size: 13px; color: var(--dim); font-weight: 300; }

        /* Divider */
        .divider { display:flex; align-items:center; gap:12px; margin-bottom:24px; }
        .divider-line { flex:1; height:1px; background:var(--border); }
        .divider-dot  { width:4px; height:4px; border-radius:50%; background:var(--muted); }

        /* Toast */
        .toast { display:flex; align-items:center; gap:9px; padding:11px 14px; border-radius:var(--r-sm); font-size:13px; font-weight:500; margin-bottom:20px; }
        .toast-dot { width:6px; height:6px; border-radius:50%; flex-shrink:0; }
        .toast.error   { background:rgba(224,80,104,0.10); border:1px solid rgba(224,80,104,0.25); color:#f87171; animation:shake 0.4s ease; }
        .toast.error   .toast-dot { background:var(--rose); }
        .toast.success { background:rgba(52,211,153,0.10); border:1px solid rgba(52,211,153,0.25); color:var(--green); animation:fadeDown 0.3s ease both; }
        .toast.success .toast-dot { background:var(--green); }

        /* Form */
        .form-group { margin-bottom:16px; }
        .form-group label { display:block; font-size:11px; font-weight:700; letter-spacing:0.07em; text-transform:uppercase; color:var(--dim); margin-bottom:7px; }
        .input-wrap { position:relative; }
        .input-icon { position:absolute; left:13px; top:50%; transform:translateY(-50%); font-size:14px; color:var(--muted); pointer-events:none; }
        .form-group input { width:100%; padding:12px 13px 12px 38px; background:var(--bg2); border:1.5px solid var(--border); border-radius:var(--r-sm); font-family:'DM Sans',sans-serif; font-size:14px; color:var(--text); outline:none; transition:all 0.2s; }
        .form-group input:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(212,168,67,0.12); background:var(--panel2); }
        .form-group input::placeholder { color:var(--muted); }
        .toggle-pw { position:absolute; right:11px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; font-size:14px; color:var(--muted); transition:color 0.2s; }
        .toggle-pw:hover { color:var(--dim); }

        /* Separator */
        .sep { border:none; border-top:1px solid var(--border); margin:18px 0; }

        /* Strength */
        .s-bar  { height:3px; border-radius:2px; background:var(--border); margin-top:6px; overflow:hidden; }
        .s-fill { height:100%; border-radius:2px; width:0; transition:width 0.3s,background 0.3s; }
        .s-lbl  { font-size:10px; margin-top:4px; color:var(--muted); }

        /* Button */
        .btn-signup {
            width:100%; padding:13px;
            background: linear-gradient(135deg, #c49520, var(--gold));
            color: var(--bg); border:none; border-radius:var(--r-sm);
            font-family:'DM Sans',sans-serif; font-size:15px; font-weight:700;
            cursor:pointer; transition:all 0.2s; margin-top:8px;
            box-shadow:0 4px 18px rgba(212,168,67,0.28);
        }
        .btn-signup:hover { filter:brightness(1.1); transform:translateY(-1px); box-shadow:0 8px 26px rgba(212,168,67,0.38); }
        .btn-signup:active { transform:translateY(0); }

        /* Footer */
        .foot { text-align:center; margin-top:20px; font-size:12px; color:var(--muted); }
        .foot a { color:var(--gold); font-weight:600; text-decoration:none; opacity:0.8; transition:opacity 0.2s; }
        .foot a:hover { opacity:1; }

        .brand-foot { margin-top:28px; display:flex; align-items:center; gap:8px; font-size:13px; color:var(--muted); animation:fadeDown 0.5s 0.2s ease both; }
        .brand-foot-icon { width:22px; height:22px; border-radius:6px; background:var(--panel2); border:1px solid var(--border2); display:flex; align-items:center; justify-content:center; font-size:11px; }

        /* Animations */
        @keyframes fadeDown { from{opacity:0;transform:translateY(-14px);}to{opacity:1;transform:translateY(0);} }
        @keyframes fadeUp   { from{opacity:0;transform:translateY(18px);}to{opacity:1;transform:translateY(0);} }
        @keyframes shake    { 0%,100%{transform:translateX(0);}20%{transform:translateX(-5px);}40%{transform:translateX(5px);}60%{transform:translateX(-3px);}80%{transform:translateX(3px);} }
    </style>
</head>
<body>

<div class="wrap">

    <div class="seal">🛡️</div>

    <div class="card">
        <div class="card-top"></div>
        <div class="card-body">

            <div class="card-head">
                <div class="admin-label">Administrator Registration</div>
                <h1>Create admin account</h1>
                <p>Set up your admin credentials below</p>
            </div>

            <div class="divider">
                <div class="divider-line"></div>
                <div class="divider-dot"></div>
                <div class="divider-line"></div>
            </div>

            <?php if ($message === 'success'): ?>
            <div class="toast success">
                <div class="toast-dot"></div>
                Admin account created! Redirecting to login…
            </div>
            <script>setTimeout(()=>window.location.href='admin_login.php', 2000);</script>

            <?php elseif ($message): ?>
            <div class="toast error">
                <div class="toast-dot"></div>
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>

            <?php if ($message !== 'success'): ?>
            <form method="POST">

                <div class="form-group">
                    <label>Username</label>
                    <div class="input-wrap">
                        <span class="input-icon">👤</span>
                        <input type="text" name="username"
                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                               placeholder="Choose a username" required>
                    </div>
                </div>

                <hr class="sep">

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">🔒</span>
                        <input type="password" name="password" id="pw1"
                               placeholder="Min. 8 characters" required
                               oninput="checkStr(this.value)">
                        <button type="button" class="toggle-pw" onclick="tp('pw1','t1')" id="t1">👁</button>
                    </div>
                    <div class="s-bar"><div class="s-fill" id="sfill"></div></div>
                    <div class="s-lbl"  id="slbl"></div>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">🔐</span>
                        <input type="password" name="confirm_password" id="pw2"
                               placeholder="Repeat password" required>
                        <button type="button" class="toggle-pw" onclick="tp('pw2','t2')" id="t2">👁</button>
                    </div>
                </div>

                <button type="submit" name="signup" class="btn-signup">
                    Create Admin Account
                </button>
            </form>

            <div class="foot">
                Already have an account? <a href="admin_login.php">Sign in</a>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <div class="brand-foot">
        <div class="brand-foot-icon">📋</div>
        QR Attendance · Admin Portal
    </div>

</div>

<script>
function tp(fid, bid) {
    const f=document.getElementById(fid), b=document.getElementById(bid), h=f.type==='password';
    f.type=h?'text':'password'; b.textContent=h?'🙈':'👁';
}

function checkStr(v) {
    const fill=document.getElementById('sfill'), lbl=document.getElementById('slbl');
    let s=0;
    if(v.length>=8)s++; if(/[A-Z]/.test(v))s++; if(/[0-9]/.test(v))s++; if(/[^A-Za-z0-9]/.test(v))s++;
    const lvl=[
        {w:'0%',  bg:'transparent',   t:''},
        {w:'25%', bg:'#e05068',       t:'Weak'},
        {w:'50%', bg:'#f59e0b',       t:'Fair'},
        {w:'75%', bg:'#d4a843',       t:'Good'},
        {w:'100%',bg:'#34d399',       t:'Strong'},
    ][s];
    fill.style.width=lvl.w; fill.style.background=lvl.bg;
    lbl.textContent=lvl.t; lbl.style.color=lvl.bg;
}
</script>
</body>
</html>