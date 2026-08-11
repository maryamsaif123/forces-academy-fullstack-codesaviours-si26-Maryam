<?php
session_start();

include("../config/database.php");

/* =========================================
   CHECK STUDENT LOGIN
========================================= */

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = (int) $_SESSION['student_id'];


/* =========================================
   FETCH STUDENT INFORMATION
========================================= */

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, full_name, email
     FROM students
     WHERE id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$student = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


/* Student does not exist */

if (!$student) {
    session_destroy();
    header("Location: login.php");
    exit();
}


/* =========================================
   FETCH FEE RECORDS
   According to your fees table
========================================= */

$fee_records = array();

$fee_stmt = mysqli_prepare(
    $conn,
    "SELECT
        id,
        amount,
        due_date,
        paid_date,
        status,
        description,
        created_at
     FROM fees
     WHERE student_id = ?
     ORDER BY due_date DESC"
);

if (!$fee_stmt) {
    die("Fee query error: " . mysqli_error($conn));
}

mysqli_stmt_bind_param(
    $fee_stmt,
    "i",
    $student_id
);

mysqli_stmt_execute($fee_stmt);

$fee_result = mysqli_stmt_get_result($fee_stmt);


/* =========================================
   CALCULATE FEE SUMMARY
========================================= */

$total_fee = 0;
$paid_fee = 0;
$remaining_fee = 0;

while ($fee = mysqli_fetch_assoc($fee_result)) {

    $fee_records[] = $fee;

    $amount = (float) $fee['amount'];

    $total_fee += $amount;

    if (strtolower($fee['status']) == "paid") {

        $paid_fee += $amount;

    }
}


/* Remaining fee */

$remaining_fee = $total_fee - $paid_fee;

if ($remaining_fee < 0) {
    $remaining_fee = 0;
}

