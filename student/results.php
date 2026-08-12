<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$student_id = $_SESSION['student_id'];

// Student Information
$studentQuery = mysqli_query($conn, "
SELECT *
FROM students
WHERE id='$student_id'
");

$student = mysqli_fetch_assoc($studentQuery);

// Student Results
$resultQuery = mysqli_query($conn, "
SELECT *
FROM results
WHERE student_id='$student_id'
ORDER BY created_at DESC
");

$totalMarks = 0;
$obtainedMarks = 0;
$totalSubjects = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Results</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <!-- Dashboard CSS -->
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>

<?php include("sidebar.php"); ?>

<?php include("navbar.php"); ?>

<div class="main-content">
<body class="bg-light">

<div class="container-fluid mt-4">

<div class="card shadow border-0">

<div class="card-header bg-primary text-white">

<h4><i class="fas fa-chart-line"></i> My Results</h4>

</div>

<div class="card-body">

<table class="table table-bordered table-hover">

<thead class="table-primary">

<tr>

<th>#</th>

<th>Subject</th>

<th>Marks</th>

<th>Total</th>

<th>Percentage</th>

<th>Grade</th>

<th>Exam Type</th>

<th>Remarks</th>

<th>Date</th>

</tr>

</thead>

<tbody>

<?php

$count=1;

while($row=mysqli_fetch_assoc($resultQuery)){

$totalSubjects++;

$totalMarks += $row['total_marks'];

$obtainedMarks += $row['marks'];

$percentage = ($row['marks']/$row['total_marks'])*100;

?>

<tr>

<td><?php echo $count++; ?></td>

<td><?php echo $row['subject']; ?></td>

<td><?php echo $row['marks']; ?></td>

<td><?php echo $row['total_marks']; ?></td>

<td><?php echo number_format($percentage,1); ?>%</td>

<td>

<span class="badge bg-success">

<?php echo $row['grade']; ?>

</span>

</td>

<td>

<?php echo $row['exam_type']; ?>

</td>

<td>

<?php echo $row['remarks']; ?>

</td>

<td>

<?php echo date("d M Y",strtotime($row['created_at'])); ?>

</td>

</tr>

<?php

}

if($totalSubjects==0){

?>

<tr>

<td colspan="9" class="text-center text-danger">

No Result Found

</td>

</tr>

<?php } ?>

</tbody>

</table>

<?php

if($totalSubjects>0){

$overall = ($obtainedMarks/$totalMarks)*100;

?>

<div class="row mt-4">

<div class="col-md-4">

<div class="card bg-primary text-white">

<div class="card-body">

<h6>Total Subjects</h6>

<h2><?php echo $totalSubjects; ?></h2>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card bg-success text-white">

<div class="card-body">

<h6>Total Marks</h6>

<h2><?php echo $obtainedMarks." / ".$totalMarks; ?></h2>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card bg-warning text-dark">

<div class="card-body">

<h6>Overall Percentage</h6>

<h2><?php echo number_format($overall,2); ?>%</h2>

</div>

</div>

</div>

</div>

<?php } ?>

</div>

</div>

</div>

</body>

</html>