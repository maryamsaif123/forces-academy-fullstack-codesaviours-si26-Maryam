<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/*=========================================
    VALIDATE RESULT ID
=========================================*/

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){

    $_SESSION['error']="Invalid Result ID.";

    header("Location: manage_results.php");
    exit();

}

$id=(int)$_GET['id'];

/*=========================================
    FETCH RESULT
=========================================*/

$stmt=mysqli_prepare(

$conn,

"SELECT

results.*,

students.full_name,
students.roll_number,
students.email,
students.class,
students.photo,

courses.course_name

FROM results

INNER JOIN students
ON students.id=results.student_id

LEFT JOIN courses
ON courses.id=results.course_id

WHERE results.id=?

LIMIT 1"

);

mysqli_stmt_bind_param($stmt,"i",$id);

mysqli_stmt_execute($stmt);

$result=mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)==0){

$_SESSION['error']="Result not found.";

header("Location: manage_results.php");

exit();

}

$row=mysqli_fetch_assoc($result);

$percentage=0;

if($row['total_marks']>0){

$percentage=round(($row['marks']/$row['total_marks'])*100,2);

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>

View Result

</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

body{

background:#f4f7fb;

font-family:'Poppins',sans-serif;

}

.card{

border:none;

border-radius:20px;

box-shadow:0 15px 35px rgba(0,0,0,.08);

}

.card-header{

background:linear-gradient(135deg,#0d6efd,#20c997);

color:#fff;

padding:30px;

}

.profile{

width:120px;

height:120px;

border-radius:50%;

object-fit:cover;

border:4px solid #fff;

}

.info{

background:#f8f9fa;

border-radius:15px;

padding:20px;

margin-bottom:20px;

}

</style>

</head>

<body>

<div class="container py-5">

<div class="card">

<div class="card-header">

<div class="d-flex align-items-center">

<img

src="<?php

echo !empty($row['photo'])

?

'../uploads/students/'.$row['photo']

:

'assets/images/avatar.png';

?>"

class="profile me-4">

<div>

<h2>

<?php echo htmlspecialchars($row['full_name']); ?>

</h2>

<p class="mb-0">

<?php echo htmlspecialchars($row['email']); ?>

</p>

</div>

</div>

</div>

<div class="card-body">

<div class="row">

<div class="col-lg-6">

<div class="info">

<h5>

Student Information

</h5>

<hr>

<p>

<strong>Roll Number:</strong>

<?php echo htmlspecialchars($row['roll_number']); ?>

</p>

<p>

<strong>Class:</strong>

<?php echo htmlspecialchars($row['class']); ?>

</p>

<p>

<strong>Course:</strong>

<?php echo htmlspecialchars($row['course_name']); ?>

</p>

<p>

<strong>Subject:</strong>

<?php echo htmlspecialchars($row['subject']); ?>

</p>

<p>

<strong>Exam Type:</strong>

<?php echo htmlspecialchars($row['exam_type']); ?>

</p>

</div>

</div>

<div class="col-lg-6">

<div class="info">

<h5>

Academic Performance

</h5>

<hr>

<p>

<strong>Marks:</strong>

<?php echo $row['marks']; ?>

/

<?php echo $row['total_marks']; ?>

</p>

<p>

<strong>Percentage:</strong>

<?php echo $percentage; ?>%

</p>

<p>

<strong>Grade:</strong>

<?php

$grade=$row['grade'];

$badge="bg-danger";

if($grade=="A+"){

$badge="bg-success";

}

elseif($grade=="A"){

$badge="bg-primary";

}

elseif($grade=="B"){

$badge="bg-info text-dark";

}

elseif($grade=="C"){

$badge="bg-warning text-dark";

}

elseif($grade=="D"){

$badge="bg-secondary";

}

?>

<span class="badge <?php echo $badge; ?> fs-6">

<?php echo $grade; ?>

</span>

</p>

<p>

<strong>Remarks:</strong>

</p>

<div class="border rounded p-3 bg-light">

<?php

echo !empty($row['remarks'])

?

nl2br(htmlspecialchars($row['remarks']))

:

"<span class='text-muted'>No remarks available.</span>";

?>

</div>

</div>

</div>

</div>

<hr>

<div class="d-flex justify-content-end">

<a

href="print_result.php?id=<?php echo $row['id']; ?>"

target="_blank"

class="btn btn-success me-2">

<i class="fas fa-print me-2"></i>

Print

</a>

<a

href="edit_result.php?id=<?php echo $row['id']; ?>"

class="btn btn-warning me-2">

<i class="fas fa-edit me-2"></i>

Edit

</a>

<a

href="manage_results.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>