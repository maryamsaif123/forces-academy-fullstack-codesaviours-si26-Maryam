<?php

$date = date("l, d F Y");

?>

<nav class="top-navbar">

<div class="left-side">

<h3>

Dashboard

</h3>

<p>

<?php echo $date; ?>

</p>

</div>

<div class="right-side">

<!-- Search -->

<div class="search-box">

<i class="fas fa-search"></i>

<input
type="text"
placeholder="Search...">

</div>

<!-- Notification -->

<div class="notification">

<i class="fas fa-bell"></i>

<span class="badge">

3

</span>

</div>

<!-- Messages -->

<div class="notification">

<i class="fas fa-envelope"></i>

<span class="badge bg-success">

2

</span>

</div>

<!-- Admin Profile -->

<div class="admin-profile">

<img
src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png">

<div>

<h6>

<?php

echo $_SESSION['admin'];

?>

</h6>

<small>

Administrator

</small>

</div>

</div>

</div>

</nav>