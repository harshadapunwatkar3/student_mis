<?php
session_start();
include("db.php");

if($_SESSION['user']['role'] != 'student'){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

/* FIRST NAME */
$nameParts = explode(" ", $_SESSION['user']['name']);
$firstName = ucfirst($nameParts[0]);

/* STUDENT INFO */
$student = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT * FROM students WHERE user_id='$user_id'"
));

/* TODAY DATE */
$today = date('Y-m-d');
$self_msg = "";
$self_err = "";

/* SELF ATTENDANCE */
/* SELF ATTENDANCE */
$self_msg = "";
$self_err = "";

if(isset($_POST['mark_attendance'])){

    $lat = mysqli_real_escape_string($conn, $_POST['latitude'] ?? '');
    $lon = mysqli_real_escape_string($conn, $_POST['longitude'] ?? '');

    if(empty($lat) || empty($lon)){
        $self_err = "Location not detected. Please allow GPS access.";
    } else {

        // Check if attendance already exists
        $check = mysqli_query($conn,"
            SELECT id, latitude, longitude 
            FROM attendance 
            WHERE student_id='$user_id' AND attendance_date='$today'
        ");

        if(mysqli_num_rows($check) > 0){

            $row = mysqli_fetch_assoc($check);

            // 🔥 UPDATE location if it was missing
            if(empty($row['latitude']) || empty($row['longitude'])){
                mysqli_query($conn,"
                    UPDATE attendance 
                    SET latitude='$lat', longitude='$lon'
                    WHERE id='{$row['id']}'
                ");
                $self_msg = "Attendance updated with location successfully!";
            } else {
                $self_err = "Attendance already marked for today";
            }

        } else {

            // Insert new attendance
            mysqli_query($conn,"
                INSERT INTO attendance 
                (student_id, attendance_date, status, latitude, longitude)
                VALUES 
                ('$user_id','$today','Present','$lat','$lon')
            ");

            $self_msg = "Attendance marked successfully with location!";
        }
    }
}


/* ATTENDANCE STATS */
$total = mysqli_num_rows(mysqli_query($conn,
    "SELECT id FROM attendance WHERE student_id='$user_id'"
));
$present = mysqli_num_rows(mysqli_query($conn,
    "SELECT id FROM attendance WHERE student_id='$user_id' AND status='Present'"
));
$absent = $total - $present;
$percentage = ($total > 0) ? round(($present/$total)*100, 2) : 0;
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Dashboard</title>
<style>
body{font-family:'Segoe UI',Arial;background:#eef1f5;}
.container{width:92%;margin:30px auto;}
.header{background:#0d6efd;color:#fff;padding:25px;border-radius:12px;margin-bottom:25px;}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;margin-bottom:30px;}
.card{background:#fff;padding:20px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.1);}
.btn{padding:12px 18px;background:#198754;color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:16px;}
.btn-red{background:#dc3545;}
.btn:disabled{background:#6c757d;cursor:not-allowed;}
.msg{background:#d4edda;color:#155724;padding:12px;border-radius:6px;margin-bottom:15px;border:1px solid #c3e6cb;}
.err{background:#f8d7da;color:#721c24;padding:12px;border-radius:6px;margin-bottom:15px;border:1px solid #f5c6cb;}
.profile{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:15px;margin-bottom:30px;}
.profile div{background:#fff;padding:16px;border-radius:10px;}
.actions a{display:inline-block;margin-right:12px;padding:12px 18px;background:#0d6efd;color:#fff;text-decoration:none;border-radius:8px;}
.actions a.logout{background:#dc3545;}
.location-status{margin-top:10px;padding:8px 12px;background:#e7f1ff;border-radius:6px;font-size:0.9em;color:#0d6efd;}
.location-status.error{background:#f8d7da;color:#721c24;}
</style>
</head>

<body>

<div class="container">

<div class="header">
    <h1>Hello, <?= $firstName ?> 👋</h1>
    <p>Student Dashboard</p>
</div>

<!-- SELF ATTENDANCE -->
<div class="card" style="margin-bottom:25px;">
<h3>Mark Today's Attendance (<?= date('d M Y') ?>)</h3>

<?php if($self_msg!=""){ ?><div class="msg"><?= $self_msg ?></div><?php } ?>
<?php if($self_err!=""){ ?><div class="err"><?= $self_err ?></div><?php } ?>

<form method="post" onsubmit="return checkLocation();">

    <input type="hidden" name="latitude" id="latitude">
    <input type="hidden" name="longitude" id="longitude">

    <button type="submit"
            name="mark_attendance"
            class="btn"
            id="markBtn"
            disabled>
        ⏳ Fetching location...
    </button>

    <div id="locationStatus" style="margin-top:10px;color:#0d6efd;">
        📍 Waiting for location...
    </div>

    <p style="margin-top:10px;font-size:0.9em;color:#666;">
        <strong>Note:</strong> Please allow location access when prompted.
    </p>

</form>
</div>

<!-- ATTENDANCE SUMMARY -->
<div class="cards">
    <div class="card"><h3><?= $total ?></h3>Total Classes</div>
    <div class="card"><h3><?= $present ?></h3>Present</div>
    <div class="card"><h3><?= $absent ?></h3>Absent</div>
    <div class="card"><h3><?= $percentage ?>%</h3>Attendance %</div>
</div>

<!-- STUDENT DETAILS -->
<h2>Student Details</h2>
<div class="profile">
    <div><b>Name</b><br><?= htmlspecialchars($_SESSION['user']['name']) ?></div>
    <div><b>Email</b><br><?= htmlspecialchars($_SESSION['user']['email']) ?></div>
    <div><b>Roll No</b><br><?= htmlspecialchars($student['roll_no']) ?></div>
    <div><b>Course</b><br><?= htmlspecialchars(ucfirst($student['course'])) ?></div>
    <div><b>Semester</b><br><?= htmlspecialchars($student['semester']) ?></div>
</div>

<!-- ACTIONS -->
<div class="actions">
    <a href="student_view_attendance.php" class="btn">View Attendance</a>
    <a href="change_password.php">Change Password</a>
    <a href="logout.php" class="logout">Logout</a>
</div>

</div>

<script>
let locationReady = false;

document.addEventListener("DOMContentLoaded", function () {

    const btn = document.getElementById("markBtn");
    const status = document.getElementById("locationStatus");

    if (!navigator.geolocation) {
        status.innerHTML = "❌ Geolocation not supported";
        return;
    }

    navigator.geolocation.getCurrentPosition(
        function (position) {
            document.getElementById("latitude").value = position.coords.latitude;
            document.getElementById("longitude").value = position.coords.longitude;

            locationReady = true;
            btn.disabled = false;
            btn.innerHTML = "✅ Mark Present";
            status.innerHTML = "📍 Location captured";
        },
        function (error) {
            status.innerHTML = "❌ Please allow location access";
            console.error(error);
        }
    );
});

function checkLocation(){
    if(!locationReady){
        alert("⏳ Please wait, fetching location...");
        return false;
    }
    return true;
}
</script>

</body>
</html>