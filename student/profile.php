<?php
session_start();

if(!isset($_SESSION['student_id'])){
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$student_id = $_SESSION['student_id'];

$stmt = mysqli_prepare(

$conn,

"SELECT *
FROM students
WHERE id=?
LIMIT 1"

);

mysqli_stmt_bind_param(

$stmt,

"i",

$student_id

);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$student = mysqli_fetch_assoc($result);

if(!$student){

session_destroy();

header("Location: login.php");

exit();

}

/*=========================================
    PROFILE PHOTO
=========================================*/

$avatar = !empty($student['photo'])

?

"../uploads/students/".$student['photo']

:

"assets/images/avatar.png";

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1">

<title>

Student Profile

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

<style>

body{

background:#f5f7fb;

font-family:'Poppins',sans-serif;

}

.profile-header{

background:linear-gradient(135deg,#2563eb,#4f46e5);

height:220px;

border-radius:0 0 30px 30px;

}

.profile-card{

margin-top:-90px;

border:none;

border-radius:25px;

box-shadow:0 15px 40px rgba(0,0,0,.12);

}

.profile-image{

width:170px;

height:170px;

border-radius:50%;

border:6px solid white;

object-fit:cover;

box-shadow:0 10px 30px rgba(0,0,0,.2);

}

.info-box{

background:#f8f9fa;

padding:18px;

border-radius:15px;

margin-bottom:15px;

transition:.3s;

}

.info-box:hover{

background:#eef4ff;

transform:translateY(-3px);

}

.action-btn{

border-radius:12px;

padding:12px;

font-weight:600;

}

</style>

</head>

<body>

<div class="profile-header"></div>

<div class="container">

<div class="card profile-card">

<div class="card-body p-5">
<div class="row">

<!-- ==========================================
        LEFT PROFILE CARD
========================================== -->

<div class="col-lg-4">

<div class="text-center">

<img

src="<?php echo $avatar; ?>"

class="profile-image"

alt="Student Photo">

<h3 class="mt-4 mb-1">

<?php echo htmlspecialchars($student['full_name']); ?>

</h3>

<p class="text-muted">

<i class="fas fa-graduation-cap me-2"></i>

<?php echo htmlspecialchars($student['class']); ?>

</p>

<hr>

<div class="d-grid gap-3">

<a

href="edit_profile.php"

class="btn btn-primary action-btn">

<i class="fas fa-user-edit me-2"></i>

Edit Profile

</a>

<a

href="change_password.php"

class="btn btn-warning action-btn">

<i class="fas fa-lock me-2"></i>

Change Password

</a>

<a

href="dashboard.php"

class="btn btn-secondary action-btn">

<i class="fas fa-arrow-left me-2"></i>

Back to Dashboard

</a>

</div>

</div>

</div>

<!-- ==========================================
        PROFILE INFORMATION
========================================== -->

<div class="col-lg-8">

<h3 class="mb-4">

<i class="fas fa-user-circle text-primary me-2"></i>

Personal Information

</h3>

<div class="row">

<div class="col-md-6">

<div class="info-box">

<h6 class="text-muted">

Full Name

</h6>

<h5>

<?php echo htmlspecialchars($student['full_name']); ?>

</h5>

</div>

</div>

<div class="col-md-6">

<div class="info-box">

<h6 class="text-muted">

Email Address

</h6>

<h5>

<?php echo htmlspecialchars($student['email']); ?>

</h5>

</div>

</div>

<div class="col-md-6">

<div class="info-box">

<h6 class="text-muted">

Class

</h6>

<h5>

<?php echo htmlspecialchars($student['class']); ?>

</h5>

</div>

</div>

<div class="col-md-6">

<div class="info-box">

<h6 class="text-muted">

Gender

</h6>

<h5>

<?php echo htmlspecialchars($student['gender']); ?>

</h5>

</div>

</div>

<div class="col-md-6">

<div class="info-box">

<h6 class="text-muted">

Phone Number

</h6>

<h5>

<?php echo !empty($student['phone']) ? htmlspecialchars($student['phone']) : "Not Available"; ?>

</h5>

</div>

</div>

<div class="col-md-6">

<div class="info-box">

<h6 class="text-muted">

Registration Date

</h6>

<h5>

<?php

if(!empty($student['created_at'])){

echo date("d F Y",strtotime($student['created_at']));

}else{

echo "N/A";

}

?>

</h5>

</div>

</div>

<div class="col-md-6">

<div class="info-box">

<h6 class="text-muted">

Account Status

</h6>

<h5>

<span class="badge bg-success px-3 py-2">

Active

</span>

</h5>

</div>

</div>

</div>

</div>

</div>

<hr class="my-5">

<!-- ==========================================
        ACADEMIC SUMMARY
========================================== -->

<h3 class="mb-4">

<i class="fas fa-chart-line text-success me-2"></i>

Academic Summary

</h3>

<div class="row">

<div class="col-md-3">

<div class="card text-center border-0 shadow-sm">

<div class="card-body">

<i class="fas fa-book fa-2x text-primary mb-3"></i>

<h3>

<?php

echo mysqli_fetch_assoc(

mysqli_query($conn,"SELECT COUNT(*) total FROM courses")

)['total'];

?>

</h3>

<p class="text-muted mb-0">

Courses

</p>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card text-center border-0 shadow-sm">

<div class="card-body">

<i class="fas fa-file-alt fa-2x text-warning mb-3"></i>

<h3>

<?php

echo mysqli_fetch_assoc(

mysqli_query($conn,"SELECT COUNT(*) total FROM assignments")

)['total'];

?>

</h3>

<p class="text-muted mb-0">

Assignments

</p>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card text-center border-0 shadow-sm">

<div class="card-body">

<i class="fas fa-award fa-2x text-success mb-3"></i>

<h3>

<?php

$result = mysqli_fetch_assoc(

mysqli_query(

$conn,

"SELECT ROUND(AVG((marks/total_marks)*100),1) avg_marks
FROM results
WHERE student_id=$student_id"

)

);

echo $result['avg_marks'] ?? 0;

?>%

</h3>

<p class="text-muted mb-0">

Average Marks

</p>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card text-center border-0 shadow-sm">

<div class="card-body">

<i class="fas fa-bullhorn fa-2x text-danger mb-3"></i>

<h3>

<?php

echo mysqli_fetch_assoc(

mysqli_query($conn,"SELECT COUNT(*) total FROM notices")

)['total'];

?>

</h3>

<p class="text-muted mb-0">

Notices

</p>

</div>

</div>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>