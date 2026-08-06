<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

if ($_SERVER['REQUEST_METHOD'] != "POST") {
    header("Location: manage_teachers.php");
    exit();
}

/*==============================
    GET FORM DATA
==============================*/

$full_name     = trim($_POST['full_name']);
$gender        = trim($_POST['gender']);
$email         = trim($_POST['email']);
$password      = trim($_POST['password']);
$phone         = trim($_POST['phone']);
$department    = trim($_POST['department']);
$qualification = trim($_POST['qualification']);
$experience    = (int)$_POST['experience'];

$status = "Active";

/*==============================
    VALIDATION
==============================*/

if (
    empty($full_name) ||
    empty($email) ||
    empty($password) ||
    empty($gender)
) {

    $_SESSION['error']="Please fill all required fields.";

    header("Location: manage_teachers.php");

    exit();
}

/*==============================
    CHECK DUPLICATE EMAIL
==============================*/

$check=mysqli_prepare(
$conn,
"SELECT id FROM teachers WHERE email=?"
);

mysqli_stmt_bind_param($check,"s",$email);

mysqli_stmt_execute($check);

mysqli_stmt_store_result($check);

if(mysqli_stmt_num_rows($check)>0){

$_SESSION['error']="Email already exists.";

header("Location: manage_teachers.php");

exit();

}

/*==============================
    HASH PASSWORD
==============================*/

$hashedPassword=password_hash(
$password,
PASSWORD_DEFAULT
);

/*==============================
    PHOTO UPLOAD
==============================*/

$photo=NULL;

if(isset($_FILES['photo']) && $_FILES['photo']['error']==0){

$allowed=['jpg','jpeg','png','gif','webp'];

$ext=strtolower(pathinfo(
$_FILES['photo']['name'],
PATHINFO_EXTENSION
));

if(in_array($ext,$allowed)){

$photo=time()."_".uniqid().".".$ext;

$uploadDir="../uploads/teachers/";

if(!is_dir($uploadDir)){
mkdir($uploadDir,0777,true);
}

move_uploaded_file(

$_FILES['photo']['tmp_name'],

$uploadDir.$photo

);

}

}

/*==============================
    INSERT
==============================*/

$stmt=mysqli_prepare($conn,

"INSERT INTO teachers(

full_name,
gender,
email,
password,
phone,
department,
qualification,
experience,
photo,
status

)

VALUES(

?,?,?,?,?,?,?,?,?,?

)"

);

mysqli_stmt_bind_param(

$stmt,

"sssssssiss",

$full_name,

$gender,

$email,

$hashedPassword,

$phone,

$department,

$qualification,

$experience,

$photo,

$status

);

if(mysqli_stmt_execute($stmt)){

$_SESSION['success']="Teacher added successfully.";

}else{

$_SESSION['error']="Database Error.";

}

header("Location: manage_teachers.php");

exit();
?>