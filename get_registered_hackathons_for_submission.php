<?php

include "db.php";

// ✅ GET TEAM ID
$team_id = isset($_GET['team_id']) ? intval($_GET['team_id']) : 0;

$hackathons = [];

if ($team_id > 0) {

    $query = "
        SELECT h.*
        FROM hackathons h
        JOIN registrations r ON h.id = r.hackathon_id
        WHERE r.team_id = ?
        AND h.status = 'active'
        AND h.submission_deadline > NOW()
        ORDER BY h.id DESC
    ";

    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $team_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            $hackathons[] = $row;
        }
    } else {
        echo json_encode(["error" => "Query failed"]);
        exit();
    }
}

// ✅ RETURN JSON
echo json_encode($hackathons);

?>