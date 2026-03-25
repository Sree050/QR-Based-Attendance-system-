<!DOCTYPE html>
<html>
<head>
    <title>Teacher Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: url('bg.jpg') no-repeat center center / cover;
            position: relative;
        }

        /* Gradient overlay — richer than plain black */
        body::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15,23,60,0.82) 0%, rgba(30,10,60,0.78) 100%);
        }

        /* Floating orbs for depth */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.25;
            pointer-events: none;
        }
        .orb-1 { width: 380px; height: 380px; background: #3b82f6; top: -100px; left: -120px; }
        .orb-2 { width: 300px; height: 300px; background: #8b5cf6; bottom: -80px; right: -80px; }

        /* Card */
        .login-card {
            position: relative;
            z-index: 2;
            width: 420px;
            padding: 48px 44px;
            border-radius: 28px;
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.15);
            box-shadow: 0 24px 64px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.12);
            color: white;
            animation: slideUp 0.55s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(36px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0)    scale(1);    }
        }

        /* Avatar badge */
        .avatar-badge {
            width: 72px;
            height: 72px;
            border-radius: 22px;
            background: linear-gradient(135deg, #3b82f6, #6d28d9);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin: 0 auto 20px;
            box-shadow: 0 8px 24px rgba(59,130,246,0.45);
        }

        .login-card h4 {
            font-size: 1.55rem;
            font-weight: 700;
            letter-spacing: -0.3px;
            margin-bottom: 4px;
        }

        .login-card .subtitle {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.55);
            margin-bottom: 36px;
        }

        /* Input group */
        .input-group-custom {
            position: relative;
            margin-bottom: 16px;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1rem;
            opacity: 0.55;
            pointer-events: none;
            z-index: 2;
        }

        .form-control {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 12px;
            color: white;
            padding: 14px 16px 14px 44px;
            font-size: 0.92rem;
            transition: all 0.2s ease;
            width: 100%;
        }

        .form-control::placeholder { color: rgba(255,255,255,0.4); }

        .form-control:focus {
            outline: none;
            background: rgba(255,255,255,0.15);
            border-color: rgba(99,102,241,0.8);
            color: white;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.2);
        }

        /* Password toggle */
        .toggle-pw {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            opacity: 0.45;
            font-size: 1rem;
            transition: opacity 0.2s;
            z-index: 2;
            background: none;
            border: none;
            color: white;
            padding: 0;
        }
        .toggle-pw:hover { opacity: 0.85; }

        /* Remember me */
        .extras {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            margin-top: 4px;
        }

        .form-check-label {
            color: rgba(255,255,255,0.6);
            font-size: 0.82rem;
            cursor: pointer;
        }

        .form-check-input {
            background-color: rgba(255,255,255,0.15);
            border-color: rgba(255,255,255,0.25);
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: #6366f1;
            border-color: #6366f1;
        }

        .forgot-link {
            font-size: 0.82rem;
            color: rgba(255,255,255,0.55);
            text-decoration: none;
            transition: color 0.2s;
        }
        .forgot-link:hover { color: #a5b4fc; }

        /* Login button */
        .btn-login {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, #6366f1, #3b82f6);
            color: white;
            font-size: 0.97rem;
            font-weight: 600;
            letter-spacing: 0.2px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: transform 0.18s, box-shadow 0.18s;
            box-shadow: 0 8px 24px rgba(99,102,241,0.4);
        }

        .btn-login::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0);
            transition: background 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(99,102,241,0.5);
        }

        .btn-login:hover::after { background: rgba(255,255,255,0.07); }
        .btn-login:active { transform: translateY(0); }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
        }
        .divider hr {
            flex: 1;
            border-color: rgba(255,255,255,0.12);
            margin: 0;
        }
        .divider span {
            color: rgba(255,255,255,0.35);
            font-size: 0.75rem;
            white-space: nowrap;
        }

        /* Footer */
        .footer-text {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.5);
        }
        .footer-text a {
            color: #a5b4fc;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        .footer-text a:hover { color: #c7d2fe; }

        /* Back link */
        .back-link {
            position: absolute;
            top: -48px;
            left: 0;
            font-size: 0.82rem;
            color: rgba(255,255,255,0.5);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
        }
        .back-link:hover { color: rgba(255,255,255,0.9); }

        @media (max-width: 480px) {
            .login-card { width: 92%; padding: 36px 28px; }
        }
    </style>
</head>

<body>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <div class="login-card text-center" style="position:relative;">

        <a href="index.php" class="back-link">← Back to roles</a>

        <div class="avatar-badge">👨‍🏫</div>
        <h4>Teacher Login</h4>
        <p class="subtitle">Sign in to manage your classes</p>

        <form action="teacher_login_process.php" method="POST">

            <div class="input-group-custom">
                <span class="input-icon">✉️</span>
                <input type="email" name="email" class="form-control"
                       placeholder="Email address" required>
            </div>

            <div class="input-group-custom">
                <span class="input-icon">🔒</span>
                <input type="password" name="password" id="passwordField" class="form-control"
                       placeholder="Password" required>
                <button type="button" class="toggle-pw" onclick="togglePassword()" id="toggleBtn">👁️</button>
            </div>

            <div class="extras">
                <div class="form-check d-flex align-items-center gap-2 mb-0">
                    <input class="form-check-input mt-0" type="checkbox" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                </div>
                <a href="forgot_password.php" class="forgot-link">Forgot password?</a>
            </div>

            <button type="submit" class="btn-login">Sign In →</button>

        </form>

        <div class="divider"><hr><span>NEW HERE?</span><hr></div>

        <p class="footer-text">
            Don't have an account? <a href="teacher_signup.php">Create one</a>
        </p>

    </div>

    <script>
        function togglePassword() {
            const field = document.getElementById('passwordField');
            const btn = document.getElementById('toggleBtn');
            if (field.type === 'password') {
                field.type = 'text';
                btn.textContent = '🙈';
            } else {
                field.type = 'password';
                btn.textContent = '👁️';
            }
        }
    </script>
</body>
</html>