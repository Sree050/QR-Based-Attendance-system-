<?php
include("db.php");

$semester = $_GET['semester'];

$query = "SELECT * FROM subjects WHERE semester = '$semester'";
$result = mysqli_query($conn, $query);

echo "<div class='mb-3'>";
echo "<label>Semester $semester Subjects</label>";
echo "<select name='subject_ids[]' class='form-control' required>";
echo "<option value=''>Select Subject</option>";

while ($row = mysqli_fetch_assoc($result)) {

    echo "<option value='{$row['id']}'>
            {$row['subject_name']} 
            ({$row['subject_code']}) - {$row['type']}
          </option>";
}

echo "</select>";
echo "</div>";
?>