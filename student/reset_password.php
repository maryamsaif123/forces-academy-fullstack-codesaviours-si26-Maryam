<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['reset_student_id'])){
    header("Location: forgot_password.php");
    exit();
}

$error = "";
$success = "";

if(isset($_POST['reset'])){

    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    /*=========================================
        VALIDATION
    =========================================*/

    if(empty($password) || empty($confirm_password)){

        $error = "Please fill in all fields.";

    }

    elseif(strlen($password) < 6){

        $error = "Password must be at least 6 characters.";

    }

    elseif($password != $confirm_password){

        $error = "Passwords do not match.";

    }

    else{

        /*=========================================
            HASH PASSWORD
        =========================================*/

        $hashedPassword = password_hash(

            $password,

            PASSWORD_DEFAULT

        );

        /*=========================================
            UPDATE PASSWORD
        =========================================*/

        $stmt = mysqli_prepare(

            $conn,

            "UPDATE students
             SET password=?
             WHERE id=?"

        );

        mysqli_stmt_bind_param(

            $stmt,

            "si",

            $hashedPassword,

            $_SESSION['reset_student_id']

        );

        if(mysqli_stmt_execute($stmt)){

            unset($_SESSION['reset_student_id']);

            $success = "Password reset successfully.";

            header("refresh:2;url=login.php");

        }else{

            $error = "Unable to reset password.";

        }

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

Reset Password

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

background:linear-gradient(135deg,#2563eb,#4f46e5);

font-family:'Poppins',sans-serif;

display:flex;

justify-content:center;

align-items:center;

min-height:100vh;

padding:20px;

}

.card{

width:100%;

max-width:500px;

border:none;

border-radius:20px;

padding:35px;

box-shadow:0 20px 50px rgba(0,0,0,.25);

}

.form-control{

height:50px;

border-radius:12px;

}

.btn{

height:50px;

border-radius:12px;

font-weight:600;

}

</style>

</head>

<body>

<div class="card">

<div class="text-center mb-4">

<i class="fas fa-lock fa-4x text-primary mb-3"></i>

<h3>

Reset Password

</h3>

<p class="text-muted">

Create a new secure password for your account.

</p>

</div>

<?php if($error!=""){ ?>

<div class="alert alert-danger">

<?php echo $error; ?>

</div>

<?php } ?>

<?php if($success!=""){ ?>

<div class="alert alert-success">

<?php echo $success; ?>

<br>

Redirecting to login...

</div>

<?php } ?>

<form method="POST">

<div class="mb-3">

<label class="form-label">

New Password

</label>

<div class="input-group">

<input

type="password"

id="password"

name="password"

class="form-control"

placeholder="Enter new password"

required>

<button

class="btn btn-outline-secondary"

type="button"

onclick="togglePassword('password')">

<i class="fas fa-eye"></i>

</button>

</div>

</div>

<div class="mb-4">

<label class="form-label">

Confirm Password

</label>

<div class="input-group">

<input

type="password"

id="confirm_password"

name="confirm_password"

class="form-control"

placeholder="Confirm password"

required>

<button

class="btn btn-outline-secondary"

type="button"

onclick="togglePassword('confirm_password')">

<i class="fas fa-eye"></i>

</button>

</div>

</div>

<div class="d-grid">

<button

type="submit"

name="reset"

class="btn btn-primary">

<i class="fas fa-key me-2"></i>

Reset Password

</button>

</div>

</form>

<hr>

<div class="text-center">

<a

href="login.php"

class="text-decoration-none">

← Back to Login

</a>

</div>

</div>

<script>

function togglePassword(id){

const input = document.getElementById(id);

input.type = (input.type === "password") ? "text" : "password";

}

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>