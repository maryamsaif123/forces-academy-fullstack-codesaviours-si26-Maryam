<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/*=========================================
    DASHBOARD STATISTICS
=========================================*/

// Total Students
$totalStudents = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM students")
)['total'];

// Total Courses
$totalCourses = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM courses")
)['total'];

// Total Results
$totalResults = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM results")
)['total'];

// Average Marks
$averageMarks = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT ROUND(AVG((marks/total_marks)*100),2) avg_marks
         FROM results"
    )
);

$average = $averageMarks['avg_marks'] ?? 0;

/*=========================================
    GRADE DISTRIBUTION
=========================================*/

$grades=[];

$query=mysqli_query(

$conn,

"SELECT grade,COUNT(*) total
FROM results
GROUP BY grade"

);

while($row=mysqli_fetch_assoc($query)){

$grades[$row['grade']]=$row['total'];

}

$aPlus=$grades['A+'] ?? 0;
$a=$grades['A'] ?? 0;
$b=$grades['B'] ?? 0;
$c=$grades['C'] ?? 0;
$d=$grades['D'] ?? 0;
$f=$grades['F'] ?? 0;

/*=========================================
    TOP STUDENTS
=========================================*/

$topStudents=mysqli_query(

$conn,

"

SELECT

students.full_name,
students.roll_number,

ROUND(AVG((marks/total_marks)*100),2)
average

FROM results

INNER JOIN students

ON students.id=results.student_id

GROUP BY student_id

ORDER BY average DESC

LIMIT 10

"

);

/*=========================================
    COURSE PERFORMANCE
=========================================*/

