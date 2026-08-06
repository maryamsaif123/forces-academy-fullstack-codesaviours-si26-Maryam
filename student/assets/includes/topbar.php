<?php

$date = date("d M Y");
$day = date("l");

?>

<!-- ==========================================
        TOP NAVBAR
========================================== -->

<nav class="navbar dashboard-navbar navbar-expand-lg">

<div class="container-fluid">

<!-- Sidebar Toggle -->

<button class="btn menu-toggle">

<i class="fas fa-bars"></i>

</button>



<!-- Search -->

<div class="search-box ms-3">

<div class="input-group">

<input
type="text"
class="form-control"
placeholder="Search here...">

<button class="btn btn-primary">

<i class="fas fa-search"></i>

</button>

</div>

</div>



<!-- Right Side -->

<ul class="navbar-nav ms-auto align-items-center">




<!-- Calendar -->

<li class="nav-item me-4">

<div class="date-box">

<div>

<i class="far fa-calendar-alt"></i>

</div>

<div class="ms-2">

<h6>

<?php echo $date; ?>

</h6>

<small>

<?php echo $day; ?>

</small>

</div>

</div>

</li>




<!-- Notification -->

<li class="nav-item dropdown me-3">

<a

class="nav-link"

href="#"

data-bs-toggle="dropdown">

<i class="far fa-bell fa-lg"></i>

<span class="notification-badge">

3

</span>

</a>

<ul class="dropdown-menu dropdown-menu-end">

<li>

<h6 class="dropdown-header">

Notifications

</h6>

</li>

<li>

<a class="dropdown-item" href="#">

New student registered.

</a>

</li>

<li>

<a class="dropdown-item" href="#">

Assignment submitted.

</a>

</li>

<li>

<a class="dropdown-item" href="#">

New notice published.

</a>

</li>

</ul>

</li>




<!-- Messages -->

<li class="nav-item dropdown me-4">

<a

class="nav-link"

href="#"

data-bs-toggle="dropdown">

<i class="far fa-envelope fa-lg"></i>

<span class="notification-badge bg-success">

2

</span>

</a>

<ul class="dropdown-menu dropdown-menu-end">

<li>

<h6 class="dropdown-header">

Messages

</h6>

</li>

<li>

<a class="dropdown-item" href="#">

Teacher sent a message.

</a>

</li>

<li>

<a class="dropdown-item" href="#">

Principal approved course.

</a>

</li>

</ul>

</li>




<!-- Profile -->

<li class="nav-item dropdown">

<a

class="nav-link dropdown-toggle d-flex align-items-center"

href="#"

data-bs-toggle="dropdown">

<img

src="assets/images/avatar.png"

class="admin-avatar"

alt="Admin">

<div class="ms-2">

<h6 class="mb-0">

</h6>

<small>

Administrator

</small>

</div>

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

<li>

<hr class="dropdown-divider">

</li>

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