<?php
session_start();

if(!isset($_SESSION['admin_id'])){

    header("Location: login.php");
    exit();

}

include("../config/database.php");
?>

<?php

include("../config/database.php");

// Admin Information
$admin_id = $_SESSION['admin_id'];

$adminQuery = mysqli_query($conn,
"SELECT * FROM admins
WHERE id='$admin_id'
LIMIT 1");

$admin = mysqli_fetch_assoc($adminQuery);

$admin_name = $admin['full_name'] ?? "Administrator";



/*==================================================
    DASHBOARD COUNTS
===================================================*/

// Students
$studentResult = mysqli_query($conn,
"SELECT COUNT(*) AS total FROM students");

$totalStudents = 0;

if($studentResult){
    $totalStudents = mysqli_fetch_assoc($studentResult)['total'];
}



// Teachers
$teacherResult = mysqli_query($conn,
"SELECT COUNT(*) AS total FROM teachers");

$totalTeachers = 0;

if($teacherResult){
    $totalTeachers = mysqli_fetch_assoc($teacherResult)['total'];
}



// Courses
$courseResult = mysqli_query($conn,
"SELECT COUNT(*) AS total FROM courses");

$totalCourses = 0;

if($courseResult){
    $totalCourses = mysqli_fetch_assoc($courseResult)['total'];
}



// Assignments
$assignmentResult = mysqli_query($conn,
"SELECT COUNT(*) AS total FROM assignments");

$totalAssignments = 0;

if($assignmentResult){
    $totalAssignments = mysqli_fetch_assoc($assignmentResult)['total'];
}



// Submitted Assignments
$submissionResult = mysqli_query($conn,
"SELECT COUNT(*) AS total FROM submissions");

$totalSubmissions = 0;

if($submissionResult){
    $totalSubmissions = mysqli_fetch_assoc($submissionResult)['total'];
}



// Notices
$noticeResult = mysqli_query($conn,
"SELECT COUNT(*) AS total FROM notices");

$totalNotices = 0;

if($noticeResult){
    $totalNotices = mysqli_fetch_assoc($noticeResult)['total'];
}



/*==================================================
    RECENT NOTICES
===================================================*/

$recentNotices = mysqli_query($conn,

"SELECT *
FROM notices
ORDER BY created_at DESC
LIMIT 5"

);



/*==================================================
    RECENT ACTIVITIES
===================================================*/

$recentActivities = mysqli_query($conn,

"SELECT
students.full_name,
assignments.title,
submissions.submitted_at,
submissions.status

FROM submissions

INNER JOIN students
ON students.id=submissions.student_id

INNER JOIN assignments
ON assignments.id=submissions.assignment_id

ORDER BY submissions.submitted_at DESC

LIMIT 8"

);



/*==================================================
    MONTHLY STUDENT REGISTRATIONS
===================================================*/

$chartLabels = [];
$chartData = [];

$chartQuery = mysqli_query($conn,

"SELECT
MONTHNAME(created_at) AS month,
COUNT(*) AS total

FROM students

GROUP BY MONTH(created_at)

ORDER BY MONTH(created_at)"

);

while($row=mysqli_fetch_assoc($chartQuery))
{
    $chartLabels[]=$row['month'];
    $chartData[]=$row['total'];
}



/*==================================================
    PIE CHART DATA
===================================================*/

$pieLabels=[
    "Students",
    "Teachers",
    "Courses",
    "Assignments"
];

$pieData=[
    $totalStudents,
    $totalTeachers,
    $totalCourses,
    $totalAssignments
];

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

Admin Dashboard |
Forces Academy LMS

</title>

<!-- Bootstrap -->

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<!-- Font Awesome -->

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

<!-- Google Font -->

<link
href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet">

<!-- Animate CSS -->

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<!-- AOS Animation -->

<link
href="https://unpkg.com/aos@2.3.4/dist/aos.css"
rel="stylesheet">

<!-- Dashboard CSS -->

<link
rel="stylesheet"
href="assets/css/dashboard.css">

<!-- Chart JS -->

<script
src="https://cdn.jsdelivr.net/npm/chart.js">
</script>

<style>

body{

font-family:'Poppins',sans-serif;
background:#f5f7fb;

}

.loading{

position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:#fff;
display:flex;
justify-content:center;
align-items:center;
z-index:99999;

}

.spinner{

width:60px;
height:60px;
border-radius:50%;
border:6px solid #ddd;
border-top:6px solid #0d6efd;
animation:spin 1s linear infinite;

}

@keyframes spin{

100%{

transform:rotate(360deg);

}

}

</style>

</head>

<body>

<div class="loading">

<div class="spinner"></div>

</div>

<div class="wrapper">
<!-- ==============================
        SIDEBAR
================================ -->

<aside class="sidebar">

    <div class="logo-area">

        <img src="assets/images/logo.png" class="logo" alt="Logo">

        <h3>Forces Academy</h3>

        <span>LMS Admin</span>

    </div>


    <ul class="sidebar-menu">

        <li class="active">
            <a href="dashboard.php">
                <i class="fas fa-home"></i>
                Dashboard
            </a>
        </li>

        <li>
            <a href="manage_students.php">
                <i class="fas fa-user-graduate"></i>
                Students
            </a>
        </li>

        <li>
            <a href="manage_teachers.php">
                <i class="fas fa-chalkboard-teacher"></i>
                Teachers
            </a>
        </li>

        <li>
            <a href="manage_courses.php">
                <i class="fas fa-book-open"></i>
                Courses
            </a>
        </li>

        <li>
            <a href="manage_assignments.php">
                <i class="fas fa-file-alt"></i>
                Assignments
            </a>
        </li>

        <li>
            <a href="manage_submissions.php">
                <i class="fas fa-upload"></i>
                Submissions
            </a>
        </li>
        <li>
    <a href="manage_results.php">
        <i class="fas fa-chart-line"></i>
        Results
    </a>
</li>
<li>
            <a href="manage_notices.php">
                <i class="fas fa-bullhorn"></i>
                Notices
            </a>
        </li>
        <li>
    <a href="timetable.php">
        <i class="fas fa-calendar-alt"></i>
        <span>Timetable</span>
    </a>
</li>
</li>
        <li>
            <a href="reports.php">
                <i class="fas fa-chart-bar"></i>
                Reports
            </a>
        </li>
        <li>
            <a href="settings.php">
                <i class="fas fa-cog"></i>
                Settings
            </a>
        </li>
    </ul>
    <div class="logout-area">

        <a href="logout.php" class="btn btn-danger w-100">

            <i class="fas fa-sign-out-alt"></i>

            Logout
        </a>

    </div>

</aside>



<!-- ==============================
        MAIN CONTENT
================================ -->

<div class="main-content">

<!-- ==============================
        TOP NAVBAR
================================ -->

<nav class="navbar navbar-expand-lg dashboard-navbar">

<div class="container-fluid">

<button class="btn menu-toggle">

<i class="fas fa-bars"></i>

</button>



<form class="search-box">

<div class="input-group">

<span class="input-group-text">

<i class="fas fa-search"></i>

</span>

<input
type="text"
class="form-control"
placeholder="Search anything...">

</div>

</form>




<ul class="navbar-nav ms-auto align-items-center">


<li class="nav-item me-4">

<a href="#">

<i class="far fa-bell fa-lg"></i>

<span class="badge bg-danger">

<?php echo $totalNotices; ?>

</span>

</a>

</li>




<li class="nav-item me-4">

<a href="#">

<i class="far fa-envelope fa-lg"></i>

<span class="badge bg-primary">

3

</span>

</a>

</li>




<li class="nav-item dropdown">

<a

class="nav-link dropdown-toggle"

data-bs-toggle="dropdown"

href="#">

<img

src="assets/images/avatar.png"

class="admin-avatar">

<?php echo $admin_name; ?>

</a>

<ul class="dropdown-menu dropdown-menu-end">

<li>

<a class="dropdown-item"

href="profile.php">

<i class="fas fa-user me-2"></i>

My Profile

</a>

</li>

<li>

<a class="dropdown-item"

href="settings.php">

<i class="fas fa-cog me-2"></i>

Settings

</a>

</li>

<li><hr></li>

<li>

<a

class="dropdown-item text-danger"

href="logout.php">

<i class="fas fa-sign-out-alt me-2"></i>

Logout

</a>

</li>

</ul>

</li>

</ul>

</div>

</nav>




<!-- ==============================
        HERO SECTION
================================ -->

<div class="container-fluid">

<div

class="hero-banner"

data-aos="fade-up">

<div class="row align-items-center">

<div class="col-lg-8">

<h2>

Welcome Back,

<?php echo $admin_name; ?> 👋

</h2>

<p>

Manage students, teachers, courses, assignments,
reports and notices from one powerful dashboard.

</p>

<div class="mt-4">

<a

href="manage_students.php"

class="btn btn-primary btn-lg">

Manage Students

</a>

<a

href="manage_courses.php"

class="btn btn-light btn-lg ms-3">

Courses

</a>

</div>

</div>

<div class="col-lg-4 text-center">

<img

src="assets/images/admin-banner.png"

class="img-fluid banner-image">

</div>

</div>

</div>

<!-- ==========================================
        DASHBOARD STATISTICS
===========================================-->

<div class="row mt-4">

    <!-- Students -->
    <div class="col-xl-3 col-md-6 mb-4">

        <div class="dashboard-card bg-primary text-white" data-aos="zoom-in">

            <div class="card-icon">
                <i class="fas fa-user-graduate"></i>
            </div>

            <div class="card-info">

                <h6>Total Students</h6>

                <h2 class="counter">
                    <?php echo $totalStudents; ?>
                </h2>

                <p>
                    Registered Students
                </p>

            </div>

        </div>

    </div>



    <!-- Teachers -->

    <div class="col-xl-3 col-md-6 mb-4">

        <div class="dashboard-card bg-success text-white" data-aos="zoom-in">

            <div class="card-icon">

                <i class="fas fa-chalkboard-teacher"></i>

            </div>

            <div class="card-info">

                <h6>Total Teachers</h6>

                <h2 class="counter">

                    <?php echo $totalTeachers; ?>

                </h2>

                <p>

                    Active Teachers

                </p>

            </div>

        </div>

    </div>




    <!-- Courses -->

    <div class="col-xl-3 col-md-6 mb-4">

        <div class="dashboard-card bg-warning text-white" data-aos="zoom-in">

            <div class="card-icon">

                <i class="fas fa-book-open"></i>

            </div>

            <div class="card-info">

                <h6>Total Courses</h6>

                <h2 class="counter">

                    <?php echo $totalCourses; ?>

                </h2>

                <p>

                    Available Courses

                </p>

            </div>

        </div>

    </div>




    <!-- Assignments -->

    <div class="col-xl-3 col-md-6 mb-4">

        <div class="dashboard-card bg-danger text-white" data-aos="zoom-in">

            <div class="card-icon">

                <i class="fas fa-file-alt"></i>

            </div>

            <div class="card-info">

                <h6>Total Assignments</h6>

                <h2 class="counter">

                    <?php echo $totalAssignments; ?>

                </h2>

                <p>

                    Published Assignments

                </p>

            </div>

        </div>

    </div>

</div>
<a href="timetable.php" class="btn btn-primary">

    <i class="fas fa-calendar-alt me-2"></i>

    Timetable

</a>



<!-- ==========================================
            QUICK ACTIONS
===========================================-->

<div class="row mt-3">

<div class="col-lg-12">

<div class="card shadow border-0">

<div class="card-body">

<h4 class="mb-4">

<i class="fas fa-bolt text-warning"></i>

Quick Actions

</h4>

<div class="row g-4">

<div class="col-lg-2 col-md-4 col-6">

<a href="add_student.php" class="quick-box">

<i class="fas fa-user-plus"></i>

<span>Add Student</span>

</a>

</div>


<div class="col-lg-2 col-md-4 col-6">

<a href="add_teacher.php" class="quick-box">

<i class="fas fa-user-tie"></i>

<span>Add Teacher</span>

</a>

</div>



<div class="col-lg-2 col-md-4 col-6">

<a href="add_course.php" class="quick-box">

<i class="fas fa-book"></i>

<span>Add Course</span>

</a>

</div>



<div class="col-lg-2 col-md-4 col-6">

<a href="add_assignment.php" class="quick-box">

<i class="fas fa-file-circle-plus"></i>

<span>Assignment</span>

</a>

</div>



<div class="col-lg-2 col-md-4 col-6">

<a href="manage_notices.php" class="quick-box">

<i class="fas fa-bullhorn"></i>

<span>Notice</span>

</a>

</div>



<div class="col-lg-2 col-md-4 col-6">

<a href="reports.php" class="quick-box">

<i class="fas fa-chart-line"></i>

<span>Reports</span>

</a>

</div>

</div>

</div>

</div>

</div>

</div>



<!-- ==========================================
        CHART SECTION STARTS
===========================================-->

<div class="row mt-4">

<div class="col-lg-8">

<div class="card shadow border-0">

<div class="card-header bg-white">

<h5>

Student Registration Overview

</h5>

</div>

<div class="card-body">

<canvas id="studentChart" height="120"></canvas>

</div>

</div>

</div>

<div class="col-lg-4">

<div class="card shadow border-0">

<div class="card-header bg-white">

<h5>

System Statistics

</h5>

</div>

<div class="card-body">

<canvas id="pieChart"></canvas>

</div>

</div>

</div>

</div>
s
<!-- ==========================================
        RECENT NOTICES & RECENT ACTIVITIES
========================================== -->

<div class="row mt-4">

    <!-- Recent Notices -->

    <div class="col-lg-4">

        <div class="card shadow-lg border-0 rounded-4">

            <div class="card-header bg-primary text-white">

                <h5 class="mb-0">
                    <i class="fas fa-bullhorn me-2"></i>
                    Recent Notices
                </h5>

            </div>

            <div class="card-body p-0">

                <?php if(mysqli_num_rows($recentNotices)>0){ ?>

                    <ul class="list-group list-group-flush">

                        <?php while($notice=mysqli_fetch_assoc($recentNotices)){ ?>

                        <li class="list-group-item">

                            <h6 class="fw-bold mb-1">

                                <?php echo htmlspecialchars($notice['title']); ?>

                            </h6>

                            <small class="text-muted">

                                <?php echo date("d M Y",
                                strtotime($notice['created_at'])); ?>

                            </small>

                            <p class="mt-2 mb-0">

                                <?php echo substr(strip_tags($notice['title']),0,80); ?>

                                ...

                            </p>

                        </li>

                        <?php } ?>

                    </ul>

                <?php }else{ ?>

                    <div class="p-4 text-center">

                        <img src="assets/images/empty.png"
                             width="120">

                        <p class="text-muted mt-3">

                            No notices available.

                        </p>

                    </div>

                <?php } ?>

            </div>

        </div>

    </div>



    <!-- Recent Activities -->

    <div class="col-lg-8">

        <div class="card shadow-lg border-0 rounded-4">

            <div class="card-header bg-success text-white">

                <h5 class="mb-0">

                    <i class="fas fa-clock me-2"></i>

                    Recent Assignment Activity

                </h5>

            </div>

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th>Student</th>

                            <th>Assignment</th>

                            <th>Date</th>

                            <th>Status</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php while($activity=mysqli_fetch_assoc($recentActivities)){ ?>

                        <tr>

                            <td>

                                <?php echo htmlspecialchars($activity['full_name']); ?>

                            </td>

                            <td>

                                <?php echo htmlspecialchars($activity['title']); ?>

                            </td>

                            <td>

                                <?php echo date("d M Y",
                                strtotime($activity['submitted_at'])); ?>

                            </td>

                            <td>

                            <?php

                            if($activity['status']=="graded"){

                                echo '<span class="badge bg-success">Graded</span>';

                            }else{

                                echo '<span class="badge bg-warning text-dark">Submitted</span>';

                            }

                            ?>

                            </td>

                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>



<!-- ==========================================
        FOOTER
========================================== -->

<footer class="mt-5 mb-3 text-center">

    <small class="text-muted">

        © <?php echo date("Y"); ?>

        Forces Academy LMS |

        Admin Dashboard

    </small>

</footer>

</div>

<!-- End Main Content -->

</div>





<!-- ==========================================
        JAVASCRIPT
========================================== -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

<script>

AOS.init({

duration:800,

once:true

});


// Hide Loader

window.addEventListener("load",function(){

document.querySelector(".loading").style.display="none";

});



// Student Registration Chart

const studentChart = document.getElementById("studentChart");

new Chart(studentChart,{

type:'line',

data:{

labels:<?php echo json_encode($chartLabels); ?>,

datasets:[{

label:'Students',

data:<?php echo json_encode($chartData); ?>,

borderColor:'#0d6efd',

backgroundColor:'rgba(13,110,253,.2)',

fill:true,

tension:.4

}]

},

options:{

responsive:true,

plugins:{

legend:{

display:false

}

}

}

});




// Pie Chart

const pieChart=document.getElementById("pieChart");

new Chart(pieChart,{

type:'doughnut',

data:{

labels:<?php echo json_encode($pieLabels); ?>,

datasets:[{

data:<?php echo json_encode($pieData); ?>,

backgroundColor:[

"#0d6efd",

"#198754",

"#ffc107",

"#dc3545"

]

}]

},

options:{

responsive:true

}

});




// Counter Animation

const counters=document.querySelectorAll(".counter");

counters.forEach(counter=>{

const target=+counter.innerText;

let count=0;

const update=()=>{

const increment=Math.ceil(target/60);

count+=increment;

if(count<target){

counter.innerText=count;

requestAnimationFrame(update);

}else{

counter.innerText=target;

}

};

update();

});

</script>

</body>

</html>