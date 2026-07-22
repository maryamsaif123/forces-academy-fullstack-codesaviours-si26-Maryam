<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

// Statistics
$totalStudents = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM students"));
$totalCourses = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM courses"));
$totalAssignments = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM assignments"));
$totalSubmissions = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM submissions"));

date_default_timezone_set("Asia/Karachi");

$hour=date("H");

if($hour<12){
$greeting="Good Morning";
}elseif($hour<17){
$greeting="Good Afternoon";
}else{
$greeting="Good Evening";
}
?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Admin Dashboard</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

<link rel="stylesheet" href="dashboard.css">

<style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const ctx=document.getElementById("dashboardChart");

new Chart(ctx,{

type:"bar",

data:{

labels:["Students","Courses","Assignments","Submissions"],

datasets:[{

label:"Academy Statistics",

data:[

<?php echo $totalStudents; ?>,

<?php echo $totalCourses; ?>,

<?php echo $totalAssignments; ?>,

<?php echo $totalSubmissions; ?>

],

backgroundColor:[

"#2563eb",

"#16a34a",

"#f97316",

"#7c3aed"

],

borderRadius:12

}]

},

options:{

responsive:true,

plugins:{

legend:{

display:false

}

}

}

});

</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

<script>

AOS.init({

duration:1000,

once:true

});


function updateClock(){

const now=new Date();

document.getElementById("todayDate").innerHTML=now.toDateString();

document.getElementById("liveClock").innerHTML=now.toLocaleTimeString();

}

setInterval(updateClock,1000);

updateClock();

// Dark Mode

document.getElementById("darkModeBtn").onclick=function(){

document.body.classList.toggle("dark-mode");

};

</script>

</body>

</html>


body{

background:#f5f7fb;

}

