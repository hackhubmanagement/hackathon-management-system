<?php
include "db.php";

$email = $_GET['email'];

$query = "
SELECT teams.* 
FROM team_members 
JOIN teams ON team_members.team_id = teams.id
WHERE team_members.email = '$email'
LIMIT 1
";

$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) > 0){
    $team = mysqli_fetch_assoc($result);
    echo json_encode($team);
} else {
    echo "none";
}
?>