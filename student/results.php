<?php
session_start();

if (!isset($_SESSION['student'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$results = mysqli_query($conn,"SELECT * FROM results ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Student Results</title>

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

.table{
background:white;
box-shadow:0 5px 15px rgba(0,0,0,.1);
border-radius:10px;
}

</style>

</head>

<body>

<?php include("sidebar.php"); ?>

<div class="main">

<?php include("navbar.php"); ?>

<div class="content">

<h2 class="mb-4">

<i class="fa fa-chart-line text-success"></i>

My Results

</h2>

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>Course</th>

<th>Marks</th>

<th>Grade</th>

<th>Status</th>

</tr>

</thead>

<tbody>

<?php

if(mysqli_num_rows($results)>0){

while($row=mysqli_fetch_assoc($results)){

?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['course_name']; ?></td>

<td><?php echo $row['marks']; ?></td>

<td><?php echo $row['grade']; ?></td>

<td>

<span class="badge bg-success">

Pass

</span>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="5" class="text-center">

No Results Available

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

<?php include("footer.php"); ?>

</div>

</body>

</html>