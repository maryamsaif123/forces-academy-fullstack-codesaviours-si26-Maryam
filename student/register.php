<?php

include "../config/database.php";


if(isset($_POST['register']))
{

$name = $_POST['full_name'];
$email = $_POST['email'];
$password = $_POST['password'];
$roll = $_POST['roll_number'];
$class = $_POST['class'];


$query = "INSERT INTO students
(full_name,email,password,roll_number,class)

VALUES

('$name','$email','$password','$roll','$class')";


$result = mysqli_query($conn,$query);


if($result)
{
    echo "Registration Successful";
}
else
{
    echo "Registration Failed";
}

}

?>


<!DOCTYPE html>
<html>

<head>
<title>Student Registration</title>
</head>


<body>


<h2>Student Registration</h2>


<form method="POST">


Name:
<input type="text" name="full_name">

<br><br>


Email:
<input type="email" name="email">


<br><br>


Password:
<input type="password" name="password">


<br><br>


Roll Number:
<input type="text" name="roll_number">


<br><br>


Class:
<input type="text" name="class">


<br><br>


<button name="register">
Register
</button>


</form>


</body>

</html>