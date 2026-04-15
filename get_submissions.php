<?php

include "db.php";

$sql = "SELECT 
    s.*,
    GROUP_CONCAT(tm.email SEPARATOR ', ') AS team_members,
    COALESCE(st.college_name, 'N/A') AS college_name
FROM submissions s
LEFT JOIN team_members tm ON s.team_id = tm.team_id
LEFT JOIN teams t ON s.team_id = t.id
LEFT JOIN students st ON t.leader_email = st.email
GROUP BY s.id
ORDER BY s.submitted_at DESC";

$result = mysqli_query($conn,$sql);

$data = [];

while($row = mysqli_fetch_assoc($result)){
$data[] = $row;
}

echo json_encode($data);

?>