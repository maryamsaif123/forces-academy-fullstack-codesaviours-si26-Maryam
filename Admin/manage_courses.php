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

$totalCourses = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM courses")
)['total'];

$totalTeachers = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM teachers")
)['total'];

$totalStudents = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM students")
)['total'];

$totalAssignments = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM assignments")
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
    TOTAL RECORDS
=========================================*/

$countQuery = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total
     FROM courses
     WHERE course_name LIKE CONCAT('%', ?, '%')
     OR teacher_name LIKE CONCAT('%', ?, '%')"
);

mysqli_stmt_bind_param(
    $countQuery,
    "ss",
    $search,
    $search
);

mysqli_stmt_execute($countQuery);

$countResult = mysqli_stmt_get_result($countQuery);

$totalRows = mysqli_fetch_assoc($countResult)['total'];

$totalPages = ceil($totalRows / $limit);

/*=========================================
    FETCH COURSES
=========================================*/

$query = mysqli_prepare(
    $conn,
    "SELECT *
     FROM courses
     WHERE course_name LIKE CONCAT('%', ?, '%')
     OR teacher_name LIKE CONCAT('%', ?, '%')
     ORDER BY created_at DESC
     LIMIT ?, ?"
);

mysqli_stmt_bind_param(
    $query,
    "ssii",
    $search,
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

Manage Courses |

Forces Academy LMS

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

background:#f5f7fb;

font-family:'Poppins',sans-serif;

}

.page-title{

font-size:28px;

font-weight:700;

color:#1f2937;

}

.stats-card{

border:none;

border-radius:18px;

box-shadow:0 10px 25px rgba(0,0,0,.08);

transition:.35s;

}

.stats-card:hover{

transform:translateY(-6px);

}

.course-table{

border-radius:15px;

overflow:hidden;

}

.badge-pdf{

background:#dc3545;

}

