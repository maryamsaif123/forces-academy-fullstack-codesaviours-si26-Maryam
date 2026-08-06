<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/*=========================================
    CHECK NOTICE ID
=========================================*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid Notice ID.";

    header("Location: manage_notices.php");
    exit();
}

$id = (int)$_GET['id'];

/*=========================================
    FETCH NOTICE
=========================================*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM notices WHERE id=? LIMIT 1"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    $_SESSION['error'] = "Notice not found.";

    header("Location: manage_notices.php");
    exit();
}

$notice = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>View Notice | Forces Academy LMS</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

body{
    background:#f5f7fb;
    font-family:'Poppins',sans-serif;
}

.notice-card{
    border:none;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 15px 35px rgba(0,0,0,.08);
}

.notice-header{
    background:linear-gradient(135deg,#0d6efd,#20c997);
    color:#fff;
    padding:45px;
}

.notice-body{
    padding:35px;
}

.info-box{
    background:#f8f9fa;
    border-radius:15px;
    padding:20px;
    margin-bottom:20px;
}

.notice-content{
    background:#ffffff;
    border-left:5px solid #0d6efd;
    padding:25px;
    border-radius:12px;
    line-height:1.9;
    font-size:16px;
}

.badge-admin{
    background:#0d6efd;
    padding:8px 16px;
    border-radius:50px;
    color:white;
}

</style>

</head>

<body>

<div class="container py-5">

<div class="card notice-card">

<div class="notice-header">

<h2>

<i class="fas fa-bullhorn me-2"></i>

<?php echo htmlspecialchars($notice['title']); ?>

</h2>

<p class="mt-3 mb-0">

Official Announcement

</p>

</div>

<div class="notice-body">

<div class="row">

<div class="col-lg-8">

<div class="notice-content">

<?php echo nl2br(htmlspecialchars($notice['content'])); ?>

</div>

</div>

<div class="col-lg-4">

<div class="info-box">

<h5>

<i class="fas fa-user me-2 text-primary"></i>

Posted By

</h5>

<p class="mt-3">

<span class="badge-admin">

<?php echo htmlspecialchars($notice['posted_by']); ?>

</span>

</p>

</div>

<div class="info-box">

<h5>

<i class="fas fa-calendar-alt me-2 text-success"></i>

Published Date

</h5>

<p class="mt-3">

<?php

echo date(

"d F Y",

strtotime($notice['created_at'])

);

?>

</p>

</div>

<div class="info-box">

<h5>

<i class="fas fa-clock me-2 text-danger"></i>

Published Time

</h5>

<p class="mt-3">

<?php

echo date(

"h:i A",

strtotime($notice['created_at'])

);

?>

</p>

</div>

</div>

</div>

<hr class="my-4">

<div class="d-flex justify-content-end gap-2">

<a
href="edit_notice.php?id=<?php echo $notice['id']; ?>"
class="btn btn-warning">

<i class="fas fa-edit me-2"></i>

Edit Notice

</a>

<a
href="manage_notices.php"
class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>