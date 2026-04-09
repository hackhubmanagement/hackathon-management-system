<?php
include "db.php";

$team_id = $_GET['team_id'];

/* ✅ 1. PARTICIPATED (REGISTERED) */
$participated = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM registrations WHERE team_id='$team_id'")
);

/* ✅ 2. COMPLETED (ONLY REVIEWED PROJECTS) */
$completed = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM submissions 
    WHERE team_id='$team_id' AND status='reviewed'")
);

/* ✅ 3. WON (REVIEWED + HIGH SCORE) */
$won = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM submissions 
    WHERE team_id='$team_id' AND status='reviewed' AND score >= 0")
);

/* ✅ 4. LOST (REVIEWED + LOW SCORE ONLY) */
$lost = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM submissions 
    WHERE team_id='$team_id' AND status='reviewed' AND score < 40")
);

/* ✅ 5. PENDING (SUBMITTED BUT NOT REVIEWED) */
$pending = mysqli_num_rows(
    mysqli_query($conn, "SELECT * FROM submissions 
    WHERE team_id='$team_id' AND status='pending'")
);

/* ✅ 6. LATE SUBMISSION (SUBMITTED AFTER DEADLINE) */
$late_submission = mysqli_num_rows(
    mysqli_query($conn, "SELECT s.* FROM submissions s 
    JOIN hackathons h ON s.hackathon_id = h.id 
    WHERE s.team_id='$team_id' AND s.submitted_at > h.submission_deadline")
);

echo json_encode([
    "participated" => $participated,
    "completed" => $completed,
    "won" => $won,
    "lost" => $lost,
    "pending" => $pending,
    "late_submission" => $late_submission
]);
?>