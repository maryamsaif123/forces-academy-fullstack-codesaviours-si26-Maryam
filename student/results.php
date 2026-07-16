<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$student_id = $_SESSION['student_id'];

$query = mysqli_query($conn,"
SELECT
submissions.*,
assignments.title,
courses.course_name
FROM submissions
INNER JOIN assignments
ON submissions.assignment_id = assignments.id
LEFT JOIN courses
ON assignments.course_id = courses.id
WHERE submissions.student_id='$student_id'
AND submissions.status='graded'
ORDER BY submissions.submitted_at DESC
");

$top = mysqli_query($conn,"
SELECT
    students.id,
    students.full_name,
    students.email,
    ROUND(AVG(results.marks),2) AS average_marks
FROM students
LEFT JOIN results
ON students.id = results.student_id
GROUP BY students.id
ORDER BY average_marks DESC
LIMIT 5
");

$totalResults = mysqli_num_rows($query);
?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>My Results</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="dashboard.css">

<style>

.page-header{

background:linear-gradient(135deg,#198754,#22c55e);

padding:35px;

border-radius:20px;

display:flex;

justify-content:space-between;

align-items:center;

color:white;

margin-bottom:30px;

}

.result-card{

border:none;

border-radius:20px;

box-shadow:0 10px 25px rgba(0,0,0,.1);

transition:.3s;

margin-bottom:25px;

}

.result-card:hover{

transform:translateY(-6px);

}

.result-icon{

font-size:55px;

color:#198754;

}
.result-card{

overflow:hidden;

transition:.3s;

}

.result-card:hover{

box-shadow:0 20px 40px rgba(0,0,0,.15);

}

.result-card h4{

font-weight:700;

}

.result-icon{

font-size:60px;

color:#198754;

margin-bottom:15px;

}

.badge{

padding:8px 14px;

font-size:13px;

}

.form-control{

height:55px;

border-radius:12px;

}

@media(max-width:768px){

.page-header{

flex-direction:column;

text-align:center;

}

}

</style>

</head>

<body>

<?php include("sidebar.php"); ?>

<div class="main-content">

<?php include("navbar.php"); ?>

<div class="container-fluid">

<div class="page-header">

<div>

<h2>

<i class="fas fa-chart-line"></i>

My Results

</h2>

<p>

View your assignment grades and teacher feedback.

</p>

</div>

<div>

<span class="badge bg-light text-success fs-5">

<?php echo $totalResults; ?> Results

</span>

</div>

</div>

<div class="row">
<?php

$rank=1;

while($student=mysqli_fetch_assoc($top))
{

$avg=$student['average_marks'];

if($avg==""){

$avg=0;

}

if($avg>=90){

$grade="A+";

$badge="🥇";

$color="success";

}elseif($avg>=80){

$grade="A";

$badge="🥈";

$color="primary";

}elseif($avg>=70){

$grade="B";

$badge="🥉";

$color="warning";

}else{

$grade="C";

$badge="🎓";

$color="secondary";

}

?>

<div class="student-rank-card">

<div class="d-flex align-items-center">

<img

src="https://cdn-icons-png.flaticon.com/512/4140/4140048.png"

class="student-avatar">

<div class="ms-3">

<h5 class="mb-1">

<?php echo $badge; ?>

Rank #<?php echo $rank; ?>

</h5>

<h6 class="mb-0">

<?php echo $student['full_name']; ?>

</h6>

<small>

<?php echo $student['email']; ?>

</small>

</div>

<div class="ms-auto text-end">

<div class="badge bg-<?php echo $color; ?> fs-6">

<?php echo $grade; ?>

</div>

<h4 class="mt-2">

<?php echo $avg; ?>%

</h4>

</div>

</div>

</div>

<?php

$rank++;

}

?>

<div class="col-lg-6">

<div class="card result-card">

<div class="card-body">

<div class="d-flex justify-content-between">

<i class="fas fa-award result-icon"></i>

<span class="badge bg-<?php echo $badge; ?> fs-6">

<?php echo $grade; ?>

</span>

</div>

<hr>

<h4 class="fw-bold">

<?php echo htmlspecialchars($row['title']); ?>

</h4>

<p class="text-muted">

Course:

<strong>

<?php echo htmlspecialchars($row['course_name']); ?>

</strong>

</p>

<div class="row text-center">

<div class="col-4">

<h6>Marks</h6>

<h3 class="text-success">

<?php echo $row['marks']; ?>/100

</h3>

</div>

<div class="col-4">

<h6>Status</h6>

<span class="badge bg-success">

Graded

</span>

</div>

<div class="col-4">

<h6>Date</h6>

<small>

<?php echo date("d M Y",strtotime($row['submitted_at'])); ?>

</small>

</div>

</div>

<hr>

<h6 class="text-success">

Teacher Feedback

</h6>

<p>

<?php echo nl2br(htmlspecialchars($row['feedback'])); ?>

</p>

</div>

</div>

</div>

<?php

?>

</div>

<?php include("footer.php"); ?>

</div>

<script>

// ==========================
// Search Box
// ==========================

const search=document.createElement("input");

search.className="form-control form-control-lg mb-4";

search.placeholder="🔍 Search Results";

document.querySelector(".container-fluid").insertBefore(

search,

document.querySelector(".row")

);

search.addEventListener("keyup",function(){

let value=this.value.toLowerCase();

let cards=document.querySelectorAll(".result-card");

cards.forEach(function(card){

card.style.display=card.innerText.toLowerCase().includes(value)

? ""

: "none";

});

});

// ==========================
// Hover Animation
// ==========================

document.querySelectorAll(".result-card").forEach(function(card){

card.addEventListener("mouseenter",function(){

this.style.transform="translateY(-8px)";

this.style.transition=".3s";

});

card.addEventListener("mouseleave",function(){

this.style.transform="translateY(0px)";

});

});

</script>

</body>

</html>

