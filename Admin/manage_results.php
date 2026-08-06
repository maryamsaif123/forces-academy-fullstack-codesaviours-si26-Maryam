<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/*=========================================
    DASHBOARD STATISTICS
=========================================*/

$totalResults = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) total FROM results")
)['total'];

$totalStudents = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) total FROM students")
)['total'];

$totalCourses = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) total FROM courses")
)['total'];

$averageMarks = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT AVG(marks) avg_marks FROM results")
);

$average = round($averageMarks['avg_marks'] ?? 0, 2);

/*=========================================
    SEARCH
=========================================*/

$search = "";

if(isset($_GET['search'])){

    $search = trim($_GET['search']);

}

/*=========================================
    PAGINATION
=========================================*/

$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if($page < 1){

$page = 1;

}

$offset = ($page-1) * $limit;

/*=========================================
    TOTAL ROWS
=========================================*/

$count = mysqli_prepare(

$conn,

"

SELECT COUNT(*) total

FROM results

INNER JOIN students
ON students.id=results.student_id

LEFT JOIN courses
ON courses.id=results.course_id

WHERE students.full_name

LIKE CONCAT('%', ?, '%')

"

);

mysqli_stmt_bind_param(

$count,

"s",

$search

);

mysqli_stmt_execute($count);

$countResult = mysqli_stmt_get_result($count);

$totalRows = mysqli_fetch_assoc($countResult)['total'];

$totalPages = ceil($totalRows/$limit);

/*=========================================
    FETCH RESULTS
=========================================*/

$query = mysqli_prepare(

$conn,

"

SELECT

results.*,

students.full_name,

students.roll_number,

courses.course_name

FROM results

INNER JOIN students

ON students.id=results.student_id

LEFT JOIN courses

ON courses.id=results.course_id

WHERE students.full_name

LIKE CONCAT('%', ?, '%')

ORDER BY created_at DESC

LIMIT ?,?

"

);

mysqli_stmt_bind_param(

$query,

"sii",

$search,

$offset,

$limit

);

mysqli_stmt_execute($query);

$result = mysqli_stmt_get_result($query);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1">

<title>

Manage Results

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

font-size:30px;

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

