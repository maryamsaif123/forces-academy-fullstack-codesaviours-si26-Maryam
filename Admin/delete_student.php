<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/*=========================================
    CHECK STUDENT ID
=========================================*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    $_SESSION['error'] = "Invalid student ID.";

    header("Location: manage_students.php");
    exit();
}

$id = (int)$_GET['id'];

/*=========================================
    GET STUDENT
=========================================*/

$query = mysqli_prepare(
    $conn,
    "SELECT * FROM students WHERE id=? LIMIT 1"
);

mysqli_stmt_bind_param($query, "i", $id);
mysqli_stmt_execute($query);

$result = mysqli_stmt_get_result($query);

if (mysqli_num_rows($result) == 0) {

    $_SESSION['error'] = "Student not found.";

    header("Location: manage_students.php");
    exit();
}

$student = mysqli_fetch_assoc($result);

/*=========================================
    DELETE PHOTO 
=========================================*/

if (
    isset($student['photo']) &&
    !empty($student['photo'])
) {

    $photoPath = "../uploads/students/" . $student['photo'];

    if (file_exists($photoPath)) {
        unlink($photoPath);
    }
}

/*=========================================
    DELETE STUDENT
=========================================*/

$delete = mysqli_prepare(
    $conn,
    "DELETE FROM students WHERE id=?"
);

mysqli_stmt_bind_param($delete, "i", $id);

if (mysqli_stmt_execute($delete)) {
    
onclick="return confirm('Are you sure you want to permanently delete this student? This action cannot be undone.')"

    $_SESSION['success'] = "Student deleted successfully.";

} else {

    $_SESSION['error'] = "Failed to delete student.";

}

mysqli_stmt_close($delete);
mysqli_close($conn);

header("Location: manage_students.php");
exit();
?>
