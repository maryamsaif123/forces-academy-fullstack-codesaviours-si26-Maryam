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

// Notices
$noticeQuery = mysqli_query($conn, "
SELECT *
FROM notices
ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Notices</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

<link rel="stylesheet" href="assets/css/dashboard.css">
</head>

<body>

<!-- Page Header -->

<div class="card border-0 shadow-sm mb-4">

<div class="card-body bg-primary text-white rounded">

<div class="d-flex justify-content-between align-items-center">

<div>

<h2 class="fw-bold mb-1">

<i class="fas fa-bullhorn"></i>

Latest Notices

</h2>

<p class="mb-0">

Stay updated with academy announcements.

</p>

</div>

<div>

<i class="fas fa-bell fa-3x opacity-50"></i>

</div>

</div>

</div>

</div>

<div class="row">
<?php

if(mysqli_num_rows($noticeQuery)>0){

while($notice=mysqli_fetch_assoc($noticeQuery)){

?>

<div class="col-lg-6 mb-4">

<div class="card border-0 shadow-sm h-100 notice-card">

<div class="card-header bg-white border-0">

<div class="d-flex justify-content-between">

<h5 class="fw-bold text-primary">

<i class="fas fa-bullhorn"></i>

<?php echo $notice['title']; ?>

</h5>

<span class="badge bg-primary">

New

</span>

</div>

</div>

<div class="card-body">

<p class="text-muted">

<?php echo nl2br($notice['content']); ?>

</p>

</div>

<div class="card-footer bg-white">

<div class="d-flex justify-content-between">

<small>

<i class="fas fa-user text-primary"></i>

<?php echo $notice['posted_by']; ?>

</small>

<small>

<i class="fas fa-calendar"></i>

<?php echo date("d M Y",strtotime($notice['created_at'])); ?>

</small>

</div>

</div>

</div>

</div>

<?php

}

}else{

?>

<div class="col-12">

<div class="alert alert-warning">

No Notices Available.

</div>

</div>

<?php } ?>

</div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>