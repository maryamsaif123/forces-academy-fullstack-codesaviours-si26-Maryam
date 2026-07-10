<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include("../config/database.php");

if(!isset($_GET['id'])){
    header("Location: manage_students.php");
    exit();
}

$id = intval($_GET['id']);

$query = mysqli_query($conn,"SELECT * FROM students WHERE id='$id'");

if(mysqli_num_rows($query)==0){
    die("Student not found");
}

$student=mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Student Profile</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="dashboard.css">

<style>

.profile-card{

background:white;

border-radius:20px;

box-shadow:0 10px 30px rgba(0,0,0,.1);

padding:40px;

}

.avatar{

width:140px;

height:140px;

border-radius:50%;

border:5px solid #22c55e;

}

.info{

padding:15px 0;

border-bottom:1px solid #eee;

}

.info:last-child{

border:none;

}

.label{

font-weight:600;

color:#666;

}

.value{

font-size:18px;

font-weight:500;

}

</style>

</head>

<body>

<?php include("sidebar.php"); ?>

<div class="main-content">

<?php include("navbar.php"); ?>

<div class="container">

<div class="profile-card">

<div class="text-center">

<img
src="https://cdn-icons-png.flaticon.com/512/4140/4140048.png"
class="avatar">

<h2 class="mt-3">

<?php echo $student['full_name']; ?>

</h2>

<p class="text-muted">

Student Profile

</p>

<span class="badge bg-success">

Active Student

</span>

</div>

<hr>

<div class="row mt-4">

<div class="col-md-6">

<div class="info">

<div class="label">

<i class="fas fa-user"></i>

Full Name

</div>

<div class="value">

<?php echo $student['full_name']; ?>

</div>

</div>

<div class="info">

<div class="label">

<i class="fas fa-envelope"></i>

Email

</div>

<div class="value">

<?php echo $student['email']; ?>

</div>

</div>

<div class="info">

<div class="label">

<i class="fas fa-id-card"></i>

Roll Number

</div>

<div class="value">

<?php echo $student['roll_number']; ?>

</div>

</div>

</div>

<div class="col-md-6">

<div class="info">

<div class="label">

<i class="fas fa-graduation-cap"></i>

Class

</div>

<div class="value">

<?php echo $student['class']; ?>

</div>

</div>

<div class="info">

<div class="label">

<i class="fas fa-calendar"></i>

Registered On

</div>

<div class="value">

<?php echo date("d M Y",strtotime($student['created_at'])); ?>

</div>

</div>

<div class="info">

<div class="label">

<i class="fas fa-check-circle"></i>

Status

</div>

<div class="value">

<span class="badge bg-success">

Active

</span>

</div>

</div>

</div>

</div>

<div class="text-center mt-5">

<a
href="edit_student.php?id=<?php echo $student['id']; ?>"
class="btn btn-warning btn-lg">

<i class="fas fa-edit"></i>

Edit Student

</a>

<a
href="manage_students.php"
class="btn btn-secondary btn-lg">

<i class="fas fa-arrow-left"></i>

Back

</a>

</div>

</div>

</div>

</div>

</body>

</html>