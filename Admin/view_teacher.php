<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/*=====================================
    CHECK ID
======================================*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid Teacher ID.";

    header("Location: manage_teachers.php");
    exit();
}

$id = (int)$_GET['id'];

/*=====================================
    FETCH TEACHER
======================================*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM teachers WHERE id=? LIMIT 1"
);

mysqli_stmt_bind_param($stmt,"i",$id);

mysqli_stmt_execute($stmt);

$result=mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)==0){

$_SESSION['error']="Teacher not found.";

header("Location: manage_teachers.php");

exit();

}

$teacher=mysqli_fetch_assoc($result);

$photo="assets/images/avatar.png";

if(
!empty($teacher['photo']) &&
file_exists("../uploads/teachers/".$teacher['photo'])
){

$photo="../uploads/teachers/".$teacher['photo'];

}
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Teacher Profile</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

body{

background:#f5f7fb;

font-family:'Poppins',sans-serif;

}

.profile-card{

border:none;

border-radius:20px;

box-shadow:0 15px 35px rgba(0,0,0,.08);

overflow:hidden;

}

.profile-header{

background:linear-gradient(135deg,#0d6efd,#20c997);

height:170px;

}

.avatar{

margin-top:-70px;

border:6px solid white;

object-fit:cover;

width:140px;

height:140px;

}

.info-box{

padding:15px;

border-radius:12px;

background:#f8f9fa;

margin-bottom:15px;

}

</style>

</head>

<body>

<div class="container py-5">

<div class="card profile-card">

<div class="profile-header"></div>

<div class="card-body text-center">

<img

src="<?php echo $photo; ?>"

class="rounded-circle avatar shadow">

<h2 class="mt-3">

<?php echo htmlspecialchars($teacher['full_name']); ?>

</h2>

<p class="text-muted">

<?php echo htmlspecialchars($teacher['department']); ?>

</p>

<?php if($teacher['status']=="Active"){ ?>

<span class="badge bg-success fs-6">

Active

</span>

<?php }else{ ?>

<span class="badge bg-danger fs-6">

Inactive

</span>

<?php } ?>

<hr>

<div class="row mt-4">

<div class="col-md-6">

<div class="info-box">

<h6>

<i class="fas fa-envelope text-primary"></i>

Email

</h6>

<p>

<?php echo htmlspecialchars($teacher['email']); ?>

</p>

</div>

</div>

<div class="col-md-6">

<div class="info-box">

<h6>

<i class="fas fa-phone text-success"></i>

Phone

</h6>

<p>

<?php echo htmlspecialchars($teacher['phone']); ?>

</p>

</div>

</div>

<div class="col-md-6">

<div class="info-box">

<h6>

<i class="fas fa-venus-mars text-warning"></i>

Gender

</h6>

<p>

<?php echo htmlspecialchars($teacher['gender']); ?>

</p>

</div>

</div>

<div class="col-md-6">

<div class="info-box">

<h6>

<i class="fas fa-building text-info"></i>

Department

</h6>

<p>

<?php echo htmlspecialchars($teacher['department']); ?>

</p>

</div>

</div>

<div class="col-md-6">

<div class="info-box">

<h6>

<i class="fas fa-graduation-cap text-danger"></i>

Qualification

</h6>

<p>

<?php echo htmlspecialchars($teacher['qualification']); ?>

</p>

</div>

</div>

<div class="col-md-6">

<div class="info-box">

<h6>

<i class="fas fa-briefcase text-secondary"></i>

Experience

</h6>

<p>

<?php echo (int)$teacher['experience']; ?>

Years

</p>

</div>

</div>

<div class="col-md-12">

<div class="info-box">

<h6>

<i class="fas fa-calendar-alt text-primary"></i>

Joined On

</h6>

<p>

<?php echo date("d M Y",strtotime($teacher['created_at'])); ?>

</p>

</div>

</div>

</div>

<div class="mt-4">

<a

href="edit_teacher.php?id=<?php echo $teacher['id']; ?>"

class="btn btn-warning">

<i class="fas fa-edit me-2"></i>

Edit Teacher

</a>

<a

href="manage_teachers.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

</div>

</div>

</div>

</div>

</body>

</html>