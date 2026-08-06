<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/*=========================================
    DASHBOARD REPORT COUNTS
=========================================*/

$totalStudents = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM students")
)['total'];

$totalTeachers = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM teachers")
)['total'];

$totalCourses = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM courses")
)['total'];

$totalAssignments = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM assignments")
)['total'];

$totalResults = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM results")
)['total'];

$totalNotices = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM notices")
)['total'];

$totalTimetable = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM timetable")
)['total'];
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Reports | Forces Academy LMS</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

body{
    background:#eef3f9;
    font-family:'Poppins',sans-serif;
}

.report-card{
    border:none;
    border-radius:18px;
    color:#fff;
    transition:.3s;
}

.report-card:hover{
    transform:translateY(-6px);
    box-shadow:0 15px 35px rgba(0,0,0,.18);
}

.icon-box{
    width:65px;
    height:65px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    background:rgba(255,255,255,.2);
}

</style>

</head>

<body>

<div class="container-fluid py-4">

<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h2 class="fw-bold">

<i class="fas fa-chart-bar text-primary me-2"></i>

Reports Dashboard

</h2>

<p class="text-muted">

View complete academy statistics and reports.

</p>

</div>

<div>

<a href="dashboard.php" class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Dashboard

</a>

</div>

</div>

<div class="row g-4">

<!-- Students -->

<div class="col-lg-3 col-md-6">

<div class="card report-card bg-primary">

<div class="card-body d-flex justify-content-between align-items-center">

<div>

<h6>Total Students</h6>

<h2 class="fw-bold">

<?php echo $totalStudents; ?>

</h2>

</div>

<div class="icon-box">

<i class="fas fa-user-graduate fa-2x"></i>

</div>

</div>

</div>

</div>

<!-- Teachers -->

<div class="col-lg-3 col-md-6">

<div class="card report-card bg-success">

<div class="card-body d-flex justify-content-between align-items-center">

<div>

<h6>Total Teachers</h6>

<h2 class="fw-bold">

<?php echo $totalTeachers; ?>

</h2>

</div>

<div class="icon-box">

<i class="fas fa-chalkboard-teacher fa-2x"></i>

</div>

</div>

</div>

</div>

<!-- Courses -->

<div class="col-lg-3 col-md-6">

<div class="card report-card bg-warning">

<div class="card-body d-flex justify-content-between align-items-center">

<div>

<h6>Total Courses</h6>

<h2 class="fw-bold">

<?php echo $totalCourses; ?>

</h2>

</div>

<div class="icon-box">

<i class="fas fa-book-open fa-2x"></i>

</div>

</div>

</div>

</div>

<!-- Results -->

<div class="col-lg-3 col-md-6">

<div class="card report-card bg-danger">

<div class="card-body d-flex justify-content-between align-items-center">

<div>

<h6>Total Results</h6>

<h2 class="fw-bold">

<?php echo $totalResults; ?>

</h2>

</div>

<div class="icon-box">

<i class="fas fa-chart-line fa-2x"></i>

</div>

</div>

</div>

</div>

<!-- Assignments -->

<div class="col-lg-3 col-md-6">

<div class="card report-card bg-info">

<div class="card-body d-flex justify-content-between align-items-center">

<div>

<h6>Assignments</h6>

<h2 class="fw-bold">

<?php echo $totalAssignments; ?>

</h2>

</div>

<div class="icon-box">

<i class="fas fa-file-alt fa-2x"></i>

</div>

</div>

</div>

</div>

<!-- Notices -->

<div class="col-lg-3 col-md-6">

<div class="card report-card bg-secondary">

<div class="card-body d-flex justify-content-between align-items-center">

<div>

<h6>Notices</h6>

<h2 class="fw-bold">

<?php echo $totalNotices; ?>

</h2>

</div>

<div class="icon-box">

<i class="fas fa-bullhorn fa-2x"></i>

</div>

</div>

</div>

</div>

<!-- Timetable -->

<div class="col-lg-3 col-md-6">

<div class="card report-card bg-dark">

<div class="card-body d-flex justify-content-between align-items-center">

<div>

<h6>Timetables</h6>

<h2 class="fw-bold">

<?php echo $totalTimetable; ?>

</h2>

</div>

<div class="icon-box">

<i class="fas fa-calendar-alt fa-2x"></i>

</div>

</div>

</div>

</div>

<!-- Academy -->

<div class="col-lg-3 col-md-6">

