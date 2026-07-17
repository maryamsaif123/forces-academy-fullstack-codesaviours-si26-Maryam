
<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include("../config/database.php");

if(!isset($_GET['id'])){
    header("Location: manage_submissions.php?success=1");
exit();
}

$id=$_GET['id'];

$query=mysqli_query($conn,"
SELECT
submissions.*,
students.name,
students.email,
assignments.title,
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

if(isset($_POST['grade_submission'])){

$marks=$_POST['marks'];

$total_marks=$_POST['total_marks'];

$feedback=mysqli_real_escape_string($conn,$_POST['feedback']);

$exam_type=$_POST['exam_type'];

$percentage=($marks/$total_marks)*100;

/* Grade */

if($percentage>=90){

$grade="A+";

}elseif($percentage>=80){

$grade="A";

}elseif($percentage>=70){

$grade="B";

}elseif($percentage>=60){

$grade="C";

}elseif($percentage>=50){

$grade="D";

}else{

$grade="F";

}

/* Update Submission */

mysqli_query($conn,"
UPDATE submissions
SET

status='graded'

WHERE id='$id'
");

/* Insert Result */

mysqli_query($conn,"
INSERT INTO results(

student_id,
course_id,
subject,
marks,
total_marks,
grade,
exam_type,
remarks

)

VALUES(

'{$submission['student_id']}',
'{$submission['course_id']}',
'{$submission['title']}',
'$marks',
'$total_marks',
'$grade',
'$exam_type',
'$feedback'

)

");

header("Location: manage_submissions.php?graded=1");

}

?>
<?php include("sidebar.php"); ?>

<div class="main-content">

<?php include("navbar.php"); ?>
<?php

if(isset($_GET['success'])){

?>

<div class="alert alert-success alert-dismissible fade show">

<i class="fas fa-check-circle"></i>

Assignment graded successfully.

<button
class="btn-close"
data-bs-dismiss="alert">

</button>

</div>

<?php

}

?>

<div class="container-fluid">

<div class="page-header">

<div class="d-flex justify-content-between align-items-center">

<h2>

⭐ Grade Assignment

</h2>

<a href="manage_submissions.php"

class="btn btn-light">

← Back

</a>

</div>

</div>

<div class="card shadow-lg border-0 rounded-4 mb-4">

<div class="card-body">

<div class="row">

<div class="col-md-2 text-center">

<img

src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"

width="120"

class="rounded-circle">

</div>

<div class="col-md-10">

<h3>

<?php echo $submission['name']; ?>

</h3>

<p>

<?php echo $submission['email']; ?>

</p>

<hr>

<h5>

Assignment:

<?php echo $submission['title']; ?>

</h5>

<h6>

Course:

<?php echo $submission['course_name']; ?>

</h6>

</div>

</div>

</div>

</div>

<div class="card shadow-lg border-0 rounded-4">

<div class="card-header bg-success text-white">

<h4>

<i class="fas fa-star"></i>

Grade Assignment

</h4>

</div>

<div class="card-body">

<form method="POST">

<div class="row">

<!-- Download Assignment -->

<div class="col-md-12 mb-4">

<label class="form-label fw-bold">

Submitted File

</label>

<br>

<a href="../uploads/<?php echo $submission['file_path']; ?>"

class="btn btn-primary btn-lg"

download>

<i class="fas fa-download"></i>

Download Student Assignment

</a>

</div>

<!-- Marks -->

<div class="col-md-6 mb-3">

<label class="form-label">

Obtained Marks

</label>

<input

type="number"

name="marks"

class="form-control"

required

min="0"

max="100"

placeholder="Enter Obtained Marks">

</div>

<!-- Total Marks -->

<div class="col-md-6 mb-3">

<label class="form-label">

Total Marks

</label>

<input

type="number"

name="total_marks"

class="form-control"

required

value="100">

</div>

<!-- Exam Type -->

<div class="col-md-6 mb-3">

<label class="form-label">

Assessment Type

</label>

<select

name="exam_type"

class="form-select"

required>

<option value="">Select</option>

<option value="Assignment">

Assignment

</option>

<option value="Quiz">

Quiz

</option>

<option value="Project">

Project

</option>

<option value="Lab">

Lab Task

</option>

</select>

</div>

<!-- Status -->

<div class="col-md-6 mb-3">

<label class="form-label">

Submission Status

</label>

<input

type="text"

class="form-control"

value="<?php echo ucfirst($submission['status']); ?>"

readonly>

</div>

<!-- Feedback -->

<div class="col-md-12 mb-4">

<label class="form-label">

Teacher Feedback

</label>

<textarea

name="feedback"

rows="6"

class="form-control"

placeholder="Write feedback for the student..."></textarea>

</div>

<!-- Buttons -->

<div class="col-md-12 text-end">

<a href="manage_submissions.php"

class="btn btn-secondary btn-lg">

Cancel

</a>

<button

type="submit"

name="grade_submission"

class="btn btn-success btn-lg">

<i class="fas fa-check-circle"></i>

Save Grade

</button>

</div>

</div>

</form>

</div>

</div>
<button

type="button"

onclick="window.print();"

class="btn btn-primary btn-lg">

<i class="fas fa-print"></i>

Print

</button>

<!-- Grade Information -->

<div class="row mt-4">

<div class="col-lg-4">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-body text-center">

<i class="fas fa-award fa-4x text-warning mb-3"></i>

<h4>Grading Scale</h4>

<hr>

<p><span class="badge bg-success">A+</span> 90 - 100</p>

<p><span class="badge bg-primary">A</span> 80 - 89</p>

<p><span class="badge bg-info">B</span> 70 - 79</p>

<p><span class="badge bg-warning text-dark">C</span> 60 - 69</p>

<p><span class="badge bg-secondary">D</span> 50 - 59</p>

<p><span class="badge bg-danger">F</span> Below 50</p>

</div>

</div>

</div>

<div class="col-lg-8">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-header bg-white">

<h5>

<i class="fas fa-user-graduate text-success"></i>

Submission Information

</h5>

</div>

<div class="card-body">

<table class="table table-borderless">

<tr>

<th width="180">Student</th>

<td><?php echo $submission['name']; ?></td>

</tr>

<tr>

<th>Email</th>

<td><?php echo $submission['email']; ?></td>

</tr>

<tr>

<th>Course</th>

<td><?php echo $submission['course_name']; ?></td>

</tr>

<tr>

<th>Assignment</th>

<td><?php echo $submission['title']; ?></td>

</tr>

<tr>

<th>Status</th>

<td>

<?php

if($submission['status']=="graded"){

?>

<span class="badge bg-success">

Graded

</span>

<?php

}else{

?>

<span class="badge bg-warning text-dark">

Pending

</span>

<?php

}

?>

</td>

</tr>

</table>

</div>

</div>

</div>

</div>