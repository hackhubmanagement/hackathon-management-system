<?php
include "db.php";
include "log_system.php";

$title = trim($_POST['title']);
$message = trim($_POST['message']);
$priority = trim($_POST['priority']);

if($title=="" || $message==""){
    echo "empty";
    exit();
}

$sql = "INSERT INTO announcements (title,message,priority)
VALUES ('$title','$message','$priority')";

if(mysqli_query($conn,$sql)){
addLog("Announcement Sent: ".$title);
    echo "success";
}else{
    echo "error";
}

$students = mysqli_query($conn, "SELECT id FROM students");

while($s = mysqli_fetch_assoc($students)){
    mysqli_query($conn, "INSERT INTO notifications 
    (user_id, title, message, is_read, created_at)
    VALUES ('{$s['id']}', '$title', '$message', 0, NOW())");
}
?>