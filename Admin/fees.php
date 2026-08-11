<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

$success = "";
$error = "";

/* =========================================
   ADD FEE
========================================= */

if (isset($_POST['add_fee'])) {

    $student_id = (int) $_POST['student_id'];
    $amount     = (float) $_POST['amount'];
    $due_date   = $_POST['due_date'];
    $status     = $_POST['status'];
    $paid_date  = !empty($_POST['paid_date'])
        ? $_POST['paid_date']
        : NULL;

    $description = trim($_POST['description']);

    /* Validation */

    if (
        $student_id <= 0 ||
        $amount <= 0 ||
        empty($due_date) ||
        empty($status)
    ) {

        $error = "Please fill all required fields.";

    } else {

        /* Prepared Statement */

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO fees
            (
                student_id,
                amount,
                due_date,
                paid_date,
                status,
                description
            )
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "idssss",
                $student_id,
                $amount,
                $due_date,
                $paid_date,
                $status,
                $description
            );

            if (mysqli_stmt_execute($stmt)) {

                $success = "Fee record added successfully.";

            } else {

                $error = "Unable to add fee record: " .
                         mysqli_stmt_error($stmt);
            }

            mysqli_stmt_close($stmt);

        } else {

            $error = "Database error: " . mysqli_error($conn);
        }
    }
}


/* =========================================
   DELETE FEE
========================================= */

if (isset($_GET['delete'])) {

    $id = (int) $_GET['delete'];

    if ($id > 0) {

        $stmt = mysqli_prepare(
            $conn,
            "DELETE FROM fees WHERE id = ?"
        );

        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "i",
                $id
            );

            mysqli_stmt_execute($stmt);

            mysqli_stmt_close($stmt);
        }
    }

    header("Location: fees.php");
    exit();
}


/* =========================================
   FETCH STUDENTS
========================================= */

$students = mysqli_query(
    $conn,
    "SELECT id, full_name, email
     FROM students
     ORDER BY full_name ASC"
);


/* =========================================
   FETCH FEES
========================================= */

$fees = mysqli_query(
    $conn,
    "SELECT
        fees.id,
        fees.student_id,
        fees.amount,
        fees.due_date,
        fees.paid_date,
        fees.status,
        fees.description,
        fees.created_at,
        students.full_name
     FROM fees
     LEFT JOIN students
     ON fees.student_id = students.id
     ORDER BY fees.created_at DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Fee Management | Forces Academy</title>


<!-- Bootstrap -->

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">


<!-- Font Awesome -->

<link
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
rel="stylesheet">


<!-- Dashboard CSS -->

<link
href="assets/css/dashboard.css"
rel="stylesheet">


<style>
/* =========================================
   SIDEBAR
========================================= */

.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 270px;
    height: 100vh;
    background: #14263d;
    color: #fff;
    z-index: 1000;
    overflow-y: auto;
    overflow-x: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 5px 0 20px rgba(0, 0, 0, 0.08);
}

/* Logo Area */

.logo-area {
    text-align: center;
    padding: 25px 15px 20px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}

.logo-area .logo {
    width: 90px;
    height: 90px;
    object-fit: contain;
    display: block;
    margin: 0 auto 12px;
    border-radius: 50%;
    background: #fff;
    padding: 5px;
}

.logo-area h3 {
    color: #fff;
    font-size: 19px;
    font-weight: 700;
    margin: 5px 0;
}

.logo-area span {
    color: #93b4df;
    font-size: 12px;
}

/* Sidebar Menu */

.sidebar-menu {
    list-style: none;
    padding: 18px 12px;
    margin: 0;
}

.sidebar-menu li {
    margin-bottom: 6px;
}

.sidebar-menu li a {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 14px;
    color: #dbe7f5;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.sidebar-menu li a i {
    width: 20px;
    text-align: center;
    font-size: 15px;
}

/* Hover */

.sidebar-menu li a:hover {
    background: rgba(37, 99, 235, 0.25);
    color: #fff;
    transform: translateX(3px);
}

/* Active */

.sidebar-menu li.active a {
    background: #2563eb;
    color: #fff;
    box-shadow: 0 6px 18px rgba(37, 99, 235, 0.30);
}

/* Logout */

.logout-area {
    margin-top: auto;
    padding: 15px;
}

.logout-area .logout-btn,
.logout-area .btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 11px;
    border-radius: 10px;
    font-weight: 600;
    border: none;
}

/* Sidebar Scrollbar */

.sidebar::-webkit-scrollbar {
    width: 5px;
}

.sidebar::-webkit-scrollbar-thumb {
    background: #2563eb;
    border-radius: 10px;
}

.sidebar::-webkit-scrollbar-track {
    background: transparent;
}

body {
    background: #f4f7fb;
    font-family: 'Poppins', sans-serif;
}


