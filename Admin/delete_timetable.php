<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include("../config/database.php");

/*=========================================
    CHECK ID
=========================================*/

if (!isset($_GET['id'])) {

    header("Location: timetable.php");
    exit();

}

$id = (int)$_GET['id'];

/*=========================================
    DELETE RECORD
=========================================*/

$stmt = mysqli_prepare(

    $conn,

    "DELETE FROM timetable WHERE id=?"

);

mysqli_stmt_bind_param(

    $stmt,

    "i",

    $id

);

if (mysqli_stmt_execute($stmt)) {

    header("Location: timetable.php?success=deleted");
    exit();

} else {

    header("Location: timetable.php?error=delete_failed");
    exit();

}

?>
<div class="col-md-6 mb-3">

    <label class="form-label fw-bold">
        Class
    </label>

    <select
        name="class"
        class="form-select"
        required>

        <option <?=($row['class']=="BSIT 1st Semester")?"selected":"";?>>
            BSIT 1st Semester
        </option>

        <option <?=($row['class']=="BSIT 2nd Semester")?"selected":"";?>>
            BSIT 2nd Semester
        </option>

        <option <?=($row['class']=="BSIT 3rd Semester")?"selected":"";?>>
            BSIT 3rd Semester
        </option>

        <option <?=($row['class']=="BSIT 4th Semester")?"selected":"";?>>
            BSIT 4th Semester
        </option>

        <option <?=($row['class']=="BSIT 5th Semester")?"selected":"";?>>
            BSIT 5th Semester
        </option>

        <option <?=($row['class']=="BSIT 6th Semester")?"selected":"";?>>
            BSIT 6th Semester
        </option>

    </select>

</div>

<div class="col-md-6 mb-3">

    <label class="form-label fw-bold">
        Day
    </label>

    <select
        name="day"
        class="form-select"
        required>

        <option <?=($row['day']=="Monday")?"selected":"";?>>Monday</option>
        <option <?=($row['day']=="Tuesday")?"selected":"";?>>Tuesday</option>
        <option <?=($row['day']=="Wednesday")?"selected":"";?>>Wednesday</option>
        <option <?=($row['day']=="Thursday")?"selected":"";?>>Thursday</option>
        <option <?=($row['day']=="Friday")?"selected":"";?>>Friday</option>

    </select>

</div>

<div class="col-md-6 mb-3">

    <label class="form-label fw-bold">
        Time Slot
    </label>

    <select
        name="time_slot"
        class="form-select"
        required>

        <option <?=($row['time_slot']=="08:00 AM - 09:00 AM")?"selected":"";?>>
            08:00 AM - 09:00 AM
        </option>

        <option <?=($row['time_slot']=="09:00 AM - 10:00 AM")?"selected":"";?>>
            09:00 AM - 10:00 AM
        </option>

        <option <?=($row['time_slot']=="10:00 AM - 11:00 AM")?"selected":"";?>>
            10:00 AM - 11:00 AM
        </option>

        <option <?=($row['time_slot']=="11:00 AM - 12:00 PM")?"selected":"";?>>
            11:00 AM - 12:00 PM
        </option>

        <option <?=($row['time_slot']=="01:00 PM - 02:00 PM")?"selected":"";?>>
            01:00 PM - 02:00 PM
        </option>

        <option <?=($row['time_slot']=="02:00 PM - 03:00 PM")?"selected":"";?>>
            02:00 PM - 03:00 PM
        </option>

        <option <?=($row['time_slot']=="03:00 PM - 04:00 PM")?"selected":"";?>>
            03:00 PM - 04:00 PM
        </option>

    </select>

</div>

<div class="col-md-6 mb-3">

    <label class="form-label fw-bold">
        Subject
    </label>

    <input
        type="text"
        name="subject"
        class="form-control"
        value="<?php echo htmlspecialchars($row['subject']); ?>"
        required>

</div>

<div class="col-md-6 mb-3">

    <label class="form-label fw-bold">
        Teacher
    </label>

    <input
        type="text"
        name="teacher"
        class="form-control"
        value="<?php echo htmlspecialchars($row['teacher']); ?>"
        required>

</div>

<div class="col-12 mt-4">

    <button
        type="submit"
        name="update"
        class="btn btn-success">

        <i class="fas fa-save me-2"></i>

        Update Timetable

    </button>

    <a
        href="timetable.php"
        class="btn btn-secondary ms-2">

        <i class="fas fa-arrow-left me-2"></i>

        Back

    </a>

</div>

</div>

</form>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>