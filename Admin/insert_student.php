<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: manage_students.php");
    exit();
}

/*=========================================
    GET FORM DATA
=========================================*/

$full_name  = trim($_POST['full_name']);
$roll_number = trim($_POST['roll_number']);
$email      = trim($_POST['email']);
$password   = $_POST['password'];
$gender     = trim($_POST['gender']);
$class      = trim($_POST['class']);

/*=========================================
    VALIDATION
=========================================*/

if (
    empty($full_name) ||
    empty($roll_number) ||
    empty($email) ||
    empty($password) ||
    empty($gender) ||
    empty($class)
) {
    $_SESSION['error'] = "Please fill all required fields.";
    header("Location: manage_students.php");
    exit();
}

/*=========================================
    DUPLICATE EMAIL / ROLL NUMBER CHECK
=========================================*/

$check = mysqli_prepare(
    $conn,
    "SELECT id FROM students WHERE email=? OR roll_number=?"
);

mysqli_stmt_bind_param($check, "ss", $email, $roll_number);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) > 0) {

    $_SESSION['error'] = "Email or Roll Number already exists.";

    mysqli_stmt_close($check);

    header("Location: manage_students.php");
    exit();
}

mysqli_stmt_close($check);

/*=========================================
    PASSWORD HASH
=========================================*/

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

/*=========================================
    IMAGE UPLOAD
=========================================*/

$photoName = "";

$uploadDir = "../uploads/students/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (
    isset($_FILES['photo']) &&
    $_FILES['photo']['error'] == 0
) {

    $allowed = ['jpg','jpeg','png','gif','webp'];

    $extension = strtolower(
        pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION)
    );

    if (in_array($extension, $allowed)) {

        $photoName = time() . "_" . uniqid() . "." . $extension;

        move_uploaded_file(
            $_FILES['photo']['tmp_name'],
            $uploadDir . $photoName
        );

    } else {

        $_SESSION['error'] = "Only JPG, PNG, GIF and WEBP images are allowed.";

        header("Location: manage_students.php");
        exit();
    }
}

/*=========================================
    INSERT STUDENT
=========================================*/

$query = mysqli_prepare(
    $conn,
    "INSERT INTO students
    (
        full_name,
        email,
        password,
        roll_number,
        gender,
        class,
        photo
    )
    VALUES
    (
        ?,?,?,?,?,?,?
    )"
);

mysqli_stmt_bind_param(
    $query,
    "sssssss",
    $full_name,
    $email,
    $hashedPassword,
    $roll_number,
    $gender,
    $class,
    $photoName
);

if (mysqli_stmt_execute($query)) {

    $_SESSION['success'] = "Student added successfully.";

} else {

    $_SESSION['error'] = "Database Error: " . mysqli_error($conn);

}

mysqli_stmt_close($query);
mysqli_close($conn);

header("Location: manage_students.php");
exit();
?>