<?php
session_start();

include("../config/database.php");

if(!isset($_GET['id'])){
    header("Location: manage_assignments.php");
    exit();
}

$id = (int)$_GET['id'];

/* Fetch Assignment */

$query = mysqli_query($conn,"
SELECT * FROM assignments
WHERE id='$id'
");

if(mysqli_num_rows($query)==0){
    header("Location: manage_assignments.php");
    exit();
}

$assignment = mysqli_fetch_assoc($query);

/* Fetch Courses */

$courses = mysqli_query($conn,"
SELECT *
FROM courses
ORDER BY course_name ASC
");

/* Update Assignment */

if(isset($_POST['update_assignment'])){

    $title = mysqli_real_escape_string($conn,$_POST['title']);

    $description = mysqli_real_escape_string($conn,$_POST['description']);

    $course_id = $_POST['course_id'];

    $deadline = $_POST['deadline'];

    $sql = "
    UPDATE assignments
    SET

    title='$title',

    description='$description',

    course_id='$course_id',

    deadline='$deadline'

    WHERE id='$id'
    ";

    if(mysqli_query($conn,$sql)){

        header("Location: manage_assignments.php?updated=1");

        exit();

    }else{

        $error="Something went wrong.";

    }

}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Edit Assignment</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<style>

body{

background:#f5f7fb;

}

.card{

border:none;

border-radius:20px;

box-shadow:0 15px 35px rgba(0,0,0,.1);

}

.header{

background:linear-gradient(135deg,#16a34a,#22c55e);

padding:30px;

border-radius:20px;

color:white;

margin-bottom:30px;

}

.btn{

border-radius:10px;

}

.form-control,

.form-select{

border-radius:10px;

}

</style>

</head>

<body>

<div class="container py-5">

<div class="header">

<div class="d-flex justify-content-between align-items-center">

<h2>

<i class="fas fa-edit"></i>

Edit Assignment

</h2>

<a href="manage_assignments.php"

class="btn btn-light">

Back

</a>

</div>

</div>

<?php

if(isset($error)){

?>

<div class="alert alert-danger">

<?php echo $error; ?>

</div>

<?php

}

?>

<div class="card">

<div class="card-body p-4">

<form method="POST">

<div class="mb-3">

<label class="form-label">

Assignment Title

</label>

<input

type="text"

name="title"

class="form-control"

value="<?php echo htmlspecialchars($assignment['title']); ?>"

required>

</div>

<div class="mb-3">

<label class="form-label">

Description

</label>

<textarea

name="description"

class="form-control"

rows="5"

required><?php echo htmlspecialchars($assignment['description']); ?></textarea>

</div>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Course

</label>

<select

name="course_id"

class="form-select"

required>

<?php

while($course=mysqli_fetch_assoc($courses)){

?>

<option

value="<?php echo $course['id']; ?>"

<?php

if($course['id']==$assignment['course_id'])

echo "selected";

?>

>

<?php echo htmlspecialchars($course['course_name']); ?>

</option>

<?php

}

?>

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Deadline

</label>

<input

type="date"

name="deadline"

class="form-control"

value="<?php echo $assignment['deadline']; ?>"

required>

</div>

</div>

<div class="text-end">

<a

href="manage_assignments.php"

class="btn btn-secondary">

Cancel

</a>

<button

type="submit"

name="update_assignment"

class="btn btn-success">

<i class="fas fa-save"></i>

Update Assignment

</button>

</div>

</form>

</div>

</div>

</div>

</body>

</html>