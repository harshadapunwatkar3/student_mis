<?php
session_start();
include("db.php");
$today = date('Y-m-d');

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

/* DELETE ATTENDANCE */
/* DELETE ALL TODAY ATTENDANCE */
if(isset($_POST['delete_all_attendance'])){
    $today = date('Y-m-d');

    mysqli_query($conn,
        "DELETE FROM attendance WHERE attendance_date='$today'"
    );

    header("Location: admin_dashboard.php");
    exit;
}
/* DELETE SINGLE ATTENDANCE */
if(isset($_GET['delete_id'])){
    $delete_id = intval($_GET['delete_id']);

    mysqli_query($conn, "DELETE FROM attendance WHERE id='$delete_id'");

    header("Location: admin_dashboard.php");
    exit;
}

/* DASHBOARD STATS */
$total_students = mysqli_num_rows(
    mysqli_query($conn, "SELECT id FROM users WHERE role='student'")
);

$total_attendance = mysqli_num_rows(
    mysqli_query($conn, "SELECT id FROM attendance")
);

/* TODAY ATTENDANCE */
$today_attendance = mysqli_query($conn,"
    SELECT 
        a.id,
        s.roll_no,
        u.name,
        a.status,
        a.latitude,
        
        a.longitude
    FROM attendance a
    INNER JOIN users u ON a.student_id = u.id
    INNER JOIN students s ON s.user_id = u.id
    WHERE a.attendance_date = '$today'
      AND a.id IN (
          SELECT MAX(id)
          FROM attendance
          WHERE attendance_date = '$today'
          GROUP BY student_id
      )
    ORDER BY s.roll_no ASC
");



?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard | Student MIS</title>

<style>
body{margin:0;font-family:'Segoe UI',Arial;background:#eef1f5;}
.container{width:94%;margin:25px auto;}
.header{
    background:#343a40;color:#fff;padding:20px;border-radius:12px;
    display:flex;justify-content:space-between;align-items:center;
}
.cards{
    display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;margin:25px 0;
}
.card{
    background:#fff;padding:20px;border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
}
.card h3{margin:0;font-size:28px;color:#0d6efd;}
.section{
    background:#fff;padding:20px;border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
}
table{width:100%;border-collapse:collapse;margin-top:10px;}
th{background:#0d6efd;color:#fff;padding:12px;}
td{padding:10px;border-bottom:1px solid #ddd;}
.badge{padding:5px 10px;border-radius:12px;font-size:13px;}
.present{background:#d4edda;color:#155724;}
.map{
    background:#198754;color:#fff;padding:6px 10px;
    border-radius:6px;text-decoration:none;font-size:13px;
}
.menu{
    display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;margin-top:30px;
}
.menu a{
    background:#fff;padding:18px;text-decoration:none;border-radius:12px;
    color:#333;font-weight:600;
}
.logout{background:#dc3545;color:#fff;}
.footer{text-align:center;margin-top:30px;color:#777;}
.attendance-table {
    width: 100%;
    border-collapse: collapse;
}

.attendance-table th,
.attendance-table td {
    padding: 12px;
    text-align: center;
    vertical-align: middle;
}

.attendance-table tr:nth-child(even) {
    background: #f8f9fa;
}

.attendance-table tr:hover {
    background: #eef3ff;
}

.delete-btn {
    color: #dc3545;
    font-weight: bold;
    text-decoration: none;
}

</style>
</head>

<body>

<div class="container">

<!-- HEADER -->
<div class="header">
    <div>
        <h2>Admin Dashboard</h2>
        <span>Student Management Information System</span>
    </div>
    <a href="logout.php" style="color:#fff;">Logout</a>
</div>

<!-- STATS -->
<div class="cards">
    <div class="card">
        <h3><?= $total_students ?></h3>
        <p>Total Students</p>
    </div>
    <div class="card">
        <h3><?= $total_attendance ?></h3>
        <p>Total Attendance Records</p>
    </div>
</div>

<!-- TODAY ATTENDANCE -->
<div class="section">
<h3>📍 Attendance Today (<?= date('d M Y') ?>)</h3>
<form method="post" style="margin-bottom:15px;"
      onsubmit="return confirm('⚠ Are you sure you want to delete ALL today attendance?');">
    <button type="submit" name="delete_all_attendance"
            style="background:#dc3545;color:#fff;border:none;
                   padding:10px 16px;border-radius:8px;
                   cursor:pointer;font-weight:600;">
        🗑 Delete All Today Attendance
    </button>
</form>
<table class="attendance-table">
<tr>
    <th style="width:10%">Roll No</th>
    <th style="width:25%">Name</th>
    <th style="width:15%">Status</th>
    <th style="width:25%">Location</th>
    <th style="width:15%">Action</th>
</tr>

<?php if(mysqli_num_rows($today_attendance)==0){ ?>
<tr>
    <td colspan="4">No attendance marked today</td>
</tr>
<?php } ?>

<?php while($row = mysqli_fetch_assoc($today_attendance)){ ?>
<tr>
    <td><?= htmlspecialchars($row['roll_no']) ?></td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td>
        <span class="badge present"><?= $row['status'] ?></span>
    </td>
    <td>
        <?php if(!empty($row['latitude']) && !empty($row['longitude'])){ ?>
            <a class="map" target="_blank"
               href="https://www.google.com/maps?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>">
                View Map
            </a>
        <?php } else { ?>
            <span style="color:red;">Location not captured</span>
        <?php } ?>
    </td>
    <td>
        <a href="admin_dashboard.php?delete_id=<?= $row['id'] ?>"
   onclick="return confirm('Delete this attendance?')"
   class="delete-btn">
   🗑 Delete
</a>
    </td>
</tr>
<?php } ?>

</table>
</div>

<!-- MENU -->
<div class="menu">
    <a href="add_student.php">➕ Add Student</a>
    <a href="view_attendance.php">👥 View Students</a>
    <a href="mark_attendance.php">📝 Mark Attendance</a>
    <a href="admin_view_attendance.php">📊 View Attendance</a>
    <a href="change_password.php">🔐 Change Password</a>
    <a href="logout.php" class="logout">🚪 Logout</a>
    <a href="admin_student_calendar.php">📅 Monthly Attendance</a>

</div>

<div class="footer">
    © <?= date('Y') ?> Student Management Information System
</div>

</div>
</body>
</html>
