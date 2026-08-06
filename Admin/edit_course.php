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

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    $_SESSION['error'] = "Course not found.";

    header("Location: manage_courses.php");
    exit();
}

$course = mysqli_fetch_assoc($result);

/*=========================================
    UPDATE COURSE
=========================================*/

if(isset($_POST['update'])){

$course_name = trim($_POST['course_name']);
$description = trim($_POST['description']);
$teacher_name = trim($_POST['teacher_name']);
$video_link = trim($_POST['video_link']);

$pdf = $course['notes_pdf'];

/*=========================================
    VALIDATION
=========================================*/

if(
empty($course_name) ||
empty($description) ||
empty($teacher_name)
){

$_SESSION['error']="Please fill all required fields.";

header("Location: edit_course.php?id=".$id);

exit();

}

/*=========================================
    DUPLICATE CHECK
=========================================*/

$check=mysqli_prepare(

$conn,

"SELECT id
FROM courses
WHERE course_name=?
AND id<>?"

);

mysqli_stmt_bind_param(

$check,

"si",

$course_name,

$id

);

mysqli_stmt_execute($check);

mysqli_stmt_store_result($check);

if(mysqli_stmt_num_rows($check)>0){

$_SESSION['error']="Course already exists.";

header("Location: edit_course.php?id=".$id);

exit();

}

/*=========================================
    PDF UPLOAD
=========================================*/

if(isset($_FILES['notes_pdf']) &&
$_FILES['notes_pdf']['error']==0){

$ext=strtolower(

pathinfo(

$_FILES['notes_pdf']['name'],

PATHINFO_EXTENSION

)

);

if($ext!="pdf"){

$_SESSION['error']="Only PDF files are allowed.";

header("Location: edit_course.php?id=".$id);

exit();

}

if(!empty($pdf) &&
file_exists("../uploads/notes/".$pdf)){

unlink("../uploads/notes/".$pdf);

}

$pdf=time()."_".$_FILES['notes_pdf']['name'];

move_uploaded_file(

$_FILES['notes_pdf']['tmp_name'],

"../uploads/notes/".$pdf

);

}

/*=========================================
    UPDATE QUERY
=========================================*/

$update=mysqli_prepare(

$conn,

"UPDATE courses SET

course_name=?,
description=?,
teacher_name=?,
notes_pdf=?,
video_link=?

WHERE id=?"

);

mysqli_stmt_bind_param(

$update,

"sssssi",

$course_name,
$description,
$teacher_name,
$pdf,
$video_link,
$id

);

if(mysqli_stmt_execute($update)){

$_SESSION['success']="Course updated successfully.";

header("Location: manage_courses.php");

exit();

}else{

$_SESSION['error']="Database Error.";

}

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Edit Course</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
rel="stylesheet">

<link rel="stylesheet"
href="assets/css/dashboard.css">

</head>

<body class="bg-light">

<div class="container py-5">

<div class="card shadow-lg border-0 rounded-4">

<div class="card-header bg-primary text-white">

<h3>

<i class="fas fa-edit me-2"></i>

Edit Course

</h3>

</div>

<div class="card-body">

<form method="POST" enctype="multipart/form-data">

<div class="row">
<!-- Course Name -->
<div class="col-md-6 mb-3">

<label class="form-label">

Course Name

</label>

<input

type="text"

name="course_name"

class="form-control"

value="<?php echo htmlspecialchars($course['course_name']); ?>"

required>

</div>

<!-- Teacher Name -->
<div class="col-md-6 mb-3">

<label class="form-label">

Teacher Name

</label>

<input

type="text"

name="teacher_name"

class="form-control"

value="<?php echo htmlspecialchars($course['teacher_name']); ?>"

required>

</div>

<!-- Description -->
<div class="col-12 mb-3">

<label class="form-label">

Course Description

</label>

<textarea

name="description"

rows="7"

class="form-control"

required><?php echo htmlspecialchars($course['description']); ?></textarea>

</div>

<!-- Current PDF -->
<div class="col-md-6 mb-4">

<label class="form-label">

Current PDF Notes

</label>

<br>

<?php

if(!empty($course['notes_pdf']) &&
file_exists("../uploads/notes/".$course['notes_pdf'])){

?>

<a

href="../uploads/notes/<?php echo $course['notes_pdf']; ?>"

target="_blank"

class="btn btn-danger">

<i class="fas fa-file-pdf me-2"></i>

View PDF

</a>

<?php

}else{

?>

<span class="badge bg-secondary">

No PDF Uploaded

</span>

<?php

}

?>

</div>

<!-- Upload New PDF -->
<div class="col-md-6 mb-4">

<label class="form-label">

Replace PDF

</label>

<input

type="file"

name="notes_pdf"

class="form-control"

accept=".pdf">

<small class="text-muted">

Leave empty to keep the current PDF.

</small>

</div>

<!-- Video Link -->
<div class="col-12 mb-4">

<label class="form-label">

YouTube Video Link

</label>

<input

type="url"

name="video_link"

class="form-control"

placeholder="https://youtube.com/watch?v=..."

value="<?php echo htmlspecialchars($course['video_link']); ?>">

</div>

<hr class="my-4">

<div class="col-12 text-end">

<a

href="manage_courses.php"

class="btn btn-secondary px-4">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<button

type="submit"

name="update"

class="btn btn-primary px-4">

<i class="fas fa-save me-2"></i>

Update Course

</button>

</div>

</div>

</form>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

// Validate YouTube URL

document.querySelector('input[name="video_link"]').addEventListener("blur",function(){

let url=this.value.trim();

if(url!=="" &&
!url.includes("youtube.com") &&
!url.includes("youtu.be")){

alert("Please enter a valid YouTube link.");

this.focus();

}

});

</script>

</body>

</html>