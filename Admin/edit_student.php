<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

if (!isset($_GET['id'])) {
    header("Location: manage_students.php");
    exit();
}

$id = $_GET['id'];

$query = "SELECT * FROM students WHERE id='$id'";
$result = mysqli_query($conn, $query);
$student = mysqli_fetch_assoc($result);

if (!$student) {
    die("Student not found.");
}

$message = "";

if (isset($_POST['update_student'])) {

    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $roll_number = mysqli_real_escape_string($conn, $_POST['roll_number']);
    $class = mysqli_real_escape_string($conn, $_POST['class']);

    $update = "UPDATE students SET
                full_name='$full_name',
                email='$email',
                roll_number='$roll_number',
                class='$class'
                WHERE id='$id'";

    if (mysqli_query($conn, $update)) {

        $message = "<div class='alert alert-success'>
                        Student updated successfully.
                    </div>";

        $query = "SELECT * FROM students WHERE id='$id'";
        $result = mysqli_query($conn, $query);
        $student = mysqli_fetch_assoc($result);

    } else {

        $message = "<div class='alert alert-danger'>
                        Update failed.
                    </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Student</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<?php include("navbar.php"); ?>

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-8">

<div class="card shadow">

<div class="card-header bg-warning">

<h3>Edit Student</h3>

</div>

<div class="card-body">

<?php echo $message; ?>

<form method="POST">

<div class="mb-3">

<label>Full Name</label>

<input
type="text"
name="full_name"
class="form-control"
value="<?php echo $student['full_name']; ?>"
required>

</div>

<div class="mb-3">

<label>Email</label>

<input
type="email"
name="email"
class="form-control"
value="<?php echo $student['email']; ?>"
required>

</div>

<div class="mb-3">

<label>Roll Number</label>

<input
type="text"
name="roll_number"
class="form-control"
value="<?php echo $student['roll_number']; ?>"
required>

</div>

<div class="mb-3">

<label>Class</label>

<input
type="text"
name="class"
class="form-control"
value="<?php echo $student['class']; ?>"
required>

</div>

<button
type="submit"
name="update_student"
class="btn btn-warning">

Update Student

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