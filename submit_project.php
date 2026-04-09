<?php
include "db.php";

header('Content-Type: application/json');

$team_id = $_POST['team_id'];
$team_name = $_POST['team_name'];
$hackathon_id = $_POST['hackathon_id'];
$project_name = $_POST['project_name'];
$description = $_POST['description'];
$github_repo = $_POST['github_repo'];
$domain = $_POST['domain'];
$demo_link = $_POST['demo_link'];

// 🔹 CHECK REGISTRATION
$check_reg_query = "SELECT * FROM registrations WHERE team_id=? AND hackathon_id=?";
$stmt_reg = mysqli_prepare($conn, $check_reg_query);
mysqli_stmt_bind_param($stmt_reg, "ii", $team_id, $hackathon_id);
mysqli_stmt_execute($stmt_reg);
$check_reg_result = mysqli_stmt_get_result($stmt_reg);

if (mysqli_num_rows($check_reg_result) == 0) {
    echo json_encode(["status" => "not_registered"]);
    exit();
}

// 🔹 CHECK SUBMISSION DEADLINE
$deadline_query = "SELECT submission_deadline FROM hackathons WHERE id=?";
$stmt_deadline = mysqli_prepare($conn, $deadline_query);
mysqli_stmt_bind_param($stmt_deadline, "i", $hackathon_id);
mysqli_stmt_execute($stmt_deadline);
$deadline_result = mysqli_stmt_get_result($stmt_deadline);

if (mysqli_num_rows($deadline_result) > 0) {
    $deadline_row = mysqli_fetch_assoc($deadline_result);
    if (strtotime($deadline_row['submission_deadline']) < time()) {
        echo json_encode(["status" => "deadline_passed"]);
        exit();
    }
}

// 🔹 INSERT PROJECT
$insert_query = "INSERT INTO submissions (team_id, team_name, hackathon_id, project_name, description, github_repo, domain, demo_link, status, score) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', 0)";
$stmt_insert = mysqli_prepare($conn, $insert_query);
mysqli_stmt_bind_param($stmt_insert, "isisssss", $team_id, $team_name, $hackathon_id, $project_name, $description, $github_repo, $domain, $demo_link);

if (mysqli_stmt_execute($stmt_insert)) {
    $status = "success";

    // 🔥 GET LEADER ID
    $leader_query = "SELECT leader_email FROM teams WHERE id=?";
    $stmt_leader = mysqli_prepare($conn, $leader_query);
    mysqli_stmt_bind_param($stmt_leader, "i", $team_id);
    mysqli_stmt_execute($stmt_leader);
    $leader_result = mysqli_stmt_get_result($stmt_leader);
    $row = mysqli_fetch_assoc($leader_result);
    $leaderEmail = $row['leader_email'];

    $user_query = "SELECT id FROM students WHERE email=?";
    $stmt_user = mysqli_prepare($conn, $user_query);
    mysqli_stmt_bind_param($stmt_user, "s", $leaderEmail);
    mysqli_stmt_execute($stmt_user);
    $user_result = mysqli_stmt_get_result($stmt_user);
    $userRow = mysqli_fetch_assoc($user_result);
    $leaderId = $userRow['id'];

    // 🔔 INSERT NOTIFICATION
    $title = "Project Submitted";
    $message = "Your project was submitted successfully.";

    $notif_query = "INSERT INTO notifications (user_id, title, message, is_read, created_at) VALUES (?, ?, ?, 0, NOW())";
    $stmt_notif = mysqli_prepare($conn, $notif_query);
    mysqli_stmt_bind_param($stmt_notif, "iss", $leaderId, $title, $message);
    mysqli_stmt_execute($stmt_notif);
} else {
    $status = "error";
}

echo json_encode([
    "status" => $status
]);
?>