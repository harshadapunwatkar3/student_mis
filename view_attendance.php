<?php
include("db.php");

$data = mysqli_query($conn,"
SELECT users.name, users.email, students.roll_no, students.course, students.semester
FROM students
JOIN users ON students.user_id = users.id
");
?>

<!DOCTYPE html>
<html>
<head>
<title>View Students | Admin</title>
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
.header h2{
    margin:0;
}
.search-box{
    margin-bottom:15px;
}
.search-box input{
    width:300px;
    padding:8px;
    border-radius:6px;
    border:1px solid #ccc;
}
.table-card{
    background:#fff;
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
}
table{
    width:100%;
    border-collapse:collapse;
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
.back{
    margin-top:20px;
}
.back a{
    text-decoration:none;
    padding:10px 15px;
    background:#0d6efd;
    color:#fff;
    border-radius:6px;
    font-size:14px;
}
</style>

<script>
function searchStudent(){
    let input = document.getElementById("search").value.toLowerCase();
    let rows = document.querySelectorAll("tbody tr");

    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(input)
            ? ""
            : "none";
    });
}
</script>
</head>

<body>

<div class="container">

<!-- HEADER -->
<div class="header">
    <h2>Student List</h2>
    <span>Admin Panel</span>
</div>

<!-- SEARCH -->
<div class="search-box">
    <input type="text" id="search" onkeyup="searchStudent()" placeholder="Search by name, email, roll...">
</div>

<!-- TABLE -->
<div class="table-card">
<table>
<thead>
<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Roll No</th>
    <th>Course</th>
    <th>Semester</th>
</tr>
</thead>
<tbody>
<?php while($row=mysqli_fetch_assoc($data)){ ?>
<tr>
    <td><?= $row['name'] ?></td>
    <td><?= $row['email'] ?></td>
    <td><?= $row['roll_no'] ?></td>
    <td><?= ucfirst($row['course']) ?></td>
    <td><?= $row['semester'] ?></td>
</tr>
<?php } ?>
</tbody>
</table>
</div>

<!-- BACK -->
<div class="back">
    <a href="admin_dashboard.php">← Back to Admin Dashboard</a>
</div>

</div>

</body>
</html>
