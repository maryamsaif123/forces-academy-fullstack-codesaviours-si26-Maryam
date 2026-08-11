<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$admin_id = (int) $_SESSION['admin_id'];

$success = "";
$error = "";

/* ==================================================
   FETCH ADMIN
================================================== */

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, username, email, password, photo, role, created_at
     FROM admins
     WHERE id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param($stmt, "i", $admin_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$admin) {
    session_destroy();
    header("Location: login.php");
    exit();
}


/* ==================================================
   ADMIN INFORMATION
================================================== */

$admin_username = $admin['username'] ?? "Administrator";
$admin_email    = $admin['email'] ?? "";
$admin_role     = $admin['role'] ?? "Admin";
$admin_photo    = $admin['photo'] ?? "";


/* ==================================================
   UPDATE PROFILE
================================================== */

if (isset($_POST['update_profile'])) {

    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);

    if (empty($username) || empty($email)) {

        $error = "Username and email are required.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } else {

        /* Check if email already belongs to another admin */

        $check = mysqli_prepare(
            $conn,
            "SELECT id
             FROM admins
             WHERE email = ?
             AND id != ?
             LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $check,
            "si",
            $email,
            $admin_id
        );

        mysqli_stmt_execute($check);

        $checkResult = mysqli_stmt_get_result($check);

        if (mysqli_num_rows($checkResult) > 0) {

            $error = "This email is already being used by another admin.";

        } else {

            $update = mysqli_prepare(
                $conn,
                "UPDATE admins
                 SET username = ?, email = ?
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $update,
                "ssi",
                $username,
                $email,
                $admin_id
            );

            if (mysqli_stmt_execute($update)) {

                $success = "Profile updated successfully.";

                $admin_username = $username;
                $admin_email = $email;

                /* Update session name if used elsewhere */

                $_SESSION['admin_name'] = $username;

            } else {

                $error = "Unable to update profile.";
            }

            mysqli_stmt_close($update);
        }

        mysqli_stmt_close($check);
    }
}


/* ==================================================
   CHANGE PASSWORD
================================================== */

if (isset($_POST['change_password'])) {

    $current_password = $_POST['current_password'] ?? "";
    $new_password     = $_POST['new_password'] ?? "";
    $confirm_password = $_POST['confirm_password'] ?? "";

    if (
        empty($current_password) ||
        empty($new_password) ||
        empty($confirm_password)
    ) {

        $error = "Please fill all password fields.";

    } elseif ($new_password !== $confirm_password) {

        $error = "New password and confirm password do not match.";

    } elseif (strlen($new_password) < 6) {

        $error = "New password must be at least 6 characters.";

    } else {

        /*
         * Verify current password.
         * Supports both hashed passwords and old plain-text
         * passwords, then saves the new password securely.
         */

        $password_correct = password_verify(
            $current_password,
            $admin['password']
        );

        /* Support old plain-text password if your old system used it */

        if (!$password_correct && $current_password === $admin['password']) {
            $password_correct = true;
        }

        if (!$password_correct) {

            $error = "Current password is incorrect.";

        } else {

            $hashed_password = password_hash(
                $new_password,
                PASSWORD_DEFAULT
            );

            $updatePassword = mysqli_prepare(
                $conn,
                "UPDATE admins
                 SET password = ?
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $updatePassword,
                "si",
                $hashed_password,
                $admin_id
            );

            if (mysqli_stmt_execute($updatePassword)) {

                $success = "Password changed successfully.";

                /* Update local password value */
                $admin['password'] = $hashed_password;

            } else {

                $error = "Unable to change password.";
            }

            mysqli_stmt_close($updatePassword);
        }
    }
}


/* ==================================================
   PROFILE PHOTO
================================================== */

