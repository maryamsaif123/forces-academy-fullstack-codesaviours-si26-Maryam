<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    mysqli_query($conn, "DELETE FROM students WHERE id='$id'");
}

header("Location: manage_students.php");
exit();
?>