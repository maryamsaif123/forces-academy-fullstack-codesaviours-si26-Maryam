<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['student_id'])){
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

if(!isset($_GET['id'])){
    header("Location: assignments.php");
    exit();
}

$assignment_id = (int)$_GET['id'];

/* Fetch Assignment */

$assignmentQuery = mysqli_query($conn,"
SELECT
assignments.*,
courses.course_name
FROM assignments
LEFT JOIN courses
ON assignments.course_id = courses.id
WHERE assignments.id='$assignment_id'
LIMIT 1
");

if(mysqli_num_rows($assignmentQuery)==0){

header("Location: assignments.php");

exit();

}

$assignment=mysqli_fetch_assoc($assignmentQuery);

/* Check Already Submitted */

$check=mysqli_query($conn,"
SELECT *

FROM submissions

WHERE

student_id='$student_id'

AND

assignment_id='$assignment_id'

");

$alreadySubmitted=mysqli_num_rows($check);

/* Upload Assignment */

$message="";

if(isset($_POST['submit_assignment'])){

if($alreadySubmitted>0){

$message="<div class='alert alert-warning'>
You have already submitted this assignment.
</div>";

}else{

$file=$_FILES['assignment_file'];

$fileName=time()."_".$file['name'];

$tmpName=$file['tmp_name'];

$folder="../uploads/".$fileName;

$fileType=strtolower(pathinfo($fileName,PATHINFO_EXTENSION));

$allowed=['pdf','zip','doc','docx','jpg','jpeg','png'];

if(in_array($fileType,$allowed)){

move_uploaded_file($tmpName,$folder);

mysqli_query($conn,"
INSERT INTO submissions

(student_id,assignment_id,file_path,status,submitted_at)

VALUES

('$student_id',
'$assignment_id',
'$fileName',
'Pending',
NOW())

");

$message="<div class='alert alert-success'>
Assignment submitted successfully.
</div>";

}else{

$message="<div class='alert alert-danger'>
Only PDF, DOC, DOCX, ZIP and Images are allowed.
</div>";

}

}

}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Submit Assignment</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="../css/dashboard.css">

</head>

<body>

<?php include("sidebar.php"); ?>

<div class="main-content">

<?php include("navbar.php"); ?>

<div class="container-fluid">

<!-- Hero -->

<div class="page-header">

<div class="d-flex justify-content-between align-items-center">

<div>

<h2>

<i class="fas fa-upload"></i>

Submit Assignment

</h2>

<p>

Upload your completed assignment before the deadline.

</p>

</div>

<a href="assignments.php"

class="btn btn-light">

<i class="fas fa-arrow-left"></i>

Back

</a>

</div>

</div>

<?php echo $message; ?>

<div class="row">

<!-- Assignment Card -->

<div class="col-lg-4">

<div class="card shadow border-0 rounded-4 mb-4">

<div class="card-body">

<span class="badge bg-success mb-3">

<?php echo htmlspecialchars($assignment['course_name']); ?>

</span>

<h3>

<?php echo htmlspecialchars($assignment['title']); ?>

</h3>

<p class="text-muted">

<?php echo nl2br(htmlspecialchars($assignment['description'])); ?>

</p>

<hr>

<p>

<strong>

Deadline

</strong>

<br>

<span class="text-danger">

<i class="fas fa-calendar"></i>

<?php echo date("d M Y",strtotime($assignment['deadline'])); ?>

</span>

</p>

<?php

$status=(strtotime($assignment['deadline'])>=time())

? "Open"

: "Closed";

?>

<span class="badge bg-<?php

echo ($status=="Open")

?"success"

:"danger";

?>">

<?php echo $status; ?>

</span>

</div>

</div>

</div>

<!-- Upload Card -->

<div class="col-lg-8">

<div class="card shadow border-0 rounded-4">

<div class="card-body">

<h3>

<i class="fas fa-cloud-upload-alt"></i>

Upload Assignment

</h3>

<hr>

<?php

if($alreadySubmitted){

?>

<div class="alert alert-success">

<i class="fas fa-check-circle"></i>

You have already submitted this assignment.

</div>

<?php

}else{

?>

<form

method="POST"

enctype="multipart/form-data">

<div class="mb-4">

<label class="form-label">

Choose Assignment File

</label>

<input

type="file"

name="assignment_file"

class="form-control"

required>

<div class="form-text">

Allowed:

PDF,

DOC,

DOCX,

ZIP,

JPG,

PNG

</div>

</div>

<div class="row">

<div class="col-md-6">

<button

type="submit"

name="submit_assignment"

class="btn btn-success btn-lg w-100">

<i class="fas fa-paper-plane"></i>

Submit Assignment

</button>

</div>

<div class="col-md-6">

<a

href="assignments.php"

class="btn btn-secondary btn-lg w-100">

<i class="fas fa-times"></i>

Cancel

</a>

</div>

</div>

</form>

<?php

}

?>

</div>

</div>

</div>

</div>

<!-- Upload Tips -->

<div class="row mt-4">

<div class="col-lg-12">

<div class="card border-0 shadow rounded-4">

<div class="card-body">

<h5>

<i class="fas fa-lightbulb text-warning"></i>

Submission Guidelines

</h5>

<ul class="mb-0">

<li>Upload only your own work.</li>

<li>Accepted file types:
PDF, DOC, DOCX, ZIP, JPG, PNG.</li>

<li>Maximum file size: 10 MB.</li>

<li>Late submissions may not be accepted.</li>

<li>Check your file before submitting.</li>

</ul>

</div>

</div>

</div>

</div>

</div>

</div>

<style>

.page-header{

background:linear-gradient(135deg,#16a34a,#22c55e);

padding:35px;

border-radius:20px;

color:white;

margin-bottom:30px;

}

.card{

border:none;

border-radius:20px;

}

.form-control{

border-radius:12px;

padding:12px;

}

.btn{

border-radius:12px;

padding:12px;

font-weight:600;

}

.card:hover{

transform:translateY(-3px);

transition:.3s;

box-shadow:0 20px 40px rgba(0,0,0,.12);

}

.badge{

font-size:14px;

padding:8px 14px;

}

</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

document.querySelector("input[type=file]").addEventListener("change",function(){

if(this.files.length){

alert("Selected File:\n\n"+this.files[0].name);

}

});

</script>

</body>

</html>