<?php
include 'db.php';

$user_id = $_POST['user_id'];

// Get current user
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id"));

$skills = $user['skills'];

// Find matching users
$query = "SELECT * FROM users 
          WHERE team_id IS NULL 
          AND id != $user_id 
          AND skills LIKE '%$skills%' 
          LIMIT 5";

$result = mysqli_query($conn, $query);

$matches = [];

while ($row = mysqli_fetch_assoc($result)) {
    $matches[] = $row;
}

echo json_encode($matches);
?>