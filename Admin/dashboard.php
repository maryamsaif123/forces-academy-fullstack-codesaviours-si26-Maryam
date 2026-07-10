<?php

session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include("../config/database.php");

// Total Students
$students = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM students"));

// Total Courses
$courses = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM courses"));

// Total Notices
$notices = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM notices"));

// Latest Notices
$latest = mysqli_query($conn,"SELECT * FROM notices ORDER BY id DESC LIMIT 4");

$date = date("d M Y");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Admin Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="dashboard.css">

</head>

<body>

<?php include("sidebar.php"); ?>

<div class="main-content">

<?php include("navbar.php"); ?>

<div class="container-fluid mt-4">

<div class="welcome-banner">

<div>

<h1>

Welcome Back, Admin 👋

</h1>

<p>

Manage your Learning Management System from one place.

</p>

<a href="manage_students.php" class="btn btn-light">

View Summary

</a>

</div>

<img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png">

</div>

<div class="row mt-4">

<div class="col-lg-4">

<div class="stats-card students">

<div>

<h6>Total Students</h6>

<h2><?php echo $students; ?></h2>

</div>

<i class="fas fa-users"></i>

</div>

</div>

<div class="col-lg-4">

<div class="stats-card courses">

<div>

<h6>Total Courses</h6>

<h2><?php echo $courses; ?></h2>

</div>

<i class="fas fa-book-open"></i>

</div>

</div>

<div class="col-lg-4">

<div class="stats-card notices">

<div>

<h6>Total Notices</h6>

<h2><?php echo $notices; ?></h2>

</div>

<i class="fas fa-bullhorn"></i>

</div>

</div>

</div>

<div class="row mt-4">

<div class="col-lg-7">

<div class="card dashboard-card">

<div class="card-header">

Quick Actions

</div>

<div class="card-body">

<div class="row g-3">

<div class="col-md-6">

<a href="manage_students.php" class="action-btn blue">

<i class="fas fa-users"></i>

Manage Students

</a>

</div>

<div class="col-md-6">

<a href="manage_courses.php" class="action-btn green">

<i class="fas fa-book"></i>

Manage Courses

</a>

</div>

<div class="col-md-6">

<a href="manage_notices.php" class="action-btn orange">

<i class="fas fa-bullhorn"></i>

Manage Notices

</a>

</div>

<div class="col-md-6">

<a href="manage_results.php" class="action-btn purple">

<i class="fas fa-chart-line"></i>

Results

</a>

</div>

</div>

</div>

</div>

</div>

<div class="col-lg-5">

<div class="card dashboard-card">

<div class="card-header">

Recent Notices

</div>

<div class="card-body">

<?php

while($row=mysqli_fetch_assoc($latest))

{

?>

<div class="notice-item">

<h6>

<?php echo $row['title']; ?>

</h6>

<p>

<?php echo $row['content']; ?>

</p>

<small>

<?php echo $row['created_at']; ?>

</small>

</div>

<?php

}

?>

</div>

</div>

</div>

</div>

<div class="footer">

© 2026 Forces Academy LMS

</div>

</div>

</div>

</body>

</html>

