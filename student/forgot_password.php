<?php
session_start();
include("../config/database.php");

$error = "";
$success = "";

if(isset($_POST['verify'])){

    $email = trim($_POST['email']);
    $roll_number = trim($_POST['roll_number']);

    if(empty($email) || empty($roll_number)){

        $error = "Please fill in all fields.";

    }else{

        $stmt = mysqli_prepare(

            $conn,

            "SELECT id
             FROM students
             WHERE email=?
             AND roll_number=?
             LIMIT 1"

        );

        mysqli_stmt_bind_param(

            $stmt,

            "ss",

            $email,

            $roll_number

        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($result)==1){

            $student = mysqli_fetch_assoc($result);

            $_SESSION['reset_student_id'] = $student['id'];

            header("Location: reset_password.php");

            exit();

        }else{

            $error = "No student found with the provided information.";

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

Forgot Password

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

height:100vh;

padding:20px;

}

.card{

max-width:500px;

width:100%;

border:none;

border-radius:20px;

box-shadow:0 20px 45px rgba(0,0,0,.25);

padding:35px;

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

<i class="fas fa-key fa-4x text-primary mb-3"></i>

<h3>

Forgot Password

</h3>

<p class="text-muted">

Verify your identity to reset your password.

</p>

</div>

<?php if($error!=""){ ?>

<div class="alert alert-danger">

<?php echo $error; ?>

</div>

<?php } ?>

<form method="POST">

<div class="mb-3">

<label class="form-label">

Email Address

</label>

<input

type="email"

name="email"

class="form-control"

placeholder="Enter your email"

required>

</div>

<div class="mb-4">

<label class="form-label">

Roll Number

</label>

<input

type="text"

name="roll_number"

class="form-control"

placeholder="Enter your roll number"

required>

</div>

<div class="d-grid">

<button

type="submit"

name="verify"

class="btn btn-primary">

<i class="fas fa-check-circle me-2"></i>

Verify Account

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>