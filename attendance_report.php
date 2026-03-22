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

$report_generated = false;
$total_students = 0;
$present = 0;
$absent = 0;
$percentage = 0;
$absentees = [];
$subject_name = "";
$subject_code = "";
$selected_date = "";
$session_found = false;

/* Fetch teacher subjects */
$subjects_query = "
SELECT s.id, s.subject_name, s.subject_code
FROM subjects s
JOIN teacher_subjects ts
ON s.id = ts.subject_id
WHERE ts.teacher_id = '$teacher_id'
";
$subjects_result = mysqli_query($conn, $subjects_query);

if (isset($_POST['view_report'])) {

    $subject_id = $_POST['subject_id'];
    $selected_date = $_POST['date'];

    /* Get subject details */
    $sub_query = "
    SELECT subject_name, subject_code, semester
    FROM subjects
    WHERE id = '$subject_id'
    ";
    $sub_result = mysqli_query($conn, $sub_query);
    $subject = mysqli_fetch_assoc($sub_result);

    $subject_name = $subject['subject_name'];
    $subject_code = $subject['subject_code'];
    $semester = $subject['semester'];

    /* 🔥 FIXED SESSION FETCH (LATEST SESSION OF THAT DATE) */
    $session_query = "
    SELECT id
    FROM session_qr
    WHERE teacher_id = '$teacher_id'
    AND subject_id = '$subject_id'
    AND DATE(created_at) = '$selected_date'
    ORDER BY id DESC
    LIMIT 1
    ";

    $session_result = mysqli_query($conn, $session_query);

    if ($session = mysqli_fetch_assoc($session_result)) {

        $session_found = true;
        $session_id = $session['id'];

        /* Present Count */
        $present_query = "
        SELECT COUNT(*) as total
        FROM attendance
        WHERE session_id = '$session_id'
        ";
        $present_result = mysqli_query($conn, $present_query);
        $present = mysqli_fetch_assoc($present_result)['total'];

        /* Total Students (Based on Year = Semester) */
        $total_query = "
        SELECT COUNT(*) as total
        FROM students
        WHERE year = '$semester'
        ";
        $total_result = mysqli_query($conn, $total_query);
        $total_students = mysqli_fetch_assoc($total_result)['total'];

        $absent = $total_students - $present;

        if ($total_students > 0) {
            $percentage = round(($present / $total_students) * 100, 2);
        }

        /* Absentees List */
        $abs_query = "
        SELECT full_name, register_no
        FROM students
        WHERE year = '$semester'
        AND id NOT IN (
            SELECT student_id
            FROM attendance
            WHERE session_id = '$session_id'
        )
        ";

        $abs_result = mysqli_query($conn, $abs_query);

        while ($row = mysqli_fetch_assoc($abs_result)) {
            $absentees[] = $row;
        }

        $report_generated = true;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light p-5">

<div class="container">

    <div class="card p-4 shadow">
        <h4>Attendance Report</h4>
        <hr>

        <form method="POST" class="row g-3">

            <div class="col-md-4">
                <label>Select Date</label>
                <input type="date" name="date" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label>Select Subject</label>
                <select name="subject_id" class="form-control" required>
                    <option value="">Choose Subject</option>
                    <?php while($row = mysqli_fetch_assoc($subjects_result)): ?>
                        <option value="<?php echo $row['id']; ?>">
                            <?php echo $row['subject_name']; ?>
                            (<?php echo $row['subject_code']; ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-4 align-self-end">
                <button name="view_report" class="btn btn-primary w-100">
                    View Report
                </button>
            </div>

        </form>
    </div>

<?php if ($report_generated): ?>

    <div class="card mt-4 p-4 shadow">

        <h5>
            <?php echo $subject_name; ?>
            (<?php echo $subject_code; ?>)
        </h5>

        <p>
            Date: <?php echo $selected_date; ?><br>
            Day: <?php echo date('l', strtotime($selected_date)); ?>
        </p>

        <hr>

        <div class="row text-center">
            <div class="col-md-3"><strong>Total Students</strong><br><?php echo $total_students; ?></div>
            <div class="col-md-3 text-success"><strong>Present</strong><br><?php echo $present; ?></div>
            <div class="col-md-3 text-danger"><strong>Absent</strong><br><?php echo $absent; ?></div>
            <div class="col-md-3"><strong>Attendance %</strong><br><?php echo $percentage; ?>%</div>
        </div>

        <hr>

        <h6>Absentees:</h6>

        <?php if (!empty($absentees)): ?>
            <ul>
                <?php foreach($absentees as $student): ?>
                    <li>
                        <?php echo $student['full_name']; ?>
                        (<?php echo $student['register_no']; ?>)
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-success">No absentees 🎉</p>
        <?php endif; ?>

    </div>

<?php elseif(isset($_POST['view_report']) && !$session_found): ?>

    <div class="alert alert-danger mt-4">
        No attendance session found for selected date.
    </div>

<?php endif; ?>

</div>

</body>
</html>