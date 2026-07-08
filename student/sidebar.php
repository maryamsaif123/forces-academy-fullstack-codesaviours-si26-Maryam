<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">

<div class="text-center py-4">

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

<h3>

Forces Academy

</h3>

<p style="font-size:13px;opacity:.8;">

Student Portal

</p>

</div>

<a href="dashboard.php"
class="<?php if($currentPage=="dashboard.php") echo "active"; ?>">

<i class="fas fa-home"></i>

Dashboard

</a>

<a href="courses.php"
class="<?php if($currentPage=="courses.php") echo "active"; ?>">

<i class="fas fa-book-open"></i>

My Courses

</a>

<a href="assignments.php"
class="<?php if($currentPage=="assignments.php") echo "active"; ?>">

<i class="fas fa-file-alt"></i>

Assignments

</a>

<a href="results.php"
class="<?php if($currentPage=="results.php") echo "active"; ?>">

<i class="fas fa-chart-line"></i>

Results

</a>

<a href="notices.php"
class="<?php if($currentPage=="notices.php") echo "active"; ?>">

<i class="fas fa-bullhorn"></i>

Notice Board

</a>

<a href="profile.php"
class="<?php if($currentPage=="profile.php") echo "active"; ?>">

<i class="fas fa-user"></i>

My Profile

</a>

<a href="logout.php">

<i class="fas fa-sign-out-alt"></i>

Logout

</a>

</div>