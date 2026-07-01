<?php

session_start();

include "../config/database.php";


if(isset($_POST['login']))
{

$email=$_POST['email'];
$password=$_POST['password'];


$query="SELECT * FROM students

WHERE email='$email'

AND password='$password'";


$result=mysqli_query($conn,$query);


if(mysqli_num_rows($result)>0)
{

$_SESSION['student']=$email;

header("location:dashboard.php");

}

else
{

echo "Invalid Login";

}

}

?>


<h2>Student Login</h2>


<form method="POST">


Email:

<input type="email" name="email">


<br><br>


Password:

<input type="password" name="password">


<br><br>


<button name="login">
Login
</button>


</form>