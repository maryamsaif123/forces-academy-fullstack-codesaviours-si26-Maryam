<?php

session_start();

if(!isset($_SESSION['admin']))
{
    header("Location: login.php");
    exit();
}

include("../config/database.php");

if(isset($_GET['id']))
{
    $id = $_GET['id'];

    mysqli_query($conn,"DELETE FROM courses WHERE id='$id'");
}

header("Location: manage_courses.php");
exit();

?>