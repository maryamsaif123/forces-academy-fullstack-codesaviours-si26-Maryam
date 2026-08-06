<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/*=====================================
    CHECK ID
======================================*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid Teacher ID.";

    header("Location: manage_teachers.php");
    exit();
}

$id = (int)$_GET['id'];

/*=====================================
    FETCH TEACHER
======================================*/

$stmt = mysqli_prepare(
    $conn,
    "SELECT photo FROM teachers WHERE id=? LIMIT 1"
);

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {

    $_SESSION['error'] = "Teacher not found.";

    header("Location: manage_teachers.php");
    exit();
}

$teacher = mysqli_fetch_assoc($result);

/*=====================================
    DELETE PHOTO
======================================*/

if (
    !empty($teacher['photo']) &&
    file_exists("../uploads/teachers/" . $teacher['photo'])
) {
    unlink("../uploads/teachers/" . $teacher['photo']);
}

/*=====================================
    DELETE RECORD
======================================*/

$delete = mysqli_prepare(
    $conn,
    "DELETE FROM teachers WHERE id=?"
);

mysqli_stmt_bind_param($delete, "i", $id);

if (mysqli_stmt_execute($delete)) {

    $_SESSION['success'] = "Teacher deleted successfully.";

} else {

    $_SESSION['error'] = "Failed to delete teacher.";

}

header("Location: manage_teachers.php");
exit();
?>