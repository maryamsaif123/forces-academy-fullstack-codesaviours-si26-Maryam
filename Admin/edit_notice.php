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

mysqli_stmt_bind_param($stmt,"i",$id);

mysqli_stmt_execute($stmt);

$result=mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)==0){

    $_SESSION['error']="Notice not found.";

    header("Location: manage_notices.php");

    exit();
}

$notice=mysqli_fetch_assoc($result);

/*=========================================
    UPDATE NOTICE
=========================================*/

if(isset($_POST['update'])){

$title=trim($_POST['title']);

$content=trim($_POST['content']);

$posted_by=trim($_POST['posted_by']);

if(
empty($title) ||
empty($content) ||
empty($posted_by)
){

$_SESSION['error']="Please fill all required fields.";

header("Location: edit_notice.php?id=".$id);

exit();

}

/*=========================================
    DUPLICATE CHECK
=========================================*/

$check=mysqli_prepare(

$conn,

"SELECT id
FROM notices
WHERE title=?
AND id<>?"

);

mysqli_stmt_bind_param(

$check,

"si",

$title,

$id

);

mysqli_stmt_execute($check);

mysqli_stmt_store_result($check);

if(mysqli_stmt_num_rows($check)>0){

$_SESSION['error']="A notice with this title already exists.";

header("Location: edit_notice.php?id=".$id);

exit();

}

/*=========================================
    UPDATE QUERY
=========================================*/

$update=mysqli_prepare(

$conn,

"UPDATE notices SET

title=?,
content=?,
posted_by=?

WHERE id=?"

);

mysqli_stmt_bind_param(

$update,

"sssi",

$title,
$content,
$posted_by,
$id

);

if(mysqli_stmt_execute($update)){

$_SESSION['success']="Notice updated successfully.";

header("Location: manage_notices.php");

exit();

}else{

$_SESSION['error']="Database Error.";

}

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

Edit Notice

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

<style>

body{

background:#f4f7fb;

font-family:'Poppins',sans-serif;

}

.card{

border:none;

border-radius:20px;

box-shadow:0 15px 40px rgba(0,0,0,.08);

}

.card-header{

background:linear-gradient(135deg,#0d6efd,#20c997);

color:white;

padding:20px;

}

</style>

</head>

<body>

<div class="container py-5">

<div class="card">

<div class="card-header">

<h3>

<i class="fas fa-edit me-2"></i>

Edit Notice

</h3>

</div>

<div class="card-body">

<form method="POST">

<div class="row">

<!-- Notice Title -->

<div class="col-12 mb-3">

<label class="form-label">

Notice Title

</label>

<input

type="text"

name="title"

class="form-control"

value="<?php echo htmlspecialchars($notice['title']); ?>"

required>

</div>

<!-- Notice Content -->

<div class="col-12 mb-3">

<label class="form-label">

Notice Content

</label>

<textarea

name="content"

rows="8"

class="form-control"

required><?php echo htmlspecialchars($notice['content']); ?></textarea>

</div>

<!-- Posted By -->

<div class="col-md-6 mb-4">

<label class="form-label">

Posted By

</label>

<input

type="text"

name="posted_by"

class="form-control"

value="<?php echo htmlspecialchars($notice['posted_by']); ?>"

required>

</div>

<!-- Created Date -->

<div class="col-md-6 mb-4">

<label class="form-label">

Created Date

</label>

<input

type="text"

class="form-control"

value="<?php echo date('d M Y h:i A', strtotime($notice['created_at'])); ?>"

readonly>

</div>

<hr>

<div class="text-end mt-3">

<a

href="manage_notices.php"

class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<button

type="submit"

name="update"

class="btn btn-primary">

<i class="fas fa-save me-2"></i>

Update Notice

</button>

</div>

</div>

</form>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>