<?php
include("db.php");

$message = "";

if (isset($_POST['signup'])) {

    $full_name = $_POST['full_name'];
    $register_no = $_POST['register_no'];
    $department = $_POST['department'];
    $branch = $_POST['branch'];
    $year = $_POST['year'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $message = "Passwords do not match!";
    } else {

        $check = "SELECT id FROM students WHERE register_no='$register_no'";
        $result = mysqli_query($conn, $check);

        if (mysqli_num_rows($result) > 0) {
            $message = "Register Number already exists!";
        } else {

            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $insert = "INSERT INTO students 
            (full_name, register_no, department, branch, year, password)
            VALUES
            ('$full_name','$register_no','$department','$branch','$year','$hashed')";

            if (mysqli_query($conn, $insert)) {
                $message = "success";
            } else {
                $message = "Something went wrong. Try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Signup</title>
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

.signup-card {
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(12px);
    border-radius: 18px;
    padding: 40px;
    width: 420px;
    color: white;
    box-shadow: 0 8px 32px rgba(0,0,0,0.25);
}

.signup-card h4 {
    text-align: center;
    margin-bottom: 25px;
}

.form-control, select {
    border-radius: 10px;
}

.btn-success {
    background: white;
    color: #1e3c72;
    border: none;
    font-weight: 500;
}

.btn-success:hover {
    background: #e6e6e6;
}
</style>
</head>

<body>

<div class="signup-card">

    <h4>Student Signup</h4>

    <?php if($message == "success"): ?>

        <div class="alert alert-success text-center">
            Account created successfully! Redirecting to login...
        </div>

        <script>
            setTimeout(function() {
                window.location.href = "student_login.php";
            }, 2000);
        </script>

    <?php elseif($message): ?>

        <div class="alert alert-danger">
            <?php echo $message; ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <div class="mb-2">
            <label>Full Name</label>
            <input type="text" name="full_name" class="form-control" required>
        </div>

        <div class="mb-2">
            <label>Register Number</label>
            <input type="text" name="register_no" class="form-control" required>
        </div>

        <div class="mb-2">
            <label>Department</label>
            <input type="text" name="department" class="form-control" required>
        </div>

        <div class="mb-2">
            <label>Branch</label>
            <input type="text" name="branch" class="form-control" required>
        </div>

        <div class="mb-2">
            <label>Year</label>
            <select name="year" class="form-control" required>
                <option value="">Select Year</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>

        <div class="mb-2">
            <label>Create Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>

        <button name="signup" class="btn btn-success w-100">
            Create Account
        </button>

    </form>

    <p class="text-center mt-3">
        Already have account?
        <a href="student_login.php" class="text-white">Login</a>
    </p>

</div>

</body>
</html>