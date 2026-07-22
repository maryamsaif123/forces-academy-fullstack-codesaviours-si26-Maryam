<?php
session_start();

include("../config/database.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

/*=========================
SEARCH
=========================*/

$search = "";

if(isset($_GET['search'])){
    $search = mysqli_real_escape_string($conn,$_GET['search']);
}

/*=========================
STATISTICS
=========================*/

$totalQuery = mysqli_query($conn,"
SELECT COUNT(*) total
FROM submissions
");

$totalSubmissions = mysqli_fetch_assoc($totalQuery)['total'];

$gradedQuery = mysqli_query($conn,"
SELECT COUNT(*) total
FROM submissions
WHERE status='Graded'
");

$totalGraded = mysqli_fetch_assoc($gradedQuery)['total'];

$pendingQuery = mysqli_query($conn,"
SELECT COUNT(*) total
FROM submissions
WHERE status='Pending'
");

$totalPending = mysqli_fetch_assoc($pendingQuery)['total'];

$avgQuery = mysqli_query($conn,"
SELECT AVG(marks) avgMarks
FROM submissions
WHERE marks IS NOT NULL
");

$averageMarks = mysqli_fetch_assoc($avgQuery);

if($averageMarks['avgMarks']==""){
    $averageMarks['avgMarks']=0;
}

/*=========================
LOAD SUBMISSIONS
=========================*/

$query="

SELECT

submissions.*,

students.full_name,

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

";

if($search!=""){

$query.="

WHERE

students.full_name LIKE '%$search%'

OR students.email LIKE '%$search%'

OR assignments.title LIKE '%$search%'

OR courses.course_name LIKE '%$search%'

";

}

$query.="

ORDER BY submissions.id DESC

";

$result=mysqli_query($conn,$query);
?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>
Manage Assignment Submissions
</title>

<meta name="viewport"
content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
rel="stylesheet">

<link rel="stylesheet"
href="../css/dashboard.css">

</head>

<body>

<?php include("sidebar.php"); ?>

<div class="main-content">

<?php include("navbar.php"); ?>

<div class="container-fluid py-4">

<!-- Hero -->

<div class="page-header">

<div class="d-flex justify-content-between align-items-center">

<div>

<h2>

<i class="fas fa-upload"></i>

Manage Assignment Submissions

</h2>

<p>

Review, download and grade student submissions.

</p>

</div>

</div>

</div>

<!-- Statistics -->

<div class="row mb-4">

<div class="col-lg-3">

<div class="dashboard-card blue">

<i class="fas fa-file-upload fa-2x"></i>

<h2>

<?php echo $totalSubmissions; ?>

</h2>

<p>Total Submissions</p>

</div>

</div>

<div class="col-lg-3">

<div class="dashboard-card green">

<i class="fas fa-check-circle fa-2x"></i>

<h2>

<?php echo $totalGraded; ?>

</h2>

<p>Graded</p>

</div>

</div>

<div class="col-lg-3">

<div class="dashboard-card orange">

<i class="fas fa-clock fa-2x"></i>

<h2>

<?php echo $totalPending; ?>

</h2>

<p>Pending Review</p>

</div>

</div>

<div class="col-lg-3">

<div class="dashboard-card purple">

<i class="fas fa-star fa-2x"></i>

<h2>

<?php echo number_format($averageMarks['avgMarks'],1); ?>

</h2>

<p>Average Marks</p>

</div>

</div>

</div>

<!-- Search -->

<div class="card shadow border-0 mb-4">

<div class="card-body">

<form method="GET">

<div class="row">

<div class="col-lg-10">

<input
type="text"
name="search"
class="form-control form-control-lg"
placeholder="Search by Student, Assignment or Course..."
value="<?php echo htmlspecialchars($search); ?>">

</div>

<div class="col-lg-2 d-grid">

<button
class="btn btn-success btn-lg">

<i class="fas fa-search"></i>

Search

</button>

</div>

</div>

</form>

</div>

</div>

<!-- Submission Table Starts -->

<div class="card shadow border-0">

<div class="card-header bg-success text-white">

<h4>

<i class="fas fa-list"></i>

Student Submissions

</h4>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-hover align-middle">

<thead class="table-success">

<tr>

<th>Student</th>

<th>Email</th>

<th>Course</th>

<th>Assignment</th>

<th>Submitted</th>

<th>Status</th>

<th>Marks</th>

<th>Actions</th>

</tr>

</thead>

<tbody>