/* =========================================
   MAIN CONTENT
========================================= */

.main-content {
    padding: 30px;
}


/* =========================================
   PAGE HEADER
========================================= */

.page-header {

    background: #ffffff;

    padding: 25px;

    border-radius: 18px;

    margin-bottom: 25px;

    box-shadow:
        0 5px 20px rgba(0,0,0,.06);
}


/* =========================================
   FEE CARD
========================================= */

.fee-card {

    border: none;

    border-radius: 18px;

    box-shadow:
        0 5px 20px rgba(0,0,0,.07);

    overflow: hidden;
}


.fee-card .card-header {

    background: #1268f3;

    color: white;

    padding: 18px 22px;
}


/* =========================================
   FORM
========================================= */

.form-control,
.form-select {

    border-radius: 10px;

    padding: 11px 12px;

    border: 1px solid #dee2e6;
}


.form-control:focus,
.form-select:focus {

    border-color: #1268f3;

    box-shadow:
        0 0 0 .2rem rgba(18,104,243,.10);
}


.form-label {

    color: #374151;
}


/* =========================================
   BUTTONS
========================================= */

.btn {

    border-radius: 9px;

    font-weight: 600;
}


/* =========================================
   TABLE
========================================= */

.table {

    margin-bottom: 0;
}


.table th {

    white-space: nowrap;

    font-size: 14px;

    padding: 15px;
}


.table td {

    padding: 15px;

    vertical-align: middle;
}


/* =========================================
   BADGES
========================================= */

.badge {

    padding: 8px 12px;

    border-radius: 20px;

    font-size: 12px;
}


/* =========================================
   FEE SUMMARY
========================================= */

.fee-summary {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 20px;

    margin-bottom: 25px;
}


.summary-box {

    background: white;

    padding: 20px;

    border-radius: 16px;

    box-shadow:
        0 5px 20px rgba(0,0,0,.06);
}


.summary-box h6 {

    color: #6b7280;

    margin-bottom: 8px;

    font-size: 14px;
}


.summary-box h3 {

    margin: 0;

    font-weight: 700;
}


@media(max-width:768px) {

    .main-content {

        padding: 15px;
    }

    .fee-summary {

        grid-template-columns: 1fr;
    }

}

</style>

</head>


<body>
<?php include("includes/sidebar.php"); ?>
<div class="wrapper">
<div class="main-content">

<?php include("includes/topbar.php"); ?>

<div class="container-fluid mt-4">


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

                Fee Management

            </h2>

            <p class="text-muted mb-0">

                Manage student fees, payments and due dates.

            </p>

        </div>


        <a
        href="dashboard.php"
        class="btn btn-secondary">

            <i class="fas fa-arrow-left me-2"></i>

            Dashboard

        </a>

    </div>

</div>


<!-- =========================================
     ALERTS
========================================= -->

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


<!-- =========================================
     ADD FEE FORM
========================================= -->

<div class="card fee-card mb-4">


    <div class="card-header">

        <h5 class="mb-0">

            <i class="fas fa-plus-circle me-2"></i>

            Add New Fee

        </h5>

    </div>


    <div class="card-body">

        <form method="POST">


            <div class="row">


                <!-- STUDENT -->

                <div class="col-md-6 mb-3">

                    <label class="form-label fw-semibold">

                        Student *

                    </label>


                    <select
                    name="student_id"
                    class="form-select"
                    required>

                        <option value="">

                            Select Student

                        </option>


                        <?php

                        if ($students &&
                            mysqli_num_rows($students) > 0) {

                            while (
                                $student =
                                mysqli_fetch_assoc($students)
                            ) {

                        ?>

                        <option
                        value="<?php
                            echo $student['id'];
                        ?>">

                            <?php

                            echo htmlspecialchars(
                                $student['full_name']
                            );

                            ?>

                        </option>

                        <?php

                            }

                        }

                        ?>

                    </select>

                </div>


                <!-- AMOUNT -->

                <div class="col-md-6 mb-3">

                    <label class="form-label fw-semibold">

                        Fee Amount *

                    </label>


                    <div class="input-group">

                        <span class="input-group-text">

                            PKR

                        </span>


                        <input
                        type="number"
                        name="amount"
                        class="form-control"
                        step="0.01"
                        min="1"
                        placeholder="Enter fee amount"
                        required>

                    </div>

                </div>


                <!-- DUE DATE -->

                <div class="col-md-4 mb-3">

                    <label class="form-label fw-semibold">

                        Due Date *

                    </label>


                    <input
                    type="date"
                    name="due_date"
                    class="form-control"
                    required>

                </div>


                <!-- STATUS -->

                <div class="col-md-4 mb-3">

                    <label class="form-label fw-semibold">

                        Status *

                    </label>


                    <select
                    name="status"
                    class="form-select"
                    required>

                        <option value="pending">

                            Pending

                        </option>

                        <option value="paid">

                            Paid

                        </option>

                        <option value="overdue">

                            Overdue

                        </option>

                    </select>

                </div>


                <!-- PAID DATE -->

                <div class="col-md-4 mb-3">

                    <label class="form-label fw-semibold">

                        Paid Date

                    </label>


                    <input
                    type="date"
                    name="paid_date"
                    class="form-control">

                </div>


                <!-- DESCRIPTION -->

                <div class="col-md-8 mb-3">

                    <label class="form-label fw-semibold">

                        Description

                    </label>


                    <input
                    type="text"
                    name="description"
                    class="form-control"
                    placeholder="Example: Semester fee, admission fee, exam fee">

                </div>


                <!-- SUBMIT -->

                <div class="col-md-4 mb-3 d-flex
                            align-items-end">

                    <button
                    type="submit"
                    name="add_fee"
                    class="btn btn-primary w-100">

                        <i class="fas fa-save me-2"></i>

                        Add Fee Record

                    </button>

                </div>


            </div>

        </form>

    </div>

