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

$totalAssignments = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM assignments")
)['total'];

$totalCourses = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM courses")
)['total'];

$totalStudents = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM students")
)['total'];

$totalSubmissions = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM submissions")
)['total'];

/*=========================================
    SEARCH
=========================================*/

$search = "";

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

/*=========================================
    PAGINATION
=========================================*/

$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $limit;

/*=========================================
    TOTAL ROWS
=========================================*/

$count = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total
     FROM assignments
     WHERE title LIKE CONCAT('%', ?, '%')"
);

mysqli_stmt_bind_param($count, "s", $search);

mysqli_stmt_execute($count);

$countResult = mysqli_stmt_get_result($count);

$totalRows = mysqli_fetch_assoc($countResult)['total'];

$totalPages = ceil($totalRows / $limit);

/*=========================================
    FETCH ASSIGNMENTS
=========================================*/

$query = mysqli_prepare(
$conn,
"SELECT
assignments.*,
courses.course_name

FROM assignments

LEFT JOIN courses
ON assignments.course_id=courses.id

WHERE assignments.title
LIKE CONCAT('%', ?, '%')

ORDER BY deadline ASC

LIMIT ?, ?"
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
content="width=device-width, initial-scale=1.0">

<title>

Manage Assignments

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

.assignment-table{
border-radius:15px;
overflow:hidden;
}
/* =========================================
   SIDEBAR
========================================= */

.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 270px;
    height: 100vh;
    background: #14263d;
    color: #fff;
    z-index: 1000;
    overflow-y: auto;
    overflow-x: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 5px 0 20px rgba(0, 0, 0, 0.08);
}

/* Logo Area */

.logo-area {
    text-align: center;
    padding: 25px 15px 20px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}

.logo-area .logo {
    width: 90px;
    height: 90px;
    object-fit: contain;
    display: block;
    margin: 0 auto 12px;
    border-radius: 50%;
    background: #fff;
    padding: 5px;
}

.logo-area h3 {
    color: #fff;
    font-size: 19px;
    font-weight: 700;
    margin: 5px 0;
}

.logo-area span {
    color: #93b4df;
    font-size: 12px;
}

/* Sidebar Menu */

.sidebar-menu {
    list-style: none;
    padding: 18px 12px;
    margin: 0;
}

.sidebar-menu li {
    margin-bottom: 6px;
}

.sidebar-menu li a {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 14px;
    color: #dbe7f5;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.sidebar-menu li a i {
    width: 20px;
    text-align: center;
    font-size: 15px;
}

/* Hover */

.sidebar-menu li a:hover {
    background: rgba(37, 99, 235, 0.25);
    color: #fff;
    transform: translateX(3px);
}

/* Active */

.sidebar-menu li.active a {
    background: #2563eb;
    color: #fff;
    box-shadow: 0 6px 18px rgba(37, 99, 235, 0.30);
}

/* Logout */

.logout-area {
    margin-top: auto;
    padding: 15px;
}

.logout-area .logout-btn,
.logout-area .btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 11px;
    border-radius: 10px;
    font-weight: 600;
    border: none;
}

/* Sidebar Scrollbar */

.sidebar::-webkit-scrollbar {
    width: 5px;
}

.sidebar::-webkit-scrollbar-thumb {
    background: #2563eb;
    border-radius: 10px;
}

.sidebar::-webkit-scrollbar-track {
    background: transparent;
}


</style>

</head>

<body>
<?php include("includes/sidebar.php"); ?>
<div class="wrapper">
<div class="main-content">

<?php include("includes/topbar.php"); ?>

<div class="container-fluid mt-4">
<!-- ==========================================
        PAGE HEADER
========================================== -->

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="page-title">

            <i class="fas fa-file-alt text-primary me-2"></i>

            Manage Assignments

        </h2>

        <p class="text-muted mb-0">

            Create, edit and manage assignments for all courses.

        </p>

    </div>

    <button
        class="btn btn-primary px-4"
        data-bs-toggle="modal"
        data-bs-target="#addAssignmentModal">

        <i class="fas fa-plus-circle me-2"></i>

        Add Assignment

    </button>

</div>

<!-- ==========================================
        WELCOME BANNER
========================================== -->

<div class="card border-0 shadow-lg rounded-4 mb-4 overflow-hidden">

<div class="card-body p-5">

<div class="row align-items-center">

<div class="col-lg-8">

<h2 class="fw-bold text-primary">

Assignment Management 📝

</h2>

<p class="text-muted mt-3 mb-4">

Create assignments, assign them to courses, set deadlines and monitor student submissions.

