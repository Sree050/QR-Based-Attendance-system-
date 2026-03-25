<!DOCTYPE html>
<html>
<head>
    <title>Teacher Signup</title>
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
            padding: 24px 0;
        }

        body::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15,23,60,0.82) 0%, rgba(30,10,60,0.78) 100%);
        }

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
        .signup-card {
            position: relative;
            z-index: 2;
            width: 440px;
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
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

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

        .signup-card h4 {
            font-size: 1.55rem;
            font-weight: 700;
            letter-spacing: -0.3px;
            margin-bottom: 4px;
        }

        .signup-card .subtitle {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.55);
            margin-bottom: 32px;
        }

        /* Steps indicator */
        .steps {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-bottom: 28px;
        }
        .step-dot {
            width: 28px;
            height: 5px;
            border-radius: 99px;
            background: rgba(255,255,255,0.15);
            transition: background 0.3s;
        }
        .step-dot.active { background: #6366f1; }
        .step-dot.done   { background: rgba(99,102,241,0.45); }

        /* Input group */
        .input-group-custom {
            position: relative;
            margin-bottom: 14px;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1rem;
            opacity: 0.5;
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
            width: 100%;
            transition: all 0.2s ease;
        }

        .form-control::placeholder { color: rgba(255,255,255,0.38); }

        .form-control:focus {
            outline: none;
            background: rgba(255,255,255,0.15);
            border-color: rgba(99,102,241,0.8);
            color: white;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.2);
        }

        .form-control.is-valid-field {
            border-color: rgba(34,197,94,0.6);
        }

        /* Password wrapper */
        .pw-wrap { position: relative; }
        .toggle-pw {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            opacity: 0.45;
            font-size: 1rem;
            background: none;
            border: none;
            color: white;
            padding: 0;
            transition: opacity 0.2s;
            z-index: 2;
        }
        .toggle-pw:hover { opacity: 0.85; }

        /* Password strength */
        .strength-bar {
            display: flex;
            gap: 4px;
            margin-top: 8px;
            margin-bottom: 4px;
        }
        .strength-seg {
            flex: 1;
            height: 4px;
            border-radius: 99px;
            background: rgba(255,255,255,0.12);
            transition: background 0.3s;
        }
        .strength-label {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.4);
            text-align: right;
            margin-bottom: 12px;
            min-height: 16px;
        }

        /* Terms */
        .terms-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin: 16px 0 24px;
            text-align: left;
        }
        .form-check-input {
            background-color: rgba(255,255,255,0.15);
            border-color: rgba(255,255,255,0.25);
            cursor: pointer;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .form-check-input:checked {
            background-color: #6366f1;
            border-color: #6366f1;
        }
        .terms-text {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.5);
            line-height: 1.5;
        }
        .terms-text a {
            color: #a5b4fc;
            text-decoration: none;
        }
        .terms-text a:hover { color: #c7d2fe; }

        /* Button */
        .btn-signup {
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
            transition: transform 0.18s, box-shadow 0.18s;
            box-shadow: 0 8px 24px rgba(99,102,241,0.4);
            position: relative;
            overflow: hidden;
        }
        .btn-signup::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0);
            transition: background 0.2s;
        }
        .btn-signup:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(99,102,241,0.5);
        }
        .btn-signup:hover::after { background: rgba(255,255,255,0.07); }
        .btn-signup:active { transform: translateY(0); }

        /* Perks */
        .perks {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0 0;
        }
        .perk {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.75rem;
            color: rgba(255,255,255,0.4);
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
        }
        .divider hr { flex: 1; border-color: rgba(255,255,255,0.12); margin: 0; }
        .divider span { color: rgba(255,255,255,0.3); font-size: 0.75rem; white-space: nowrap; }

        .footer-text {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.5);
        }
        .footer-text a {
            color: #a5b4fc;
            text-decoration: none;
            font-weight: 500;
        }
        .footer-text a:hover { color: #c7d2fe; }

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
            .signup-card { width: 92%; padding: 36px 24px; }
            .perks { flex-direction: column; align-items: center; gap: 8px; }
        }
    </style>
</head>