</div>


<!-- =========================================
     FEE RECORDS
========================================= -->

<div class="card fee-card">


    <div class="card-header">

        <h5 class="mb-0">

            <i class="fas fa-table me-2"></i>

            All Fee Records

        </h5>

    </div>


    <div class="card-body">


        <div class="table-responsive">


            <table
            class="table table-hover
                   table-bordered align-middle">


                <thead class="table-primary">

                    <tr>

                        <th>#</th>

                        <th>Student</th>

                        <th>Amount</th>

                        <th>Due Date</th>

                        <th>Paid Date</th>

                        <th>Status</th>

                        <th>Description</th>

                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>


                <?php

                if (
                    $fees &&
                    mysqli_num_rows($fees) > 0
                ) {

                    $count = 1;


                    while (
                        $row =
                        mysqli_fetch_assoc($fees)
                    ) {

                ?>


                    <tr>


                        <!-- NUMBER -->

                        <td>

                            <?php
                            echo $count++;
                            ?>

                        </td>


                        <!-- STUDENT -->

                        <td>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $row['full_name']
                                    ?? 'Unknown Student'
                                );

                                ?>

                            </strong>

                        </td>


                        <!-- AMOUNT -->

                        <td>

                            <strong>

                                PKR

                                <?php

                                echo number_format(
                                    (float)$row['amount'],
                                    2
                                );

                                ?>

                            </strong>

                        </td>


                        <!-- DUE DATE -->

                        <td>

                            <?php

                            if (!empty($row['due_date'])) {

                                echo date(
                                    "d M Y",
                                    strtotime(
                                        $row['due_date']
                                    )
                                );

                            } else {

                                echo "-";

                            }

                            ?>

                        </td>


                        <!-- PAID DATE -->

                        <td>

                            <?php

                            if (!empty($row['paid_date'])) {

                                echo date(
                                    "d M Y",
                                    strtotime(
                                        $row['paid_date']
                                    )
                                );

                            } else {

                                echo "-";

                            }

                            ?>

                        </td>


                        <!-- STATUS -->

                        <td>


                            <?php

                            if (
                                $row['status']
                                == "paid"
                            ) {

                            ?>

                                <span
                                class="badge bg-success">

                                    <i
                                    class="fas fa-check
                                           me-1"></i>

                                    Paid

                                </span>


                            <?php

                            } elseif (
                                $row['status']
                                == "overdue"
                            ) {

                            ?>

                                <span
                                class="badge bg-danger">

                                    <i
                                    class="fas fa-exclamation-circle
                                           me-1"></i>

                                    Overdue

                                </span>


                            <?php

                            } else {

                            ?>

                                <span
                                class="badge bg-warning
                                       text-dark">

                                    <i
                                    class="fas fa-clock
                                           me-1"></i>

                                    Pending

                                </span>

                            <?php

                            }

                            ?>


                        </td>


                        <!-- DESCRIPTION -->

                        <td>

                            <?php

                            echo !empty(
                                $row['description']
                            )
                                ? htmlspecialchars(
                                    $row['description']
                                )
                                : "-";

                            ?>

                        </td>


                        <!-- ACTION -->

                        <td>

                            <a
                            href="?delete=<?php
                                echo $row['id'];
                            ?>"
                            class="btn btn-danger btn-sm"
                            onclick="return confirm(
                                'Are you sure you want to delete this fee record?'
                            );">

                                <i
                                class="fas fa-trash"></i>

                            </a>

                        </td>


                    </tr>


                <?php

                    }

                } else {

                ?>


                    <tr>

                        <td
                        colspan="8"
                        class="text-center
                               text-muted py-5">

                            <i
                            class="fas fa-file-invoice-dollar
                                   fa-2x mb-2"></i>

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


</div>


</body>

</html>