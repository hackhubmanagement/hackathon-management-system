<?php
include "db.php";

function addLog($message){

global $conn;

$stmt = $conn->prepare("INSERT INTO system_logs (log_message) VALUES (?)");

$stmt->bind_param("s",$message);

$stmt->execute();

}
?>