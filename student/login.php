<?php
session_start();
require_once "../config/database.php";

$error = "";

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];
  
    // Check email
    $sql = "SELECT * FROM students WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {

        $student = mysqli_fetch_assoc($result);

        // Verify password
        if (password_verify($password, $student['password'])) {

            $_SESSION['student_id'] = $student['id'];
            $_SESSION['student_name'] = $student['full_name'];
            $_SESSION['student_email'] = $student['email'];
            $_SESSION['student_gender'] = $student['gender'];

            header("Location: dashboard.php");
            exit();

        } else {

            $error = "Incorrect Password!";

        }

    } else {

        $error = "Email not found!";

    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Student Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-5">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h3 class="text-center">Student Login</h3>

</div>

<div class="card-body">

<?php
if ($error != "") {
    echo "<div class='alert alert-danger'>$error</div>";
}

if (isset($_GET['registered'])) {
    echo "<div class='alert alert-success'>Registration Successful. Please Login.</div>";
}
?>

<form method="POST">

<div class="mb-3">

<label class="form-label">Email</label>

<input
type="email"
name="email"
class="form-control"
placeholder="Enter Email"
required>

</div>

<div class="mb-3">

<label class="form-label">Password</label>

<input
type="password"
name="password"
class="form-control"
placeholder="Enter Password"
required>

</div>

<div class="d-grid">

<button
type="submit"
name="login"
class="btn btn-primary">

Login

</button>

</div>

</form>

<hr>

<p class="text-center">

Don't have an account?

<a href="register.php">

Register Here

</a>

</p>

</div>

</div>

</div>

</div>

</div>

</body>

</html>