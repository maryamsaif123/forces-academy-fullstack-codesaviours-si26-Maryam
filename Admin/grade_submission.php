<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/*=========================================
    VALIDATE SUBMISSION ID
=========================================*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid Submission ID.";

    header("Location: manage_submissions.php");
    exit();
}

$id = (int)$_GET['id'];

/*=========================================
    FETCH SUBMISSION
=========================================*/

$stmt = mysqli_prepare(

$conn,

"SELECT

submissions.*,

students.full_name,
students.roll_number,

assignments.title

FROM submissions

INNER JOIN students
ON students.id=submissions.student_id

INNER JOIN assignments
ON assignments.id=submissions.assignment_id

WHERE submissions.id=?

LIMIT 1"

);

mysqli_stmt_bind_param($stmt,"i",$id);

mysqli_stmt_execute($stmt);

$result=mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)==0){

$_SESSION['error']="Submission not found.";

header("Location: manage_submissions.php");

exit();

}

$submission=mysqli_fetch_assoc($result);

/*=========================================
    UPDATE GRADE
=========================================*/

if(isset($_POST['grade'])){

$marks=(int)$_POST['marks'];

$feedback=trim($_POST['feedback']);

if($marks<0 || $marks>100){

$_SESSION['error']="Marks must be between 0 and 100.";

header("Location: grade_submission.php?id=".$id);

exit();

}

$update=mysqli_prepare(

$conn,

"UPDATE submissions

SET

marks=?,
feedback=?,
status='graded'

WHERE id=?"

);

mysqli_stmt_bind_param(

$update,

"isi",

$marks,

$feedback,

$id

);

if(mysqli_stmt_execute($update)){

$_SESSION['success']="Submission graded successfully.";

header("Location: manage_submissions.php");

exit();

}else{

$_SESSION['error']="Database Error.";

}

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>

Grade Submission

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
rel="stylesheet">

<link
href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet">

<style>

body{

background:#f4f7fb;

font-family:'Poppins',sans-serif;

}

.card{

border:none;

border-radius:20px;

box-shadow:0 15px 35px rgba(0,0,0,.08);

}

.card-header{

background:linear-gradient(135deg,#0d6efd,#20c997);

color:white;

padding:25px;

}

.info{

background:#f8f9fa;

padding:20px;

border-radius:12px;

margin-bottom:25px;

}

</style>

</head>

<body>

<div class="container py-5">

<div class="card">

<div class="card-header">

<h3>

<i class="fas fa-star me-2"></i>

Grade Assignment Submission

</h3>

</div>

<div class="card-body">

<div class="info">

<h5>

Student Information

</h5>

<hr>

<p>

<strong>Name:</strong>

<?php echo htmlspecialchars($submission['full_name']); ?>

</p>

<p>

<strong>Roll Number:</strong>

<?php echo htmlspecialchars($submission['roll_number']); ?>

</p>

<p>

<strong>Assignment:</strong>

<?php echo htmlspecialchars($submission['title']); ?>

</p>

</div>

<form method="POST">

<div class="mb-4">

<label class="form-label">

Marks (0-100)

</label>

<input

type="number"

name="marks"

class="form-control"

min="0"

max="100"

required

value="<?php echo $submission['marks']; ?>">

</div>

<div class="mb-4">

<label class="form-label">

Feedback

</label>

<textarea

name="feedback"

rows="8"

class="form-control"

placeholder="Write comments for the student..."><?php

echo htmlspecialchars($submission['feedback']);

?></textarea>

</div>

<div class="d-flex justify-content-end">

<a

href="manage_submissions.php"

class="btn btn-secondary me-2">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<button

type="submit"

name="grade"

class="btn btn-success">

<i class="fas fa-check-circle me-2"></i>

Save Grade

</button>

</div>

</form>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>