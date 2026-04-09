<?php
include "db.php";

$team_name = $_POST['team_name'];
$email = $_POST['email'];

// create team
$query = "INSERT INTO teams (team_name, leader_email) VALUES (?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ss", $team_name, $email);
mysqli_stmt_execute($stmt);

$team_id = mysqli_insert_id($conn);

// notification (FIXED)
$title = "Team Created";
$message = "Your team $team_name was created successfully.";

$notif_query = "INSERT INTO notifications (user_id, title, message, is_read, created_at) VALUES (?, ?, ?, 0, NOW())";
$stmt_notif = mysqli_prepare($conn, $notif_query);
mysqli_stmt_bind_param($stmt_notif, "sss", $email, $title, $message);
mysqli_stmt_execute($stmt_notif);

// add leader to team
$member_query = "INSERT INTO team_members (team_id, email) VALUES (?, ?)";
$stmt_member = mysqli_prepare($conn, $member_query);
mysqli_stmt_bind_param($stmt_member, "is", $team_id, $email);
mysqli_stmt_execute($stmt_member);

// save team_id back to the student record
$update_student_query = "UPDATE students SET team_id = ? WHERE email = ?";
$stmt_update_student = mysqli_prepare($conn, $update_student_query);
mysqli_stmt_bind_param($stmt_update_student, "is", $team_id, $email);
mysqli_stmt_execute($stmt_update_student);

echo "success";
?>