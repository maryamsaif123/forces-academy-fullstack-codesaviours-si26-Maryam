<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/*=========================================
    VALIDATE ID
=========================================*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid Submission ID.";

    header("Location: manage_submissions.php");
    exit();
}

$id = (int)$_GET['id'];

/*=========================================
    FETCH SUBMISSION
=========================================*/

$stmt = mysqli_prepare(

$conn,

"SELECT

submissions.*,

students.full_name,
students.email,
students.roll_number,
students.class,
students.photo,

assignments.title,
assignments.description,
assignments.deadline,

courses.course_name

FROM submissions

INNER JOIN students
ON students.id=submissions.student_id

INNER JOIN assignments
ON assignments.id=submissions.assignment_id

LEFT JOIN courses
ON courses.id=assignments.course_id

WHERE submissions.id=?

LIMIT 1"

);

mysqli_stmt_bind_param($stmt,"i",$id);

mysqli_stmt_execute($stmt);

$result=mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)==0){

$_SESSION['error']="Submission not found.";

header("Location: manage_submissions.php");

exit();

}

$data=mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>

View Submission

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
border-radius:18px;
box-shadow:0 10px 30px rgba(0,0,0,.08);
}

.header{
background:linear-gradient(135deg,#0d6efd,#20c997);
color:#fff;
padding:35px;
border-radius:18px 18px 0 0;
}

.profile-img{

width:110px;
height:110px;
border-radius:50%;
object-fit:cover;
border:4px solid white;

}

.info-box{

background:#f8f9fa;
padding:20px;
border-radius:12px;
margin-bottom:20px;

}

.badge-status{

font-size:15px;
padding:8px 18px;

}

</style>

</head>

<body>

<div class="container py-5">

<div class="card">

<div class="header">

<div class="d-flex align-items-center">

<img

src="<?php
echo !empty($data['photo'])
?
'../uploads/students/'.$data['photo']
:
'assets/images/avatar.png';
?>"

class="profile-img me-4">

<div>

<h2>

<?php echo htmlspecialchars($data['full_name']); ?>

</h2>

<p class="mb-0">

<?php echo htmlspecialchars($data['email']); ?>

</p>

</div>

</div>

</div>

<div class="card-body p-4">

<div class="row">

<div class="col-lg-8">

<div class="info-box">

<h4>

Assignment Details

</h4>

<hr>

<p>

<strong>Course:</strong>

<?php echo htmlspecialchars($data['course_name']); ?>

</p>

<p>

<strong>Assignment:</strong>

<?php echo htmlspecialchars($data['title']); ?>

</p>

<p>

<strong>Description:</strong>

</p>

<div>

<?php echo nl2br(htmlspecialchars($data['description'])); ?>

</div>

</div>

<div class="info-box">

<h4>

Submitted File

</h4>

<hr>

<?php if(!empty($data['file_path'])){ ?>

<a

href="../<?php echo $data['file_path']; ?>"

target="_blank"

class="btn btn-primary">

<i class="fas fa-download me-2"></i>

Download Submission

</a>

<?php }else{ ?>

<span class="badge bg-secondary">

No File Uploaded

</span>

<?php } ?>

</div>

</div>

<div class="col-lg-4">

<div class="info-box">

<h5>

Student Information

</h5>

<hr>

<p>

<strong>Roll No:</strong>

<?php echo htmlspecialchars($data['roll_number']); ?>

</p>

<p>

<strong>Class:</strong>

<?php echo htmlspecialchars($data['class']); ?>

</p>

</div>

<div class="info-box">

<h5>

Submission Details

</h5>

<hr>

<p>

<strong>Deadline</strong><br>

<?php echo date("d M Y",strtotime($data['deadline'])); ?>

</p>

<p>

<strong>Submitted On</strong><br>

<?php echo date("d M Y h:i A",strtotime($data['submitted_at'])); ?>

</p>

<p>

<strong>Status</strong><br>

<?php

if($data['status']=="graded"){

echo '<span class="badge bg-success badge-status">Graded</span>';

}else{

echo '<span class="badge bg-warning text-dark badge-status">Submitted</span>';

}

?>

</p>

<p>

<strong>Marks</strong><br>

<?php

if($data['marks']==""){

echo "<span class='text-muted'>Not Graded</span>";

}else{

echo "<strong>".$data['marks']." / 100</strong>";

}

?>

</p>

<p>

<strong>Feedback</strong>

</p>

<div class="border rounded p-3 bg-light">

<?php

echo !empty($data['feedback'])

?

nl2br(htmlspecialchars($data['feedback']))

:

"<span class='text-muted'>No feedback yet.</span>";

?>

</div>

</div>

</div>

</div>

<hr>

<div class="text-end">

<a

href="grade_submission.php?id=<?php echo $data['id']; ?>"

class="btn btn-success">

<i class="fas fa-star me-2"></i>

Grade Submission

</a>

<a

href="manage_submissions.php"

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