<?php

include "db.php";

$team_id = $_POST['team_id'];
$email = $_POST['email'];

// ✅ 1. CHECK IF USER ALREADY IN TEAM
$check_query = "SELECT * FROM team_members WHERE team_id=? AND email=?";
$stmt_check = mysqli_prepare($conn, $check_query);
mysqli_stmt_bind_param($stmt_check, "is", $team_id, $email);
mysqli_stmt_execute($stmt_check);
$check_result = mysqli_stmt_get_result($stmt_check);

if (mysqli_num_rows($check_result) > 0) {
    echo "already_joined";
    exit();
}

// ✅ 2. CHECK TEAM SIZE (MAX 4)
$count_query = "SELECT COUNT(*) as total FROM team_members WHERE team_id=?";
$stmt_count = mysqli_prepare($conn, $count_query);
mysqli_stmt_bind_param($stmt_count, "i", $team_id);
mysqli_stmt_execute($stmt_count);
$count_result = mysqli_stmt_get_result($stmt_count);
$count_data = mysqli_fetch_assoc($count_result);

if ($count_data['total'] >= 4) {
    echo "team_full";
    exit();
}

// ✅ 3. INSERT MEMBER INTO TEAM
$insert_query = "INSERT INTO team_members (team_id, email) VALUES (?, ?)";
$stmt_insert = mysqli_prepare($conn, $insert_query);
mysqli_stmt_bind_param($stmt_insert, "is", $team_id, $email);

if (mysqli_stmt_execute($stmt_insert)) {
        // save team_id to joining student record
        $update_student_query = "UPDATE students SET team_id = ? WHERE email = ?";
        $stmt_update_student = mysqli_prepare($conn, $update_student_query);
        mysqli_stmt_bind_param($stmt_update_student, "is", $team_id, $email);
        mysqli_stmt_execute($stmt_update_student);

    $leader_query = "SELECT leader_email FROM teams WHERE id=?";
    $stmt_leader = mysqli_prepare($conn, $leader_query);
    mysqli_stmt_bind_param($stmt_leader, "i", $team_id);
    mysqli_stmt_execute($stmt_leader);
    $leader_result = mysqli_stmt_get_result($stmt_leader);
    $leaderData = mysqli_fetch_assoc($leader_result);
    $leaderEmail = $leaderData['leader_email'];

    // 🔥 5. GET LEADER ID FROM STUDENTS TABLE (IMPORTANT FIX)
    $user_query = "SELECT id, name FROM students WHERE email=?";
    $stmt_user = mysqli_prepare($conn, $user_query);
    mysqli_stmt_bind_param($stmt_user, "s", $leaderEmail);
    mysqli_stmt_execute($stmt_user);
    $user_result = mysqli_stmt_get_result($stmt_user);
    $userData = mysqli_fetch_assoc($user_result);
    $leaderId = $userData['id'];

    // 🔥 6. GET JOINING STUDENT NAME (BETTER UI)
    $student_query = "SELECT name FROM students WHERE email=?";
    $stmt_student = mysqli_prepare($conn, $student_query);
    mysqli_stmt_bind_param($stmt_student, "s", $email);
    mysqli_stmt_execute($stmt_student);
    $student_result = mysqli_stmt_get_result($stmt_student);
    $studentData = mysqli_fetch_assoc($student_result);
    $studentName = $studentData['name'];

    // 🔥 7. INSERT NOTIFICATION
    $title = "New Team Member Joined";
    $message = "$studentName has joined your team.";

    $notif_query = "INSERT INTO notifications (user_id, title, message, is_read, created_at) VALUES (?, ?, ?, 0, NOW())";
    $stmt_notif = mysqli_prepare($conn, $notif_query);
    mysqli_stmt_bind_param($stmt_notif, "iss", $leaderId, $title, $message);
    mysqli_stmt_execute($stmt_notif);

    echo "success";
} else {
    echo "error";
}

?>