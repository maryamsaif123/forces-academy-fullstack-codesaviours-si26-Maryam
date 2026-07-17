<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/* ===============================
   Statistics
================================ */

$totalSubmissions = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM submissions
"));

$gradedSubmissions = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM submissions
WHERE status='graded'
"));

$pendingSubmissions = mysqli_num_rows(mysqli_query($conn,"
SELECT * FROM submissions
WHERE status='submitted'
"));

/* ===============================
   Fetch Data
================================
 */
$result = mysqli_query($conn,"
SELECT
submissions.*,
students.name,
students.email,
assignments.title,
courses.course_name
FROM submissions

LEFT JOIN students
ON submissions.student_id=students.id

LEFT JOIN assignments
ON submissions.assignment_id=assignments.id

LEFT JOIN courses
ON assignments.course_id=courses.id

ORDER BY submissions.submitted_at DESC
");
?>
<?php include("sidebar.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

/* ===========================
   LIVE SEARCH
=========================== */

document.getElementById("searchInput").addEventListener("keyup", function(){

let value=this.value.toLowerCase();

let rows=document.querySelectorAll("#submissionTable tbody tr");

rows.forEach(function(row){

row.style.display=row.innerText.toLowerCase().includes(value)
? ""
: "none";

});

});


/* ===========================
   SUBMISSION ANALYTICS
=========================== */

const submissionChart=document.getElementById("submissionChart");

if(submissionChart){

new Chart(submissionChart,{

type:'doughnut',

data:{

labels:['Graded','Pending'],

datasets:[{

data:[

<?php echo $gradedSubmissions; ?>,

<?php echo $pendingSubmissions; ?>

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
<div class="main-content">

<?php include("navbar.php"); ?>

<div class="container-fluid">

<div class="page-header">

<div class="d-flex justify-content-between align-items-center">

<div>

<h2>

<i class="fas fa-upload"></i>

Manage Assignment Submissions

</h2>

<p>

Review, Download and Grade Student Assignments

</p>

</div>

</div>

</div>
<div class="row mb-4">

<div class="col-md-4">

<div class="summary-card blue">

<h6>Total Submissions</h6>

<h2><?php echo $totalSubmissions; ?></h2>

</div>

</div>

<div class="col-md-4">

<div class="summary-card green">

<h6>Graded</h6>

<h2><?php echo $gradedSubmissions; ?></h2>

</div>

</div>

<div class="col-md-4">

<div class="summary-card orange">

<h6>Pending</h6>

<h2><?php echo $pendingSubmissions; ?></h2>

</div>

</div>

</div>
<div class="card shadow-lg border-0 rounded-4 mb-4">

<div class="card-body">

<input
type="text"
id="searchInput"
class="form-control form-control-lg"
placeholder="Search Student, Assignment or Course">

</div>

</div>

<div class="card border-0 shadow-lg rounded-4">

<div class="card-header bg-white border-0">

<h4 class="fw-bold">

<i class="fas fa-upload text-success"></i>

Student Assignment Submissions

</h4>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-hover align-middle" id="submissionTable">

<thead class="table-success">

<tr>

<th>Student</th>

<th>Assignment</th>

<th>Course</th>

<th>Submitted</th>

<th>File</th>

<th>Status</th>

<th width="250">Actions</th>

</tr>

</thead>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

/* ===========================
   LIVE SEARCH
=========================== */

document.getElementById("searchInput").addEventListener("keyup", function(){

let value=this.value.toLowerCase();

let rows=document.querySelectorAll("#submissionTable tbody tr");

rows.forEach(function(row){

row.style.display=row.innerText.toLowerCase().includes(value)
? ""
: "none";

});

});


/* ===========================
   SUBMISSION ANALYTICS
=========================== */

const submissionChart=document.getElementById("submissionChart");

if(submissionChart){

new Chart(submissionChart,{

type:'doughnut',

data:{

labels:['Graded','Pending'],

datasets:[{

data:[

<?php echo $gradedSubmissions; ?>,

<?php echo $pendingSubmissions; ?>

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

<tbody>

<?php

if(mysqli_num_rows($result)>0){

while($row=mysqli_fetch_assoc($result)){

?>

<tr>

<td>

<div class="d-flex align-items-center">

<img

src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"

width="55"

height="55"

class="rounded-circle border border-3 border-success me-3">

<div>

<h6 class="mb-0">

<?php echo htmlspecialchars($row['name']); ?>

</h6>

<small class="text-muted">

<?php echo htmlspecialchars($row['email']); ?>

</small>

</div>

</div>

</td>

<td>

<strong>

<?php echo htmlspecialchars($row['title']); ?>

</strong>

</td>

<td>

<span class="badge bg-primary">

<?php echo htmlspecialchars($row['course_name']); ?>

</span>

</td>

<td>

<?php

echo date(

"d M Y",

strtotime($row['submitted_at'])

);

?>

</td>

<td>

<a

href="../uploads/<?php echo urlencode($row['file_path']); ?>"

class="btn btn-info btn-sm"

download>

<i class="fas fa-download"></i>

Download

</a>

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

<a

href="grade_submission.php?id=<?php echo $row['id']; ?>"

class="btn btn-success btn-sm">

<i class="fas fa-star"></i>

Grade

</a>

<a

href="view_submission.php?id=<?php echo $row['id']; ?>"

class="btn btn-primary btn-sm">

<i class="fas fa-eye"></i>

View

</a>

<a

href="delete_submission.php?id=<?php echo $row['id']; ?>"

class="btn btn-danger btn-sm"

onclick="return confirm('Delete this submission?')">

<i class="fas fa-trash"></i>

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

src="https://cdn-icons-png.flaticon.com/512/4076/4076478.png"

width="120"

class="mb-3">

<h3>

No Submissions Found

</h3>

<p class="text-muted">

Students haven't submitted any assignments yet.

</p>

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

Submission Analytics

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

Assignment Submissions

</h6>

<hr>

<p>

<strong>

<?php echo $gradedSubmissions; ?>

</strong>

Graded

</p>

<p>

<strong>

<?php echo $pendingSubmissions; ?>

</strong>

Pending Review

</p>

</div>

</div>

</div>

</div>