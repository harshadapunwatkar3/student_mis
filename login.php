<?php
session_start();
include("db.php");

$error = "";

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    // ✅ DO NOT CHECK ROLE HERE
    $query = mysqli_query($conn, "
        SELECT * FROM users 
        WHERE email='$email' AND password='$password'
        LIMIT 1
    ");

    if (!$query) {
        die("Login query failed: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($query) === 1) {

        $user = mysqli_fetch_assoc($query);
        $_SESSION['user'] = $user;

        // ✅ REDIRECT BASED ON DB ROLE
        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: student_dashboard.php");
        }
        exit;

    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login | Student MIS</title>
<style>
body{
    margin:0;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#0d6efd,#6f42c1);
    font-family:'Segoe UI', Arial;
}
.login-box{
    background:#fff;
    width:360px;
    padding:30px;
    border-radius:12px;
    box-shadow:0 10px 25px rgba(0,0,0,0.2);
}
.login-box h2{
    text-align:center;
    margin-bottom:10px;
    color:#0d6efd;
}
.login-box p{
    text-align:center;
    margin-bottom:25px;
    color:#666;
    font-size:14px;
}
.input-group{
    margin-bottom:18px;
}
.input-group label{
    font-size:14px;
    color:#555;
}
.input-group input{
    width:100%;
    padding:10px;
    margin-top:6px;
    border:1px solid #ccc;
    border-radius:6px;
    font-size:14px;
}
.input-group input:focus{
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
.error{
    background:#f8d7da;
    color:#842029;
    padding:10px;
    border-radius:6px;
    margin-bottom:15px;
    font-size:14px;
    text-align:center;
}
.footer{
    text-align:center;
    margin-top:20px;
    font-size:13px;
    color:#777;
}
</style>
</head>

<body>

<div class="login-box">

    <h2>Student MIS</h2>
<p>Login to your account</p>

<!-- <p>Please select role to login</p> -->
    <?php if($error!=""){ ?>
        <div class="error"><?= $error ?></div>
    <?php } ?>

    <form method="post">

    <!-- <div class="input-group">
        <label>Login As</label>
        <select name="role" required style="width:100%;padding:10px;border-radius:6px;">
            <option value="">-- Select Role --</option>
            <option value="student">Student</option>
            <option value="admin">Admin</option>
        </select>
    </div> -->

    <div class="input-group">
        <label>Email Address</label>
        <input type="email" name="email" placeholder="Enter your email" required>
    </div>

    <div class="input-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Enter your password" required>
    </div>

    <button type="submit" name="login" class="btn">Login</button>

</form>


    <div class="footer">
        © <?= date('Y') ?> Student Management Information System
    </div>

</div>

</body>
</html>
