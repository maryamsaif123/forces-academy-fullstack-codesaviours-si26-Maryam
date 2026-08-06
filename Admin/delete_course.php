<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/*=========================================
    VALIDATE ASSIGNMENT ID
=========================================*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid Assignment ID.";

    header("Location: manage_assignments.php");
    exit();
}

$id = (int)$_GET['id'];

/*=========================================
    CHECK ASSIGNMENT EXISTS
=========================================*/

$check = mysqli_prepare(
    $conn,
    "SELECT id,title
     FROM assignments
     WHERE id=?
     LIMIT 1"
);

mysqli_stmt_bind_param($check, "i", $id);

mysqli_stmt_execute($check);

$result = mysqli_stmt_get_result($check);

if (mysqli_num_rows($result) == 0) {

    $_SESSION['error'] = "Assignment not found.";

    header("Location: manage_assignments.php");
    exit();
}

/*=========================================
    CHECK SUBMISSIONS
=========================================*/

$submissionCheck = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total
     FROM submissions
     WHERE assignment_id=?"
);

mysqli_stmt_bind_param(
    $submissionCheck,
    "i",
    $id
);

mysqli_stmt_execute($submissionCheck);

$submissionResult = mysqli_stmt_get_result($submissionCheck);

$submissionData = mysqli_fetch_assoc($submissionResult);

if ($submissionData['total'] > 0) {

    $_SESSION['error'] =
    "This assignment cannot be deleted because students have already submitted their work.";

    header("Location: manage_assignments.php");
    exit();
}

/*=========================================
    DELETE ASSIGNMENT
=========================================*/

$delete = mysqli_prepare(
    $conn,
    "DELETE FROM assignments
     WHERE id=?"
);

mysqli_stmt_bind_param(
    $delete,
    "i",
    $id
);

if (mysqli_stmt_execute($delete)) {

    $_SESSION['success'] =
    "Assignment deleted successfully.";

} else {

    $_SESSION['error'] =
    "Unable to delete assignment.";

}

header("Location: manage_assignments.php");
exit();

?>