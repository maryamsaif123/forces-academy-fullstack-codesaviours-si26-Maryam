<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$result = mysqli_query($conn, "SELECT * FROM students ORDER BY id DESC");

$totalStudents = mysqli_num_rows($result);

$activeStudents = $totalStudents;

$newStudents = 0;

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Students</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="dashboard.css">

<style>

.page-header{

background:linear-gradient(135deg,#16a34a,#22c55e);

border-radius:25px;

padding:35px;

display:flex;

justify-content:space-between;

align-items:center;

margin-bottom:30px;

color:white;

box-shadow:0 10px 30px rgba(0,0,0,.15);

}

.page-header h2{

font-weight:700;

margin-bottom:10px;

}

.page-header p{

margin:0;

opacity:.9;

}

.header-buttons .btn{

margin-left:10px;

border-radius:10px;

}

.summary-card{

border:none;

border-radius:20px;

padding:25px;

color:white;

box-shadow:0 10px 30px rgba(0,0,0,.12);

transition:.3s;

margin-bottom:25px;

}

.summary-card:hover{

transform:translateY(-8px);

}

.summary-card h6{

font-size:17px;

}

.summary-card h2{

font-size:42px;

font-weight:bold;

}

.summary-card i{

font-size:55px;

opacity:.8;

}

.blue{

background:linear-gradient(135deg,#3b82f6,#2563eb);

}

.green{

background:linear-gradient(135deg,#10b981,#059669);

}

.orange{

background:linear-gradient(135deg,#f97316,#ea580c);

}

.search-card{

background:white;

border-radius:20px;

padding:25px;

box-shadow:0 10px 25px rgba(0,0,0,.08);

margin-bottom:30px;

}

.table{

vertical-align:middle;

}

.table thead th{

font-weight:600;

padding:18px;

}

.table tbody td{

padding:16px;

}

.table tbody tr{

transition:.3s;

}

.table tbody tr:hover{

background:#f0fff4;

transform:scale(1.01);

}

.btn-sm{

border-radius:10px;

margin:2px;

padding:8px 12px;

}

.badge{

padding:8px 12px;

font-size:13px;

}

/* ==========================
   Student Table
========================== */

.table{

border-collapse:separate;

border-spacing:0 10px;

}

.table thead{

background:#198754;

color:white;

}

.table thead th{

border:none;

padding:18px;

}

.table tbody tr{

background:white;

box-shadow:0 5px 15px rgba(0,0,0,.08);

transition:.3s;

}

.table tbody tr:hover{

transform:translateY(-3px);

box-shadow:0 10px 25px rgba(0,0,0,.15);

}

.table td{

vertical-align:middle;

padding:18px;

border:none;

}


.btn{

border-radius:12px;

font-weight:600;

transition:.3s;

}

.btn:hover{

transform:translateY(-2px);

}


.student-avatar{

width:55px;

height:55px;

border-radius:50%;

border:3px solid #198754;

object-fit:cover;

}

.badge{

padding:8px 14px;

font-size:13px;

}


#search{

border-radius:15px;

height:55px;

border:2px solid #198754;

}

#search:focus{

box-shadow:0 0 10px rgba(25,135,84,.3);

}

/* ==========================
Responsive
========================== */

@media(max-width:768px){

.page-header{

flex-direction:column;

text-align:center;

}

.header-buttons{

margin-top:20px;

}

.summary-card{

margin-bottom:20px;

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

<i class="fas fa-user-graduate"></i>

Manage Students

</h2>

<p>

Manage all registered students of Forces Academy LMS.

</p>

</div>

<div class="header-buttons">

<a href="add_student.php" class="btn btn-light">

<i class="fas fa-user-plus"></i>

Add Student

</a>

<a href="#" class="btn btn-warning">

<i class="fas fa-file-pdf"></i>

PDF

</a>

<a href="#" class="btn btn-info">

<i class="fas fa-file-excel"></i>

Excel

</a>

</div>

</div>

<div class="row">

<div class="col-lg-4">

<div class="summary-card blue">

<div class="d-flex justify-content-between">

<div>

<h6>Total Students</h6>

<h2><?php echo $totalStudents; ?></h2>

</div>

<i class="fas fa-users"></i>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="summary-card green">

<div class="d-flex justify-content-between">

<div>

<h6>Active Students</h6>

<h2><?php echo $activeStudents; ?></h2>

</div>

<i class="fas fa-user-check"></i>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="summary-card orange">

<div class="d-flex justify-content-between">

<div>

<h6>New Admissions</h6>

<h2><?php echo $newStudents; ?></h2>

</div>

<i class="fas fa-chart-line"></i>

</div>

</div>

</div>

</div>

<div class="search-card">

<div class="row">

<div class="col-md-8">

<input

type="text"

id="search"

class="form-control form-control-lg"

placeholder="🔍 Search student by name, email or roll number">

</div>

<div class="col-md-4 text-end">

<a href="add_student.php" class="btn btn-success btn-lg">

<i class="fas fa-user-plus"></i>

New Student

</a>

</div>

</div>

</div>

<!-- Student Records Card -->

<div class="card border-0 shadow-lg rounded-4">

<div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">

<h4 class="fw-bold mb-0">

<i class="fas fa-user-graduate text-success"></i>

Student Records

</h4>

<span class="badge bg-success fs-6">

<?php echo $totalStudents; ?> Students

</span>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-hover align-middle" id="studentTable">

<thead class="table-success">

<tr>

<th>ID</th>

<th>Student</th>

<th>Email</th>

<th>Roll No</th>

<th>Class</th>

<th>Status</th>

<th class="text-center">Actions</th>

</tr>

</thead>

<tbody>

<?php

mysqli_data_seek($result,0);

while($row=mysqli_fetch_assoc($result))
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

width="55"

height="55"

class="rounded-circle border border-3 border-success shadow-sm me-3">

<div>

<h6 class="mb-0 fw-bold">

<?php echo htmlspecialchars($row['full_name']); ?>

</h6>

<small class="text-muted">

Student ID:
<?php echo $row['id']; ?>

</small>

</div>

</div>

</td>

<td>

<i class="fas fa-envelope text-primary me-2"></i>

<?php echo htmlspecialchars($row['email']); ?>

</td>

<td>

<span class="badge bg-info">

<?php echo htmlspecialchars($row['roll_number']); ?>

</span>

</td>

<td>

<span class="badge bg-secondary">

<?php echo htmlspecialchars($row['class']); ?>

</span>

</td>

<td>

<span class="badge bg-success">

<i class="fas fa-circle me-1"></i>

Active

</span>

</td>

<td class="text-center">

<a

href="view_student.php?id=<?php echo $row['id']; ?>"

class="btn btn-info btn-sm"

title="View">

<i class="fas fa-eye"></i>

</a>

<a

href="edit_student.php?id=<?php echo $row['id']; ?>"

class="btn btn-warning btn-sm"

title="Edit">

<i class="fas fa-edit"></i>

</a>

<a

href="delete_student.php?id=<?php echo $row['id']; ?>"

class="btn btn-danger btn-sm"

title="Delete"

onclick="return confirm('Are you sure you want to delete this student?');">

<i class="fas fa-trash"></i>

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

</div>

</div>

</div>

<?php include("footer.php"); ?>

</div>

<script>

// ===========================
// Live Search
// ===========================

document.getElementById("search").addEventListener("keyup", function () {

let value = this.value.toLowerCase();

let rows = document.querySelectorAll("#studentTable tbody tr");

rows.forEach(function(row){

row.style.display = row.innerText.toLowerCase().includes(value)

? ""

: "none";

});

});


// ===========================
// Delete Confirmation
// ===========================

document.querySelectorAll(".btn-danger").forEach(function(btn){

btn.addEventListener("click",function(e){

if(!confirm("Are you sure you want to delete this student?")){

e.preventDefault();

}

});

});


// ===========================
// Hover Animation
// ===========================

const cards = document.querySelectorAll(".summary-card");

cards.forEach(card=>{

card.addEventListener("mouseenter",()=>{

card.style.transform="translateY(-8px) scale(1.03)";

});

card.addEventListener("mouseleave",()=>{

card.style.transform="translateY(0px)";

});

});

</script>

</body>

</html>