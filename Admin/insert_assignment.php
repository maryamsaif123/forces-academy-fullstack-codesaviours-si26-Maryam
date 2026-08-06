<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/*=========================================
    ALLOW ONLY POST REQUEST
=========================================*/

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    header("Location: manage_assignments.php");
    exit();
}

/*=========================================
    GET FORM DATA
=========================================*/

$course_id   = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
$title       = trim($_POST['title']);
$description = trim($_POST['description']);
$deadline    = trim($_POST['deadline']);

/*=========================================
    VALIDATION
=========================================*/

if (
    $course_id <= 0 ||
    empty($title) ||
    empty($description) ||
    empty($deadline)
) {

    $_SESSION['error'] = "Please fill all required fields.";

    header("Location: manage_assignments.php");
    exit();
}

/*=========================================
    VALID DEADLINE
=========================================*/

$today = date("Y-m-d");

if ($deadline < $today) {

    $_SESSION['error'] = "Deadline cannot be earlier than today.";

    header("Location: manage_assignments.php");
    exit();
}

/*=========================================
    COURSE EXISTS?
=========================================*/

$courseCheck = mysqli_prepare(
    $conn,
    "SELECT id FROM courses WHERE id=? LIMIT 1"
);

mysqli_stmt_bind_param($courseCheck, "i", $course_id);

mysqli_stmt_execute($courseCheck);

mysqli_stmt_store_result($courseCheck);

if (mysqli_stmt_num_rows($courseCheck) == 0) {

    $_SESSION['error'] = "Selected course does not exist.";

    header("Location: manage_assignments.php");
    exit();
}

/*=========================================
    DUPLICATE CHECK
=========================================*/

$duplicate = mysqli_prepare(
    $conn,
    "SELECT id
     FROM assignments
     WHERE title=? AND course_id=?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $duplicate,
    "si",
    $title,
    $course_id
);

mysqli_stmt_execute($duplicate);

mysqli_stmt_store_result($duplicate);

if (mysqli_stmt_num_rows($duplicate) > 0) {

    $_SESSION['error'] = "This assignment already exists for the selected course.";

    header("Location: manage_assignments.php");
    exit();
}

/*=========================================
    INSERT ASSIGNMENT
=========================================*/

$insert = mysqli_prepare(
    $conn,
    "INSERT INTO assignments
    (
        course_id,
        title,
        description,
        deadline
    )
    VALUES
    (
        ?, ?, ?, ?
    )"
);

mysqli_stmt_bind_param(
    $insert,
    "isss",
    $course_id,
    $title,
    $description,
    $deadline
);

if (mysqli_stmt_execute($insert)) {

    $_SESSION['success'] = "Assignment created successfully.";

} else {

    $_SESSION['error'] = "Database Error: " . mysqli_error($conn);

}

header("Location: manage_assignments.php");
exit();

?>