<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/*=========================================
    LOAD STUDENTS
=========================================*/

$students = mysqli_query(
    $conn,
    "SELECT id, full_name, roll_number
     FROM students
     ORDER BY full_name ASC"
);

$student = null;
$results = null;
$overallPercentage = 0;
$overallGrade = "N/A";

if (isset($_GET['student_id']) && is_numeric($_GET['student_id'])) {

    $student_id = (int)$_GET['student_id'];

    /*=========================================
        STUDENT DETAILS
    =========================================*/

    $studentQuery = mysqli_prepare(
        $conn,
        "SELECT *
         FROM students
         WHERE id=?
         LIMIT 1"
    );

    mysqli_stmt_bind_param($studentQuery,"i",$student_id);
    mysqli_stmt_execute($studentQuery);

    $studentResult = mysqli_stmt_get_result($studentQuery);

    if(mysqli_num_rows($studentResult)>0){

        $student = mysqli_fetch_assoc($studentResult);

    }

    /*=========================================
        ALL RESULTS
    =========================================*/

    $resultQuery = mysqli_prepare(

        $conn,

        "SELECT

        results.*,

        courses.course_name

        FROM results

        LEFT JOIN courses

        ON courses.id=results.course_id

        WHERE student_id=?

        ORDER BY created_at DESC"

    );

    mysqli_stmt_bind_param(
        $resultQuery,
        "i",
        $student_id
    );

    mysqli_stmt_execute($resultQuery);

    $results = mysqli_stmt_get_result($resultQuery);

    /*=========================================
        OVERALL PERCENTAGE
    =========================================*/

    $summary = mysqli_prepare(

        $conn,

        "SELECT

        SUM(marks) obtained,

        SUM(total_marks) total

        FROM results

        WHERE student_id=?"

    );

    mysqli_stmt_bind_param(
        $summary,
        "i",
        $student_id
    );

    mysqli_stmt_execute($summary);

    $summaryResult = mysqli_stmt_get_result($summary);

    $summaryData = mysqli_fetch_assoc($summaryResult);

    if($summaryData['total']>0){

        $overallPercentage = round(

            ($summaryData['obtained']/$summaryData['total'])*100,

            2

        );

    }

    /*=========================================
        OVERALL GRADE
    =========================================*/

    if($overallPercentage>=90){

        $overallGrade="A+";

    }elseif($overallPercentage>=80){

        $overallGrade="A";

    }elseif($overallPercentage>=70){

        $overallGrade="B";

    }elseif($overallPercentage>=60){

        $overallGrade="C";

    }elseif($overallPercentage>=50){

        $overallGrade="D";

    }else{

        $overallGrade="F";

    }

}
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>

Student Report Card

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

<style>

body{

background:#f4f7fb;

font-family:'Poppins',sans-serif;

}

.card{

border:none;

border-radius:20px;

box-shadow:0 10px 30px rgba(0,0,0,.08);

}