if (!empty($admin_photo)) {

    $photo_path = "assets/images/" . $admin_photo;

} else {

    $photo_path = "assets/images/avatar.png";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>
My Profile | Forces Academy LMS
</title>


<!-- Bootstrap -->

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">


<!-- Font Awesome -->

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
rel="stylesheet">


<!-- Google Font -->

<link
href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet">


<style>

* {
    box-sizing: border-box;
}

body {

    margin: 0;

    font-family: 'Poppins', sans-serif;

    background: #f4f7fb;

    color: #1f2937;
}


/* ==========================================
   MAIN CONTENT
========================================== */

.main-content {

    padding: 30px;

    max-width: 1250px;

    margin: auto;
}


/* ==========================================
   PAGE HEADER
========================================== */

.page-header {

    background: #ffffff;

    padding: 18px 22px;

    border-radius: 12px;

    margin-bottom: 14px;

    box-shadow: 0 4px 18px rgba(0,0,0,.06);

    display: flex;

    justify-content: space-between;

    align-items: center;
}

.page-header h3 {

    font-size: 18px;

    font-weight: 700;

    margin: 0;
}

.page-header p {

    font-size: 9px;

    color: #8b96a7;

    margin: 3px 0 0;
}


/* ==========================================
   PROFILE CARD
========================================== */

.profile-card {

    background: white;

    border-radius: 12px;

    overflow: hidden;

    box-shadow: 0 4px 18px rgba(0,0,0,.07);

    height: 100%;
}


/* ==========================================
   PROFILE LEFT
========================================== */

.profile-left {

    background: linear-gradient(
        135deg,
        #1468ed,
        #244fa4
    );

    color: white;

    min-height: 365px;

    padding: 20px 12px;

    text-align: center;
}


.profile-avatar {

    width: 75px;

    height: 75px;

    object-fit: cover;

    border-radius: 50%;

    border: 3px solid white;

    margin-bottom: 8px;

    background: white;
}


.profile-left h4 {

    font-size: 15px;

    font-weight: 700;

    margin-bottom: 3px;
}


.profile-left .role {

    font-size: 9px;

    opacity: .9;

    margin-bottom: 15px;
}


/* ==========================================
   INFORMATION BOX
========================================== */

.info-box {

    background: rgba(255,255,255,.95);

    color: #1f2937;

    border-radius: 7px;

    padding: 7px 10px;

    margin-bottom: 6px;

    text-align: left;

    font-size: 8px;
}

.info-box i {

    width: 18px;

    color: #1769e8;
}

.info-title {

    font-weight: 700;

    font-size: 8px;

    display: block;
}

.info-value {

    color: #667085;

    font-size: 8px;
}


/* ==========================================
   RIGHT SIDE
========================================== */

.profile-right {

    padding: 15px;
}


.section-card {

    background: white;

    border-radius: 10px;

    box-shadow: 0 4px 15px rgba(0,0,0,.06);

    margin-bottom: 12px;

    overflow: hidden;
}


.section-header {

    padding: 10px 13px;

    border-bottom: 1px solid #edf0f5;

    font-size: 11px;

    font-weight: 700;
}


.section-header i {

    margin-right: 7px;
}


.section-body {

    padding: 13px;
}


/* ==========================================
   FORM
========================================== */

.form-label {

    font-size: 8px;

    font-weight: 600;

    margin-bottom: 4px;
}

.form-control {

    height: 29px;

    border-radius: 6px;

    font-size: 8px;
}

.form-control:focus {

    box-shadow: none;

    border-color: #1769e8;
}


/* ==========================================
   BUTTON
========================================== */

.btn {

    font-size: 8px;

    font-weight: 600;

    border-radius: 6px;

    padding: 7px 12px;
}


/* ==========================================
   ALERT
========================================== */

.alert {

    font-size: 9px;

    padding: 8px 12px;

    border-radius: 7px;

}


/* ==========================================
   FOOTER
========================================== */

.footer {

    text-align: center;

    margin-top: 25px;

    font-size: 8px;

    color: #8490a0;
}


/* ==========================================
   RESPONSIVE
========================================== */

@media(max-width: 768px) {

    .main-content {

        padding: 15px;
    }

    .page-header {

        flex-direction: column;

        align-items: flex-start;

        gap: 10px;
    }

    .profile-left {

        min-height: auto;
    }

}

</style>

</head>


<body>


<div class="main-content">


<!-- ==========================================
     PAGE HEADER
========================================== -->

<div class="page-header">

    <div>

        <h3>

            <i class="fas fa-user-circle text-primary me-1"></i>

            My Profile

        </h3>

        <p>
            View and manage your administrator account information.
        </p>

    </div>


    <a href="dashboard.php"
       class="btn btn-secondary">

        <i class="fas fa-arrow-left me-1"></i>

        Dashboard

    </a>

</div>



<!-- ==========================================
     ALERTS
========================================== -->

<?php if ($success != "") { ?>

<div class="alert alert-success">

    <i class="fas fa-check-circle me-2"></i>

    <?php echo htmlspecialchars($success); ?>

</div>

<?php } ?>


<?php if ($error != "") { ?>

<div class="alert alert-danger">

    <i class="fas fa-exclamation-circle me-2"></i>

    <?php echo htmlspecialchars($error); ?>

</div>

<?php } ?>



<!-- ==========================================
     PROFILE AREA
========================================== -->

<div class="row g-3">


<!-- ==========================================
     LEFT PROFILE CARD
========================================== -->

<div class="col-lg-4">

<div class="profile-card">

<div class="profile-left">


<img
src="<?php echo htmlspecialchars($photo_path); ?>"
class="profile-avatar"
alt="Admin Avatar"
onerror="this.src='assets/images/avatar.png';">


<h4>

<?php echo htmlspecialchars($admin_username); ?>

</h4>


<div class="role">

<i class="fas fa-shield-alt me-1"></i>

<?php echo htmlspecialchars($admin_role); ?>

</div>



<!-- Username -->

<div class="info-box">

    <i class="fas fa-user"></i>

    <span class="info-title">
        Username
    </span>

    <span class="info-value">
        <?php echo htmlspecialchars($admin_username); ?>
    </span>

</div>



<!-- Email -->

<div class="info-box">

    <i class="fas fa-envelope"></i>

    <span class="info-title">
        Email
    </span>

    <span class="info-value">
        <?php echo htmlspecialchars($admin_email); ?>
    </span>

</div>



<!-- Role -->

<div class="info-box">

    <i class="fas fa-user-shield"></i>

    <span class="info-title">
        Role
    </span>

    <span class="info-value">
        <?php echo htmlspecialchars($admin_role); ?>
    </span>

</div>



<!-- Admin ID -->

<div class="info-box">

    <i class="fas fa-id-card"></i>

    <span class="info-title">
        Admin ID
    </span>

    <span class="info-value">
        #<?php echo $admin_id; ?>
    </span>

</div>



</div>

</div>

</div>



<!-- ==========================================
     RIGHT SIDE
========================================== -->

<div class="col-lg-8">

<div class="profile-right">



<!-- ==========================================
     EDIT PROFILE
========================================== -->

<div class="section-card">

<div class="section-header">

    <span class="text-primary">

        <i class="fas fa-user-edit"></i>

        Edit Profile

    </span>

</div>


<div class="section-body">

<form method="POST">


<div class="row">


<!-- Username -->

<div class="col-md-6 mb-3">

<label class="form-label">

Username

</label>

<input
type="text"
name="username"
class="form-control"
value="<?php echo htmlspecialchars($admin_username); ?>"
required>

</div>



<!-- Email -->

<div class="col-md-6 mb-3">

<label class="form-label">

Email Address

</label>

<input
type="email"
name="email"
class="form-control"
value="<?php echo htmlspecialchars($admin_email); ?>"
required>

</div>


</div>


<button
type="submit"
name="update_profile"
class="btn btn-primary">

<i class="fas fa-save me-1"></i>

Save Changes

</button>


</form>

</div>

</div>



<!-- ==========================================
     CHANGE PASSWORD
========================================== -->

<div class="section-card">

<div class="section-header">

    <span class="text-danger">

        <i class="fas fa-lock"></i>

        Change Password

    </span>

</div>


<div class="section-body">

<form method="POST">


<!-- Current Password -->

<div class="mb-3">

<label class="form-label">

Current Password

</label>

<input
type="password"
name="current_password"
class="form-control"
placeholder="Enter current password"
required>

</div>



<!-- New Password -->

<div class="mb-3">

<label class="form-label">

New Password

</label>

<input
type="password"
name="new_password"
class="form-control"
placeholder="Enter new password"
required>

</div>



<!-- Confirm Password -->

<div class="mb-3">

<label class="form-label">

Confirm New Password

</label>

<input
type="password"
name="confirm_password"
class="form-control"
placeholder="Confirm new password"
required>

</div>



<button
type="submit"
name="change_password"
class="btn btn-danger">

<i class="fas fa-key me-1"></i>

Change Password

</button>


</form>

</div>

</div>


</div>

</div>

</div>



<!-- ==========================================
     FOOTER
========================================== -->

<div class="footer">

© <?php echo date("Y"); ?>

Forces Academy LMS |

Admin Profile

</div>


</div>


</body>

</html>