<?php
include "db.php";

$sql = "
SELECT 
    submissions.team_name,
    submissions.project_name,
    submissions.score,
    students.college_name
FROM submissions
JOIN teams 
    ON submissions.team_id = teams.id
JOIN students 
    ON teams.leader_email = students.email

-- ✅ IMPORTANT FILTER
WHERE submissions.status = 'reviewed'

ORDER BY submissions.score DESC
";

$result = mysqli_query($conn, $sql);

$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        "team_name" => $row["team_name"],
        "project_name" => $row["project_name"],
        "score" => $row["score"],
        "college_name" => $row["college_name"]
    ];
}

echo json_encode($data);
?>