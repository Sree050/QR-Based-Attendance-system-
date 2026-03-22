<?php
session_start();
include("db.php");

$message = "";

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM admin WHERE username='$username'";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {

        if (password_verify($password, $row['password'])) {

            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_username'] = $row['username'];

            header("Location: admin_dashboard.php");
            exit();
        } else {
            $message = "Invalid Password";
        }
    } else {
        $message = "Admin Not Found";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>
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
.card {
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: 40px;
    width: 350px;
    color: white;
}
</style>
</head>

<body>

<div class="card shadow">

<h4 class="text-center mb-4">Admin Login</h4>

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

    <button name="login" class="btn btn-light w-100">
        Login
    </button>
    <div class="text-center mt-3">
<a href="admin_signup.php" class="text-white">
Create Admin Account
</a>
</div>
</form>

</div>

</body>
</html>