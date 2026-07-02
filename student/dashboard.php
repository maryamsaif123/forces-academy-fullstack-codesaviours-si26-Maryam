<?php

session_start();

if (!isset($_SESSION['student_name'])) {

    header("Location: login.php");
    exit();

}

$studentName = $_SESSION['student_name'];

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Student Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h2>Student Dashboard</h2>

</div>

<div class="card-body">

<h3>Welcome, <?php echo $studentName; ?> 👋</h3>

<hr>

<div class="alert alert-success">

<strong>Login Successful!</strong>

You have successfully logged into your account.

</div>

<table class="table table-bordered">

<tr>
<th>Student Name</th>
<td><?php echo $studentName; ?></td>
</tr>

<tr>
<th>Status</th>
<td>Active</td>
</tr>

<tr>
<th>Dashboard</th>
<td>Student Panel</td>
</tr>

</table>

<div class="d-grid">

<a href="logout.php" class="btn btn-danger">

Logout

</a>

</div>

</div>

</div>

</div>

</body>

</html>