</p>

<button class="btn btn-primary btn-lg">

<i class="fas fa-book-open me-2"></i>

Manage Assignments

</button>

</div>

<div class="col-lg-4 text-end">

<img
src="assets/images/assignment-banner.png"
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

<div class="d-flex justify-content-between">

<div>

<h6>Total Assignments</h6>

<h2>

<?php echo $totalAssignments; ?>

</h2>

<p class="mb-0">

Available Assignments

</p>

</div>

<div>

<i class="fas fa-file-alt fa-3x opacity-50"></i>

</div>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card stats-card bg-success text-white">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Courses</h6>

<h2>

<?php echo $totalCourses; ?>

</h2>

<p class="mb-0">

Active Courses

</p>

</div>

<div>

<i class="fas fa-book fa-3x opacity-50"></i>

</div>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card stats-card bg-warning text-white">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Students</h6>

<h2>

<?php echo $totalStudents; ?>

</h2>

<p class="mb-0">

Registered Students

</p>

</div>

<div>

<i class="fas fa-user-graduate fa-3x opacity-50"></i>

</div>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card stats-card bg-danger text-white">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Submissions</h6>

<h2>

<?php echo $totalSubmissions; ?>

</h2>

<p class="mb-0">

Received Files

</p>

</div>

<div>

<i class="fas fa-upload fa-3x opacity-50"></i>

</div>

</div>

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
placeholder="Search assignment..."
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
        ASSIGNMENT TABLE
========================================== -->

<div class="card border-0 shadow assignment-table">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-list-check me-2"></i>

Assignment List

</h5>

</div>

<div class="card-body p-0">
<div class="table-responsive">

<table class="table table-hover table-bordered align-middle mb-0">

<thead class="table-dark">

<tr>

<th width="70">#</th>

<th>Course</th>

<th>Assignment Title</th>

<th>Description</th>

<th width="140">Deadline</th>

<th width="140">Status</th>

<th width="180" class="text-center">Actions</th>

</tr>

</thead>

<tbody>

<?php

