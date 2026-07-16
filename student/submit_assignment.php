<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$student_id = $_SESSION['student_id'];

if (!isset($_GET['id'])) {
    header("Location: assignments.php");
    exit();
}

$assignment_id = intval($_GET['id']);

$assignment = mysqli_query($conn,"
SELECT assignments.*, courses.course_name
FROM assignments
LEFT JOIN courses
ON assignments.course_id=courses.id
WHERE assignments.id='$assignment_id'
");

$data = mysqli_fetch_assoc($assignment);

$message = "";

// Check duplicate submission
$check = mysqli_query($conn,"
SELECT *
FROM submissions
WHERE assignment_id='$assignment_id'
AND student_id='$student_id'
");

if(mysqli_num_rows($check)>0){

$message="<div class='alert alert-warning'>
You have already submitted this assignment.
</div>";

}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Submit Assignment</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="dashboard.css">

<style>

.upload-card{

background:white;

padding:35px;

border-radius:20px;

box-shadow:0 10px 25px rgba(0,0,0,.1);

}

.page-header{

background:linear-gradient(135deg,#198754,#22c55e);

padding:35px;

border-radius:20px;

color:white;

margin-bottom:30px;

display:flex;

justify-content:space-between;

align-items:center;

}

.upload-card{

transition:.3s;

}

.upload-card:hover{

box-shadow:0 20px 40px rgba(0,0,0,.15);

}

.form-control{

height:55px;

border-radius:12px;

}

textarea.form-control{

height:auto;

}

.btn-success{

height:55px;

border-radius:12px;

font-size:18px;

font-weight:600;

}

.btn-success:hover{

transform:translateY(-2px);

}

@media(max-width:768px){

.page-header{

flex-direction:column;

text-align:center;

}

.page-header .btn{

margin-top:20px;

}

}

</style>

</head>

<body>

<?php include("sidebar.php"); ?>

<div class="main-content">

<?php include("navbar.php"); ?>

<div class="container-fluid">

<div class="page-header">

<h2>

<i class="fas fa-upload"></i>

Submit Assignment

</h2>

<a href="assignments.php" class="btn btn-light">

Back

</a>

</div>

<?php echo $message; ?>

<div class="upload-card">

<form method="POST" enctype="multipart/form-data">

<div class="mb-4">

<label class="form-label fw-bold">

Assignment Title

</label>

<input

type="text"

class="form-control"

value="<?php echo $data['title']; ?>"

readonly>

</div>

<div class="mb-4">

<label class="form-label fw-bold">

Course

</label>

<input

type="text"

class="form-control"

value="<?php echo $data['course_name']; ?>"

readonly>

</div>

<div class="mb-4">

<label class="form-label fw-bold">

Due Date

</label>

<input

type="text"

class="form-control"

value="<?php echo date('d M Y',strtotime($data['due_date'])); ?>"

readonly>

</div>

<div class="mb-4">

<label class="form-label fw-bold">

Assignment Description

</label>

<textarea

class="form-control"

rows="5"

readonly><?php echo $data['description']; ?></textarea>

</div>

<div class="mb-4">

<label class="form-label fw-bold">

Upload Assignment

</label>

<input

type="file"

name="assignment_file"

class="form-control"

accept=".pdf,.doc,.docx"

required>

<small class="text-muted">

Allowed Files: PDF, DOC, DOCX (Max 10MB)

</small>

</div>

<div class="d-grid">

<button

type="submit"

name="submit_assignment"

class="btn btn-success btn-lg">

<i class="fas fa-upload"></i>

Submit Assignment

</button>

</div>

<?php

if(isset($_POST['submit_assignment'])){

$file=$_FILES['assignment_file'];

$fileName=time()."_".basename($file['name']);

$target="../uploads/assignment_files/".$fileName;

$fileType=strtolower(pathinfo($target,PATHINFO_EXTENSION));

$allowed=array("pdf","doc","docx");

if(in_array($fileType,$allowed)){

if(move_uploaded_file($file['tmp_name'],$target)){

mysqli_query($conn,"
INSERT INTO submissions
(assignment_id,student_id,file_path,status)
VALUES
('$assignment_id','$student_id','$fileName','submitted')
");

echo "<script>

alert('Assignment Submitted Successfully!');

window.location='assignments.php';

</script>";

}else{

echo "<div class='alert alert-danger mt-3'>

File upload failed.

</div>";

}

}else{

echo "<div class='alert alert-danger mt-3'>

Only PDF, DOC and DOCX files are allowed.

</div>";

}

}

?>

</form>

</div>

</div>

<?php include("footer.php"); ?>

</div>

<script>

// ==============================
// File Name Preview
// ==============================

const fileInput = document.querySelector("input[name='assignment_file']");

fileInput.addEventListener("change",function(){

if(this.files.length>0){

alert("Selected File: " + this.files[0].name);

}

});

// ==============================
// Upload Card Animation
// ==============================

const uploadCard=document.querySelector(".upload-card");

uploadCard.addEventListener("mouseenter",function(){

this.style.transform="translateY(-5px)";

this.style.transition=".3s";

});

uploadCard.addEventListener("mouseleave",function(){

this.style.transform="translateY(0px)";

});

// ==============================
// File Size Validation (10MB)
// ==============================

fileInput.addEventListener("change",function(){

const maxSize = 10 * 1024 * 1024; // 10MB

if(this.files[0].size > maxSize){

alert("File size must be less than 10 MB.");

this.value="";

}

});

</script>

</body>

</html>