<?php
session_start();
include("db.php");

// only logged-in users
if(!isset($_SESSION['user'])){
    header("Location: login.php");
}

$user_id = $_SESSION['user']['id'];
$role = $_SESSION['user']['role'];

$success = "";
$error = "";

if(isset($_POST['change'])){

    $old = $_POST['old_password'];
    $new = $_POST['new_password'];

    $check = mysqli_query($conn,
        "SELECT * FROM users WHERE id='$user_id' AND password='$old'"
    );

    if(mysqli_num_rows($check) == 1){

        mysqli_query($conn,
            "UPDATE users SET password='$new' WHERE id='$user_id'"
        );

        $success = "Password changed successfully";

    } else {
        $error = "Old password is incorrect";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Change Password</title>
<style>
body{
    margin:0;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:#eef1f5;
    font-family:'Segoe UI', Arial;
}
.card{
    background:#fff;
    width:380px;
    padding:30px;
    border-radius:12px;
    box-shadow:0 8px 20px rgba(0,0,0,0.15);
}
.card h2{
    margin-top:0;
    text-align:center;
    color:#0d6efd;
}
.form-group{
    margin-bottom:15px;
}
.form-group label{
    font-size:14px;
    color:#555;
}
.form-group input{
    width:100%;
    padding:10px;
    margin-top:6px;
    border:1px solid #ccc;
    border-radius:6px;
    font-size:14px;
}
.form-group input:focus{
    outline:none;
    border-color:#0d6efd;
}
.btn{
    width:100%;
    padding:12px;
    background:#0d6efd;
    color:#fff;
    border:none;
    border-radius:6px;
    font-size:15px;
    cursor:pointer;
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
    font-size:14px;
    text-align:center;
}
.error{
    background:#f8d7da;
    color:#721c24;
    padding:10px;
    border-radius:6px;
    margin-bottom:15px;
    font-size:14px;
    text-align:center;
}
.back{
    text-align:center;
    margin-top:15px;
}
.back a{
    text-decoration:none;
    color:#0d6efd;
    font-size:14px;
}
</style>
</head>

<body>

<div class="card">
    <h2>Change Password</h2>

    <?php if($success!=""){ ?>
        <div class="success"><?= $success ?></div>
    <?php } ?>

    <?php if($error!=""){ ?>
        <div class="error"><?= $error ?></div>
    <?php } ?>

    <form method="post">

        <div class="form-group">
            <label>Old Password</label>
            <input type="password" name="old_password" placeholder="Enter old password" required>
        </div>

        <div class="form-group">
            <label>New Password</label>
            <input type="password" name="new_password" placeholder="Enter new password" required>
        </div>

        <button type="submit" name="change" class="btn">Update Password</button>

    </form>

    <div class="back">
        <?php if($role=='admin'){ ?>
            <a href="admin_dashboard.php">← Back to Admin Dashboard</a>
        <?php } else { ?>
            <a href="student_dashboard.php">← Back to Student Dashboard</a>
        <?php } ?>
    </div>
</div>

</body>
</html>
