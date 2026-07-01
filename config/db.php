<?php

$host = "localhost";
$user = "root";
$password = "";
$dbname = "forces_academy_lms";


$conn = mysqli_connect($host,$user,$password,$dbname);


if(!$conn)
{
    die("Database Connection Failed");
}

echo "Database Connected Successfully";

?>