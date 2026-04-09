<?php
include "db.php";

$user_id = $_GET['user_id'];

$result = mysqli_query($conn, "SELECT * FROM notifications WHERE user_id='$user_id' ORDER BY created_at DESC");

$notifications = [];

while($row = mysqli_fetch_assoc($result)){
    $notifications[] = $row;
}

echo json_encode($notifications);
?>