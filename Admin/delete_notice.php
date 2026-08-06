<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/*=========================================
    VALIDATE NOTICE ID
=========================================*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid Notice ID.";

    header("Location: manage_notices.php");
    exit();
}

$id = (int) $_GET['id'];

/*=========================================
    CHECK NOTICE EXISTS
=========================================*/

$check = mysqli_prepare(
    $conn,
    "SELECT id FROM notices WHERE id=? LIMIT 1"
);

mysqli_stmt_bind_param($check, "i", $id);

mysqli_stmt_execute($check);

$result = mysqli_stmt_get_result($check);

if (mysqli_num_rows($result) == 0) {

    $_SESSION['error'] = "Notice not found.";

    header("Location: manage_notices.php");
    exit();
}

/*=========================================
    DELETE NOTICE
=========================================*/

$delete = mysqli_prepare(
    $conn,
    "DELETE FROM notices WHERE id=?"
);

mysqli_stmt_bind_param($delete, "i", $id);

if (mysqli_stmt_execute($delete)) {

    $_SESSION['success'] = "Notice deleted successfully.";

} else {

    $_SESSION['error'] = "Unable to delete notice.";

}

header("Location: manage_notices.php");
exit();

?>