<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

if (!isset($_GET['id'])) {
    header("Location: manage_students.php");
    exit();
}

$id = (int)$_GET['id'];

/*====================================
    FETCH STUDENT
=====================================*/

$query = mysqli_prepare($conn,
"SELECT * FROM students WHERE id=?");

mysqli_stmt_bind_param($query,"i",$id);

mysqli_stmt_execute($query);

$result=mysqli_stmt_get_result($query);

if(mysqli_num_rows($result)==0){

$_SESSION['error']="Student not found.";

header("Location: manage_students.php");

exit();

}

$student=mysqli_fetch_assoc($result);


/*====================================
    UPDATE STUDENT
=====================================*/

if(isset($_POST['update'])){

$full_name=trim($_POST['full_name']);
$email=trim($_POST['email']);
$gender=trim($_POST['gender']);
$roll_number=trim($_POST['roll_number']);
$class=trim($_POST['class']);



/* PASSWORD */

$password=$student['password'];

if(!empty($_POST['password'])){

$password=password_hash($_POST['password'],PASSWORD_DEFAULT);

}



/* PHOTO */

$photo=$student['photo'] ?? "";

if(isset($_FILES['photo']) && $_FILES['photo']['error']==0){

$allowed=['jpg','jpeg','png','gif','webp'];

$ext=strtolower(pathinfo($_FILES['photo']['name'],PATHINFO_EXTENSION));

if(in_array($ext,$allowed)){

if(!empty($photo) && file_exists("../uploads/students/".$photo)){

unlink("../uploads/students/".$photo);

}

$photo=time()."_".uniqid().".".$ext;

move_uploaded_file(

$_FILES['photo']['tmp_name'],

"../uploads/students/".$photo

);

}

}



/* DUPLICATE CHECK */

$check=mysqli_prepare(

$conn,

"SELECT id FROM students
WHERE
(email=? OR roll_number=?)
AND id<>?"

);

mysqli_stmt_bind_param(

$check,

"ssi",

$email,

$roll_number,

$id

);

mysqli_stmt_execute($check);

mysqli_stmt_store_result($check);

if(mysqli_stmt_num_rows($check)>0){

$_SESSION['error']="Email or Roll Number already exists.";

header("Location: edit_student.php?id=".$id);

exit();

}



/* UPDATE */

if(isset($student['photo'])){

$sql=mysqli_prepare(

$conn,

"UPDATE students SET

full_name=?,

gender=?,

email=?,

password=?,

roll_number=?,

class=?,

photo=?

WHERE id=?"

);

mysqli_stmt_bind_param(

$sql,

"sssssssi",

$full_name,

$gender,

$email,

$password,

$roll_number,

$class,

$photo,

$id

);

}else{

$sql=mysqli_prepare(

$conn,

"UPDATE students SET

full_name=?,

gender=?,

email=?,

password=?,

roll_number=?,

class=?

WHERE id=?"

);

mysqli_stmt_bind_param(

$sql,

"ssssssi",

$full_name,

$gender,

$email,

$password,

$roll_number,

$class,

$id

);

}

if(mysqli_stmt_execute($sql)){

$_SESSION['success']="Student updated successfully.";

header("Location: manage_students.php");

exit();

}else{

$_SESSION['error']="Database Error.";

}

}

?>

<!DOCTYPE html>

<html>

<head>

<title>Edit Student</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="card shadow">

<div class="card-header">

<h3>Edit Student</h3>

</div>

<div class="card-body">

<form method="POST" enctype="multipart/form-data">

<div class="row">

<div class="col-md-6 mb-3">

<label>Name</label>

<input

type="text"

name="full_name"

class="form-control"

value="<?= htmlspecialchars($student['full_name']); ?>"

required>

</div>

<div class="col-md-6 mb-3">

<label>Email</label>

<input

type="email"

name="email"

class="form-control"

value="<?= htmlspecialchars($student['email']); ?>"

required>

</div>

<div class="col-md-6 mb-3">

<label>Roll Number</label>

<input

type="text"

name="roll_number"

class="form-control"

value="<?= htmlspecialchars($student['roll_number']); ?>"

required>

</div>

<div class="col-md-6 mb-3">

<label>Class</label>

<input

type="text"

name="class"

class="form-control"

value="<?= htmlspecialchars($student['class']); ?>"

required>

</div>

<div class="col-md-6 mb-3">

<label>Gender</label>

<select

name="gender"

class="form-select">

<option <?=($student['gender']=="Male")?"selected":"";?>>Male</option>

<option <?=($student['gender']=="Female")?"selected":"";?>>Female</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label>New Password</label>

<input

type="password"

name="password"

class="form-control"

placeholder="Leave blank to keep current password">

</div>

<?php if(isset($student['photo'])){ ?>

<div class="col-md-6 mb-3">

<label>Photo</label>

<input

type="file"

name="photo"

class="form-control">

</div>

<div class="col-md-6 mb-3">

<?php

$image="assets/images/avatar.png";

if(!empty($student['photo'])){

$image="../uploads/students/".$student['photo'];

}

?>

<img src="<?= $image; ?>" width="120" class="rounded-circle shadow">

</div>

<?php } ?>

</div>

<div class="mt-3">

<button

class="btn btn-primary"

name="update">

Update Student

</button>

<a

href="manage_students.php"

class="btn btn-secondary">

Cancel

</a>

</div>

</form>

</div>

</div>

</div>

</body>

</html>