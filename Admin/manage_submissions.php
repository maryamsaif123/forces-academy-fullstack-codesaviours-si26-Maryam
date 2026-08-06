<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/*=========================================
    STATISTICS
=========================================*/

$totalSubmissions = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM submissions")
)['total'];

$totalAssignments = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM assignments")
)['total'];

$totalStudents = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM students")
)['total'];

$totalGraded = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM submissions
    WHERE status='graded'")
)['total'];

$totalPending = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM submissions
    WHERE status='submitted'")
)['total'];

/*=========================================
    FILTER
=========================================*/

$assignmentFilter = 0;

if(isset($_GET['assignment_id'])){

    $assignmentFilter=(int)$_GET['assignment_id'];

}

/*=========================================
    SEARCH
=========================================*/

$search="";

if(isset($_GET['search'])){

    $search=trim($_GET['search']);

}

/*=========================================
    PAGINATION
=========================================*/

$limit=10;

$page=isset($_GET['page'])?(int)$_GET['page']:1;

if($page<1){

$page=1;

}

$offset=($page-1)*$limit;

/*=========================================
    TOTAL ROWS
=========================================*/

$countSQL="

SELECT COUNT(*) total

FROM submissions

INNER JOIN students
ON students.id=submissions.student_id

INNER JOIN assignments
ON assignments.id=submissions.assignment_id

WHERE

students.full_name
LIKE CONCAT('%', ?, '%')

";

if($assignmentFilter>0){

$countSQL.="

AND submissions.assignment_id=?

";

}

$count=mysqli_prepare($conn,$countSQL);

if($assignmentFilter>0){

mysqli_stmt_bind_param(

$count,

"si",

$search,

$assignmentFilter

);

}else{

mysqli_stmt_bind_param(

$count,

"s",

$search

);

}

mysqli_stmt_execute($count);

$countResult=mysqli_stmt_get_result($count);

$totalRows=mysqli_fetch_assoc($countResult)['total'];

$totalPages=ceil($totalRows/$limit);

/*=========================================
    FETCH SUBMISSIONS
=========================================*/

$sql="

SELECT

submissions.*,

students.full_name,

students.roll_number,

students.email,

assignments.title

FROM submissions

INNER JOIN students
ON students.id=submissions.student_id

INNER JOIN assignments
ON assignments.id=submissions.assignment_id

WHERE

students.full_name
LIKE CONCAT('%', ?, '%')

";

if($assignmentFilter>0){

$sql.="

AND submissions.assignment_id=?

";

}

$sql.="

ORDER BY submitted_at DESC

LIMIT ?,?

";

$query=mysqli_prepare($conn,$sql);

if($assignmentFilter>0){

mysqli_stmt_bind_param(

$query,

"siii",

$search,

$assignmentFilter,

$offset,

$limit

);

}else{

mysqli_stmt_bind_param(

$query,

"sii",

$search,

$offset,

$limit

);

}

mysqli_stmt_execute($query);

$result=mysqli_stmt_get_result($query);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1">

<title>

Manage Submissions

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
rel="stylesheet">

<link
href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet">

<link
rel="stylesheet"
href="assets/css/dashboard.css">

<style>

body{
background:#f4f7fb;
font-family:'Poppins',sans-serif;
}

.page-title{
font-size:28px;
font-weight:700;
}

.stats-card{
border:none;
border-radius:18px;
box-shadow:0 10px 25px rgba(0,0,0,.08);
transition:.3s;
}

.stats-card:hover{
transform:translateY(-5px);
}

.submission-table{
border-radius:15px;
overflow:hidden;
}

</style>

</head>

<body>

<div class="wrapper">

<?php include("includes/sidebar.php"); ?>

<div class="main-content">

<?php include("includes/topbar.php"); ?>

<div class="container-fluid mt-4">
<!-- ==========================================
        PAGE HEADER
========================================== -->

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="page-title">

            <i class="fas fa-upload text-primary me-2"></i>

            Manage Submissions

        </h2>

        <p class="text-muted mb-0">

            Review, grade and manage student assignment submissions.

        </p>

    </div>

</div>

<!-- ==========================================
        WELCOME BANNER
========================================== -->

<div class="card border-0 shadow-lg rounded-4 mb-4 overflow-hidden">

<div class="card-body p-5">

<div class="row align-items-center">

<div class="col-lg-8">

<h2 class="fw-bold text-primary">

Assignment Submission Center 📄

</h2>

<p class="text-muted mt-3 mb-4">

Monitor assignment submissions, download student files, assign marks, provide feedback, and keep track of grading progress.

</p>

<a href="manage_assignments.php" class="btn btn-primary btn-lg">

