<?php
$current = basename($_SERVER['PHP_SELF']);
?>

<!-- ==========================================
        SIDEBAR
========================================== -->

<div class="sidebar">

    <!-- Logo -->

    <div class="logo-area">

        <img src="assets/images/logo.png" class="logo" alt="Logo">

        <h3>Forces Academy</h3>

        <span>LMS Admin Panel</span>

    </div>

    <!-- Navigation -->

    <ul class="sidebar-menu">

        <li class="<?= ($current=="dashboard.php")?'active':''; ?>">

            <a href="dashboard.php">

                <i class="fas fa-home"></i>

                Dashboard

            </a>

        </li>

        <li class="<?= ($current=="manage_students.php")?'active':''; ?>">

            <a href="manage_students.php">

                <i class="fas fa-user-graduate"></i>

                Students

            </a>

        </li>



        <li class="<?= ($current=="manage_teachers.php")?'active':''; ?>">

            <a href="manage_teachers.php">

                <i class="fas fa-chalkboard-teacher"></i>

                Teachers

            </a>

        </li>



        <li class="<?= ($current=="manage_courses.php")?'active':''; ?>">

            <a href="manage_courses.php">

                <i class="fas fa-book-open"></i>

                Courses

            </a>

        </li>



        <li class="<?= ($current=="manage_assignments.php")?'active':''; ?>">

            <a href="manage_assignments.php">

                <i class="fas fa-file-alt"></i>

                Assignments

            </a>

        </li>



        <li class="<?= ($current=="manage_submissions.php")?'active':''; ?>">

            <a href="manage_submissions.php">

                <i class="fas fa-upload"></i>

                Submissions

            </a>

        </li>



        <li class="<?= ($current=="manage_notices.php")?'active':''; ?>">

            <a href="manage_notices.php">

                <i class="fas fa-bullhorn"></i>

                Notices

            </a>

        </li>

        <li class="<?= ($current=="manage_results.php")?'active':''; ?>">

            <a href="manage_results.php">

                <i class="fas fa-chart-line"></i>

                Results

            </a>
        </li>

    <li class="<?= ($current=="timetable.php")?'active':''; ?>">

        <a href="timetable.php">
            <i class="fas fa-calendar-alt"></i>
            Timetable
        </a>
    </li>
    <li class="<?= ($current=="fees.php") ? 'active' : ''; ?>">

    <a href="fees.php">

        <i class="fas fa-money-bill-wave"></i>

        Fees

    </a>

</li>
        <li class="<?= ($current=="reports.php")?'active':''; ?>">

            <a href="reports.php">

                <i class="fas fa-chart-pie"></i>

                Reports
            </a>
</li>
        <li class="<?= ($current=="settings.php")?'active':''; ?>">

            <a href="settings.php">

                <i class="fas fa-cog"></i>
                Settings
            </a>
        </li>
    <!-- Bottom Card -->

        <p>
            Forces Academy LMS &copy; 2024
        </p>
    </div>

