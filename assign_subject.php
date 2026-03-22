<?php
session_start();
include("db.php");
if (!isset($_SESSION['admin_id'])) { header("Location: admin_login.php"); exit(); }

if(isset($_POST['assign'])){
mysqli_query($conn,"INSERT INTO teacher_subjects (teacher_id,subject_id)
VALUES ('$_POST[teacher_id]','$_POST[subject_id]')");
}

$teachers=mysqli_query($conn,"SELECT * FROM teachers");
$subjects=mysqli_query($conn,"SELECT * FROM subjects");
$assigned=mysqli_query($conn,"
SELECT ts.id,t.name,s.subject_name
FROM teacher_subjects ts
JOIN teachers t ON ts.teacher_id=t.id
JOIN subjects s ON ts.subject_id=s.id");
?>

<!DOCTYPE html>
<html>
<head>
<title>Assign Subjects</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include("admin_layout.php"); ?>

<div class="content">
<h2>Assign Subjects</h2>

<form method="POST" class="row g-3 mb-4">
<div class="col-md-4">
<select name="teacher_id" class="form-control">
<?php while($t=mysqli_fetch_assoc($teachers)){ ?>
<option value="<?php echo $t['id']; ?>"><?php echo $t['name']; ?></option>
<?php } ?>
</select>
</div>

<div class="col-md-4">
<select name="subject_id" class="form-control">
<?php while($s=mysqli_fetch_assoc($subjects)){ ?>
<option value="<?php echo $s['id']; ?>"><?php echo $s['subject_name']; ?></option>
<?php } ?>
</select>
</div>

<div class="col-md-2">
<button name="assign" class="btn btn-success">Assign</button>
</div>
</form>

<table class="table table-bordered table-striped">
<thead class="table-dark">
<tr><th>Teacher</th><th>Subject</th></tr>
</thead>
<tbody>
<?php while($row=mysqli_fetch_assoc($assigned)){ ?>
<tr>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['subject_name']; ?></td>
</tr>
<?php } ?>
</tbody>
</table>
</div>
</body>
</html>