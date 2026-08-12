<?php

session_start();

if (!isset($_SESSION['student_id'])) {
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
    PROFILE PHOTO
=========================================*/
$avatar = !empty($student['photo'])

?

"../uploads/students/".$student['photo']

:

"assets/images/avatar.png";

/*=========================================
    DASHBOARD COUNTS
=========================================*/

// Courses

$totalCourses = mysqli_fetch_assoc(

mysqli_query(

$conn,

"SELECT COUNT(*) total FROM courses"

)

)['total'];

// Assignments

$totalAssignments = mysqli_fetch_assoc(

mysqli_query(

$conn,

"SELECT COUNT(*) total FROM assignments"

)

)['total'];

// Notices

$totalNotices = mysqli_fetch_assoc(

mysqli_query(

$conn,

"SELECT COUNT(*) total FROM notices"

)

)['total'];

/*=========================================
    PERFORMANCE
=========================================*/

$performance = mysqli_fetch_assoc(

mysqli_query(

$conn,

"

SELECT

ROUND(

AVG((marks/total_marks)*100),

2

)

average

FROM results

WHERE student_id=$student_id

"

)

);

$average = $performance['average'] ?? 0;

/*=========================================
    GRADE
=========================================*/

if($average>=90){

$grade="A+";

}elseif($average>=80){

$grade="A";

}elseif($average>=70){

$grade="B";

}elseif($average>=60){

$grade="C";

}elseif($average>=50){

$grade="D";

}else{

$grade="F";

}

/*=========================================
    RECENT NOTICES
=========================================*/

$recentNotices = mysqli_query(

$conn,

"

SELECT *

FROM notices

ORDER BY created_at DESC

LIMIT 5

"

);

/*=========================================
    RECENT ASSIGNMENTS
=========================================*/

$recentAssignments = mysqli_query(

$conn,

"

SELECT *

FROM assignments

ORDER BY deadline ASC

LIMIT 5

"

);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1">

<title>

Student Dashboard

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
rel="stylesheet">

<link
href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<link
rel="stylesheet"
href="assets/css/dashboard.css">

<style>

body{
    margin:0;
    font-family:'Poppins',sans-serif;
    background:#f4f7fb;
}


.dashboard-container{

margin-left:250px;
padding:100px 30px 30px;
}

.hero-card{

background:linear-gradient(135deg,#2563eb,#3b82f6);

border-radius:25px;

padding:35px;

color:white;

box-shadow:0 15px 35px rgba(37,99,235,.25);

overflow:hidden;

position:relative;

}

.hero-card::before{

content:'';

position:absolute;

width:250px;

height:250px;

background:rgba(255,255,255,.08);

border-radius:50%;

right:-70px;

top:-70px;

}

.hero-avatar{

width:100px;

height:100px;

border-radius:50%;

border:5px solid rgba(255,255,255,.35);

object-fit:cover;

}

.stat-card{

border:none;

border-radius:20px;

color:white;

transition:.35s;

overflow:hidden;

}

.stat-card:hover{

transform:translateY(-8px);

}

/* MAIN AREA */


.main-content{

margin-left:250px;

padding:75px 25px 30px;


}


/* RESPONSIVE */


@media(max-width:900px){


.sidebar{

left:-250px;

}


.top-navbar{

left:0;

}


.main-content{

margin-left:0;

}



.sidebar.active{

left:0;

}

}
</style>

</head>

<body>
<?php include "sidebar.php"; ?>

<?php include "navbar.php"; ?>
 
<div class="dashboard-container">

<!-- ==========================================
        WELCOME HERO
========================================== -->

<div class="hero-card mb-4">

<div class="row align-items-center">

<div class="col-lg-8">

<span class="badge bg-light text-primary px-3 py-2 mb-3">

<i class="fas fa-graduation-cap me-2"></i>

Student Dashboard

</span>

<h1 class="fw-bold mb-3">

Welcome Back,

<?php echo htmlspecialchars($student['full_name']); ?>

👋

</h1>

<p class="mb-4 fs-5">

Continue your learning journey and stay updated with your assignments,
courses and academic progress.

</p>

<div class="d-flex flex-wrap gap-3">

<a href="courses.php" class="btn btn-light btn-lg">

<i class="fas fa-book-open me-2"></i>

My Courses

</a>

<a href="results.php" class="btn btn-outline-light btn-lg">

<i class="fas fa-chart-line me-2"></i>

View Results

</a>

<a href="assignments.php" class="btn btn-outline-light btn-lg">

<i class="fas fa-file-upload me-2"></i>

Assignments

</a>

</div>

</div>

<div class="col-lg-4 text-center">

<img

src="<?php echo $avatar; ?>"

class="hero-avatar shadow-lg">

<h4 class="mt-3">

<?php echo htmlspecialchars($student['full_name']); ?>

</h4>

<p>

<?php echo htmlspecialchars($student['email']); ?>

</p>

</div>

</div>

</div>

<!-- ==========================================
        DASHBOARD CARDS
========================================== -->

<div class="row g-4 mb-4">

<div class="col-lg-3 col-md-6">

<div class="card stat-card shadow bg-primary">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>

Total Courses

</h6>

<h2 class="fw-bold">

<?php echo $totalCourses; ?>

</h2>

<p class="mb-0">

Enrolled Courses

</p>

</div>

<div>

<i class="fas fa-book fa-3x opacity-75"></i>

</div>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card stat-card shadow bg-warning">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>

Assignments

</h6>

<h2 class="fw-bold">

<?php echo $totalAssignments; ?>

</h2>

<p class="mb-0">

Available Tasks

</p>

</div>

<div>

<i class="fas fa-file-alt fa-3x opacity-75"></i>

</div>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card stat-card shadow bg-danger">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>

Notices

</h6>

<h2 class="fw-bold">

<?php echo $totalNotices; ?>

</h2>

<p class="mb-0">

Latest Updates

</p>

</div>

<div>

<i class="fas fa-bullhorn fa-3x opacity-75"></i>

</div>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card stat-card shadow bg-success">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>

Performance

</h6>

<h2 class="fw-bold">

<?php echo $grade; ?>

</h2>

<p class="mb-0">

Average <?php echo $average; ?>%

</p>

</div>

<div>

<i class="fas fa-award fa-3x opacity-75"></i>

</div>

</div>

</div>

</div>

</div>

</div>

<!-- ==========================================
        QUICK ACTIONS
========================================== -->

<div class="card shadow border-0 rounded-4 mb-4">

<div class="card-header bg-white border-0 pt-4">

<h4>

<i class="fas fa-bolt text-warning me-2"></i>

Quick Actions

</h4>

</div>

<div class="card-body">

<div class="row text-center g-4">

<div class="col-lg-2 col-md-4 col-6">

<a href="courses.php" class="text-decoration-none text-dark">

<div class="p-4 rounded-4 bg-light shadow-sm">

<i class="fas fa-book fa-2x text-primary mb-3"></i>

<h6>Courses</h6>

</div>

</a>

</div>

<div class="col-lg-2 col-md-4 col-6">

<a href="assignments.php" class="text-decoration-none text-dark">

<div class="p-4 rounded-4 bg-light shadow-sm">

<i class="fas fa-file-upload fa-2x text-warning mb-3"></i>

<h6>Assignments</h6>

</div>

</a>

</div>

<div class="col-lg-2 col-md-4 col-6">

<a href="results.php" class="text-decoration-none text-dark">

<div class="p-4 rounded-4 bg-light shadow-sm">

<i class="fas fa-chart-line fa-2x text-success mb-3"></i>

<h6>Results</h6>

</div>

</a>

</div>

<div class="col-lg-2 col-md-4 col-6">

<a href="notices.php" class="text-decoration-none text-dark">

<div class="p-4 rounded-4 bg-light shadow-sm">

<i class="fas fa-bullhorn fa-2x text-danger mb-3"></i>

<h6>Notices</h6>

</div>

</a>

</div>

<div class="col-lg-2 col-md-4 col-6">

<a href="profile.php" class="text-decoration-none text-dark">

<div class="p-4 rounded-4 bg-light shadow-sm">

<i class="fas fa-user-circle fa-2x text-info mb-3"></i>

<h6>Profile</h6>

</div>

</a>

</div>

<div class="col-lg-2 col-md-4 col-6">

<a href="logout.php" class="text-decoration-none text-dark">

<div class="p-4 rounded-4 bg-light shadow-sm">

<i class="fas fa-sign-out-alt fa-2x text-secondary mb-3"></i>

<h6>Logout</h6>

</div>

</a>

</div>

</div>

</div>

</div>
<!-- ==========================================
        MAIN CONTENT
========================================== -->

<div class="row">

<!-- Recent Notices -->

<div class="col-lg-8">

<div class="card border-0 shadow rounded-4 mb-4">

<div class="card-header bg-primary text-white rounded-top-4">

<h5 class="mb-0">

<i class="fas fa-bullhorn me-2"></i>

Recent Notices

</h5>

</div>

<div class="card-body">

<?php

if(mysqli_num_rows($recentNotices)>0){

while($notice=mysqli_fetch_assoc($recentNotices)){

?>

<div class="border-start border-5 border-primary rounded p-3 mb-3 bg-light">

<div class="d-flex justify-content-between">

<h6 class="fw-bold">

<?php echo htmlspecialchars($notice['title']); ?>

</h6>

<small class="text-muted">

<?php echo date("d M Y",strtotime($notice['created_at'])); ?>

</small>

</div>

<p class="mb-1 text-muted">

<?php echo substr(strip_tags($notice['content']),0,180); ?>...

</p>

<small class="text-secondary">

Posted By:

<?php echo htmlspecialchars($notice['posted_by']); ?>

</small>

</div>

<?php

}

}else{

?>

<div class="alert alert-info">

No notices available.

</div>

<?php } ?>

</div>

</div>

<!-- Upcoming Assignments -->

<div class="card border-0 shadow rounded-4">

<div class="card-header bg-warning text-dark">

<h5 class="mb-0">

<i class="fas fa-file-alt me-2"></i>

Upcoming Assignments

</h5>

</div>

<div class="card-body">

<?php

if(mysqli_num_rows($recentAssignments)>0){

while($assignment=mysqli_fetch_assoc($recentAssignments)){

?>

<div class="d-flex justify-content-between align-items-center border-bottom py-3">

<div>

<h6 class="mb-1">

<?php echo htmlspecialchars($assignment['title']); ?>

</h6>

<small class="text-muted">

Due:

<?php echo date("d M Y",strtotime($assignment['deadline'])); ?>

</small>

</div>

<span class="badge bg-danger">

Pending

</span>

</div>

<?php

}

}else{

?>

<div class="alert alert-success">

No assignments available.

</div>

<?php } ?>

</div>

</div>

</div>

<!-- Right Sidebar -->

<div class="col-lg-4">

<!-- Student Profile -->

<div class="card border-0 shadow rounded-4 mb-4">

<div class="card-body text-center">

<img

src="<?php echo $avatar; ?>"

class="rounded-circle shadow"

width="120"

height="120"

style="object-fit:cover;">

<h4 class="mt-3">

<?php echo htmlspecialchars($student['full_name']); ?>

</h4>

<p class="text-muted">

<?php echo htmlspecialchars($student['email']); ?>

</p>

<hr>

<div class="row text-center">

<div class="col-6">

<h6>Class</h6>

<strong>

<?php echo htmlspecialchars($student['class']); ?>

</strong>

</div>

<div class="col-6">

<h6>Grade</h6>

<strong class="text-success">

<?php echo $grade; ?>

</strong>

</div>

</div>

<a

href="profile.php"

class="btn btn-primary w-100 mt-4">

<i class="fas fa-user-edit me-2"></i>

View Profile

</a>

</div>

</div>

<!-- Learning Progress -->

<div class="card border-0 shadow rounded-4">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-chart-line me-2"></i>

Learning Progress

</h5>

</div>

<div class="card-body">

<p class="mb-1">

Courses Completed

</p>

<div class="progress mb-3" style="height:10px;">

<div

class="progress-bar bg-primary"

style="width:85%">

</div>

</div>

<p class="mb-1">

Assignments Submitted

</p>

<div class="progress mb-3" style="height:10px;">

<div

class="progress-bar bg-warning"

style="width:70%">

</div>

</div>

<p class="mb-1">

Attendance

</p>

<div class="progress mb-3" style="height:10px;">

<div

class="progress-bar bg-success"

style="width:92%">

</div>

</div>

<p class="mb-1">

Overall Performance

</p>

<div class="progress" style="height:10px;">

<div

class="progress-bar bg-danger"

style="width:<?php echo $average; ?>%">

</div>

</div>

</div>

</div>

</div>

</div>
<!-- ==========================================
        CHARTS
========================================== -->

<div class="row mt-4">

<div class="col-lg-8">

<div class="card border-0 shadow rounded-4">

<div class="card-header bg-info text-white">

<h5 class="mb-0">

<i class="fas fa-chart-line me-2"></i>

Academic Performance

</h5>

</div>

<div class="card-body">

<canvas id="marksChart" height="120"></canvas>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card border-0 shadow rounded-4">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-chart-pie me-2"></i>

Course Progress

</h5>

</div>

<div class="card-body">

<canvas id="progressChart"></canvas>

</div>

</div>

</div>

</div>

<!-- ==========================================
        ACHIEVEMENTS
========================================== -->

<div class="row mt-4">

<div class="col-md-3">

<div class="card border-0 shadow text-center">

<div class="card-body">

<i class="fas fa-medal fa-3x text-warning mb-3"></i>

<h5>Excellent</h5>

<p class="text-muted">

Performance Grade

</p>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card border-0 shadow text-center">

<div class="card-body">

<i class="fas fa-book-reader fa-3x text-primary mb-3"></i>

<h5>

<?php echo $totalCourses; ?>

</h5>

<p class="text-muted">

Courses Enrolled

</p>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card border-0 shadow text-center">

<div class="card-body">

<i class="fas fa-file-upload fa-3x text-success mb-3"></i>

<h5>

<?php echo $totalAssignments; ?>

</h5>

<p class="text-muted">

Assignments

</p>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card border-0 shadow text-center">

<div class="card-body">

<i class="fas fa-award fa-3x text-danger mb-3"></i>

<h5>

<?php echo $grade; ?>

</h5>

<p class="text-muted">

Current Grade

</p>

</div>

</div>

</div>

</div>

<!-- ==========================================
        QUOTE & CALENDAR
========================================== -->

<div class="row mt-4">

<div class="col-lg-8">

<div class="card border-0 shadow rounded-4">

<div class="card-header bg-warning">

<h5 class="mb-0">

⭐ Quote of the Day

</h5>

</div>

<div class="card-body">

<blockquote class="blockquote mb-0">

<p>

"The beautiful thing about learning is that nobody can take it away from you."

</p>

<footer class="blockquote-footer mt-2">

B.B. King

</footer>

</blockquote>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card border-0 shadow rounded-4">

<div class="card-header bg-secondary text-white">

<h5 class="mb-0">

<i class="fas fa-calendar-alt me-2"></i>

Today

</h5>

</div>

<div class="card-body text-center">

<h1>

<?php echo date("d"); ?>

</h1>

<h5>

<?php echo date("F Y"); ?>

</h5>

<p class="text-muted">

<?php echo date("l"); ?>

</p>

<hr>

<p>

Keep completing your assignments on time and check notices daily.

</p>

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

<p class="mb-1">

© <?php echo date("Y"); ?>

<strong>Forces Academy LMS</strong>

</p>

<small class="text-muted">

Empowering Students Through Smart Learning

</small>

</div>

</div>

</footer>

</div>

<!-- ==========================================
        CHART JS
========================================== -->

<script>

const marksChart = new Chart(

document.getElementById('marksChart'),

{

type:'line',

data:{

labels:['Quiz','Assignment','Mid','Project','Final'],

datasets:[{

label:'Marks (%)',

data:[72,80,78,90,<?php echo round($average); ?>],

borderColor:'#2563eb',

backgroundColor:'rgba(37,99,235,.15)',

fill:true,

tension:.4

}]

},

options:{

responsive:true,

plugins:{

legend:{

display:true

}

}

}

}

);

const progressChart = new Chart(

document.getElementById('progressChart'),

{

type:'doughnut',

data:{

labels:[

'Completed',

'Remaining'

],

datasets:[{

data:[85,15],

backgroundColor:[

'#22c55e',

'#e5e7eb'

]

}]

},

options:{

responsive:true,

plugins:{

legend:{

position:'bottom'

}

}

}

}

);

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>