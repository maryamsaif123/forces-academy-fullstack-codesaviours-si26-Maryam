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
        ADMIN DETAILS
=========================================*/

$admin_id = $_SESSION['admin_id'];

$adminQuery = mysqli_query(
    $conn,
    "SELECT * FROM admins WHERE id='$admin_id' LIMIT 1"
);

$admin = mysqli_fetch_assoc($adminQuery);

$admin_name = $admin['full_name'] ?? "Administrator";

/*=========================================
        SEARCH
=========================================*/

$search = "";

if (isset($_GET['search'])) {

    $search = mysqli_real_escape_string(
        $conn,
        trim($_GET['search'])
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
        TOTAL RECORDS
=========================================*/

$countSQL = "

SELECT COUNT(*) total

FROM teachers

WHERE

full_name LIKE '%$search%'

OR email LIKE '%$search%'

";

$countResult = mysqli_query($conn, $countSQL);

$totalRows = mysqli_fetch_assoc($countResult)['total'];

$totalPages = ceil($totalRows / $limit);

/*=========================================
        FETCH TEACHERS
=========================================*/

$sql = "

SELECT *

FROM teachers

WHERE

full_name LIKE '%$search%'

OR email LIKE '%$search%'

ORDER BY id DESC

LIMIT $offset,$limit

";

$result = mysqli_query($conn, $sql);

/*=========================================
        DASHBOARD COUNTS
=========================================*/

$totalTeachers = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM teachers")
)['total'];

$totalStudents = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM students")
)['total'];

$totalCourses = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM courses")
)['total'];

$totalAssignments = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM assignments")
)['total'];

$pageTitle = "Manage Teachers";
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1">

<title>

Manage Teachers |
Forces Academy LMS

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

<link
href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet">

<link
rel="stylesheet"
href="assets/css/dashboard.css">

<link
rel="stylesheet"
href="assets/css/manage_teachers.css">

<style>

