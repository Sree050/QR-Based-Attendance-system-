<!DOCTYPE html>
<html>
<head>
    <title>Teacher Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            height: 100vh;
            background: url('bg.jpg') no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
            position: relative;
        }

        /* Dark overlay */
        body::before {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
        }

        /* Glass Card */
        .login-card {
            position: relative;
            z-index: 1;
            width: 400px;
            padding: 40px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            color: white;
            animation: fadeIn 0.6s ease-in-out;
        }

        .login-card h4 {
            font-weight: 600;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
        }

        .form-control::placeholder {
            color: #ddd;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            box-shadow: none;
        }

        .btn-login {
            background: linear-gradient(to right, #00c6ff, #0072ff);
            border: none;
        }

        .btn-login:hover {
            opacity: 0.9;
        }

        a {
            color: #ddd;
            text-decoration: none;
        }

        a:hover {
            color: white;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media(max-width: 480px) {
            .login-card {
                width: 90%;
                padding: 30px;
            }
        }
    </style>
</head>

<body>

<div class="login-card text-center">

    <h4 class="mb-4">Teacher Login</h4>

    <form action="teacher_login_process.php" method="POST">

        <input type="email" name="email" class="form-control mb-3"
               placeholder="Enter Email" required>

        <input type="password" name="password" class="form-control mb-3"
               placeholder="Enter Password" required>

        <button class="btn btn-login w-100 mb-3 text-white">
            Login
        </button>

    </form>

    <div>
        <a href="teacher_signup.php">No account? Sign Up</a>
    </div>

</div>

</body>
</html>
