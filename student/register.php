<?php
session_start();
require_once "../config/database.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST['full_name']);
    $gender = $_POST['gender'];
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $roll_number = trim($_POST['roll_number']);
    $class = trim($_POST['class']);

    // Validation

    if(empty($full_name) || empty($email) || empty($password) || empty($confirm_password) || empty($roll_number) || empty($class)){

        $error = "All fields are required.";

    }

    elseif($password != $confirm_password){

        $error = "Passwords do not match.";

    }

    else{

        // Check Email

        $check = "SELECT * FROM students WHERE email = ?";

        $stmt = mysqli_prepare($conn,$check);

        mysqli_stmt_bind_param($stmt,"s",$email);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($result)>0){

            $error = "Email already exists.";

        }

        else{

            // Hash Password

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

           $sql = "INSERT INTO students
(full_name, gender, email, password, roll_number, class)
VALUES
(?,?,?,?,?,?)";

            $stmt = mysqli_prepare($conn,$sql);

            mysqli_stmt_bind_param(
    $stmt,
    "ssssss",
    $full_name,
    $gender,
    $email,
    $hashed_password,
    $roll_number,
    $class
);

            if(mysqli_stmt_execute($stmt)){

                header("Location: login.php?registered=1");

                exit();

            }

            else{

                $error = "Registration Failed.";

            }

        }

    }

}

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Student Registration</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-6">

<div class="card shadow">

<div class="card-header bg-success text-white text-center">

<h3>Student Registration</h3>

</div>

<div class="card-body">

<?php

if($error!=""){

echo "<div class='alert alert-danger'>$error</div>";

}

?>

<form method="POST">

<div class="mb-3">

<label>Full Name</label>

<input
type="text"
name="full_name"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">Gender</label>

<select name="gender" class="form-select" required>

<option value="">Select Gender</option>

<option value="Male">Male</option>

<option value="Female">Female</option>

</select>

</div>

<div class="mb-3">

<label>Email</label>

<input
type="email"
name="email"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Password</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Confirm Password</label>

<input
type="password"
name="confirm_password"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Roll Number</label>

<input
type="text"
name="roll_number"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Class</label>

<input
type="text"
name="class"
class="form-control"
required>

</div>

<button
type="submit"
class="btn btn-success w-100">

Register

</button>

</form>

<hr>

<p class="text-center">

Already have an account?

<a href="login.php">

Login Here

</a>

</p>

</div>

</div>

</div>

</div>

</div>

</body>

</html>