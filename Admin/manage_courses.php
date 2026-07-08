<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$result = mysqli_query($conn,"SELECT * FROM courses ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>

<head>

<title>Manage Courses</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<?php include("navbar.php"); ?>

<div class="container mt-5">

<div class="d-flex justify-content-between">

<h2>📚 Manage Courses</h2>

<a href="add_course.php" class="btn btn-success">

+ Add Course

</a>

</div>

<hr>

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>ID</th>
<th>Course Name</th>
<th>Course Code</th>
<th>Instructor</th>
<th>Duration</th>
<th>Action</th>

</tr>

</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['course_name']; ?></td>

<td><?php echo $row['course_code']; ?></td>

<td><?php echo $row['instructor']; ?></td>

<td><?php echo $row['duration']; ?></td>

<td>

<a href="edit_course.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">

Edit

</a>

<a href="delete_course.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm"
onclick="return confirm('Delete this course?');">

Delete

</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<?php include("footer.php"); ?>

</body>

</html>