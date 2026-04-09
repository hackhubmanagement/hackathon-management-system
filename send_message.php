<?php
$conn = new mysqli("localhost", "root", "", "hackathon-db",3307);

if ($conn->connect_error) {
    die("Connection failed");
}

$team_id = $_POST['team_id'];
$email = $_POST['email'];
$message = $_POST['message'];

$sql = "INSERT INTO messages (team_id, sender_email, message) 
        VALUES ('$team_id', '$email', '$message')";

if ($conn->query($sql) === TRUE) {
    echo "success";
} else {
    echo "error";
}

$conn->close();
?>