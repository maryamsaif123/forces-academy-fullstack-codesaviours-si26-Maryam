<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$student_id = $_SESSION['student_id'];

$query = mysqli_query($conn,"
SELECT assignments.*, courses.course_name
FROM assignments
LEFT JOIN courses
ON assignments.course_id = courses.id
ORDER BY assignments.due_date ASC
");

$totalAssignments = mysqli_num_rows($query);
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Assignments</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="dashboard.css">

<style>

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

.assignment-card{
border:none;
border-radius:20px;
box-shadow:0 10px 25px rgba(0,0,0,.08);
transition:.3s;
margin-bottom:25px;
}

.assignment-card:hover{
transform:translateY(-8px);
}

.assignment-icon{
font-size:60px;
color:#198754;
}

.assignment-card{

overflow:hidden;

}

.assignment-card h4{

font-weight:700;

}

.assignment-card p{

min-height:80px;

}

.assignment-card .btn{

border-radius:12px;

font-weight:600;

}

.assignment-card:hover{

box-shadow:0 20px 40px rgba(0,0,0,.15);

}

.badge{

padding:8px 12px;

font-size:13px;

}

.assignment-icon{

font-size:60px;

color:#198754;

margin-bottom:15px;

}

@media(max-width:768px){

.page-header{

flex-direction:column;

text-align:center;

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

<div>

<h2>

<i class="fas fa-book"></i>

My Assignments

</h2>

<p>

Complete and submit your assignments before the due date.

</p>

</div>

<div>

<span class="badge bg-light text-success fs-5">

<?php echo $totalAssignments; ?>

Assignments

</span>

</div>

</div>

<div class="row">

<?php

while($row = mysqli_fetch_assoc($query))
{

$assignment_id = $row['id'];

// Check submission status

$check = mysqli_query($conn,"
SELECT * FROM submissions
WHERE assignment_id='$assignment_id'
AND student_id='$student_id'
");

$submitted = mysqli_num_rows($check);

?>

<div class="col-lg-6">

<div class="card assignment-card">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<i class="fas fa-book-open assignment-icon"></i>

</div>

<span class="badge bg-danger">

Due:
<?php echo date("d M Y",strtotime($row['due_date'])); ?>

</span>

</div>

<hr>

<h4 class="fw-bold">

<?php echo htmlspecialchars($row['title']); ?>

</h4>

<p class="text-muted">

<?php echo htmlspecialchars($row['description']); ?>

</p>

<div class="mb-3">

<span class="badge bg-primary">

<i class="fas fa-book"></i>

<?php echo htmlspecialchars($row['course_name']); ?>

</span>

</div>

<div class="row text-center">

<div class="col-6">

<h6 class="text-success">

Due Date

</h6>

<p>

<?php echo date("d M Y",strtotime($row['due_date'])); ?>

</p>

</div>

<div class="col-6">

<h6>

Status

</h6>

<?php

if($submitted)

{

?>

<span class="badge bg-success">

Submitted

</span>

<?php

}

else

{

?>

<span class="badge bg-warning text-dark">

Pending

</span>

<?php

}

?>

</div>

</div>

<?php

if(!$submitted)

{

?>

<a

href="submit_assignment.php?id=<?php echo $assignment_id; ?>"

class="btn btn-success w-100 mt-3">

<i class="fas fa-upload"></i>

Submit Assignment

</a>

<?php

}

else

{

?>

<button class="btn btn-secondary w-100 mt-3" disabled>

<i class="fas fa-check-circle"></i>

Already Submitted

</button>

<?php

}

?>

</div>

</div>

</div>

<?php

}

?>

</div>

<?php include("footer.php"); ?>

</div>

<script>

// Live Search

document.addEventListener("DOMContentLoaded",function(){

const search=document.createElement("input");

search.className="form-control form-control-lg mb-4";

search.placeholder="🔍 Search Assignment";

document.querySelector(".container-fluid").insertBefore(

search,

document.querySelector(".row")

);

search.addEventListener("keyup",function(){

let value=this.value.toLowerCase();

let cards=document.querySelectorAll(".assignment-card");

cards.forEach(function(card){

card.style.display=card.innerText.toLowerCase().includes(value)

? ""

: "none";

});

});

});

// Card Animation

document.querySelectorAll(".assignment-card").forEach(function(card){

card.addEventListener("mouseenter",function(){

this.style.transform="translateY(-8px)";

this.style.transition=".3s";

});

card.addEventListener("mouseleave",function(){

this.style.transform="translateY(0px)";

});

});

</script>

</body>

</html>