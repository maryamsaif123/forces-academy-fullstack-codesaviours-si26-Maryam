<?php

session_start();

if(!isset($_SESSION['admin']))
{
    header("location:login.php");
}

?>


<h1>
Welcome Admin
</h1>


<a href="logout.php">
Logout
</a>