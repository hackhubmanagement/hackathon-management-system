<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

// ✅ DATABASE CONNECTION (your DB name is correct)
$conn = mysqli_connect("127.0.0.1", "root", "", "hackathon-db", 3307);

if (!$conn) {
    echo json_encode([
        "type" => "error",
        "message" => "Database connection failed: " . mysqli_connect_error()
    ]);
    exit();
}

// 🔴 CHECK INPUT
if (!isset($_POST['email']) || !isset($_POST['password'])) {
    echo json_encode([
        "type" => "error",
        "message" => "Email or Password missing"
    ]);
    exit();
}

$email = mysqli_real_escape_string($conn, trim($_POST['email']));
$password = trim($_POST['password']);


// ===================================================
// 🔹 ADMIN LOGIN
// ===================================================
$query_admin = "SELECT * FROM admins WHERE email=?";
$stmt_admin = mysqli_prepare($conn, $query_admin);
mysqli_stmt_bind_param($stmt_admin, "s", $email);
mysqli_stmt_execute($stmt_admin);
$result_admin = mysqli_stmt_get_result($stmt_admin);

if ($result_admin && mysqli_num_rows($result_admin) > 0) {
    $admin = mysqli_fetch_assoc($result_admin);

    if (password_verify($password, $admin['password'])) {
        echo json_encode([
            "type" => "admin",
            "id" => $admin['id'],
            "name" => $admin['name'],
            "email" => $admin['email'],
            "role" => "Admin"
        ]);
        exit();
    }
}


// ===================================================
// 🔹 STUDENT LOGIN
// ===================================================
$query_student = "SELECT * FROM students WHERE email=?";
$stmt_student = mysqli_prepare($conn, $query_student);
mysqli_stmt_bind_param($stmt_student, "s", $email);
mysqli_stmt_execute($stmt_student);
$result_student = mysqli_stmt_get_result($stmt_student);

if ($result_student && mysqli_num_rows($result_student) > 0) {
    $student = mysqli_fetch_assoc($result_student);

    if (password_verify($password, $student['password'])) {
        echo json_encode([
            "type" => "student",
            "id" => $student['id'],
            "name" => $student['name'],
            "email" => $student['email'],
            "student_id" => $student['student_id'],
            "college_name" => $student['college_name'], // 🔥 IMPORTANT FIX
            "department" => $student['department'],
            "year" => $student['year'],
            "skills" => $student['skills'],
            "github" => $student['github']
        ]);
        exit();
    }
}


// ===================================================
// 🔹 JUDGE LOGIN
// ===================================================
$query_judge = "SELECT * FROM judges WHERE email=?";
$stmt_judge = mysqli_prepare($conn, $query_judge);
mysqli_stmt_bind_param($stmt_judge, "s", $email);
mysqli_stmt_execute($stmt_judge);
$result_judge = mysqli_stmt_get_result($stmt_judge);

if ($result_judge && mysqli_num_rows($result_judge) > 0) {
    $judge = mysqli_fetch_assoc($result_judge);

    if (password_verify($password, $judge['password'])) {
        echo json_encode([
            "type" => "judge",
            "id" => $judge['id'],
            "name" => $judge['name'],
            "email" => $judge['email'],
            "expertise" => $judge['expertise'],
            "role" => "Judge"
        ]);
        exit();
    }
}


// ===================================================
// 🔹 INVALID LOGIN
// ===================================================
echo json_encode([
    "type" => "invalid",
    "message" => "Invalid email or password"
]);

?>