if(mysqli_num_rows($result)>0){

$serial=$offset+1;

while($assignment=mysqli_fetch_assoc($result)){

$today=date("Y-m-d");

$deadline=$assignment['deadline'];

$remaining=(strtotime($deadline)-strtotime($today))/86400;

?>

<tr>

<td>

<?php echo $serial++; ?>

</td>

<td>

<span class="badge bg-primary">

<?php

echo htmlspecialchars(

$assignment['course_name']

);

?>

</span>

</td>

<td>

<strong>

<?php

echo htmlspecialchars(

$assignment['title']

);

?>

</strong>

</td>

<td>

<?php

$text=strip_tags($assignment['description']);

echo strlen($text)>80

?

htmlspecialchars(substr($text,0,80))."..."

:

htmlspecialchars($text);

?>

</td>

<td>

<?php

echo date(

"d M Y",

strtotime($assignment['deadline'])

);

?>

</td>

<td>

<?php

if($remaining<0){

?>

<span class="badge bg-danger">

Expired

</span>

<?php

}elseif($remaining==0){

?>

<span class="badge bg-warning">

Due Today

</span>

<?php

}else{

?>

<span class="badge bg-success">

<?php echo (int)$remaining; ?>

Days Left

</span>

<?php

}

?>

</td>

<td class="text-center">

<a

href="view_assignment.php?id=<?php echo $assignment['id']; ?>"

class="btn btn-info btn-sm"

title="View">

<i class="fas fa-eye"></i>

</a>

<a

href="edit_assignment.php?id=<?php echo $assignment['id']; ?>"

class="btn btn-warning btn-sm"

title="Edit">

<i class="fas fa-edit"></i>

</a>

<a href="delete_assignment.php?id=<?php echo $assignment['id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Are you sure you want to delete this assignment?');">

<i class="fas fa-trash"></i>

</a>

<a

href="manage_submissions.php?assignment_id=<?php echo $assignment['id']; ?>"

class="btn btn-success btn-sm"

title="View Submissions">

<i class="fas fa-upload"></i>

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

<i class="fas fa-file-circle-xmark fa-5x text-secondary mb-3"></i>

<h4>

No Assignments Found

</h4>

<p class="text-muted">

There are currently no assignments available.

Create your first assignment to get started.

</p>

<button

class="btn btn-primary"

data-bs-toggle="modal"

data-bs-target="#addAssignmentModal">

<i class="fas fa-plus-circle me-2"></i>

Create Assignment

</button>

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

assignments

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
        ADD ASSIGNMENT MODAL
========================================== -->

<div

class="modal fade"

id="addAssignmentModal"

tabindex="-1"

aria-hidden="true">

<div class="modal-dialog modal-xl">

<div class="modal-content">

<form

action="insert_assignment.php"

method="POST">

<div class="modal-header bg-primary text-white">

<h4>

<i class="fas fa-plus-circle me-2"></i>

Create Assignment

</h4>

<button

type="button"

class="btn-close btn-close-white"

data-bs-dismiss="modal">

</button>

</div>

<div class="modal-body">

<div class="row">

<!-- Course -->

<div class="col-md-6 mb-3">

<label class="form-label">

Course

</label>

<select

name="course_id"

class="form-select"

required>

<option value="">

Choose Course

</option>

<?php

$courses=mysqli_query(

$conn,

"SELECT id,course_name
FROM courses
ORDER BY course_name ASC"

);

while($course=mysqli_fetch_assoc($courses)){

?>

<option value="<?php echo $course['id']; ?>">

<?php echo htmlspecialchars($course['course_name']); ?>

</option>

<?php

}

?>

</select>

</div>

<!-- Deadline -->

<div class="col-md-6 mb-3">

<label class="form-label">

Deadline

</label>

<input

type="date"

name="deadline"

class="form-control"

required>

</div>

<!-- Assignment Title -->

<div class="col-12 mb-3">

<label class="form-label">

Assignment Title

</label>

<input

type="text"

name="title"

class="form-control"

placeholder="Enter assignment title"

required>

</div>

<!-- Description -->

<div class="col-12 mb-3">

<label class="form-label">

Assignment Description

</label>

<textarea

name="description"

rows="7"

class="form-control"

placeholder="Enter assignment instructions..."

required></textarea>

</div>

<div class="col-md-6">

<label class="form-label">

Created By

</label>

<input

type="text"

class="form-control"

value="<?php echo isset($_SESSION['admin_username']) ? htmlspecialchars($_SESSION['admin_username']) : 'Administrator'; ?>"

readonly>

</div>

<div class="col-md-6">

<label class="form-label">

Today's Date

</label>

<input

type="text"

class="form-control"

value="<?php echo date('d M Y'); ?>"

readonly>

</div>

</div>

</div>

<div class="modal-footer">

<button

type="button"

class="btn btn-secondary"

data-bs-dismiss="modal">

Cancel

</button>

<button

type="submit"

class="btn btn-primary">

<i class="fas fa-save me-2"></i>

Save Assignment

</button>

</div>

</form>

</div>

</div>

</div>
<!-- ==========================================
        SUCCESS MESSAGE
========================================== -->

<?php if(isset($_SESSION['success'])){ ?>

<div class="alert alert-success alert-dismissible fade show m-4">

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

<div class="alert alert-danger alert-dismissible fade show m-4">

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

<!-- Bootstrap -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- ==========================================
        LIVE SEARCH
========================================== -->

<script>

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

</script>

<!-- ==========================================
        AUTO HIDE ALERTS
========================================== -->

<script>

setTimeout(function(){

document.querySelectorAll(".alert").forEach(function(alert){

let bsAlert=new bootstrap.Alert(alert);

bsAlert.close();

});

},4000);

</script>

<!-- ==========================================
        CHARACTER COUNTER
========================================== -->

<script>

const textarea=document.querySelector('textarea[name="description"]');

if(textarea){

const counter=document.createElement("small");

counter.className="text-muted d-block mt-2";

textarea.parentNode.appendChild(counter);

function updateCounter(){

counter.innerHTML=textarea.value.length+" characters";

}

textarea.addEventListener("keyup",updateCounter);

updateCounter();

}

</script>

<!-- ==========================================
        FORM VALIDATION
========================================== -->

<script>

const form=document.querySelector("#addAssignmentModal form");

if(form){

form.addEventListener("submit",function(e){

let course=document.querySelector('select[name="course_id"]').value;

let title=document.querySelector('input[name="title"]').value.trim();

let description=document.querySelector('textarea[name="description"]').value.trim();

let deadline=document.querySelector('input[name="deadline"]').value;

if(course=="" || title=="" || description=="" || deadline==""){

alert("Please complete all required fields.");

e.preventDefault();

return;

}

let today=new Date();

today.setHours(0,0,0,0);

let selected=new Date(deadline);

if(selected<today){

alert("Deadline cannot be earlier than today.");

e.preventDefault();

}

});

}

</script>

<script src="assets/js/dashboard.js"></script>

</body>

</html>