<?php
session_start();
include("db.php");

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'student'){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

$query = mysqli_query($conn, "
    SELECT 
        a.attendance_date,
        a.status,
        a.created_at,
        u.name
    FROM attendance a
    INNER JOIN users u ON a.student_id = u.id
    WHERE a.student_id = '$user_id'
    ORDER BY a.attendance_date DESC
");

?>

<!DOCTYPE html>
<html>
<head>
<title>My Attendance</title>
<style>
body{font-family:'Segoe UI',Arial;background:#eef1f5;}
.container{width:92%;margin:30px auto;}
.header{background:#0d6efd;color:#fff;padding:20px;border-radius:12px;margin-bottom:25px;}
table{width:100%;background:#fff;border-radius:12px;border-collapse:collapse;}
th{background:#0d6efd;color:#fff;padding:12px;text-align:left;}
td{padding:10px;border-bottom:1px solid #ddd;}
.present{color:#198754;font-weight:bold;}
.absent{color:#dc3545;font-weight:bold;}
.map{color:#0d6efd;text-decoration:none;}
.back{display:inline-block;margin-bottom:15px;text-decoration:none;color:#0d6efd;}
</style>
</head>

<body>

<div class="container">

<a href="student_dashboard.php" class="back">← Back to Dashboard</a>

<div class="header">
    <h2>📊 My Attendance</h2>
</div>

<table>
<tr>
    <th>Date</th>
    <th>Name</th>
    <th>Status</th>
    <th>Time</th>
</tr>

<?php if(mysqli_num_rows($query)==0){ ?>
<tr>
    <td colspan="4" style="text-align:center;">No attendance records found</td>
</tr>
<?php } ?>

<?php while($row = mysqli_fetch_assoc($query)){ ?>
<tr>
    <td><?= date('d M Y', strtotime($row['attendance_date'])) ?></td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td class="<?= strtolower($row['status']) ?>">
        <?= $row['status'] ?>
    </td>
    <td><?= date('h:i A', strtotime($row['created_at'])) ?></td>
</tr>
<?php } ?>
</table>
</div>
</body>
</html>
