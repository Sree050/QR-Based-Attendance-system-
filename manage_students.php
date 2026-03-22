<?php
session_start();
include("db.php");
if (!isset($_SESSION['admin_id'])) { header("Location: admin_login.php"); exit(); }

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn,"DELETE FROM students WHERE id='$id'");
    header("Location: manage_students.php");
    exit();
}

$result = mysqli_query($conn,"SELECT * FROM students ORDER BY year, full_name");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Students</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include("admin_layout.php"); ?>

<div class="content">
<h2 class="mb-4">Manage Students</h2>

<table class="table table-bordered table-striped">
<thead class="table-dark">
<tr><th>ID</th><th>Name</th><th>Register No</th><th>Year</th><th>Action</th></tr>
</thead>
<tbody>
<?php while($row=mysqli_fetch_assoc($result)){ ?>
<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo $row['full_name']; ?></td>
<td><?php echo $row['register_no']; ?></td>
<td><?php echo $row['year']; ?></td>
<td>
<a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm"
onclick="return confirm('Delete student?')">Delete</a>
</td>
</tr>
<?php } ?>
</tbody>
</table>
</div>
</body>
</html>