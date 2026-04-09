<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "hackathon-db",3307);

if ($conn->connect_error) {
    echo json_encode([]);
    exit;
}

$team_id = $_GET['team_id'];

$sql = "SELECT m.*, s.name as sender_name FROM messages m
        LEFT JOIN students s ON m.sender_email = s.email
        WHERE m.team_id='$team_id' ORDER BY m.id ASC";
$result = $conn->query($sql);

$messages = [];

while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);

$conn->close();
?>