<?php

session_start();

if(!isset($_SESSION['admin']))
{
    header("Location: login.php");
    exit();
}

include("../config/database.php");

// Count Students
$student = mysqli_query($conn,"SELECT * FROM students");
$totalStudents = mysqli_num_rows($student);

// Count Courses
$course = mysqli_query($conn,"SELECT * FROM courses");
$totalCourses = mysqli_num_rows($course);

// Count Notices
$notice = mysqli_query($conn,"SELECT * FROM notices");
$totalNotices = mysqli_num_rows($notice);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<style>

body{
background:#f5f7fb;
}

.sidebar{
width:250px;
height:100vh;
background:#0F172A;
position:fixed;
left:0;
top:0;
color:white;
}

.sidebar h3{
padding:20px;
text-align:center;
border-bottom:1px solid gray;
}

.sidebar a{
display:block;
padding:15px 20px;
text-decoration:none;
color:white;
font-size:17px;
}

.sidebar a:hover{
background:#2563EB;
}

.content{
margin-left:250px;
padding:30px;
}

.card{
border:none;
border-radius:15px;
box-shadow:0px 5px 15px rgba(0,0,0,.15);
}

.card i{
font-size:45px;
margin-bottom:15px;
}

.card h2{
font-weight:bold;
}

.topbar{
background:white;
padding:15px 25px;
border-radius:15px;
margin-bottom:30px;
display:flex;
justify-content:space-between;
align-items:center;
box-shadow:0px 5px 10px rgba(0,0,0,.1);
}

</style>

</head>

<body>

<div class="sidebar">

<h3>🎓 LMS Admin</h3>

<a href="dashboard.php">
<i class="fa fa-home"></i>
 Dashboard
</a>

<a href="manage_students.php">
<i class="fa fa-users"></i>
 Students
</a>

<a href="#">
<i class="fa fa-book"></i>
 Courses
</a>

<a href="#">
<i class="fa fa-bullhorn"></i>
 Notices
</a>

<a href="#">
<i class="fa fa-chart-line"></i>
 Results
</a>

<a href="logout.php">
<i class="fa fa-right-from-bracket"></i>
 Logout
</a>

</div>


<div class="content">

<div class="topbar">

<h3>
Welcome,
<?php echo $_SESSION['admin']; ?>
</h3>

<span>
<?php echo date("d M Y"); ?>
</span>

</div>


<div class="row">

<div class="col-md-4 mb-4">

<div class="card text-center p-4">

<i class="fa fa-users text-primary"></i>

<h2>
<?php echo $totalStudents; ?>
</h2>

<h5>Total Students</h5>

</div>

</div>


<div class="col-md-4 mb-4">

<div class="card text-center p-4">

<i class="fa fa-book text-success"></i>

<h2>
<?php echo $totalCourses; ?>
</h2>

<h5>Total Courses</h5>

</div>

</div>

<div class="col-md-4 mb-4">

<div class="card text-center p-4">

<i class="fa fa-bullhorn text-danger"></i>

<h2>
<?php echo $totalNotices; ?>
</h2>

<h5>Total Notices</h5>

</div>

</div>

</div>


<div class="card mt-4 p-4">

<h4>Quick Actions</h4>

<hr>

<a href="manage_students.php" class="btn btn-primary">
Manage Students
</a>

<a href="#" class="btn btn-success">
Add Course
</a>

<a href="#" class="btn btn-warning">
Add Notice
</a>

<a href="#" class="btn btn-info">
View Results
</a>

</div>

</div>

</body>

</html>

