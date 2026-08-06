<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/*=========================================
    VALIDATE RESULT ID
=========================================*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid Result ID.";

    header("Location: manage_results.php");
    exit();
}

$id = (int)$_GET['id'];

/*=========================================
    CHECK RESULT EXISTS
=========================================*/

$check = mysqli_prepare(

$conn,

"SELECT id
FROM results
WHERE id=?
LIMIT 1"

);

mysqli_stmt_bind_param($check,"i",$id);

mysqli_stmt_execute($check);

$result = mysqli_stmt_get_result($check);

if(mysqli_num_rows($result)==0){

    $_SESSION['error']="Result not found.";

    header("Location: manage_results.php");
    exit();

}

/*=========================================
    DELETE RESULT
=========================================*/

$delete = mysqli_prepare(

$conn,

"DELETE FROM results
WHERE id=?"

);

mysqli_stmt_bind_param(

$delete,

"i",

$id

);

if(mysqli_stmt_execute($delete)){

    $_SESSION['success']="Result deleted successfully.";

}else{

    $_SESSION['error']="Unable to delete result.";

}

header("Location: manage_results.php");
exit();

?>