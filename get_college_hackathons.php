<?php
include "db.php";

$status = isset($_GET['status']) ? $_GET['status'] : 'pending';

$query = "SELECT ch.*, s.name as student_name, s.email as student_email, s.college_name
          FROM college_hackathons ch
          JOIN students s ON ch.student_id = s.id
          WHERE ch.status = ?
          ORDER BY ch.submitted_at DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $status);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$hackathons = [];
while ($row = mysqli_fetch_assoc($result)) {
    $hackathons[] = $row;
}

echo json_encode($hackathons);
?>