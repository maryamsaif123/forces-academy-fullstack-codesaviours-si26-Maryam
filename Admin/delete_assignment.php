<?php
session_start();

include("../config/database.php");

if(!isset($_GET['id'])){

header("Location: manage_assignments.php");

exit();

}

$id=(int)$_GET['id'];

/* Check Assignment */

$check=mysqli_query($conn,"
SELECT *
FROM assignments
WHERE id='$id'
");

if(mysqli_num_rows($check)==0){

header("Location: manage_assignments.php");

exit();

}

/* Delete Related Submissions First */

mysqli_query($conn,"
DELETE FROM submissions
WHERE assignment_id='$id'
");

/* Delete Assignment */

mysqli_query($conn,"
DELETE FROM assignments
WHERE id='$id'
");

header("Location: manage_assignments.php?deleted=1");

exit();

?>