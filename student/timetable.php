<?php

session_start();

if(!isset($_SESSION['student_id'])){
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/*=========================================
    STUDENT INFORMATION
=========================================*/

$student_id = $_SESSION['student_id'];

$stmt = mysqli_prepare(

$conn,

"SELECT *
FROM students
WHERE id=?
LIMIT 1"

);

mysqli_stmt_bind_param($stmt,"i",$student_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$student = mysqli_fetch_assoc($result);

if(!$student){

session_destroy();

header("Location: login.php");

exit();

}

/*=========================================
    STUDENT CLASS / SEMESTER
=========================================*/

$class_name = $student['class'];

/*=========================================
    FETCH TIMETABLE
=========================================*/

$timetable = mysqli_prepare(

$conn,

"SELECT *
FROM timetable
WHERE class_name=?
ORDER BY
FIELD(
day,
'Monday',
'Tuesday',
'Wednesday',
'Thursday',
'Friday',
'Saturday'
),
period_no ASC"

);

mysqli_stmt_bind_param($timetable,"s",$class_name);

mysqli_stmt_execute($timetable);

$timetable_result = mysqli_stmt_get_result($timetable);

?>
<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>My Timetable</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="assets/css/dashboard.css">

</head>

<body>

<?php include("sidebar.php"); ?>

<?php include("navbar.php"); ?>
<div class="main-content">

<div class="container-fluid py-4">

<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h2 class="fw-bold">

<i class="fas fa-calendar-alt text-primary me-2"></i>

My Timetable

</h2>

<p class="text-muted">

Weekly class schedule for

<strong>

<?php echo htmlspecialchars($class_name); ?>

</strong>

</p>

</div>

<button
onclick="window.print()"
class="btn btn-success">

<i class="fas fa-print me-2"></i>

Print Timetable

</button>

</div>
<!-- ==========================================
        WEEKLY TIMETABLE
========================================== -->

<div class="card shadow border-0 rounded-4">

<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

<h4 class="mb-0">

<i class="fas fa-calendar-week me-2"></i>

Weekly Timetable

</h4>

<span class="badge bg-light text-primary fs-6">

<?php echo htmlspecialchars($class_name); ?>

</span>

</div>

<div class="card-body">

<?php

if(mysqli_num_rows($timetable_result)>0){

?>

<div class="table-responsive">

<table class="table table-hover table-bordered align-middle">

<thead class="table-dark">

<tr>

<th width="120">

Day

</th>

<th width="80">

Period

</th>

<th width="170">

Time

</th>

<th>

Subject

</th>

<th>

Teacher

</th>

<th width="120">

Room

</th>

</tr>

</thead>

<tbody>

<?php

while($row=mysqli_fetch_assoc($timetable_result)){

?>

<tr>

<td>

<span class="badge bg-primary px-3 py-2">

<?php echo $row['day']; ?>

</span>

</td>

<td>

<span class="badge bg-secondary">

<?php echo $row['period_no']; ?>

</span>

</td>

<td>

<?php

echo date("h:i A",strtotime($row['start_time']));

?>

<br>

<small class="text-muted">

to

</small>

<br>

<?php

echo date("h:i A",strtotime($row['end_time']));

?>

</td>

<td>

<strong>

<?php echo htmlspecialchars($row['subject']); ?>

</strong>

</td>

<td>

<i class="fas fa-user-tie text-success me-2"></i>

<?php echo htmlspecialchars($row['teacher']); ?>

</td>

<td>

<span class="badge bg-info">

<?php echo htmlspecialchars($row['room_no']); ?>

</span>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

<?php

}else{

?>

<div class="text-center py-5">

<i class="fas fa-calendar-times fa-5x text-muted mb-3"></i>

<h4>

No Timetable Available

</h4>

<p class="text-muted">

Your timetable has not been uploaded yet.

Please contact the administration.

</p>

</div>

<?php

}

?>

</div>

</div>
<?php

$today = date("l");

$todayQuery = mysqli_prepare(

$conn,

"SELECT *
FROM timetable
WHERE class_name=?
AND day=?
ORDER BY period_no"

);

mysqli_stmt_bind_param(

$todayQuery,

"ss",

$class_name,

$today

);

mysqli_stmt_execute($todayQuery);

$todayResult = mysqli_stmt_get_result($todayQuery);

$totalToday = mysqli_num_rows($todayResult);

mysqli_data_seek($todayResult,0);

$nextClass = mysqli_fetch_assoc($todayResult);

?>

<div class="row mt-4">

<!-- Today's Summary -->

<div class="col-lg-4">

<div class="card border-0 shadow rounded-4 bg-primary text-white">

<div class="card-body">

<h5>

<i class="fas fa-calendar-day me-2"></i>

Today's Classes

</h5>

<h1 class="display-4">

<?php echo $totalToday; ?>

</h1>

<p class="mb-0">

<?php echo $today; ?>

</p>

</div>

</div>

</div>

<!-- Next Class -->

<div class="col-lg-4">

<div class="card border-0 shadow rounded-4 bg-success text-white">

<div class="card-body">

<h5>

<i class="fas fa-clock me-2"></i>

Next Class

</h5>

<?php if($nextClass){ ?>

<h4>

<?php echo htmlspecialchars($nextClass['subject']); ?>

</h4>

<p>

<?php echo date("h:i A",strtotime($nextClass['start_time'])); ?>

-

<?php echo date("h:i A",strtotime($nextClass['end_time'])); ?>

</p>

<small>

Teacher:

<?php echo htmlspecialchars($nextClass['teacher']); ?>

</small>

<?php }else{ ?>

<h5>No Classes Today</h5>

<?php } ?>

</div>

</div>

</div>

<!-- Class Information -->

<div class="col-lg-4">

<div class="card border-0 shadow rounded-4 bg-warning">

<div class="card-body">

<h5>

<i class="fas fa-school me-2"></i>

Class

</h5>

<h4>

<?php echo htmlspecialchars($class_name); ?>

</h4>

<p class="mb-0">

Forces Academy LMS

</p>

</div>

</div>

</div>

</div>
<!-- ==========================================
        QUOTE OF THE DAY
========================================== -->

<div class="row mt-4">

<div class="col-lg-8">

<div class="card border-0 shadow rounded-4">

<div class="card-header bg-warning">

<h4 class="mb-0">

<i class="fas fa-quote-left me-2"></i>

Quote of the Day

</h4>

</div>

<div class="card-body">

<blockquote class="blockquote mb-0">

<p class="fs-5">

"Success is the sum of small efforts, repeated day in and day out."

</p>

<footer class="blockquote-footer mt-2">

Robert Collier

</footer>

</blockquote>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card border-0 shadow rounded-4">

<div class="card-header bg-info text-white">

<h4 class="mb-0">

<i class="fas fa-lightbulb me-2"></i>

Study Tips

</h4>

</div>

<div class="card-body">

<ul class="list-group list-group-flush">

<li class="list-group-item">

📚 Revise today's lecture.

</li>

<li class="list-group-item">

📝 Complete pending assignments.

</li>

<li class="list-group-item">

⏰ Reach class 10 minutes early.

</li>

<li class="list-group-item">

💧 Stay hydrated during lectures.

</li>

<li class="list-group-item">

🎯 Prepare for tomorrow's classes.

</li>

</ul>

</div>

</div>

</div>

</div>

<!-- ==========================================
        FOOTER
========================================== -->

<footer class="mt-5">

<div class="card border-0 shadow">

<div class="card-body text-center">

<h5 class="fw-bold text-primary">

Forces Academy LMS

</h5>

<p class="text-muted mb-1">

Smart Learning • Better Future

</p>

<small class="text-secondary">

© <?php echo date("Y"); ?> Forces Academy LMS. All Rights Reserved.

</small>

</div>

</div>

</footer>

</div>

<!-- END main-content -->

<style>

@media print{

.sidebar,
.navbar,
.btn,
footer{

display:none !important;

}

.main-content{

margin:0 !important;

padding:0 !important;

width:100% !important;

}

.card{

box-shadow:none !important;

border:1px solid #ddd !important;

}

body{

background:white !important;

}

}

</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>