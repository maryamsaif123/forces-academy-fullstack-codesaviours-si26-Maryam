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

if ($_SERVER['REQUEST_METHOD'] != "POST") {
    header("Location: manage_notices.php");
    exit();
}

/*=========================================
    GET FORM DATA
=========================================*/

$title = trim($_POST['title']);
$content = trim($_POST['content']);
$posted_by = trim($_POST['posted_by']);

/*=========================================
    VALIDATION
=========================================*/

if (empty($title) || empty($content) || empty($posted_by)) {

    $_SESSION['error'] = "Please fill all required fields.";

    header("Location: manage_notices.php");
    exit();
}

/*=========================================
    DUPLICATE NOTICE CHECK
=========================================*/

$check = mysqli_prepare(
    $conn,
    "SELECT id FROM notices WHERE title=? LIMIT 1"
);

mysqli_stmt_bind_param($check, "s", $title);

mysqli_stmt_execute($check);

mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) > 0) {

    $_SESSION['error'] = "A notice with this title already exists.";

    header("Location: manage_notices.php");
    exit();
}

/*=========================================
    INSERT NOTICE
=========================================*/

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO notices
    (
        title,
        content,
        posted_by
    )
    VALUES
    (
        ?, ?, ?
    )"
);

mysqli_stmt_bind_param(
    $stmt,
    "sss",
    $title,
    $content,
    $posted_by
);

if (mysqli_stmt_execute($stmt)) {

    $_SESSION['success'] = "Notice published successfully.";

} else {

    $_SESSION['error'] = "Database Error: " . mysqli_error($conn);

}

header("Location: manage_notices.php");
exit();
?>