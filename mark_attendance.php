<?php
session_start();
include("db.php");

if($_SESSION['user']['role'] != 'admin'){
    header("Location: login.php");
}

/* FETCH STUDENTS */
$students = mysqli_query($conn,
    "SELECT id, name FROM users WHERE role='student'"
);

/* SAVE ATTENDANCE */
$message = "";

if(isset($_POST['save'])){
    $date = $_POST['date'];

    foreach($_POST['status'] as $student_id => $status){
        mysqli_query($conn,
            "INSERT INTO attendance (student_id, attendance_date, status)
             VALUES ('$student_id','$date','$status')"
        );
    }

    $message = "Attendance saved successfully for $date";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Mark Attendance | Admin</title>
<style>
body{
    margin:0;
    font-family:'Segoe UI', Arial;
    background:#eef1f5;
}
.container{
    width:94%;
    margin:25px auto;
}
.header{
    background:#343a40;
    color:#fff;
    padding:20px;
    border-radius:12px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}
.card{
    background:#fff;
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
}
h2{
    margin-top:0;
}
.form-row{
    margin-bottom:15px;
}
.form-row label{
    font-weight:600;
}
.form-row input{
    padding:8px;
    margin-left:10px;
}
table{
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
}
th{
    background:#0d6efd;
    color:#fff;
    padding:12px;
    text-align:left;
}
td{
    padding:10px;
    border-bottom:1px solid #ddd;
}
tr:hover{
    background:#f1f3f5;
}
select{
    padding:6px;
    border-radius:6px;
}
.btn{
    padding:10px 16px;
    background:#0d6efd;
    color:#fff;
    border:none;
    border-radius:6px;
    cursor:pointer;
    font-size:14px;
}
.btn:hover{
    background:#0b5ed7;
}
.success{
    background:#d4edda;
    color:#155724;
    padding:10px;
    border-radius:6px;
    margin-bottom:15px;
}
.back{
    margin-top:20px;
}
.back a{
    text-decoration:none;
    color:#0d6efd;
    font-weight:600;
}
</style>
</head>

<body>

<div class="container">

<!-- HEADER -->
<div class="header">
    <h2>Mark Attendance</h2>
    <a href="admin_dashboard.php" style="color:#fff;text-decoration:none;">Dashboard</a>
</div>

<div class="card">

<?php if($message!=""){ ?>
    <div class="success"><?= $message ?></div>
<?php } ?>

<form method="post">

<div class="form-row">
    <label>Select Date:</label>
    <input type="date" name="date" required>
</div>

<table>
<tr>
    <th>Student Name</th>
    <th>Status</th>
</tr>

<?php while($s = mysqli_fetch_assoc($students)){ ?>
<tr>
    <td><?= $s['name'] ?></td>
    <td>
        <select name="status[<?= $s['id'] ?>]">
            <option value="Present">Present</option>
            <option value="Absent">Absent</option>
        </select>
    </td>
</tr>
<?php } ?>

</table>

<br>
<button type="submit" name="save" class="btn">Save Attendance</button>

</form>

<div class="back">
    <a href="admin_dashboard.php">← Back to Admin Dashboard</a>
</div>

</div>

</div>

</body>
</html>
