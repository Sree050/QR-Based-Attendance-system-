<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("db.php");
if (!isset($_SESSION['teacher_id'])) {
    header("Location: teacher_login.php");
    exit();
}
$teacher_id = $_SESSION['teacher_id'];
$message = "";
$query = "SELECT * FROM teachers WHERE id = '$teacher_id'";
$result = mysqli_query($conn, $query);
$teacher = mysqli_fetch_assoc($result);
if (isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $check_email = "SELECT id FROM teachers WHERE email = '$email' AND id != '$teacher_id'";
    $check_result = mysqli_query($conn, $check_email);
    if (mysqli_num_rows($check_result) > 0) {
        $message = "<div class='alert alert-danger'>Email already exists!</div>";
    } else {
        $update = "UPDATE teachers SET name='$name', email='$email' WHERE id='$teacher_id'";
        mysqli_query($conn, $update);
        $_SESSION['teacher_name'] = $name;
        $message = "<div class='alert alert-success'>Profile updated successfully!</div>";
    }
}
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    if (!password_verify($current_password, $teacher['password'])) {
        $message = "<div class='alert alert-danger'>Current password is incorrect!</div>";
    } elseif ($new_password !== $confirm_password) {
        $message = "<div class='alert alert-danger'>New passwords do not match!</div>";
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $update_pass = "UPDATE teachers SET password='$hashed' WHERE id='$teacher_id'";
        mysqli_query($conn, $update_pass);
        $message = "<div class='alert alert-success'>Password changed successfully!</div>";
    }
}
$subject_query = "
SELECT subjects.subject_name, subjects.subject_code, subjects.semester
FROM subjects
JOIN teacher_subjects
ON subjects.id = teacher_subjects.subject_id
WHERE teacher_subjects.teacher_id = '$teacher_id'
";
$subject_result = mysqli_query($conn, $subject_query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teacher Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light p-5">

<div class="container">

    <div class="card p-4 shadow">

        <h4>My Profile</h4>
        <hr>

        <?php echo $message; ?>

        <!-- Basic Info -->
        <h6>Basic Information</h6>
        <p><strong>Teacher ID:</strong> <?php echo $teacher['id']; ?></p>
        <p><strong>Name:</strong> <?php echo $teacher['name']; ?></p>
        <p><strong>Email:</strong> <?php echo $teacher['email']; ?></p>
        <p><strong>Account Created:</strong> <?php echo $teacher['created_at']; ?></p>

        <hr>

        <!-- Edit Profile -->
        <h6>Edit Profile</h6>
        <form method="POST">
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control"
                       value="<?php echo $teacher['name']; ?>" required>
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control"
                       value="<?php echo $teacher['email']; ?>" required>
            </div>

            <button name="update_profile" class="btn btn-primary">
                Update Profile
            </button>
        </form>

        <hr>

        <!-- Change Password -->
        <h6>Change Password</h6>
        <form method="POST">

            <div class="mb-3">
                <label>Current Password</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>

            <button name="change_password" class="btn btn-warning">
                Change Password
            </button>
        </form>

        <hr>
    </div>

</div>

</body>
</html>