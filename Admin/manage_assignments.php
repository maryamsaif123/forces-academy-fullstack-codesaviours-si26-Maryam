<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

// Get assignments with course name
$query = mysqli_query($conn,"
SELECT assignments.*, courses.course_name
FROM assignments
LEFT JOIN courses
ON assignments.course_id = courses.id
ORDER BY assignments.id DESC
");

$totalAssignments = mysqli_num_rows($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Manage Assignments</title>

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
box-shadow:0 10px 25px rgba(0,0,0,.15);
}

.stats-card{
background:white;
border-radius:20px;
padding:25px;
box-shadow:0 10px 25px rgba(0,0,0,.08);
margin-bottom:30px;
}

.stats-card h2{
font-size:42px;
font-weight:bold;
color:#198754;
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
font-size:55px;
color:#198754;

.assignment-card{

overflow:hidden;

}

.assignment-card h4{

font-weight:700;

color:#222;

}

.assignment-card p{

min-height:70px;

}

.assignment-card .btn{

border-radius:12px;

font-weight:600;

}

.assignment-card .btn:hover{

transform:translateY(-2px);

transition:.3s;

}

.assignment-card .badge{

font-size:13px;

padding:8px 12px;

}

.assignment-card:hover{

box-shadow:0 20px 40px rgba(0,0,0,.15);

}

/* Search Box */

#search{

height:55px;

border-radius:15px;

border:2px solid #198754;

}

#search:focus{

border-color:#16a34a;

box-shadow:0 0 10px rgba(22,163,74,.3);

}

/* Buttons */

.btn{

border-radius:12px;

font-weight:600;

transition:.3s;

}

.btn:hover{

transform:translateY(-2px);

}

/* Assignment Cards */

.assignment-card{

overflow:hidden;

transition:.3s;

}

.assignment-card:hover{

box-shadow:0 20px 40px rgba(0,0,0,.15);

}

/* Responsive */

@media(max-width:768px){

.page-header{

flex-direction:column;

text-align:center;

}

.page-header .btn{

margin-top:20px;

}

.stats-card{

margin-bottom:20px;

}

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

<i class="fas fa-tasks"></i>

Manage Assignments

</h2>

<p>Create and manage all assignments.</p>

</div>

<a href="add_assignment.php" class="btn btn-light btn-lg">

<i class="fas fa-plus"></i>

Add Assignment

</a>

</div>

<div class="stats-card">

<div class="d-flex justify-content-between align-items-center">

<div>

<h5>Total Assignments</h5>

<h2><?php echo $totalAssignments; ?></h2>

</div>

<i class="fas fa-file-alt fa-4x text-success"></i>

</div>

</div>

<div class="row">

<?php

while($row=mysqli_fetch_assoc($query))
{

?>

<div class="col-lg-6">

<div class="card assignment-card">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<i class="fas fa-book assignment-icon"></i>

</div>

<span class="badge bg-success">

<?php echo date("d M Y",strtotime($row['due_date'])); ?>

</span>

</div>

<hr>

<h4 class="fw-bold mt-3">

<?php echo htmlspecialchars($row['title']); ?>

</h4>

<p class="text-muted">

<?php echo htmlspecialchars($row['description']); ?>

</p>

<div class="mb-3">

<span class="badge bg-primary">

<i class="fas fa-book-open"></i>

<?php echo htmlspecialchars($row['course_name']); ?>

</span>

</div>

<div class="row text-center mb-3">

<div class="col-6">

<h6 class="text-success">

Due Date

</h6>

<p class="mb-0">

<?php echo date("d M Y",strtotime($row['due_date'])); ?>

</p>

</div>

<div class="col-6">

<h6 class="text-primary">

Status

</h6>

<span class="badge bg-success">

Active

</span>

</div>

</div>

<div class="d-grid gap-2">

<a href="view_assignment.php?id=<?php echo $row['id']; ?>" class="btn btn-info">

<i class="fas fa-eye"></i>

View Assignment

</a>

<a href="edit_assignment.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">

<i class="fas fa-edit"></i>

Edit Assignment

</a>

<a href="delete_assignment.php?id=<?php echo $row['id']; ?>"

class="btn btn-danger"

onclick="return confirm('Delete this assignment?')">

<i class="fas fa-trash"></i>

Delete Assignment

</a>

</div>

</div>

</div>

</div>

<?php

}

?>

</div>

<div class="card border-0 shadow-lg rounded-4 mt-4">

<div class="card-body">

<input

type="text"

id="search"

class="form-control form-control-lg"

placeholder="🔍 Search Assignment">

</div>

</div>

</div>

<?php include("footer.php"); ?>

</div>

<script>

// ==========================
// Live Search
// ==========================

document.getElementById("search").addEventListener("keyup", function(){

let value=this.value.toLowerCase();

let cards=document.querySelectorAll(".assignment-card");

cards.forEach(function(card){

card.style.display=card.innerText.toLowerCase().includes(value)

? ""

: "none";

});

});

// ==========================
// Card Hover Animation
// ==========================

const assignmentCards=document.querySelectorAll(".assignment-card");

assignmentCards.forEach(function(card){

card.addEventListener("mouseenter",function(){

this.style.transform="translateY(-8px) scale(1.02)";

});

card.addEventListener("mouseleave",function(){

this.style.transform="translateY(0px)";

});

});

// ==========================
// Delete Confirmation
// ==========================

document.querySelectorAll(".btn-danger").forEach(function(btn){

btn.addEventListener("click",function(e){

if(!confirm("Are you sure you want to delete this assignment?")){

e.preventDefault();

}

});

});

</script>

</body>

</html>