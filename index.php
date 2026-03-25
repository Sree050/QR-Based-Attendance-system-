<!DOCTYPE html>
<html>

<head>
    <title>Select Role</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            border: none;
            border-radius: 24px;
            padding: 48px 40px;
            width: 420px;
            background: rgba(255, 255, 255, 0.97);
            box-shadow: 0 32px 64px rgba(0,0,0,0.18), 0 0 0 1px rgba(255,255,255,0.2);
        }

        .brand-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 28px;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
        }

        .card h4 {
            font-weight: 700;
            font-size: 1.5rem;
            color: #1a1a2e;
            margin-bottom: 6px;
        }

        .card .subtitle {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 32px;
        }

        .role-btn {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 16px 20px;
            border-radius: 14px;
            border: 2px solid transparent;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 12px;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }

        .role-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .role-btn:hover::before {
            opacity: 1;
        }

        .role-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }

        .role-btn:active {
            transform: translateY(0px);
        }

        .role-btn .icon-wrap {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-right: 16px;
            flex-shrink: 0;
        }

        .role-btn .text-wrap {
            text-align: left;
        }

        .role-btn .text-wrap .role-label {
            display: block;
            font-size: 1rem;
            font-weight: 700;
        }

        .role-btn .text-wrap .role-desc {
            display: block;
            font-size: 0.78rem;
            opacity: 0.75;
            font-weight: 400;
            margin-top: 1px;
        }

        .role-btn .arrow {
            margin-left: auto;
            opacity: 0.4;
            font-size: 1.1rem;
            transition: transform 0.25s, opacity 0.25s;
        }

        .role-btn:hover .arrow {
            transform: translateX(4px);
            opacity: 0.9;
        }

        /* Teacher */
        .btn-teacher {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #dbeafe;
        }
        .btn-teacher .icon-wrap { background: #dbeafe; }
        .btn-teacher:hover {
            background: #1d4ed8;
            color: #fff;
            border-color: #1d4ed8;
        }
        .btn-teacher:hover .icon-wrap { background: rgba(255,255,255,0.2); }

        /* Student */
        .btn-student {
            background: #f0fdf4;
            color: #15803d;
            border-color: #dcfce7;
        }
        .btn-student .icon-wrap { background: #dcfce7; }
        .btn-student:hover {
            background: #15803d;
            color: #fff;
            border-color: #15803d;
        }
        .btn-student:hover .icon-wrap { background: rgba(255,255,255,0.2); }

        /* Admin */
        .btn-admin {
            background: #fff7ed;
            color: #c2410c;
            border-color: #fed7aa;
        }
        .btn-admin .icon-wrap { background: #fed7aa; }
        .btn-admin:hover {
            background: #c2410c;
            color: #fff;
            border-color: #c2410c;
        }
        .btn-admin:hover .icon-wrap { background: rgba(255,255,255,0.2); }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }
        .divider hr {
            flex: 1;
            border-color: #e5e7eb;
            margin: 0;
        }
        .divider span {
            color: #9ca3af;
            font-size: 0.78rem;
            white-space: nowrap;
        }
    </style>
</head>

<body>

    <div class="card text-center">
        <div class="brand-icon">🎓</div>
        <h4>Welcome Back</h4>
        <p class="subtitle">Choose your role to continue</p>

        <div class="divider">
            <hr><span>SELECT A ROLE</span><hr>
        </div>

        <a href="teacher_login.php" class="role-btn btn-teacher">
            <div class="icon-wrap">👨‍🏫</div>
            <div class="text-wrap">
                <span class="role-label">Teacher</span>
                <span class="role-desc">Manage classes & assignments</span>
            </div>
            <span class="arrow">›</span>
        </a>

        <a href="student_login.php" class="role-btn btn-student">
            <div class="icon-wrap">👨‍🎓</div>
            <div class="text-wrap">
                <span class="role-label">Student</span>
                <span class="role-desc">View courses & submissions</span>
            </div>
            <span class="arrow">›</span>
        </a>

        <a href="admin_login.php" class="role-btn btn-admin" style="margin-bottom:0;">
            <div class="icon-wrap">👨‍💼</div>
            <div class="text-wrap">
                <span class="role-label">Admin</span>
                <span class="role-desc">System settings & users</span>
            </div>
            <span class="arrow">›</span>
        </a>
    </div>

</body>

</html>