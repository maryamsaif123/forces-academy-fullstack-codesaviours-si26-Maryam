<?php
session_start();

if (!isset($_SESSION['student'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$assignments = mysqli_query($conn,"SELECT * FROM assignments ORDER BY due_date ASC");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Assignments</title>

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

.assignment-card{
border:none;
border-radius:15px;
box-shadow:0px 5px 15px rgba(0,0,0,.1);
margin-bottom:20px;
}

</style>

</head>

<body>

<?php include("sidebar.php"); ?>

<div class="main">

<?php include("navbar.php"); ?>

<div class="content">

<h2 class="mb-4">

<i class="fa fa-file-alt text-primary"></i>

Assignments

</h2>

<?php

if(mysqli_num_rows($assignments)>0){

while($row=mysqli_fetch_assoc($assignments)){

?>

<div class="card assignment-card">

<div class="card-body">

<h4 class="text-success">

<?php echo $row['title']; ?>

</h4>

<hr>

<p>

<strong>Course:</strong>

<?php echo $row['course_name']; ?>

</p>

<p>

<strong>Description:</strong>

<?php echo $row['description']; ?>

</p>

<p>

<strong>Due Date:</strong>

<?php echo $row['due_date']; ?>

</p>

<span class="badge bg-warning">

Pending

</span>

</div>

</div>

<?php

}

}else{

?>

<div class="alert alert-warning">

No Assignments Available.

</div>

<?php

}

?>

</div>

<?php include("footer.php"); ?>

</div>

</body>

</html>