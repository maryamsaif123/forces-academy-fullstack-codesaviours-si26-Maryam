<?php

session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

// Total Courses
$courseQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM courses");
$course = mysqli_fetch_assoc($courseQuery);

// Total Notices
$noticeQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM notices");
$notice = mysqli_fetch_assoc($noticeQuery);

// Latest Notices
$latestNotice = mysqli_query($conn, "SELECT * FROM notices ORDER BY id DESC LIMIT 3");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Student Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="../css/dashboard.css">

</head>

<body>

<?php include("sidebar.php"); ?>

<div class="main-content">

<?php include("navbar.php"); ?>

<div class="container-fluid py-4">

<div class="welcome-banner">

<div>

<h2>Welcome Back 👋</h2>

<h4><?php echo $_SESSION['student_name']; ?></h4>

<p>Have a productive learning day.</p>

</div>

<div>
    <?php

if($_SESSION['student_gender']=="Female")
{
    $avatar="https://cdn-icons-png.flaticon.com/512/6997/6997662.png";
}
else
{
    $avatar="https://cdn-icons-png.flaticon.com/512/3135/3135715.png";
}

?>

<img
src="<?php echo $avatar; ?>"
width="120"
class="rounded-circle shadow">

</div>

</div>

<div class="row mt-4">

<div class="col-lg-3">

<div class="dashboard-card green">

<i class="fa-solid fa-book-open"></i>

<h2><?php echo $course['total']; ?></h2>

<p>Total Courses</p>

</div>

</div>

<div class="col-lg-3">

<div class="dashboard-card yellow">

<i class="fa-solid fa-file-lines"></i>

<h2>0</h2>

<p>Assignments</p>

</div>

</div>

<div class="col-lg-3">

<div class="dashboard-card red">

<i class="fa-solid fa-bullhorn"></i>

<h2><?php echo $notice['total']; ?></h2>

<p>Notices</p>

</div>

</div>

<div class="col-lg-3">

<div class="dashboard-card blue">

<i class="fa-solid fa-award"></i>

<h2>A+</h2>

<p>Performance</p>

</div>

</div>

</div>

<div class="row mt-4">

<div class="col-lg-8">

<div class="card shadow border-0">

<div class="card-header bg-primary text-white">

<h4>Recent Notices</h4>

</div>

<div class="card-body">

<?php

while($row=mysqli_fetch_assoc($latestNotice)){

?>

<div class="notice-box">

<h5>

<i class="fa-solid fa-bullhorn"></i>

<?php echo $row['title']; ?>

</h5>

<p>

<?php echo $row['content']; ?>

</p>

<small>

Posted By

<b><?php echo $row['posted_by']; ?></b>

|

<?php echo date("d M Y",strtotime($row['created_at'])); ?>

</small>

</div>

<?php

}

?>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card shadow border-0">

<div class="card-body text-center">

<?php

if($_SESSION['student_gender']=="Female")
{
    $avatar="https://cdn-icons-png.flaticon.com/512/6997/6997662.png";
}
else
{
    $avatar="https://cdn-icons-png.flaticon.com/512/3135/3135715.png";
}

?>

<img
src="<?php echo $avatar; ?>"
width="120"
class="rounded-circle shadow">

<h3 class="mt-3">

<?php echo $_SESSION['student_name']; ?>

</h3>

<p>

<?php echo $_SESSION['student_email']; ?>

</p>

<hr>

<p><b>Status:</b> Active Student</p>

<p><b>Class:</b> BSIT</p>

<div class="progress">

<div class="progress-bar bg-success"

style="width:80%">

80%

</div>

</div>

</div>

</div>

</div>

</div>

<div class="row mt-4">

<div class="col-lg-12">

<div class="card shadow border-0">

<div class="card-header bg-success text-white">

<h4>

Quick Actions

</h4>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-3 mb-3">

<a href="courses.php" class="action-card text-decoration-none">

<i class="fas fa-book"></i>

<h5>Courses</h5>

<p>View all available courses</p>

</a>

</div>

<div class="col-md-3 mb-3">

<a href="assignments.php" class="action-card text-decoration-none">

<i class="fas fa-file-alt"></i>

<h5>Assignments</h5>

<p>Submit your work</p>

</a>

</div>

<div class="col-md-3 mb-3">

<a href="results.php" class="action-card text-decoration-none">

<i class="fas fa-chart-line"></i>

<h5>Results</h5>

<p>View your grades</p>

</a>

</div>

<div class="col-md-3 mb-3">

<a href="notices.php" class="action-card text-decoration-none">

<i class="fas fa-bullhorn"></i>

<h5>Notice Board</h5>

<p>Latest announcements</p>

</a>

</div>

</div>

</div>

</div>

</div>

</div>

<!-- Quote -->

<div class="row mt-4">

<div class="col-lg-8">

<div class="card shadow border-0">

<div class="card-body">

<h4>

<i class="fas fa-lightbulb text-warning"></i>

Motivational Quote

</h4>

<hr>

<h5 class="text-primary">

"Success is the sum of small efforts, repeated day in and day out."

</h5>

<p class="text-muted">

Stay focused, complete your assignments on time, and never stop learning.

</p>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card shadow border-0">

<div class="card-body">

<h4>

Learning Progress

</h4>

<hr>

<p>Courses Completed</p>

<div class="progress mb-3">

<div class="progress-bar bg-success"

style="width:75%;">

75%

</div>

</div>

<p>Assignments Submitted</p>

<div class="progress mb-3">

<div class="progress-bar bg-info"

style="width:60%;">

60%

</div>

</div>

<p>Attendance</p>

<div class="progress">

<div class="progress-bar bg-warning"

style="width:92%;">

92%

</div>

</div>

</div>

</div>

</div>

</div>

<footer class="text-center mt-5 mb-3">

<hr>

<h5>

Forces Academy LMS

</h5>

<p>

Developed by

<strong>Maryam Saif</strong>

<br>

BS Information Technology

<br>

The University of Faisalabad

</p>

</footer>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>


</html>



Improve navbar styling with icons and notification badge