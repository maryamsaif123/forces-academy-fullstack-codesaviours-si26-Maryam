<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/*=========================================
    CHECK COURSE ID
=========================================*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid Course ID.";

    header("Location: manage_courses.php");
    exit();
}

$id = (int)$_GET['id'];

/*=========================================
    FETCH COURSE
=========================================*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM courses WHERE id=? LIMIT 1"
);

mysqli_stmt_bind_param($stmt,"i",$id);
mysqli_stmt_execute($stmt);

$result=mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)==0){

    $_SESSION['error']="Course not found.";

    header("Location: manage_courses.php");
    exit();
}

$course=mysqli_fetch_assoc($result);

$pdfExists=false;

if(
!empty($course['notes_pdf']) &&
file_exists("../uploads/notes/".$course['notes_pdf'])
){
    $pdfExists=true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

View Course

</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

body{

background:#f4f7fb;

font-family:'Poppins',sans-serif;

}

.course-card{

border:none;

border-radius:20px;

overflow:hidden;

box-shadow:0 15px 40px rgba(0,0,0,.08);

}

.course-header{

background:linear-gradient(135deg,#0d6efd,#20c997);

padding:50px;

color:white;

}

.info-card{

background:#fff;

border-radius:15px;

padding:20px;

box-shadow:0 5px 20px rgba(0,0,0,.06);

height:100%;

}

iframe{

border-radius:15px;

}

</style>

</head>

<body>

<div class="container py-5">

<div class="card course-card">

<div class="course-header">

<h2>

<i class="fas fa-book-open me-2"></i>

<?php echo htmlspecialchars($course['course_name']); ?>

</h2>

<p class="mb-0">

Teacher:

<strong>

<?php echo htmlspecialchars($course['teacher_name']); ?>

</strong>

</p>

</div>

<div class="card-body p-4">

<div class="row g-4">

<!-- Description -->

<div class="col-lg-8">

<div class="info-card">

<h4 class="mb-3">

Course Description

</h4>

<p style="line-height:1.9;">

<?php echo nl2br(htmlspecialchars($course['description'])); ?>

</p>

</div>

</div>

<!-- Information -->

<div class="col-lg-4">

<div class="info-card">

<h4 class="mb-4">

Course Details

</h4>

<p>

<strong>Teacher:</strong>

<br>

<?php echo htmlspecialchars($course['teacher_name']); ?>

</p>

<hr>

<p>

<strong>Created:</strong>

<br>

<?php echo date("d M Y",strtotime($course['created_at'])); ?>

</p>

<hr>

<?php if($pdfExists){ ?>

<a

href="../uploads/notes/<?php echo $course['notes_pdf']; ?>"

target="_blank"

class="btn btn-danger w-100 mb-3">

<i class="fas fa-file-pdf me-2"></i>

Download Notes

</a>

<?php }else{ ?>

<button

class="btn btn-secondary w-100 mb-3"

disabled>

No PDF Uploaded

</button>

<?php } ?>

<?php

if(!empty($course['video_link'])){

?>

<a

href="<?php echo htmlspecialchars($course['video_link']); ?>"

target="_blank"

class="btn btn-primary w-100">

<i class="fab fa-youtube me-2"></i>

Watch Video

</a>

<?php

}else{

?>

<button

class="btn btn-secondary w-100"

disabled>

No Video Link

</button>

<?php

}

?>

</div>

</div>

</div>

<!-- Video Preview -->

<?php

if(!empty($course['video_link'])){

$link=$course['video_link'];

$link=str_replace("watch?v=","embed/",$link);

$link=str_replace("youtu.be/","youtube.com/embed/",$link);

?>

<div class="row mt-4">

<div class="col-12">

<div class="info-card">

<h4 class="mb-3">

Video Preview

</h4>

<iframe

width="100%"

height="500"

src="<?php echo $link; ?>"

allowfullscreen>

</iframe>

</div>

</div>

</div>

<?php

}

?>

<div class="text-end mt-5">

<a

href="edit_course.php?id=<?php echo $course['id']; ?>"

class="btn btn-warning px-4">

<i class="fas fa-edit me-2"></i>

Edit Course

</a>

<a

href="manage_courses.php"

class="btn btn-secondary px-4">

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