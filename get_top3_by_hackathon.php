<?php
include "db.php";

$sql = "SELECT 
    h.id AS hackathon_id,
    h.name AS hackathon_name,
    h.prize_pool,
    s.team_name,
    s.project_name,
    s.score,
    COALESCE(st.college_name, 'N/A') AS college_name
FROM submissions s
JOIN hackathons h ON s.hackathon_id = h.id
JOIN teams t ON s.team_id = t.id
LEFT JOIN students st ON t.leader_email = st.email
WHERE s.status = 'reviewed'
ORDER BY h.id, s.score DESC";

$result = mysqli_query($conn, $sql);

$hackathons = [];

while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['hackathon_id'];
    if (!isset($hackathons[$id])) {
        $hackathons[$id] = [
            'hackathon_id' => $id,
            'hackathon_name' => $row['hackathon_name'],
            'prize_pool' => $row['prize_pool'],
            'winners' => []
        ];
    }

    if (count($hackathons[$id]['winners']) < 3) {
        $hackathons[$id]['winners'][] = [
            'team_name' => $row['team_name'],
            'project_name' => $row['project_name'],
            'score' => $row['score'],
            'college_name' => $row['college_name']
        ];
    }
}

echo json_encode(array_values($hackathons));
?>