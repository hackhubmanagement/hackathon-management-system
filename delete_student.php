<?php

include "db.php";
include "log_system.php";

$id = $_POST["id"];

/* get student email before deleting */
$result = mysqli_query($conn,"SELECT email FROM students WHERE id='$id'");
$row = mysqli_fetch_assoc($result);
$email = $row['email'];

/* delete student */
mysqli_query($conn,"DELETE FROM students WHERE id='$id'");

/* add log */
addLog("Student Deleted: ".$email);

echo "success";

?>