<i class="fas fa-file-alt me-2"></i>

Manage Assignments

</a>

</div>

<div class="col-lg-4 text-end">

<img
src="assets/images/submission-banner.png"
class="img-fluid"
style="max-height:180px;">

</div>

</div>

</div>

</div>

<!-- ==========================================
        STATISTICS
========================================== -->

<div class="row g-4 mb-4">

<div class="col-lg-2 col-md-4">

<div class="card stats-card bg-primary text-white">

<div class="card-body">

<h6>Total</h6>

<h2><?php echo $totalSubmissions; ?></h2>

<p class="mb-0">Submissions</p>

</div>

</div>

</div>

<div class="col-lg-2 col-md-4">

<div class="card stats-card bg-success text-white">

<div class="card-body">

<h6>Graded</h6>

<h2><?php echo $totalGraded; ?></h2>

<p class="mb-0">Completed</p>

</div>

</div>

</div>

<div class="col-lg-2 col-md-4">

<div class="card stats-card bg-warning text-white">

<div class="card-body">

<h6>Pending</h6>

<h2><?php echo $totalPending; ?></h2>

<p class="mb-0">Waiting</p>

</div>

</div>

</div>

<div class="col-lg-2 col-md-4">

<div class="card stats-card bg-info text-white">

<div class="card-body">

<h6>Assignments</h6>

<h2><?php echo $totalAssignments; ?></h2>

<p class="mb-0">Available</p>

</div>

</div>

</div>

<div class="col-lg-2 col-md-4">

<div class="card stats-card bg-danger text-white">

<div class="card-body">

<h6>Students</h6>

<h2><?php echo $totalStudents; ?></h2>

<p class="mb-0">Registered</p>

</div>

</div>

</div>

</div>

<!-- ==========================================
        SEARCH & FILTER
========================================== -->

<div class="card border-0 shadow-sm rounded-4 mb-4">

<div class="card-body">

<form method="GET">

<div class="row g-3">

<div class="col-lg-5">

<input
type="text"
name="search"
class="form-control"
placeholder="Search student..."
value="<?php echo htmlspecialchars($search); ?>">

</div>

<div class="col-lg-5">

<select
name="assignment_id"
class="form-select">

<option value="0">

All Assignments

</option>

<?php

$list=mysqli_query(

$conn,

"SELECT id,title
FROM assignments
ORDER BY title ASC"

);

while($row=mysqli_fetch_assoc($list)){

?>

<option

value="<?php echo $row['id']; ?>"

<?php

if($assignmentFilter==$row['id']) echo "selected";

?>>

<?php echo htmlspecialchars($row['title']); ?>

</option>

<?php } ?>

</select>

</div>

<div class="col-lg-2 d-grid">

<button class="btn btn-primary">

<i class="fas fa-search me-2"></i>

Search

</button>

</div>

</div>

</form>

</div>

</div>

<!-- ==========================================
        SUBMISSION TABLE
========================================== -->

<div class="card border-0 shadow submission-table">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-upload me-2"></i>

Student Submissions

</h5>

</div>

<div class="card-body p-0">
<div class="table-responsive">

<table class="table table-hover table-bordered align-middle mb-0">

<thead class="table-dark">

<tr>

<th width="60">#</th>

<th>Student</th>

<th>Roll No</th>

<th>Assignment</th>

<th>Submitted File</th>

<th>Submitted On</th>

<th>Status</th>

<th>Marks</th>

<th width="220" class="text-center">

Actions

</th>

</tr>

</thead>

<tbody>

<?php

