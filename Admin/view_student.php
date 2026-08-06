<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("config/database.php");

/*=========================================
    CHECK ID
=========================================*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid Student ID.";

    header("Location: manage_students.php");
    exit();
}

$id = (int) $_GET['id'];

/*=========================================
    STUDENT DETAILS
=========================================*/

$query = mysqli_prepare(
    $conn,
    "SELECT * FROM students WHERE id=? LIMIT 1"
);

mysqli_stmt_bind_param($query, "i", $id);
mysqli_stmt_execute($query);

$result = mysqli_stmt_get_result($query);

if (mysqli_num_rows($result) == 0) {

    $_SESSION['error'] = "Student not found.";

    header("Location: manage_students.php");
    exit();
}

$student = mysqli_fetch_assoc($result);

/*=========================================
    TOTAL SUBMISSIONS
=========================================*/

$submissionCount = 0;

$check = mysqli_query(
    $conn,
    "SELECT COUNT(*) total
     FROM submissions
     WHERE student_id='$id'"
);

if ($check) {
    $submissionCount = mysqli_fetch_assoc($check)['total'];
}

/*=========================================
    TOTAL RESULTS
=========================================*/

$resultCount = 0;

$check2 = mysqli_query(
    $conn,
    "SELECT COUNT(*) total
     FROM results
     WHERE student_id='$id'"
);

if ($check2) {
    $resultCount = mysqli_fetch_assoc($check2)['total'];
}

$pageTitle = "Student Profile";
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1">

<title>

Student Profile

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

<link
rel="stylesheet"
href="assets/css/dashboard.css">

</head>

<body>

<div class="wrapper">

<?php include("includes/sidebar.php"); ?>

<div class="main-content">

<?php include("includes/topbar.php"); ?>

<div class="container-fluid">

<div class="row mb-4">

<div class="col-lg-12">

<h2 class="fw-bold">

<i class="fas fa-user-circle text-primary"></i>

Student Profile

</h2>

<p class="text-muted">

Complete student information.

</p>

</div>

</div>
<!-- ==========================================
        PROFILE SECTION
========================================== -->

<div class="row">

    <!-- Profile Card -->

    <div class="col-lg-4">

        <div class="card shadow-lg border-0 rounded-4">

            <div class="card-body text-center">

                <?php

                $photo = "assets/images/avatar.png";

                if (
                    isset($student['photo']) &&
                    !empty($student['photo']) &&
                    file_exists("../uploads/students/" . $student['photo'])
                ) {
                    $photo = "../uploads/students/" . $student['photo'];
                }

                ?>

                <img
                    src="<?php echo $photo; ?>"
                    class="rounded-circle shadow mb-3"
                    width="170"
                    height="170"
                    style="object-fit:cover;">

                <h3 class="fw-bold">

                    <?php echo htmlspecialchars($student['full_name']); ?>

                </h3>

                <span class="badge bg-success">

                    Active Student

                </span>

                <hr>

                <div class="text-start">

                    <p>

                        <i class="fas fa-id-card text-primary me-2"></i>

                        <strong>Roll Number:</strong>

                        <?php echo htmlspecialchars($student['roll_number']); ?>

                    </p>

                    <p>

                        <i class="fas fa-envelope text-primary me-2"></i>

                        <strong>Email:</strong>

                        <?php echo htmlspecialchars($student['email']); ?>

                    </p>

                    <p>

                        <i class="fas fa-user text-primary me-2"></i>

                        <strong>Gender:</strong>

                        <?php echo htmlspecialchars($student['gender']); ?>

                    </p>

                    <p>

                        <i class="fas fa-graduation-cap text-primary me-2"></i>

                        <strong>Class:</strong>

                        <?php echo htmlspecialchars($student['class']); ?>

                    </p>

                    <p>

                        <i class="fas fa-calendar text-primary me-2"></i>

                        <strong>Registered:</strong>

                        <?php echo date(
                            "d M Y",
                            strtotime($student['created_at'])
                        ); ?>

                    </p>

                </div>

                <div class="d-grid gap-2 mt-4">

                    <a
                        href="edit_student.php?id=<?php echo $student['id']; ?>"
                        class="btn btn-warning">

                        <i class="fas fa-edit me-2"></i>

                        Edit Student

                    </a>

                    <a
                        href="delete_student.php?id=<?php echo $student['id']; ?>"
                        class="btn btn-danger"
                        onclick="return confirm('Delete this student?');">

                        <i class="fas fa-trash me-2"></i>

                        Delete Student

                    </a>

                </div>

            </div>

        </div>

    </div>



    <!-- Statistics -->

    <div class="col-lg-8">

        <div class="row">

            <div class="col-md-6 mb-4">

                <div class="dashboard-card bg-primary text-white">

                    <div class="card-icon">

                        <i class="fas fa-upload"></i>

                    </div>

                    <h6>

                        Total Submissions

                    </h6>

                    <h2>

                        <?php echo $submissionCount; ?>

                    </h2>

                    <p>

                        Assignment uploads

                    </p>

                </div>

            </div>



            <div class="col-md-6 mb-4">

                <div class="dashboard-card bg-success text-white">

                    <div class="card-icon">

                        <i class="fas fa-chart-line"></i>

                    </div>

                    <h6>

                        Published Results

                    </h6>

                    <h2>

                        <?php echo $resultCount; ?>

                    </h2>

                    <p>

                        Academic records

                    </p>

                </div>

            </div>



            <div class="col-md-6 mb-4">

                <div class="dashboard-card bg-warning text-white">

                    <div class="card-icon">

                        <i class="fas fa-user-check"></i>

                    </div>

                    <h6>

                        Student Status

                    </h6>

                    <h2>

                        Active

                    </h2>

                    <p>

                        Currently enrolled

                    </p>

                </div>

            </div>



            <div class="col-md-6 mb-4">

                <div class="dashboard-card bg-danger text-white">

                    <div class="card-icon">

                        <i class="fas fa-award"></i>

                    </div>

                    <h6>

                        Performance

                    </h6>

                    <h2>

                        Good

                    </h2>

                    <p>

                        Overall progress

                    </p>

                </div>

            </div>

        </div>
        </div>
    </div>
