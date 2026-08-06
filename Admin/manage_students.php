<?php
session_start();

if(!isset($_SESSION['admin_id'])){

    header("Location: login.php");
    exit();

}

include("../config/database.php");
?>

<?php

/*=========================================
        ADMIN AUTHENTICATION
=========================================*/

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/*=========================================
        ADMIN INFORMATION
=========================================*/

$admin_id = $_SESSION['admin_id'];

$adminQuery = mysqli_query(
    $conn,
    "SELECT * FROM admins WHERE id='$admin_id' LIMIT 1"
);

$admin = mysqli_fetch_assoc($adminQuery);

$admin_name = $admin['full_name'] ?? "Administrator";

/*=========================================
        DASHBOARD COUNTS
=========================================*/

$totalStudents = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) total FROM students")
)['total'];

$totalTeachers = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) total FROM teachers")
)['total'];

$totalCourses = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) total FROM courses")
)['total'];

$totalAssignments = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) total FROM assignments")
)['total'];

/*=========================================
        SEARCH
=========================================*/

$search = "";

if (isset($_GET['search'])) {

    $search = mysqli_real_escape_string(
        $conn,
        $_GET['search']
    );

}

/*=========================================
        PAGINATION
=========================================*/

$limit = 10;

$page = isset($_GET['page'])
    ? (int)$_GET['page']
    : 1;

