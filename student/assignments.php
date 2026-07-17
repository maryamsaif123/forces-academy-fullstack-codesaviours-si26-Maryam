<?php
session_start();

if(!isset($_SESSION['student_id'])){
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$student_id = $_SESSION['student_id'];

/* ============================
   Fetch Assignments
============================= */

$result = mysqli_query($conn,"
SELECT
assignments.*,
courses.course_name
FROM assignments
LEFT JOIN courses
ON assignments.course_id = courses.id
ORDER BY assignments.deadline ASC
");

/* ============================
   Dashboard Statistics
============================= */

$totalAssignments = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM assignments
"));

$submittedAssignments = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM submissions
WHERE student_id='$student_id'
"));

$pendingAssignments = $totalAssignments - $submittedAssignments;

?>

<!DOCTYPE html>

<html lang="en">

<head>
<link rel="stylesheet" href="../css/dashboard.css">

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>

Student Assignments

</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="dashboard.css">

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

.summary-card h2{

font-size:34px;

font-weight:bold;

}

.assignment-card{

border:none;

border-radius:20px;

transition:.3s;

overflow:hidden;

}

.assignment-card:hover{

transform:translateY(-8px);

box-shadow:0 18px 35px rgba(0,0,0,.15);

}

</style>

</head>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

/* ==========================
   LIVE SEARCH
========================== */

document.getElementById("searchInput").addEventListener("keyup", function(){

let value=this.value.toLowerCase();

let cards=document.querySelectorAll(".assignment-item");

cards.forEach(function(card){

card.style.display=card.innerText.toLowerCase().includes(value)

? "block"

: "none";

});

});


/* ==========================
   ASSIGNMENT ANALYTICS
========================== */

const assignmentChart=document.getElementById("assignmentChart");

if(assignmentChart){

new Chart(assignmentChart,{

type:'doughnut',

data:{

labels:['Submitted','Pending'],

datasets:[{

data:[

<?php echo $submittedAssignments; ?>,

<?php echo $pendingAssignments; ?>

],

backgroundColor:[

'#16a34a',

'#f59e0b'

],

borderWidth:0

}]

},

options:{

responsive:true,

plugins:{

legend:{

position:'bottom'

}

}

}

});

}

</script>

</body>

</html>

<body>

<?php include("sidebar.php"); ?>

<div class="main-content">

<?php include("navbar.php"); ?>

<div class="container-fluid">

<!-- Header -->

<div class="page-header">

<div class="d-flex justify-content-between align-items-center">

<div>

<h2>

<i class="fas fa-book-open"></i>

My Assignments

</h2>

<p>

View, Submit and Track Your Assignments

</p>

</div>

<div>

<i class="fas fa-user-graduate fa-4x"></i>

</div>

</div>

</div>

<!-- Statistics -->

<div class="row mb-4">

<div class="col-md-4">

<div class="summary-card blue">

<h6>Total Assignments</h6>

<h2>

<?php echo $totalAssignments; ?>

</h2>

</div>

</div>

<div class="col-md-4">

<div class="summary-card green">

<h6>Submitted</h6>

<h2>

<?php echo $submittedAssignments; ?>

</h2>

</div>

</div>

<div class="col-md-4">

<div class="summary-card orange">

<h6>Pending</h6>

<h2>

<?php echo $pendingAssignments; ?>

</h2>

</div>

</div>

</div>

<!-- Search -->

<div class="card border-0 shadow-lg rounded-4 mb-4">

<div class="card-body">

<input

type="text"

id="searchInput"

class="form-control form-control-lg"

placeholder="Search Assignment or Course">

</div>

</div>
<!-- ============================
     Assignment Cards
============================= -->

<div class="row" id="assignmentContainer">

<?php

if(mysqli_num_rows($result)>0){

while($row=mysqli_fetch_assoc($result)){

$assignment_id = $row['id'];

/* Check if student already submitted */

$check = mysqli_query($conn,"
SELECT *
FROM submissions
WHERE assignment_id='$assignment_id'
AND student_id='$student_id'
");

$isSubmitted = mysqli_num_rows($check);

/* Status */

if(strtotime($row['deadline']) < strtotime(date("Y-m-d"))){

$status = "Overdue";
$badge = "danger";

}else{

$status = "Active";
$badge = "success";

}

?>

<div class="col-lg-4 mb-4 assignment-item">

<div class="card assignment-card shadow-lg h-100">

<div class="card-body">

<div class="text-center mb-3">

<img
src="https://cdn-icons-png.flaticon.com/512/3976/3976626.png"
width="90"
class="mb-3">

<h4 class="fw-bold">

<?php echo htmlspecialchars($row['title']); ?>

</h4>

<span class="badge bg-primary">

<?php echo htmlspecialchars($row['course_name']); ?>

</span>

</div>

<hr>

<p class="text-muted">

<?php echo nl2br(htmlspecialchars($row['description'])); ?>

</p>

<div class="mb-3">

<i class="fas fa-calendar-alt text-success"></i>

<strong>Deadline:</strong>

<?php echo date("d M Y",strtotime($row['deadline'])); ?>

</div>

<div class="mb-3">

Status:

<span class="badge bg-<?php echo $badge; ?>">

<?php echo $status; ?>

</span>

</div>

<?php

if($isSubmitted){

?>

<div class="alert alert-success text-center">

<i class="fas fa-check-circle"></i>

Already Submitted

</div>

<a href="my_submissions.php"

class="btn btn-success w-100">

<i class="fas fa-eye"></i>

View Submission

</a>

<?php

}else{

?>

<a href="submit_assignment.php?id=<?php echo $row['id']; ?>"

class="btn btn-primary w-100 mb-2">

<i class="fas fa-upload"></i>

Submit Assignment

</a>

<a href="assignment_details.php?id=<?php echo $row['id']; ?>"

class="btn btn-outline-success w-100">

<i class="fas fa-book-open"></i>

View Details

</a>

<?php

}

?>

</div>

</div>

</div>

<?php

}

}else{

?>

<div class="col-12">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-body text-center p-5">

<img
src="https://cdn-icons-png.flaticon.com/512/4076/4076478.png"
width="150"
class="mb-4">

<h3>

No Assignments Available

</h3>

<p class="text-muted">

There are currently no assignments available.

</p>

</div>

</div>

</div>

<?php

}

?>

</div>
<div class="row mb-4">

<div class="col-lg-8">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-header bg-white">

<h5>

<i class="fas fa-chart-pie text-success"></i>

Assignment Progress

</h5>

</div>

<div class="card-body">

<canvas id="assignmentChart" height="120"></canvas>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-body text-center">

<i class="fas fa-book-reader fa-4x text-success mb-3"></i>

<h2>

<?php echo $totalAssignments; ?>

</h2>

<p class="text-muted">

Available Assignments

</p>

<hr>

<p>

<strong>

<?php echo $submittedAssignments; ?>

</strong>

Submitted

</p>

<p>

<strong>

<?php echo $pendingAssignments; ?>

</strong>

Pending

</p>

</div>

</div>

</div>

</div>