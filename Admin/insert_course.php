<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/*=========================================
    ONLY ALLOW POST
=========================================*/

if ($_SERVER['REQUEST_METHOD'] != "POST") {

    header("Location: manage_courses.php");
    exit();
}

/*=========================================
    GET FORM DATA
=========================================*/

$course_name = trim($_POST['course_name']);
$description = trim($_POST['description']);
$teacher_name = trim($_POST['teacher_name']);
$video_link = trim($_POST['video_link']);

$notes_pdf = "";

/*=========================================
    VALIDATION
=========================================*/

if (
    empty($course_name) ||
    empty($description) ||
    empty($teacher_name)
) {

    $_SESSION['error'] = "Please fill all required fields.";

    header("Location: manage_courses.php");
    exit();
}

/*=========================================
    DUPLICATE COURSE CHECK
=========================================*/

$check = mysqli_prepare(
    $conn,
    "SELECT id FROM courses WHERE course_name=? LIMIT 1"
);

mysqli_stmt_bind_param($check, "s", $course_name);

mysqli_stmt_execute($check);

mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) > 0) {

    $_SESSION['error'] = "Course already exists.";

    header("Location: manage_courses.php");
    exit();
}

/*=========================================
    PDF UPLOAD
=========================================*/

if (
    isset($_FILES['notes_pdf']) &&
    $_FILES['notes_pdf']['error'] == 0
) {

    $allowed = ['pdf'];

    $ext = strtolower(
        pathinfo(
            $_FILES['notes_pdf']['name'],
            PATHINFO_EXTENSION
        )
    );

    if (!in_array($ext, $allowed)) {

        $_SESSION['error'] = "Only PDF files are allowed.";

        header("Location: manage_courses.php");
        exit();
    }

    if (!is_dir("../uploads/notes")) {
        mkdir("../uploads/notes", 0777, true);
    }

    $notes_pdf =
        time() . "_" .
        preg_replace("/[^a-zA-Z0-9._-]/", "_", $_FILES['notes_pdf']['name']);

    move_uploaded_file(
        $_FILES['notes_pdf']['tmp_name'],
        "../uploads/notes/" . $notes_pdf
    );
}

/*=========================================
    INSERT COURSE
=========================================*/

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO courses
    (
        course_name,
        description,
        teacher_name,
        notes_pdf,
        video_link
    )
    VALUES
    (
        ?, ?, ?, ?, ?
    )"
);

mysqli_stmt_bind_param(
    $stmt,
    "sssss",
    $course_name,
    $description,
    $teacher_name,
    $notes_pdf,
    $video_link
);

if (mysqli_stmt_execute($stmt)) {

    $_SESSION['success'] = "Course added successfully.";

} else {

    $_SESSION['error'] = "Database Error: " . mysqli_error($conn);
}

header("Location: manage_courses.php");
exit();
?>
