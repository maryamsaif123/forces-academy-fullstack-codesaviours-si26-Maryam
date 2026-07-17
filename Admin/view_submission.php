<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include("../config/database.php");

if(!isset($_GET['id'])){
    header("Location: manage_submissions.php");
    exit();
}

$id=$_GET['id'];

$query=mysqli_query($conn,"
SELECT
submissions.*,
students.name,
students.email,
assignments.title,
assignments.description,
assignments.deadline,
courses.course_name

FROM submissions

LEFT JOIN students
ON submissions.student_id=students.id

LEFT JOIN assignments
ON submissions.assignment_id=assignments.id

LEFT JOIN courses
ON assignments.course_id=courses.id

WHERE submissions.id='$id'
");

$submission=mysqli_fetch_assoc($query);

/* Get Result */

$result=mysqli_query($conn,"
SELECT *
FROM results
WHERE student_id='{$submission['student_id']}'
AND subject='{$submission['title']}'
LIMIT 1
");

$grade=mysqli_fetch_assoc($result);

?>
<?php include("sidebar.php"); ?>

<div class="main-content">

<?php include("navbar.php"); ?>

<div class="container-fluid">

<div class="page-header">

<div class="d-flex justify-content-between align-items-center">

<div>

<h2>

<i class="fas fa-eye"></i>

View Submission

</h2>

<p>

Assignment Submission Details

</p>

</div>

<div>

<a href="manage_submissions.php"

class="btn btn-light">

<i class="fas fa-arrow-left"></i>

Back

</a>

</div>

</div>

</div>

<div class="card shadow-lg border-0 rounded-4 mb-4">

<div class="card-body">

<div class="row">

<div class="col-md-2 text-center">

<img

src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"

width="120"

class="rounded-circle shadow">

</div>

<div class="col-md-10">

<h3>

<?php echo $submission['name']; ?>

</h3>

<p>

<?php echo $submission['email']; ?>

</p>

<hr>

<div class="row">

<div class="col-md-4">

<strong>

Course

</strong>

<br>

<?php echo $submission['course_name']; ?>

</div>

<div class="col-md-4">

<strong>

Assignment

</strong>

<br>

<?php echo $submission['title']; ?>

</div>

<div class="col-md-4">

<strong>

Deadline

</strong>

<br>

<?php echo date("d M Y",strtotime($submission['deadline'])); ?>

</div>

</div>

</div>

</div>

</div>

</div>

<!-- ===========================
     Assignment Details
============================ -->

<div class="card shadow-lg border-0 rounded-4 mb-4">

<div class="card-header bg-success text-white">

<h4>

<i class="fas fa-book-open"></i>

Assignment Details

</h4>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-8">

<h5>

<?php echo $submission['title']; ?>

</h5>

<p class="text-muted">

<?php echo nl2br($submission['description']); ?>

</p>

<hr>

<div class="row">

<div class="col-md-4">

<strong>

Course

</strong>

<br>

<?php echo $submission['course_name']; ?>

</div>

<div class="col-md-4">

<strong>

Deadline

</strong>

<br>

<?php echo date("d M Y",strtotime($submission['deadline'])); ?>

</div>

<div class="col-md-4">

<strong>

Submitted On

</strong>

<br>

<?php echo date("d M Y",strtotime($submission['submitted_at'])); ?>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card bg-light border-0">

<div class="card-body text-center">

<i class="fas fa-file-pdf fa-5x text-danger mb-3"></i>

<h5>

Submitted File

</h5>

<p class="text-muted">

<?php echo basename($submission['file_path']); ?>

</p>

<a

href="../uploads/<?php echo $submission['file_path']; ?>"

download

class="btn btn-success w-100 mb-2">

<i class="fas fa-download"></i>

Download File

</a>

<button

onclick="window.print()"

class="btn btn-primary w-100">

<i class="fas fa-print"></i>

Print

</button>

</div>

</div>

</div>

</div>

</div>

</div>

<div class="card shadow-lg border-0 rounded-4 mb-4">

<div class="card-header bg-white">

<h5>

<i class="fas fa-info-circle text-success"></i>

Submission Status

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-4">

<h6>Status</h6>

<?php

if($submission['status']=="graded"){

?>

<span class="badge bg-success fs-6">

Graded

</span>

<?php

}else{

?>

<span class="badge bg-warning text-dark fs-6">

Pending Review

</span>

<?php

}

?>

</div>

<div class="col-md-4">

<h6>Submission Date</h6>

<p>

<?php echo date("d M Y h:i A",strtotime($submission['submitted_at'])); ?>

</p>

</div>

<div class="col-md-4">

<h6>File Type</h6>

<p>

<?php

echo strtoupper(pathinfo($submission['file_path'],PATHINFO_EXTENSION));

?>

</p>

</div>

</div>

</div>

</div>
<div class="card shadow-lg border-0 rounded-4 mb-4">

<div class="card-header bg-success text-white">

<h4>

<i class="fas fa-award"></i>

Assignment Result

</h4>

</div>

<div class="card-body">

<?php if($grade){ ?>

<div class="row">

<div class="col-md-3 text-center">

<?php

$badge="secondary";

if($grade['grade']=="A+") $badge="success";
elseif($grade['grade']=="A") $badge="primary";
elseif($grade['grade']=="B") $badge="info";
elseif($grade['grade']=="C") $badge="warning";
elseif($grade['grade']=="D") $badge="secondary";
else $badge="danger";

?>

<span class="badge bg-<?php echo $badge; ?> fs-2 p-3">

<?php echo $grade['grade']; ?>

</span>

</div>

<div class="col-md-9">

<div class="row">

<div class="col-md-4">

<h6>Obtained Marks</h6>

<h3 class="text-success">

<?php echo $grade['marks']; ?>

</h3>

</div>

<div class="col-md-4">

<h6>Total Marks</h6>

<h3>

<?php echo $grade['total_marks']; ?>

</h3>

</div>

<div class="col-md-4">

<h6>Percentage</h6>

<h3 class="text-primary">

<?php echo round(($grade['marks']/$grade['total_marks'])*100); ?>%

</h3>

</div>

</div>

<hr>

<h5>

Teacher Feedback

</h5>

<div class="alert alert-light border">

<?php

echo !empty($grade['remarks'])

? nl2br(htmlspecialchars($grade['remarks']))

: "<span class='text-muted'>No feedback available.</span>";

?>

</div>

</div>

</div>

<?php } else { ?>

<div class="text-center py-5">

<i class="fas fa-hourglass-half fa-5x text-warning mb-3"></i>

<h3>

Result Not Available

</h3>

<p class="text-muted">

This submission has not been graded yet.

</p>

<a href="grade_submission.php?id=<?php echo $submission['id']; ?>"

class="btn btn-success btn-lg">

<i class="fas fa-star"></i>

Grade Submission

</a>

</div>

<?php } ?>

</div>

</div>

<div class="text-end mb-5">

<a href="manage_submissions.php"

class="btn btn-secondary btn-lg">

<i class="fas fa-arrow-left"></i>

Back

</a>

<button

onclick="window.print()"

class="btn btn-primary btn-lg">

<i class="fas fa-print"></i>

Print

</button>

</div>