.result-table{

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

            <i class="fas fa-chart-line text-primary me-2"></i>

            Results Management

        </h2>

        <p class="text-muted mb-0">

            Manage student academic results, grades and performance.

        </p>

    </div>

    <a href="insert_result.php" class="btn btn-primary btn-lg">

        <i class="fas fa-plus-circle me-2"></i>

        Add Result

    </a>

</div>

<!-- ==========================================
        WELCOME CARD
========================================== -->

<div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">

<div class="card-body p-5">

<div class="row align-items-center">

<div class="col-lg-8">

<h2 class="fw-bold text-primary">

Academic Results Dashboard 📊

</h2>

<p class="text-muted mt-3">

View, add, edit and analyze student examination results with automatic grading and performance tracking.

</p>

<div class="mt-4">

<a href="analytics.php" class="btn btn-success me-2">

<i class="fas fa-chart-pie me-2"></i>

Analytics

</a>

<a href="export_results.php" class="btn btn-outline-primary">

<i class="fas fa-file-excel me-2"></i>

Export Results

</a>

</div>

</div>

<div class="col-lg-4 text-end">

<img

src="assets/images/results-banner.png"

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

<div class="col-lg-3 col-md-6">

<div class="card stats-card bg-primary text-white">

<div class="card-body">

<h6>Total Results</h6>

<h2>

<?php echo $totalResults; ?>

</h2>

<p class="mb-0">

Academic Records

</p>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card stats-card bg-success text-white">

<div class="card-body">

<h6>Students</h6>

<h2>

<?php echo $totalStudents; ?>

</h2>

<p class="mb-0">

Registered Students

</p>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card stats-card bg-warning text-white">

<div class="card-body">

<h6>Courses</h6>

<h2>

<?php echo $totalCourses; ?>

</h2>

<p class="mb-0">

Available Courses

</p>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card stats-card bg-danger text-white">

<div class="card-body">

<h6>Average Marks</h6>

<h2>

<?php echo $average; ?>

%

</h2>

<p class="mb-0">

Overall Performance

</p>

</div>

</div>

</div>

</div>

<!-- ==========================================
        SEARCH
========================================== -->

<div class="card border-0 shadow-sm rounded-4 mb-4">

<div class="card-body">

<form method="GET">

<div class="row">

<div class="col-lg-10">

<input

type="text"

name="search"

class="form-control"

placeholder="Search by student name..."

value="<?php echo htmlspecialchars($search); ?>">

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
        RESULTS TABLE
========================================== -->

<div class="card border-0 shadow result-table">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-table me-2"></i>

Student Results

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

<th>Course</th>

<th>Subject</th>

<th>Marks</th>

<th>Percentage</th>

<th>Grade</th>

<th>Exam Type</th>

<th width="220" class="text-center">

Actions

</th>

</tr>

</thead>

<tbody>

<?php

if(mysqli_num_rows($result)>0){

$serial=$offset+1;

while($row=mysqli_fetch_assoc($result)){

$percentage=0;

if($row['total_marks']>0){

$percentage=round(($row['marks']/$row['total_marks'])*100,2);

}

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

<?php echo htmlspecialchars($row['full_name']); ?>

</strong>

</div>

</div>

</td>

<td>

<?php echo htmlspecialchars($row['roll_number']); ?>

</td>

<td>

<?php

echo htmlspecialchars(

$row['course_name'] ?? "N/A"

);

?>

</td>

<td>

<?php echo htmlspecialchars($row['subject']); ?>

</td>

<td>

<strong>

<?php echo $row['marks']; ?>

/

<?php echo $row['total_marks']; ?>

</strong>

</td>

<td>

<span class="badge bg-info">

<?php echo $percentage; ?>%

</span>

</td>

<td>

<?php

$grade=strtoupper(trim($row['grade']));

switch($grade){

case "A+":

echo '<span class="badge bg-success">A+</span>';

break;

case "A":

echo '<span class="badge bg-primary">A</span>';

break;

case "B":

echo '<span class="badge bg-info text-dark">B</span>';

break;

case "C":

echo '<span class="badge bg-warning text-dark">C</span>';

break;

case "D":

echo '<span class="badge bg-secondary">D</span>';

break;

default:

echo '<span class="badge bg-danger">F</span>';

}

?>

</td>

<td>

<span class="badge bg-dark">

<?php echo htmlspecialchars($row['exam_type']); ?>

</span>

</td>

<td class="text-center">

<a

href="view_result.php?id=<?php echo $row['id']; ?>"

class="btn btn-info btn-sm"

title="View">

<i class="fas fa-eye"></i>

</a>

<a

href="edit_result.php?id=<?php echo $row['id']; ?>"

class="btn btn-warning btn-sm"

title="Edit">

<i class="fas fa-edit"></i>

</a>

<a

href="print_result.php?id=<?php echo $row['id']; ?>"

class="btn btn-success btn-sm"

title="Print"

target="_blank">

<i class="fas fa-print"></i>

</a>

<a

href="delete_result.php?id=<?php echo $row['id']; ?>"

class="btn btn-danger btn-sm"

title="Delete"

onclick="return confirm('Delete this result?');">

<i class="fas fa-trash"></i>

</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="10">

<div class="text-center py-5">

<i class="fas fa-chart-bar fa-5x text-secondary mb-4"></i>

<h3>

No Results Found

</h3>

<p class="text-muted">

No student results are available.

</p>

<a

href="insert_result.php"

class="btn btn-primary">

<i class="fas fa-plus-circle me-2"></i>

Add First Result

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

results

</p>

</div>

<div class="col-md-6">

<nav>

<ul class="pagination justify-content-end mb-0">

<?php if($page>1){ ?>

<li class="page-item">

<a

class="page-link"

href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>">

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

href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">

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

href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>">

Next

</a>

</li>

<?php } ?>

</ul>

</nav>

</div>

</div>

</div>

<!-- ==========================================
        SUCCESS MESSAGE
========================================== -->

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

<!-- ==========================================
        ERROR MESSAGE
========================================== -->

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

<!-- ==========================================
        JAVASCRIPT
========================================== -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

// Live Search

const searchInput=document.querySelector('input[name="search"]');

if(searchInput){

searchInput.addEventListener("keyup",function(){

let value=this.value.toLowerCase();

document.querySelectorAll("tbody tr").forEach(function(row){

row.style.display=row.innerText.toLowerCase().includes(value)

? ""

: "none";

});

});

}

// Auto Close Alerts

setTimeout(function(){

document.querySelectorAll(".alert").forEach(function(item){

let alert=new bootstrap.Alert(item);

alert.close();

});

},4000);

</script>

</body>

</html>