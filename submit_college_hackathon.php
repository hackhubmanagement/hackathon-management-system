<?php
include "db.php";
session_start();
header('Content-Type: application/json');

// Ensure location column exists for submitted college hackathons
mysqli_query($conn, "ALTER TABLE college_hackathons ADD COLUMN IF NOT EXISTS location VARCHAR(150) DEFAULT NULL");

// Check if user is logged in via session or email/id post data
$student_id = null;
if (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];
} elseif (!empty($_POST['student_id'])) {
    $student_id = (int)$_POST['student_id'];
} elseif (!empty($_POST['student_email'])) {
    $student_email = mysqli_real_escape_string($conn, $_POST['student_email']);
    $query = "SELECT id FROM students WHERE email = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $student_email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $student = mysqli_fetch_assoc($result);
    if ($student) {
        $student_id = $student['id'];
    }
}

if (!$student_id) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $theme = mysqli_real_escape_string($conn, $_POST['theme']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $rules = mysqli_real_escape_string($conn, $_POST['rules']);
    $team_size = (int)$_POST['team_size'];
    $registration_deadline = $_POST['registration_deadline'];
    $submission_deadline = $_POST['submission_deadline'];
    $prize_pool = mysqli_real_escape_string($conn, $_POST['prize_pool']);
    $contact_email = mysqli_real_escape_string($conn, $_POST['contact_email']);
    $contact_phone = mysqli_real_escape_string($conn, $_POST['contact_phone']);
    $location = trim(mysqli_real_escape_string($conn, $_POST['location']));

    // Get student's college name
    $student_query = "SELECT college_name FROM students WHERE id = ?";
    $stmt = mysqli_prepare($conn, $student_query);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $student = mysqli_fetch_assoc($result);

    if (!$student) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        exit();
    }

    $college_name = $student['college_name'];

    // Validate required fields
    if (empty($name) || empty($theme) || empty($registration_deadline) || empty($submission_deadline) || empty($location)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields and include the college location']);
        exit();
    }

    // Validate dates
    $reg_deadline = strtotime($registration_deadline);
    $sub_deadline = strtotime($submission_deadline);
    $now = time();

    if ($reg_deadline <= $now) {
        echo json_encode(['success' => false, 'message' => 'Registration deadline must be in the future']);
        exit();
    }

    if ($sub_deadline <= $reg_deadline) {
        echo json_encode(['success' => false, 'message' => 'Submission deadline must be after registration deadline']);
        exit();
    }

    // Insert college hackathon
    $query = "INSERT INTO college_hackathons (student_id, name, theme, description, rules, team_size, registration_deadline, submission_deadline, prize_pool, college_name, contact_email, contact_phone, location) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "issssisssssss", $student_id, $name, $theme, $description, $rules, $team_size, $registration_deadline, $submission_deadline, $prize_pool, $college_name, $contact_email, $contact_phone, $location);

    if (mysqli_stmt_execute($stmt)) {
        // Log the submission
        $log_message = "College Hackathon Submitted: " . $name . " by student ID " . $student_id;
        $log_query = "INSERT INTO system_logs (log_message) VALUES (?)";
        $log_stmt = mysqli_prepare($conn, $log_query);
        mysqli_stmt_bind_param($log_stmt, "s", $log_message);
        mysqli_stmt_execute($log_stmt);

        echo json_encode(['success' => true, 'message' => 'College hackathon submitted successfully! It will be reviewed by administrators.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit hackathon. Please try again.']);
    }

    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>