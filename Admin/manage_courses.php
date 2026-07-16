<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$result=mysqli_query($conn,"SELECT * FROM courses ORDER BY id DESC");

$totalCourses=mysqli_num_rows($result);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Courses</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="dashboard.css">

<style>

.page-header{

background:linear-gradient(135deg,#198754,#22c55e);

padding:35px;

border-radius:20px;

display:flex;

justify-content:space-between;

align-items:center;

color:white;

margin-bottom:30px;

box-shadow:0 10px 25px rgba(0,0,0,.15);

}

.summary-card{

background:linear-gradient(135deg,#3b82f6,#2563eb);

color:white;

padding:25px;

border-radius:20px;

box-shadow:0 10px 25px rgba(0,0,0,.15);

margin-bottom:30px;

transition:.3s;

}

.summary-card:hover{

transform:translateY(-8px);

}

.summary-card h2{

font-size:45px;

font-weight:bold;

}

.summary-card i{

font-size:55px;

opacity:.8;

}

.course-card{

border:none;

border-radius:20px;

box-shadow:0 10px 25px rgba(0,0,0,.08);

margin-bottom:25px;

transition:.3s;

}

.course-card:hover{

transform:translateY(-6px);

}

.course-icon{

font-size:55px;

color:#198754;

}

.teacher{

color:#198754;

font-weight:600;

}

</style>

</head>

<script>

document.getElementById("courseSearch").addEventListener("keyup",function(){

let value=this.value.toLowerCase();

let cards=document.querySelectorAll(".course-card");

cards.forEach(function(card){

card.style.display=card.innerText.toLowerCase().includes(value)

? ""

: "none";

});

});

// Card Animation

document.querySelectorAll(".course-card").forEach(function(card){

card.addEventListener("mouseenter",function(){

this.style.transform="translateY(-10px)";

});

card.addEventListener("mouseleave",function(){

this.style.transform="translateY(0px)";

});

});

</script>

<body>

<?php include("sidebar.php"); ?>

<div class="main-content">

<?php include("navbar.php"); ?>

<div class="container-fluid">

<div class="page-header">

<div>

<h2>

<i class="fas fa-book-open"></i>

Manage Courses

</h2>

<p>

Manage all LMS courses.

</p>

</div>

<div>

<a href="add_course.php" class="btn btn-light">

<i class="fas fa-plus"></i>

Add Course

</a>

</div>

</div>

<div class="summary-card">

<div class="d-flex justify-content-between">

<div>

<h5>Total Courses</h5>

<h2>

<?php echo $totalCourses; ?>

</h2>

</div>

<i class="fas fa-book"></i>

</div>

</div>

<div class="card border-0 shadow-lg rounded-4 mb-4">

<div class="card-body">

<input

type="text"

id="search"

class="form-control form-control-lg"

placeholder="🔍 Search Courses">

</div>

</div>

<?php

$totalCourses = mysqli_num_rows($result);

mysqli_data_seek($result,0);

?>

<div class="row mb-4">

<div class="col-lg-3">

<div class="card border-0 shadow rounded-4 bg-primary text-white">

<div class="card-body">

<h6>Total Courses</h6>

<h2><?php echo $totalCourses; ?></h2>

</div>

</div>

</div>

<div class="col-lg-9 text-end">

<a href="add_course.php" class="btn btn-success btn-lg">

<i class="fas fa-plus-circle"></i>

Add New Course

</a>

<button class="btn btn-danger btn-lg">

<i class="fas fa-file-pdf"></i>

Export PDF

</button>

<button class="btn btn-success btn-lg">

<i class="fas fa-file-excel"></i>

Export Excel

</button>

</div>

</div>

<div class="mb-4">

<input

type="text"

id="courseSearch"

class="form-control form-control-lg"

placeholder="🔍 Search Course by Name or Teacher">

</div>

<div class="row">

<?php
while($course = mysqli_fetch_assoc($result))
{

?>
<div class="col-lg-4 col-md-6 mb-4">

<div class="card border-0 shadow-lg rounded-4 h-100 course-card">

<img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800"
class="card-img-top"
style="height:220px;object-fit:cover;">

<div class="card-body d-flex flex-column">

<div class="d-flex justify-content-between align-items-center mb-3">

<h4 class="fw-bold text-success">

<?php echo $course['course_name']; ?>

</h4>

<span class="badge bg-success">

Active

</span>

</div>

<p>

<i class="fas fa-chalkboard-teacher text-primary"></i>

<strong>Teacher:</strong>

<?php echo $course['teacher_name']; ?>

</p>

<p class="text-muted">

<?php echo substr($course['description'],0,120); ?>

<?php
if(strlen($course['description'])>120){
echo "...";
}
else{

?>

<div class="col-12">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-body text-center p-5">

<i class="fas fa-book-open fa-5x text-success mb-4"></i>

<h3>No Courses Available</h3>

<p class="text-muted">

Start by adding your first course.

</p>

<a href="add_course.php"

class="btn btn-success btn-lg">

Add Course

</a>

</div>

</div>

</div>

<?php

}

?>

</p>

<hr>

<div class="mb-2">

<i class="fas fa-calendar text-success"></i>

<strong>Created:</strong>

<?php echo date("d M Y",strtotime($course['created_at'])); ?>

</div>

<div class="mt-auto">

<?php

if(!empty($course['notes_pdf']))
{

?>

<a href="../uploads/<?php echo $course['notes_pdf']; ?>"

target="_blank"

class="btn btn-outline-primary w-100 mb-2">

<i class="fas fa-file-pdf"></i>

Download Notes

</a>

<?php

}

?>

<?php

if(!empty($course['video_link']))
{

?>

<a href="<?php echo $course['video_link']; ?>"

target="_blank"

class="btn btn-outline-success w-100 mb-2">

<i class="fas fa-play-circle"></i>

Watch Video

</a>

<?php

}

?>

<div class="d-flex gap-2 mt-3">

<a href="edit_course.php?id=<?php echo $course['id']; ?>"

class="btn btn-warning w-50">

<i class="fas fa-edit"></i>

Edit

</a>

<a href="delete_course.php?id=<?php echo $course['id']; ?>"

class="btn btn-danger w-50"

onclick="return confirm('Are you sure you want to delete this course?')">

<i class="fas fa-trash"></i>

Delete

</a>

</div>

</div>

</div>

</div>

</div>

<?php
}
?>

</div>