if ($page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $limit;

/*=========================================
        TOTAL STUDENTS
=========================================*/

$countSQL = "

SELECT COUNT(*) total

FROM students

WHERE

full_name LIKE '%$search%'

OR email LIKE '%$search%'

OR roll_number LIKE '%$search%'

";

$countResult = mysqli_query($conn, $countSQL);

$totalRows = mysqli_fetch_assoc($countResult)['total'];

$totalPages = ceil($totalRows / $limit);

/*=========================================
        STUDENT QUERY
=========================================*/

$sql = "

SELECT *

FROM students

WHERE

full_name LIKE '%$search%'

OR email LIKE '%$search%'

OR roll_number LIKE '%$search%'

ORDER BY id DESC

LIMIT $offset,$limit

";

$result = mysqli_query($conn, $sql);

/*=========================================
        PAGE TITLE
=========================================*/

$pageTitle = "Manage Students";

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

Manage Students |
Forces Academy LMS

</title>

<!-- Bootstrap -->

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<!-- Font Awesome -->

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

<!-- Google Font -->

<link
href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet">

<!-- Animate CSS -->

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<!-- AOS -->

<link
href="https://unpkg.com/aos@2.3.4/dist/aos.css"
rel="stylesheet">

<!-- Dashboard CSS -->

<link
rel="stylesheet"
href="assets/css/dashboard.css">

<!-- Student CSS -->

<link
rel="stylesheet"
href="assets/css/manage_students.css">

<style>

body{

font-family:'Poppins',sans-serif;

background:#f5f7fb;

overflow-x:hidden;

}

.loading{

position:fixed;

top:0;

left:0;

width:100%;

height:100%;

background:#fff;

display:flex;

justify-content:center;

align-items:center;

z-index:99999;

}

.spinner{

width:60px;

height:60px;

border-radius:50%;

border:6px solid #ddd;

border-top:6px solid #2563eb;

animation:spin 1s linear infinite;

}

@keyframes spin{

100%{

transform:rotate(360deg);

}

}

</style>

</head>

<body>

<div class="loading">

<div class="spinner"></div>

</div>

<div class="wrapper">
<!-- ==========================================
        SIDEBAR
========================================== -->

<?php include("includes/sidebar.php"); ?>


<!-- ==========================================
        MAIN CONTENT
========================================== -->

<div class="main-content">

<!-- ==========================================
        TOPBAR
========================================== -->

<?php include("includes/topbar.php"); ?>


<!-- ==========================================
        PAGE HEADER
========================================== -->

<div class="container-fluid">

<div class="page-header mb-4">

<div class="row align-items-center">

<div class="col-lg-8">

<h2 class="fw-bold">

<i class="fas fa-user-graduate text-primary me-2"></i>

Manage Students

</h2>

<p class="text-muted">

View, search, edit and manage all registered students.

</p>

<nav>

<ol class="breadcrumb">

<li class="breadcrumb-item">

<a href="dashboard.php">

Dashboard

</a>

</li>

<li class="breadcrumb-item active">

Students

</li>

</ol>

</nav>

</div>



<div class="col-lg-4 text-end">

<a

href="#"

class="btn btn-primary btn-lg"

data-bs-toggle="modal"

data-bs-target="#addStudentModal">

<i class="fas fa-user-plus me-2"></i>

Add Student

</a>

</div>

</div>

</div>



<!-- ==========================================
        SUMMARY CARDS
========================================== -->

<div class="row mb-4">

<div class="col-lg-3 col-md-6">

<div class="dashboard-card bg-primary text-white">

<div class="card-icon">

<i class="fas fa-user-graduate"></i>

</div>

<h6>Total Students</h6>

<h2>

<?php echo $totalStudents; ?>

</h2>

<p>

Registered Students

</p>

</div>

</div>



<div class="col-lg-3 col-md-6">

<div class="dashboard-card bg-success text-white">

<div class="card-icon">

<i class="fas fa-chalkboard-teacher"></i>

</div>

<h6>Teachers</h6>

<h2>

<?php echo $totalTeachers; ?>

</h2>

<p>

Active Faculty

</p>

</div>

</div>



<div class="col-lg-3 col-md-6">

<div class="dashboard-card bg-warning text-white">

<div class="card-icon">

<i class="fas fa-book-open"></i>

</div>

<h6>Courses</h6>

<h2>

<?php echo $totalCourses; ?>

</h2>

<p>

Available Courses

</p>

</div>

</div>



<div class="col-lg-3 col-md-6">

<div class="dashboard-card bg-danger text-white">

<div class="card-icon">

<i class="fas fa-file-alt"></i>

</div>

<h6>Assignments</h6>

<h2>

<?php echo $totalAssignments; ?>

</h2>

<p>

Published Assignments

</p>

</div>

</div>

</div>



<!-- ==========================================
        SEARCH CARD
========================================== -->

<div class="card shadow-lg border-0 rounded-4 mb-4">

<div class="card-body">

<div class="row align-items-center">

<div class="col-lg-8">

<form method="GET">

<div class="input-group">

<span class="input-group-text">

<i class="fas fa-search"></i>

</span>

<input

type="text"

name="search"

class="form-control"

placeholder="Search by Name, Email or Roll Number..."

value="<?php echo htmlspecialchars($search); ?>">

<button

class="btn btn-primary"

type="submit">

Search

</button>

<a

href="manage_students.php"

class="btn btn-secondary">

Reset

</a>

</div>

</form>

</div>



<div class="col-lg-4 text-end">

<span class="badge bg-primary p-3">

Total Records :

<?php echo $totalRows; ?>

</span>

</div>

</div>

</div>

</div>



<!-- ==========================================
        STUDENT TABLE CARD
========================================== -->

<div class="card shadow-lg border-0 rounded-4">

<div class="card-header bg-white">

<div class="d-flex justify-content-between align-items-center">

<h4>

<i class="fas fa-users text-primary me-2"></i>

Students List

</h4>

<div>

<button class="btn btn-success">

<i class="fas fa-file-excel"></i>

Export Excel

</button>

<button class="btn btn-danger">

<i class="fas fa-file-pdf"></i>

Export PDF

</button>

</div>

</div>

</div>

<div class="card-body p-0">

<div class="table-responsive">

<table class="table table-hover align-middle mb-0">

<thead class="table-light">

<tr>

<th>#</th>

<th>Photo</th>

<th>Name</th>

<th>Roll No</th>

<th>Email</th>

<th>Gender</th>

<th>Class</th>

<th>Status</th>

<th width="170">

Actions

</th>

</tr>

</thead>

<tbody>
<?php

$sr = $offset + 1;

if(mysqli_num_rows($result)>0){

while($student = mysqli_fetch_assoc($result)){

// Student Image

$photo = "assets/images/avatar.png";

if(!empty($student['photo']) && file_exists("../uploads/students/".$student['photo'])){

    $photo = "../uploads/students/".$student['photo'];

}

?>

<tr>

<td>

<strong>

<?php echo $sr++; ?>

</strong>

</td>



<td>

<img

src="<?php echo $photo; ?>"

width="55"

height="55"

class="rounded-circle shadow"

style="object-fit:cover;">

</td>



<td>

<div>

<h6 class="mb-0 fw-bold">

<?php echo htmlspecialchars($student['full_name']); ?>

</h6>

<small class="text-muted">

Student ID :

<?php echo $student['id']; ?>

</small>

</div>

</td>



<td>

<span class="badge bg-info">

<?php echo htmlspecialchars($student['roll_number']); ?>

</span>

</td>



<td>

<?php echo htmlspecialchars($student['email']); ?>

</td>



<td>

<?php echo htmlspecialchars($student['gender']); ?>

</td>



<td>

<?php echo htmlspecialchars($student['class']); ?>

</td>



<td>

<?php

$status = $student['status'] ?? 'Active';

if($status=="Active"){

echo '<span class="badge bg-success">Active</span>';

}else{

echo '<span class="badge bg-danger">Inactive</span>';

}

?>

</td>



<td>

<div class="btn-group">

<a

href="view_student.php?id=<?php echo $student['id']; ?>"

class="btn btn-sm btn-primary"

title="View">

<i class="fas fa-eye"></i>

</a>



<a

href="edit_student.php?id=<?php echo $student['id']; ?>"

class="btn btn-sm btn-warning"

title="Edit">

<i class="fas fa-edit"></i>

</a>



<a

href="delete_student.php?id=<?php echo $student['id']; ?>"

class="btn btn-sm btn-danger"

onclick="return confirm('Delete this student?')"

title="Delete">

<i class="fas fa-trash"></i>

</a>

</div>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="9" class="text-center p-5">

<img

src="assets/images/empty.png"

width="170"

class="mb-3">

<h4>

No Students Found

</h4>

<p class="text-muted">

There are no student records available.

</p>

<a

href="#"

class="btn btn-primary"

data-bs-toggle="modal"

data-bs-target="#addStudentModal">

<i class="fas fa-user-plus"></i>

Add First Student

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
<!-- ==========================================
        PAGINATION
========================================== -->

<div class="card-footer bg-white">

<div class="row align-items-center">

<div class="col-md-6">

<p class="mb-0 text-muted">

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

students

</p>

</div>



<div class="col-md-6">

<nav>

<ul class="pagination justify-content-end mb-0">

<?php

if($page>1){

?>

<li class="page-item">

<a

class="page-link"

href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>">

Previous

</a>

</li>

<?php

}

?>



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



<?php

if($page<$totalPages){

?>

<li class="page-item">

<a

class="page-link"

href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>">

Next

</a>

</li>

<?php

}

?>

</ul>

</nav>

</div>

</div>

</div>



<!-- ==========================================
        ADD STUDENT MODAL
========================================== -->

<div

class="modal fade"

id="addStudentModal"

tabindex="-1">

<div class="modal-dialog modal-lg">

<div class="modal-content">

<form

action="insert_student.php"

method="POST"

enctype="multipart/form-data">

<div class="modal-header">

<h4>

<i class="fas fa-user-plus"></i>

Add Student

</h4>

<button

type="button"

class="btn-close"

data-bs-dismiss="modal">

</button>

</div>



<div class="modal-body">

<div class="row">

<div class="col-md-6 mb-3">

<label>

Full Name

</label>

<input

type="text"

name="full_name"

class="form-control"

required>

</div>



<div class="col-md-6 mb-3">

<label>

Roll Number

</label>

<input

type="text"

name="roll_number"

class="form-control"

required>

</div>



<div class="col-md-6 mb-3">

<label>

Email

</label>

<input

type="email"

name="email"

class="form-control"

required>

</div>



<div class="col-md-6 mb-3">

<label>

Password

</label>

<input

type="password"

name="password"

class="form-control"

required>

</div>



<div class="col-md-6 mb-3">

<label>

Gender

</label>

<select

name="gender"

class="form-select"

required>

<option value="">Select</option>

<option>Male</option>

<option>Female</option>

</select>

</div>



<div class="col-md-6 mb-3">

<label>

Class

</label>

<input

type="text"

name="class"

class="form-control"

required>

</div>



<div class="col-12">

<label>

Student Photo

</label>

<input

type="file"

name="photo"

class="form-control">

</div>

</div>

</div>



<div class="modal-footer">

<button

class="btn btn-secondary"

data-bs-dismiss="modal"

type="button">

Cancel

</button>

<button

class="btn btn-primary"

type="submit">

<i class="fas fa-save"></i>

Save Student

</button>

</div>

</form>

</div>

</div>

</div>

<!-- End container -->
</div>
<!-- ==========================================
        SUCCESS / ERROR MESSAGE
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



<?php if(isset($_SESSION['error'])){ ?>

<div class="alert alert-danger alert-dismissible fade show m-4">

<i class="fas fa-times-circle me-2"></i>

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



<!-- ==========================================
        IMAGE PREVIEW
========================================== -->

<script>

const photo=document.querySelector('input[name="photo"]');

if(photo){

photo.addEventListener("change",function(e){

const file=e.target.files[0];

if(file){

const reader=new FileReader();

reader.onload=function(event){

let preview=document.getElementById("photoPreview");

if(!preview){

preview=document.createElement("img");

preview.id="photoPreview";

preview.width=120;

preview.height=120;

preview.className="rounded-circle shadow mt-3";

preview.style.objectFit="cover";

photo.parentNode.appendChild(preview);

}

preview.src=event.target.result;

}

reader.readAsDataURL(file);

}

});

}

</script>



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
        AUTO HIDE ALERT
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
        TOOLTIPS
========================================== -->

<script>

const tooltipTriggerList=[].slice.call(

document.querySelectorAll('[title]')

);

tooltipTriggerList.map(function(el){

return new bootstrap.Tooltip(el);

});

</script>



<!-- ==========================================
        LOADER
========================================== -->

<script>

window.addEventListener("load",function(){

const loader=document.querySelector(".loading");

if(loader){

loader.style.opacity="0";

setTimeout(function(){

loader.remove();

},500);

}

});

</script>



<!-- ==========================================
        AOS
========================================== -->

<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

<script>

AOS.init({

duration:800,

once:true

});

</script>



<!-- ==========================================
        BOOTSTRAP
========================================== -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



<!-- ==========================================
        DASHBOARD JS
========================================== -->

<script src="assets/js/dashboard.js"></script>

</body>

</html>