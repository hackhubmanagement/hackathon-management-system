<?php
include "db.php";

$team_id = $_GET['team_id'];

$query = "
SELECT students.name, students.email 
FROM team_members 
JOIN students 
ON team_members.email = students.email 
WHERE team_members.team_id = '$team_id'
";

$result = mysqli_query($conn, $query);

$members = [];

while($row = mysqli_fetch_assoc($result)){
    $members[] = $row;
}

echo json_encode($members);
?>