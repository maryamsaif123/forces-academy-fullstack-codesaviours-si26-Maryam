<?php
include "../config/database.php";

if (isset($_POST['register'])) {

    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $roll_number = $_POST['roll_number'];
    $class = $_POST['class'];

    $check = "SELECT * FROM students WHERE email='$email'";
    $checkResult = mysqli_query($conn, $check);

    if (mysqli_num_rows($checkResult) > 0) {

        $error = "Email already exists!";

    } else {

        $query = "INSERT INTO students (full_name, email, password, roll_number, class)
                  VALUES ('$full_name','$email','$password','$roll_number','$class')";

        if (mysqli_query($conn, $query)) {

            $success = "Registration Successful!";

        } else {

            $error = "Registration Failed!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Student Registration</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-6">

<div class="card shadow">

<div class="card-header bg-success text-white">

<h3 class="text-center">Student Registration</h3>

</div>

<div class="card-body">

<?php
if(isset($success))
{
    echo "<div class='alert alert-success'>$success</div>";
}

if(isset($error))
{
    echo "<div class='alert alert-danger'>$error</div>";
}
?>

<form method="POST">

<div class="mb-3">
<label class="form-label">Full Name</label>
<input type="text" name="full_name" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Email Address</label>
<input type="email" name="email" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Password</label>
<input type="password" name="password" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Roll Number</label>
<input type="text" name="roll_number" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Class</label>
<input type="text" name="class" class="form-control" required>
</div>

<div class="d-grid">

<button type="submit" name="register" class="btn btn-success">
Register
</button>

</div>

</form>

<hr>

<p class="text-center">

Already have an account?

<a href="login.php">Login Here</a>

</p>

</div>

</div>

</div>

</div>

</div>

</body>

</html>