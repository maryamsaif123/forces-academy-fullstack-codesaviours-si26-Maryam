<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$search = "";

if(isset($_GET['search'])){
    $search = mysqli_real_escape_string($conn,$_GET['search']);

    $query = "SELECT * FROM students
              WHERE full_name LIKE '%$search%'
              OR email LIKE '%$search%'
              OR roll_number LIKE '%$search%'
              ORDER BY id DESC";
}
else{
    $query = "SELECT * FROM students ORDER BY id DESC";
}

$result = mysqli_query($conn,$query);
?>

<!DOCTYPE html>
<html>

<head>

<title>Manage Students</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<?php include("navbar.php"); ?>

<div class="container mt-5">

<div class="d-flex justify-content-between mb-4">

<h2>
<i class="fa fa-users"></i>
Manage Students
</h2>

<a href="add_student.php" class="btn btn-success">

<i class="fa fa-plus"></i>

Add Student

</a>

</div>


<form method="GET">

<div class="input-group mb-4">

<input
type="text"
name="search"
class="form-control"
placeholder="Search Student..."
value="<?php echo $search;?>">

<button class="btn btn-primary">

Search

</button>

</div>

</form>


<table class="table table-bordered table-hover bg-white">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>Name</th>

<th>Email</th>

<th>Roll No</th>

<th>Class</th>

<th width="180">

Action

</th>

</tr>

</thead>

<tbody>

<?php

while($row=mysqli_fetch_assoc($result))
{

?>

<tr>

<td>

<?php echo $row['id'];?>

</td>

<td>

<?php echo $row['full_name'];?>

</td>

<td>

<?php echo $row['email'];?>

</td>

<td>

<?php echo $row['roll_number'];?>

</td>

<td>

<?php echo $row['class'];?>

</td>

<td>

<a
href="edit_student.php?id=<?php echo $row['id'];?>"
class="btn btn-warning btn-sm">

Edit

</a>


<a
href="delete_student.php?id=<?php echo $row['id'];?>"
class="btn btn-danger btn-sm"

onclick="return confirm('Are you sure you want to delete this student?');">

Delete

</a>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

<?php include("footer.php"); ?>

</body>

</html>