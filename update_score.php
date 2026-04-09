<?php
include "db.php";

$id = $_POST['id'];
$score = $_POST['score'];

// ✅ VALIDATION
if ($score < 0 || $score > 100) {
    echo "Invalid score";
    exit();
}

// ✅ UPDATE SCORE + STATUS
$query = "UPDATE submissions 
          SET score='$score', status='reviewed' 
          WHERE id='$id'";

if (mysqli_query($conn, $query)) {

    // 🔥 GET TEAM LEADER
    $res = mysqli_query($conn, "
        SELECT teams.leader_email 
        FROM submissions 
        JOIN teams ON submissions.team_id = teams.id
        WHERE submissions.id='$id'
    ");

    $row = mysqli_fetch_assoc($res);
    $leaderEmail = $row['leader_email'];

    // 🔥 GET USER ID
    $userRes = mysqli_query($conn, "
        SELECT id FROM students WHERE email='$leaderEmail'
    ");

    $userRow = mysqli_fetch_assoc($userRes);
    $userId = $userRow['id'];

    // 🔔 SEND NOTIFICATION
    $title = "Project Reviewed";
    $message = "Your project has been evaluated. Score: $score";

    mysqli_query($conn, "
        INSERT INTO notifications (user_id, title, message, is_read, created_at)
        VALUES ('$userId', '$title', '$message', 0, NOW())
    ");

    echo "success";
} else {
    echo "error";
}
?>