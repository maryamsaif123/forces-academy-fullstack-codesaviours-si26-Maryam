<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set("Asia/Karachi");

?>

<nav class="navbar navbar-expand-lg bg-white shadow-sm px-4 py-3">

<div class="container-fluid">

<!-- Left -->

<h4 class="fw-bold text-success mb-0">

<i class="fas fa-graduation-cap"></i>

Student Dashboard

</h4>

<!-- Right -->

<div class="d-flex align-items-center ms-auto">

<!-- Search -->

<div class="me-4">

<div class="input-group">

<span class="input-group-text bg-light border-0">

<i class="fas fa-search"></i>

</span>

<input
type="text"
class="form-control border-0 bg-light"
placeholder="Search...">

</div>

</div>

<!-- Date -->

<div class="me-4 text-center">

<small class="text-muted">

Today

</small>

<br>

<strong>

<?php echo date("d M Y"); ?>

</strong>

</div>

<!-- Notification -->

<div class="dropdown me-4">

<a
class="btn btn-light position-relative"
href="#"
data-bs-toggle="dropdown">

<i class="fas fa-bell"></i>

<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">

3

</span>

</a>

<ul class="dropdown-menu dropdown-menu-end shadow">

<li>

<h6 class="dropdown-header">

Notifications

</h6>

</li>

<li>

<a class="dropdown-item" href="#">

📢 New Notice Added

</a>

</li>

<li>

<a class="dropdown-item" href="#">

📚 New Course Available

</a>

</li>

<li>

<a class="dropdown-item" href="#">

📝 Assignment Due Soon

</a>

</li>

</ul>

</div>

<!-- Student Profile -->

<div class="dropdown">

<a
class="d-flex align-items-center text-decoration-none"
href="#"
data-bs-toggle="dropdown">

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
width="45"
height="45"
class="rounded-circle border border-success">

<div class="ms-3">

<h6 class="mb-0">

<?php echo $_SESSION['student_name']; ?>

</h6>

<small class="text-muted">

<?php echo $_SESSION['student_email']; ?>

</small>

</div>

<i class="fas fa-chevron-down ms-3 text-secondary"></i>

</a>

<ul class="dropdown-menu dropdown-menu-end shadow">

<li>

<h6 class="dropdown-header">

Welcome

<?php echo $_SESSION['student_name']; ?>

</h6>

</li>

<li>

<hr class="dropdown-divider">

</li>

<li>

<a class="dropdown-item" href="profile.php">

<i class="fas fa-user me-2"></i>

My Profile

</a>

</li>

<li>

<a class="dropdown-item" href="results.php">

<i class="fas fa-chart-line me-2"></i>

Results

</a>

</li>

<li>

<a class="dropdown-item" href="courses.php">

<i class="fas fa-book me-2"></i>

My Courses

</a>

</li>

<li>

<hr class="dropdown-divider">

</li>

<li>

<a class="dropdown-item text-danger" href="logout.php">

<i class="fas fa-sign-out-alt me-2"></i>

Logout

</a>

</li>

</ul>

</div>

</div>

</div>

</nav>

Improve sidebar navigation and active menu states