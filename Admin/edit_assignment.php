<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/*=========================================
    CHECK ASSIGNMENT ID
=========================================*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid Assignment ID.";

    header("Location: manage_assignments.php");
    exit();
}

$id = (int)$_GET['id'];

/*=========================================
    FETCH ASSIGNMENT
=========================================*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM assignments WHERE id=? LIMIT 1"
);

mysqli_stmt_bind_param($stmt,"i",$id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)==0){

    $_SESSION['error']="Assignment not found.";

    header("Location: manage_assignments.php");
    exit();
}

$assignment=mysqli_fetch_assoc($result);

/*=========================================
    UPDATE
=========================================*/

if(isset($_POST['update'])){

    $course_id=(int)$_POST['course_id'];
    $title=trim($_POST['title']);
    $description=trim($_POST['description']);
    $deadline=$_POST['deadline'];

    if(
        $course_id<=0 ||
        empty($title) ||
        empty($description) ||
        empty($deadline)
    ){

        $_SESSION['error']="Please fill all required fields.";

        header("Location: edit_assignment.php?id=".$id);
        exit();
    }

    if($deadline < date("Y-m-d")){

        $_SESSION['error']="Deadline cannot be in the past.";

        header("Location: edit_assignment.php?id=".$id);
        exit();
    }

    /* Duplicate Check */

    $check=mysqli_prepare(

        $conn,

        "SELECT id
         FROM assignments
         WHERE title=?
         AND course_id=?
         AND id<>?"

    );

    mysqli_stmt_bind_param(

        $check,

        "sii",

        $title,

        $course_id,

        $id

    );

    mysqli_stmt_execute($check);

    mysqli_stmt_store_result($check);

    if(mysqli_stmt_num_rows($check)>0){

        $_SESSION['error']="Assignment already exists.";

        header("Location: edit_assignment.php?id=".$id);
        exit();
    }

    /* Update */

    $update=mysqli_prepare(

        $conn,

        "UPDATE assignments SET

        course_id=?,
        title=?,
        description=?,
        deadline=?

        WHERE id=?"

    );

    mysqli_stmt_bind_param(

        $update,

        "isssi",

        $course_id,
        $title,
        $description,
        $deadline,
        $id

    );

    if(mysqli_stmt_execute($update)){

        $_SESSION['success']="Assignment updated successfully.";

        header("Location: manage_assignments.php");
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
content="width=device-width, initial-scale=1">

<title>Edit Assignment</title>

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
color:white;
padding:22px;
}

</style>

</head>

<body>

<div class="container py-5">

<div class="card">

<div class="card-header">

<h3>

<i class="fas fa-edit me-2"></i>

Edit Assignment

</h3>

</div>

<div class="card-body">

<form method="POST">

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

$courses=mysqli_query(

$conn,

"SELECT id,course_name
FROM courses
ORDER BY course_name"

);

while($course=mysqli_fetch_assoc($courses)){

?>

<option
value="<?php echo $course['id']; ?>"
<?php
if($course['id']==$assignment['course_id']) echo "selected";
?>>

<?php echo htmlspecialchars($course['course_name']); ?>

</option>

<?php } ?>

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

<div class="col-12 mb-3">

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

<div class="col-12 mb-4">

<label class="form-label">

Description

</label>

<textarea

name="description"

rows="8"

class="form-control"

required><?php echo htmlspecialchars($assignment['description']); ?></textarea>

</div>

<div class="text-end">

<a
href="manage_assignments.php"
class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<button
type="submit"
name="update"
class="btn btn-primary">

<i class="fas fa-save me-2"></i>

Update Assignment

</button>

</div>

</div>

</form>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>