<?php
session_start();
include("db.php");
if (!isset($_SESSION['admin_id'])) { header("Location: admin_login.php"); exit(); }

if (isset($_POST['add_subject'])) {
    mysqli_query($conn,"INSERT INTO subjects (subject_name,subject_code,semester,type)
    VALUES ('$_POST[subject_name]','$_POST[subject_code]','$_POST[semester]','$_POST[type]')");
}

if (isset($_GET['delete'])) {
    mysqli_query($conn,"DELETE FROM subjects WHERE id='$_GET[delete]'");
    header("Location: manage_subjects.php");
    exit();
}

$result=mysqli_query($conn,"SELECT * FROM subjects ORDER BY semester");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Subjects</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include("admin_layout.php"); ?>

<div class="content">
<h2>Add Subject</h2>

<form method="POST" class="row g-3 mb-4">
<div class="col-md-3"><input type="text" name="subject_name" class="form-control" placeholder="Name" required></div>
<div class="col-md-2"><input type="text" name="subject_code" class="form-control" placeholder="Code" required></div>
<div class="col-md-2">
<select name="semester" class="form-control">
<?php for($i=1;$i<=8;$i++) echo "<option>$i</option>"; ?>
</select>
</div>
<div class="col-md-2">
<select name="type" class="form-control">
<option>Theory</option>
<option>Lab</option>
</select>
</div>
<div class="col-md-2">
<button name="add_subject" class="btn btn-success">Add</button>
</div>
</form>

<table class="table table-bordered table-striped">
<thead class="table-dark">
<tr><th>Name</th><th>Code</th><th>Semester</th><th>Type</th><th>Action</th></tr>
</thead>
<tbody>
<?php while($row=mysqli_fetch_assoc($result)){ ?>
<tr>
<td><?php echo $row['subject_name']; ?></td>
<td><?php echo $row['subject_code']; ?></td>
<td><?php echo $row['semester']; ?></td>
<td><?php echo $row['type']; ?></td>
<td>
<a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
</td>
</tr>
<?php } ?>
</tbody>
</table>
</div>
</body>
</html>