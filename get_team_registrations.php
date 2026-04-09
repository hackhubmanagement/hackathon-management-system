<?php
include "db.php";

$team_id = isset($_GET['team_id']) ? intval($_GET['team_id']) : 0;

$registrations = [];
if ($team_id > 0) {
    $result = mysqli_query($conn, "SELECT hackathon_id FROM registrations WHERE team_id='$team_id'");
    while ($row = mysqli_fetch_assoc($result)) {
        $registrations[] = intval($row['hackathon_id']);
    }
}

echo json_encode($registrations);
?>