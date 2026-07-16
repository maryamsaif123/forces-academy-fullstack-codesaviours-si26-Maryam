<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

if (!isset($_GET['id'])) {
    header("Location: manage_submissions.php");
    exit();
}

$submission_id = intval($_GET['id']);

$query = mysqli_query($conn,"
SELECT
submissions.*,
students.full_name,
assignments.title
FROM submissions
INNER JOIN students
ON submissions.student_id = students.id
INNER JOIN assignments
ON submissions.assignment_id = assignments.id
WHERE submissions.id='$submission_id'
");

$data = mysqli_fetch_assoc($query);

$message = "";

if(isset($_POST['save_grade'])){

$marks = intval($_POST['marks']);

$feedback = mysqli_real_escape_string($conn,$_POST['feedback']);

mysqli_query($conn,"
UPDATE submissions
SET
status='graded',
marks='$marks',
feedback='$feedback'
WHERE id='$submission_id'
");

$message = "
<div class='alert alert-success'>
Assignment graded successfully.
</div>";

$query = mysqli_query($conn,"
SELECT
submissions.*,
students.full_name,
assignments.title
FROM submissions
INNER JOIN students
ON submissions.student_id = students.id
INNER JOIN assignments
ON submissions.assignment_id = assignments.id
WHERE submissions.id='$submission_id'
");

$data = mysqli_fetch_assoc($query);

}
?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Grade Submission</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="dashboard.css">

<style>

.grade-card{

background:white;

padding:35px;

border-radius:20px;

box-shadow:0 10px 25px rgba(0,0,0,.1);

}

.page-header{

background:linear-gradient(135deg,#198754,#22c55e);

padding:35px;

border-radius:20px;

color:white;

margin-bottom:30px;

display:flex;

justify-content:space-between;

align-items:center;

}

.form-control{

height:55px;

border-radius:12px;

}

textarea.form-control{

height:140px;

}
.grade-card{

transition:.3s;

}

.grade-card:hover{

box-shadow:0 20px 40px rgba(0,0,0,.15);

}

.btn{

border-radius:12px;

font-weight:600;

}

.form-control{

border-radius:12px;

}

textarea{

resize:none;

}

@media(max-width:768px){

.page-header{

flex-direction:column;

text-align:center;

}

.page-header .btn{

margin-top:20px;

}

}

</style>

</head>

<body>

<?php include("sidebar.php"); ?>

<div class="main-content">

<?php include("navbar.php"); ?>

<div class="container-fluid">

<div class="page-header">

<h2>

<i class="fas fa-star"></i>

Grade Assignment

</h2>

<a href="manage_submissions.php" class="btn btn-light">

Back

</a>

</div>

<?php echo $message; ?>

<div class="grade-card">

<form method="POST">

<div class="mb-4">

<label class="form-label fw-bold">

Student Name

</label>

<input
type="text"
class="form-control"
value="<?php echo htmlspecialchars($data['full_name']); ?>"
readonly>

</div>

<div class="mb-4">

<label class="form-label fw-bold">

Assignment Title

</label>

<input
type="text"
class="form-control"
value="<?php echo htmlspecialchars($data['title']); ?>"
readonly>

</div>

<div class="mb-4">

<label class="form-label fw-bold">

Submitted File

</label>

<a
href="../uploads/assignment_files/<?php echo $data['file_path']; ?>"
target="_blank"
class="btn btn-primary w-100">

<i class="fas fa-download"></i>

Download Student Assignment

</a>

</div>

<div class="mb-4">

<label class="form-label fw-bold">

Marks (Out of 100)

</label>

<input
type="number"
name="marks"
class="form-control"
min="0"
max="100"
value="<?php echo isset($data['marks']) ? $data['marks'] : ''; ?>"
required>

</div>

<div class="mb-4">

<label class="form-label fw-bold">

Feedback

</label>

<textarea
name="feedback"
class="form-control"
placeholder="Write feedback for the student..."
required><?php echo isset($data['feedback']) ? htmlspecialchars($data['feedback']) : ''; ?></textarea>

</div>

<div class="row">

<div class="col-md-6">

<button
type="submit"
name="save_grade"
class="btn btn-success w-100 btn-lg">

<i class="fas fa-save"></i>

Save Grade

</button>

</div>

<div class="col-md-6">

<a
href="manage_submissions.php"
class="btn btn-secondary w-100 btn-lg">

<i class="fas fa-arrow-left"></i>

Back

</a>

</div>

</div>

</form>

</div>

</div>

<?php include("footer.php"); ?>

</div>

<script>

// Card Animation

const card=document.querySelector(".grade-card");

card.addEventListener("mouseenter",function(){

this.style.transform="translateY(-5px)";

this.style.transition=".3s";

});

card.addEventListener("mouseleave",function(){

this.style.transform="translateY(0px)";

});

// Marks Validation

const marks=document.querySelector("input[name='marks']");

marks.addEventListener("input",function(){

if(this.value>100){

alert("Marks cannot exceed 100.");

this.value=100;

}

if(this.value<0){

this.value=0;

}

});

</script>

</body>

</html>