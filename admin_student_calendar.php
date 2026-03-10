<?php
session_start();
include("db.php");

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

/* FETCH STUDENTS */
$students = mysqli_query($conn,"
    SELECT u.id, u.name, s.roll_no
    FROM users u
    INNER JOIN students s ON s.user_id = u.id
    WHERE u.role='student'
    ORDER BY s.roll_no
");

$attendance = null;
$present = 0;
$absent = 0;

if(isset($_GET['student_id'], $_GET['month'])){
    $student_id = $_GET['student_id'];
    $month = $_GET['month']; // YYYY-MM

    $attendance = mysqli_query($conn,"
        SELECT attendance_date, status
        FROM attendance
        WHERE student_id='$student_id'
        AND DATE_FORMAT(attendance_date,'%Y-%m')='$month'
        ORDER BY attendance_date
    ");

    $present = mysqli_num_rows(mysqli_query($conn,"
        SELECT id FROM attendance
        WHERE student_id='$student_id'
        AND status='Present'
        AND DATE_FORMAT(attendance_date,'%Y-%m')='$month'
    "));

    $absent = mysqli_num_rows(mysqli_query($conn,"
        SELECT id FROM attendance
        WHERE student_id='$student_id'
        AND status='Absent'
        AND DATE_FORMAT(attendance_date,'%Y-%m')='$month'
    "));
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Monthly Attendance | Admin</title>
<style>
body{font-family:'Segoe UI',Arial;background:#eef1f5;}
.container{width:94%;margin:25px auto;}
.card{background:#fff;padding:20px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.1);}
table{width:100%;border-collapse:collapse;margin-top:15px;}
th{background:#0d6efd;color:#fff;padding:12px;}
td{padding:10px;border-bottom:1px solid #ddd;text-align:center;}
.present{color:#198754;font-weight:bold;}
.absent{color:#dc3545;font-weight:bold;}
.btn{padding:10px 16px;background:#0d6efd;color:#fff;border:none;border-radius:8px;cursor:pointer;}
.summary{display:flex;gap:20px;margin-top:15px;}
.summary div{background:#f8f9fa;padding:12px;border-radius:8px;font-weight:600;}
.back{margin-bottom:15px;display:inline-block;text-decoration:none;color:#0d6efd;}
</style>
</head>

<body>

<div class="container">

<a href="admin_dashboard.php" class="back">← Back to Dashboard</a>

<div class="card">
<h2>📅 Student Monthly Attendance</h2>

<form method="get" style="margin-bottom:15px;">
    <label><strong>Student:</strong></label>
    <select name="student_id" required>
        <option value="">-- Select Student --</option>
        <?php while($s = mysqli_fetch_assoc($students)){ ?>
            <option value="<?= $s['id'] ?>">
                <?= $s['roll_no'] ?> - <?= $s['name'] ?>
            </option>
        <?php } ?>
    </select>

    <label style="margin-left:15px;"><strong>Month:</strong></label>
    <input type="month" name="month" required>

    <button type="submit" class="btn">View</button>
</form>

<?php if($attendance){ ?>

<div class="summary">
    <div>✅ Present Days: <?= $present ?></div>
    <div>❌ Absent Days: <?= $absent ?></div>
</div>

<table>
<tr>
    <th>Date</th>
    <th>Status</th>
</tr>

<?php if(mysqli_num_rows($attendance)==0){ ?>
<tr>
    <td colspan="2">No attendance records found</td>
</tr>
<?php } ?>

<?php while($row = mysqli_fetch_assoc($attendance)){ ?>
<tr>
    <td><?= date('d M Y', strtotime($row['attendance_date'])) ?></td>
    <td class="<?= strtolower($row['status']) ?>">
        <?= $row['status'] ?>
    </td>
</tr>
<?php } ?>

</table>

<?php } ?>

</div>
</div>

</body>
</html>
