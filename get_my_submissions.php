<?php
include "db.php";

$team_id = $_GET['team_id'];

$result = mysqli_query($conn, "
SELECT s.*, h.name AS hackathon_name 
FROM submissions s
JOIN hackathons h ON s.hackathon_id = h.id
WHERE s.team_id='$team_id'
ORDER BY s.id DESC
");

$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode($data);
?>