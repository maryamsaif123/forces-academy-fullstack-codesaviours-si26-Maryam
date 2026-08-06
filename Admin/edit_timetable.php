<?php

session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

include("../config/database.php");

if(!isset($_GET['id'])){
    header("Location: timetable.php");
    exit();
}

$id = (int)$_GET['id'];

/*=========================================
    FETCH RECORD
=========================================*/

$stmt = mysqli_prepare(

$conn,

"SELECT *
FROM timetable
WHERE id=?
LIMIT 1"

);

mysqli_stmt_bind_param($stmt,"i",$id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$timetable = mysqli_fetch_assoc($result);

if(!$timetable){

header("Location: timetable.php");

exit();

}

/*=========================================
    UPDATE
=========================================*/

if(isset($_POST['update'])){

$education_level = $_POST['education_level'];

$class_name = $_POST['class_name'];

$section = $_POST['section'];

$day = $_POST['day'];

$period_no = $_POST['period_no'];

$start_time = $_POST['start_time'];

$end_time = $_POST['end_time'];

$subject = $_POST['subject'];

$teacher = $_POST['teacher'];

$room_no = $_POST['room_no'];

$update = mysqli_prepare(

$conn,

"UPDATE timetable SET

education_level=?,
class_name=?,
section=?,
day=?,
period_no=?,
start_time=?,
end_time=?,
subject=?,
teacher=?,
room_no=?

WHERE id=?"

);

mysqli_stmt_bind_param(

$update,

"ssssisssssi",

$education_level,
$class_name,
$section,
$day,
$period_no,
$start_time,
$end_time,
$subject,
$teacher,
$room_no,
$id

);

mysqli_stmt_execute($update);

header("Location:timetable.php");

exit();

}

?>
<!-- Education Level -->

<div class="col-md-4 mb-3">

<label class="form-label fw-bold">Education Level</label>

<select name="education_level" class="form-select" required>

<option value="School" <?php if($timetable['education_level']=="School") echo "selected"; ?>>School</option>

<option value="University" <?php if($timetable['education_level']=="University") echo "selected"; ?>>University</option>

</select>

</div>

<!-- Class -->

<div class="col-md-4 mb-3">

<label class="form-label fw-bold">Class / Semester</label>

<input
type="text"
name="class_name"
class="form-control"
value="<?php echo htmlspecialchars($timetable['class_name']); ?>"
required>

</div>

<!-- Section -->

<div class="col-md-4 mb-3">

<label class="form-label fw-bold">Section</label>

<input
type="text"
name="section"
class="form-control"
value="<?php echo htmlspecialchars($timetable['section']); ?>">

</div>

<!-- Day -->

<div class="col-md-3 mb-3">

<label class="form-label fw-bold">Day</label>

<select name="day" class="form-select">

<option value="Monday" <?php if($timetable['day']=="Monday") echo "selected"; ?>>Monday</option>

<option value="Tuesday" <?php if($timetable['day']=="Tuesday") echo "selected"; ?>>Tuesday</option>

<option value="Wednesday" <?php if($timetable['day']=="Wednesday") echo "selected"; ?>>Wednesday</option>

<option value="Thursday" <?php if($timetable['day']=="Thursday") echo "selected"; ?>>Thursday</option>

<option value="Friday" <?php if($timetable['day']=="Friday") echo "selected"; ?>>Friday</option>

<option value="Saturday" <?php if($timetable['day']=="Saturday") echo "selected"; ?>>Saturday</option>

</select>

</div>

<!-- Period -->

<div class="col-md-3 mb-3">

<label class="form-label fw-bold">Period No</label>

<input
type="number"
name="period_no"
class="form-control"
value="<?php echo $timetable['period_no']; ?>"
required>

</div>

<!-- Start Time -->

<div class="col-md-3 mb-3">

<label class="form-label fw-bold">Start Time</label>

<input
type="time"
name="start_time"
class="form-control"
value="<?php echo $timetable['start_time']; ?>"
required>

</div>

<!-- End Time -->

<div class="col-md-3 mb-3">

<label class="form-label fw-bold">End Time</label>

<input
type="time"
name="end_time"
class="form-control"
value="<?php echo $timetable['end_time']; ?>"
required>

</div>

<!-- Subject -->

<div class="col-md-4 mb-3">

<label class="form-label fw-bold">Subject</label>

<input
type="text"
name="subject"
class="form-control"
value="<?php echo htmlspecialchars($timetable['subject']); ?>"
required>

</div>

<!-- Teacher -->

<div class="col-md-4 mb-3">

<label class="form-label fw-bold">Teacher</label>

<input
type="text"
name="teacher"
class="form-control"
value="<?php echo htmlspecialchars($timetable['teacher']); ?>"
required>

</div>

<!-- Room -->

<div class="col-md-4 mb-3">

<label class="form-label fw-bold">Room No</label>

<input
type="text"
name="room_no"
class="form-control"
value="<?php echo htmlspecialchars($timetable['room_no']); ?>">

</div>

<div class="col-12 mt-3">

<button
type="submit"
name="update"
class="btn btn-success">

<i class="fas fa-save me-2"></i>

Update Timetable

</button>

<a
href="timetable.php"
class="btn btn-secondary">

<i class="fas fa-arrow-left me-2"></i>

Back

</a>

</div>

</div>

</form>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>