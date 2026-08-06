<?php

session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$success = "";
$error = "";

/*=========================================
    SAVE TIMETABLE
=========================================*/

if(isset($_POST['save'])){

    $education_level = trim($_POST['education_level']);
    $class_name      = trim($_POST['class_name']);
    $section         = trim($_POST['section']);
    $day             = trim($_POST['day']);
    $period_no       = (int)$_POST['period_no'];
    $start_time      = $_POST['start_time'];
    $end_time        = $_POST['end_time'];
    $subject         = trim($_POST['subject']);
    $teacher         = trim($_POST['teacher']);
    $room_no         = trim($_POST['room_no']);

    if(
        empty($education_level) ||
        empty($class_name) ||
        empty($day) ||
        empty($period_no) ||
        empty($start_time) ||
        empty($end_time) ||
        empty($subject) ||
        empty($teacher)
    ){

        $error = "Please fill all required fields.";

    }else{

        $stmt = mysqli_prepare(

            $conn,

            "INSERT INTO timetable
            (
                education_level,
                class_name,
                section,
                day,
                period_no,
                start_time,
                end_time,
                subject,
                teacher,
                room_no
            )
            VALUES
            (?,?,?,?,?,?,?,?,?,?)"

        );

        mysqli_stmt_bind_param(

            $stmt,

            "ssssisssss",

            $education_level,
            $class_name,
            $section,
            $day,
            $period_no,
            $start_time,
            $end_time,
            $subject,
            $teacher,
            $room_no

        );

        if(mysqli_stmt_execute($stmt)){

            $success = "Timetable added successfully.";

        }else{

            $error = "Unable to save timetable.";

        }

    }

}
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1">

<title>

Manage Submissions

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
rel="stylesheet">

<link
href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet">

<link
rel="stylesheet"
href="assets/css/dashboard.css">

<style>

body{
background:#f4f7fb;
font-family:'Poppins',sans-serif;
}

.page-title{
font-size:28px;
font-weight:700;
}

.stats-card{
border:none;
border-radius:18px;
box-shadow:0 10px 25px rgba(0,0,0,.08);
transition:.3s;
}

.stats-card:hover{
transform:translateY(-5px);
}

.submission-table{
border-radius:15px;
overflow:hidden;
}

</style>

</head>

<body>

<div class="wrapper">

<?php include("includes/sidebar.php"); ?>

<div class="main-content">

<?php include("includes/topbar.php"); ?>

<div class="container-fluid py-4">

<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h2 class="fw-bold">

<i class="fas fa-calendar-alt text-primary me-2"></i>

Manage Timetable

</h2>

<p class="text-muted">

Create separate timetables for School Classes (1–12) and University Programs.

</p>

</div>

<a href="dashboard.php" class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Dashboard

</a>

</div>

<?php if($success!=""){ ?>

<div class="alert alert-success">

<?php echo $success; ?>

</div>

<?php } ?>

<?php if($error!=""){ ?>

<div class="alert alert-danger">

<?php echo $error; ?>

</div>

<?php } ?>
<div class="card shadow border-0 rounded-4">

<div class="card-header bg-primary text-white">

<h4>

<i class="fas fa-plus-circle me-2"></i>

Add New Timetable

</h4>

</div>

<div class="card-body">

<form method="POST">

<div class="row">

<!-- Education Level -->

<div class="col-md-4 mb-3">

<label class="form-label fw-bold">

Education Level

</label>

<select
name="education_level"
class="form-select"
required>

<option value="">Select Level</option>

<option value="School">School</option>

<option value="University">University</option>

</select>

</div>

<!-- Class -->

<div class="col-md-4 mb-3">

<label class="form-label fw-bold">

Class / Semester

</label>

<select
name="class_name"
class="form-select"
required>

<option value="">Select Class</option>

<optgroup label="School">

<option>Class 1</option>

<option>Class 2</option>

<option>Class 3</option>

<option>Class 4</option>

<option>Class 5</option>

<option>Class 6</option>

<option>Class 7</option>

<option>Class 8</option>

<option>Class 9</option>

<option>Class 10</option>

<option>Class 11</option>

<option>Class 12</option>

</optgroup>

<optgroup label="BSIT">

<option>BSIT Semester 1</option>

<option>BSIT Semester 2</option>

<option>BSIT Semester 3</option>

<option>BSIT Semester 4</option>

<option>BSIT Semester 5</option>

<option>BSIT Semester 6</option>

<option>BSIT Semester 7</option>

<option>BSIT Semester 8</option>

</optgroup>

<optgroup label="BSCS">

<option>BSCS Semester 1</option>

<option>BSCS Semester 2</option>

<option>BSCS Semester 3</option>

<option>BSCS Semester 4</option>

<option>BSCS Semester 5</option>

<option>BSCS Semester 6</option>

<option>BSCS Semester 7</option>

<option>BSCS Semester 8</option>

</optgroup>

<optgroup label="BBA">

<option>BBA Semester 1</option>

<option>BBA Semester 2</option>

<option>BBA Semester 3</option>

<option>BBA Semester 4</option>

<option>BBA Semester 5</option>

<option>BBA Semester 6</option>

<option>BBA Semester 7</option>

<option>BBA Semester 8</option>

</optgroup>

<optgroup label="ADP">

<option>ADP Semester 1</option>

<option>ADP Semester 2</option>

<option>ADP Semester 3</option>

<option>ADP Semester 4</option>

</optgroup>

</select>

</div>

<!-- Section -->

<div class="col-md-4 mb-3">

<label class="form-label fw-bold">

Section

