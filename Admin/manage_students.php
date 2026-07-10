<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$query = mysqli_query($conn,"SELECT * FROM students ORDER BY id DESC");

$totalStudents = mysqli_num_rows($query);
?>
<?php include("sidebar.php"); ?>

<div class="main-content">

<?php include("navbar.php"); ?>

<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h2 class="fw-bold">

Student Management

</h2>

<p class="text-muted">

Manage all registered students.

</p>

</div>

<a href="add_student.php" class="btn btn-success btn-lg">

<i class="fas fa-plus"></i>

Add Student

</a>

</div>

<div class="row mb-4">

<div class="col-md-4">

<div class="stats-card students">

<div>

<h6>Total Students</h6>

<h2>

<?php echo $totalStudents; ?>

</h2>

</div>

<i class="fas fa-user-graduate"></i>

</div>

</div>

</div>

<div class="card dashboard-card mb-4">

<div class="card-body">

<input
type="text"
id="searchStudent"
class="form-control"
placeholder="🔍 Search Student">

</div>

</div>

<div class="card dashboard-card">

<div class="card-body">

<div class="table-responsive">

<table class="table table-hover align-middle" id="studentTable">

<thead class="table-success">

<tr>

<th>Avatar</th>

<th>Name</th>

<th>Email</th>

<th>Roll No</th>

<th>Class</th>

<th>Actions</th>

</tr>

</thead>

<tbody>

<?php

while($row=mysqli_fetch_assoc($query))
{

?>

<tr>

<td>

<img
src="https://cdn-icons-png.flaticon.com/512/4140/4140048.png"
width="50"
class="rounded-circle">

</td>

<td>

<?php echo $row['full_name']; ?>

</td>

<td>

<?php echo $row['email']; ?>

</td>

<td>

<?php echo $row['roll_number']; ?>

</td>

<td>

<?php echo $row['class']; ?>

</td>

<td>

<a
href="view_student.php?id=<?php echo $row['id']; ?>"
class="btn btn-info btn-sm">

<i class="fas fa-eye"></i>

</a>

<a
href="edit_student.php?id=<?php echo $row['id']; ?>"
class="btn btn-warning btn-sm">

<i class="fas fa-edit"></i>

</a>

<a
href="delete_student.php?id=<?php echo $row['id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete Student?')">

<i class="fas fa-trash"></i>

</a>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</div>

<script>
document.getElementById("searchStudent").addEventListener("keyup", function () {

let value = this.value.toLowerCase();

let rows = document.querySelectorAll("#studentTable tbody tr");

rows.forEach(function(row){

row.style.display =
row.innerText.toLowerCase().includes(value)
? ""
: "none";

});

});
</script>