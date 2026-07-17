<?php
session_start();

if(!isset($_SESSION['student_id'])){
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$student_id=$_SESSION['student_id'];

if(!isset($_GET['id'])){
    header("Location: my_submissions.php");
    exit();
}

$assignment_id=$_GET['id'];

$query=mysqli_query($conn,"
SELECT

results.*,
assignments.title,
assignments.description,
assignments.deadline,
courses.course_name

FROM results

LEFT JOIN assignments
ON results.subject=assignments.title

LEFT JOIN courses
ON assignments.course_id=courses.id

WHERE

results.student_id='$student_id'

AND assignments.id='$assignment_id'

LIMIT 1

");

$result=mysqli_fetch_assoc($query);

?>

<div class="card shadow-lg border-0 rounded-4 mb-4">

<div class="card-body">

<div class="row">

<div class="col-md-4 text-center">

<?php

$badge="secondary";

if($result['grade']=="A+"){

$badge="success";

}elseif($result['grade']=="A"){

$badge="primary";

}elseif($result['grade']=="B"){

$badge="info";

}elseif($result['grade']=="C"){

$badge="warning";

}elseif($result['grade']=="D"){

$badge="secondary";

}else{

$badge="danger";

}

?>

<span class="badge bg-<?php echo $badge; ?> fs-1 p-4">

<?php echo $result['grade']; ?>

</span>

<script>

window.addEventListener("load",function(){

const cards=document.querySelectorAll(".card");

cards.forEach(function(card,index){

card.style.opacity="0";

card.style.transform="translateY(30px)";

setTimeout(function(){

card.style.transition="all .5s";

card.style.opacity="1";

card.style.transform="translateY(0)";

},index*150);

});

});

</script>
</div>

<div class="col-md-8">

<h3>

<?php echo $result['title']; ?>

</h3>

<p>

<?php echo $result['course_name']; ?>

</p>

<hr>

<div class="row">

<div class="col-md-4">

<h6>Marks</h6>

<h2>

<?php echo $result['marks']; ?>

</h2>

</div>

<div class="col-md-4">

<h6>Total</h6>

<h2>

<?php echo $result['total_marks']; ?>

</h2>

</div>

<div class="col-md-4">

<h6>Percentage</h6>

<h2>

<?php

echo round(

($result['marks']/$result['total_marks'])*100

);

?>%

</h2>

</div>

</div>

</div>

</div>

</div>

</div>

<!-- ======================================
     Assignment Details
====================================== -->

<div class="card shadow-lg border-0 rounded-4 mb-4">

<div class="card-header bg-success text-white">

<h4>

<i class="fas fa-book"></i>

Assignment Details

</h4>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-8">

<h4>

<?php echo htmlspecialchars($result['title']); ?>

</h4>

<p class="text-muted">

<?php echo nl2br(htmlspecialchars($result['description'])); ?>

</p>

</div>

<div class="col-md-4">

<div class="list-group">

<div class="list-group-item">

<strong>Course</strong>

<br>

<?php echo htmlspecialchars($result['course_name']); ?>

</div>

<div class="list-group-item">

<strong>Deadline</strong>

<br>

<?php echo date("d M Y",strtotime($result['deadline'])); ?>

</div>

<div class="list-group-item">

<strong>Assessment</strong>

<br>

<?php echo htmlspecialchars($result['exam_type']); ?>

</div>

</div>

</div>

</div>

</div>

</div>

<!-- ======================================
     Teacher Feedback
====================================== -->

<div class="card shadow-lg border-0 rounded-4 mb-4">

<div class="card-header bg-primary text-white">

<h4>

<i class="fas fa-comments"></i>

Teacher Feedback

</h4>

</div>

<div class="card-body">

<?php

if(!empty($result['remarks'])){

?>

<div class="alert alert-light border">

<?php echo nl2br(htmlspecialchars($result['remarks'])); ?>

</div>

<?php

}else{

?>

<div class="text-center py-4">

<i class="fas fa-comment-slash fa-4x text-muted mb-3"></i>

<h5>No Feedback Available</h5>

<p class="text-muted">

Your instructor hasn't added feedback yet.

</p>

</div>

<?php

}

?>

</div>

</div>

<div class="text-end mb-5">

<a href="my_submissions.php"

class="btn btn-secondary btn-lg">

<i class="fas fa-arrow-left"></i>

Back

</a>

<button

onclick="window.print()"

class="btn btn-success btn-lg">

<i class="fas fa-print"></i>

Print Result

</button>

</div>