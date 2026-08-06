<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$student_id = $_SESSION['student_id'];

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM students WHERE id=? LIMIT 1"
);

mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

if (!$student) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$success = "";
$error = "";

/*=========================================
    UPDATE PROFILE
=========================================*/

if(isset($_POST['update'])){

    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $class     = trim($_POST['class']);
    $gender    = $_POST['gender'];

    if(empty($full_name) || empty($email) || empty($class) || empty($gender)){

        $error = "Please fill all required fields.";

    }elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)){

        $error = "Invalid email address.";

    }else{

        /*=============================
            CHECK DUPLICATE EMAIL
        =============================*/

        $check = mysqli_prepare(

            $conn,

            "SELECT id
             FROM students
             WHERE email=?
             AND id!=?"

        );

        mysqli_stmt_bind_param(

            $check,

            "si",

            $email,

            $student_id

        );

        mysqli_stmt_execute($check);

        mysqli_stmt_store_result($check);

        if(mysqli_stmt_num_rows($check)>0){

            $error = "Email already exists.";

        }else{

            /*=============================
                PHOTO UPLOAD
            =============================*/

            $photo = $student['photo'];

            if(isset($_FILES['photo']) &&
               $_FILES['photo']['error']==0){

                $extension = strtolower(

                    pathinfo(

                        $_FILES['photo']['name'],

                        PATHINFO_EXTENSION

                    )

                );

                $allowed = ['jpg','jpeg','png','webp'];

                if(in_array($extension,$allowed)){

                    if(!empty($photo) &&
                       file_exists("../uploads/students/".$photo)){

                        unlink("../uploads/students/".$photo);

                    }

                    $photo = time().rand(1000,9999).".".$extension;

                    move_uploaded_file(

                        $_FILES['photo']['tmp_name'],

                        "../uploads/students/".$photo

                    );

                }

            }

            /*=============================
                UPDATE DATABASE
            =============================*/

            $update = mysqli_prepare(

                $conn,

                "UPDATE students SET

                full_name=?,
                email=?,
                phone=?,
                class=?,
                gender=?,
                photo=?

                WHERE id=?"

            );

            mysqli_stmt_bind_param(

                $update,

                "ssssssi",

                $full_name,
                $email,
                $phone,
                $class,
                $gender,
                $photo,
                $student_id

            );

            if(mysqli_stmt_execute($update)){

                $_SESSION['student_name'] = $full_name;
                $_SESSION['student_email'] = $email;

                $success = "Profile updated successfully.";

                $student['full_name'] = $full_name;
                $student['email'] = $email;
                $student['phone'] = $phone;
                $student['class'] = $class;
                $student['gender'] = $gender;
                $student['photo'] = $photo;

            }else{

                $error = "Failed to update profile.";

            }

        }

    }

}

$avatar = !empty($student['photo'])
?
"../uploads/students/".$student['photo']
:
"assets/images/avatar.png";
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Edit Profile</title>

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

box-shadow:0 15px 35px rgba(0,0,0,.10);

}

.profile-photo{

width:150px;

height:150px;

border-radius:50%;

object-fit:cover;

border:5px solid #0d6efd;

}

.form-control,
.form-select{

border-radius:12px;

height:48px;

}

.btn{

border-radius:12px;

}

</style>

</head>

<body>

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-lg-9">

<div class="card">

<div class="card-body p-5">

<h2 class="mb-4">

<i class="fas fa-user-edit text-primary me-2"></i>

Edit Profile

</h2>

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

<form method="POST" enctype="multipart/form-data">

<div class="row">
<!-- ==========================================
        PROFILE PHOTO
========================================== -->

<div class="col-md-12 text-center mb-4">

<img

src="<?php echo $avatar; ?>"

class="profile-photo shadow"

alt="Profile Photo">

</div>

<!-- ==========================================
        FULL NAME
========================================== -->

<div class="col-md-6 mb-3">

<label class="form-label">

Full Name <span class="text-danger">*</span>

</label>

<input

type="text"

name="full_name"

class="form-control"

value="<?php echo htmlspecialchars($student['full_name']); ?>"

required>

</div>

<!-- ==========================================
        EMAIL
========================================== -->

<div class="col-md-6 mb-3">

<label class="form-label">

Email Address <span class="text-danger">*</span>

</label>

<input

type="email"

name="email"

class="form-control"

value="<?php echo htmlspecialchars($student['email']); ?>"

required>

</div>

<!-- ==========================================
        PHONE
========================================== -->

<div class="col-md-6 mb-3">

<label class="form-label">

Phone Number

</label>

<input

type="text"

name="phone"

class="form-control"

value="<?php echo htmlspecialchars($student['phone']); ?>"

placeholder="03XXXXXXXXX">

</div>

<!-- ==========================================
        CLASS
========================================== -->

<div class="col-md-6 mb-3">

<label class="form-label">

Class <span class="text-danger">*</span>

</label>

<input

type="text"

name="class"

class="form-control"

value="<?php echo htmlspecialchars($student['class']); ?>"

required>

</div>

<!-- ==========================================
        GENDER
========================================== -->

<div class="col-md-6 mb-3">

<label class="form-label">

Gender <span class="text-danger">*</span>

</label>

<select

name="gender"

class="form-select"

required>

<option value="Male"

<?php if($student['gender']=="Male") echo "selected"; ?>>

Male

</option>

<option value="Female"

<?php if($student['gender']=="Female") echo "selected"; ?>>

Female

</option>

<option value="Other"

<?php if($student['gender']=="Other") echo "selected"; ?>>

Other

</option>

</select>

</div>

<!-- ==========================================
        PHOTO
========================================== -->

<div class="col-md-6 mb-4">

<label class="form-label">

Change Profile Picture

</label>

<input

type="file"

name="photo"

class="form-control"

accept=".jpg,.jpeg,.png,.webp">

<small class="text-muted">

Allowed: JPG, JPEG, PNG, WEBP

</small>

</div>

<!-- ==========================================
        BUTTONS
========================================== -->

<div class="col-md-12">

<hr>

<div class="d-flex flex-wrap gap-3 justify-content-between">

<a

href="profile.php"

class="btn btn-secondary px-4">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

<div>

<button

type="reset"

class="btn btn-warning px-4 me-2">

<i class="fas fa-rotate-left me-2"></i>

Reset

</button>

<button

type="submit"

name="update"

class="btn btn-primary px-4">

<i class="fas fa-save me-2"></i>

Save Changes

</button>

</div>

</div>

</div>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>