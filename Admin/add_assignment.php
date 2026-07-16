<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$message = "";

if(isset($_POST['add_assignment'])){

$title = mysqli_real_escape_string($conn,$_POST['title']);

$description = mysqli_real_escape_string($conn,$_POST['description']);

$course_id = $_POST['course_id'];

$due_date = $_POST['due_date'];

$sql="INSERT INTO assignments(title,description,course_id,due_date)
VALUES('$title','$description','$course_id','$due_date')";

if(mysqli_query($conn,$sql)){

$message="<div class='alert alert-success'>
Assignment Added Successfully.
</div>";

}else{

$message="<div class='alert alert-danger'>
Something went wrong.
</div>";

}

}

$courses=mysqli_query($conn,"SELECT * FROM courses ORDER BY course_name ASC");

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Add Assignment</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="dashboard.css">

<style>

.page-header{

background:linear-gradient(135deg,#16a34a,#22c55e);

padding:35px;

border-radius:20px;

color:white;

display:flex;

justify-content:space-between;

align-items:center;

margin-bottom:30px;

}

.assignment-card{

background:white;

border-radius:20px;

padding:35px;

box-shadow:0 10px 25px rgba(0,0,0,.1);

}

.form-control,

.form-select{

height:55px;

border-radius:12px;

}

textarea{

height:160px !important;

}

.btn-save{

background:#198754;

color:white;

height:55px;

border-radius:12px;

font-weight:bold;

}

.btn-save:hover{

background:#157347;

color:white;

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

<i class="fas fa-plus-circle"></i>

Add Assignment

</h2>

<a href="manage_assignments.php" class="btn btn-light">

<i class="fas fa-arrow-left"></i>

Back

</a>

</div>

<?php echo $message; ?>

<div class="assignment-card">

<form method="POST">

<div class="row">

<div class="col-md-12 mb-4">

<label class="form-label fw-bold">

Assignment Title

</label>

<input

type="text"

name="title"

class="form-control"

placeholder="Enter Assignment Title"

required>

</div>

<div class="col-md-12 mb-4">

<label class="form-label fw-bold">

Description

</label>

<textarea

name="description"

class="form-control"

placeholder="Enter Assignment Description"

required></textarea>

</div>

<div class="col-md-6 mb-4">

<label class="form-label fw-bold">

Select Course

</label>

<select

name="course_id"

class="form-select"

required>

<option value="">Choose Course</option>

<?php

while($course=mysqli_fetch_assoc($courses))

{

?>

<option value="<?php echo $course['id']; ?>">

<?php echo $course['course_name']; ?>

</option>

<?php

}

?>

</select>

</div>

<div class="col-md-6 mb-4">

<label class="form-label fw-bold">

Due Date

</label>

<input

type="date"

name="due_date"

class="form-control"

required>

</div>

<div class="col-md-6">

<button

type="submit"

name="add_assignment"

class="btn btn-save w-100">

<i class="fas fa-save"></i>

Save Assignment

</button>

</div>

<div class="col-md-6">

<button

type="reset"

class="btn btn-secondary w-100"

style="height:55px;border-radius:12px;">

<i class="fas fa-rotate-left"></i>

Reset

</button>

</div>

</div>

</form>

</div>

</div>

<?php include("footer.php"); ?>

</div>

<script>

// Animation

const card=document.querySelector(".assignment-card");

card.addEventListener("mouseenter",function(){

this.style.transform="translateY(-5px)";

this.style.transition=".3s";

});

card.addEventListener("mouseleave",function(){

this.style.transform="translateY(0px)";

});

// Today's date as minimum due date

document.querySelector("input[name='due_date']").min=new Date().toISOString().split("T")[0];

</script>

</body>

</html>