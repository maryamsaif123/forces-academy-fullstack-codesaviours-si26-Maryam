<?php
session_start();

include "../config/database.php";

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM students WHERE email='$email' AND password='$password'";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {

        $student = mysqli_fetch_assoc($result);

        $_SESSION['student_id'] = $student['id'];
        $_SESSION['student_name'] = $student['full_name'];
        $_SESSION['student_email'] = $student['email'];

        header("Location: dashboard.php");
        exit();

    } else {
        $error = "Invalid Email or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>

    <!-- Bootstrap CSS -->
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
                    if (isset($error)) {
                        echo "<div class='alert alert-danger'>$error</div>";
                    }
                    ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" name="login" class="btn btn-primary">
                                Login
                            </button>
                        </div>

                    </form>

                    <hr>

                    <p class="text-center">
                        Don't have an account?
                        <a href="register.php">Register Here</a>
                    </p>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>