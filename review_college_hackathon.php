<?php
include "db.php";
session_start();
header('Content-Type: application/json');

// Ensure approved college hackathons preserve college metadata
mysqli_query($conn, "ALTER TABLE hackathons ADD COLUMN IF NOT EXISTS college_name VARCHAR(150) DEFAULT NULL, ADD COLUMN IF NOT EXISTS location VARCHAR(150) DEFAULT NULL");
mysqli_query($conn, "ALTER TABLE college_hackathons ADD COLUMN IF NOT EXISTS location VARCHAR(150) DEFAULT NULL");

// Check if admin is logged in via session or email post data
$admin_id = null;
if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
} elseif (!empty($_POST['admin_id'])) {
    $admin_id = (int)$_POST['admin_id'];
} elseif (!empty($_POST['admin_email'])) {
    $admin_email = mysqli_real_escape_string($conn, $_POST['admin_email']);
    $query = "SELECT id FROM admins WHERE email = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $admin_email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $admin = mysqli_fetch_assoc($result);
    if ($admin) {
        $admin_id = $admin['id'];
    }
}

if (!$admin_id) {
    echo json_encode(['success' => false, 'message' => 'Admin access required. Please login again as admin.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hackathon_id = (int)$_POST['hackathon_id'];
    $action = $_POST['action']; // 'approve' or 'reject'
    $rejection_reason = isset($_POST['rejection_reason']) ? mysqli_real_escape_string($conn, $_POST['rejection_reason']) : '';

    if ($action === 'approve') {
        // Get college hackathon details
        $query = "SELECT * FROM college_hackathons WHERE id = ? AND status = 'pending'";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $hackathon_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $hackathon = mysqli_fetch_assoc($result);

        if (!$hackathon) {
            echo json_encode(['success' => false, 'message' => 'Hackathon not found or already processed']);
            exit();
        }

        // Insert into main hackathons table
        $insert_query = "INSERT INTO hackathons (name, theme, rules, team_size, registration_deadline, submission_deadline, status, prize_pool, source, college_name, location) VALUES (?, ?, ?, ?, ?, ?, 'active', ?, 'college', ?, ?)";
        $insert_stmt = mysqli_prepare($conn, $insert_query);
        if (!$insert_stmt) {
            echo json_encode(['success' => false, 'message' => 'Failed to prepare hackathon insert query']);
            exit();
        }
        mysqli_stmt_bind_param($insert_stmt, "sssisssss", $hackathon['name'], $hackathon['theme'], $hackathon['rules'], $hackathon['team_size'], $hackathon['registration_deadline'], $hackathon['submission_deadline'], $hackathon['prize_pool'], $hackathon['college_name'], $hackathon['location']);
        mysqli_stmt_execute($insert_stmt);

        // Update college hackathon status
        $update_query = "UPDATE college_hackathons SET status = 'approved', reviewed_at = NOW(), reviewed_by = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "ii", $admin_id, $hackathon_id);
        mysqli_stmt_execute($update_stmt);

        // Log the approval
        $log_message = "College Hackathon Approved: " . $hackathon['name'] . " (ID: " . $hackathon_id . ")";
        $log_query = "INSERT INTO system_logs (log_message) VALUES (?)";
        $log_stmt = mysqli_prepare($conn, $log_query);
        mysqli_stmt_bind_param($log_stmt, "s", $log_message);
        mysqli_stmt_execute($log_stmt);

        echo json_encode(['success' => true, 'message' => 'College hackathon approved and published successfully!']);

    } elseif ($action === 'reject') {
        if (empty($rejection_reason)) {
            echo json_encode(['success' => false, 'message' => 'Please provide a rejection reason']);
            exit();
        }

        // Update college hackathon status
        $update_query = "UPDATE college_hackathons SET status = 'rejected', reviewed_at = NOW(), reviewed_by = ?, rejection_reason = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "isi", $admin_id, $rejection_reason, $hackathon_id);
        mysqli_stmt_execute($update_stmt);

        // Log the rejection
        $log_message = "College Hackathon Rejected: ID " . $hackathon_id . " - " . $rejection_reason;
        $log_query = "INSERT INTO system_logs (log_message) VALUES (?)";
        $log_stmt = mysqli_prepare($conn, $log_query);
        mysqli_stmt_bind_param($log_stmt, "s", $log_message);
        mysqli_stmt_execute($log_stmt);

        echo json_encode(['success' => true, 'message' => 'College hackathon rejected successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>