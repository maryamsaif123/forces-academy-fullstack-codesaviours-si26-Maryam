<?php
session_start();

include("../config/database.php");

// If already logged in
if(isset($_SESSION['admin_id'])){
    header("Location: dashboard.php");
    exit();
}

$error = "";

if(isset($_POST['login'])){

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if(empty($email) || empty($password)){
        $error = "Please enter your email and password.";
    }else{

        $stmt = mysqli_prepare($conn,
        "SELECT * FROM admins WHERE email=? LIMIT 1");

        mysqli_stmt_bind_param($stmt,"s",$email);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($result)==1){

            $admin = mysqli_fetch_assoc($result);

            if(password_verify($password,$admin['password'])){

                $_SESSION['admin_id']       = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_email']    = $admin['email'];
                $_SESSION['admin_photo']    = $admin['photo'];
                $_SESSION['admin_role']     = $admin['role'];

                header("Location: dashboard.php");
                exit();

            }else{

                $error="Incorrect password.";

            }

        }else{

            $error="Admin account not found.";

        }

    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Forces Academy LMS | Admin Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Poppins',sans-serif;
}

body{

background:linear-gradient(135deg,#0f172a,#1e3a8a,#10b981);

height:100vh;

display:flex;

justify-content:center;

align-items:center;

overflow:hidden;

}

.login-card{

width:430px;

background:#fff;

border-radius:20px;

padding:40px;

box-shadow:0 20px 50px rgba(0,0,0,.3);

animation:fade .7s;

}

@keyframes fade{

from{

opacity:0;

transform:translateY(30px);

}

to{

opacity:1;

transform:translateY(0);

}

}

.logo{

width:90px;

height:90px;

border-radius:50%;

background:#10b981;

display:flex;

align-items:center;

justify-content:center;

margin:auto;

margin-bottom:20px;

color:white;

font-size:40px;

}

.form-control{

height:50px;

border-radius:10px;

}

.btn-login{

height:50px;

font-weight:600;

border-radius:10px;

background:#10b981;

border:none;

transition:.3s;

}

.btn-login:hover{

background:#059669;

transform:translateY(-2px);

}

.footer{

margin-top:20px;

font-size:14px;

color:#777;

text-align:center;

}

</style>

</head>

<body>

<div class="login-card">

<div class="logo">

<i class="fas fa-graduation-cap"></i>

</div>

<h2 class="text-center fw-bold">

Forces Academy LMS

</h2>

<p class="text-center text-muted mb-4">

Administrator Login

</p>

<?php if($error!=""){ ?>

<div class="alert alert-danger">

<i class="fas fa-circle-exclamation"></i>

<?php echo $error; ?>

</div>

<?php } ?>

<form method="POST">

<div class="mb-3">

<label class="mb-2">

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

placeholder="admin@forcesacademy.com"

required>

</div>

</div>

<div class="mb-4">

<label class="mb-2">

Password

</label>

<div class="input-group">

<span class="input-group-text">

<i class="fas fa-lock"></i>

</span>

<input

type="password"

name="password"

id="password"

class="form-control"

placeholder="Enter Password"

required>

<button

class="btn btn-outline-secondary"

type="button"

onclick="togglePassword()">

<i class="fas fa-eye" id="eye"></i>

</button>

</div>

</div>

<button

type="submit"

name="login"

class="btn btn-login w-100">

<i class="fas fa-right-to-bracket"></i>

Login

</button>

</form>

<div class="footer">

© <?php echo date('Y'); ?>

Forces Academy LMS

</div>

</div>

<script>

function togglePassword(){

let p=document.getElementById("password");

let eye=document.getElementById("eye");

if(p.type==="password"){

p.type="text";

eye.className="fas fa-eye-slash";

}else{

p.type="password";

eye.className="fas fa-eye";

}

}

</script>

</body>

</html>