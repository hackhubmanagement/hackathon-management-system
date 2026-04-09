<?php
include "db.php";

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$department = isset($_POST['department']) ? trim($_POST['department']) : '';
$year = isset($_POST['year']) ? intval($_POST['year']) : 0;
$skills = isset($_POST['skills']) ? trim($_POST['skills']) : '';
$github = isset($_POST['github']) ? trim($_POST['github']) : '';

if (!$email) {
    echo "error: no email";
    exit();
}

$query = "UPDATE students SET department = ?, year = ?, skills = ?, github = ? WHERE email = ?";
$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    echo "error: prepare failed";
    exit();
}

mysqli_stmt_bind_param($stmt, "sisss", $department, $year, $skills, $github, $email);
$exec = mysqli_stmt_execute($stmt);

if ($exec) {
    echo "success";
} else {
    echo "error: execute failed " . mysqli_stmt_error($stmt);
}