body{
background:#f5f7fb;
font-family:'Poppins',sans-serif;
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

<?php include("includes/topbar.php"); ?>

<div class="container-fluid">

<!-- ==========================================
        PAGE HEADER
========================================== -->

<div class="page-header mb-4">

<div class="row align-items-center">

<div class="col-lg-8">

<h2 class="fw-bold">

<i class="fas fa-chalkboard-teacher text-primary me-2"></i>

Manage Teachers

</h2>

<p class="text-muted">

Manage all teachers, instructors and faculty members.

</p>

<nav>

<ol class="breadcrumb">

<li class="breadcrumb-item">

<a href="dashboard.php">

Dashboard

</a>

</li>

<li class="breadcrumb-item active">

Teachers

</li>

</ol>

</nav>

</div>



<div class="col-lg-4 text-end">

<button

class="btn btn-primary btn-lg"

data-bs-toggle="modal"

data-bs-target="#addTeacherModal">

<i class="fas fa-user-plus me-2"></i>

Add Teacher

</button>

</div>

</div>

</div>


<!-- ==========================================
        DASHBOARD CARDS
========================================== -->

<div class="row mb-4">

<div class="col-lg-3 col-md-6">

<div class="dashboard-card bg-primary text-white">

<div class="card-icon">

<i class="fas fa-chalkboard-teacher"></i>

</div>

<h6>Total Teachers</h6>

<h2>

<?php echo $totalTeachers; ?>

</h2>

<p>Faculty Members</p>

</div>

</div>



<div class="col-lg-3 col-md-6">

<div class="dashboard-card bg-success text-white">

<div class="card-icon">

<i class="fas fa-user-graduate"></i>

</div>

<h6>Total Students</h6>

<h2>

<?php echo $totalStudents; ?>

</h2>

<p>Registered Students</p>

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

<p>Available Courses</p>

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

<p>Published Assignments</p>

</div>

</div>

</div>



<!-- ==========================================
        SEARCH
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

placeholder="Search Teacher..."

value="<?php echo htmlspecialchars($search); ?>">

<button

type="submit"

class="btn btn-primary">

Search

</button>

<a

href="manage_teachers.php"

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
        TEACHER TABLE
========================================== -->

<div class="card shadow-lg border-0 rounded-4">

<div class="card-header bg-white">

<div class="d-flex justify-content-between align-items-center">

<h4>

<i class="fas fa-users text-primary me-2"></i>

Teachers List

</h4>

<div>

<button class="btn btn-success">

<i class="fas fa-file-excel"></i>

Excel

</button>

<button class="btn btn-danger">

<i class="fas fa-file-pdf"></i>

PDF

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

<th>Email</th>

<th>Department</th>

<th>Phone</th>

<th>Status</th>

<th width="170">

Actions

</th>

</tr>

</thead>

<tbody>
<?php

$sr = $offset + 1;

if(mysqli_num_rows($result) > 0){

while($teacher = mysqli_fetch_assoc($result)){

// Default Photo

$photo = "assets/images/avatar.png";

if(
isset($teacher['photo']) &&
!empty($teacher['photo']) &&
file_exists("../uploads/teachers/".$teacher['photo'])
){

$photo="../uploads/teachers/".$teacher['photo'];

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

<?php echo htmlspecialchars($teacher['full_name']); ?>

</h6>

<small class="text-muted">

Teacher ID :
<?php echo $teacher['id']; ?>

</small>

</div>

</td>

<td>

<?php echo htmlspecialchars($teacher['email']); ?>

</td>

<td>

<?php

echo !empty($teacher['department'])

? htmlspecialchars($teacher['department'])

: '<span class="text-muted">N/A</span>';

?>

</td>

<td>

<?php

echo !empty($teacher['phone'])

? htmlspecialchars($teacher['phone'])

: '<span class="text-muted">N/A</span>';

?>

</td>

<td>

<?php

$status = $teacher['status'] ?? "Active";

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

href="view_teacher.php?id=<?php echo $teacher['id']; ?>"

class="btn btn-primary btn-sm"

title="View">

<i class="fas fa-eye"></i>

</a>

<a

href="edit_teacher.php?id=<?php echo $teacher['id']; ?>"

class="btn btn-warning btn-sm"

title="Edit">

<i class="fas fa-edit"></i>

</a>

<a

href="delete_teacher.php?id=<?php echo $teacher['id']; ?>"

class="btn btn-danger btn-sm"

title="Delete"

onclick="return confirm('Delete this teacher?')">
<a href="delete_teacher.php?id=<?php echo $teacher['id']; ?>"
   class="btn btn-sm btn-danger"
   onclick="return confirm('Are you sure you want to delete this teacher?');">

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

<td colspan="8" class="text-center p-5">

<img

src="assets/images/empty.png"

width="170"

class="mb-3">

<h4>

No Teachers Found

</h4>

<p class="text-muted">

There are currently no teachers available.

</p>

<button

class="btn btn-primary"

data-bs-toggle="modal"

data-bs-target="#addTeacherModal">

<i class="fas fa-user-plus"></i>

Add First Teacher

</button>

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

teachers

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
        ADD TEACHER MODAL
========================================== -->

<div

class="modal fade"

id="addTeacherModal"

tabindex="-1"

aria-hidden="true">

<div class="modal-dialog modal-lg">

<div class="modal-content">

<form

action="insert_teacher.php"

method="POST"

enctype="multipart/form-data">

<div class="modal-header">

<h4>

<i class="fas fa-user-plus"></i>

Add Teacher

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

<label class="form-label">

Full Name

</label>

<input

type="text"

name="full_name"

class="form-control"

required>

</div>



<div class="col-md-6 mb-3">

<label class="form-label">

Email

</label>

<input

type="email"

name="email"

class="form-control"

required>

</div>



<div class="col-md-6 mb-3">

<label class="form-label">

Password

</label>

<input

type="password"

name="password"

class="form-control"

required>

</div>



<div class="col-md-6 mb-3">

<label class="form-label">

Phone

</label>

<input

type="text"

name="phone"

class="form-control">

</div>



<div class="col-md-6 mb-3">

<label class="form-label">

Gender

</label>

<select

name="gender"

class="form-select"

required>

<option value="">Select Gender</option>

<option value="Male">Male</option>

<option value="Female">Female</option>

<option value="Other">Other</option>

</select>

</div>



<div class="col-md-6 mb-3">

<label class="form-label">

Department

</label>

<input

type="text"

name="department"

class="form-control"

placeholder="Computer Science">

</div>



<div class="col-md-6 mb-3">

<label class="form-label">

Qualification

</label>

<input

type="text"

name="qualification"

class="form-control"

placeholder="MS Computer Science">

</div>



<div class="col-md-6 mb-3">

<label class="form-label">

Experience (Years)

</label>

<input

type="number"

name="experience"

class="form-control"

min="0"

value="0">

</div>



<div class="col-12 mb-3">

<label class="form-label">

Teacher Photo

</label>

<input

type="file"

name="photo"

class="form-control"

accept=".jpg,.jpeg,.png,.gif,.webp">

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

Save Teacher

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



</div>

</div>



<!-- ==========================================
        BOOTSTRAP JS
========================================== -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>



<!-- ==========================================
        IMAGE PREVIEW
========================================== -->

<script>

const teacherPhoto=document.querySelector('input[name="photo"]');

if(teacherPhoto){

teacherPhoto.addEventListener("change",function(e){

const file=e.target.files[0];

if(file){

const reader=new FileReader();

reader.onload=function(event){

let preview=document.getElementById("teacherPreview");

if(!preview){

preview=document.createElement("img");

preview.id="teacherPreview";

preview.width=130;

preview.height=130;

preview.className="rounded-circle shadow mt-3";

preview.style.objectFit="cover";

teacherPhoto.parentNode.appendChild(preview);

}

preview.src=event.target.result;

};

reader.readAsDataURL(file);

}

});

}

</script>



<!-- ==========================================
        LIVE SEARCH
========================================== -->

<script>

const search=document.querySelector('input[name="search"]');

if(search){

search.addEventListener("keyup",function(){

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

const bsAlert=new bootstrap.Alert(alert);

bsAlert.close();

});

},4000);

</script>



<!-- ==========================================
        PAGE LOADER
========================================== -->

<script>

window.addEventListener("load",function(){

const loader=document.querySelector(".loading");

if(loader){

loader.style.opacity="0";

setTimeout(function(){

loader.remove();

},600);

}

});

</script>



<!-- ==========================================
        DASHBOARD JS
========================================== -->

<script src="assets/js/dashboard.js"></script>

</body>

</html>