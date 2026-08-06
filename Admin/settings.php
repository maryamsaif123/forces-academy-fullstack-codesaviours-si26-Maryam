<?php
session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$success="";
$error="";

/*=========================================
    LOAD SETTINGS
=========================================*/

$settings=mysqli_query($conn,"
SELECT *
FROM settings
LIMIT 1
");

$setting=mysqli_fetch_assoc($settings);

/*=========================================
    SAVE SETTINGS
=========================================*/

if(isset($_POST['save'])){

    $academy_name=trim($_POST['academy_name']);
    $academy_email=trim($_POST['academy_email']);
    $academy_phone=trim($_POST['academy_phone']);
    $academy_address=trim($_POST['academy_address']);
    $website=trim($_POST['website']);
    $session_name=trim($_POST['session_name']);
    $passing_marks=(int)$_POST['passing_marks'];

    $update=mysqli_prepare(

        $conn,

        "UPDATE settings
        SET
        academy_name=?,
        academy_email=?,
        academy_phone=?,
        academy_address=?,
        website=?,
        session_name=?,
        passing_marks=?"

    );

    mysqli_stmt_bind_param(

        $update,

        "ssssssi",

        $academy_name,
        $academy_email,
        $academy_phone,
        $academy_address,
        $website,
        $session_name,
        $passing_marks

    );

    if(mysqli_stmt_execute($update)){

        $success="Settings updated successfully.";

        header("Refresh:1");

    }else{

        $error="Unable to save settings.";

    }

}
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>System Settings</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<style>

body{
    background:#eef3f9;
    font-family:Poppins,sans-serif;
}

.setting-card{
    border:none;
    border-radius:18px;
}

</style>

</head>

<body>

<div class="container-fluid py-4">

<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h2 class="fw-bold">

<i class="fas fa-cogs text-primary me-2"></i>

System Settings

</h2>

<p class="text-muted">

Manage Forces Academy LMS Settings

</p>

</div>

<a href="dashboard.php" class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Dashboard

</a>

</div>

<?php if($success!=""){ ?>

<div class="alert alert-success">

<?php echo $success; ?>

</div>

<?php } ?>

<?php if($error!=""){ ?>

<div class="alert alert-danger">

<?php echo $error; ?>

</div>

<?php } ?>

<form method="POST">

<div class="row">

<div class="col-lg-8">

<div class="card setting-card shadow mb-4">

<div class="card-header bg-primary text-white">

<h5>

<i class="fas fa-school me-2"></i>

General Settings

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6 mb-3">

<label>Academy Name</label>

<input
type="text"
name="academy_name"
class="form-control"
value="<?php echo $setting['academy_name']; ?>">

</div>

<div class="col-md-6 mb-3">

<label>Email</label>

<input
type="email"
name="academy_email"
class="form-control"
value="<?php echo $setting['academy_email']; ?>">

</div>

<div class="col-md-6 mb-3">

<label>Phone</label>

<input
type="text"
name="academy_phone"
class="form-control"
value="<?php echo $setting['academy_phone']; ?>">

</div>

<div class="col-md-6 mb-3">

<label>Website</label>

<input
type="text"
name="website"
class="form-control"
value="<?php echo $setting['website']; ?>">

</div>

<div class="col-md-12 mb-3">

<label>Address</label>

<textarea
name="academy_address"
class="form-control"
rows="3"><?php echo $setting['academy_address']; ?></textarea>

</div>

</div>

</div>

</div>

<div class="card setting-card shadow">

<div class="card-header bg-success text-white">

<h5>

<i class="fas fa-calendar me-2"></i>

Academic Settings

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6 mb-3">

<label>Current Session</label>

<input
type="text"
name="session_name"
class="form-control"
value="<?php echo $setting['session_name']; ?>">

</div>

<div class="col-md-6 mb-3">

<label>Passing Marks (%)</label>

<input
type="number"
name="passing_marks"
class="form-control"
value="<?php echo $setting['passing_marks']; ?>">

</div>

</div>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card setting-card shadow">

<div class="card-header bg-dark text-white">

<h5>

<i class="fas fa-user-shield me-2"></i>

Administrator

</h5>

</div>

<div class="card-body text-center">

<img
src="assets/images/admin.png"
width="120"
class="rounded-circle shadow mb-3">

<h4>

<?php echo $_SESSION['admin_username']; ?>

</h4>

<p class="text-muted">

System Administrator

</p>

<hr>

<p>

Manage all academy configurations from one place.

</p>

<button
type="submit"
name="save"
class="btn btn-primary w-100">

<i class="fas fa-save me-2"></i>

Save Settings

</button>

</div>

</div>

</div>

</div>
<!-- =========================================
        THEME SETTINGS
========================================== -->

<div class="row mt-4">

<div class="col-lg-6">

<div class="card setting-card shadow mb-4">

<div class="card-header bg-info text-white">

<h5>

<i class="fas fa-palette me-2"></i>

Theme Settings

</h5>

</div>

<div class="card-body">

<div class="mb-3">

<label class="form-label">System Theme</label>

<select name="theme" class="form-select">

<option value="Light">Light</option>

<option value="Dark">Dark</option>

<option value="Blue">Blue</option>

<option value="Green">Green</option>

</select>

</div>

<div class="form-check form-switch mb-3">

<input
class="form-check-input"
type="checkbox"
name="sidebar_fixed"
checked>

<label class="form-check-label">

Fixed Sidebar

</label>

</div>

<div class="form-check form-switch">

<input
class="form-check-input"
type="checkbox"
name="animations"
checked>

<label class="form-check-label">

Enable Dashboard Animations

</label>

</div>

</div>

</div>

</div>

<!-- =========================================
        NOTIFICATIONS
========================================== -->

<div class="col-lg-6">

<div class="card setting-card shadow mb-4">

<div class="card-header bg-warning">

<h5>

<i class="fas fa-bell me-2"></i>

Notification Settings

</h5>

</div>

<div class="card-body">

<div class="form-check form-switch mb-3">

<input
class="form-check-input"
type="checkbox"
checked>

<label class="form-check-label">

Student Registration Notification

</label>

</div>

<div class="form-check form-switch mb-3">

<input
class="form-check-input"
type="checkbox"
checked>

<label class="form-check-label">

Assignment Notification

</label>

</div>

<div class="form-check form-switch mb-3">

<input
class="form-check-input"
type="checkbox"
checked>

<label class="form-check-label">

Result Notification

</label>

</div>

<div class="form-check form-switch">

<input
class="form-check-input"
type="checkbox">

<label class="form-check-label">

Email Alerts

</label>

</div>

</div>

</div>

</div>

</div>

<!-- =========================================
        LOGO UPLOAD
========================================== -->

<div class="card setting-card shadow mb-4">

<div class="card-header bg-primary text-white">

<h5>

<i class="fas fa-image me-2"></i>

Academy Logo

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-3 text-center">

<img

src="assets/images/logo.png"

class="img-fluid rounded shadow"

style="max-height:130px;">

</div>

<div class="col-md-9">

<label class="form-label">

Upload New Logo

</label>

<input

type="file"

name="logo"

class="form-control">

<small class="text-muted">

PNG, JPG or WEBP

</small>

</div>

</div>

</div>

</div>

<!-- =========================================
        SECURITY
========================================== -->

<div class="card setting-card shadow mb-4">

<div class="card-header bg-danger text-white">

<h5>

<i class="fas fa-shield-alt me-2"></i>

Security Settings

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6 mb-3">

<label>

Session Timeout (Minutes)

</label>

<input

type="number"

class="form-control"

value="30">

</div>

<div class="col-md-6 mb-3">

<label>

Minimum Password Length

</label>

<input

type="number"

class="form-control"

value="8">

</div>

</div>

<div class="form-check form-switch mb-3">

<input

class="form-check-input"

type="checkbox"

checked>

<label class="form-check-label">

Enable Strong Password Policy

</label>

</div>

<div class="form-check form-switch">

<input

class="form-check-input"

type="checkbox">

<label class="form-check-label">

Enable Two-Factor Authentication

</label>

</div>

</div>

</div>

<!-- =========================================
        EMAIL SETTINGS
========================================== -->

<div class="card setting-card shadow mb-4">

<div class="card-header bg-success text-white">

<h5>

<i class="fas fa-envelope me-2"></i>

SMTP Email Settings

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6 mb-3">

<label>SMTP Host</label>

<input

type="text"

class="form-control"

placeholder="smtp.gmail.com">

</div>

<div class="col-md-6 mb-3">

<label>SMTP Port</label>

<input

type="number"

class="form-control"

placeholder="587">

</div>

<div class="col-md-6 mb-3">

<label>Email</label>

<input

type="email"

class="form-control">

</div>

<div class="col-md-6 mb-3">

<label>Password</label>

<input

type="password"

class="form-control">

</div>

</div>

</div>

</div>
<!-- =========================================
        DATABASE BACKUP
========================================== -->

<div class="card setting-card shadow mb-4">

<div class="card-header bg-dark text-white">

<h5>

<i class="fas fa-database me-2"></i>

Database Backup & Restore

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-4 d-grid mb-3">

<a href="backup_database.php" class="btn btn-success">

<i class="fas fa-download me-2"></i>

Backup Database

</a>

</div>

<div class="col-md-4 d-grid mb-3">

<a href="restore_database.php" class="btn btn-warning">

<i class="fas fa-upload me-2"></i>

Restore Database

</a>

</div>

<div class="col-md-4 d-grid mb-3">

<a href="export_sql.php" class="btn btn-primary">

<i class="fas fa-file-export me-2"></i>

Export SQL

</a>

</div>

</div>

</div>

</div>

<!-- =========================================
        SYSTEM INFORMATION
========================================== -->

<div class="card setting-card shadow mb-4">

<div class="card-header bg-secondary text-white">

<h5>

<i class="fas fa-server me-2"></i>

System Information

</h5>

</div>

<div class="card-body">

<div class="row text-center">

<div class="col-md-3">

<i class="fab fa-php fa-3x text-primary mb-2"></i>

<h6>PHP Version</h6>

<p>

<?php echo phpversion(); ?>

</p>

</div>

<div class="col-md-3">

<i class="fas fa-database fa-3x text-success mb-2"></i>

<h6>MySQL Version</h6>

<p>

<?php echo mysqli_get_server_info($conn); ?>

</p>

</div>

<div class="col-md-3">

<i class="fas fa-clock fa-3x text-warning mb-2"></i>

<h6>Server Time</h6>

<p>

<?php echo date("d M Y h:i A"); ?>

</p>

</div>

<div class="col-md-3">

<i class="fas fa-circle-check fa-3x text-success mb-2"></i>

<h6>Status</h6>

<span class="badge bg-success">

Online

</span>

</div>

</div>

</div>

</div>

<!-- =========================================
        STORAGE INFORMATION
========================================== -->

<div class="card setting-card shadow mb-4">

<div class="card-header bg-info text-white">

<h5>

<i class="fas fa-hard-drive me-2"></i>

Storage Information

</h5>

</div>

<div class="card-body">

<div class="progress mb-3" style="height:25px;">

<div
class="progress-bar bg-success"
style="width:35%;">

35% Used

</div>

</div>

<div class="row">

<div class="col-md-4">

<strong>Total Storage</strong>

<br>

100 GB

</div>

<div class="col-md-4">

<strong>Used</strong>

<br>

35 GB

</div>

<div class="col-md-4">

<strong>Available</strong>

<br>

65 GB

</div>

</div>

</div>

</div>

<!-- =========================================
        ACTION BUTTONS
========================================== -->

<div class="card shadow border-0 mb-5">

<div class="card-body text-center">

<button
type="submit"
name="save"
class="btn btn-primary btn-lg px-5">

<i class="fas fa-save me-2"></i>

Save Settings

</button>

<button
type="reset"
class="btn btn-warning btn-lg px-5 ms-3">

<i class="fas fa-rotate-left me-2"></i>

Reset

</button>

<a
href="dashboard.php"
class="btn btn-secondary btn-lg px-5 ms-3">

<i class="fas fa-home me-2"></i>

Dashboard

</a>

</div>

</div>

</form>

<!-- =========================================
        FOOTER
========================================== -->

<footer class="text-center py-4">

<hr>

<p class="mb-1">

© <?php echo date("Y"); ?>

<strong>Forces Academy LMS</strong>

</p>

<p class="text-muted">

Professional Learning Management System

</p>

</footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>