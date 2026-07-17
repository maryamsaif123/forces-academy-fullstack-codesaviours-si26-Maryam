<?php
session_start();

if(!isset($_SESSION['student_id'])){
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$student_id = $_SESSION['student_id'];

/* ==========================
   Statistics
========================== */

$totalSubmissions = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM submissions
WHERE student_id='$student_id'
"));

$graded = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM submissions
WHERE student_id='$student_id'
AND status='graded'
"));

$pending = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM submissions
WHERE student_id='$student_id'
AND status='submitted'
"));

/* ==========================
   Fetch Submissions
========================== */

$result = mysqli_query($conn,"
SELECT
submissions.*,
assignments.title,
courses.course_name,
results.marks,
results.total_marks,
results.grade,
results.remarks

FROM submissions

LEFT JOIN assignments
ON submissions.assignment_id=assignments.id

LEFT JOIN courses
ON assignments.course_id=courses.id

LEFT JOIN results
ON results.student_id=submissions.student_id
AND results.subject=assignments.title

WHERE submissions.student_id='$student_id'

ORDER BY submissions.submitted_at DESC
");

?>

<?php include("sidebar.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

/* ==========================
   LIVE SEARCH
========================== */

document.getElementById("searchInput").addEventListener("keyup",function(){

let value=this.value.toLowerCase();

let rows=document.querySelectorAll("#submissionTable tbody tr");

rows.forEach(function(row){

row.style.display=row.innerText.toLowerCase().includes(value)

? ""

: "none";

});

});


/* ==========================
   CHART
========================== */

const chart=document.getElementById("submissionChart");

if(chart){

new Chart(chart,{

type:'doughnut',

data:{

labels:['Graded','Pending'],

datasets:[{

data:[

<?php echo $graded; ?>,

<?php echo $pending; ?>

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

<div class="main-content">

<?php include("navbar.php"); ?>

<div class="container-fluid">

<div class="page-header">

<div class="d-flex justify-content-between align-items-center">

<div>

<h2>

📤 My Submissions

</h2>

<p>

Track your submitted assignments and grades

</p>

</div>

</div>

</div>
<div class="row mb-4">

<div class="col-md-4">

<div class="summary-card blue">

<h6>Total Submitted</h6>

<h2>

<?php echo $totalSubmissions; ?>

</h2>

</div>

</div>

<div class="col-md-4">

<div class="summary-card green">

<h6>Graded</h6>

<h2>

<?php echo $graded; ?>

</h2>

</div>

</div>

<div class="col-md-4">

<div class="summary-card orange">

<h6>Pending Review</h6>

<h2>

<?php echo $pending; ?>

</h2>

</div>

</div>

</div>

<div class="card shadow-lg border-0 rounded-4 mb-4">

<div class="card-body">

<input
type="text"
id="searchInput"
class="form-control form-control-lg"
placeholder="Search Assignment">

</div>

</div>

<!-- ============================
     My Submissions Table
============================ -->

<div class="card border-0 shadow-lg rounded-4">

<div class="card-header bg-success text-white">

<h4>

<i class="fas fa-file-upload"></i>

My Assignment Submissions

</h4>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-hover align-middle" id="submissionTable">

<thead class="table-success">

<tr>

<th>Assignment</th>

<th>Course</th>

<th>Submitted On</th>

<th>Status</th>

<th>Marks</th>

<th>Grade</th>

<th>Feedback</th>

<th width="220">Actions</th>

</tr>

</thead>

<tbody>

<?php

if(mysqli_num_rows($result)>0){

while($row=mysqli_fetch_assoc($result)){

?>

<tr>

<td>

<div class="d-flex align-items-center">

<img

src="https://cdn-icons-png.flaticon.com/512/3976/3976626.png"

width="55"

height="55"

class="rounded-circle border border-3 border-success me-3">

<div>

<h6 class="mb-1">

<?php echo htmlspecialchars($row['title']); ?>

</h6>

<small class="text-muted">

Assignment Submission

</small>

</div>

</div>

</td>

<td>

<span class="badge bg-primary">

<?php echo htmlspecialchars($row['course_name']); ?>

</span>

</td>

<td>

<?php echo date("d M Y",strtotime($row['submitted_at'])); ?>

</td>

<td>

<?php

if($row['status']=="graded"){

?>

<span class="badge bg-success">

Graded

</span>

<?php

}else{

?>

<span class="badge bg-warning text-dark">

Pending

</span>

<?php

}

?>

</td>

<td>

<?php

if(!empty($row['marks'])){

echo $row['marks']." / ".$row['total_marks'];

}else{

echo "-";

}

?>

</td>

<td>

<?php

if(!empty($row['grade'])){

?>

<span class="badge bg-info">

<?php echo $row['grade']; ?>

</span>

<?php

}else{

echo "-";

}

?>

</td>

<td>

<?php

if(!empty($row['remarks'])){

echo substr(htmlspecialchars($row['remarks']),0,40)." ...";

}else{

?>

<span class="text-muted">

No Feedback

</span>

<?php

}

?>

</td>

<td>

<a

href="../uploads/<?php echo urlencode($row['file_path']); ?>"

class="btn btn-primary btn-sm"

download>

<i class="fas fa-download"></i>

Download

</a>

<a

href="view_result.php?id=<?php echo $row['assignment_id']; ?>"

class="btn btn-success btn-sm">

<i class="fas fa-eye"></i>

View Result

</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="8">

<div class="text-center py-5">

<img

src="https://cdn-icons-png.flaticon.com/512/4076/4076478.png"

width="140"

class="mb-3">

<h3>

No Submissions Found

</h3>

<p class="text-muted">

You haven't submitted any assignments yet.

</p>

<a

href="assignments.php"

class="btn btn-success btn-lg">

<i class="fas fa-book-open"></i>

View Assignments

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

<h5>

<i class="fas fa-chart-pie text-success"></i>

Submission Progress

</h5>

</div>

<div class="card-body">

<canvas id="submissionChart" height="120"></canvas>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card border-0 shadow-lg rounded-4">

<div class="card-body text-center">

<i class="fas fa-file-upload fa-4x text-success mb-3"></i>

<h2>

<?php echo $totalSubmissions; ?>

</h2>

<h6 class="text-muted">

Submitted Assignments

</h6>

<hr>

<p>

<strong>

<?php echo $graded; ?>

</strong>

Graded

</p>

<p>

<strong>

<?php echo $pending; ?>

</strong>

Pending Review

</p>

</div>

</div>

</div>

</div>