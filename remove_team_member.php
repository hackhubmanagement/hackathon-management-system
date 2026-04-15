<?php
include "db.php";

$team_id = isset($_POST['team_id']) ? intval($_POST['team_id']) : 0;
$member_email = isset($_POST['member_email']) ? trim($_POST['member_email']) : '';
$leader_email = isset($_POST['leader_email']) ? trim($_POST['leader_email']) : '';

if (!$team_id || !$member_email || !$leader_email) {
    echo "Invalid request.";
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT leader_email FROM teams WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $team_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$team = mysqli_fetch_assoc($result);

if (!$team) {
    echo "Team not found.";
    exit();
}

if ($team['leader_email'] !== $leader_email) {
    echo "Only the team leader can remove members.";
    exit();
}

if ($member_email === $leader_email) {
    echo "Leader cannot remove themselves.";
    exit();
}

// Confirm member exists in the team
$stmt = mysqli_prepare($conn, "SELECT * FROM team_members WHERE team_id = ? AND email = ?");
mysqli_stmt_bind_param($stmt, "is", $team_id, $member_email);
mysqli_stmt_execute($stmt);
$memberResult = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($memberResult) === 0) {
    echo "Member not found in this team.";
    exit();
}

$stmt = mysqli_prepare($conn, "DELETE FROM team_members WHERE team_id = ? AND email = ?");
mysqli_stmt_bind_param($stmt, "is", $team_id, $member_email);
if (!mysqli_stmt_execute($stmt)) {
    echo "Failed to remove member.";
    exit();
}

$stmt = mysqli_prepare($conn, "UPDATE students SET team_id = NULL WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $member_email);
mysqli_stmt_execute($stmt);

echo "success";
?>