<div class="card report-card" style="background:#7c3aed;">

<div class="card-body d-flex justify-content-between align-items-center">

<div>

<h6>Academy Status</h6>

<h4 class="fw-bold">

Active

</h4>

</div>

<div class="icon-box">

<i class="fas fa-school fa-2x"></i>

</div>

</div>

</div>

</div>

</div>
<?php
/*=========================================
    RECENT STUDENTS
=========================================*/

$recentStudents = mysqli_query($conn,"
SELECT *
FROM students
ORDER BY id DESC
LIMIT 5
");

/*=========================================
    RECENT ASSIGNMENTS
=========================================*/

$recentAssignments = mysqli_query($conn,"
SELECT *
FROM assignments
ORDER BY id DESC
LIMIT 5
");
?>

<!-- =========================================
        CHARTS
========================================== -->

<div class="row mt-4">

    <div class="col-lg-8">

        <div class="card shadow border-0 rounded-4">

            <div class="card-header bg-primary text-white">

                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Student Performance Overview
                </h5>

            </div>

            <div class="card-body">

                <canvas id="studentChart" height="110"></canvas>

            </div>

        </div>

    </div>

    <div class="col-lg-4">

        <div class="card shadow border-0 rounded-4">

            <div class="card-header bg-success text-white">

                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Academy Statistics
                </h5>

            </div>

            <div class="card-body">

                <canvas id="academyChart"></canvas>

            </div>

        </div>

    </div>

</div>

<!-- =========================================
        TABLES
========================================== -->

<div class="row mt-4">

    <!-- Recent Students -->

    <div class="col-lg-6">

        <div class="card shadow border-0 rounded-4">

            <div class="card-header bg-dark text-white">

                <h5 class="mb-0">

                    <i class="fas fa-user-graduate me-2"></i>

                    Recent Students

                </h5>

            </div>

            <div class="card-body p-0">

                <table class="table table-hover mb-0">

                    <thead class="table-light">

                        <tr>

                            <th>Name</th>

                            <th>Email</th>

                            <th>Class</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php

                    if(mysqli_num_rows($recentStudents)>0){

                        while($student=mysqli_fetch_assoc($recentStudents)){

                    ?>

                    <tr>

                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>

                        <td><?php echo htmlspecialchars($student['email']); ?></td>

                        <td><?php echo htmlspecialchars($student['class']); ?></td>

                    </tr>

                    <?php

                        }

                    }else{

                    ?>

                    <tr>

                        <td colspan="3" class="text-center">

                            No Students Found

                        </td>

                    </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <!-- Recent Assignments -->

    <div class="col-lg-6">

        <div class="card shadow border-0 rounded-4">

            <div class="card-header bg-warning">

                <h5 class="mb-0">

                    <i class="fas fa-file-alt me-2"></i>

                    Recent Assignments

                </h5>

            </div>

            <div class="card-body p-0">

                <table class="table table-hover mb-0">

                    <thead class="table-light">

                        <tr>

                            <th>Title</th>

                            <th>Course</th>

                            <th>Deadline</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php

                    if(mysqli_num_rows($recentAssignments)>0){

                        while($assignment=mysqli_fetch_assoc($recentAssignments)){

                    ?>

                    <tr>

                        <td><?php echo htmlspecialchars($assignment['title']); ?></td>

                        <td><?php echo htmlspecialchars($assignment['course_name'] ?? '-'); ?></td>

                        <td>

                            <?php echo date("d M Y",strtotime($assignment['deadline'])); ?>

                        </td>

                    </tr>

                    <?php

                        }

                    }else{

                    ?>

                    <tr>

                        <td colspan="3" class="text-center">

                            No Assignments Found

                        </td>

                    </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<!-- =========================================
        CHART JS
========================================== -->

<script>

const studentChart = new Chart(

document.getElementById('studentChart'),

{

type:'line',

data:{

labels:['Jan','Feb','Mar','Apr','May','Jun'],

datasets:[{

label:'Student Growth',

data:[20,35,50,68,90,110],

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

const academyChart = new Chart(

document.getElementById('academyChart'),

{

type:'doughnut',

data:{

labels:[

'Students',

'Teachers',

'Courses',

'Results'

],

datasets:[{

data:[

<?php echo $totalStudents; ?>,

<?php echo $totalTeachers; ?>,

<?php echo $totalCourses; ?>,

<?php echo $totalResults; ?>

],

backgroundColor:[

'#2563eb',

'#22c55e',

'#f59e0b',

'#ef4444'

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
<?php
/*=========================================
    TOP PERFORMING STUDENTS
=========================================*/

$topStudents = mysqli_query($conn,"
SELECT
students.full_name,
students.class,
ROUND(AVG((results.marks/results.total_marks)*100),2) average
FROM results
INNER JOIN students
ON results.student_id=students.id
GROUP BY students.id
ORDER BY average DESC
LIMIT 10
");

/*=========================================
    COURSE SUMMARY
=========================================*/

$courseSummary = mysqli_query($conn,"
SELECT
courses.course_name,
COUNT(assignments.id) assignments
FROM courses
LEFT JOIN assignments
ON assignments.course_id=courses.id
GROUP BY courses.id
");
?>

<!-- ===========================
REPORT ACTION BUTTONS
=========================== -->

<div class="card shadow border-0 rounded-4 mt-4">

<div class="card-body text-center">

<a href="#" onclick="window.print()" class="btn btn-primary me-2">

<i class="fas fa-print me-2"></i>

Print Report

</a>

<a href="export_pdf.php" class="btn btn-danger me-2">

<i class="fas fa-file-pdf me-2"></i>

Export PDF

</a>

<a href="export_excel.php" class="btn btn-success">

<i class="fas fa-file-excel me-2"></i>

Export Excel

</a>

</div>

</div>

<!-- ===========================
TOP STUDENTS
=========================== -->

<div class="row mt-4">

<div class="col-lg-6">

<div class="card shadow border-0 rounded-4">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-trophy me-2"></i>

Top Performing Students

</h5>

</div>

<div class="card-body p-0">

<table class="table table-hover mb-0">

<thead class="table-light">

<tr>

<th>#</th>

<th>Name</th>

<th>Class</th>

<th>Average</th>

</tr>

</thead>

<tbody>

<?php

$rank=1;

if(mysqli_num_rows($topStudents)>0){

while($student=mysqli_fetch_assoc($topStudents)){

?>

<tr>

<td><?php echo $rank++; ?></td>

<td><?php echo htmlspecialchars($student['full_name']); ?></td>

<td><?php echo htmlspecialchars($student['class']); ?></td>

<td>

<span class="badge bg-success">

<?php echo $student['average']; ?>%

</span>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="4" class="text-center">

No Results Found

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

<!-- ===========================
COURSE SUMMARY
=========================== -->

<div class="col-lg-6">

<div class="card shadow border-0 rounded-4">

<div class="card-header bg-info text-white">

<h5 class="mb-0">

<i class="fas fa-book-open me-2"></i>

Course Summary

</h5>

</div>

<div class="card-body p-0">

<table class="table table-hover mb-0">

<thead class="table-light">

<tr>

<th>Course</th>

<th>Assignments</th>

</tr>

</thead>

<tbody>

<?php

if(mysqli_num_rows($courseSummary)>0){

while($course=mysqli_fetch_assoc($courseSummary)){

?>

<tr>

<td>

<?php echo htmlspecialchars($course['course_name']); ?>

</td>

<td>

<span class="badge bg-primary">

<?php echo $course['assignments']; ?>

</span>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="2" class="text-center">

No Courses Found

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

<!-- ===========================
REPORT SUMMARY
=========================== -->

<div class="card shadow border-0 rounded-4 mt-4">

<div class="card-header bg-dark text-white">

<h5 class="mb-0">

<i class="fas fa-chart-bar me-2"></i>

Academy Report Summary

</h5>

</div>

<div class="card-body">

<div class="row text-center">

<div class="col-md-3">

<h3 class="text-primary">

<?php echo $totalStudents; ?>

</h3>

<p>Total Students</p>

</div>

<div class="col-md-3">

<h3 class="text-success">

<?php echo $totalTeachers; ?>

</h3>

<p>Total Teachers</p>

</div>

<div class="col-md-3">

<h3 class="text-warning">

<?php echo $totalCourses; ?>

</h3>

<p>Total Courses</p>

</div>

<div class="col-md-3">

<h3 class="text-danger">

<?php echo $totalResults; ?>

</h3>

<p>Total Results</p>

</div>

</div>

</div>

</div>
<?php
/*=========================================
    MONTHLY STUDENT REGISTRATION
=========================================*/

$monthlyStudents = mysqli_query($conn,"
SELECT
MONTH(created_at) month_no,
COUNT(*) total
FROM students
GROUP BY MONTH(created_at)
ORDER BY MONTH(created_at)
");

$months=[];
$studentCounts=[];

while($row=mysqli_fetch_assoc($monthlyStudents)){
    $months[] = date("M",mktime(0,0,0,$row['month_no'],1));
    $studentCounts[] = $row['total'];
}

/*=========================================
    GRADE DISTRIBUTION
=========================================*/

$a = mysqli_num_rows(mysqli_query($conn,"
SELECT *
FROM results
WHERE (marks/total_marks)*100>=80
"));

$b = mysqli_num_rows(mysqli_query($conn,"
SELECT *
FROM results
WHERE (marks/total_marks)*100>=60
AND (marks/total_marks)*100<80
"));

$c = mysqli_num_rows(mysqli_query($conn,"
SELECT *
FROM results
WHERE (marks/total_marks)*100>=40
AND (marks/total_marks)*100<60
"));

$f = mysqli_num_rows(mysqli_query($conn,"
SELECT *
FROM results
WHERE (marks/total_marks)*100<40
"));
?>

<!-- =========================================
MONTHLY REPORT
========================================== -->

<div class="row mt-4">

<div class="col-lg-8">

<div class="card shadow border-0 rounded-4">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-chart-area me-2"></i>

Monthly Student Registration

</h5>

</div>

<div class="card-body">

<canvas id="monthlyChart" height="110"></canvas>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card shadow border-0 rounded-4">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-award me-2"></i>

Grade Distribution

</h5>

</div>

<div class="card-body">

<canvas id="gradeChart"></canvas>

</div>

</div>

</div>

</div>

<!-- =========================================
ATTENDANCE SUMMARY
========================================== -->

<div class="card shadow border-0 rounded-4 mt-4">

<div class="card-header bg-info text-white">

<h5 class="mb-0">

<i class="fas fa-calendar-check me-2"></i>

Attendance Summary

</h5>

</div>

<div class="card-body">

<div class="row text-center">

<div class="col-md-3">

<h2 class="text-success">95%</h2>

<p>Overall Attendance</p>

</div>

<div class="col-md-3">

<h2 class="text-primary">90%</h2>

<p>Students</p>

</div>

<div class="col-md-3">

<h2 class="text-warning">97%</h2>

<p>Teachers</p>

</div>

<div class="col-md-3">

<h2 class="text-danger">6</h2>

<p>Absent Today</p>

</div>

</div>

</div>

</div>

<!-- =========================================
SYSTEM INFORMATION
========================================== -->

<div class="card shadow border-0 rounded-4 mt-4">

<div class="card-header bg-dark text-white">

<h5 class="mb-0">

<i class="fas fa-server me-2"></i>

System Information

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-3">

<strong>PHP Version</strong><br>

<?php echo phpversion(); ?>

</div>

<div class="col-md-3">

<strong>MySQL</strong><br>

<?php echo mysqli_get_server_info($conn); ?>

</div>

<div class="col-md-3">

<strong>Server Time</strong><br>

<?php echo date("d M Y h:i A"); ?>

</div>

<div class="col-md-3">

<strong>Status</strong><br>

<span class="badge bg-success">

Online

</span>

</div>

</div>

</div>

</div>

<!-- =========================================
FOOTER
========================================== -->

<footer class="mt-5">

<div class="card shadow border-0">

<div class="card-body text-center">

<p class="mb-1">

© <?php echo date("Y"); ?>

<b>Forces Academy LMS</b>

</p>

<small class="text-muted">

Professional Reporting Module

</small>

</div>

</div>

</footer>

</div>

<!-- =========================================
CHART JS
========================================== -->

<script>

const monthlyChart = new Chart(
document.getElementById('monthlyChart'),
{
type:'bar',
data:{
labels:<?php echo json_encode($months); ?>,
datasets:[{
label:'New Students',
data:<?php echo json_encode($studentCounts); ?>,
backgroundColor:'#2563eb'
}]
},
options:{
responsive:true
}
});

const gradeChart = new Chart(
document.getElementById('gradeChart'),
{
type:'doughnut',
data:{
labels:['A Grade','B Grade','C Grade','Fail'],
datasets:[{
data:[
<?php echo $a; ?>,
<?php echo $b; ?>,
<?php echo $c; ?>,
<?php echo $f; ?>
],
backgroundColor:[
'#22c55e',
'#3b82f6',
'#f59e0b',
'#ef4444'
]
}]
},
options:{
responsive:true
}
});

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>