</label>

<input
type="text"
name="section"
class="form-control"
placeholder="A / B / C">

</div>

<!-- Day -->

<div class="col-md-3 mb-3">

<label class="form-label fw-bold">

Day

</label>

<select
name="day"
class="form-select"
required>

<option>Monday</option>

<option>Tuesday</option>

<option>Wednesday</option>

<option>Thursday</option>

<option>Friday</option>

<option>Saturday</option>

</select>

</div>

<!-- Period -->

<div class="col-md-3 mb-3">

<label class="form-label fw-bold">

Period

</label>

<input
type="number"
name="period_no"
class="form-control"
min="1"
required>

</div>

<!-- Start -->

<div class="col-md-3 mb-3">

<label class="form-label fw-bold">

Start Time

</label>

<input
type="time"
name="start_time"
class="form-control"
required>

</div>

<!-- End -->

<div class="col-md-3 mb-3">

<label class="form-label fw-bold">

End Time

</label>

<input
type="time"
name="end_time"
class="form-control"
required>

</div>

<!-- Subject -->

<div class="col-md-4 mb-3">

<label class="form-label fw-bold">

Subject

</label>

<input
type="text"
name="subject"
class="form-control"
required>

</div>

<!-- Teacher -->

<div class="col-md-4 mb-3">

<label class="form-label fw-bold">

Teacher

</label>

<input
type="text"
name="teacher"
class="form-control"
required>

</div>

<!-- Room -->

<div class="col-md-4 mb-3">

<label class="form-label fw-bold">

Room No

</label>

<input
type="text"
name="room_no"
class="form-control">

</div>

<div class="col-12">

<button
type="submit"
name="save"
class="btn btn-primary">

<i class="fas fa-save me-2"></i>

Save Timetable

</button>

<button
type="reset"
class="btn btn-secondary">

Reset

</button>

</div>

</div>

</form>

</div>

</div>
<div class="card shadow border-0 rounded-4 mt-4">

<div class="card-header bg-dark text-white">

<h4>

<i class="fas fa-table me-2"></i>

All Timetables

</h4>

</div>

<div class="card-body">

<form method="GET">

<div class="row">

<div class="col-md-4 mb-3">

<input
type="text"
name="search"
class="form-control"
placeholder="Search Class, Subject or Teacher"
value="<?php echo $_GET['search'] ?? ''; ?>">

</div>

<div class="col-md-3 mb-3">

<select
name="level"
class="form-select">

<option value="">All Levels</option>

<option value="School">School</option>

<option value="University">University</option>

</select>

</div>

<div class="col-md-3 mb-3">

<select
name="day"
class="form-select">

<option value="">All Days</option>

<option>Monday</option>
<option>Tuesday</option>
<option>Wednesday</option>
<option>Thursday</option>
<option>Friday</option>
<option>Saturday</option>

</select>

</div>

<div class="col-md-2 mb-3 d-grid">

<button class="btn btn-primary">

<i class="fas fa-search"></i>

Search

</button>

</div>

</div>

</form>
<?php

$where = " WHERE 1 ";

if(!empty($_GET['search'])){

$search = mysqli_real_escape_string($conn,$_GET['search']);

$where .= "

AND (

class_name LIKE '%$search%'

OR

subject LIKE '%$search%'

OR

teacher LIKE '%$search%'

)

";

}

if(!empty($_GET['level'])){

$level = mysqli_real_escape_string($conn,$_GET['level']);

$where .= "

AND education_level='$level'

";

}

if(!empty($_GET['day'])){

$day = mysqli_real_escape_string($conn,$_GET['day']);

$where .= "

AND day='$day'

";

}

$list = mysqli_query(

$conn,

"

SELECT *

FROM timetable

$where

ORDER BY

class_name,

FIELD(

day,

'Monday',

'Tuesday',

'Wednesday',

'Thursday',

'Friday',

'Saturday'

),

period_no

"

);

?>
<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead class="table-primary">

<tr>

<th>ID</th>

<th>Level</th>

<th>Class</th>

<th>Section</th>

<th>Day</th>

<th>Period</th>

<th>Time</th>

<th>Subject</th>

<th>Teacher</th>

<th>Room</th>

<th width="130">

Action

</th>

</tr>

</thead>

<tbody>

<?php

if(mysqli_num_rows($list)>0){

while($row=mysqli_fetch_assoc($list)){

?>

<tr>

<td><?php echo $row['id']; ?></td>

<td>

<span class="badge bg-info">

<?php echo $row['education_level']; ?>

</span>

</td>

<td><?php echo $row['class_name']; ?></td>

<td><?php echo $row['section']; ?></td>

<td><?php echo $row['day']; ?></td>

<td><?php echo $row['period_no']; ?></td>

<td>

<?php

echo date("h:i A",strtotime($row['start_time']));

?>

-

<?php

echo date("h:i A",strtotime($row['end_time']));

?>

</td>

<td><?php echo $row['subject']; ?></td>

<td><?php echo $row['teacher']; ?></td>

<td><?php echo $row['room_no']; ?></td>

<td>
</a>
<a href="#" onclick="window.print()" class="btn btn-success mb-3">

<i class="fas fa-print me-2"></i>

Print Timetable

</a>
<a
href="edit_timetable.php?id=<?php echo $row['id']; ?>"
class="btn btn-warning btn-sm">

<i class="fas fa-edit"></i>

</a>

<a
href="delete_timetable.php?id=<?php echo $row['id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this timetable?')">

<i class="fas fa-trash"></i>

</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="11" class="text-center">

No timetable found.

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>