mysqli_stmt_close($fee_stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>My Fees | Forces Academy</title>


<!-- Bootstrap -->

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">


<!-- Font Awesome -->

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
rel="stylesheet">


<style>

body {

    margin: 0;

    background: #f4f7fb;

    font-family: Arial, sans-serif;

}


/* =========================================
   MAIN CONTENT
========================================= */

.main-content {

    margin-left: 250px;

    padding: 30px;

}


/* =========================================
   PAGE HEADER
========================================= */

.page-header {

    background: white;

    padding: 25px;

    border-radius: 18px;

    margin-bottom: 25px;

    box-shadow: 0 5px 20px rgba(0,0,0,.06);

}


/* =========================================
   SUMMARY CARDS
========================================= */

.summary-card {

    background: white;

    border-radius: 18px;

    padding: 22px;

    box-shadow: 0 5px 20px rgba(0,0,0,.07);

    height: 100%;

}


.summary-icon {

    width: 55px;

    height: 55px;

    border-radius: 14px;

    display: flex;

    align-items: center;

    justify-content: center;

    color: white;

    font-size: 22px;

}


.summary-card h3 {

    font-weight: 700;

}


/* =========================================
   FEE TABLE
========================================= */

.fee-card {

    border: none;

    border-radius: 18px;

    overflow: hidden;

    box-shadow: 0 5px 20px rgba(0,0,0,.07);

}


.fee-card .card-header {

    background: #1268f3;

    color: white;

    padding: 18px 22px;

}


.table th {

    white-space: nowrap;

}


.badge {

    padding: 8px 12px;

}


/* =========================================
   MOBILE
========================================= */

@media(max-width: 768px) {

    .main-content {

        margin-left: 0;

        padding: 15px;

    }

}

</style>

</head>


<body>


<div class="main-content">


<!-- =========================================
     PAGE HEADER
========================================= -->

<div class="page-header">

    <div class="d-flex justify-content-between
                align-items-center">

        <div>

            <h2 class="fw-bold mb-1">

                <i class="fas fa-money-bill-wave
                          text-primary me-2"></i>

                My Fees

            </h2>

            <p class="text-muted mb-0">

                View your fee records, payments
                and due dates.

            </p>

        </div>


        <a href="dashboard.php"
           class="btn btn-secondary">

            <i class="fas fa-arrow-left me-2"></i>

            Dashboard

        </a>

    </div>

</div>



<!-- =========================================
     STUDENT INFORMATION
========================================= -->

<div class="card border-0 shadow-sm
            rounded-4 mb-4">

    <div class="card-body">

        <h5 class="fw-bold mb-1">

            <?php
            echo htmlspecialchars(
                $student['full_name']
            );
            ?>

        </h5>

        <p class="text-muted mb-0">

            <?php
            echo htmlspecialchars(
                $student['email']
            );
            ?>

        </p>

    </div>

</div>



<!-- =========================================
     FEE SUMMARY
========================================= -->

<div class="row g-4 mb-4">


<!-- TOTAL FEE -->

<div class="col-md-4">

<div class="summary-card">

    <div class="summary-icon bg-primary">

        <i class="fas fa-file-invoice-dollar"></i>

    </div>

    <h6 class="text-muted mt-3">

        Total Fee

    </h6>

    <h3>

        PKR

        <?php
        echo number_format(
            $total_fee,
            2
        );
        ?>

    </h3>

</div>

</div>



<!-- PAID FEE -->

<div class="col-md-4">

<div class="summary-card">

    <div class="summary-icon bg-success">

        <i class="fas fa-check-circle"></i>

    </div>

    <h6 class="text-muted mt-3">

        Paid Amount

    </h6>

    <h3 class="text-success">

        PKR

        <?php
        echo number_format(
            $paid_fee,
            2
        );
        ?>

    </h3>

</div>

</div>



<!-- REMAINING FEE -->

<div class="col-md-4">

<div class="summary-card">

    <div class="summary-icon bg-warning">

        <i class="fas fa-clock"></i>

    </div>

    <h6 class="text-muted mt-3">

        Remaining Fee

    </h6>

    <h3 class="text-warning">

        PKR

        <?php
        echo number_format(
            $remaining_fee,
            2
        );
        ?>

    </h3>

</div>

</div>

</div>



<!-- =========================================
     FEE RECORDS
========================================= -->

<div class="card fee-card">


<div class="card-header">

    <h5 class="mb-0">

        <i class="fas fa-table me-2"></i>

        My Fee Records

    </h5>

</div>



<div class="card-body">


<div class="table-responsive">


<table class="table table-hover
              table-bordered align-middle">


<thead class="table-light">

<tr>

    <th>#</th>

    <th>Description</th>

    <th>Amount</th>

    <th>Due Date</th>

    <th>Status</th>

    <th>Paid Date</th>

</tr>

</thead>



<tbody>


<?php

if (count($fee_records) > 0) {

    $count = 1;

    foreach ($fee_records as $fee) {

?>

<tr>


<!-- Number -->

<td>

<?php
echo $count++;
?>

</td>



<!-- Description -->

<td>

<strong>

<?php

if (!empty($fee['description'])) {

    echo htmlspecialchars(
        $fee['description']
    );

} else {

    echo "Fee Payment";

}

?>

</strong>

</td>



<!-- Amount -->

<td>

<strong>

PKR

<?php

echo number_format(
    (float)$fee['amount'],
    2
);

?>

</strong>

</td>



<!-- Due Date -->

<td>

<?php

if (!empty($fee['due_date'])) {

    echo date(
        "d M Y",
        strtotime($fee['due_date'])
    );

} else {

    echo "-";

}

?>

</td>



<!-- Status -->

<td>

<?php

$status = strtolower(
    trim($fee['status'])
);


if ($status == "paid") {

?>

<span class="badge bg-success">

    <i class="fas fa-check me-1"></i>

    Paid

</span>

<?php

} elseif ($status == "overdue") {

?>

<span class="badge bg-danger">

    <i class="fas fa-exclamation-circle me-1"></i>

    Overdue

</span>

<?php

} else {

?>

<span class="badge bg-warning text-dark">

    <i class="fas fa-clock me-1"></i>

    Pending

</span>

<?php

}

?>

</td>



<!-- Paid Date -->

<td>

<?php

if (!empty($fee['paid_date'])) {

    echo date(
        "d M Y",
        strtotime($fee['paid_date'])
    );

} else {

    echo "-";

}

?>

</td>


</tr>


<?php

    }

} else {

?>


<tr>

<td colspan="6"
    class="text-center text-muted py-5">

    <i class="fas fa-file-invoice-dollar
              fa-2x mb-3"></i>

    <br>

    No fee records available.

</td>

</tr>


<?php

}

?>


</tbody>

</table>


</div>

</div>

</div>



<!-- =========================================
     FOOTER
========================================= -->

<footer class="text-center mt-5">

<small class="text-muted">

© <?php echo date("Y"); ?>

Forces Academy LMS

</small>

</footer>


</div>


<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


</body>

</html>