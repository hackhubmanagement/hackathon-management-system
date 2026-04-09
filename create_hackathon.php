<?php

include "db.php";
include "log_system.php";

// ✅ GET DATA
$name = $_POST['name'] ?? '';
$theme = $_POST['theme'] ?? '';
$rules = $_POST['rules'] ?? '';
$teamSize = $_POST['teamSize'] ?? '';
$prize_pool = $_POST['prize_pool'] ?? '';
$regDeadline = $_POST['regDeadline'] ?? '';
$subDeadline = $_POST['subDeadline'] ?? '';

// ✅ VALIDATE EMPTY
if (!$name || !$theme || !$rules || !$teamSize || !$prize_pool || !$regDeadline || !$subDeadline) {
    echo "❌ All fields are required";
    exit();
}

// ✅ TODAY DATE
$today = date("Y-m-d");

// 🔴 VALIDATION 1: Registration must be future
if ($regDeadline < $today) {
    echo "❌ Registration deadline must be a future date";
    exit();
}

// 🔴 VALIDATION 2: Submission > Registration
if ($subDeadline <= $regDeadline) {
    echo "❌ Submission deadline must be after registration deadline";
    exit();
}

// 🔴 VALIDATION 3: Team size must be valid
if ($teamSize <= 0) {
    echo "❌ Invalid team size";
    exit();
}

// 🔴 VALIDATION 4: Prize pool must be valid
if ($prize_pool < 0) {
    echo "❌ Invalid prize pool";
    exit();
}

// ✅ PREPARED STATEMENT (SECURE 🔥)
$stmt = mysqli_prepare($conn, "
    INSERT INTO hackathons
    (name, theme, rules, team_size, prize_pool, registration_deadline, submission_deadline, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, 'active')
");

mysqli_stmt_bind_param(
    $stmt,
    "sssisss",
    $name,
    $theme,
    $rules,
    $teamSize,
    $prize_pool,
    $regDeadline,
    $subDeadline
);

// ✅ EXECUTE
if (mysqli_stmt_execute($stmt)) {
    addLog("Hackathon Created: " . $name);
    echo "success";
} else {
    echo "❌ DB Error: " . mysqli_error($conn);
}

?>