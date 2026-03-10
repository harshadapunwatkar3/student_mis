<?php
include("db.php");

$message = "";
$error = "";

if(isset($_POST['add'])){

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $roll = trim($_POST['roll']);
    $course = trim($_POST['course']);
    $semester = trim($_POST['semester']);

    // Default password for student
    $password = "student123";

    // check duplicate email
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if(mysqli_num_rows($check) > 0){
        $error = "Email already exists";
    } else {

        mysqli_query($conn,
            "INSERT INTO users (name,email,password,role)
             VALUES ('$name','$email','$password','student')"
        );

        $user_id = mysqli_insert_id($conn);

        mysqli_query($conn,
            "INSERT INTO students (user_id,roll_no,course,semester)
             VALUES ('$user_id','$roll','$course','$semester')"
        );

        $message = "Student added successfully! 
                    Login Email: $email | Password: $password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Student | Admin</title>
<style>
body{
    margin:0;
    font-family:'Segoe UI', Arial;
    background:#eef1f5;
}
.container{
    width:100%;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}
.card{
    background:#fff;
    width:420px;
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
    display:block;
    font-size:14px;
    margin-bottom:6px;
    color:#555;
}
.form-group input{
    width:100%;
    padding:10px;
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
}
.error{
    background:#f8d7da;
    color:#721c24;
    padding:10px;
    border-radius:6px;
    margin-bottom:15px;
    font-size:14px;
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

<div class="container">

<div class="card">
    <h2>Add New Student</h2>

    <?php if($message!=""){ ?>
        <div class="success"><?= $message ?></div>
    <?php } ?>

    <?php if($error!=""){ ?>
        <div class="error"><?= $error ?></div>
    <?php } ?>

    <form method="post">

        <div class="form-group">
            <label>Student Name</label>
            <input type="text" name="name" placeholder="Enter full name" required>
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="Enter email" required>
        </div>

        <div class="form-group">
            <label>Roll Number</label>
            <input type="text" name="roll" placeholder="Enter roll number" required>
        </div>

        <div class="form-group">
            <label>Course</label>
            <input type="text" name="course" placeholder="Enter course" required>
        </div>

        <div class="form-group">
            <label>Semester</label>
            <input type="text" name="semester" placeholder="Enter semester" required>
        </div>

        <button type="submit" name="add" class="btn">Add Student</button>
    </form>

    <div class="back">
        <a href="admin_dashboard.php">← Back to Admin Dashboard</a>
    </div>
</div>

</div>

</body>
</html>
