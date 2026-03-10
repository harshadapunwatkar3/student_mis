<?php
session_start();
include("db.php");

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

$result = mysqli_query($conn,"
    SELECT 
        u.name,
        s.roll_no,
        a.attendance_date,
        a.status
    FROM attendance a
    INNER JOIN users u ON a.student_id = u.id
    INNER JOIN students s ON s.user_id = u.id
    ORDER BY a.attendance_date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>View Attendance | Admin</title>
<style>
body{font-family:'Segoe UI',Arial;background:#eef1f5;}
.container{width:95%;margin:30px auto;}
.header{background:#343a40;color:#fff;padding:20px;border-radius:12px;margin-bottom:20px;}
table{width:100%;background:#fff;border-collapse:collapse;border-radius:12px;overflow:hidden;}
th{background:#0d6efd;color:#fff;padding:12px;}
td{padding:10px;border-bottom:1px solid #ddd;}
.present{color:green;font-weight:bold;}
.absent{color:red;font-weight:bold;}
.back{display:inline-block;margin-bottom:15px;color:#0d6efd;text-decoration:none;font-weight:600;}
</style>
</head>

<body>
<div class="container">

<a href="admin_dashboard.php" class="back">← Back to Dashboard</a>

<div class="header">
    <h2>📊 All Attendance Records</h2>
</div>

<table>
<tr>
    <th>Date</th>
    <th>Roll No</th>
    <th>Name</th>
    <th>Status</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)){ ?>
<tr>
    <td><?= date('d M Y', strtotime($row['attendance_date'])) ?></td>
    <td><?= htmlspecialchars($row['roll_no']) ?></td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td class="<?= strtolower($row['status']) ?>">
        <?= $row['status'] ?>
    </td>
</tr>
<?php } ?>

</table>

</div>
</body>
</html>
