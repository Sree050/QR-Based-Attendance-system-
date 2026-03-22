<?php
session_start();
include("db.php");

$message = "";

if (isset($_POST['signup'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $message = "Passwords do not match!";
    } else {

        $check = mysqli_query($conn,
            "SELECT * FROM admin WHERE username='$username'");

        if (mysqli_num_rows($check) > 0) {
            $message = "Username already exists!";
        } else {

            $hashed = password_hash($password, PASSWORD_DEFAULT);

            mysqli_query($conn,
            "INSERT INTO admin (username, password)
             VALUES ('$username', '$hashed')");

            echo "<script>
                    alert('Admin Account Created Successfully!');
                    window.location='admin_login.php';
                  </script>";
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Signup</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    height: 100vh;
    background: linear-gradient(135deg,#1e3c72,#2a5298);
    display:flex;
    align-items:center;
    justify-content:center;
    font-family:'Segoe UI';
}
.card {
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: 40px;
    width: 380px;
    color:white;
}
</style>
</head>

<body>

<div class="card shadow">

<h4 class="text-center mb-4">Admin Signup</h4>

<?php if($message): ?>
<div class="alert alert-danger"><?php echo $message; ?></div>
<?php endif; ?>

<form method="POST">

<div class="mb-3">
<label>Username</label>
<input type="text" name="username" class="form-control" required>
</div>

<div class="mb-3">
<label>Password</label>
<input type="password" name="password" class="form-control" required>
</div>

<div class="mb-3">
<label>Confirm Password</label>
<input type="password" name="confirm_password" class="form-control" required>
</div>

<button name="signup" class="btn btn-light w-100">
Create Admin Account
</button>

<div class="text-center mt-3">
<a href="admin_login.php" class="text-white">
Already have account? Login
</a>
</div>

</form>

</div>

</body>
</html>