<body>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <div class="signup-card text-center" style="position:relative;">

        <a href="teacher_login.php" class="back-link">← Back to login</a>

        <div class="avatar-badge">👨‍🏫</div>
        <h4>Create Account</h4>
        <p class="subtitle">Join as a teacher — it's free</p>

        <!-- Step indicator -->
        <div class="steps">
            <div class="step-dot active" id="dot1"></div>
            <div class="step-dot" id="dot2"></div>
            <div class="step-dot" id="dot3"></div>
        </div>

        <form action="teacher_signup_process.php" method="POST" id="signupForm">

            <div class="input-group-custom">
                <span class="input-icon">👤</span>
                <input type="text" name="name" id="nameField" class="form-control"
                       placeholder="Full Name" required autocomplete="name"
                       oninput="onFieldInput(this, 'dot1')">
            </div>

            <div class="input-group-custom">
                <span class="input-icon">✉️</span>
                <input type="email" name="email" id="emailField" class="form-control"
                       placeholder="Email Address" required autocomplete="email"
                       oninput="onFieldInput(this, 'dot2')">
            </div>

            <div class="input-group-custom pw-wrap">
                <span class="input-icon">🔒</span>
                <input type="password" name="password" id="passwordField" class="form-control"
                       placeholder="Create Password" required
                       oninput="checkStrength(this.value)">
                <button type="button" class="toggle-pw" onclick="togglePassword()" id="toggleBtn">👁️</button>
            </div>

            <!-- Strength bar -->
            <div class="strength-bar">
                <div class="strength-seg" id="s1"></div>
                <div class="strength-seg" id="s2"></div>
                <div class="strength-seg" id="s3"></div>
                <div class="strength-seg" id="s4"></div>
            </div>
            <div class="strength-label" id="strengthLabel"></div>

            <!-- Terms -->
            <div class="terms-row">
                <input class="form-check-input" type="checkbox" id="terms" required>
                <label class="terms-text" for="terms">
                    I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                </label>
            </div>

            <button type="submit" class="btn-signup">Create My Account →</button>

        </form>

        <!-- Perks -->
        <div class="perks">
            <div class="perk">✅ Free forever</div>
            <div class="perk">🔒 Secure</div>
            <div class="perk">⚡ Instant access</div>
        </div>

        <div class="divider"><hr><span>ALREADY REGISTERED?</span><hr></div>

        <p class="footer-text">
            Have an account? <a href="teacher_login.php">Sign in instead</a>
        </p>

    </div>

    <script>
        function togglePassword() {
            const field = document.getElementById('passwordField');
            const btn   = document.getElementById('toggleBtn');
            field.type  = field.type === 'password' ? 'text' : 'password';
            btn.textContent = field.type === 'password' ? '👁️' : '🙈';
        }

        function checkStrength(val) {
            const segs   = [s1, s2, s3, s4];
            const label  = document.getElementById('strengthLabel');
            const colors = ['#ef4444','#f97316','#eab308','#22c55e'];
            const labels = ['Weak','Fair','Good','Strong'];

            let score = 0;
            if (val.length >= 8)           score++;
            if (/[A-Z]/.test(val))         score++;
            if (/[0-9]/.test(val))         score++;
            if (/[^A-Za-z0-9]/.test(val))  score++;

            segs.forEach((s, i) => {
                s.style.background = i < score ? colors[score - 1] : 'rgba(255,255,255,0.12)';
            });

            label.textContent = val.length ? labels[score - 1] || '' : '';
            label.style.color = val.length ? colors[score - 1] : 'rgba(255,255,255,0.4)';

            if (score === 3) activateDot('dot3');
        }

        function onFieldInput(el, dotId) {
            if (el.value.length > 0) activateDot(dotId);
        }

        function activateDot(id) {
            const dots = ['dot1','dot2','dot3'];
            const idx  = dots.indexOf(id);
            dots.forEach((d, i) => {
                const el = document.getElementById(d);
                if (i < idx)  { el.className = 'step-dot done'; }
                if (i === idx){ el.className = 'step-dot active'; }
                if (i > idx)  { el.className = 'step-dot'; }
            });
        }
    </script>
</body>
</html>