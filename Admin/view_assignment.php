<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/*=========================================
    VALIDATE ID
=========================================*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid Assignment ID.";

    header("Location: manage_assignments.php");
    exit();
}

$id = (int)$_GET['id'];

/*=========================================
    FETCH ASSIGNMENT
=========================================*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        assignments.*,
        courses.course_name
     FROM assignments

     LEFT JOIN courses
     ON assignments.course_id = courses.id

     WHERE assignments.id=?
     LIMIT 1"
);

mysqli_stmt_bind_param($stmt,"i",$id);
mysqli_stmt_execute($stmt);

$result=mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)==0){

    $_SESSION['error']="Assignment not found.";

    header("Location: manage_assignments.php");
    exit();
}

$assignment=mysqli_fetch_assoc($result);

/*=========================================
    SUBMISSION STATISTICS
=========================================*/

$total=mysqli_fetch_assoc(

mysqli_query(

$conn,

"SELECT COUNT(*) total
FROM submissions
WHERE assignment_id=".$id

)

)['total'];

$graded=mysqli_fetch_assoc(

mysqli_query(

$conn,

"SELECT COUNT(*) total
FROM submissions
WHERE assignment_id=".$id."
AND status='graded'"

)

)['total'];

$pending=$total-$graded;

$daysLeft=(strtotime($assignment['deadline'])-strtotime(date("Y-m-d")))/86400;
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>

View Assignment

</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

body{
background:#f4f7fb;
font-family:'Poppins',sans-serif;
}

.assignment-card{
border:none;
border-radius:20px;
overflow:hidden;
box-shadow:0 15px 40px rgba(0,0,0,.08);
}

.assignment-header{
background:linear-gradient(135deg,#0d6efd,#20c997);
color:#fff;
padding:45px;
}

.info-card{
background:#fff;
border-radius:15px;
padding:20px;
box-shadow:0 5px 20px rgba(0,0,0,.06);
height:100%;
}

.assignment-text{
line-height:1.9;
font-size:16px;
}

.stat-box{
padding:20px;
border-radius:15px;
text-align:center;
color:#fff;
}

</style>

</head>

<body>

<div class="container py-5">

<div class="card assignment-card">

<div class="assignment-header">

<h2>

<i class="fas fa-file-alt me-2"></i>

<?php echo htmlspecialchars($assignment['title']); ?>

</h2>

<p class="mb-0 mt-2">

Course:

<strong>

<?php echo htmlspecialchars($assignment['course_name']); ?>

</strong>

</p>

</div>

<div class="card-body p-4">

<div class="row g-4">

<div class="col-lg-8">

<div class="info-card">

<h4 class="mb-3">

Assignment Description

</h4>

<div class="assignment-text">

<?php

echo nl2br(

htmlspecialchars($assignment['description'])

);

?>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="info-card mb-3">

<h5>

Course

</h5>

<p>

<?php echo htmlspecialchars($assignment['course_name']); ?>

</p>

<hr>

<h5>

Deadline

</h5>

<p>

<?php

echo date(

"d F Y",

strtotime($assignment['deadline'])

);

?>

</p>

<hr>

<h5>

Status

</h5>

<?php

if($daysLeft<0){

?>

<span class="badge bg-danger">

Expired

</span>

<?php

}elseif($daysLeft==0){

?>

<span class="badge bg-warning">

Due Today

</span>

<?php

}else{

?>

<span class="badge bg-success">

<?php echo floor($daysLeft); ?>

Days Remaining

</span>

<?php } ?>

</div>

</div>

</div>

<hr class="my-4">

<div class="row g-4">

<div class="col-md-4">

<div class="stat-box bg-primary">

<h2>

<?php echo $total; ?>

</h2>

<p>

Total Submissions

</p>

</div>

</div>

<div class="col-md-4">

<div class="stat-box bg-success">

<h2>

<?php echo $graded; ?>

</h2>

<p>

Graded

</p>

</div>

</div>

<div class="col-md-4">

<div class="stat-box bg-warning">

<h2>

<?php echo $pending; ?>

</h2>

<p>

Pending Review

</p>

</div>

</div>

</div>

<div class="text-end mt-5">

<a

href="manage_submissions.php?assignment_id=<?php echo $assignment['id']; ?>"

class="btn btn-success">

<i class="fas fa-upload me-2"></i>

View Submissions

</a>

<a

href="edit_assignment.php?id=<?php echo $assignment['id']; ?>"

class="btn btn-warning">

<i class="fas fa-edit me-2"></i>

Edit

</a>

<a

href="manage_assignments.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>