<?php
session_start();

if (!isset($_SESSION['student'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$courses = mysqli_query($conn, "SELECT * FROM courses ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>My Courses</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<style>

body{
    background:#f4f6f9;
    margin:0;
    font-family:Arial;
}

.main{
    margin-left:240px;
}

.content{
    padding:30px;
}

.course-card{
    border:none;
    border-radius:15px;
    box-shadow:0px 5px 15px rgba(0,0,0,.1);
    transition:.3s;
}

.course-card:hover{
    transform:translateY(-5px);
}

</style>

</head>

<body>

<?php include("sidebar.php"); ?>

<div class="main">

<?php include("navbar.php"); ?>

<div class="content">

<h2 class="mb-4">

<i class="fa fa-book"></i>

My Courses

</h2>

<div class="row">

<?php

if(mysqli_num_rows($courses)>0){

while($row=mysqli_fetch_assoc($courses)){

?>

<div class="col-md-6 mb-4">

<div class="card course-card">

<div class="card-body">

<h4 class="text-success">

<?php echo $row['course_name']; ?>

</h4>

<hr>

<p>

<strong>Course Code:</strong>

<?php echo $row['course_code']; ?>

</p>

<p>

<strong>Instructor:</strong>

<?php echo $row['instructor']; ?>

</p>

<p>

<strong>Duration:</strong>

<?php echo $row['duration']; ?>

</p>

</div>

</div>

</div>

<?php

}

}else{

?>

<div class="alert alert-warning">

No Courses Available.

</div>

<?php

}

?>

</div>

</div>

<?php include("footer.php"); ?>

</div>

</body>

</html>