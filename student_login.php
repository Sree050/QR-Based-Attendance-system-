<?php
session_start();
include("db.php");

$message = "";

if (isset($_POST['login'])) {

    $register_no = $_POST['register_no'];
    $password = $_POST['password'];

    $query = "SELECT * FROM students WHERE register_no='$register_no'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {

        $student = mysqli_fetch_assoc($result);

        if (password_verify($password, $student['password'])) {
            $_SESSION['student_id'] = $student['id'];
            $_SESSION['student_name'] = $student['full_name'];
            $_SESSION['student_regno'] = $student['register_no'];
            header("Location: student_dashboard.php");
            exit();

        } else {
            $message = "Invalid Password!";
        }

    } else {
        $message = "Register Number not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    height: 100vh;
    background: linear-gradient(135deg, #1e3c72, #2a5298);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Segoe UI', sans-serif;
}

.login-card {
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: 40px;
    width: 380px;
    color: white;
    box-shadow: 0 8px 32px rgba(0,0,0,0.2);
}

.login-card h4 {
    text-align: center;
    margin-bottom: 25px;
}

.form-control {
    border-radius: 8px;
}

.btn-primary {
    background: #ffffff;
    color: #1e3c72;
    border: none;
}

.btn-primary:hover {
    background: #e6e6e6;
}
</style>
</head>

<body>

<div class="login-card">

    <h4>Student Login</h4>

    <?php if($message) echo "<div class='alert alert-danger'>$message</div>"; ?>

    <form method="POST">

        <div class="mb-3">
            <label>Register Number</label>
            <input type="text" name="register_no" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button name="login" class="btn btn-primary w-100">Login</button>

    </form>

    <p class="text-center mt-3">
        No account? <a href="student_signup.php" class="text-white">Sign Up</a>
    </p>

</div>

</body>
</html>