$coursePerformance=mysqli_query(

$conn,

"

SELECT

courses.course_name,

ROUND(
AVG((marks/total_marks)*100),
2
)

average

FROM results

LEFT JOIN courses

ON courses.id=results.course_id

GROUP BY course_id

ORDER BY average DESC

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

Analytics Dashboard

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

<style>

body{

background:#f4f7fb;

font-family:'Poppins',sans-serif;

}

.card{

border:none;

border-radius:18px;

box-shadow:0 10px 25px rgba(0,0,0,.08);

}

.stats-card{

transition:.3s;

}

.stats-card:hover{

transform:translateY(-5px);

}

</style>

</head>

<body>

<div class="container py-5">
<!-- ==========================================
        PAGE HEADER
========================================== -->

<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h2 class="fw-bold">

<i class="fas fa-chart-line text-primary me-2"></i>

Results Analytics Dashboard

</h2>

<p class="text-muted">

Academic performance overview of the entire LMS.

</p>

</div>

<a href="manage_results.php" class="btn btn-primary">

<i class="fas fa-arrow-left me-2"></i>

Back to Results

</a>

</div>

<!-- ==========================================
        STATISTICS CARDS
========================================== -->

<div class="row g-4 mb-4">

<div class="col-md-3">

<div class="card stats-card bg-primary text-white">

<div class="card-body">

<h6>Total Students</h6>

<h2>

<?php echo $totalStudents; ?>

</h2>

<i class="fas fa-user-graduate fa-2x float-end"></i>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card stats-card bg-success text-white">

<div class="card-body">

<h6>Total Courses</h6>

<h2>

<?php echo $totalCourses; ?>

</h2>

<i class="fas fa-book fa-2x float-end"></i>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card stats-card bg-warning text-dark">

<div class="card-body">

<h6>Total Results</h6>

<h2>

<?php echo $totalResults; ?>

</h2>

<i class="fas fa-file-alt fa-2x float-end"></i>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card stats-card bg-danger text-white">

<div class="card-body">

<h6>Average Score</h6>

<h2>

<?php echo $average; ?>%

</h2>

<i class="fas fa-chart-bar fa-2x float-end"></i>

</div>

</div>

</div>

</div>

<!-- ==========================================
        CHARTS
========================================== -->

<div class="row">

<div class="col-lg-6">

<div class="card mb-4">

<div class="card-header">

<h5 class="mb-0">

Grade Distribution

</h5>

</div>

<div class="card-body">

<canvas id="gradeChart" height="250"></canvas>

</div>

</div>

</div>

<div class="col-lg-6">

<div class="card mb-4">

<div class="card-header">

<h5 class="mb-0">

Course Performance

</h5>

</div>

<div class="card-body">

<canvas id="courseChart" height="250"></canvas>

</div>

</div>

</div>

</div>

<!-- ==========================================
        TOP STUDENTS
========================================== -->

<div class="card mb-4">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fas fa-trophy me-2"></i>

Top 10 Students

</h5>

</div>

<div class="table-responsive">

<table class="table table-hover mb-0">

<thead>

<tr>

<th>#</th>

<th>Student Name</th>

<th>Roll Number</th>

<th>Average %</th>

</tr>

</thead>

<tbody>

<?php

$rank=1;

while($student=mysqli_fetch_assoc($topStudents)){

?>

<tr>

<td>

<?php echo $rank++; ?>

</td>

<td>

<?php echo htmlspecialchars($student['full_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($student['roll_number']); ?>

</td>

<td>

<span class="badge bg-success">

<?php echo $student['average']; ?>%

</span>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

<!-- ==========================================
        COURSE PERFORMANCE
========================================== -->

<div class="card">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-book-open me-2"></i>

Course Performance

</h5>

</div>

<div class="table-responsive">

<table class="table table-bordered table-hover mb-0">

<thead>

<tr>

<th>#</th>

<th>Course</th>

<th>Average Score</th>

</tr>

</thead>

<tbody>

<?php

$count=1;

$courseLabels=[];

$courseData=[];

mysqli_data_seek($coursePerformance,0);

while($course=mysqli_fetch_assoc($coursePerformance)){

$courseLabels[]=$course['course_name'];

$courseData[]=$course['average'];

?>

<tr>

<td>

<?php echo $count++; ?>

</td>

<td>

<?php echo htmlspecialchars($course['course_name']); ?>

</td>

<td>

<?php echo $course['average']; ?>%

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>
<!-- ==========================================
        RECENT RESULTS
========================================== -->

<?php

$recentResults = mysqli_query(

$conn,

"

SELECT

students.full_name,

results.subject,

results.marks,

results.total_marks,

results.grade,

results.created_at

FROM results

INNER JOIN students

ON students.id = results.student_id

ORDER BY results.created_at DESC

LIMIT 8

"

);

?>

<div class="card mt-4">

<div class="card-header bg-dark text-white">

<h5 class="mb-0">

<i class="fas fa-clock me-2"></i>

Recent Results

</h5>

</div>

<div class="table-responsive">

<table class="table table-hover mb-0">

<thead>

<tr>

<th>Student</th>

<th>Subject</th>

<th>Marks</th>

<th>Grade</th>

<th>Date</th>

</tr>

</thead>

<tbody>

<?php while($recent=mysqli_fetch_assoc($recentResults)){ ?>

<tr>

<td>

<?php echo htmlspecialchars($recent['full_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($recent['subject']); ?>

</td>

<td>

<?php echo $recent['marks']; ?>

/

<?php echo $recent['total_marks']; ?>

</td>

<td>

<span class="badge bg-success">

<?php echo $recent['grade']; ?>

</span>

</td>

<td>

<?php echo date("d M Y",strtotime($recent['created_at'])); ?>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

<!-- ==========================================
        CHART.JS
========================================== -->

<script>

const gradeChart=new Chart(

document.getElementById('gradeChart'),

{

type:'pie',

data:{

labels:[

'A+',

'A',

'B',

'C',

'D',

'F'

],

datasets:[{

data:[

<?php echo $aPlus; ?>,

<?php echo $a; ?>,

<?php echo $b; ?>,

<?php echo $c; ?>,

<?php echo $d; ?>,

<?php echo $f; ?>

],

backgroundColor:[

'#198754',

'#0d6efd',

'#20c997',

'#ffc107',

'#6c757d',

'#dc3545'

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

const courseChart=new Chart(

document.getElementById('courseChart'),

{

type:'bar',

data:{

labels:

<?php echo json_encode($courseLabels); ?>,

datasets:[{

label:'Average %',

data:

<?php echo json_encode($courseData); ?>,

backgroundColor:'#0d6efd',

borderRadius:8

}]

},

options:{

responsive:true,

scales:{

y:{

beginAtZero:true,

max:100

}

},

plugins:{

legend:{

display:false

}

}

}

}

);

// Auto refresh every 5 minutes

setTimeout(function(){

location.reload();

},300000);

</script>

</body>

</html>