.card-header{

background:linear-gradient(135deg,#0d6efd,#20c997);

color:white;

padding:25px;

}

.profile{

width:120px;

height:120px;

border-radius:50%;

object-fit:cover;

border:4px solid white;

}

.summary-card{

border-radius:15px;

padding:20px;

color:white;

}

</style>

</head>

<body>

<div class="container py-5">
<div class="card mb-4">

<div class="card-header">

<h3>

<i class="fas fa-user-graduate me-2"></i>

Student Academic Report

</h3>

</div>

<div class="card-body">

<form method="GET">

<div class="row align-items-end">

<div class="col-md-10">

<label class="form-label">

Select Student

</label>

<select

name="student_id"

class="form-select"

required>

<option value="">

Choose Student

</option>

<?php

mysqli_data_seek($students,0);

while($std=mysqli_fetch_assoc($students)){

?>

<option

value="<?php echo $std['id']; ?>"

<?php

if(isset($_GET['student_id']) && $_GET['student_id']==$std['id']){

echo "selected";

}

?>>

<?php

echo htmlspecialchars(

$std['full_name']

)." (".$std['roll_number'].")";

?>

</option>

<?php } ?>

</select>

</div>

<div class="col-md-2 d-grid">

<button

class="btn btn-primary">

<i class="fas fa-search me-2"></i>

Generate

</button>

</div>

</div>

</form>

</div>

</div>

<?php if($student){ ?>

<div class="card mb-4">

<div class="card-body">

<div class="row align-items-center">

<div class="col-md-2 text-center">

<img

src="<?php

echo !empty($student['photo'])

?

'../uploads/students/'.$student['photo']

:

'assets/images/avatar.png';

?>"

class="profile">

</div>

<div class="col-md-5">

<h3>

<?php echo htmlspecialchars($student['full_name']); ?>

</h3>

<p>

<strong>Roll Number:</strong>

<?php echo htmlspecialchars($student['roll_number']); ?>

</p>

<p>

<strong>Email:</strong>

<?php echo htmlspecialchars($student['email']); ?>

</p>

<p>

<strong>Class:</strong>

<?php echo htmlspecialchars($student['class']); ?>

</p>

</div>

<div class="col-md-5">

<div class="row g-3">

<div class="col-6">

<div class="summary-card bg-success">

<h6>

Overall Percentage

</h6>

<h2>

<?php echo $overallPercentage; ?>%

</h2>

</div>

</div>

<div class="col-6">

<div class="summary-card bg-primary">

<h6>

Overall Grade

</h6>

<h2>

<?php echo $overallGrade; ?>

</h2>

</div>

</div>

</div>

</div>

</div>

</div>

</div>

<div class="card">

<div class="card-header bg-dark text-white">

<h5 class="mb-0">

<i class="fas fa-table me-2"></i>

Academic Results

</h5>

</div>

<div class="card-body p-0">

<div class="table-responsive">

<table class="table table-bordered table-hover mb-0">

<thead class="table-light">

<tr>

<th>#</th>

<th>Course</th>

<th>Subject</th>

<th>Exam</th>

<th>Marks</th>

<th>Total</th>

<th>Percentage</th>

<th>Grade</th>

<th>Remarks</th>

</tr>

</thead>

<tbody>

<?php

$count=1;

$totalObtained=0;

$totalMarks=0;

while($row=mysqli_fetch_assoc($results)){

$percent=0;

if($row['total_marks']>0){

$percent=round(

($row['marks']/$row['total_marks'])*100,

2

);

}

$totalObtained+=$row['marks'];

$totalMarks+=$row['total_marks'];

?>

<tr>

<td>

<?php echo $count++; ?>

</td>

<td>

<?php echo htmlspecialchars($row['course_name']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['subject']); ?>

</td>

<td>

<?php echo htmlspecialchars($row['exam_type']); ?>

</td>

<td>

<?php echo $row['marks']; ?>

</td>

<td>

<?php echo $row['total_marks']; ?>

</td>

<td>

<?php echo $percent; ?>%

</td>

<td>

<span class="badge bg-success">

<?php echo $row['grade']; ?>

</span>

</td>

<td>

<?php

echo htmlspecialchars(

$row['remarks']

);

?>

</td>

</tr>

<?php } ?>

</tbody>

<tfoot class="table-secondary">

<tr>

<th colspan="4">

Overall

</th>

<th>

<?php echo $totalObtained; ?>

</th>

<th>

<?php echo $totalMarks; ?>

</th>

<th>

<?php echo $overallPercentage; ?>%

</th>

<th>

<?php echo $overallGrade; ?>

</th>

<th>

-

</th>

</tr>

</tfoot>

</table>

</div>

</div>

</div>
<!-- ==========================================
        PERFORMANCE SUMMARY
========================================== -->

<?php

$status = ($overallPercentage >= 50) ? "PASS" : "FAIL";

$statusColor = ($overallPercentage >= 50)
? "success"
: "danger";

?>

<div class="row mt-4">

<div class="col-md-4">

<div class="card border-0 shadow-sm">

<div class="card-body text-center">

<h6 class="text-muted">

Overall Percentage

</h6>

<h2 class="text-primary">

<?php echo $overallPercentage; ?>%

</h2>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card border-0 shadow-sm">

<div class="card-body text-center">

<h6 class="text-muted">

Overall Grade

</h6>

<h2 class="text-success">

<?php echo $overallGrade; ?>

</h2>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card border-0 shadow-sm">

<div class="card-body text-center">

<h6 class="text-muted">

Final Status

</h6>

<h2>

<span class="badge bg-<?php echo $statusColor; ?> fs-5">

<?php echo $status; ?>

</span>

</h2>

</div>

</div>

</div>

</div>

<!-- ==========================================
        REMARKS
========================================== -->

<div class="card mt-4">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fas fa-comments me-2"></i>

Teacher Remarks

</h5>

</div>

<div class="card-body">

<?php

if($overallPercentage>=90){

echo "<p>Outstanding academic performance. Keep up the excellent work.</p>";

}

elseif($overallPercentage>=80){

echo "<p>Very good performance. Continue working hard.</p>";

}

elseif($overallPercentage>=70){

echo "<p>Good performance with room for further improvement.</p>";

}

elseif($overallPercentage>=60){

echo "<p>Satisfactory performance. More consistent study is recommended.</p>";

}

elseif($overallPercentage>=50){

echo "<p>Passed successfully. Greater focus will help achieve better grades.</p>";

}

else{

echo "<p>Performance is below expectations. Additional preparation and guidance are recommended.</p>";

}

?>

</div>

</div>

<!-- ==========================================
        ACTION BUTTONS
========================================== -->

<div class="text-end mt-4">

<a

href="print_student_report.php?student_id=<?php echo $student['id']; ?>"

target="_blank"

class="btn btn-success">

<i class="fas fa-print me-2"></i>

Print Report

</a>

<a

href="manage_results.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

</div>

<?php } ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>