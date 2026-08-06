<?php
session_start();

if(!isset($_SESSION['student_id'])){
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$student_id = $_SESSION['student_id'];

$stmt = mysqli_prepare(

$conn,

"SELECT id,password,full_name
FROM students
WHERE id=?
LIMIT 1"

);

mysqli_stmt_bind_param(

$stmt,

"i",

$student_id

);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$student = mysqli_fetch_assoc($result);

if(!$student){

session_destroy();

header("Location: login.php");

exit();

}

$error="";
$success="";

/*=========================================
    CHANGE PASSWORD
=========================================*/

if(isset($_POST['change_password'])){

$current_password=$_POST['current_password'];

$new_password=$_POST['new_password'];

$confirm_password=$_POST['confirm_password'];

if(

empty($current_password) ||

empty($new_password) ||

empty($confirm_password)

){

$error="Please fill all fields.";

}

elseif(!password_verify(

$current_password,

$student['password']

)){

$error="Current password is incorrect.";

}

elseif(strlen($new_password)<6){

$error="Password must contain at least 6 characters.";

}

elseif($new_password!=$confirm_password){

$error="New passwords do not match.";

}

elseif(password_verify(

$new_password,

$student['password']

)){

$error="New password cannot be the same as your current password.";

}

else{

$hashed=password_hash(

$new_password,

PASSWORD_DEFAULT

);

$update=mysqli_prepare(

$conn,

"UPDATE students
SET password=?
WHERE id=?"

);

mysqli_stmt_bind_param(

$update,

"si",

$hashed,

$student_id

);

if(mysqli_stmt_execute($update)){

$success="Password changed successfully.";

}else{

$error="Unable to update password.";

}

}

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1">

<title>

Change Password

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

box-shadow:0 15px 35px rgba(0,0,0,.12);

}

.form-control{

height:50px;

border-radius:12px;

}

.btn{

border-radius:12px;

height:48px;

}

.password-icon{

width:80px;

height:80px;

border-radius:50%;

background:#0d6efd;

color:#fff;

display:flex;

align-items:center;

justify-content:center;

font-size:35px;

margin:auto;

}

</style>

</head>

<body>

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-lg-6">

<div class="card">

<div class="card-body p-5">

<div class="password-icon mb-4">

<i class="fas fa-lock"></i>

</div>

<h2 class="text-center mb-4">

Change Password

</h2>

<?php if($success!=""){ ?>

<div class="alert alert-success">

<?php echo $success; ?>

</div>

<?php } ?>

<?php if($error!=""){ ?>

<div class="alert alert-danger">

<?php echo $error; ?>

</div>

<?php } ?>

<form method="POST">

<div class="mb-3">
<label class="form-label">

Current Password

</label>

<div class="input-group">

<input

type="password"

name="current_password"

id="current_password"

class="form-control"

placeholder="Enter current password"

required>

<button

class="btn btn-outline-secondary"

type="button"

onclick="togglePassword('current_password')">

<i class="fas fa-eye"></i>

</button>

</div>

</div>

<div class="mb-3">

<label class="form-label">

New Password

</label>

<div class="input-group">

<input

type="password"

name="new_password"

id="new_password"

class="form-control"

placeholder="Enter new password"

required>

<button

class="btn btn-outline-secondary"

type="button"

onclick="togglePassword('new_password')">

<i class="fas fa-eye"></i>

</button>

</div>

<small class="text-muted">

Password should contain at least 6 characters.

</small>

</div>

<div class="mb-4">

<label class="form-label">

Confirm Password

</label>

<div class="input-group">

<input

type="password"

name="confirm_password"

id="confirm_password"

class="form-control"

placeholder="Confirm new password"

required>

<button

class="btn btn-outline-secondary"

type="button"

onclick="togglePassword('confirm_password')">

<i class="fas fa-eye"></i>

</button>

</div>

</div>

<hr>

<div class="d-flex justify-content-between">

<a

href="profile.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<div>

<button

type="reset"

class="btn btn-warning me-2">

<i class="fas fa-rotate-left me-2"></i>

Reset

</button>

<button

type="submit"

name="change_password"

class="btn btn-primary">

<i class="fas fa-key me-2"></i>

Change Password

</button>

</div>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

<script>

function togglePassword(id){

const input=document.getElementById(id);

if(input.type==="password"){

input.type="text";

}else{

input.type="password";

}

}

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>