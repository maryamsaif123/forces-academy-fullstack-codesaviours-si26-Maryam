<?php

session_start();
include("../config/database.php");

if(isset($_POST['login']))
{
    $username = mysqli_real_escape_string($conn,$_POST['username']);
    $password = mysqli_real_escape_string($conn,$_POST['password']);

    $query = "SELECT * FROM admins WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn,$query);

    if(mysqli_num_rows($result)>0)
    {
        $_SESSION['admin']=$username;
        header("Location: dashboard.php");
        exit();
    }
    else
    {
        $error="Invalid Username or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#0f172a;
height:100vh;
display:flex;
justify-content:center;
align-items:center;
font-family:Arial,Helvetica,sans-serif;
}

.login-box{
width:420px;
background:white;
padding:40px;
border-radius:15px;
box-shadow:0 10px 25px rgba(0,0,0,.3);
}

.logo{
font-size:50px;
text-align:center;
}

h2{
text-align:center;
margin-bottom:25px;
font-weight:bold;
}

.btn-login{
background:#2563eb;
color:white;
width:100%;
}

.btn-login:hover{
background:#1d4ed8;
color:white;
}

.footer{
text-align:center;
margin-top:20px;
font-size:13px;
color:gray;
}

</style>

</head>

<body>

<div class="login-box">

<div class="logo">
🎓
</div>

<h2>Admin Login</h2>

<?php
if(isset($error))
{
echo "<div class='alert alert-danger'>$error</div>";
}
?>

<form method="POST">

<div class="mb-3">

<label class="form-label">
Username
</label>

<input
type="text"
name="username"
class="form-control"
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
required>

</div>

<button
type="submit"
name="login"
class="btn btn-login">

Login

</button>

</form>

<div class="footer">

Forces Academy LMS<br>

Admin Panel

</div>

</div>

</body>

</html>