if(mysqli_num_rows($result)>0){

$serial=$offset+1;

while($submission=mysqli_fetch_assoc($result)){

?>

<tr>

<td>

<?php echo $serial++; ?>

</td>

<td>

<div class="d-flex align-items-center">

<img

src="assets/images/avatar.png"

width="45"

height="45"

class="rounded-circle border me-3">

<div>

<strong>

<?php

echo htmlspecialchars(

$submission['full_name']

);

?>

</strong>

<br>

<small class="text-muted">

<?php

echo htmlspecialchars(

$submission['email']

);

?>

</small>

</div>

</div>

</td>

<td>

<?php

echo htmlspecialchars(

$submission['roll_number']

);

?>

</td>

<td>

<strong>

<?php

echo htmlspecialchars(

$submission['title']

);

?>

</strong>

</td>

<td>

<?php

if(!empty($submission['file_path'])){

?>

<a

href="../<?php echo $submission['file_path']; ?>"

target="_blank"

class="btn btn-outline-primary btn-sm">

<i class="fas fa-download me-1"></i>

Download

</a>

<?php

}else{

?>

<span class="badge bg-secondary">

No File

</span>

<?php

}

?>

</td>

<td>

<?php

echo date(

"d M Y",

strtotime($submission['submitted_at'])

);

?>

<br>

<small class="text-muted">

<?php

echo date(

"h:i A",

strtotime($submission['submitted_at'])

);

?>

</small>

</td>

<td>

<?php

if($submission['status']=="graded"){

?>

<span class="badge bg-success">

Graded

</span>

<?php

}else{

?>

<span class="badge bg-warning text-dark">

Submitted

</span>

<?php

}

?>

</td>

<td>

<?php

if($submission['marks']==""){

?>

<span class="badge bg-secondary">

Pending

</span>

<?php

}else{

?>

<span class="badge bg-primary">

<?php echo $submission['marks']; ?>/100

</span>

<?php

}

?>

</td>

<td class="text-center">

<a

href="view_submission.php?id=<?php echo $submission['id']; ?>"

class="btn btn-info btn-sm"

title="View">

<i class="fas fa-eye"></i>

</a>

<a

href="grade_submission.php?id=<?php echo $submission['id']; ?>"

class="btn btn-success btn-sm"

title="Grade">

<i class="fas fa-star"></i>

</a>

<?php

if(!empty($submission['file_path'])){

?>

<a

href="../<?php echo $submission['file_path']; ?>"

download

class="btn btn-primary btn-sm"

title="Download">

<i class="fas fa-download"></i>

</a>

<?php

}

?>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="9">

<div class="text-center py-5">

<i class="fas fa-folder-open fa-5x text-secondary mb-4"></i>

<h3>

No Submissions Found

</h3>

<p class="text-muted">

No student submissions match your search criteria.

</p>

<a

href="manage_assignments.php"

class="btn btn-primary">

<i class="fas fa-file-alt me-2"></i>

Manage Assignments

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
<!-- ==========================================
        PAGINATION
========================================== -->

<div class="card-footer bg-white">

<div class="row align-items-center">

<div class="col-md-6">

<p class="text-muted mb-0">

Showing

<strong>

<?php echo ($totalRows==0)?0:$offset+1; ?>

</strong>

to

<strong>

<?php

$end=$offset+$limit;

if($end>$totalRows){

$end=$totalRows;

}

echo $end;

?>

</strong>

of

<strong>

<?php echo $totalRows; ?>

</strong>

submissions

</p>

</div>

<div class="col-md-6">

<nav>

<ul class="pagination justify-content-end mb-0">

<?php if($page>1){ ?>

<li class="page-item">

<a

class="page-link"

href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&assignment_id=<?php echo $assignmentFilter; ?>">

Previous

</a>

</li>

<?php } ?>

<?php

for($i=1;$i<=$totalPages;$i++){

?>

<li class="page-item <?php echo ($page==$i)?'active':''; ?>">

<a

class="page-link"

href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&assignment_id=<?php echo $assignmentFilter; ?>">

<?php echo $i; ?>

</a>

</li>

<?php

}

?>

<?php if($page<$totalPages){ ?>

<li class="page-item">

<a

class="page-link"

href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&assignment_id=<?php echo $assignmentFilter; ?>">

Next

</a>

</li>

<?php } ?>

</ul>

</nav>

</div>

</div>

</div>

<?php if(isset($_SESSION['success'])){ ?>

<div class="alert alert-success alert-dismissible fade show mt-4">

<i class="fas fa-check-circle me-2"></i>

<?php

echo $_SESSION['success'];

unset($_SESSION['success']);

?>

<button

type="button"

class="btn-close"

data-bs-dismiss="alert">

</button>

</div>

<?php } ?>

<?php if(isset($_SESSION['error'])){ ?>

<div class="alert alert-danger alert-dismissible fade show mt-4">

<i class="fas fa-circle-exclamation me-2"></i>

<?php

echo $_SESSION['error'];

unset($_SESSION['error']);

?>

<button

type="button"

class="btn-close"

data-bs-dismiss="alert">

</button>

</div>

<?php } ?>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

const searchBox=document.querySelector('input[name="search"]');

if(searchBox){

searchBox.addEventListener("keyup",function(){

let value=this.value.toLowerCase();

document.querySelectorAll("tbody tr").forEach(function(row){

row.style.display=row.innerText.toLowerCase().includes(value)

? ""

: "none";

});

});

}

</script>

<script>

setTimeout(function(){

document.querySelectorAll(".alert").forEach(function(item){

let alert=new bootstrap.Alert(item);

alert.close();

});

},4000);

</script>

</body>

</html>