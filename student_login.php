<?php
session_start();
include("db.php");
$message = "";
if (isset($_POST['login'])) {
    $register_no = $_POST['register_no'];
    $password    = $_POST['password'];
    $query  = "SELECT * FROM students WHERE register_no='$register_no'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $student = mysqli_fetch_assoc($result);
        if (password_verify($password, $student['password'])) {
            $_SESSION['student_id']    = $student['id'];
            $_SESSION['student_name']  = $student['full_name'];
            $_SESSION['student_regno'] = $student['register_no'];
            header("Location: student_dashboard.php");
            exit();
        } else { $message = "Incorrect password. Please try again."; }
    } else { $message = "Register number not found in our system."; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Login · QR Attendance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --teal:    #0fa97a;
            --teal-d:  #0b8a63;
            --teal-l:  #e8f7f2;
            --bg:      #f4faf7;
            --white:   #ffffff;
            --ink:     #0d1f18;
            --ink-mid: #2c4a3c;
            --dim:     #6b8c7d;
            --muted:   #adc4ba;
            --border:  #d8ece4;
            --rose:    #e8445a;
            --r:       16px;
            --r-sm:    10px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            color: var(--ink);
        }

        /* Left decorative panel */
        .left {
            width: 44%;
            background: var(--teal);
            display: flex; flex-direction: column;
            justify-content: space-between;
            padding: 44px 48px;
            position: relative; overflow: hidden;
        }
        .left::before {
            content: ''; position: absolute;
            width: 450px; height: 450px; border-radius: 50%;
            background: rgba(255,255,255,0.07);
            bottom: -150px; left: -120px;
        }
        .left::after {
            content: ''; position: absolute;
            width: 280px; height: 280px; border-radius: 50%;
            background: rgba(255,255,255,0.05);
            top: 40px; right: -80px;
        }
        .brand { display: flex; align-items: center; gap: 10px; position: relative; z-index: 1; }
        .brand-icon { width: 36px; height: 36px; border-radius: 9px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 17px; }
        .brand-name { font-size: 16px; font-weight: 700; color: #fff; }
        .left-mid { position: relative; z-index: 1; }
        .student-badge { display: inline-block; background: rgba(255,255,255,0.18); color: rgba(255,255,255,0.92); font-size: 11px; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; padding: 5px 14px; border-radius: 20px; margin-bottom: 22px; }
        .left-mid h2 { font-size: 30px; font-weight: 800; color: #fff; line-height: 1.25; margin-bottom: 14px; }
        .left-mid p  { font-size: 14px; color: rgba(255,255,255,0.68); line-height: 1.75; max-width: 270px; }
        .features { margin-top: 28px; display: flex; flex-direction: column; gap: 11px; }
        .feat { display: flex; align-items: center; gap: 10px; font-size: 13px; color: rgba(255,255,255,0.82); }
        .feat-dot { width: 6px; height: 6px; border-radius: 50%; background: rgba(255,255,255,0.55); flex-shrink: 0; }
        .left-foot { font-size: 12px; color: rgba(255,255,255,0.38); position: relative; z-index: 1; }

        /* Right form panel */
        .right {
            flex: 1;
            background: var(--white);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 48px 40px;
        }
        .form-wrap { width: 100%; max-width: 370px; animation: fadeUp 0.45s ease both; }
        .form-head { margin-bottom: 30px; }
        .form-head h1 { font-size: 25px; font-weight: 800; color: var(--ink); margin-bottom: 5px; }
        .form-head p  { font-size: 14px; color: var(--dim); font-weight: 300; }

        /* Toast */
        .toast { display: flex; align-items: center; gap: 9px; background: #fff5f6; border: 1px solid #fcc; border-left: 3px solid var(--rose); color: #b91c1c; padding: 11px 14px; border-radius: var(--r-sm); font-size: 13px; font-weight: 500; margin-bottom: 20px; animation: shake 0.4s ease; }

        /* Form */
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 12px; font-weight: 600; color: var(--ink-mid); margin-bottom: 6px; }
        .input-wrap { position: relative; }
        .input-icon { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); font-size: 14px; color: var(--muted); pointer-events: none; }
        .form-group input { width: 100%; padding: 12px 13px 12px 38px; background: var(--bg); border: 1.5px solid var(--border); border-radius: var(--r-sm); font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px; color: var(--ink); outline: none; transition: all 0.2s; }
        .form-group input:focus { border-color: var(--teal); background: #fff; box-shadow: 0 0 0 3px rgba(15,169,122,0.12); }
        .form-group input::placeholder { color: var(--muted); }
        .toggle-pw { position: absolute; right: 11px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 14px; color: var(--muted); transition: color 0.2s; }
        .toggle-pw:hover { color: var(--dim); }

        .btn-submit { width: 100%; padding: 13px; background: var(--teal); color: #fff; border: none; border-radius: var(--r-sm); font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; font-weight: 700; cursor: pointer; transition: all 0.2s; margin-top: 6px; box-shadow: 0 4px 18px rgba(15,169,122,0.28); }
        .btn-submit:hover { background: var(--teal-d); transform: translateY(-1px); box-shadow: 0 8px 24px rgba(15,169,122,0.38); }
        .btn-submit:active { transform: translateY(0); }

        .foot-link { text-align: center; margin-top: 22px; font-size: 13px; color: var(--dim); }
        .foot-link a { color: var(--teal); font-weight: 600; text-decoration: none; }
        .foot-link a:hover { text-decoration: underline; }

        @media(max-width:768px){ .left { display: none; } body { background: #fff; } }
        @keyframes fadeUp  { from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);} }
        @keyframes shake { 0%,100%{transform:translateX(0);} 20%{transform:translateX(-5px);} 40%{transform:translateX(5px);} 60%{transform:translateX(-3px);} 80%{transform:translateX(3px);} }
    </style>
</head>
<body>
    <div class="left">
        <div class="brand"><div class="brand-icon">📋</div><span class="brand-name">QR Attendance</span></div>
        <div class="left-mid">
            <span class="student-badge">Student Portal</span>
            <h2>Track your attendance with ease</h2>
            <p>Scan QR codes, view your records, and stay on top of your academic attendance — all in one place.</p>
            <div class="features">
                <div class="feat"><div class="feat-dot"></div>Instant QR scan check-in</div>
                <div class="feat"><div class="feat-dot"></div>Real-time attendance records</div>
                <div class="feat"><div class="feat-dot"></div>Subject-wise breakdown</div>
            </div>
        </div>
        <div class="left-foot">© QR Attendance System</div>
    </div>

    <div class="right">
        <div class="form-wrap">
            <div class="form-head">
                <h1>Welcome back 👋</h1>
                <p>Sign in to your student account</p>
            </div>

            <?php if ($message): ?>
            <div class="toast">⚠️ &nbsp;<?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Register Number</label>
                    <div class="input-wrap">
                        <span class="input-icon">🪪</span>
                        <input type="text" name="register_no" value="<?php echo isset($_POST['register_no'])?htmlspecialchars($_POST['register_no']):''; ?>" placeholder="e.g. 2023CS001" required>
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
                <button type="submit" name="login" class="btn-submit">Sign In →</button>
            </form>
            <div class="foot-link">New here? <a href="student_signup.php">Create an account</a></div>
        </div>
    </div>
<script>
function togglePw(){const f=document.getElementById('pw'),b=document.getElementById('tog'),h=f.type==='password';f.type=h?'text':'password';b.textContent=h?'🙈':'👁';}
</script>
</body>
</html>