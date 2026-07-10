<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$message="";

if(isset($_POST['save'])){

$full_name=$_POST['full_name'];
$email=$_POST['email'];
$password=password_hash($_POST['password'],PASSWORD_DEFAULT);
$roll_number=$_POST['roll_number'];
$class=$_POST['class'];

$sql="INSERT INTO students(full_name,email,password,roll_number,class)
VALUES('$full_name','$email','$password','$roll_number','$class')";

if(mysqli_query($conn,$sql)){

$message="<div class='alert alert-success'>Student Added Successfully!</div>";

}else{

$message="<div class='alert alert-danger'>Something went wrong!</div>";

}

}
?>

<!DOCTYPE html>
<html>

<head>

<title>Add Student</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="dashboard.css">

<style>

.form-card{

background:white;

border-radius:25px;

padding:40px;

box-shadow:0 15px 40px rgba(0,0,0,.12);

}

.avatar{

width:120px;

height:120px;

border-radius:50%;

border:5px solid #22c55e;

margin-bottom:20px;

}

.form-control{

height:55px;

border-radius:12px;

}

.btn-save{

background:#16a34a;

color:white;

height:55px;

border-radius:12px;

font-size:18px;

}

.btn-save:hover{

background:#15803d;

color:white;

}

</style>

</head>

<body>

<?php include("sidebar.php"); ?>

<div class="main-content">

<?php include("navbar.php"); ?>

<div class="container">

<div class="form-card">

<div class="text-center">

<img
src="https://cdn-icons-png.flaticon.com/512/4140/4140048.png"
class="avatar">

<h2>

Add New Student

</h2>

<p class="text-muted">

Register a new student into the LMS

</p>

</div>

<?php echo $message; ?>

<form method="POST">

<div class="row">

<div class="col-md-6 mb-3">

<label>Full Name</label>

<input
type="text"
name="full_name"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Email</label>

<input
type="email"
name="email"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Password</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label>Roll Number</label>

<input
type="text"
name="roll_number"
class="form-control"
required>

</div>

<div class="col-md-12 mb-3">

<label>Class</label>

<input
type="text"
name="class"
class="form-control"
required>

</div>

<div class="col-md-12">

<button
type="submit"
name="save"
class="btn btn-save w-100">

<i class="fas fa-user-plus"></i>

Add Student

</button>

</div>

</div>

</form>

</div>

</div>

</div>

</body>

</html>