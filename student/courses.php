<?php

session_start();

if (!isset($_SESSION['student_name'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$query = mysqli_query($conn, "SELECT * FROM courses ORDER BY created_at DESC");

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Available Courses</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<style>

body{
    background:#f5f7fb;
}

.course-card{

    border:none;

    border-radius:20px;

    transition:.3s;

    box-shadow:0 10px 25px rgba(0,0,0,.1);

}

.course-card:hover{

    transform:translateY(-8px);

}

.course-icon{

    font-size:70px;

    color:#198754;

}

.card-footer{

    background:white;

}

</style>

</head>

<body>

<div class="container mt-5">

<h2 class="mb-4 text-success">

<i class="fas fa-book-open"></i>

Available Courses

</h2>

<div class="row">

<?php

if(mysqli_num_rows($query)>0)
{

while($course=mysqli_fetch_assoc($query))
{

?>

<div class="col-lg-4 col-md-6 mb-4">

<div class="card course-card h-100">

<div class="card-body text-center">

<i class="fas fa-laptop-code course-icon mb-3"></i>

<h4>

<?php echo htmlspecialchars($course['course_name']); ?>

</h4>

<hr>

<p>

<?php echo htmlspecialchars($course['description']); ?>

</p>

<p>

<strong>

Teacher:

</strong>

<?php echo htmlspecialchars($course['teacher_name']); ?>

</p>

</div>

<div class="card-footer">

<?php

if(!empty($course['notes_pdf']))
{

?>

<a
href="../uploads/<?php echo htmlspecialchars($course['notes_pdf']); ?>"
target="_blank"
class="btn btn-success btn-sm">

<i class="fas fa-file-pdf"></i>

Notes

</a>

<?php

}

?>

<?php

if(!empty($course['video_link']))
{

?>

<a
href="<?php echo htmlspecialchars($course['video_link']); ?>"
target="_blank"
class="btn btn-danger btn-sm">

<i class="fab fa-youtube"></i>

Watch Video

</a>

<?php

}

?>

</div>

</div>

</div>

<?php

}

}

else

{

?>

<div class="col-12">

<div class="alert alert-warning text-center p-5">

<i class="fas fa-book fa-4x mb-3"></i>

<h3>

No Courses Available

</h3>

<p>

Courses will appear here once added by the administrator.

</p>

</div>

</div>

<?php

}

?>

</div>

</div>

</body>

</html>