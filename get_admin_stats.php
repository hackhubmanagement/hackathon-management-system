<?php

include "db.php";

$students = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM students"));
$hackathons = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM hackathons"));
$teams = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM teams"));
$registrations = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM registrations"));
$submissions = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM submissions"));
$completed = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM submissions WHERE status='reviewed' OR score > 0"));

echo json_encode([
"students" => $students,
"hackathons" => $hackathons,
"teams" => $teams,
"registrations" => $registrations,
"submissions" => $submissions,
"completed" => $completed
]);

?>