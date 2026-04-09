<?php

include "db.php";

// 🔴 GET DATA (NO student_id here)
$name = $_POST['name'] ?? '';
$college_name = $_POST['college_name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$department = $_POST['department'] ?? '';
$year = $_POST['year'] ?? '';


// 🔴 VALIDATION
if (
    empty($name) || empty($college_name) ||
    empty($email) || empty($password) ||
    empty($department) || empty($year)
) {
    echo "All fields are required";
    exit();
}


// ===================================================
// 🔹 CHECK EMAIL EXISTS
// ===================================================
$checkQuery = "SELECT * FROM students WHERE email = ?";
$stmt = mysqli_prepare($conn, $checkQuery);

if (!$stmt) {
    die("Prepare failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    echo "email_exists";
    exit();
}


// ===================================================
// 🔥 GENERATE UNIQUE STUDENT ID
// ===================================================
function generateStudentId($conn) {
    do {
        $id = rand(100001, 999999);

        $check = mysqli_query($conn, "SELECT * FROM students WHERE student_id='$id'");
    } while(mysqli_num_rows($check) > 0);

    return $id;
}

$student_id = generateStudentId($conn);


// ===================================================
// 🔹 INSERT STUDENT
// ===================================================

// 🔐 HASH PASSWORD
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$insertQuery = "INSERT INTO students 
(name, email, password, student_id, college_name, department, year) 
VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $insertQuery);

if (!$stmt) {
    die("Insert prepare failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param(
    $stmt,
    "sssssss",
    $name,
    $email,
    $hashedPassword,
    $student_id,
    $college_name,
    $department,
    $year
);

if (mysqli_stmt_execute($stmt)) {
    echo "success";
} else {
    echo "Insert failed: " . mysqli_error($conn);
}

?>