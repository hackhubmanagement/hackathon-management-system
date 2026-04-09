<?php
include 'db.php';

$user_id = $_GET['user_id'];

mysqli_query($conn, "UPDATE notifications SET is_read=1 WHERE user_id='$user_id'");
?>