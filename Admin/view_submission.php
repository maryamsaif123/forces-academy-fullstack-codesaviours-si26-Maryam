<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

if (!isset($_GET['id'])) {
    header("Location: manage_submissions.php");
    exit();
}

$id = (int)$_GET['id'];

$query = mysqli_query($conn, "
SELECT
submissions.*,
students.name AS student_name,
students.email,
assignments.title,
assignments.description,
assignments.deadline,
courses.course_name
FROM submissions

LEFT JOIN students
ON submissions.student_id = students.id

LEFT JOIN assignments
ON submissions.assignment_id = assignments.id

LEFT JOIN courses
ON assignments.course_id = courses.id

WHERE submissions.id='$id'

LIMIT 1
");

if(mysqli_num_rows($query)==0){
    die("Submission not found.");
}

$row = mysqli_fetch_assoc($query);

if($row['gender']=="Female"){
    $avatar="https://cdn-icons-png.flaticon.com/512/6997/6997662.png";
}else{
    $avatar="https://cdn-icons-png.flaticon.com/512/3135/3135715.png";
}
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>View Submission</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="dashboard.css">
</head>

<body>

<?php include("sidebar.php"); ?>

<div class="main-content">

<?php include("navbar.php"); ?>

<div class="container-fluid">

<div class="page-header">

<div class="d-flex justify-content-between align-items-center">

<div>

<h2>

<i class="fas fa-file-alt"></i>

Submission Details

</h2>

<p>

View uploaded assignment information.

</p>

</div>

<a href="manage_submissions.php" class="btn btn-light">

<i class="fas fa-arrow-left"></i>

Back

</a>

</div>

</div>

<div class="row">

<div class="col-lg-4">

<div class="card shadow border-0 rounded-4">

<div class="card-body text-center">

<img src="<?php echo $avatar; ?>"
width="120"
class="rounded-circle border border-success mb-3">

<h3>

<?php echo $row['student_name']; ?>

</h3>

<p class="text-muted">

<?php echo $row['email']; ?>

</p>

<hr>

<p>

<strong>Status</strong>

</p>

<?php

$statusColor="warning";

if($row['status']=="graded"){
    $statusColor="success";
}

?>

<span class="badge bg-<?php echo $statusColor; ?>">

<?php echo ucfirst($row['status']); ?>

</span>

</div>

</div>

</div>

<div class="col-lg-8">

<div class="card shadow border-0 rounded-4">

<div class="card-body">

<h4>

Assignment Information

</h4>

<hr>

<table class="table">

<tr>

<th width="180">

Course

</th>

<td>

<?php echo $row['course_name']; ?>

</td>

</tr>

<tr>

<th>

Assignment

</th>

<td>

<?php echo $row['title']; ?>

</td>

</tr>

<tr>

<th>

Description

</th>

<td>

<?php echo nl2br($row['description']); ?>

</td>

</tr>

<tr>

<th>

Deadline

</th>

<td>

<?php echo date("d M Y",strtotime($row['deadline'])); ?>

</td>

</tr>

<tr>

<th>

Submitted On

</th>

<td>

<?php echo date("d M Y h:i A",strtotime($row['submitted_at'])); ?>

</td>

</tr>

<tr>

<th>

Marks

</th>

<td>

<?php

echo $row['marks']!=""
? $row['marks']
: "<span class='text-muted'>Not Graded</span>";

?>

</td>

</tr>

<tr>

<th>

Feedback

</th>

<td>

<?php

echo $row['feedback']!=""
? nl2br($row['feedback'])
: "<span class='text-muted'>No Feedback</span>";

?>

</td>

</tr>

<tr>

<th>

Uploaded File

</th>

<td>

<a

href="../uploads/<?php echo $row['file_path']; ?>"

target="_blank"

class="btn btn-primary">

<i class="fas fa-download"></i>

Download Assignment

</a>

</td>

</tr>

</table>

<hr>

<a

href="grade_submission.php?id=<?php echo $row['id']; ?>"

class="btn btn-success">

<i class="fas fa-star"></i>

Grade Assignment

</a>

</div>

</div>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