</div>

<!-- ==========================================
        ASSIGNMENT SUBMISSIONS
========================================== -->

<div class="card shadow-lg border-0 rounded-4 mt-4">

    <div class="card-header bg-white">

        <h4>

            <i class="fas fa-file-upload text-primary me-2"></i>

            Assignment Submissions

        </h4>

    </div>

    <div class="card-body">

<?php

$submissionQuery = mysqli_query($conn, "

SELECT

submissions.*,

assignments.title

FROM submissions

LEFT JOIN assignments

ON submissions.assignment_id=assignments.id

WHERE submissions.student_id='$id'

ORDER BY submissions.submitted_at DESC

");

?>

<div class="table-responsive">

<table class="table table-hover align-middle">

<thead class="table-light">

<tr>

<th>#</th>

<th>Assignment</th>

<th>Submitted</th>

<th>Status</th>

<th>Marks</th>

<th>Feedback</th>

<th>File</th>

</tr>

</thead>

<tbody>

<?php

$i=1;

if(mysqli_num_rows($submissionQuery)>0){

while($row=mysqli_fetch_assoc($submissionQuery)){

?>

<tr>

<td>

<?php echo $i++; ?>

</td>

<td>

<?php echo htmlspecialchars($row['title']); ?>

</td>

<td>

<?php echo date("d M Y",strtotime($row['submitted_at'])); ?>

</td>

<td>

<?php

if($row['status']=="graded"){

echo '<span class="badge bg-success">Graded</span>';

}else{

echo '<span class="badge bg-warning">Submitted</span>';

}

?>

</td>

<td>

<?php

echo ($row['marks']!="")

? $row['marks']." /100"
: "-";

?>

</td>

<td>

<?php

echo !empty($row['feedback'])

? htmlspecialchars($row['feedback'])

: '<span class="text-muted">No Feedback</span>';

?>

</td>

<td>

<?php

if(!empty($row['file_path'])){

?>

<a

href="../<?php echo $row['file_path']; ?>"

target="_blank"

class="btn btn-sm btn-primary">

<i class="fas fa-download"></i>

Download

</a>

<?php

}else{

echo "-";

}

?>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="7" class="text-center">

No Assignment Submitted Yet.

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>

</div>



<!-- ==========================================
        RESULT HISTORY
========================================== -->

<div class="card shadow-lg border-0 rounded-4 mt-4">

<div class="card-header bg-white">

<h4>

<i class="fas fa-chart-line text-success me-2"></i>

Academic Results

</h4>

</div>

<div class="card-body">

<?php

$resultQuery=mysqli_query($conn,"

SELECT *

FROM results

WHERE student_id='$id'

ORDER BY id DESC

");

?>

<div class="table-responsive">

<table class="table table-bordered">

<thead class="table-success">

<tr>

<th>#</th>

<th>Course</th>

<th>Total Marks</th>

<th>Obtained</th>

<th>Grade</th>

<th>Result Date</th>

</tr>

</thead>

<tbody>

<?php

$j=1;

if(mysqli_num_rows($resultQuery)>0){

while($result=mysqli_fetch_assoc($resultQuery)){

?>

<tr>

<td>

<?php echo $j++; ?>

</td>

<td>

<?php echo htmlspecialchars($result['course_name']); ?>

</td>

<td>

<?php echo $result['total_marks']; ?>

</td>

<td>

<?php echo $result['obtained_marks']; ?>

</td>

<td>

<span class="badge bg-primary">

<?php echo $result['grade']; ?>

</span>

</td>

<td>

<?php echo date("d M Y",strtotime($result['created_at'])); ?>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="6" class="text-center">

No Results Available.

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>

</div>



<!-- ==========================================
        ACTION BUTTONS
========================================== -->

<div class="text-end mt-4 mb-5">

<a

href="manage_students.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left"></i>

Back

</a>

<a

href="edit_student.php?id=<?php echo $student['id']; ?>"

class="btn btn-warning">

<i class="fas fa-edit"></i>

Edit

</a>

<a

href="delete_student.php?id=<?php echo $student['id']; ?>"

class="btn btn-danger"

onclick="return confirm('Delete this student?')">

<i class="fas fa-trash"></i>

Delete

</a>

</div>

</div>

</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="assets/js/dashboard.js"></script>

</body>

</html>