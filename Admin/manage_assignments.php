<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/* =======================
   DASHBOARD STATISTICS
======================= */

$totalAssignments = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM assignments"));
$activeAssignments = mysqli_num_rows(mysqli_query($conn,"
SELECT *
FROM assignments
WHERE deadline >= CURDATE()
"));

$expiredAssignments = mysqli_num_rows(mysqli_query($conn,"
SELECT *
FROM assignments
WHERE deadline < CURDATE()
"));

$totalSubmissions = mysqli_num_rows(mysqli_query($conn,"
SELECT *
FROM submissions
"));

/* =======================
   FETCH ASSIGNMENTS
======================= */

$result = mysqli_query($conn,"
SELECT
assignments.*,
courses.course_name
FROM assignments
LEFT JOIN courses
ON assignments.course_id=courses.id
ORDER BY assignments.id DESC
");

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>

Manage Assignments

</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link rel="stylesheet"
href="dashboard.css">

<style>

.page-header{

background:linear-gradient(135deg,#16a34a,#22c55e);

padding:35px;

border-radius:20px;

color:white;

margin-bottom:30px;

box-shadow:0 15px 35px rgba(0,0,0,.15);

}

.summary-card{

border:none;

border-radius:20px;

padding:25px;

color:white;

transition:.3s;

box-shadow:0 10px 25px rgba(0,0,0,.12);

}

.summary-card:hover{

transform:translateY(-8px);

}

.blue{

background:linear-gradient(135deg,#2563eb,#1d4ed8);

}

.green{

background:linear-gradient(135deg,#16a34a,#15803d);

}

.orange{

background:linear-gradient(135deg,#ea580c,#fb923c);

}

.purple{

background:linear-gradient(135deg,#7c3aed,#9333ea);

}

.summary-card i{

font-size:50px;

opacity:.8;

}

.summary-card h2{

font-size:35px;

font-weight:bold;

}

</style>

</head>
<!-- Chart.js -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

/* ===========================
   LIVE SEARCH
=========================== */

document.getElementById("searchInput").addEventListener("keyup",function(){

let value=this.value.toLowerCase();

let rows=document.querySelectorAll("#assignmentTable tbody tr");

rows.forEach(function(row){

row.style.display=row.innerText.toLowerCase().includes(value)

? ""

: "none";

});

});


/* ===========================
   ANALYTICS CHART
=========================== */

const ctx=document.getElementById("assignmentChart");

if(ctx){

new Chart(ctx,{

type:'bar',

data:{

labels:['Active','Expired','Submissions'],

datasets:[{

label:'Assignments',

data:[

<?php echo $activeAssignments; ?>,

<?php echo $expiredAssignments; ?>,

<?php echo $totalSubmissions; ?>

],

backgroundColor:[

'#16a34a',

'#ef4444',

'#2563eb'

],

borderRadius:8

}]

},

options:{

responsive:true,

plugins:{

legend:{

display:false

}

},

scales:{

y:{

beginAtZero:true

}

}

}

});

}

</script>

<body>

<?php include("sidebar.php"); ?>

<div class="main-content">

<?php include("navbar.php"); ?>

<div class="container-fluid">

<div class="page-header">

<div class="d-flex justify-content-between align-items-center">

<div>

<h2>

<i class="fas fa-book-open"></i>

Manage Assignments

</h2>

<p>

Create, Edit and Manage Student Assignments

</p>

</div>

<div>

<a href="add_assignment.php"

class="btn btn-light btn-lg">

<i class="fas fa-plus-circle"></i>

Add Assignment

</a>

</div>

</div>

</div>

<!-- Statistics -->

<div class="row mb-4">

<div class="col-lg-3">

<div class="summary-card blue">

<div class="d-flex justify-content-between">

<div>

<h6>Total Assignments</h6>

<h2>

<?php echo $totalAssignments; ?>

</h2>

</div>

<i class="fas fa-book"></i>

</div>

</div>

</div>

<div class="col-lg-3">

<div class="summary-card green">

<div class="d-flex justify-content-between">

<div>

<h6>Active</h6>

<h2>

<?php echo $activeAssignments; ?>

</h2>

</div>

<i class="fas fa-check-circle"></i>

</div>

</div>

</div>

<div class="col-lg-3">

<div class="summary-card orange">

<div class="d-flex justify-content-between">

<div>

<h6>Expired</h6>

<h2>

<?php echo $expiredAssignments; ?>

</h2>

</div>

<i class="fas fa-clock"></i>

</div>

</div>

</div>

<div class="col-lg-3">

<div class="summary-card purple">

<div class="d-flex justify-content-between">

<div>

<h6>Submissions</h6>

<h2>

<?php echo $totalSubmissions; ?>

</h2>

</div>

<i class="fas fa-upload"></i>

</div>

</div>

</div>

</div>

<!-- Search -->

<div class="card shadow-lg border-0 rounded-4 mb-4">

<div class="card-body">

<div class="row">

<div class="col-md-8">

<input
type="text"
id="searchInput"
class="form-control form-control-lg"
placeholder="Search Assignment, Course or Due Date">

</div>

<div class="col-md-4 text-end">

<a href="add_assignment.php"

class="btn btn-success btn-lg">

<i class="fas fa-plus"></i>

New Assignment

</a>

</div>

</div>

</div>

</div>
<!-- Assignments Table -->

<div class="card border-0 shadow-lg rounded-4">

<div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">

<h4 class="fw-bold">

<i class="fas fa-book-open text-success"></i>

All Assignments

</h4>

<span class="badge bg-success fs-6">

<?php echo $totalAssignments; ?> Assignments

</span>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-hover align-middle" id="assignmentTable">

<thead class="table-success">

<tr>

<th>ID</th>

<th>Assignment</th>

<th>Course</th>

<th>Due Date</th>

<th>Status</th>

<th>Submissions</th>

<th width="250">Actions</th>

</tr>

</thead>

<tbody>

<?php

if(mysqli_num_rows($result)>0){

while($row=mysqli_fetch_assoc($result)){

/* Count Submissions */

$assignment_id=$row['id'];

$submission=mysqli_query($conn,"
SELECT COUNT(*) AS total
FROM submissions
WHERE assignment_id='$assignment_id'
");

$total=mysqli_fetch_assoc($submission);

/* Status */

$status = (strtotime($row['deadline']) >= strtotime(date("Y-m-d")))
? "Active"
: "Expired";

?> 
<tr>

<td>

<strong>

<?php echo $row['id']; ?>

</strong>

</td>

<td>

<div class="d-flex align-items-center">

<img
src="https://cdn-icons-png.flaticon.com/512/3135/3135755.png"
width="55"
height="55"
class="rounded-circle border border-3 border-success me-3">

<div>

<h6 class="mb-1">

<?php echo $row['title']; ?>

</h6>

<small class="text-muted">

<?php echo substr($row['description'],0,60); ?>...

</small>

</div>

</div>

</td>

<td>

<span class="badge bg-primary">

<?php echo $row['course_name']; ?>

</span>

</td>

<td>

<i class="fas fa-calendar text-success"></i>


</td>

<td>

<?php

if($status=="Active"){

?>

<span class="badge bg-success">

Active

</span>

<?php

}else{

?>

<span class="badge bg-danger">

Expired

</span>

<?php

}

?>

</td>

<td>

<span class="badge bg-info">

<?php echo $total['total']; ?>

Submitted

</span>

</td>

<td>

<a href="view_assignment.php?id=<?php echo $row['id']; ?>"
class="btn btn-info btn-sm">

<i class="fas fa-eye"></i>

<a href="edit_assignment.php?id=<?php echo $row['id']; ?>"

class="btn btn-warning btn-sm">

<i class="fas fa-edit"></i>

Edit

</a>

<a href="manage_submissions.php?id=<?php echo $row['id']; ?>"
class="btn btn-warning btn-sm">

<i class="fas fa-upload"></i>

<a href="delete_assignment.php?id=<?php echo $row['id']; ?>"

class="btn btn-danger btn-sm"

onclick="return confirm('Are you sure you want to delete this assignment?')">

<i class="fas fa-trash"></i>

Delete

</a>
</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="7">

<div class="text-center py-5">

<img
src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png"
width="120"
class="mb-4">

<h3>

No Assignments Found

</h3>

<p class="text-muted">

Create your first assignment to get started.

</p>

<a href="add_assignment.php"
class="btn btn-success btn-lg">

<i class="fas fa-plus-circle"></i>

Create Assignment

</a>

</div>

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

<div class="row mb-4">

<div class="col-lg-8">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-header bg-white border-0">

<h4>

<i class="fas fa-chart-bar text-success"></i>

Assignment Analytics

</h4>

</div>

<div class="card-body">

<canvas id="assignmentChart" height="120"></canvas>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-body text-center">

<i class="fas fa-tasks fa-4x text-success mb-3"></i>

<h2>

<?php echo $totalAssignments; ?>

</h2>

<p class="text-muted">

Total Assignments

</p>

<hr>

<h5>

<?php echo $totalSubmissions; ?>

Submissions Received

</h5>

</div>

</div>

</div>

</div>