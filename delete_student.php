<?php

include "db.php";
include "log_system.php";

$id = intval($_POST["id"]);

$result = mysqli_query($conn, "SELECT email FROM students WHERE id='$id'");
if (!$result || mysqli_num_rows($result) === 0) {
    echo "error";
    exit;
}

$row = mysqli_fetch_assoc($result);
$email = $row['email'];

// Delete any messages sent by the student
mysqli_query($conn, "DELETE FROM messages WHERE sender_email='$email'");

// Delete student notifications
mysqli_query($conn, "DELETE FROM notifications WHERE user_id='$id'");

// Remove the student from any team memberships
mysqli_query($conn, "DELETE FROM team_members WHERE email='$email'");

// Remove any teams owned by the student and all related records
$leaderTeams = [];
$teamResult = mysqli_query($conn, "SELECT id FROM teams WHERE leader_email='$email'");
while ($teamRow = mysqli_fetch_assoc($teamResult)) {
    $leaderTeams[] = intval($teamRow['id']);
}

if (count($leaderTeams) > 0) {
    $teamIds = implode(',', $leaderTeams);
    mysqli_query($conn, "DELETE FROM registrations WHERE team_id IN ($teamIds)");
    mysqli_query($conn, "DELETE FROM messages WHERE team_id IN ($teamIds)");
    mysqli_query($conn, "DELETE FROM submissions WHERE team_id IN ($teamIds)");
    mysqli_query($conn, "DELETE FROM team_members WHERE team_id IN ($teamIds)");
    mysqli_query($conn, "DELETE FROM teams WHERE id IN ($teamIds)");
}

// Clean up any orphan teams that are left with no members
$orphanTeams = [];
$orphanResult = mysqli_query($conn, "SELECT t.id FROM teams t LEFT JOIN team_members tm ON t.id = tm.team_id WHERE tm.id IS NULL");
while ($orphanRow = mysqli_fetch_assoc($orphanResult)) {
    $orphanTeams[] = intval($orphanRow['id']);
}

if (count($orphanTeams) > 0) {
    $orphanIds = implode(',', $orphanTeams);
    mysqli_query($conn, "DELETE FROM registrations WHERE team_id IN ($orphanIds)");
    mysqli_query($conn, "DELETE FROM messages WHERE team_id IN ($orphanIds)");
    mysqli_query($conn, "DELETE FROM submissions WHERE team_id IN ($orphanIds)");
    mysqli_query($conn, "DELETE FROM team_members WHERE team_id IN ($orphanIds)");
    mysqli_query($conn, "DELETE FROM teams WHERE id IN ($orphanIds)");
}

// Finally delete the student record
mysqli_query($conn, "DELETE FROM students WHERE id='$id'");

addLog("Student Deleted: " . $email);

echo "success";

?>