.badge-video{

background:#0d6efd;

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

            <i class="fas fa-book-open text-primary me-2"></i>

            Manage Courses

        </h2>

        <p class="text-muted mb-0">

            Manage all courses, notes and video lectures.

        </p>

    </div>

    <button
        class="btn btn-primary px-4"
        data-bs-toggle="modal"
        data-bs-target="#addCourseModal">

        <i class="fas fa-plus-circle me-2"></i>

        Add Course

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

                    Welcome Back,

                    Administrator 👋

                </h2>

                <p class="text-muted mt-3 mb-4">

                    Manage courses, upload notes, assign teachers
                    and provide learning resources from one place.

                </p>

                <button class="btn btn-primary btn-lg">

                    <i class="fas fa-book-open me-2"></i>

                    View Courses

                </button>

            </div>

            <div class="col-lg-4 text-end">

                <img
                    src="assets/images/course-banner.png"
                    class="img-fluid"
                    style="max-height:180px;">

            </div>

        </div>

    </div>

</div>


<!-- ==========================================
        DASHBOARD CARDS
========================================== -->

<div class="row g-4 mb-4">

<div class="col-lg-3 col-md-6">

<div class="card stats-card bg-primary text-white">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Total Courses</h6>

<h2>

<?php echo $totalCourses; ?>

</h2>

<p class="mb-0">

Available Courses

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

<div class="card stats-card bg-success text-white">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Total Teachers</h6>

<h2>

<?php echo $totalTeachers; ?>

</h2>

<p class="mb-0">

Course Instructors

</p>

</div>

<div>

<i class="fas fa-chalkboard-teacher fa-3x opacity-50"></i>

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

<h6>Total Students</h6>

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

<h6>Assignments</h6>

<h2>

<?php echo $totalAssignments; ?>

</h2>

<p class="mb-0">

Published Tasks

</p>

</div>

<div>

<i class="fas fa-file-alt fa-3x opacity-50"></i>

</div>

</div>

</div>

</div>

</div>

</div>



<!-- ==========================================
        SEARCH BAR
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

placeholder="Search course name or teacher..."

value="<?php echo htmlspecialchars($search); ?>">

</div>

<div class="col-lg-2 d-grid">

<button

class="btn btn-primary">

<i class="fas fa-search me-2"></i>

Search

</button>

</div>

</div>

</form>

</div>

</div>



<!-- ==========================================
        COURSES TABLE
========================================== -->

<div class="card border-0 shadow course-table">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-book me-2"></i>

Course List

</h5>

</div>

<div class="card-body p-0">
<div class="table-responsive">

<table class="table table-hover table-bordered align-middle mb-0">

<thead class="table-dark">

<tr>

<th width="70">#</th>

<th>Course</th>

<th>Teacher</th>

<th>Description</th>

<th width="120">Notes PDF</th>

<th width="120">Video</th>

<th width="170">Created</th>

<th width="180" class="text-center">Actions</th>

</tr>

</thead>

<tbody>

<?php

if(mysqli_num_rows($result)>0){

$serial=$offset+1;

while($course=mysqli_fetch_assoc($result)){

?>

<tr>

<td>

<?php echo $serial++; ?>

</td>

<td>

<strong>

<?php echo htmlspecialchars($course['course_name']); ?>

</strong>

</td>

<td>

<i class="fas fa-user-tie text-primary me-2"></i>

<?php echo htmlspecialchars($course['teacher_name']); ?>

</td>

<td>

<?php

echo strlen($course['description'])>80

? substr($course['description'],0,80)." ..."

: $course['description'];

?>

</td>

<td>

<?php

if(!empty($course['notes_pdf'])){

?>

<a

href="../uploads/notes/<?php echo $course['notes_pdf']; ?>"

target="_blank"

class="btn btn-sm btn-danger">

<i class="fas fa-file-pdf"></i>

PDF

</a>

<?php

}else{

?>

<span class="badge bg-secondary">

No PDF

</span>

<?php

}

?>

</td>

<td>

<?php

if(!empty($course['video_link'])){

?>

<a

href="<?php echo $course['video_link']; ?>"

target="_blank"

class="btn btn-sm btn-primary">

<i class="fab fa-youtube"></i>

Watch

</a>

<?php

}else{

?>

<span class="badge bg-secondary">

No Video

</span>

<?php

}

?>

</td>

<td>

<?php

echo date(

"d M Y",

strtotime($course['created_at'])

);

?>

</td>

<td class="text-center">

<a

href="view_course.php?id=<?php echo $course['id']; ?>"

class="btn btn-info btn-sm"

title="View">

<i class="fas fa-eye"></i>

</a>

<a

href="edit_course.php?id=<?php echo $course['id']; ?>"

class="btn btn-warning btn-sm"

title="Edit">

<i class="fas fa-edit"></i>

</a>

<a href="delete_course.php?id=<?php echo $course['id']; ?>"
   class="btn btn-danger btn-sm"
   onclick="return confirm('Are you sure you want to delete this course?')">

    <i class="fas fa-trash"></i>

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

<i class="fas fa-book-open fa-4x text-secondary mb-3"></i>

<h4>

No Courses Found

</h4>

<p class="text-muted">

Click

<strong>Add Course</strong>

to create your first course.

</p>

<button

class="btn btn-primary"

data-bs-toggle="modal"

data-bs-target="#addCourseModal">

<i class="fas fa-plus-circle me-2"></i>

Add Course

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

courses

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
        ADD COURSE MODAL
========================================== -->

<div

class="modal fade"

id="addCourseModal"

tabindex="-1"

aria-hidden="true">

<div class="modal-dialog modal-xl">

<div class="modal-content">

<form

action="insert_course.php"

method="POST"

enctype="multipart/form-data">

<div class="modal-header bg-primary text-white">

<h4>

<i class="fas fa-book-open me-2"></i>

Add New Course

</h4>

<button

type="button"

class="btn-close btn-close-white"

data-bs-dismiss="modal">

</button>

</div>

<div class="modal-body">

<div class="row">

<!-- Course Name -->

<div class="col-md-6 mb-3">

<label class="form-label">

Course Name

</label>

<input

type="text"

name="course_name"

class="form-control"

placeholder="Web Development"

required>

</div>

<!-- Teacher -->

<div class="col-md-6 mb-3">

<label class="form-label">

Teacher Name

</label>

<input

type="text"

name="teacher_name"

class="form-control"

placeholder="Muhammad Ahmed"

required>

</div>

<!-- Description -->

<div class="col-12 mb-3">

<label class="form-label">

Course Description

</label>

<textarea

name="description"

rows="6"

class="form-control"

placeholder="Write course description..."

required></textarea>

</div>

<!-- Upload PDF -->

<div class="col-md-6 mb-3">

<label class="form-label">

Course Notes (PDF)

</label>

<input

type="file"

name="notes_pdf"

class="form-control"

accept=".pdf">

</div>

<!-- Video -->

<div class="col-md-6 mb-3">

<label class="form-label">

YouTube Video Link

</label>

<input

type="url"

name="video_link"

class="form-control"

placeholder="https://youtube.com/watch?v=...">

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

Save Course

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

<!-- ==========================================
        BOOTSTRAP JS
========================================== -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<!-- ==========================================
        LIVE SEARCH
========================================== -->

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



<!-- ==========================================
        AUTO CLOSE ALERT
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
        LOADING ANIMATION
========================================== -->

<script>

window.addEventListener("load",function(){

const loader=document.querySelector(".loading");

if(loader){

loader.style.opacity="0";

loader.style.transition="0.5s";

setTimeout(function(){

loader.remove();

},500);

}

});

</script>



<!-- ==========================================
        PDF FILE NAME PREVIEW
========================================== -->

<script>

const pdfInput=document.querySelector('input[name="notes_pdf"]');

if(pdfInput){

pdfInput.addEventListener("change",function(){

if(this.files.length>0){

alert("Selected PDF: " + this.files[0].name);

}

});

}

</script>



<!-- ==========================================
        YOUTUBE LINK VALIDATION
========================================== -->

<script>

const videoInput=document.querySelector('input[name="video_link"]');

if(videoInput){

videoInput.addEventListener("blur",function(){

let url=this.value.trim();

if(url!=="" &&

!url.includes("youtube.com") &&

!url.includes("youtu.be")){

alert("Please enter a valid YouTube URL.");

this.focus();

}

});

}

</script>



<!-- ==========================================
        DASHBOARD JS
========================================== -->

<script src="assets/js/dashboard.js"></script>

</body>

</html>