.hero{

background:linear-gradient(135deg,#0d6efd,#198754);

padding:45px;

border-radius:25px;

color:white;

margin-bottom:35px;

position:relative;

overflow:hidden;

}

.hero img{

width:300px;

position:absolute;

right:20px;

bottom:0;

}

.stats-card{

border:none;

border-radius:20px;

padding:25px;

color:white;

transition:.3s;

box-shadow:0 10px 25px rgba(0,0,0,.12);

}

.stats-card:hover{

transform:translateY(-8px);

}

.blue{

background:linear-gradient(135deg,#2563eb,#1d4ed8);

}

.green{

background:linear-gradient(135deg,#16a34a,#15803d);

}

.orange{

background:linear-gradient(135deg,#f97316,#ea580c);

}

.purple{

background:linear-gradient(135deg,#7c3aed,#6d28d9);

}

.stats-card i{

font-size:50px;

opacity:.8;

}

.quick-card{

border-radius:18px;

transition:.3s;

}

.quick-card:hover{

transform:translateY(-5px);

}

</style>

</head>

<body>

<?php include("sidebar.php"); ?>

<div class="main-content">

<?php include("navbar.php"); ?>

<div class="container-fluid">

<div class="hero">

<div class="row align-items-center">

<div class="col-lg-7">

<h2>

<?php echo $greeting; ?> Admin 👋

</h2>

<p class="mt-3">

Welcome to the Forces Academy Learning Management System.

Manage your academy from one professional dashboard.

</p>

<a href="manage_students.php"

class="btn btn-light btn-lg">

Manage Students

</a>

</div>

<div class="col-lg-5 text-end">

<img src="https://cdn-icons-png.flaticon.com/512/2206/2206368.png">

</div>

</div>

</div>

<div class="row">

<div class="col-md-3 mb-4">

<div class="stats-card blue">

<div class="d-flex justify-content-between">

<div>

<h6>Total Students</h6>

<h2><?php echo $totalStudents; ?></h2>

</div>

<i class="fas fa-user-graduate"></i>

</div>

</div>

</div>

<div class="col-md-3 mb-4">

<div class="stats-card green">

<div class="d-flex justify-content-between">

<div>

<h6>Courses</h6>

<h2><?php echo $totalCourses; ?></h2>

</div>

<i class="fas fa-book"></i>

</div>

</div>

</div>

<div class="col-md-3 mb-4">

<div class="stats-card orange">

<div class="d-flex justify-content-between">

<div>

<h6>Assignments</h6>

<h2><?php echo $totalAssignments; ?></h2>

</div>

<i class="fas fa-file-alt"></i>

</div>

</div>

</div>

<div class="col-md-3 mb-4">

<div class="stats-card purple">

<div class="d-flex justify-content-between">

<div>

<h6>Submissions</h6>

<h2><?php echo $totalSubmissions; ?></h2>

</div>

<i class="fas fa-upload"></i>

</div>

</div>

</div>

</div>

<!-- Quick Actions -->

<div class="row">

<div class="col-lg-3 mb-4">

<a href="manage_students.php" class="text-decoration-none">

<div class="card quick-card shadow border-0">

<div class="card-body text-center">

<i class="fas fa-users fa-3x text-primary mb-3"></i>

<h5>Manage Students</h5>

</div>

</div>

</a>

</div>

<div class="col-lg-3 mb-4">

<a href="manage_courses.php" class="text-decoration-none">

<div class="card quick-card shadow border-0">

<div class="card-body text-center">

<i class="fas fa-book fa-3x text-success mb-3"></i>

<h5>Manage Courses</h5>

</div>

</div>

</a>

</div>

<div class="col-lg-3 mb-4">

<a href="manage_assignments.php" class="text-decoration-none">

<div class="card quick-card shadow border-0">

<div class="card-body text-center">

<i class="fas fa-file-signature fa-3x text-warning mb-3"></i>

<h5>Assignments</h5>

</div>

</div>

</a>

</div>

<div class="col-lg-3 mb-4">

<a href="manage_submissions.php" class="text-decoration-none">

<div class="card quick-card shadow border-0">

<div class="card-body text-center">

<i class="fas fa-check-circle fa-3x text-danger mb-3"></i>

<h5>Results</h5>

</div>

</div>

</a>

</div>

</div>

<!-- ================= Analytics + Recent Students ================= -->

<div class="row mt-4">

<div class="col-lg-8">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-header bg-white border-0">

<h4 class="fw-bold">

<i class="fas fa-chart-line text-success"></i>

Academy Analytics

</h4>

</div>

<div class="card-body">

<canvas id="dashboardChart" height="120"></canvas>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-header bg-white border-0">

<h4 class="fw-bold">

<i class="fas fa-user-graduate text-primary"></i>

Recently Registered Students

</h4>

</div>

<div class="card-body">

<?php

$students=mysqli_query($conn,"
SELECT *
FROM students
ORDER BY id DESC
LIMIT 5
");

while($student=mysqli_fetch_assoc($students))
{

?>

<div class="d-flex align-items-center mb-3">

<img

src="https://cdn-icons-png.flaticon.com/512/4140/4140048.png"

width="55"

height="55"

class="rounded-circle border border-3 border-success me-3">

<div>

<h6 class="mb-0">

<?php echo $student['full_name']; ?>

</h6>

<small class="text-muted">

<?php echo $student['email']; ?>

</small>

</div>

</div>

<hr>

<?php

}

?>

</div>

</div>

</div>

</div>

<!-- ================= Latest Courses ================= -->

<div class="row mt-4">

<div class="col-lg-6">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-header bg-white border-0">

<h4 class="fw-bold">

<i class="fas fa-book text-success"></i>

Latest Courses

</h4>

</div>

<div class="card-body">

<?php

$courses=mysqli_query($conn,"
SELECT *
FROM courses
ORDER BY id DESC
LIMIT 5
");

while($course=mysqli_fetch_assoc($courses))
{

?>

<div class="d-flex justify-content-between align-items-center mb-3">

<div>

<h6>

<?php echo $course['course_name']; ?>

</h6>

<small class="text-muted">

<?php echo $course['teacher_name']; ?>

</small>

</div>

<span class="badge bg-success">

Active

</span>

</div>

<hr>

<?php

}

?>

<a href="manage_courses.php"

class="btn btn-success w-100">

Manage Courses

</a>

</div>

</div>

</div>

<!-- ================= Recent Assignments ================= -->

<div class="col-lg-6">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-header bg-white border-0">

<h4 class="fw-bold">

<i class="fas fa-file-alt text-warning"></i>

Latest Assignments

</h4>

</div>

<div class="card-body">

<?php

$assignments=mysqli_query($conn,"
SELECT *
FROM assignments
ORDER BY id DESC
LIMIT 5
");

while($assignment=mysqli_fetch_assoc($assignments))
{

?>

<div class="d-flex justify-content-between mb-3">

<div>

<h6>

<?php echo $assignment['title']; ?>

</h6>

<small class="text-muted">

Due:

<?php echo date("d M Y",strtotime($assignment['deadline'])); ?>

</small>

</div>

<span class="badge bg-warning">

Pending

</span>

</div>

<hr>

<?php

}

?>

<a href="manage_assignments.php"

class="btn btn-warning w-100">

Manage Assignments

</a>

</div>

</div>

</div>

</div>

<!-- ================= Recent Submissions ================= -->

<div class="row mt-4">

<div class="col-lg-12">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-header bg-white border-0">

<h4 class="fw-bold">

<i class="fas fa-upload text-primary"></i>

Recent Assignment Submissions

</h4>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-hover align-middle">

<thead class="table-success">

<tr>

<th>Student</th>

<th>Assignment</th>

<th>Status</th>

<th>Date</th>

</tr>

</thead>

<tbody>

<?php

$submissions=mysqli_query($conn,"
SELECT
submissions.*,
students.full_name,
assignments.title
FROM submissions
INNER JOIN students
ON submissions.student_id=students.id
INNER JOIN assignments
ON submissions.assignment_id=assignments.id
ORDER BY submissions.id DESC
LIMIT 5
");

while($row=mysqli_fetch_assoc($submissions))
{

?>

<tr>

<td><?php echo $row['full_name']; ?></td>

<td><?php echo $row['title']; ?></td>

<td>

<span class="badge bg-success">

<?php echo ucfirst($row['status']); ?>

</span>

</td>

<td>

<?php echo date("d M Y",strtotime($row['submitted_at'])); ?>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

<a href="manage_submissions.php"

class="btn btn-primary w-100">

View All Submissions

</a>

</div>

</div>

</div>

</div>

<!-- ================= Latest Notices ================= -->

<div class="row mt-4">

<div class="col-lg-6">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-header bg-white border-0">

<h4 class="fw-bold">

<i class="fas fa-bullhorn text-danger"></i>

Latest Notices

</h4>

</div>

<div class="card-body">

<?php

$notices=mysqli_query($conn,"
SELECT *
FROM notices
ORDER BY id DESC
LIMIT 5
");

if(mysqli_num_rows($notices)>0){

while($notice=mysqli_fetch_assoc($notices))
{

?>

<div class="mb-3">

<h6><?php echo $notice['title']; ?></h6>

<p class="text-muted">

<?php echo substr($notice['content'],0,80); ?>...

</p>
<hr>

</div>

<?php

}

}else{

echo "<p class='text-muted'>No notices available.</p>";

}

?>

<a href="manage_notices.php"

class="btn btn-danger w-100">

Manage Notices

</a>

</div>

</div>

</div>

<!-- ================= Top Students ================= -->

<div class="col-lg-6">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-header bg-white border-0">

<h4 class="fw-bold">

<i class="fas fa-trophy text-warning"></i>

Top Performing Students

</h4>

</div>

<div class="card-body">

<?php

$top=mysqli_query($conn,"
SELECT full_name,email
FROM students
ORDER BY id DESC
LIMIT 5
");

while($student=mysqli_fetch_assoc($top))
{

?>

<div class="d-flex align-items-center mb-3">

<img

src="https://cdn-icons-png.flaticon.com/512/4140/4140048.png"

width="50"

class="rounded-circle me-3">

<div>

<h6 class="mb-0">

<?php echo $student['full_name']; ?>

</h6>

<small class="text-muted">

<?php echo $student['email']; ?>

</small>

</div>

<span class="badge bg-warning ms-auto">

⭐

</span>

</div>

<hr>

<?php

}

?>

</div>

</div>

</div>

</div>

<!-- ================= Footer ================= -->

<div class="row mt-4">

<div class="col-lg-12">

<div class="card border-0 shadow rounded-4">

<div class="card-body d-flex justify-content-between align-items-center">

<div>

<h5 class="mb-0">

📅 <span id="todayDate"></span>

</h5>

<small class="text-muted">

⏰ <span id="liveClock"></span>

</small>

</div>

<div>

<button

id="darkModeBtn"

class="btn btn-dark">

🌙 Dark Mode

</button>

</div>

</div>

</div>

</div>

</div>

</div>

<?php include("footer.php"); ?>