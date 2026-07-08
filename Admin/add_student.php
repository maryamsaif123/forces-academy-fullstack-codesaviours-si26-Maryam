<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$message = "";

if (isset($_POST['add_student'])) {

    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $roll_number = mysqli_real_escape_string($conn, $_POST['roll_number']);
    $class = mysqli_real_escape_string($conn, $_POST['class']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $check = mysqli_query($conn, "SELECT * FROM students WHERE email='$email'");

    if (mysqli_num_rows($check) > 0) {

        $message = "<div class='alert alert-danger'>Email already exists.</div>";

    } else {

        $query = "INSERT INTO students(full_name,email,roll_number,class,password)
                  VALUES('$full_name','$email','$roll_number','$class','$password')";

        if (mysqli_query($conn, $query)) {

            $message = "<div class='alert alert-success'>Student added successfully.</div>";

        } else {

            $message = "<div class='alert alert-danger'>Error: ".mysqli_error($conn)."</div>";

        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Add Student</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<?php include("navbar.php"); ?>

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-8">

<div class="card shadow">

<div class="card-header bg-success text-white">

<h3>Add New Student</h3>

</div>

<div class="card-body">

<?php echo $message; ?>

<form method="POST">

<div class="mb-3">

<label class="form-label">Full Name</label>

<input
type="text"
name="full_name"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">Email</label>

<input
type="email"
name="email"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">Roll Number</label>

<input
type="text"
name="roll_number"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">Class</label>

<input
type="text"
name="class"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">Password</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>

<button
type="submit"
name="add_student"
class="btn btn-success">

Add Student

</button>

<a href="manage_students.php" class="btn btn-secondary">

Back

</a>

</form>

</div>

</div>

</div>

</div>

</div>

<?php include("footer.php"); ?>

</body>

</html>