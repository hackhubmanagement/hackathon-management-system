<?php

include "db.php";

$source = isset($_GET['source']) ? $_GET['source'] : 'all'; // 'all', 'admin', 'college'
$suggested = isset($_GET['suggested']) ? true : false;
$exclude_college = isset($_GET['exclude_college']) ? true : false;
$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : null;

if ($suggested && $student_id) {
    // Get user's preferred themes based on past registrations
    $theme_query = "SELECT h.theme, COUNT(*) as count
                    FROM registrations r
                    JOIN hackathons h ON r.hackathon_id = h.id
                    WHERE r.team_id IN (
                        SELECT team_id FROM students WHERE id = ?
                    )
                    GROUP BY h.theme
                    ORDER BY count DESC
                    LIMIT 3";

    $stmt = mysqli_prepare($conn, $theme_query);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $theme_result = mysqli_stmt_get_result($stmt);

    $preferred_themes = [];
    while ($row = mysqli_fetch_assoc($theme_result)) {
        $preferred_themes[] = $row['theme'];
    }

    if (!empty($preferred_themes)) {
        // Get college hackathons with preferred themes that user hasn't registered for yet,
        // ordered so the student's most common themes appear first.
        $placeholders = str_repeat('?,', count($preferred_themes) - 1) . '?';
        $sql = "SELECT *, CASE ";

        for ($i = 0; $i < count($preferred_themes); $i++) {
            $sql .= "WHEN LOWER(theme) = LOWER(?) THEN " . ($i + 1) . " ";
        }

        $sql .= "ELSE 999 END AS theme_rank
                FROM hackathons
                WHERE status='active'
                AND registration_deadline > NOW()
                AND source = 'college'
                AND LOWER(theme) IN (" . $placeholders . ")
                AND id NOT IN (
                    SELECT hackathon_id FROM registrations
                    WHERE team_id IN (SELECT team_id FROM students WHERE id = ?)
                )
                ORDER BY theme_rank ASC, registration_deadline ASC";

        $stmt = mysqli_prepare($conn, $sql);
        $params = array_merge($preferred_themes, $preferred_themes, [$student_id]);
        $types = str_repeat('s', count($preferred_themes) * 2) . 'i';
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    } else {
        // If no preferences, show recent college hackathons the student has not already registered for
        $sql = "SELECT * FROM hackathons WHERE status='active' AND registration_deadline > NOW() AND source = 'college' AND id NOT IN (
                    SELECT hackathon_id FROM registrations
                    WHERE team_id IN (SELECT team_id FROM students WHERE id = ?)
                ) ORDER BY registration_deadline ASC LIMIT 10";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $student_id);
    }
} else {
    // Regular hackathon filtering
    if ($source === 'admin') {
        $sql = "SELECT * FROM hackathons WHERE status='active' AND registration_deadline > NOW() AND (source = 'admin' OR source IS NULL)";
    } elseif ($source === 'college') {
        $sql = "SELECT * FROM hackathons WHERE status='active' AND registration_deadline > NOW() AND source = 'college'";
    } else {
        $sql = "SELECT * FROM hackathons WHERE status='active' AND registration_deadline > NOW()";
    }

    if ($exclude_college && $source !== 'college') {
        $sql .= " AND (source IS NULL OR source <> 'college')";
    }

    $sql .= " ORDER BY registration_deadline ASC";
    $stmt = mysqli_prepare($conn, $sql);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$hackathons = [];
while($row = mysqli_fetch_assoc($result)){
    $hackathons[] = $row;
}

echo json_encode($hackathons);

?>