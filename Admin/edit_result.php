<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/*=========================================
    VALIDATE RESULT ID
=========================================*/

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){

    $_SESSION['error']="Invalid Result ID.";

    header("Location: manage_results.php");
    exit();

}

$id=(int)$_GET['id'];

/*=========================================
    LOAD RESULT
=========================================*/

$stmt=mysqli_prepare(

$conn,

"SELECT * FROM results WHERE id=? LIMIT 1"

);

mysqli_stmt_bind_param($stmt,"i",$id);

mysqli_stmt_execute($stmt);

$result=mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)==0){

    $_SESSION['error']="Result not found.";

    header("Location: manage_results.php");
    exit();

}

$row=mysqli_fetch_assoc($result);

/*=========================================
    LOAD STUDENTS
=========================================*/

$students=mysqli_query(

$conn,

"SELECT id,full_name,roll_number
FROM students
ORDER BY full_name"

);

/*=========================================
    LOAD COURSES
=========================================*/

$courses=mysqli_query(

$conn,

"SELECT id,course_name
FROM courses
ORDER BY course_name"

);

/*=========================================
    UPDATE RESULT
=========================================*/

if(isset($_POST['update_result'])){

    $student_id=(int)$_POST['student_id'];

    $course_id=(int)$_POST['course_id'];

    $subject=trim($_POST['subject']);

    $marks=(int)$_POST['marks'];

    $total_marks=(int)$_POST['total_marks'];

    $exam_type=trim($_POST['exam_type']);

    $remarks=trim($_POST['remarks']);

    if($marks<0){

        $_SESSION['error']="Marks cannot be negative.";

    }

    elseif($marks>$total_marks){

        $_SESSION['error']="Marks cannot exceed total marks.";

    }

    else{

        $percentage=($marks/$total_marks)*100;

        if($percentage>=90){

            $grade="A+";

        }

        elseif($percentage>=80){

            $grade="A";

        }

        elseif($percentage>=70){

            $grade="B";

        }

        elseif($percentage>=60){

            $grade="C";

        }

        elseif($percentage>=50){

            $grade="D";

        }

        else{

            $grade="F";

        }

        $update=mysqli_prepare(

        $conn,

        "UPDATE results SET

        student_id=?,
        course_id=?,
        subject=?,
        marks=?,
        total_marks=?,
        grade=?,
        exam_type=?,
        remarks=?

        WHERE id=?"

        );

        mysqli_stmt_bind_param(

        $update,

        "iisiiissi",

        $student_id,
        $course_id,
        $subject,
        $marks,
        $total_marks,
        $grade,
        $exam_type,
        $remarks,
        $id

        );

        if(mysqli_stmt_execute($update)){

            $_SESSION['success']="Result updated successfully.";

            header("Location: manage_results.php");
            exit();

        }else{

            $_SESSION['error']="Database error.";

        }

    }

}
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Edit Result</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

body{
background:#f4f7fb;
font-family:'Poppins',sans-serif;
}

.card{
border:none;
border-radius:20px;
box-shadow:0 10px 30px rgba(0,0,0,.08);
}

.card-header{
background:linear-gradient(135deg,#0d6efd,#20c997);
color:white;
padding:25px;
}

</style>

</head>

<body>

<div class="container py-5">
<?php if(isset($_SESSION['error'])){ ?>

<div class="alert alert-danger alert-dismissible fade show">

<i class="fas fa-circle-exclamation me-2"></i>

<?php

echo $_SESSION['error'];

unset($_SESSION['error']);

?>

<button
type="button"
class="btn-close"
data-bs-dismiss="alert"></button>

</div>

<?php } ?>

<div class="card">

<div class="card-header">

<h3>

<i class="fas fa-edit me-2"></i>

Edit Student Result

</h3>

</div>

<div class="card-body">

<form method="POST">

<div class="row">

<!-- Student -->

<div class="col-md-6 mb-3">

<label class="form-label">

Student

</label>

<select

name="student_id"

class="form-select"

required>

<?php

mysqli_data_seek($students,0);

while($student=mysqli_fetch_assoc($students)){

?>

<option

value="<?php echo $student['id']; ?>"

<?php

if($student['id']==$row['student_id']){

echo "selected";

}

?>>

<?php

echo htmlspecialchars(

$student['full_name']

)." (".$student['roll_number'].")";

?>

</option>

<?php } ?>

</select>

</div>

<!-- Course -->

<div class="col-md-6 mb-3">

<label class="form-label">

Course

</label>

<select

name="course_id"

class="form-select"

required>

<?php

mysqli_data_seek($courses,0);

while($course=mysqli_fetch_assoc($courses)){

?>

<option

value="<?php echo $course['id']; ?>"

<?php

if($course['id']==$row['course_id']){

echo "selected";

}

?>>

<?php

echo htmlspecialchars(

$course['course_name']

);

?>

</option>

<?php } ?>

</select>

</div>

<!-- Subject -->

<div class="col-md-6 mb-3">

<label class="form-label">

Subject

</label>

<input

type="text"

name="subject"

class="form-control"

value="<?php echo htmlspecialchars($row['subject']); ?>"

required>

</div>

<!-- Exam Type -->

<div class="col-md-6 mb-3">

<label class="form-label">

Exam Type

</label>

<select

name="exam_type"

class="form-select"

required>

<?php

$examTypes=[

"Quiz",

"Assignment",

"Mid Term",

"Final Term",

"Practical"

];

foreach($examTypes as $exam){

?>

<option

value="<?php echo $exam; ?>"

<?php

if($row['exam_type']==$exam){

echo "selected";

}

?>>

<?php echo $exam; ?>

</option>

<?php } ?>

</select>

</div>

<!-- Marks -->

<div class="col-md-6 mb-3">

<label class="form-label">

Obtained Marks

</label>

<input

type="number"

name="marks"

class="form-control"

min="0"

value="<?php echo $row['marks']; ?>"

required>

</div>

<!-- Total Marks -->

<div class="col-md-6 mb-3">

<label class="form-label">

Total Marks

</label>

<input

type="number"

name="total_marks"

class="form-control"

min="1"

value="<?php echo $row['total_marks']; ?>"

required>

</div>

<!-- Grade -->

<div class="col-md-6 mb-3">

<label class="form-label">

Current Grade

</label>

<input

type="text"

class="form-control bg-light"

value="<?php echo $row['grade']; ?>"

readonly>

</div>

<!-- Percentage -->

<div class="col-md-6 mb-3">

<label class="form-label">

Percentage

</label>

<input

type="text"

class="form-control bg-light"

value="<?php echo round(($row['marks']/$row['total_marks'])*100,2); ?>%"

readonly>

</div>

<!-- Remarks -->

<div class="col-12 mb-4">

<label class="form-label">

Remarks

</label>

<textarea

name="remarks"

rows="5"

class="form-control"><?php

echo htmlspecialchars($row['remarks']);

?></textarea>

</div>

<div class="text-end">

<a

href="manage_results.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<button

type="submit"

name="update_result"

class="btn btn-primary">

<i class="fas fa-save me-2"></i>

Update Result

</button>

</div>

</form>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>