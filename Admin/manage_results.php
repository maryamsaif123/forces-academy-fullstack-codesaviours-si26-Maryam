<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

// Dashboard Statistics
$totalResults = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM results"));

$totalStudents = mysqli_num_rows(mysqli_query($conn,"SELECT DISTINCT student_id FROM results"));

$averageMarksQuery = mysqli_query($conn,"
SELECT ROUND(AVG(marks),2) AS avgMarks
FROM results
");

$averageMarks = mysqli_fetch_assoc($averageMarksQuery)['avgMarks'];

$highestMarksQuery = mysqli_query($conn,"
SELECT MAX(marks) AS highestMarks
FROM results
");

$highestMarks = mysqli_fetch_assoc($highestMarksQuery)['highestMarks'];

$result = mysqli_query($conn,"
SELECT
results.*,
students.full_name,
students.email
FROM results
LEFT JOIN students
ON results.student_id = students.id
ORDER BY results.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Manage Results</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="dashboard.css">

<style>

.page-header{

background:linear-gradient(135deg,#16a34a,#15803d);

color:white;

padding:35px;

border-radius:20px;

margin-bottom:30px;

box-shadow:0 15px 35px rgba(0,0,0,.15);

}

.stats-card{

border:none;

border-radius:18px;

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

background:linear-gradient(135deg,#ea580c,#fb923c);

}

.purple{

background:linear-gradient(135deg,#7c3aed,#9333ea);

}

.stats-card h2{

font-size:34px;

font-weight:bold;

}

.stats-card i{

font-size:45px;

opacity:.8;

}

</style>

</head>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// ================= LIVE SEARCH =================

document.getElementById("searchInput").addEventListener("keyup", function () {

    let value = this.value.toLowerCase();

    let rows = document.querySelectorAll("#resultsTable tbody tr");

    rows.forEach(function(row){

        row.style.display =
            row.innerText.toLowerCase().includes(value)
            ? ""
            : "none";

    });

});

// ================= RESULTS CHART =================

const ctx=document.getElementById("resultChart");

if(ctx){

new Chart(ctx,{

type:'doughnut',

data:{

labels:['A+','A','B','C'],

datasets:[{

data:[

<?php

echo mysqli_num_rows(mysqli_query($conn,"SELECT * FROM results WHERE grade='A+'"));

?>,

<?php

echo mysqli_num_rows(mysqli_query($conn,"SELECT * FROM results WHERE grade='A'"));

?>,

<?php

echo mysqli_num_rows(mysqli_query($conn,"SELECT * FROM results WHERE grade='B'"));

?>,

<?php

echo mysqli_num_rows(mysqli_query($conn,"SELECT * FROM results WHERE grade='C'"));

?>

],

backgroundColor:[

'#16a34a',

'#2563eb',

'#f59e0b',

'#ef4444'

]

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
<body>

<?php include("sidebar.php"); ?>

<div class="main-content">

<?php include("navbar.php"); ?>

<div class="container-fluid">

<div class="page-header">

<div class="d-flex justify-content-between align-items-center">

<div>

<h2>

<i class="fas fa-chart-line"></i>

Manage Results

</h2>

<p>

View, edit and manage all student results.

</p>

</div>

<div>

<a href="add_result.php" class="btn btn-light btn-lg">

<i class="fas fa-plus"></i>

Add Result

</a>

</div>

</div>

</div>

<div class="row mb-4">

<div class="col-lg-3">

<div class="stats-card blue">

<div class="d-flex justify-content-between">

<div>

<h6>Total Results</h6>

<h2><?php echo $totalResults; ?></h2>

</div>

<i class="fas fa-file-alt"></i>

</div>

</div>

</div>

<div class="col-lg-3">

<div class="stats-card green">

<div class="d-flex justify-content-between">

<div>

<h6>Students</h6>

<h2><?php echo $totalStudents; ?></h2>

</div>

<i class="fas fa-users"></i>

</div>

</div>

</div>

<div class="col-lg-3">

<div class="stats-card orange">

<div class="d-flex justify-content-between">

<div>

<h6>Average Marks</h6>

<h2><?php echo $averageMarks; ?>%</h2>

</div>

<i class="fas fa-chart-column"></i>

</div>

</div>

</div>

<div class="col-lg-3">

<div class="stats-card purple">

<div class="d-flex justify-content-between">

<div>

<h6>Highest Marks</h6>

<h2><?php echo $highestMarks; ?></h2>

</div>

<i class="fas fa-trophy"></i>

</div>

</div>

</div>

</div>

<div class="card shadow-lg border-0 rounded-4 mb-4">

<div class="card-body">

<div class="row">

<div class="col-md-8">

<input
type="text"
id="searchInput"
class="form-control form-control-lg"
placeholder="Search Student, Subject or Grade">

</div>

<div class="col-md-4 text-end">

<a href="export_results.php" class="btn btn-success btn-lg">

<i class="fas fa-file-excel"></i>

Export Excel

</a>

</div>

</div>

</div>

</div>
<div class="card shadow-lg border-0 rounded-4">

<div class="card-header bg-white border-0">

<h4 class="fw-bold">

<i class="fas fa-table text-success"></i>

Student Results

</h4>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-hover align-middle" id="resultsTable">

<thead class="table-success">

<tr>

<th>ID</th>

<th>Student</th>

<th>Subject</th>

<th>Marks</th>

<th>Percentage</th>

<th>Grade</th>

<th>Exam</th>

<th>Actions</th>

</tr>

</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<?php

$percentage = ($row['marks']/$row['total_marks'])*100;

?>

<tr>

<td>

<strong>#<?php echo $row['id']; ?></strong>

</td>

<td>

<div class="d-flex align-items-center">

<img src="https://cdn-icons-png.flaticon.com/512/4140/4140048.png"

width="55"

height="55"

class="rounded-circle border border-2 border-success me-3">

<div>

<strong>

<?php echo $row['full_name']; ?>

</strong>

<br>

<small class="text-muted">

<?php echo $row['email']; ?>

</small>

</div>

</div>

</td>

<td>

<?php echo $row['subject']; ?>

</td>

<td>

<?php echo $row['marks']; ?>

/

<?php echo $row['total_marks']; ?>

</td>

<td>

<div class="progress" style="height:10px;">

<div class="progress-bar bg-success"

style="width:<?php echo $percentage; ?>%">

</div>

</div>

<small>

<?php echo round($percentage); ?>%

</small>

</td>

<td>

<?php

switch($row['grade']){

case 'A+':

$badge='success';

break;

case 'A':

$badge='primary';

break;

case 'B':

$badge='warning';

break;

default:

$badge='secondary';

}

?>

<span class="badge bg-<?php echo $badge; ?>">

<?php echo $row['grade']; ?>

</span>

</td>

<td>

<?php echo $row['exam_type']; ?>

</td>

<td>

<a href="edit_result.php?id=<?php echo $row['id']; ?>"

class="btn btn-sm btn-primary">

<i class="fas fa-edit"></i>

</a>

<a href="delete_result.php?id=<?php echo $row['id']; ?>"

class="btn btn-sm btn-danger"

onclick="return confirm('Delete this result?')">

<i class="fas fa-trash"></i>

</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</body>

</html>