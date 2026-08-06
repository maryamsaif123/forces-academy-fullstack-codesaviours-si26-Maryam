<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/*=====================================
    CHECK TEACHER ID
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

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    $_SESSION['error'] = "Teacher not found.";

    header("Location: manage_teachers.php");
    exit();
}

$teacher = mysqli_fetch_assoc($result);

/*=====================================
    UPDATE
======================================*/

if(isset($_POST['update'])){

$full_name     = trim($_POST['full_name']);
$email         = trim($_POST['email']);
$gender        = trim($_POST['gender']);
$phone         = trim($_POST['phone']);
$department    = trim($_POST['department']);
$qualification = trim($_POST['qualification']);
$experience    = (int)$_POST['experience'];
$status        = $_POST['status'];

/*=====================================
    CHECK DUPLICATE EMAIL
======================================*/

$check=mysqli_prepare(
$conn,
"SELECT id FROM teachers
WHERE email=? AND id<>?"
);

mysqli_stmt_bind_param(
$check,
"si",
$email,
$id
);

mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if(mysqli_stmt_num_rows($check)>0){

$_SESSION['error']="Email already exists.";

header("Location: edit_teacher.php?id=".$id);

exit();

}

/*=====================================
    PASSWORD
======================================*/

$password = $teacher['password'];

if(!empty($_POST['password'])){

$password = password_hash(
$_POST['password'],
PASSWORD_DEFAULT
);

}

/*=====================================
    PHOTO
======================================*/

$photo = $teacher['photo'];

if(isset($_FILES['photo']) &&
$_FILES['photo']['error']==0){

$allowed=[
'jpg',
'jpeg',
'png',
'gif',
'webp'
];

$ext=strtolower(
pathinfo(
$_FILES['photo']['name'],
PATHINFO_EXTENSION
)
);

if(in_array($ext,$allowed)){

if(!empty($photo) &&
file_exists("../uploads/teachers/".$photo)){

unlink("../uploads/teachers/".$photo);

}

$photo=time()."_".uniqid().".".$ext;

move_uploaded_file(

$_FILES['photo']['tmp_name'],

"../uploads/teachers/".$photo

);

}

}

/*=====================================
    UPDATE QUERY
======================================*/

$update=mysqli_prepare(

$conn,

"UPDATE teachers SET

full_name=?,
gender=?,
email=?,
password=?,
phone=?,
department=?,
qualification=?,
experience=?,
photo=?,
status=?

WHERE id=?"

);

mysqli_stmt_bind_param(

$update,

"sssssssissi",

$full_name,
$gender,
$email,
$password,
$phone,
$department,
$qualification,
$experience,
$photo,
$status,
$id

);

if(mysqli_stmt_execute($update)){

$_SESSION['success']="Teacher updated successfully.";

header("Location: manage_teachers.php");

exit();

}else{

$_SESSION['error']="Database Error.";

}

}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Edit Teacher</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link rel="stylesheet" href="assets/css/dashboard.css">

</head>

<body class="bg-light">

<div class="container py-5">

<div class="card shadow-lg border-0 rounded-4">

<div class="card-header bg-primary text-white">

<h3>

<i class="fas fa-user-edit me-2"></i>

Edit Teacher

</h3>

</div>

<div class="card-body">

<form method="POST" enctype="multipart/form-data">

<div class="row">
<!-- Full Name -->
<div class="col-md-6 mb-3">
    <label class="form-label">Full Name</label>
    <input
        type="text"
        name="full_name"
        class="form-control"
        value="<?php echo htmlspecialchars($teacher['full_name']); ?>"
        required>
</div>

<!-- Email -->
<div class="col-md-6 mb-3">
    <label class="form-label">Email Address</label>
    <input
        type="email"
        name="email"
        class="form-control"
        value="<?php echo htmlspecialchars($teacher['email']); ?>"
        required>
</div>

<!-- Password -->
<div class="col-md-6 mb-3">
    <label class="form-label">
        New Password
        <small class="text-muted">(Leave blank to keep current password)</small>
    </label>
    <input
        type="password"
        name="password"
        class="form-control">
</div>

<!-- Phone -->
<div class="col-md-6 mb-3">
    <label class="form-label">Phone</label>
    <input
        type="text"
        name="phone"
        class="form-control"
        value="<?php echo htmlspecialchars($teacher['phone']); ?>">
</div>

<!-- Gender -->
<div class="col-md-6 mb-3">
    <label class="form-label">Gender</label>

    <select
        name="gender"
        class="form-select"
        required>

        <option value="Male"
        <?php if($teacher['gender']=="Male") echo "selected"; ?>>
            Male
        </option>

        <option value="Female"
        <?php if($teacher['gender']=="Female") echo "selected"; ?>>
            Female
        </option>

        <option value="Other"
        <?php if($teacher['gender']=="Other") echo "selected"; ?>>
            Other
        </option>

    </select>
</div>

<!-- Department -->
<div class="col-md-6 mb-3">
    <label class="form-label">Department</label>
    <input
        type="text"
        name="department"
        class="form-control"
        value="<?php echo htmlspecialchars($teacher['department']); ?>">
</div>

<!-- Qualification -->
<div class="col-md-6 mb-3">
    <label class="form-label">Qualification</label>
    <input
        type="text"
        name="qualification"
        class="form-control"
        value="<?php echo htmlspecialchars($teacher['qualification']); ?>">
</div>

<!-- Experience -->
<div class="col-md-6 mb-3">
    <label class="form-label">Experience (Years)</label>
    <input
        type="number"
        name="experience"
        class="form-control"
        min="0"
        value="<?php echo $teacher['experience']; ?>">
</div>

<!-- Status -->
<div class="col-md-6 mb-3">
    <label class="form-label">Status</label>

    <select
        name="status"
        class="form-select">

        <option value="Active"
        <?php if($teacher['status']=="Active") echo "selected"; ?>>
            Active
        </option>

        <option value="Inactive"
        <?php if($teacher['status']=="Inactive") echo "selected"; ?>>
            Inactive
        </option>

    </select>
</div>

<!-- Current Photo -->
<div class="col-md-6 mb-3">

<label class="form-label">

Current Photo

</label>

<br>

<?php

if(!empty($teacher['photo']) &&
file_exists("../uploads/teachers/".$teacher['photo'])){

?>

<img

src="../uploads/teachers/<?php echo $teacher['photo']; ?>"

id="preview"

class="rounded-circle shadow"

width="140"

height="140"

style="object-fit:cover;">

<?php }else{ ?>

<img

src="assets/images/avatar.png"

id="preview"

class="rounded-circle shadow"

width="140"

height="140"

style="object-fit:cover;">

<?php } ?>

</div>

<!-- Upload New Photo -->
<div class="col-12 mb-4">

<label class="form-label">

Upload New Photo

</label>

<input

type="file"

name="photo"

class="form-control"

accept=".jpg,.jpeg,.png,.gif,.webp"

onchange="previewImage(event)">

</div>

<div class="col-12">

<button

type="submit"

name="update"

class="btn btn-success px-4">

<i class="fas fa-save me-2"></i>

Update Teacher

</button>

<a

href="manage_teachers.php"

class="btn btn-secondary px-4">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

</div>

</div>

</form>

</div>

</div>

</div>

<script>

function previewImage(event){

const reader=new FileReader();

reader.onload=function(){

document.getElementById("preview").src=reader.result;

};

reader.readAsDataURL(event.target.files[0]);

}

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>