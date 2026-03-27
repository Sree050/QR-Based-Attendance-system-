<?php
session_start();
include("db.php");

$message = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query  = "SELECT * FROM admin WHERE username='$username'";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_id']       = $row['id'];
            $_SESSION['admin_username'] = $row['username'];
            header("Location: admin_dashboard.php");
            exit();
        } else { $message = "Invalid password. Please try again."; }
    } else { $message = "Admin account not found."; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Login · QR Attendance</title>
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
            padding: 24px 16px;
            background-image:
                radial-gradient(ellipse 60% 50% at 50% 0%, rgba(212,168,67,0.08) 0%, transparent 60%),
                radial-gradient(ellipse 40% 40% at 80% 80%, rgba(212,168,67,0.04) 0%, transparent 55%);
        }

        .wrap {
            width: 100%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0;
        }

        /* Top seal */
        .seal {
            width: 60px; height: 60px;
            border-radius: 50%;
            background: var(--panel2);
            border: 1px solid rgba(212,168,67,0.3);
            display: flex; align-items: center; justify-content: center;
            font-size: 24px;
            box-shadow: 0 0 0 6px rgba(212,168,67,0.06), 0 8px 24px rgba(0,0,0,0.4);
            margin-bottom: -30px;
            position: relative; z-index: 2;
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
        .card-head { text-align: center; margin-bottom: 32px; }
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
            color: var(--text); letter-spacing: -0.2px;
            margin-bottom: 5px;
        }
        .card-head p { font-size: 13px; color: var(--dim); font-weight: 300; }

        /* Divider */
        .divider { display:flex; align-items:center; gap:12px; margin-bottom:24px; }
        .divider-line { flex:1; height:1px; background:var(--border); }
        .divider-dot  { width:4px; height:4px; border-radius:50%; background:var(--muted); }

        /* Toast */
        .toast {
            display:flex; align-items:center; gap:9px;
            background:rgba(224,80,104,0.10); border:1px solid rgba(224,80,104,0.25);
            color:#f87171; padding:11px 14px; border-radius:var(--r-sm);
            font-size:13px; font-weight:500; margin-bottom:22px;
            animation: shake 0.4s ease;
        }
        .toast-dot { width:6px; height:6px; border-radius:50%; background:var(--rose); flex-shrink:0; }

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

        /* Button */
        .btn-login {
            width:100%; padding:13px;
            background: linear-gradient(135deg, #c49520, var(--gold));
            color: var(--bg);
            border:none; border-radius:var(--r-sm);
            font-family:'DM Sans',sans-serif; font-size:15px; font-weight:700;
            cursor:pointer; transition:all 0.2s; margin-top:8px;
            box-shadow: 0 4px 18px rgba(212,168,67,0.28);
            letter-spacing:0.01em;
        }
        .btn-login:hover { filter:brightness(1.1); transform:translateY(-1px); box-shadow:0 8px 26px rgba(212,168,67,0.38); }
        .btn-login:active { transform:translateY(0); }

        /* Footer link */
        .foot { text-align:center; margin-top:22px; font-size:12px; color:var(--muted); }
        .foot a { color:var(--gold); font-weight:600; text-decoration:none; opacity:0.8; transition:opacity 0.2s; }
        .foot a:hover { opacity:1; }

        /* Bottom brand */
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
                <div class="admin-label">Administrator Access</div>
                <h1>Welcome back</h1>
                <p>Sign in to the admin control panel</p>
            </div>

            <div class="divider">
                <div class="divider-line"></div>
                <div class="divider-dot"></div>
                <div class="divider-line"></div>
            </div>

            <?php if ($message): ?>
            <div class="toast">
                <div class="toast-dot"></div>
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <div class="input-wrap">
                        <span class="input-icon">👤</span>
                        <input type="text" name="username"
                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                               placeholder="Enter admin username" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">🔒</span>
                        <input type="password" name="password" id="pw" placeholder="Enter your password" required>
                        <button type="button" class="toggle-pw" id="tog" onclick="togglePw()">👁</button>
                    </div>
                </div>

                <button type="submit" name="login" class="btn-login">Sign In to Admin Panel</button>
            </form>

            <div class="foot">
                No account? <a href="admin_signup.php">Create admin account</a>
            </div>

        </div>
    </div>

    <div class="brand-foot">
        <div class="brand-foot-icon">📋</div>
        QR Attendance · Admin Portal
    </div>

</div>

<script>
function togglePw() {
    const f=document.getElementById('pw'), b=document.getElementById('tog'), h=f.type==='password';
    f.type=h?'text':'password'; b.textContent=h?'🙈':'👁';
}
</script>
</body>
</html>