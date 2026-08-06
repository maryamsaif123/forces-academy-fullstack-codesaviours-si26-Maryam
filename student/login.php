<?php
session_start();

include("../config/database.php");


// If student already logged in
if(isset($_SESSION['student_id'])){
    header("Location: dashboard.php");
    exit();
}


$error = "";


if(isset($_POST['login'])){

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];


    // Fetch student record
    $query = "SELECT * FROM students WHERE email=?";

    $stmt = mysqli_prepare($conn, $query);

    mysqli_stmt_bind_param($stmt, "s", $email);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);



    if(mysqli_num_rows($result) == 1){

        $student = mysqli_fetch_assoc($result);



        // Check hashed password
        if(password_verify($password, $student['password'])){


            $_SESSION['student_id'] = $student['id'];
            $_SESSION['student_name'] = $student['full_name'];
            $_SESSION['student_email'] = $student['email'];


            header("Location: dashboard.php");
            exit();


        }else{

            $error = "Incorrect Password!";

        }



    }else{

        $error = "Student account not found!";

    }

}

?>


<!DOCTYPE html>
<html>
<head>

<title>Student Login</title>

<meta name="viewport" content="width=device-width, initial-scale=1">


<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


<style>

body{

    min-height:100vh;
    background:linear-gradient(135deg,#0d6efd,#6610f2);
    display:flex;
    justify-content:center;
    align-items:center;

}


.login-card{

    width:400px;
    background:white;
    padding:35px;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,.2);

}


.logo{

    width:80px;
    height:80px;
    background:#0d6efd;
    color:white;
    border-radius:50%;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:35px;
    margin:auto;

}


.form-control{

    height:45px;
    border-radius:10px;

}


.btn-login{

    height:45px;
    border-radius:10px;
    font-weight:bold;

}


</style>


</head>


<body>


<div class="login-card">


<div class="logo">
    🎓
</div>


<h3 class="text-center mt-3 mb-4">

Student Login

</h3>



<?php if($error!=""){ ?>

<div class="alert alert-danger">

<?php echo $error; ?>

</div>

<?php } ?>




<form method="POST">


<div class="mb-3">

<label class="form-label">
Email Address
</label>

<input 
type="email"
name="email"
class="form-control"
placeholder="Enter your email"
required>

</div>




<div class="mb-3">

<label class="form-label">
Password
</label>


<input 
type="password"
name="password"
class="form-control"
placeholder="Enter password"
required>


</div>




<button 
type="submit"
name="login"
class="btn btn-primary w-100 btn-login">

Login

</button>



</form>



<div class="text-center mt-3">

<p>
Don't have an account?
<a href="register.php">
Register Now
</a>
</p>

</div>



</div>



</body>

</html>