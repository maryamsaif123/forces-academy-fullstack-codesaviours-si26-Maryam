<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$query=mysqli_query($conn,"
SELECT
submissions.*,
students.full_name,
assignments.title
FROM submissions
INNER JOIN students
ON submissions.student_id=students.id
INNER JOIN assignments
ON submissions.assignment_id=assignments.id
ORDER BY submissions.submitted_at DESC
");

$totalSubmissions=mysqli_num_rows($query);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Submissions</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="dashboard.css">

<style>

.page-header{

background:linear-gradient(135deg,#198754,#22c55e);

padding:35px;

border-radius:20px;

color:white;

display:flex;

justify-content:space-between;

align-items:center;

margin-bottom:30px;

}

.stats-card{

background:white;

padding:25px;

border-radius:20px;

box-shadow:0 10px 25px rgba(0,0,0,.1);

margin-bottom:25px;

}

.stats-card h2{

font-size:40px;

color:#198754;

font-weight:bold;

}

.table{

border-collapse:separate;

border-spacing:0 10px;

}

.table thead th{

background:#198754 !important;

color:white;

border:none;

padding:15px;

}

.table tbody tr{

background:white;

box-shadow:0 5px 15px rgba(0,0,0,.08);

transition:.3s;

}

.table tbody td{

vertical-align:middle;

padding:15px;

border:none;

}

.table tbody tr:hover{

box-shadow:0 15px 35px rgba(0,0,0,.15);

}

.btn{

border-radius:10px;

font-weight:600;

margin:2px;

}

.badge{

padding:8px 12px;

font-size:13px;

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

<i class="fas fa-file-upload"></i>

Manage Submissions

</h2>

<p>

Review student assignment submissions.

</p>

</div>

</div>

<div class="stats-card">

<h5>Total Submissions</h5>

<h2><?php echo $totalSubmissions; ?></h2>

</div>

<div class="table-responsive">

<table class="table table-hover table-bordered align-middle">

<thead class="table-success">

<tr>

<th>ID</th>

<th>Student</th>

<th>Assignment</th>

<th>Submitted</th>

<th>Status</th>

<th>Actions</th>

</tr>

</thead>

<tbody>
<?php

while($row=mysqli_fetch_assoc($query))
{

?>

<tr>

<td>

<strong>

#<?php echo $row['id']; ?>

</strong>

</td>

<td>

<div class="d-flex align-items-center">

<img

src="https://cdn-icons-png.flaticon.com/512/4140/4140048.png"

width="50"

height="50"

class="rounded-circle border border-2 border-success me-3">

<div>

<h6 class="mb-0">

<?php echo htmlspecialchars($row['full_name']); ?>

</h6>

<small class="text-muted">

Student

</small>

</div>

</div>

</td>

<td>

<i class="fas fa-book text-success"></i>

<?php echo htmlspecialchars($row['title']); ?>

</td>

<td>

<?php echo date("d M Y h:i A",strtotime($row['submitted_at'])); ?>

</td>

<td>

<?php

if($row['status']=="submitted")

{

?>

<span class="badge bg-warning text-dark">

Submitted

</span>

<?php

}

else

{

?>

<span class="badge bg-success">

Graded

</span>

<?php

}

?>

</td>

<td>

<a

href="../uploads/assignment_files/<?php echo $row['file_path']; ?>"

target="_blank"

class="btn btn-primary btn-sm">

<i class="fas fa-download"></i>

Download

</a>

<a

href="grade_submission.php?id=<?php echo $row['id']; ?>"

class="btn btn-success btn-sm">

<i class="fas fa-star"></i>

Grade

</a>

<a

href="delete_submission.php?id=<?php echo $row['id']; ?>"

class="btn btn-danger btn-sm"

onclick="return confirm('Delete this submission?')">

<i class="fas fa-trash"></i>

Delete

</a>

</td>

</tr>

<?php

}

?>
</tbody>

</table>

</div>

</div>

<?php include("footer.php"); ?>

</div>

<script>

// ===============================
// Delete Confirmation
// ===============================

document.querySelectorAll(".btn-danger").forEach(function(btn){

btn.addEventListener("click",function(e){

if(!confirm("Are you sure you want to delete this submission?")){

e.preventDefault();

}

});

});

// ===============================
// Table Row Hover Animation
// ===============================

document.querySelectorAll("tbody tr").forEach(function(row){

row.addEventListener("mouseenter",function(){

this.style.transform="scale(1.01)";

this.style.transition=".3s";

});

row.addEventListener("mouseleave",function(){

this.style.transform="scale(1)";

});

});

// ===============================
// Live Search
// ===============================

const search=document.createElement("input");

search.className="form-control form-control-lg mb-4";

search.placeholder="🔍 Search Student or Assignment";

document.querySelector(".container-fluid").insertBefore(

search,

document.querySelector(".table-responsive")

);

search.addEventListener("keyup",function(){

let value=this.value.toLowerCase();

let rows=document.querySelectorAll("tbody tr");

rows.forEach(function(row){

row.style.display=row.innerText.toLowerCase().includes(value)

? ""

: "none";

});

});

</script>

</body>

</html>