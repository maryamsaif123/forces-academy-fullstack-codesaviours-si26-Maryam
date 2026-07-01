<?php

session_start();


if(!isset($_SESSION['student']))
{
header("location:login.php");
}

?>


<h1>
Welcome Student
</h1>


<a href="logout.php">
Logout
</a>