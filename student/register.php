<?php
session_start();
include("../config/database.php");

$success = "";
$error = "";

if(isset($_POST['register'])){

$full_name = trim($_POST['full_name']);
$email = trim($_POST['email']);
$education_level = trim($_POST['education_level']);
$class = trim($_POST['class']);
$gender = $_POST['gender'];
$phone = trim($_POST['phone']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
    /*=========================================
        VALIDATION
    =========================================*/

   if(
empty($full_name) ||
empty($email) ||
empty($education_level) ||
empty($class) ||
empty($gender) ||
empty($phone) ||
empty($password) ||
empty($confirm_password)
){

        $error = "All fields are required.";

    }

    elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)){

        $error = "Invalid email address.";

    }

    elseif($password != $confirm_password){

        $error = "Passwords do not match.";

    }

    else{

        /*=========================================
            CHECK EMAIL
        =========================================*/

        $check = mysqli_prepare(
$conn,
"SELECT id
FROM students
WHERE email=?"
);

mysqli_stmt_bind_param(
$check,
"s",
$email
);

        mysqli_stmt_execute($check);

        mysqli_stmt_store_result($check);

        if(mysqli_stmt_num_rows($check)>0){

$error="Email already exists.";
        }

        else{

            /*=========================================
                PHOTO UPLOAD
            =========================================*/

            $photo = "";

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

                    $photo = time()."_".rand(1000,9999).".".$extension;

                    $upload_dir = "../uploads/students/";

if(!is_dir($upload_dir)){
    mkdir($upload_dir, 0777, true);
}

if(!move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo)){
    $photo = "";
}

                }

            }

            /*=========================================
                PASSWORD HASH
            =========================================*/

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            /*=========================================
                INSERT STUDENT
            =========================================*/

            $insert = mysqli_prepare(

                $conn,

                "INSERT INTO students

                (
full_name,
email,
education_level,
class,
gender,
phone,
password,
photo
)
                VALUES

                (?,?,?,?,?,?,?,?)"

            );

mysqli_stmt_bind_param(

$insert,

"ssssssss",

$full_name,
$email,
$education_level,
$class,
$gender,
$phone,
$hashedPassword,
$photo

);
            if(mysqli_stmt_execute($insert)){

    $_SESSION['success'] = "Registration successful. Please login.";

    header("Location: login.php");
    exit();

}else{

    $error = "Registration failed.";

}
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

Student Registration

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

font-family:'Poppins',sans-serif;

background:linear-gradient(135deg,#2563eb,#3b82f6);

min-height:100vh;

display:flex;

justify-content:center;

align-items:center;

padding:30px;

}

.register-card{

background:white;

border-radius:20px;

box-shadow:0 20px 45px rgba(0,0,0,.18);

padding:40px;

width:100%;

max-width:850px;

}

.form-control,
.form-select{

border-radius:12px;

}

.btn-register{

border-radius:12px;

padding:12px;

font-weight:600;

}

</style>

</head>

<body>

<div class="register-card">

<h2 class="text-center mb-4">

<i class="fas fa-user-plus text-primary"></i>

Student Registration

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

<form

method="POST"

enctype="multipart/form-data">

<div class="row">
<div class="col-md-6 mb-3">

<label class="form-label">

Full Name

</label>

<div class="input-group">

<span class="input-group-text">

<i class="fas fa-user"></i>

</span>

<input

type="text"

name="full_name"

class="form-control"

placeholder="Enter full name"

required>

</div>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Email Address

</label>

<div class="input-group">

<span class="input-group-text">

<i class="fas fa-envelope"></i>

</span>

<input

type="email"

name="email"

class="form-control"

placeholder="Enter email"

required>

</div>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Education Level

</label>

<div class="input-group">

<span class="input-group-text">

<i class="fas fa-school"></i>

</span>

<select
name="education_level"
class="form-select"
required>

<option value="">Select Level</option>

<option value="School">School</option>

<option value="College">College</option>

<option value="University">University</option>

</select>

</div>

</div>
<div class="col-md-6 mb-3">

<label class="form-label">

Class / Semester

</label>

<div class="input-group">

<span class="input-group-text">

<i class="fas fa-graduation-cap"></i>

</span>

<select
name="class"
class="form-select"
required>

<option value="">Select Class / Semester</option>

<!-- School -->

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

<!-- College -->

<optgroup label="College">

<option>1st Year</option>
<option>2nd Year</option>

</optgroup>

<!-- University -->

<optgroup label="University">

<option>BSIT Semester 1</option>
<option>BSIT Semester 2</option>
<option>BSIT Semester 3</option>
<option>BSIT Semester 4</option>
<option>BSIT Semester 5</option>
<option>BSIT Semester 6</option>
<option>BSIT Semester 7</option>
<option>BSIT Semester 8</option>

<option>BSCS Semester 1</option>
<option>BSCS Semester 2</option>
<option>BSCS Semester 3</option>
<option>BSCS Semester 4</option>
<option>BSCS Semester 5</option>
<option>BSCS Semester 6</option>
<option>BSCS Semester 7</option>
<option>BSCS Semester 8</option>

<option>BBA Semester 1</option>
<option>BBA Semester 2</option>
<option>BBA Semester 3</option>
<option>BBA Semester 4</option>
<option>BBA Semester 5</option>
<option>BBA Semester 6</option>
<option>BBA Semester 7</option>
<option>BBA Semester 8</option>

<option>ADP Semester 1</option>
<option>ADP Semester 2</option>
<option>ADP Semester 3</option>
<option>ADP Semester 4</option>

</optgroup>

</select>

</div>

</div>
<div class="col-md-6 mb-3">

<label class="form-label">

Gender

</label>

<select

name="gender"

class="form-select"

required>

<option value="">

Select Gender

</option>

<option value="Male">

Male

</option>

<option value="Female">

Female

</option>

<option value="Other">

Other

</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Phone Number

</label>

<div class="input-group">

<span class="input-group-text">

<i class="fas fa-phone"></i>

</span>

<input

type="text"

name="phone"

class="form-control"

placeholder="03XXXXXXXXX"

required>

</div>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Password

</label>

<div class="input-group">

<input

type="password"

name="password"

id="password"

class="form-control"

placeholder="Create password"

required>

<button

class="btn btn-outline-secondary"

type="button"

onclick="togglePassword('password')">

<i class="fas fa-eye"></i>

</button>

</div>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Confirm Password

</label>

<div class="input-group">

<input

type="password"

name="confirm_password"

id="confirm_password"

class="form-control"

placeholder="Confirm password"

required>

<button

class="btn btn-outline-secondary"

type="button"

onclick="togglePassword('confirm_password')">

<i class="fas fa-eye"></i>

</button>

</div>

</div>

<div class="col-md-12 mb-4">

<label class="form-label">

Profile Photo

</label>

<input

type="file"

name="photo"

class="form-control"

accept=".jpg,.jpeg,.png,.webp">

</div>

<div class="col-md-12 d-grid">

<button

type="submit"

name="register"

class="btn btn-primary btn-register">

<i class="fas fa-user-plus me-2"></i>

Create Student Account

</button>

</div>

<div class="col-md-12 text-center mt-4">

Already have an account?

<a

href="login.php"

class="text-decoration-none fw-bold">

Login Here

</a>

</div>

</div>

</form>

</div>

<script>

function togglePassword(id){

const field=document.getElementById(id);

field.type=(field.type==="password")?"text":"password";

}

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>