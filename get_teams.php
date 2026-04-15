<?php

include "db.php";

$availableOnly = isset($_GET['available']) && $_GET['available'] == '1';

if ($availableOnly) {
    $sql = "SELECT t.id, t.team_name, t.leader_email, COUNT(tm.id) AS member_count
            FROM teams t
            LEFT JOIN team_members tm ON t.id = tm.team_id
            GROUP BY t.id
            HAVING member_count < 4
            ORDER BY member_count ASC, t.team_name ASC";
} else {
    $sql = "SELECT * FROM teams";
}

$result = mysqli_query($conn, $sql);

$teams = [];

while($row = mysqli_fetch_assoc($result)){
    $teams[] = $row;
}

echo json_encode($teams);

?> 